@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Тренировка</h2>

    <form method="POST" action="{{ route('trainerUser.trainingAction') }}" class="flex flex-col gap-2">
        @csrf
        <label class="w-full">Название<br>
            <input type="text" name="name" class="w-full border border-indigo-600 rounded-md p-1" value="{{ $training->name ?? ' ' }}">      
        </label>
        <label class="w-full">Длительность тренировки (мин)<br>
            <input type="number" name="time_amount" class="w-full border border-indigo-600 rounded-md p-1" value="{{ $training->time_amount ?? ' ' }}">
        </label>
        <label class="w-full">
            <span>Описание</span><br>
            <textarea name="description" class="w-full border border-indigo-600 rounded-md p-1 mr-1">{{ $training->description ?? ' ' }}</textarea>
        </label>
        <label class="w-full">Стоимость тренировки (р)<br>
            <input type="number" name="price" class="w-full border border-indigo-600 rounded-md p-1" value="{{ $training->price ?? ' ' }}">
        </label>
        <label class="w-full">Начало тренировки<br>
            <input type="time" name="start_time" class="w-full border border-indigo-600 rounded-md p-1" value="{{ isset($training) ? $training->start_time->format('H:i') : ' ' }}">
        </label>
        <label class="w-full mb-1">Дата тренировки<br>
            <input type="date" name="date" min="{{ now()->format('Y-m-d') }}" max="{{ now()->addYear()->format('Y-m-d') }}" class="w-full border border-indigo-600 rounded-md p-1" value="{{ isset($training) ? $training->date->format('Y-m-d') : ' ' }}">
        </label>
        <label class="w-full mb-1">Категория<br>
            <select name="category_id" class="w-full mt-1 border border-indigo-600 rounded-md p-1">
                @foreach ($trainingCategories as $id => $category)
                    @if (isset($training) && $training->category_id == $id)
                        <option value="{{ $id }}" selected>{{ $category }}</option>
                    @else
                        <option value="{{ $id }}">{{ $category }}</option>
                    @endif
                @endforeach
            </select>
        </label>
        @if (isset($training))
            <input type="hidden" name="id" value="{{ $training->id }}">
            <button type="submit" name="training_action" value="update" class="bg-indigo-600 text-white px-4 py-2 rounded">Сохранить</button>
            <button type="submit" name="training_action" value="delete" class="bg-red-600 text-white px-4 py-2 rounded">Удалить тренировку</button>
        @else
            <button type="submit" name="training_action" value="create" class="bg-indigo-600 text-white px-4 py-2 rounded">Добавить</button>
        @endif
    </form>

    <a class="font-medium text-red-600" href="./all-trainings">Назад</a>
</div>
@endsection