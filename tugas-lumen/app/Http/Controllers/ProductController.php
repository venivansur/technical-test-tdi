<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
class ProductController extends Controller
{
public function index() {
    return Product::all();
}

public function store(Request $request) {
    $this->validate($request, [
        'name' => 'required|min:3',
        'price' => 'required|numeric|gt:0',
        'stock' => 'required|integer|min:0'
    ]);
    return Product::create($request->all());
}
}