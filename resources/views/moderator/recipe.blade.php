@extends('layouts.moderator')

@section('content')
<h2 class="text-center text-3xl font-extrabold mb-6">{{ $recipe->name }}</h2>

<div class="bg-white rounded-lg p-4 border flex flex-col gap-2">
    @if ($recipe->image_url)
        <img src="{{ $recipe->image_url }}" alt="{{ $recipe->name }}" style="width:100%;max-height:320px;object-fit:cover;border-radius:8px;">
    @endif
    <div class="flex justify-between">
        @include('moderator.partials.status-badge', ['item' => $recipe])
        <span class="text-sm text-gray-500">{{ $recipe->created_at }}</span>
    </div>
    <div><span class="font-medium">Автор:</span> {{ $recipe->user->email ?? '—' }}</div>
    <div><span class="font-medium">Порций:</span> {{ $recipe->portions }}</div>
    <div>
        <span class="font-medium">На 1 порцию ({{ $recipe->per_portion['grams'] }} г):</span>
        {{ $recipe->per_portion['calories'] }} ккал /
        Б {{ $recipe->per_portion['proteins'] }} /
        Ж {{ $recipe->per_portion['fats'] }} /
        У {{ $recipe->per_portion['carbs'] }}
    </div>
    <div>
        <div class="font-medium">Состав</div>
        @if ($recipe->items->isNotEmpty())
            <ul class="list-disc pl-5">
                @foreach ($recipe->items as $item)
                    <li>{{ $item->product->name ?? 'Продукт удалён' }} — {{ $item->grams }} г</li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-500">Состав из продуктов не указан</p>
        @endif
    </div>
    <div>
        <div class="font-medium">Приготовление</div>
        <ol class="list-decimal pl-5">
            @foreach ($recipe->steps as $step)
                <li>{{ $step }}</li>
            @endforeach
        </ol>
    </div>
    @if ($recipe->moderation_comment)
        <div><span class="font-medium">Комментарий:</span> {{ $recipe->moderation_comment }}</div>
    @endif
    <form method="POST" action="{{ route('moderator.recipes.review', $recipe->id) }}" class="flex flex-col gap-3 mt-2">
        @csrf
        @foreach ([
            'meal_types' => 'Приемы пищи',
            'components' => 'Ингредиенты',
            'cooking_methods' => 'Методы',
            'diets' => 'Диеты',
        ] as $column => $title)
            @php
                $group = [
                    'meal_types' => 'meal_type',
                    'components' => 'component',
                    'cooking_methods' => 'cooking_method',
                    'diets' => 'diet',
                ][$column];
                $selected = $recipe->{$column} ?? [];
            @endphp
            <div>
                <div class="font-medium">{{ $title }}</div>
                <div class="flex flex-wrap gap-2 mt-1">
                    @foreach (\App\Services\DiaryBrowse::FILTERS[$group] as $value)
                        <label class="flex items-center gap-1 text-sm border rounded px-2 py-1">
                            <input type="checkbox" name="{{ $column }}[]" value="{{ $value }}" @checked(in_array($value, $selected, true))>
                            {{ \App\Services\DiaryBrowse::LABELS[$value] }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
        <button type="submit" name="action" value="tags" class="rounded w-full py-2 px-4 font-medium border">
            Сохранить категории
        </button>
        <button type="submit" name="action" value="approve" class="rounded w-full py-2 px-4 font-medium text-white bg-green-600">
            Одобрить
        </button>
    </form>
    <form method="POST" action="{{ route('moderator.recipes.review', $recipe->id) }}" class="flex gap-2">
        @csrf
        <input type="hidden" name="action" value="reject">
        <input type="text" name="comment" value="{{ old('comment') }}" class="flex-1 rounded px-3 py-2" placeholder="Комментарий (необязательно)">
        <button type="submit" class="rounded py-2 px-4 font-medium text-white bg-red-600">
            Отклонить
        </button>
    </form>
</div>

<a href="{{ route('moderator.recipes') }}" class="block text-center mt-4 text-red-600 font-medium">Назад к списку</a>
@endsection
