@extends('layouts.app')

@section('content')
<form class="gap-4 w-96 flex flex-col justify-between" method="POST" action="{{ route('trainerUser.registerCheck') }}" id="registerForm">
    @csrf
    <h2 class="text-center text-3xl font-extrabold">Регистрация аккаунта тренера</h2>

    @if ($errors->any())
        <div class="bg-red-100 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <input type="email" name="email" required class="w-full rounded px-3 py-2" placeholder="Email">
    <input type="password" name="password" required class="w-full rounded px-3 py-2" placeholder="Пароль">
    <input type="password" name="password_confirmation" required class="w-full rounded px-3 py-2" placeholder="Повторите пароль">

    <button type="submit" class="rounded w-full flex justify-center py-2 px-4 font-medium text-white bg-indigo-600">
        Зарегистрироваться
    </button>
    
    <div class="text-center flex flex-col items-center gap-1">
        <a href="./login" class="font-medium text-indigo-600">У меня уже есть аккаунт тренера</a>
        <a href="{{ route('user.login') }}" class="font-medium text-indigo-600">Вернуться к стандартной авторизации</a>
    </div>
</form>
@endsection