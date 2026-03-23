@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col justify-between">
    <h2 class="text-center text-3xl font-extrabold">Разблокируй путь к телу мечты!</h2>

    <form method="POST" action="{{ route('premium.purchase') }}">
        @csrf
        <select name="plan" class="w-full rounded px-3 py-2">
            <option value="">Выберите план</option>
            <option value="quarterly">3 месяца - 18$</option>
            <option value="yearly">12 месяцев - 39$</option>
        </select>
        
        <button type="submit" class="w-full bg-white text-indigo-600 py-2 rounded font-bold">
            Продолжить
        </button>
    </form>

    <a href="{{ route('profile') }}" class="rounded w-full py-2 px-4 font-medium text-red-600">
        Вернуться в профиль
    </a>
</div>
@endsection