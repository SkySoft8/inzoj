@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Новое блюдо</h2>

    <form method="POST" action="{{ route('restaurantUser.dish.add') }}" class="flex flex-col items-start gap-2">
        @csrf
        <label class="w-full">Название<br>
            <input type="text" name="name" class="w-full border border-indigo-600 rounded-md p-1">
        </label>
        <label class="w-full">Цена (в рублях)<br>
            <input type="number" name="price" class="w-full border border-indigo-600 rounded-md p-1">      
        </label>
        <label class="w-full">Вес порции (в граммах)<br>
            <input type="number" name="weight" class="w-full border border-indigo-600 rounded-md p-1">      
        </label>
        <label class="w-full">
            <span class="font-light">Состав</span><br>
            <textarea name="ingredients" class="w-full border border-indigo-600 rounded-md p-1 mr-1"></textarea>
        </label>
        <div class="w-full">
            <p class="font-light">На 100 г</p>
            <div class="grid grid-cols-4 gap-1">
                <label>Белки<br>
                    <input type="number" name="proteins" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
                <label>Жиры<br>
                    <input type="number" name="fats" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
                <label>Углев.<br>
                    <input type="number" name="carbs" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
                <label>Ккал<br>
                    <input type="number" name="calories" class="max-w-20 border border-indigo-600 rounded-md p-1">
                </label>
            </div>
        </div>
    
        <div class="w-full flex flex-col mt-4">
            <button class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full mt-1">Добавить</button>
        </div>
    </form>

    <a href="{{ route('restaurantUser.menu') }}" class="text-red-600">Назад</a>
</div>
@endsection