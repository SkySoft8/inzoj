@extends('layouts.app')

@section('content')
@php
    $fmt = function ($value, $digits = 1) {
        if ($value === null || $value === '') {
            return '—';
        }
        return rtrim(rtrim(number_format((float) $value, $digits, '.', ' '), '0'), '.');
    };
    $pct = fn ($value) => $value === null ? '—' : $value.'%';
    $barWidth = fn ($value) => $value === null ? 0 : max(0, min(100, (int) $value));
    $weight = $summary['weight'];
    $today = $summary['calories_today'];
    $hasCustomRange = request('from') || request('to');
    $activePeriod = $hasCustomRange ? 'custom' : ($period === 'month' ? 'month' : 'week');
@endphp

<div class="gap-4 w-96 flex flex-col justify-between">
    <h2 class="text-center text-3xl font-extrabold">Статистика</h2>

    <div class="grid grid-cols-2 gap-2">
        <a href="{{ route('stats', ['period' => 'week']) }}"
           class="rounded py-2 px-4 font-medium text-center border {{ $activePeriod === 'week' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white' }}">
            Неделя
        </a>
        <a href="{{ route('stats', ['period' => 'month']) }}"
           class="rounded py-2 px-4 font-medium text-center border {{ $activePeriod === 'month' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white' }}">
            Месяц
        </a>
    </div>

    <form method="GET" action="{{ route('stats') }}" class="bg-white rounded-lg p-4 flex flex-col gap-2">
        <input type="hidden" name="period" value="custom">
        <div class="flex gap-2">
            <label class="flex flex-col text-sm gap-1 flex-1">
                С
                <input type="date" name="from" value="{{ $from }}" class="rounded border px-2 py-1">
            </label>
            <label class="flex flex-col text-sm gap-1 flex-1">
                По
                <input type="date" name="to" value="{{ $to }}" class="rounded border px-2 py-1">
            </label>
        </div>
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">
            Показать
        </button>
        @error('from')
            <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
        @error('to')
            <div class="text-red-600 text-sm">{{ $message }}</div>
        @enderror
    </form>

    <p class="text-center text-sm text-gray-600">
        {{ $stats['period']['label'] }} · данных за {{ $stats['period']['tracked_days'] }} из {{ $stats['period']['days'] }} дн.
    </p>

    <div class="bg-white rounded-lg p-4 flex flex-col gap-3">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold">Вес</h3>
            <span class="text-sm text-gray-500">цель {{ $fmt($weight['target']) }} кг</span>
        </div>
        <div class="grid grid-cols-3 gap-2 text-center">
            <div class="flex flex-col">
                <span class="text-xs text-gray-500">Старт</span>
                <span class="font-medium">{{ $fmt($weight['start']) }} кг</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-gray-500">Сейчас</span>
                <span class="font-medium">{{ $fmt($weight['current']) }} кг</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-gray-500">Изменение</span>
                <span class="font-medium {{ $weight['delta'] !== null && $weight['delta'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ $weight['delta'] !== null ? ($weight['delta'] > 0 ? '+' : '').$fmt($weight['delta']) : '—' }} кг
                </span>
            </div>
        </div>
        <div class="flex justify-between text-sm text-gray-600">
            <span>За период</span>
            <span>
                {{ $stats['weight']['change'] !== null ? ($stats['weight']['change'] > 0 ? '+' : '').$fmt($stats['weight']['change']).' кг' : '—' }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 flex flex-col gap-3">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold">Сегодня</h3>
            <span class="text-sm text-gray-500">сожжено {{ $fmt($today['burned']) }} ккал</span>
        </div>
        <div class="grid grid-cols-4 gap-2 text-center">
            <div class="flex flex-col">
                <span class="text-xs text-gray-500">Ккал</span>
                <span class="font-medium">{{ $fmt($today['eaten']) }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-gray-500">Белки</span>
                <span class="font-medium">{{ $fmt($today['proteins']) }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-gray-500">Жиры</span>
                <span class="font-medium">{{ $fmt($today['fats']) }}</span>
            </div>
            <div class="flex flex-col">
                <span class="text-xs text-gray-500">Углеводы</span>
                <span class="font-medium">{{ $fmt($today['carbs']) }}</span>
            </div>
        </div>
    </div>

    @php $calories = $stats['calories']; @endphp
    <div class="bg-white rounded-lg p-4 flex flex-col gap-3">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold">{{ $calories['label'] }}</h3>
            <span class="text-sm text-gray-500">{{ $pct($calories['percent']) }}</span>
        </div>
        <div class="flex justify-between text-sm">
            <span>В среднем {{ $fmt($calories['eaten']) }} {{ $calories['unit'] }}</span>
            <span>цель {{ $fmt($calories['goal']) }}</span>
        </div>
        <div class="w-full h-2 bg-gray-200 rounded">
            <div class="h-2 bg-indigo-600 rounded" style="width: {{ $barWidth($calories['percent']) }}%"></div>
        </div>
        <div class="flex items-end gap-1 h-16">
            @foreach($calories['days'] as $day)
                @php
                    $height = $calories['goal'] ? min(100, round($day['value'] / $calories['goal'] * 100)) : 0;
                    $height = max($height, $day['tracked'] ? 4 : 1);
                @endphp
                <div class="flex-1 flex flex-col justify-end h-full" title="{{ $day['date'] }}: {{ $fmt($day['value']) }} {{ $calories['unit'] }}">
                    <div class="w-full rounded-t {{ $day['tracked'] ? 'bg-indigo-400' : 'bg-gray-200' }}" style="height: {{ $height }}%"></div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-between text-xs text-gray-500">
            <span>{{ \Illuminate\Support\Carbon::parse($calories['days'][0]['date'] ?? $from)->format('d.m') }}</span>
            <span>{{ \Illuminate\Support\Carbon::parse($calories['days'][count($calories['days']) - 1]['date'] ?? $to)->format('d.m') }}</span>
        </div>
    </div>

    <div class="bg-white rounded-lg p-4 flex flex-col gap-3">
        <h3 class="font-semibold">БЖУ</h3>
        @foreach($stats['bju'] as $item)
            <div class="flex flex-col gap-1">
                <div class="flex justify-between text-sm">
                    <span class="font-medium">{{ $item['label'] }}</span>
                    <span class="text-gray-500">{{ $fmt($item['eaten']) }} / {{ $fmt($item['goal']) }} {{ $item['unit'] }} · {{ $pct($item['percent']) }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded">
                    <div class="h-2 bg-emerald-500 rounded" style="width: {{ $barWidth($item['percent']) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 gap-2">
        @foreach(['water', 'steps'] as $key)
            @php $item = $stats[$key]; @endphp
            <div class="bg-white rounded-lg p-4 flex flex-col gap-2">
                <div class="flex justify-between items-center">
                    <h3 class="font-semibold">{{ $item['label'] }}</h3>
                    <span class="text-sm text-gray-500">{{ $pct($item['percent']) }}</span>
                </div>
                <div class="text-sm">
                    <span class="font-medium">{{ $fmt($item['eaten'], $key === 'steps' ? 0 : 2) }} {{ $item['unit'] }}</span>
                    <span class="text-gray-500"> / {{ $fmt($item['goal'], $key === 'steps' ? 0 : 1) }}</span>
                </div>
                <div class="w-full h-2 bg-gray-200 rounded">
                    <div class="h-2 bg-sky-500 rounded" style="width: {{ $barWidth($item['percent']) }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg p-4 flex flex-col gap-3">
        <div class="flex justify-between items-center">
            <h3 class="font-semibold">Нутриенты</h3>
            <span class="text-sm text-gray-500">покрытие {{ $fmt($stats['nutrients']['coverage_percent']) }}%</span>
        </div>

        @if(!$stats['nutrients']['has_data'])
            <p class="text-sm text-gray-500">
                Недостаточно данных о микронутриентах за выбранный период.
            </p>
        @endif

        @php
            $extraMain = collect($stats['nutrients']['main'])->reject(
                fn ($item) => in_array($item['code'], ['calories', 'proteins', 'fats', 'carbs'], true)
            );
        @endphp

        @foreach($extraMain as $item)
            <div class="flex justify-between text-sm">
                <span class="font-medium">{{ $item['label'] }}</span>
                <span class="text-gray-500">{{ $fmt($item['eaten']) }} / {{ $fmt($item['goal']) }} {{ $item['unit'] }} · {{ $pct($item['percent']) }}</span>
            </div>
        @endforeach

        @foreach(['minerals' => 'Минералы', 'vitamins' => 'Витамины'] as $groupKey => $groupLabel)
            @if(!empty($stats['nutrients'][$groupKey]))
                <details class="border-t pt-2">
                    <summary class="cursor-pointer font-medium text-sm">{{ $groupLabel }}</summary>
                    <div class="mt-2 flex flex-col gap-2">
                        @foreach($stats['nutrients'][$groupKey] as $item)
                            <div class="flex flex-col gap-1">
                                <div class="flex justify-between text-sm">
                                    <span>{{ $item['label'] }}</span>
                                    <span class="text-gray-500">
                                        {{ $item['eaten'] !== null ? $fmt($item['eaten'], 2) : '—' }}
                                        / {{ $fmt($item['goal']) }} {{ $item['unit'] }} · {{ $pct($item['percent']) }}
                                    </span>
                                </div>
                                <div class="w-full h-1.5 bg-gray-200 rounded">
                                    <div class="h-1.5 bg-amber-500 rounded" style="width: {{ $barWidth($item['percent']) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </details>
            @endif
        @endforeach
    </div>

    @if(!empty($stats['recommendations']))
        <div class="flex flex-col gap-2">
            <h3 class="font-semibold">Рекомендации</h3>
            @foreach($stats['recommendations'] as $recommendation)
                @php
                    $severityClass = [
                        'critical' => 'border-red-400 bg-red-50',
                        'warning' => 'border-amber-400 bg-amber-50',
                        'info' => 'border-indigo-300 bg-indigo-50',
                    ][$recommendation['severity']] ?? 'border-gray-300 bg-white';
                @endphp
                <div class="rounded-lg border-l-4 p-3 {{ $severityClass }}">
                    <p class="font-medium">{{ $recommendation['title'] }}</p>
                    <p class="text-sm text-gray-700">{{ $recommendation['text'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <a href="{{ route('profile') }}" class="rounded w-full flex justify-center py-2 px-4 font-medium text-red-600">
        Назад в профиль
    </a>
</div>
@endsection
