@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col justify-between">
    <h2 class="text-center text-3xl font-extrabold">{{ $product->name }}</h2>

    <div class="flex flex-col gap-1 bg-gray-200 p-4 rounded-md">
        <p class="text-gray-600">На 100 гр</p>
        <div class="flex justify-between">
            <p class="flex flex-col justify-left gap-1">Белки <span>{{ $product->proteins }}</span></p>
            <p class="flex flex-col justify-left gap-1">Жиры <span>{{ $product->fats }}</span></p>
            <p class="flex flex-col justify-left gap-1">Углев. <span>{{ $product->carbs }}</span></p>
            <p class="flex flex-col justify-left gap-1">Ккал <span>{{ $product->calories }}</span></p>
        </div>
    </div>

    <form method="POST" action="{{ $amount !== null ? route('product.update') : route('product.add') }}" class="flex flex-col gap-2 w-full">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <label>Количество</label>
        <input type="number" name="amount" min="5" max="2000" value="{{ $amount ?? 100 }}"
            class="w-full border border-indigo-600 rounded-md p-1" required>
        <button class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full">{{ $amount !== null ? 'Сохранить' : 'Добавить'}}</button>
    </form>

    <a href="{{ $amount ? route('diary') : route('meal') }}" class="text-red-600">Назад</a>
</div>
@endsection