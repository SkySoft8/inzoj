@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Тренер</h2>

    <div class="flex flex-col gap-1">
        <p>Имя: {{ $trainer->name ?? "Не указано" }}</p>
        <p>Фамилия: {{ $trainer->surname ?? "Не указано" }}</p>
        <p>Дата рождения: {{ isset($trainer->birthday) ? $trainer->birthday->format('d.m.Y') : "Не указано" }}</p>
        <p>Стаж: {{ $trainer->experience ?? "Не указано" }}</p>
        <p>Награды и достижения: {{ $trainer->achievements ?? "Не указано" }}</p>
        <p>Рейтинг: {{ isset($trainer->rating) ? round($trainer->rating/$trainer->rating_count, 2) : "Еще нет оценок" }}</p>
    </div>

    <form action="{{ route('rating') }}" class="flex flex-col gap-1">
        <input type="hidden" name="trainer_id" value="{{ $trainer->id }}">
        <input type="hidden" name="url" value="{{ $url }}">
        <div class="flex gap-2">
            <label>
                <input type="radio" name="rating" value="1"> 1
            </label>
            <label>
                <input type="radio" name="rating" value="2"> 2
            </label>
            <label>
                <input type="radio" name="rating" value="3"> 3
            </label>
            <label>
                <input type="radio" name="rating" value="4"> 4
            </label>
            <label>
                <input type="radio" name="rating" value="5" checked> 5
            </label>
        </div>
        <button class="w-full bg-indigo-600 text-white px-4 py-2 rounded">Поставить оценку</button>
    </form>

    <a href="{{ $url }}" class="font-medium text-red-600">Назад</a>
</div>
@endsection