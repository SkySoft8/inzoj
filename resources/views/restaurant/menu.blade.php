@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">{{ $restaurant->name }}</h2>

    <form action="{{ route('dish') }}" class="grid grid-cols-2 gap-2">
        @foreach ($dishes as $dish)
            <button type="submit" name="dish_id" value="{{ $dish->id }}" class="flex flex-col items-start bg-gray-200 pb-1">
                <img src="https://img.iamcook.ru/old/upl/recipes/cat/u-04391b7e3f5adfaa6ac7f554740fa12f.jpg" class="mb-1">
                <p class="ml-1">{{ $dish->price }}р.</p>
                <h6 class="ml-1 font-medium text-left">{{ $dish->name }}</h6>
                <p class="ml-1">{{ $dish->weight }}г</p>
            </button>
        @endforeach
    </form>

    <a href="{{ route('restaurant') }}" class="text-red-600">Назад</a>
</div>
@endsection