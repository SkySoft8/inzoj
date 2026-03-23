@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Данные о тренере</h2>

    <form method="POST" action="{{ route('trainerUser.updateData') }}" class="flex flex-col gap-2">
        @csrf
        <label class="w-full">Имя<br>
            <input type="text" name="name" class="w-full border border-indigo-600 rounded-md p-1" value="{{ $trainer->name ?? ' ' }}">      
        </label>
        <label class="w-full">Фамилия<br>
            <input type="text" name="surname" class="w-full border border-indigo-600 rounded-md p-1" value="{{ $trainer->surname ?? ' ' }}">
        </label>
        <label class="w-full">День рождения<br>
            <input type="date" name="birthday" max="{{ now()->format('Y-m-d') }}" class="w-full border border-indigo-600 rounded-md p-1" value="{{ $trainer->birthday ?? ' ' }}">      
        </label>
        <label class="w-full">Стаж (количество лет)<br>
            <input type="number" name="experience" class="w-full border border-indigo-600 rounded-md p-1" value="{{ $trainer->experience ?? ' ' }}">
        </label>
        <label class="w-full">
            <span>Достижения и награды (если нет, оставить пустым)</span><br>
            <textarea name="achievements" class="w-full border border-indigo-600 rounded-md p-1 mr-1">{{ $trainer->achievements ?? ' ' }}</textarea>
        </label>
        
        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Сохранить</button>
    </form>
</div>
@endsection