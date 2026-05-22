<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            if (is_array($item) && isset($item['quantity'])) {
                $product = Product::find($productId);
                if ($product) {
                    $cartItems[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'price' => $product->current_price,
                        'subtotal' => $product->current_price * $item['quantity']
                    ];
                    $total += $product->current_price * $item['quantity'];
                }
            }
        }

        return view('frontend.cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stoc insuficient!'
            ], 400);
        }

        $cart = Session::get('cart', []);

        if (isset($cart[$product->id]) && is_array($cart[$product->id])) {
            $newQuantity = ($cart[$product->id]['quantity'] ?? 0) + $request->quantity;
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stoc insuficient pentru această cantitate!'
                ], 400);
            }
            $cart[$product->id]['quantity'] = $newQuantity;
        } else {
            $cart[$product->id] = [
                'quantity' => $request->quantity
            ];
        }

        Session::put('cart', $cart);

        $cartCount = collect($cart)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Produs adăugat în coș!',
            'cart_count' => $cartCount
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0'
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = Session::get('cart', []);

        if ($request->quantity <= 0) {
            unset($cart[$product->id]);
        } else {
            if ($request->quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stoc insuficient!'
                ], 400);
            }
            if (!isset($cart[$product->id]) || !is_array($cart[$product->id])) {
                $cart[$product->id] = [];
            }
            $cart[$product->id]['quantity'] = $request->quantity;
        }

        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Coș actualizat!'
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer'
        ]);

        $cart = Session::get('cart', []);
        unset($cart[$request->product_id]);
        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Produs șters din coș!'
        ]);
    }

    public function clear()
    {
        Session::forget('cart');
        return redirect()->route('cart.index')->with('success', 'Coșul a fost golit!');
    }

    public function getCount()
    {
        $cart = Session::get('cart', []);
        $count = collect($cart)->sum('quantity');
        return response()->json(['count' => $count]);
    }
}