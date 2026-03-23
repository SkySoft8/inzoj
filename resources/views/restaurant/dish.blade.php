@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <img src="https://img.iamcook.ru/old/upl/recipes/cat/u-04391b7e3f5adfaa6ac7f554740fa12f.jpg">
    <h2 class="text-left text-xl font-extrabold">{{ $dish->name }} ({{ $dish->weight }} г)</h2>
    <p><span class="font-light">Состав</span><br>{{ $dish->ingredients }}</p>
    <div>
        <p class="font-light">На 100 г</p>
        <div class="grid grid-cols-4 gap-1">
            <p class="text-left">Белки<br>{{ $dish->proteins }}</p>
            <p class="text-left">Жиры<br>{{ $dish->fats }}</p>
            <p class="text-left">Углев.<br>{{ $dish->carbs }}</p>
            <p class="text-left">Ккал<br>{{ $dish->calories }}</p>
        </div>
    </div>
    <div>
        <p class="font-light">На всю порцию {{ $dish->weight }} г</p>
        <div class="grid grid-cols-4 gap-1">
            <p class="text-left">Белки<br>{{ $nutrients[0] }}</p>
            <p class="text-left">Жиры<br>{{ $nutrients[1] }}</p>
            <p class="text-left">Углев.<br>{{ $nutrients[2] }}</p>
            <p class="text-left">Ккал<br>{{ $nutrients[3] }}</p>
        </div>
    </div>
    
    <form method="POST" action="{{ $amount !== null ? route('dish.delete') : route('dish.add') }}">
        @csrf
        <input type="hidden" name="dish_id" value="{{ $dish->id }}">
        @if ($amount !== null)
        <button class="bg-red-600 text-white font-bold p-2 rounded-md w-full mt-1">Удалить из дневника</button>
        @else
            <p class="font-medium">Какой прием пищи?</p>
            <label class="flex items-center gap-1">
                <input type="radio" name="meal_type" value="breakfast" required>Завтрак
            </label>
            <label class="flex items-center gap-1">
                <input type="radio" name="meal_type" value="lunch" required>Обед
            </label>
            <label class="flex items-center gap-1">
                <input type="radio" name="meal_type" value="dinner" required>Ужин
            </label>
            <label class="flex items-center gap-1">
                <input type="radio" name="meal_type" value="snack" required>Перекус
            </label>
            <button class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full mt-1">Добавить в дневник</button>
        @endif
    </form>

    <a href="{{ $amount !== null ? route('diary') : route('restaurant') }}" class="text-red-600">Назад</a>
</div>
@endsection