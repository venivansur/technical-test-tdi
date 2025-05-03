<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $validated = $this->validate($request, [
            'customer_name' => 'required|string|max:255',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);


        DB::beginTransaction();

        try {
            $order = new Order();
            $order->customer_name = $request->customer_name;
            $order->order_date = $request->order_date;
            $order->total_price = 0;
            $order->save();

            $totalPrice = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    // Batalkan transaksi dan kembalikan error
                    DB::rollBack();
                    return response()->json(['error' => 'Insufficient stock for product ID ' . $product->id], 400);
                }

                $subtotal = $product->price * $item['quantity'];

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $product->id;
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $product->price;
                $orderItem->subtotal = $subtotal;
                $orderItem->save();


                $product->stock -= $item['quantity'];
                $product->save();

                $totalPrice += $subtotal;
            }


            $order->total_price = $totalPrice;
            $order->save();

            DB::commit();

            return response()->json(['message' => 'Order created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create order', 'details' => $e->getMessage()], 500);
        }
    }
}
