<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlists = Wishlist::with('product.category')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('frontend.user.wishlist', compact('wishlists'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Produsul este deja în wishlist!'
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        $count = Wishlist::where('user_id', Auth::id())->count();

        return response()->json([
            'success' => true,
            'message' => 'Produs adăugat în wishlist!',
            'wishlist_count' => $count
        ]);
    }

    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer'
        ]);

        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produs șters din wishlist!'
        ]);
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);

        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Produs șters din wishlist!'
            ]);
        }

        Wishlist::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        return response()->json([
            'success' => true,
            'action' => 'added',
            'message' => 'Produs adăugat în wishlist!'
        ]);
    }

    public function isInWishlist($productId)
    {
        $isInWishlist = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->exists();

        return response()->json(['in_wishlist' => $isInWishlist]);
    }
}