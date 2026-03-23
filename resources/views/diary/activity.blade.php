@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Тренировки</h2>
    <p class="text-2xl text-center font-medium px-10">Какая физич. нагрузка у вас была сегодня?</p>
    
    <div class="flex flex-col gap-2">
        @foreach ($activities as $training)
            <form action="{{ route('training') }}" class="flex gap-2 justify-between bg-gray-200 rounded p-2">
                <input type="hidden" name="training_id" value="{{ $training->id }}">
                <input type="hidden" name="is_favorite" value="{{ $training->is_favorite ? true : false }}">
                <button type="submit" name="action" value="show" class="flex gap-2">
                    <p>{{ $training->name }}</p>
                    <p>{{ $training->time }}</p>
                    <p>{{ $training->calories }} ккал</p>
                </button>
                <button type="submit" name="action" value="toggle_favorite">{{ $training->is_favorite ? "❤" : "♡" }}</button>
            </form>
        @endforeach
    </div>

    <a href="{{ route('diary') }}" class="text-red-600">Назад</a>
</div>
@endsection