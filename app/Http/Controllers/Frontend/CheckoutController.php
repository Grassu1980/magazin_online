<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Coșul este gol!');
        }

        $cartItems = [];
        $subtotal = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->stock >= $item['quantity']) {
                $price = $product->final_price;
                $cartItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $price * $item['quantity']
                ];
                $subtotal += $price * $item['quantity'];
            }
        }

        $shippingCost = $subtotal > 200 ? 0 : 15; // Transport gratuit pentru comenzi peste 200 lei
        $total = $subtotal + $shippingCost;

        return view('frontend.checkout.index', compact('cartItems', 'subtotal', 'shippingCost', 'total'));
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
            'notes' => 'nullable|string|max:1000'
        ]);

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Coșul este gol!');
        }

        // Verificare stoc și calcul total
        $subtotal = 0;
        $orderItems = [];

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product || $product->stock < $item['quantity']) {
                return redirect()->route('cart.index')->with('error', 'Unele produse nu mai sunt disponibile!');
            }
            $price = $product->final_price;
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

        try {
            $order = DB::transaction(function () use ($request, $subtotal, $shippingCost, $total, $orderItems) {
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
                    'payment_status' => 'pending',
                ]);

                foreach ($orderItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['product']->id,
                        'product_name' => $item['product']->name,
                        'product_sku' => $item['product']->sku,
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ]);

                    $item['product']->decrement('stock', $item['quantity']);
                    $item['product']->increment('sold_count', $item['quantity']);
                }

                return $order;
            });
        } catch (\Throwable $exception) {
            Log::error('Checkout failed', [
                'message' => $exception->getMessage(),
                'user_id' => Auth::id(),
                'payment_method' => $request->payment_method,
            ]);

            return redirect()
                ->route('checkout.index')
                ->withInput()
                ->with('error', 'A apărut o eroare la finalizarea comenzii. Te rog încearcă din nou.');
        }

        Session::forget('cart');

        // Dacă plata este cu cardul, redirecționează către pagina MobilPay
        if ($request->payment_method === 'card') {
            return redirect()->route('payment.mobilpay', ['order_id' => $order->id]);
        }

        return redirect()->route('checkout.success', $order->order_number)->with('success', 'Comanda a fost plasată cu succes!');
    }

    public function success($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('frontend.checkout.success', compact('order'));
    }
}