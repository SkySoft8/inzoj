@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col justify-between">
    <h2 class="text-center text-3xl font-extrabold">Рецепт</h2>

    <form action="{{ route('meal') }}" class="flex gap-2">
        <button type="submit" name="productsOrRecepies" value="products"
            class="bg-green-600 text-white rounded-md p-1 w-full">Продукты</button>
        <button type="submit" name="productsOrRecepies" value="recepies"
            class="bg-green-600 text-white rounded-md p-1 w-full">Рецепты</button>
    </form>

    <div class="w-full flex flex-col gap-2">
        <p class="text-2xl text-center font-medium">{{ $recepie->name }}</p>
        <img src="{{ $recepie->image }}">
        <div class="flex flex-col gap-1">
            <p class="text-lg font-medium">Ингридиенты</p>
            <div class="grid grid-cols-2">
                @foreach ($ingredients as $ingredients)
                    <p>{{ $ingredients->name }}</p>
                    <p>{{ $ingredients->amount }}</p>
                @endforeach
            </div>
            <p class="text-lg font-medium">Приготовление</p>
            <p>{{ $recepie->instructions }}</p>
        </div>
    </div>

    <form method="POST" action="{{ $amount !== null ? route('recepie.update') : route('recepie.add') }}" class="flex flex-col gap-2 w-full">
        @csrf
        <input type="hidden" name="recepie_id" value="{{ $recepie->id }}">
        <label>Количество</label>
        <input type="number" name="amount" min="5" max="2000" value="{{ $amount ?? 100 }}"
            class="w-full border border-indigo-600 rounded-md p-1" required>
        <button class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full">{{ $amount !== null ? 'Сохранить' : 'Добавить'}}</button>
    </form>

    <a href="{{ $amount ? route('diary') : $url }}" class="text-red-600">Назад</a>
</div>
@endsection