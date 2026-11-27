@extends('layouts.moderator')

@php
    $isEdit = $product->exists;
    $storedNutrients = $product->nutrients ?? [];
@endphp

@section('content')
<div class="flex justify-between items-center gap-3 mb-6">
    <h2 class="text-2xl font-extrabold">{{ $isEdit ? 'Редактирование продукта' : 'Новый продукт' }}</h2>
    <a href="{{ route('moderator.products') }}" class="text-indigo-600 font-medium">К списку</a>
</div>

<form method="POST" action="{{ $isEdit ? route('moderator.products.update', $product->id) : route('moderator.products.store') }}" class="flex flex-col gap-4">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="bg-white rounded-lg border p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-1" for="name">Название</label>
            <input id="name" name="name" type="text" value="{{ old('name', $product->name) }}" class="w-full rounded border px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="shortcode">Штрихкод (shortcode)</label>
            <input id="shortcode" name="shortcode" type="text" value="{{ old('shortcode', $product->shortcode) }}" class="w-full rounded border px-3 py-2">
            <div class="text-xs text-gray-500 mt-1">По нему работает поиск продукта.</div>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1" for="calories">Калории, ккал / 100 г</label>
            <input id="calories" name="calories" type="number" step="0.1" min="0" value="{{ old('calories', $product->calories) }}" class="w-full rounded border px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="proteins">Белки, г / 100 г</label>
            <input id="proteins" name="proteins" type="number" step="0.1" min="0" value="{{ old('proteins', $product->proteins) }}" class="w-full rounded border px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="fats">Жиры, г / 100 г</label>
            <input id="fats" name="fats" type="number" step="0.1" min="0" value="{{ old('fats', $product->fats) }}" class="w-full rounded border px-3 py-2" required>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1" for="carbs">Углеводы, г / 100 г</label>
            <input id="carbs" name="carbs" type="number" step="0.1" min="0" value="{{ old('carbs', $product->carbs) }}" class="w-full rounded border px-3 py-2" required>
        </div>
    </div>

    <div class="bg-white rounded-lg border p-4">
        <h3 class="font-medium mb-1">Порция</h3>
        <p class="text-sm text-gray-500 mb-3">Необязательно. Позволяет добавлять продукт в штуках или миллилитрах.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1" for="serving_label">Название порции</label>
                <input id="serving_label" name="serving_label" type="text" value="{{ old('serving_label', $product->serving_label) }}" class="w-full rounded border px-3 py-2" placeholder="Например, стакан">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="serving_grams">Граммов в порции</label>
                <input id="serving_grams" name="serving_grams" type="number" min="0" value="{{ old('serving_grams', $product->serving_grams) }}" class="w-full rounded border px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1" for="serving_unit">Единица порции</label>
                <select id="serving_unit" name="serving_unit" class="w-full rounded border px-3 py-2">
                    <option value="">—</option>
                    @foreach (['g' => 'граммы', 'pcs' => 'штуки', 'ml' => 'миллилитры'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('serving_unit', $product->serving_unit) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border p-4">
        <h3 class="font-medium mb-1">Нутриенты на 100 г</h3>
        <p class="text-sm text-gray-500 mb-3">Пустые поля не сохраняются: продукт будет учтён без этих нутриентов.</p>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($nutrients as $nutrient)
                <div>
                    <label class="block text-sm font-medium mb-1" for="nutrient-{{ $nutrient->code }}">{{ $nutrient->label }}, {{ $nutrient->unit }}</label>
                    <input id="nutrient-{{ $nutrient->code }}" name="nutrients[{{ $nutrient->code }}]" type="number" step="0.001" min="0"
                        value="{{ old('nutrients.'.$nutrient->code, $storedNutrients[$nutrient->code] ?? '') }}" class="w-full rounded border px-3 py-2">
                </div>
            @endforeach
        </div>
        @if ($nutrients->isEmpty())
            <p class="text-sm text-amber-700">Справочник нутриентов пуст. Выполните <code>php artisan db:seed --class=NutrientSeeder</code>.</p>
        @endif
    </div>

    <div class="flex gap-3">
        <button type="submit" class="rounded bg-indigo-600 text-white px-5 py-2 font-medium">{{ $isEdit ? 'Сохранить' : 'Создать' }}</button>
        <a href="{{ route('moderator.products') }}" class="rounded bg-white border px-5 py-2 font-medium">Отмена</a>
    </div>
</form>
@endsection
