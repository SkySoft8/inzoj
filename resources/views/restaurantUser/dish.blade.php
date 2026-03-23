@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <img src="https://img.iamcook.ru/old/upl/recipes/cat/u-04391b7e3f5adfaa6ac7f554740fa12f.jpg">
    <form method="POST" action="{{ route('restaurantUser.dishAction') }}" class="flex flex-col items-start gap-2">
        @csrf
        <input type="hidden" name="dish_id" value="{{ $dish->id }}">

        <label class="w-full">Название<br>
            <input type="text" name="name" value="{{ $dish->name }}" class="w-full border border-indigo-600 rounded-md p-1">
        </label>
        <label class="w-full">Цена (в рублях)<br>
            <input type="number" name="price" value="{{ $dish->price }}" class="w-full border border-indigo-600 rounded-md p-1">      
        </label>
        <label class="w-full">Вес порции (в граммах)<br>
            <input type="number" name="weight" value="{{ $dish->weight }}" class="w-full border border-indigo-600 rounded-md p-1">      
        </label>
        <label class="w-full">
            <span class="font-light">Состав</span><br>
            <textarea name="ingredients" class="w-full border border-indigo-600 rounded-md p-1 mr-1">{{ $dish->ingredients }}</textarea>
        </label>
        <div class="w-full">
            <p class="font-light">На 100 г</p>
            <div class="grid grid-cols-4 gap-1">
                <label>Белки<br>
                    <input type="number" name="proteins" value="{{ $dish->proteins }}" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
                <label>Жиры<br>
                    <input type="number" name="fats" value="{{ $dish->fats }}" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
                <label>Углев.<br>
                    <input type="number" name="carbs" value="{{ $dish->carbs }}" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
                <label>Ккал<br>
                    <input type="number" name="calories" value="{{ $dish->calories }}" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
            </div>
        </div>
        <div class="w-full">
            <p class="font-light">На всю порцию {{ $dish->weight }} г</p>
            <div class="grid grid-cols-4 gap-1">
                <p class="text-left">Белки<br>{{ $nutrients[0] }}</p>
                <p class="text-left">Жиры<br>{{ $nutrients[1] }}</p>
                <p class="text-left">Углев.<br>{{ $nutrients[2] }}</p>
                <p class="text-left">Ккал<br>{{ $nutrients[3] }}</p>
            </div>
        </div>
    
        <div class="w-full flex flex-col mt-4">
            <button name="action" value="update" class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full mt-1">Сохранить</button>
            <button name="action" value="delete" class="bg-red-600 text-white font-bold p-2 rounded-md w-full mt-1">Удалить из меню</button>
        </div>
    </form>

    <a href="{{ route('restaurantUser.menu') }}" class="text-red-600">Назад</a>
</div>
@endsection