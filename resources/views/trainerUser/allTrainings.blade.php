@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Мои тренировки</h2>

    <h4 class="text-xl font-bold">Активные тренировки</h4>
    @foreach ($trainings as $training)
        <form method="POST" action="{{ route('trainerUser.editTraining') }}">
            @csrf
            <button class="w-full flex flex-col text-left gap-1 border border-indigo-600 rounded-md p-1">
                <input type="hidden" name="id" value="{{ $training->id }}">
                <p>{{ $training->name }} ({{ $training->time_amount }} мин)</p>
                <p>Категория: {{ $categories[$training->category_id] }}</p>
                <div class="border border-gray-600"></div>
                <p>{{ $training->description }}</p>
                <div class="border border-gray-600"></div>
                <div class="grid grid-cols-3">
                    <p>{{ $training->date->format('d.m') }}</p>
                    <p>{{ $training->start_time->format('H:i') }}</p>
                    <p>{{ $training->price }}р</p>
                </div>
            </button>
        </form>
    @endforeach

    <form action="{{ route('trainerUser.newTraining') }}">
        <button type="submit" class=" w-full text-center bg-indigo-600 text-white px-4 py-2 rounded">Добавить тренировку</button>
    </form>

    <h4 class="text-xl font-bold">История тренировок</h4>
    @foreach ($oldTrainings as $training)
        <div class="w-full flex flex-col text-left gap-1 border border-indigo-600 rounded-md p-1">
            <p>{{ $training->name }} ({{ $training->time_amount }} мин)</p>
            <p>Категория: {{ $categories[$training->category_id] }}</p>
            <div class="border border-gray-600"></div>
            <p>{{ $training->description }}</p>
            <div class="border border-gray-600"></div>
            <div class="grid grid-cols-3">
                <p>{{ $training->date->format('d.m') }}</p>
                <p>{{ $training->start_time->format('H:i') }}</p>
                <p>{{ $training->price }}р</p>
            </div>
        </div>
    @endforeach

    <div class="flex justify-center gap-4">
        <a href="#" class="text-green-600">Мои тренировки</a>
        <a href="{{ route('trainerUser.profile') }}" class="text-gray-600 font-medium">Профиль</a>
    </div>
</div>
@endsection