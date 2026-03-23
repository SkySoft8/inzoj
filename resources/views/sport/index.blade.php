@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Тренировки</h2>

    <form action="{{ route('userTrainings') }}" class="flex gap-2">
        <p class="w-full bg-green-600 text-white text-center px-2 py-1 rounded">Онлайн тренировки</p>
        <button type="submit" class="w-full border border-black px-2 py-1 rounded">Мои тренировки</button>
    </form>

    @foreach ($trainings as $training)
    <div class="flex flex-col gap-1 border border-indigo-600 rounded-md p-1">
        @csrf
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
        <form method="POST" action="{{ route('signUpTraining') }}">
            @csrf
            <input type="hidden" name="training_id" value="{{ $training->id }}">
            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded">Записаться</button>
        </form>
    </div>
    @endforeach

    <a class="font-medium text-red-600" href="./filter-trainings">Назад</a>
</div>
@endsection