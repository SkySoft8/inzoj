@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Тренировки</h2>

    <form action="{{ route('userTrainings') }}" class="flex gap-2">
        <p class="w-full bg-green-600 text-white text-center px-2 py-1 rounded">Онлайн тренировки</p>
        <button type="submit" class="w-full border border-black px-2 py-1 rounded">Мои тренировки</button>
    </form>

    <form action="{{ route('trainings') }}" class="flex flex-col gap-2">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold">Найти тренировки в период:</h2>
            <label>Искать с:<br>
                <input type="date" name="from_date" min="{{ now()->format('Y-m-d') }}" value="{{ now()->format('Y-m-d') }}" class="w-full border border-indigo-600 rounded-md p-1">
            </label>
            <label>Искать по:<br>
                <input type="date" name="to_date" min="{{ now()->format('Y-m-d') }}" value="{{ now()->addWeek()->format('Y-m-d') }}" class="w-full border border-indigo-600 rounded-md p-1">
            </label>

            <h2 class="text-xl font-bold">Категории</h2>
            <label>
                <input type="checkbox" name="categories[]" value="all" class="mr-1" checked>Все
            </label>
            @foreach ($trainingCategories as $id => $category)
                <label>
                    <input type="checkbox" name="categories[]" value="{{ $id }}" class="mr-1">{{ $category }}
                </label>
            @endforeach
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded">Найти тренировку</button>
    </form>

    <div class="flex justify-between">
        <a href="{{ route('diary') }}" class="text-gray-600">Дневник</a>
        <a href="{{ route('restaurant') }}" class="text-gray-600">Рестораны</a>
        <a href="#" class="text-green-600 font-medium">Тренировки</a>
        <a href="{{ route('profile') }}" class="text-gray-600">Профиль</a>
    </div>
</div>
@endsection