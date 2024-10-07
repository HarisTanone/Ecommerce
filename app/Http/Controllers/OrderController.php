<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session; // Change PaymentIntent to Checkout\Session
use App\Models\Cart;
use Illuminate\Support\Facades\Log;
use App\Models\CartItem;

class OrderController extends Controller
{
    public function checkout(Request $request) {
        try {
            $request->validate([
                'cart_id' => 'required|exists:carts,id',
                'selectedItems' => 'required|array', // Validate that selectedItems is an array
                'selectedItems.*' => 'integer|exists:cart_items,id', // Validate that each item ID exists in cart_items
            ]);

            $cart = Cart::with('items.product')->find($request->cart_id);
            if (!$cart || $cart->items->isEmpty()) {
                return response()->json(['error' => 'Cart is empty or does not exist.'], 400);
            }

            // Calculate total amount based on products in the cart
            $totalAmount = 0;
            $lineItems = []; // Initialize line items

            foreach ($cart->items as $item) {
                if (in_array($item->id, $request->selectedItems)) { // Check if item is in selectedItems
                    $lineItems[] = [
                        'price_data' => [
                            'currency' => 'idr',
                            'product_data' => [
                                'name' => $item->product->title,
                                'images' => [$item->product->image],
                            ],
                            'unit_amount' => $item->product->discountedPrice * 100,
                        ],
                        'quantity' => $item->quantity,
                    ];

                    // Add to totalAmount
                    $totalAmount += $item->product->discountedPrice * $item->quantity;
                }
            }

            // Ensure there are selected items
            if (empty($lineItems)) {
                return response()->json(['error' => 'No items selected for checkout.'], 400);
            }

            // Create order
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_amount' => $totalAmount,
            ]);

            // Stripe payment
            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Create checkout session
            $selectedItems = json_encode($request->selectedItems); // Get product IDs from cart
            // dd($selectedItems);
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => url('/success?session_id={CHECKOUT_SESSION_ID}&selectedItems=' . urlencode($selectedItems)),
                // 'success_url' => url('/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => url('/cancel'),
            ]);

            // Redirect to Stripe checkout URL
            return response()->json(['url' => $session->url]);
        } catch (\Stripe\Exception\CardException $e) {
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 400);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return response()->json(['error' => 'Invalid request: ' . $e->getMessage()], 400);
        } catch (\Throwable $th) {
            dd('Error during checkout', [
                'message' => $th->getMessage(),
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An error occurred during the checkout process.'], 500);
        }
    }

    public function success(Request $request) {
        // Get session ID from query parameter
        $sessionId = $request->query('session_id');

        // Verify checkout session using Stripe API
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $session = Session::retrieve($sessionId);

            // Ensure the session has been paid
            if ($session->payment_status === 'paid') {
                // Get order data based on user_id or session_id
                $order = Order::where('user_id', auth()->id())->latest()->first();

                // If the order is found, remove items from cart and update status
                if ($order) {
                    // Get cart based on user_id
                    $cart = Cart::with('items')->where('user_id', auth()->id())->first();
                    $selectedItems = $request->query('selectedItems');

                    // Ensure selectedItems is not null and is an array
                    if ($selectedItems) {
                        $selectedItemsArray = json_decode($selectedItems, true); // Decode JSON to array

                        if (is_array($selectedItemsArray)) {
                            // Delete each item in the order from the cart
                            foreach ($selectedItemsArray as $itemId) {
                                $itemToDelete = CartItem::find($itemId); // Find item by ID

                                if ($itemToDelete) {
                                    $itemToDelete->delete(); // Remove item from cart
                                }
                            }

                            // Update order status to 'completed'
                            $order->status = 'completed';
                            $order->save(); // Save changes

                            return redirect()->route('cart-view')->with('success', 'Checkout successful, items have been removed from the cart.');
                        } else {
                            return response()->json(['error' => 'selectedItems must be an array.'], 400);
                        }
                    } else {
                        return response()->json(['error' => 'selectedItems not found.'], 400);
                    }
                }
            }

            return response()->json(['error' => 'Invalid payment session.'], 400);
        } catch (\Throwable $th) {
            // Log error for debugging
            \Log::error('Stripe error: ' . $th->getMessage());
            return response()->json(['error' => 'An error occurred while processing the payment.'], 500);
        }
    }

    public function cancel() {
        // Redirect back to the shopping cart page
        return redirect()->route('cart-view')->with('message', 'Your checkout has been canceled.');
    }

}
