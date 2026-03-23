@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Рестораны</h2>

    <form action="{{ route('restaurant') }}" class="flex flex-col gap-4 p-4 bg-gray-200 rounded-md">
        <h4 class="font-medium text-xl">Что поесть</h4>
        <div class="grid grid-cols-4 gap-2 items-start">
            <button type="submit" value="cafe" name="restaurant_type" class="font-medium">Кафе</button>
            <button type="submit" value="canteen" name="restaurant_type" class="font-medium">Столовая</button>
            <button type="submit" value="fine_restaurant" name="restaurant_type" class="font-medium max-w-20">Ресторан высокой кухни</button>
            <button type="submit" value="fast_food" name="restaurant_type" class="font-medium">Фаст-фуд</button>
            <button type="submit" value="bistro" name="restaurant_type" class="font-medium">Бистро</button>
            <button type="submit" value="pub" name="restaurant_type" class="font-medium">Паб</button>
            <button type="submit" value="bar" name="restaurant_type" class="font-medium">Бар</button>
            <button type="submit" value="other" name="restaurant_type" class="font-medium">Другое</button>
        </div>
    </form>

    <form action="{{ route('restaurant.menu') }}" class="grid grid-cols-2 gap-4">
        @foreach ($restaurants as $restaurant)
            <button class="bg-gray-200 pb-2 flex flex-col gap-1" name="restaurant_id" value="{{ $restaurant->id }}">
                <img src="https://restoplus.com.ua/content/uploads/images/1680127089_food-pibig-info-p-blyuda-restorannie-krasivo-1.jpg">
                <h6>{{ $restaurant->name }}</h6>
            </button>
        @endforeach
    </form>        

    <div class="flex justify-between">
        <a href="{{ route('diary') }}" class="text-gray-600">Дневник</a>
        <a href="#" class="text-green-600 font-medium">Рестораны</a>
        <a href="{{ route('sport') }}" class="text-gray-600">Тренировки</a>
        <a href="{{ route('profile') }}" class="text-gray-600">Профиль</a>
    </div>
</div>
@endsection