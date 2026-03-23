@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">{{ $training->name }}</h2>

    <div class="flex flex-col gap-1 bg-gray-200 p-4 rounded-md">
        <p class="text-md">{{ $training->time }} ~ {{ $training->calories }} ккал</p>
    </div>

    @php
        $timeCount = explode(' ', $training->time)[0];
    @endphp

    <form method="POST" action="{{ route('training.add') }}" class="flex flex-col gap-2 w-full">
        @csrf
        <input type="hidden" name="training_id" value="{{ $training->id }}">
        <label class="flex gap-1"><input type="number" name="calories" min="20" max="3000" value="{{ $training->calories }}"
            class="w-full border border-indigo-600 rounded-md p-1" required>ккал
        </label>
        <label class="flex gap-1">
            <input type="number" name="time_count" min="1" max="1000" value="{{ $timeCount }}"
                class="w-full border border-indigo-600 rounded-md p-1" required>
            <select name="time_type">
                <option value="minute">мин</option>
                <option value="hour">ч</option>
            </select>
        </label>
        <button class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full">Сохранить</button>
    </form>

    <a href="{{ route('activity') }}" class="text-red-600">Назад</a>
</div>
@endsection