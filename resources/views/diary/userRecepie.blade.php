@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col justify-between">
    <h2 class="text-center text-3xl font-extrabold">Собственный рецепт</h2>

    <form action="{{ route('recepie.createUserRecepie') }}" class="flex flex-col gap-2">
        <input type="text" name="name" placeholder="Название">
        <input type="number" name="ingredient_ids[]" placeholder="id инредиента">
        <textarea name="steps" placeholder="Как готовить"></textarea>
        @foreach ([
            'meal_types' => 'Приемы пищи',
            'components' => 'Ингредиенты',
            'cooking_methods' => 'Методы',
            'diets' => 'Диеты',
        ] as $column => $title)
            <div class="font-medium">{{ $title }}</div>
            <div class="flex flex-wrap gap-2">
                @foreach ($categories[$column] ?? [] as $option)
                    <label class="flex items-center gap-1 text-sm border rounded px-2 py-1">
                        <input type="checkbox" name="{{ $column }}[]" value="{{ $option['value'] }}">
                        {{ $option['label'] }}
                    </label>
                @endforeach
            </div>
        @endforeach
        <div class="flex justify-between">
            <input class="w-20" type="number" name="calories" placeholder="калл">
            <input class="w-20" type="number" name="proteins" placeholder="белки">
            <input class="w-20" type="number" name="fats" placeholder="жиры">    
            <input class="w-20" type="number" name="carbs" placeholder="углеводы">    
        </div>
        <button type="submit" class="bg-indigo-600 text-white py-1 rounded-md">Создать</button>
    </form>

    <a href="{{ route('meal') }}" class="text-red-600">Назад</a>
</div>
@endsection