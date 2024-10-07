<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(){
        return view('cartPageView');
    }
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $cartItem = CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $request->product_id],
            ['quantity' => DB::raw("quantity + {$request->quantity}")],
        );

        return response()->json(['cart' => $cart->load('items')], 200);
    }

    public function viewCart()
    {
        $cart = Cart::with('items.product')->where('user_id', auth()->id())->first();

        return response()->json($cart);
    }

    public function removeFromCart(Request $request, $itemId)
    {
        CartItem::destroy($itemId);

        return response()->json(['message' => 'Item removed from cart.']);
    }
}
