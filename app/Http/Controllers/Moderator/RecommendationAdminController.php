<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Nutrient;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RecommendationAdminController extends Controller
{
    private const SEVERITIES = [
        'info' => 'Информация',
        'warning' => 'Предупреждение',
        'critical' => 'Критично',
    ];

    private const METRICS = [
        'nutrient_percent' => 'Процент от нормы нутриента',
        'water_percent' => 'Процент воды от цели',
        'steps_percent' => 'Процент шагов от цели',
        'weight_change' => 'Изменение веса',
        'logged_days' => 'Заполненные дни дневника',
        'weight_logs' => 'Замеры веса',
    ];

    private const PERIODS = [
        'day' => 'День',
        'week' => 'Неделя',
        'month' => 'Месяц',
        'period' => 'Выбранный период',
    ];

    private const COMPARISONS = [
        'lt' => 'меньше',
        'lte' => 'меньше или равно',
        'gt' => 'больше',
        'gte' => 'больше или равно',
        'between' => 'в диапазоне',
    ];

    private const GOALS = [
        '' => 'Любая цель',
        'lose_weight' => 'Похудеть',
        'gain_muscle' => 'Набрать мышечную массу',
        'maintain' => 'Поддерживать вес',
        'other' => 'Другое',
    ];

    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
            'active' => 'nullable|in:0,1',
        ]);

        $query = Recommendation::query()->orderByDesc('priority')->orderBy('code');

        if (! empty($validated['q'])) {
            $search = $validated['q'];
            $query->where(function ($builder) use ($search) {
                $builder->where('code', 'like', '%'.$search.'%')
                    ->orWhere('title', 'like', '%'.$search.'%')
                    ->orWhere('text', 'like', '%'.$search.'%');
            });
        }

        if (isset($validated['active']) && $validated['active'] !== '') {
            $query->where('is_active', (bool) $validated['active']);
        }

        $recommendations = $query->get();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'recommendations' => $recommendations,
            ]);
        }

        return view('moderator.recommendations.index', [
            'recommendations' => $recommendations,
            'query' => $validated['q'] ?? '',
            'active' => $validated['active'] ?? '',
            'options' => $this->options(),
        ]);
    }

    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'recommendation' => null,
                'options' => $this->options(),
            ]);
        }

        return view('moderator.recommendations.form', [
            'recommendation' => new Recommendation,
            'options' => $this->options(),
        ]);
    }

    public function store(Request $request)
    {
        $recommendation = Recommendation::create($this->payload($request));

        return $this->saved($request, $recommendation, 'Рекомендация создана');
    }

    public function edit(Request $request, $id)
    {
        $recommendation = Recommendation::findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'recommendation' => $recommendation,
                'options' => $this->options(),
            ]);
        }

        return view('moderator.recommendations.form', [
            'recommendation' => $recommendation,
            'options' => $this->options(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $recommendation = Recommendation::findOrFail($id);
        $recommendation->update($this->payload($request, $recommendation));

        return $this->saved($request, $recommendation->fresh(), 'Рекомендация обновлена');
    }

    public function destroy(Request $request, $id)
    {
        Recommendation::findOrFail($id)->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Recommendation deleted',
            ]);
        }

        return redirect()->route('moderator.recommendations')->with('success', 'Рекомендация удалена');
    }

    private function payload(Request $request, ?Recommendation $recommendation = null): array
    {
        $codeRule = Rule::unique('recommendations', 'code');
        if ($recommendation) {
            $codeRule->ignore($recommendation->id);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:64', 'regex:/^[a-z0-9_]+$/', $codeRule],
            'title' => 'required|string|max:255',
            'text' => 'required|string|max:2000',
            'severity' => ['required', Rule::in(array_keys(self::SEVERITIES))],
            'metric' => ['required', Rule::in(array_keys(self::METRICS))],
            'nutrient_code' => 'nullable|required_if:metric,nutrient_percent|exists:nutrients,code',
            'goal' => ['nullable', Rule::in(array_keys(self::GOALS))],
            'comparison' => ['required', Rule::in(array_keys(self::COMPARISONS))],
            'threshold' => 'required|numeric',
            'threshold_max' => 'nullable|required_if:comparison,between|numeric|gte:threshold',
            'value_unit' => 'nullable|string|max:16',
            'period' => ['required', Rule::in(array_keys(self::PERIODS))],
            'min_tracked_days' => 'required|integer|min:0|max:366',
            'min_coverage' => 'required|numeric|min:0|max:100',
            'priority' => 'required|integer|min:-1000|max:1000',
        ]);

        return [
            'code' => $validated['code'],
            'title' => $validated['title'],
            'text' => $validated['text'],
            'severity' => $validated['severity'],
            'metric' => $validated['metric'],
            'nutrient_code' => $validated['metric'] === 'nutrient_percent'
                ? $validated['nutrient_code']
                : null,
            'goal' => ($validated['goal'] ?? null) ?: null,
            'comparison' => $validated['comparison'],
            'threshold' => $validated['threshold'],
            'threshold_max' => $validated['comparison'] === 'between'
                ? $validated['threshold_max']
                : null,
            'value_unit' => $validated['value_unit'] ?? null,
            'period' => $validated['period'],
            'min_tracked_days' => $validated['min_tracked_days'],
            'min_coverage' => $validated['min_coverage'],
            'priority' => $validated['priority'],
            'is_active' => $request->has('is_active')
                ? $request->boolean('is_active')
                : ($recommendation?->is_active ?? true),
        ];
    }

    private function options(): array
    {
        return [
            'severities' => self::SEVERITIES,
            'metrics' => self::METRICS,
            'periods' => self::PERIODS,
            'comparisons' => self::COMPARISONS,
            'goals' => self::GOALS,
            'nutrients' => Nutrient::where('is_active', true)
                ->orderBy('sort_order')
                ->get(['code', 'label', 'unit']),
        ];
    }

    private function saved(Request $request, Recommendation $recommendation, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'recommendation' => $recommendation,
            ]);
        }

        return redirect()->route('moderator.recommendations.edit', $recommendation->id)
            ->with('success', $message);
    }
}
