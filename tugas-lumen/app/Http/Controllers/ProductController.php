<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Get all products
    public function index() {
        return Product::all();
    }

    // Create new product
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|min:3',
            'price' => 'required|numeric|gt:0',
            'stock' => 'required|integer|min:0'
        ]);
        return Product::create($request->all());
    }

    // Update existing product
    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'sometimes|min:3',
            'price' => 'sometimes|numeric|gt:0',
            'stock' => 'sometimes|integer|min:0'
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());
        
        return $product;
    }

    // Delete product
    public function destroy($id) {
        $product = Product::findOrFail($id);
        $product->delete();
        
        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
}