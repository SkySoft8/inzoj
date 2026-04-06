@extends('layouts.app')

@section('content')
<div class="gap-4 w-96 flex flex-col">
    <h2 class="text-center text-3xl font-extrabold">Дневник</h2>

    <form action="{{ route('diary') }}" class="w-full flex justify-between">
        <input type="hidden" name="date" value="{{ $date }}">
        <button type="submit" value="back" name="movement" {{ $issetDays['dayBefore'] ? '' : 'disabled' }}><</button>
        <h4>{{ $date == date('Y-m-d') ? 'Сегодня' : $date }}</h4>
        <button type="submit" value="forward" name="movement" {{ $issetDays['dayAfter'] ? '' : 'disabled' }}>></button>
    </form>

    <div class="flex flex-col gap-2 p-4 bg-gray-200 rounded-md">
        <div class="w-full grid grid-cols-3">
            <div class="flex flex-col items-center gap-1">
                <p>Потребление</p>
                <p>{{ $noteData->current_calories ?? '0' }}</p>
            </div>
            <div class="flex flex-col items-center gap-1">
                <p>Осталось</p>
                @if ($noteData->left_calories !== null)
                    <p class="text-center">{{ $noteData->left_calories }}</p>
                @else
                    <a href="{{ route('targets') }}" class="text-center text-indigo-600">Введите норму калорий</a>
                @endif
            </div>
            <div class="flex flex-col items-center gap-1">
                <p>Расход</p>
                <p>{{ $noteData->burned_calories ?? '0' }}</p>
            </div>
        </div>

        <div class="w-full grid grid-cols-3">
            <div class="flex flex-col items-center gap-1">
                <p>Белки</p>
                <p>{{ $noteData->current_proteins ?? '0' }}</p>
            </div>
            <div class="flex flex-col items-center gap-1">
                <p>Жиры</p>
                <p>{{ $noteData->current_fats ?? '0' }}</p>
            </div>
            <div class="flex flex-col items-center gap-1">
                <p>Углеводы</p>
                <p>{{ $noteData->current_carbs ?? '0' }}</p>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-2 p-4 bg-gray-200 rounded">
        @foreach ($mealTypes as $type => $ruType)
        <div class="flex flex-col gap-1 border border-indigo-600 border-1 rounded-md">
            <form action="{{ route('meal') }}" class="flex flex-col gap-1">
                <input type="hidden" name="meal_type" value="{{ $type }}">
                <button type="submit" class="bg-indigo-600 text-white py-1 rounded-md">{{ $ruType }}</button>
                @if (isset($userMealData[$type]))
                    <div class="w-full grid grid-cols-4 pl-2">
                        <div class="flex flex-col gap-1">
                            <p>Белки</p>
                            <p>{{ $userMealData[$type]['proteins'] }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p>Жиры</p>
                            <p>{{ $userMealData[$type]['fats'] }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p>Углеводы</p>
                            <p>{{ $userMealData[$type]['carbs'] }}</p>
                        </div>
                        <div class="flex flex-col gap-1">
                            <p>Калории</p>
                            <p>{{ $userMealData[$type]['calories'] }}</p>
                        </div>
                    </div>
                @endif
            </form>

            @if (isset($userMealData[$type]))
                <div class="w-full flex flex-col gap-1 px-2 pb-2">
                    @foreach ($userMealData[$type]['food'] as $index => $item)
                        <form action="{{ route('diary.item') }}" class="border-t border-gray-600">
                            <input type="hidden" name="user_meal_id" value="{{ $item['user_meal_id'] }}">
                            <input type="hidden" name="item_type" value="{{ $item['item_type'] }}">
                            <button type="submit" class="flex flex-col items-start">
                                <p>{{ $item['item']->name }}</p>
                                <p class="text-gray-600 text-sm">({{ $item['amount'] }}гр)</p>
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="flex flex-col bg-gray-200 p-4 gap-1 rounded-md">
        <div class="flex justify-between">
            <p>Вода</p>
            <p>{{ $noteData->current_water }} л</p>
            @if ($user->water)
                <p>Цель: {{ $user->water }} л</p>
            @else
                <a href="{{ route('targets') }}" class="text-indigo-600">Введите норму воды</a>
            @endif
        </div>
        <form method="POST" action="{{ route('water') }}" class="grid grid-cols-6">
            @csrf
            @for ($i = 0; $i < round($noteData->current_water/0.25); $i++)
                @if ($i == round($noteData->current_water/0.25) - 1)
                    <button type="submit" name="type" value="full">Полная</button>
                @else
                    <p>Полная</p>
                @endif
            @endfor
            @for ($i = 0; $i < ceil(($user->water-$noteData->current_water)/0.25); $i++)
                @if ($i == 0)
                    <button type="submit" name="type" value="empty">Пустая</button>
                @else
                    <p>Пустая</p>
                @endif
            @endfor
        </form>
    </div>

    <div class="flex flex-col bg-gray-200 p-4 gap-1 rounded-md">
        <form action="{{ route('activity') }}" class="flex flex-col gap-2 rounded-lg bg-gray-200 p-4">
            <button type="submit" name="activityType" value="walking" class="bg-indigo-600 text-white py-1 rounded-md">Шаги {{ $noteData->current_steps ?? 0 }}</button>
            <button type="submit" name="activityType" value="training" class="bg-indigo-600 text-white py-1 rounded-md">Активность                
        </form>

        @if (!empty($userActivityData))
            @foreach ($userActivityData as $item)
                <form action="{{ route('training') }}" class="w-full flex flex-col gap-1 pl-2 pb-2">
                    <input type="hidden" name="user_activity_id" value="{{ $item['activity']->id }}">
                    <button type="submit" class="flex justify-between">
                        <p>{{ $item['name'] }}</p>
                        <p>{{ $item['activity']->time_count }} {{ $item['activity']->time_type }}</p>
                        <p>{{ $item['activity']->calories }} ккал</p>
                    </button>
                </form>
            @endforeach
        @endif
    </div>

    <div class="flex justify-between">
        <a href="#" class="text-green-600 font-medium">Дневник</a>
        <a href="{{ route('restaurant') }}" class="text-gray-600">Рестораны</a>
        <a href="{{ route('sport') }}" class="text-gray-600">Тренировки</a>
        <a href="{{ route('profile') }}" class="text-gray-600">Профиль</a>
    </div>
</div>
@endsection