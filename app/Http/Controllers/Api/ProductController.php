<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Product, Category, Review};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request) {
        $q = Product::with('category')->where('is_active', true);
        if ($request->category_id) $q->where('category_id', $request->category_id);
        if ($request->search)      $q->where('name', 'like', '%' . $request->search . '%');
        if ($request->min_price)   $q->where('price', '>=', $request->min_price);
        if ($request->max_price)   $q->where('price', '<=', $request->max_price);

        $perPage = $request->get('per_page', 12);
        $products = $q->orderByDesc('id')->paginate($perPage);

        return response()->json($products);
    }

    public function show($id) {
        $product = Product::with(['category', 'reviews.user'])->findOrFail($id);
        return response()->json($product);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only('category_id', 'name', 'slug', 'description', 'price', 'stock', 'is_active');

        // Only process image if file is uploaded
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);

        // Clear products cache
        Cache::flush();

        return response()->json([
            'message' => 'Product created successfully',
            'product' => $product,
        ], 201);
    }

    public function update(Request $request, $id) {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only('category_id', 'name', 'slug', 'description', 'price', 'stock', 'is_active');

        // Only process image if a new file is uploaded
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        // Clear products cache
        Cache::flush();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product,
        ]);
    }

    public function destroy($id) {
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        // Clear products cache
        Cache::flush();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function categories() {
        return response()->json(Category::withCount('products')->get());
    }

    public function image($id) {
        $product = Product::findOrFail($id);

        if (!$product->image) {
            return response()->json(['message' => 'No image found'], 404);
        }

        $path = storage_path('app/public/' . $product->image);

        if (!file_exists($path)) {
            return response()->json(['message' => 'Image file not found'], 404);
        }

        $mimeType = mime_content_type($path);
        $file = file_get_contents($path);

        return response($file, 200)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . basename($path) . '"');
    }
}
