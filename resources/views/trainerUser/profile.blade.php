@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Мой профиль</h2>

    <div class="flex flex-col gap-1">
        <p>Имя: {{ $trainer->name ?? "Не указано" }}</p>
        <p>Фамилия: {{ $trainer->surname ?? "Не указано" }}</p>
        <p>Дата рождения: {{ isset($trainer->birthday) ? $trainer->birthday->format('d.m.Y') : "Не указано" }}</p>
        <p>Стаж: {{ $trainer->experience ?? "Не указано" }}</p>
        <p>Награды и достижения: {{ $trainer->achievements ?? "Не указано" }}</p>
        <p>Рейтинг: {{ isset($trainer->rating) ? round($trainer->rating/$trainer->rating_count, 2) : "Еще нет оценок" }}</p>
    </div>

    <form action="{{ route('trainerUser.changeData') }}">
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Изменить данные</button>
    </form>

    <form method="POST" action="{{ route('trainerUser.logout') }}">
        @csrf
        <button type="submit" class="rounded w-full flex justify-center py-2 px-4 font-medium text-red-600">
            Выйти из аккаунта
        </button>
    </form>
    
    <div class="flex justify-center gap-4">
        <a href="{{ route('trainerUser.showAllTrainings') }}" class="text-gray-600">Мои тренировки</a>
        <a href="#" class="text-green-600 font-medium">Профиль</a>
    </div>
</div>
@endsection