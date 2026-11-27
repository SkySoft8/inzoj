@extends('layouts.moderator')

@php
    $isEdit = $recommendation->exists;
    $current = fn (string $field, $default = null) => old($field, $recommendation->{$field} ?? $default);
@endphp

@section('content')
<div class="flex justify-between items-center gap-3 mb-6">
    <h2 class="text-2xl font-extrabold">{{ $isEdit ? 'Редактирование рекомендации' : 'Новая рекомендация' }}</h2>
    <a href="{{ route('moderator.recommendations') }}" class="text-indigo-600 font-medium">К списку</a>
</div>

<form method="POST" action="{{ $isEdit ? route('moderator.recommendations.update', $recommendation->id) : route('moderator.recommendations.store') }}" class="flex flex-col gap-4">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="bg-white rounded-lg border p-4">
        <h3 class="font-medium mb-1">Правило</h3>
        <p class="text-sm text-gray-500 mb-4">Условие, при котором рекомендация попадает в статистику.</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="code">Код</label>
                <input id="code" name="code" type="text" value="{{ $current('code') }}" class="w-full rounded border px-3 py-2" placeholder="fiber_low" required>
                <div class="text-xs text-gray-500 mt-1">Латиница, цифры и подчёркивание.</div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1" for="title">Заголовок</label>
                <input id="title" name="title" type="text" value="{{ $current('title') }}" class="w-full rounded border px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="metric">Показатель</label>
                <select id="metric" name="metric" class="w-full rounded border px-3 py-2">
                    @foreach ($options['metrics'] as $value => $label)
                        <option value="{{ $value }}" @selected($current('metric') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="nutrient_code">Нутриент</label>
                <select id="nutrient_code" name="nutrient_code" class="w-full rounded border px-3 py-2">
                    <option value="">—</option>
                    @foreach ($options['nutrients'] as $nutrient)
                        <option value="{{ $nutrient->code }}" @selected($current('nutrient_code') === $nutrient->code)>{{ $nutrient->label }} ({{ $nutrient->unit }})</option>
                    @endforeach
                </select>
                <div class="text-xs text-gray-500 mt-1">Обязателен для показателя «Процент от нормы нутриента».</div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="period">Период</label>
                <select id="period" name="period" class="w-full rounded border px-3 py-2">
                    @foreach ($options['periods'] as $value => $label)
                        <option value="{{ $value }}" @selected($current('period', 'period') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="comparison">Сравнение</label>
                <select id="comparison" name="comparison" class="w-full rounded border px-3 py-2">
                    @foreach ($options['comparisons'] as $value => $label)
                        <option value="{{ $value }}" @selected($current('comparison', 'lt') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="threshold">Порог</label>
                <input id="threshold" name="threshold" type="number" step="0.01" value="{{ $current('threshold') }}" class="w-full rounded border px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="threshold_max">Верхний порог</label>
                <input id="threshold_max" name="threshold_max" type="number" step="0.01" value="{{ $current('threshold_max') }}" class="w-full rounded border px-3 py-2">
                <div class="text-xs text-gray-500 mt-1">Только для сравнения «в диапазоне».</div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="value_unit">Единица</label>
                <input id="value_unit" name="value_unit" type="text" value="{{ $current('value_unit') }}" class="w-full rounded border px-3 py-2" placeholder="%, кг, дн., шт.">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="goal">Цель пользователя</label>
                <select id="goal" name="goal" class="w-full rounded border px-3 py-2">
                    @foreach ($options['goals'] as $value => $label)
                        <option value="{{ $value }}" @selected($current('goal', '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="priority">Приоритет</label>
                <input id="priority" name="priority" type="number" step="1" value="{{ $current('priority', 0) }}" class="w-full rounded border px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1" for="min_tracked_days">Минимум дней с дневником</label>
                <input id="min_tracked_days" name="min_tracked_days" type="number" min="0" max="366" step="1" value="{{ $current('min_tracked_days', 0) }}" class="w-full rounded border px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="min_coverage">Минимальное покрытие данных, %</label>
                <input id="min_coverage" name="min_coverage" type="number" min="0" max="100" step="0.01" value="{{ $current('min_coverage', 0) }}" class="w-full rounded border px-3 py-2">
            </div>
            <div class="flex items-end pb-2">
                <input type="hidden" name="is_active" value="0">
                <label class="flex items-center gap-2 font-medium">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $isEdit ? $recommendation->is_active : true))>
                    Правило активно
                </label>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border p-4">
        <h3 class="font-medium mb-1">Текст рекомендации</h3>
        <p class="text-sm text-gray-500 mb-4">
            Подстановки: <code>:value</code>, <code>:threshold</code>, <code>:threshold_max</code>, <code>:nutrient</code>, <code>:days</code>.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="severity">Важность</label>
                <select id="severity" name="severity" class="w-full rounded border px-3 py-2">
                    @foreach ($options['severities'] as $value => $label)
                        <option value="{{ $value }}" @selected($current('severity', 'info') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium mb-1" for="text">Текст</label>
                <textarea id="text" name="text" rows="4" class="w-full rounded border px-3 py-2" required>{{ $current('text') }}</textarea>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded bg-indigo-600 text-white px-5 py-2 font-medium">{{ $isEdit ? 'Сохранить' : 'Создать' }}</button>
        <a href="{{ route('moderator.recommendations') }}" class="rounded bg-white border px-5 py-2 font-medium">Отмена</a>
    </div>
</form>
@endsection
