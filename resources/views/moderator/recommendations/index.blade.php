@extends('layouts.moderator')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-2xl font-extrabold">Рекомендации</h2>
    <a href="{{ route('moderator.recommendations.create') }}" class="rounded bg-indigo-600 text-white px-4 py-2 font-medium">Добавить правило</a>
</div>

<form method="GET" action="{{ route('moderator.recommendations') }}" class="flex flex-wrap gap-2 mb-4">
    <input type="text" name="q" value="{{ $query }}" placeholder="Код, заголовок или текст" class="flex-1 min-w-[220px] rounded border px-3 py-2">
    <select name="active" class="rounded border px-3 py-2">
        <option value="">Все правила</option>
        <option value="1" @selected($active === '1')>Только активные</option>
        <option value="0" @selected($active === '0')>Только выключенные</option>
    </select>
    <button type="submit" class="rounded bg-white border px-4 py-2 font-medium">Фильтровать</button>
</form>

<div class="bg-white rounded-lg border overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Правило</th>
                <th class="px-4 py-3">Условие</th>
                <th class="px-4 py-3">Период</th>
                <th class="px-4 py-3">Важность</th>
                <th class="px-4 py-3">Приоритет</th>
                <th class="px-4 py-3">Статус</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recommendations as $recommendation)
                <tr class="border-t align-top">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $recommendation->title }}</div>
                        <div class="text-xs text-gray-500">{{ $recommendation->code }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $options['metrics'][$recommendation->metric] ?? $recommendation->metric }}
                        @if ($recommendation->nutrient_code)
                            · {{ $recommendation->nutrient_code }}
                        @endif
                        <div class="text-xs">
                            {{ $options['comparisons'][$recommendation->comparison] ?? $recommendation->comparison }}
                            {{ $recommendation->threshold }}
                            @if ($recommendation->threshold_max !== null)
                                … {{ $recommendation->threshold_max }}
                            @endif
                            {{ $recommendation->value_unit }}
                        </div>
                        @if ($recommendation->goal)
                            <div class="text-xs">Цель: {{ $recommendation->goal }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $options['periods'][$recommendation->period] ?? $recommendation->period }}</td>
                    <td class="px-4 py-3">{{ $options['severities'][$recommendation->severity] ?? $recommendation->severity }}</td>
                    <td class="px-4 py-3">{{ $recommendation->priority }}</td>
                    <td class="px-4 py-3">
                        @if ($recommendation->is_active)
                            <span class="rounded bg-green-100 text-green-800 px-2 py-1 text-xs">Активна</span>
                        @else
                            <span class="rounded bg-gray-100 text-gray-600 px-2 py-1 text-xs">Выключена</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('moderator.recommendations.edit', $recommendation->id) }}" class="text-indigo-600 font-medium">Изменить</a>
                            <form method="POST" action="{{ route('moderator.recommendations.destroy', $recommendation->id) }}" onsubmit="return confirm('Удалить правило?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-medium">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Правила не найдены</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
