<?php

namespace App\Http\Controllers;


use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function index(Request $request) {
        return response()->json(Priority::orderBy('name')->get());
    }
}
