@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Шаги</h2>

    <div class="flex flex-col gap-1 bg-gray-200 p-2 rounded-md">
        <p class="text-дп">На 1000 шагов ~ 50 ккал</p>
    </div>

    <form method="POST" action="{{ route('activity.steps.update') }}" class="flex flex-col gap-2 w-full">
        @csrf
        <label>Количество шагов</label>
        <input type="number" name="stepsCount" min="100" max="60000" value="{{ $steps ?? 1000 }}"
            class="w-full border border-indigo-600 rounded-md p-1" required>
        <button class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full">Сохранить</button>
    </form>

    <a href="{{ route('diary') }}" class="text-red-600">Назад</a>
</div>
@endsection
