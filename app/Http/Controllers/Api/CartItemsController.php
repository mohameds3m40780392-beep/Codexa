<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart_items;

class CartItemsController extends Controller
{
    // AddToCart
    public function AddToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart_items::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cartItem = Cart_items::create([
                'user_id' => auth()->id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'message' => 'Product added to cart successfully',
            'cart_item' => $cartItem
        ], 200);
    }

    // RemoveFromCart
    public function RemoveFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $cartItem = Cart_items::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        }

        $cartItem->delete();

        return response()->json(['message' => 'Product removed from cart successfully'], 200);
    }

    // UpdateCart
    public function UpdateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart_items::where('user_id', auth()->id())
            ->where('product_id', $request->product_id)
            ->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Product not found in cart'], 404);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json(['message' => 'Cart updated successfully', 'cart_item' => $cartItem], 200);
    }

    // GetCartItems
    public function getCart()
    {
    $cartItems = Cart_items::with('product')
        ->where('user_id', auth()->id())
        ->get();

    $totalPrice = $cartItems->sum(function ($item) {
        return $item->quantity * ($item->product->price ?? 0); 
    });

    return response()->json([
        'cart_items' => $cartItems,
        'total_price' => $totalPrice
    ], 200);
    }
}