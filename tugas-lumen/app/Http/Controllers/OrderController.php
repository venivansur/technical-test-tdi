<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use DB;
use App\Models\OrderItem;
class OrderController extends Controller
{

    public function index()
{
    return Order::with('items.product')->get();
}

    public function store(Request $request)
    {

        $this->validate($request, [
            'customer_name' => 'required',
            'order_date' => 'required|date',
            'items' => 'required|array'
        ]);


        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return response()->json([
                    'error' => "Insufficient stock for product ID {$item['product_id']}"
                ], 400);
            }
        }


        DB::transaction(function () use ($request) {
            $order = Order::create($request->only('customer_name', 'order_date'));

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price
                ]);


                $product->decrement('stock', $item['quantity']);
            }
        });

        return response()->json(['message' => 'Order created'], 201);
    }
}