@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col justify-between">

    @php
        $mealTypeConvertor = [
            'breakfast' => 'Завтрак',
            'lunch' => 'Обед',
            'dinner' => 'Ужин',
            'snack' => 'Перекус'    
        ];
    @endphp

    <h2 class="text-center text-3xl font-extrabold">{{ $mealTypeConvertor[session('meal_type')] }}</h2>

    <form action="{{ route('meal') }}" class="flex gap-2">
        <button type="submit" name="productsOrRecepies" value="products"
            class="bg-green-600 text-white rounded-md p-1 w-full">Продукты</button>
        <button type="submit" name="productsOrRecepies" value="recepies"
            class="bg-green-600 text-white rounded-md p-1 w-full">Рецепты</button>
    </form>
    
    @if ($products)
        <p class="text-2xl text-center font-medium">Что вы ели на {{ mb_strtolower($mealTypeConvertor[session('meal_type')]) }}?</p>
        @foreach ($products as $product)
            <form action="{{ route('product') }}" class="flex gap-2 justify-between bg-gray-200 rounded p-2">
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <button type="submit" class="flex gap-4">
                    <p>{{ $product->name }}</p>
                    <p>{{ $product->calories }} ккал</p>
                </button>
            </form>
            <form method="POST" action="{{ route('meal.toggleFavorite') }}" class="flex gap-2 justify-between bg-gray-200 rounded p-2">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="is_favorite" value="{{ $product->is_favorite ? true : false }}">
                <input type="hidden" name="productsOrRecepies" value="products">
                <button type="submit">{{ $product->is_favorite ? "❤" : "♡" }}</button>
            </form>
        @endforeach
    @elseif ($recepies)
        <h4 class="text-2xl text-center font-medium">Рецепты</h4>

        <form action="{{ route('filter') }}">
            <button type="submit" class="flex flex-col bg-gray-300 rounded-lg p-1">Фильтры</button>
        </form>

        <div class="grid grid-cols-2 gap-2">
            @foreach ($recepies as $recepie)
                <form action="{{ route('recepie') }}">
                    <input type="hidden" name="recepie_id" value="{{ $recepie->id }}">
                    <button type="submit" name="action" value="show" class="flex flex-col bg-gray-300 rounded-lg">
                        <img src="{{ $recepie->image }}" class="rounded-lg">
                        <div class="flex flex-col gap-1 p-2">
                            <p>{{ $recepie->name }}</p>
                            <p>БЖУ: {{ $recepie->proteins }}  {{ $recepie->fats }}  {{ $recepie->carbs }}</p>
                        </div>
                    </button>
                </form>
                <form method="POST" action="{{ route('meal.toggleFavorite') }}">
                    @csrf
                    <input type="hidden" name="recepie_id" value="{{ $recepie->id }}">
                    <input type="hidden" name="is_favorite" value="{{ $recepie->is_favorite ? true : false }}">
                    <input type="hidden" name="productsOrRecepies" value="recepies">
                    <button type="submit" name="action" value="toggle_favorite">{{ $recepie->is_favorite ? "❤" : "♡" }}</button>
                </form>
            @endforeach
        </div>
    @else
        <h4 class="text-2xl text-center font-medium">Нет подходящих результатов</h4>

        <form action="{{ route('filter') }}">
            <button type="submit" class="flex flex-col bg-gray-300 rounded-lg p-1">Фильтры</button>
        </form>
    @endif

    <a href="{{ route('diary') }}" class="text-red-600">Назад</a>
</div>
@endsection