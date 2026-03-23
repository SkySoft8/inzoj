@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Фильтры</h2>

    <form action="{{ route('filter.apply') }}" class="flex flex-col gap-2">
        <div>
            <p class="font-medium">Приемы пищи</p>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="meal_type[]" value="breakfast">Завтрак
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="meal_type[]" value="lunch">Обед
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="meal_type[]" value="dinner">Ужин
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="meal_type[]" value="snack">Перекус
            </label>
        </div>

        <div>
            <p class="font-medium">Ингредиенты</p>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="component[]" value="poultry">Птицы
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="component[]" value="meat">Мясо
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="component[]" value="fish">Рыба
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="component[]" value="vegetables">Овощи
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="component[]" value="fruits">Фрукты
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="component[]" value="sweet">Сладко
            </label>
        </div>

        <div>
            <p class="font-medium">Методы</p>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="cooking_method[]" value="boiled">Вареное
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="cooking_method[]" value="steamed">На пару
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="cooking_method[]" value="fried">Жареное
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="cooking_method[]" value="stew">Тушеное
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="cooking_method[]" value="baked">Запеченое
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="cooking_method[]" value="basic">Базовое
            </label>
        </div>

        <div>
            <p class="font-medium">Диеты</p>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="vegetarian">Вегетарианский
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="vegan">Веганский
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="low_fat">Мало жиров
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="lots_of_fiber">Много клетчатки
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="low_carb">Низкоуглеводный
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="keto_diet">Кетодиета
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="high_protein">Высокобелковый
            </label>
            <label class="flex items-center gap-1">
                <input type="checkbox" name="diet[]" value="lactose_free">Без лактозы
            </label>
        </div>

        <button type="submit" class="bg-indigo-600 text-white font-bold p-2 rounded-md w-full mt-1">Применить</button>
    
        <a href="{{ route('meal') }}" class="text-red-600">Назад</a>
    </form>
</div>
@endsection