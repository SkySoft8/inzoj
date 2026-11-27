@extends('layouts.moderator')

@section('content')
<div class="flex flex-wrap justify-between items-center gap-3 mb-6">
    <h2 class="text-2xl font-extrabold">Продукты</h2>
    <a href="{{ route('moderator.products.create') }}" class="rounded bg-indigo-600 text-white px-4 py-2 font-medium">Добавить продукт</a>
</div>

<form method="GET" action="{{ route('moderator.products') }}" class="flex gap-2 mb-4">
    <input type="text" name="q" value="{{ $query }}" placeholder="Название или штрихкод" class="w-full rounded border px-3 py-2">
    <button type="submit" class="rounded bg-white border px-4 py-2 font-medium">Найти</button>
</form>

<div class="bg-white rounded-lg border overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Название</th>
                <th class="px-4 py-3">Штрихкод</th>
                <th class="px-4 py-3">Ккал</th>
                <th class="px-4 py-3">Б / Ж / У</th>
                <th class="px-4 py-3">Нутриенты</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr class="border-t">
                    <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $product->shortcode ?: '—' }}</td>
                    <td class="px-4 py-3">{{ $product->calories }}</td>
                    <td class="px-4 py-3">{{ $product->proteins }} / {{ $product->fats }} / {{ $product->carbs }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ count($product->nutrients ?? []) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('moderator.products.edit', $product->id) }}" class="text-indigo-600 font-medium">Изменить</a>
                            <form method="POST" action="{{ route('moderator.products.destroy', $product->id) }}" onsubmit="return confirm('Удалить продукт?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-medium">Удалить</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Продукты не найдены</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $products->links() }}</div>
@endsection
