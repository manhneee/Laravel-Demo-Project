<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;

class LabelController extends Controller
{
    
    public function index(Request $request) {
        $labels = Label::all();
        return response()->json($labels);
    }
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $label = Label::create($request->only(['name', 'description']));
        return response()->json($label, 201);
    }
    public function show($id) {
        $label = Label::find($id);
        return response()->json($label);
    }
    public function update(Request $request, Label $label) {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    }
    public function destroy(Label $label) {
        $label->delete();
        return response()->json([
            'message' => 'Label deleted successfully',
        ], 200);
    }
}
