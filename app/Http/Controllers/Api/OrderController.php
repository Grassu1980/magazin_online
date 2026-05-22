<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return response()->json($orders);
    }

    public function show($id)
    {
        $order = Order::with('items.product')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Comandă negăsită'], 404);
        }

        return response()->json($order);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'shipping_city' => 'required|string|max:100',
            'payment_method' => 'required|in:cash,card',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        // Verificare stoc și calcul total
        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->stock < $item['quantity']) {
                return response()->json([
                    'message' => 'Produsul "' . ($product ? $product->name : 'necunoscut') . '" nu are stoc suficient!'
                ], 400);
            }
            $price = $product->current_price;
            $itemSubtotal = $price * $item['quantity'];
            $subtotal += $itemSubtotal;
            
            $orderItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $price,
                'subtotal' => $itemSubtotal
            ];
        }

        $shippingCost = $subtotal > 200 ? 0 : 15;
        $total = $subtotal + $shippingCost;

        // Creare comandă
        $order = Order::create([
            'user_id' => Auth::id(),
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'shipping_address' => $request->shipping_address,
            'shipping_city' => $request->shipping_city,
            'notes' => $request->notes,
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total' => $total,
            'status' => 'pending',
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending'
        ]);

        // Creare items și actualizare stoc
        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'product_name' => $item['product']->name,
                'product_sku' => $item['product']->sku,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['subtotal']
            ]);

            $item['product']->decrement('stock', $item['quantity']);
            $item['product']->increment('sold_count', $item['quantity']);
        }

        return response()->json([
            'message' => 'Comandă creată cu succes!',
            'order' => $order
        ], 201);
    }
}