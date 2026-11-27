<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Nutrient;
use App\Models\Product;
use App\Models\UserFavoriteProduct;
use App\Models\UserMeal;
use App\Models\UserRecepieItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'q' => 'nullable|string|max:100',
        ]);

        $query = Product::query()->orderBy('name');

        if (!empty($validated['q'])) {
            $search = $validated['q'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%'.$search.'%')
                    ->orWhere('shortcode', 'like', '%'.$search.'%');
            });
        }

        $products = $query->paginate(20)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'products' => $products->items(),
                'meta' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ],
            ]);
        }

        return view('moderator.products.index', [
            'products' => $products,
            'query' => $validated['q'] ?? '',
        ]);
    }

    public function create(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product' => null,
                'nutrients' => $this->nutrients(),
            ]);
        }

        return view('moderator.products.form', [
            'product' => new Product(),
            'nutrients' => $this->nutrients(),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->payload($request);
        $product = Product::create($payload);

        return $this->saved($request, $product, 'Продукт создан');
    }

    public function edit(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'product' => $product,
                'nutrients' => $this->nutrients(),
            ]);
        }

        return view('moderator.products.form', [
            'product' => $product,
            'nutrients' => $this->nutrients(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($this->payload($request, $product));

        return $this->saved($request, $product->fresh(), 'Продукт обновлён');
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $inUse = UserMeal::where('product_id', $product->id)->exists()
            || UserRecepieItem::where('product_id', $product->id)->exists()
            || UserFavoriteProduct::where('product_id', $product->id)->exists();

        if ($inUse) {
            $message = 'Продукт используется в дневнике или рецептах и не может быть удалён.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return back()->withErrors(['product' => $message]);
        }

        $product->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted',
            ]);
        }

        return redirect()->route('moderator.products')->with('success', 'Продукт удалён');
    }

    private function payload(Request $request, ?Product $product = null): array
    {
        $nameRule = Rule::unique('products', 'name');
        if ($product) {
            $nameRule->ignore($product->id);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', $nameRule],
            'shortcode' => 'nullable|string|max:64',
            'calories' => 'required|numeric|min:0|max:9999.9',
            'proteins' => 'required|numeric|min:0|max:999.9',
            'fats' => 'required|numeric|min:0|max:999.9',
            'carbs' => 'required|numeric|min:0|max:999.9',
            'serving_label' => 'nullable|string|max:50',
            'serving_grams' => 'nullable|integer|min:0|max:100000',
            'serving_unit' => 'nullable|in:g,pcs,ml',
            'nutrients' => 'nullable|array',
            'nutrients.*' => 'nullable|numeric|min:0',
        ]);

        return [
            'name' => $validated['name'],
            'shortcode' => $validated['shortcode'] ? trim($validated['shortcode']) : null,
            'calories' => $validated['calories'],
            'proteins' => $validated['proteins'],
            'fats' => $validated['fats'],
            'carbs' => $validated['carbs'],
            'serving_label' => $validated['serving_label'] ?? null,
            'serving_grams' => $validated['serving_grams'] ?? null,
            'serving_unit' => $validated['serving_unit'] ?? null,
            'nutrients' => $this->nutrientPayload($validated['nutrients'] ?? []),
        ];
    }

    private function nutrientPayload(array $submitted): array
    {
        $codes = $this->nutrients()->pluck('code')->all();
        $nutrients = [];

        foreach ($codes as $code) {
            $value = $submitted[$code] ?? null;

            if ($value === null || $value === '') {
                continue;
            }

            $nutrients[$code] = (float) $value;
        }

        return $nutrients;
    }

    private function nutrients()
    {
        return Nutrient::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->get();
    }

    private function saved(Request $request, Product $product, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'product' => $product,
            ]);
        }

        return redirect()->route('moderator.products.edit', $product->id)->with('success', $message);
    }
}
