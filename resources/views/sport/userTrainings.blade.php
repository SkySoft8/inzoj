@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Тренировки</h2>

    <form action="{{ route('sport') }}" class="flex gap-2">
        <button type="submit" class="w-full border border-black px-2 py-1 rounded">Онлайн тренировки</button>
        <p class="w-full bg-green-600 text-white text-center px-2 py-1 rounded">Мои тренировки</p>
    </form>

    <h4 class="text-xl font-bold">Ваши записи</h4>
    @foreach ($userTrainings as $training)
    <div class="flex flex-col gap-1 border border-indigo-600 rounded-md p-1">
        <p>{{ $training->name }} ({{ $training->time_amount }} мин)</p>
        <p>Категория: {{ $categories[$training->category_id] }}</p>
        <div class="border border-gray-600"></div>
        <p>{{ $training->description }}</p>
        <div class="border border-gray-600"></div>
        <form method="POST" action="{{ route('trainer') }}">Тренер: 
            @csrf
            <button name="trainer_id" value="{{ $training->trainer_user_id }}" class="underline">{{ $trainers[$training->trainer_user_id] }}</button>
        </form>
        <div class="grid grid-cols-3">
            <p>{{ $training->date->format('d.m') }}</p>
            <p>{{ $training->start_time->format('H:i') }}</p>
            <p>{{ $training->price }}р</p>
        </div>
        <form method="POST" action="{{ route('revoke') }}">
            @csrf
            <input type="hidden" name="training_id" value="{{ $training->id }}">
            <button type="submit" class="w-full border border-black px-4 py-2 rounded">Отменить запись</button>
        </form>
    </div>
    @endforeach

    <h4 class="text-xl font-bold">История тренировок</h4>
    @foreach ($oldTrainings as $training)
    <div class="flex flex-col gap-1 border border-indigo-600 rounded-md p-1">
        <p>{{ $training->name }} ({{ $training->time_amount }} мин)</p>
        <p>Категория: {{ $categories[$training->category_id] }}</p>
        <div class="border border-gray-600"></div>
        <p>{{ $training->description }}</p>
        <div class="border border-gray-600"></div>
        <form method="POST" action="{{ route('trainer') }}">Тренер: 
            @csrf
            <button name="trainer_id" value="{{ $training->trainer_user_id }}" class="underline">{{ $trainers[$training->trainer_user_id] }}</button>
        </form>
        <div class="grid grid-cols-3">
            <p>{{ $training->date->format('d.m') }}</p>
            <p>{{ $training->start_time->format('H:i') }}</p>
            <p>{{ $training->price }}р</p>
        </div>
    </div>
    @endforeach

    <a class="font-medium text-red-600" href="./filter-trainings">Назад</a>

</div>
@endsection