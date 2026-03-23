@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col justify-between">
    <h2 class="text-center text-3xl font-extrabold">Особенности рациона</h2>

    <form method="POST" action="{{ route('ration.update') }}" class="space-y-3 mb-6">
        @csrf
        <h4 class="text-lg font-medium mb-2">Предпочтения в еде</h4>
        <div class="flex flex-col gap-3 border-gray-300 rounded mb-4">
            <label class="flex justify-between font-medium">
                Нет предпочтений
                <input type="radio" name="food_preferences" value="no_preferences"
                {{ $food_preferences == 'no_preferences' ? 'checked' : '' }}>
            </label>
            <label class="flex justify-between font-medium">
                Веган
                <input type="radio" name="food_preferences" value="vegan"
                {{ $food_preferences == 'vegan' ? 'checked' : '' }}>
            </label>
            <label class="flex justify-between font-medium">
                Вегетарианец
                <input type="radio" name="food_preferences" value="vegetarian"
                {{ $food_preferences == 'vegetarian' ? 'checked' : '' }}>
            </label>
        </div>

        <div>
            <h4 class="text-lg font-medium mb-2">Аллергии</h4>
            @foreach($all_allergies as $key => $label)
            <label class="flex items-center space-x-2 p-3 border rounded-lg">
                <input type="checkbox" name="allergies[]" value="{{ $key }}" 
                    {{ in_array($key, $userAllergies) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-indigo-600">
                <span class="font-medium">{{ $label }}</span>
            </label>
            @endforeach
        </div>

        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">
            Сохранить
        </button>
    </form>

    <form method="GET" action="{{ route('profile') }}">
        @csrf
        <button type="submit" class="rounded w-full flex justify-center py-2 px-4 font-medium text-red-600">
            Назад в профиль
        </button>
    </form>
</div>
@endsection