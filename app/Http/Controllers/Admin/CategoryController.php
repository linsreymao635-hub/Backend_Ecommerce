<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index(Request $r) {
        $search = $r->input('search');
        $categories = Category::withCount('products')
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.categories.index', ['categories' => $categories, 'search' => $search]);
    }

    public function create()            { return view('admin.categories.form', ['category' => new Category()]); }
public function store(Request $r) {
        $r->validate(['name' => 'required|unique:categories', 'description' => 'nullable']);
        Category::create($r->only('name', 'description'));
        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }
    public function edit($id) {
        $category = Category::findOrFail($id);
        return view('admin.categories.form', ['category' => $category]);
    }
    public function update(Request $r, Category $cat) {
        $r->validate(['name' => 'required|unique:categories,name,'.$cat->id, 'description' => 'nullable']);
        $cat->update($r->only('name', 'description'));
        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }
    public function destroy($id) {
        $category = Category::findOrFail($id);

        // Log before deletion
        Log::info('BEFORE DELETE - Category ID: ' . $category->id . ', Name: ' . $category->name);
        Log::info('BEFORE DELETE - Total categories: ' . Category::count());

        // Delete the category
        $category->delete();

        // Log after deletion
        Log::info('AFTER DELETE - Total categories: ' . Category::count());

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
