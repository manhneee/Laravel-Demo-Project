<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Category::orderBy('name')->get());
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'string|required|max:255',
            'description' => 'nullable|string',
        ];
        $request->validate($rules);
        $category = Category::create($request->only(['name', 'description']));
        return response()->json($category, 201);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        return response()->json($category);
    }

    public function destroy(Category $category) {
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully',
        ], 200);
    }
}
