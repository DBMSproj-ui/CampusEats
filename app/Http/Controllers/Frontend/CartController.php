<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; 
use App\Models\Product;
use Illuminate\Support\Facades\Session; 
use App\Models\Coupon;

class CartController extends Controller
{
    // Add a product to the cart by ID
    public function AddToCart($id){
    // Remove any previously applied coupon
    if (Session::has('coupon')) {
        Session::forget('coupon');
    }

    $product = Product::find($id);
    $clientId = $product->client_id;

    $cart = session()->get('cart', []);

    // If cart is not empty, check if new product matches existing client_id
    if (!empty($cart)) {
        $existingClientId = reset($cart)['client_id'];

        if ($existingClientId != $clientId) {
            return response()->json([
                'error' => 'You can only add items from one restaurant at a time.'
            ]);
        }
    }

    // Add or update item in cart
    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $priceToShow = isset($product->discount_price) ? $product->discount_price : $product->price;

        $cart[$id] = [
            'id' => $id,
            'name' => $product->name,
            'image' => $product->image,
            'price' => $priceToShow,
            'client_id' => $clientId,
            'quantity' => 1
        ];
    }

    session()->put('cart', $cart);

    return response()->json([
        'message' => 'Add to Cart Successfully',
        'alert-type' => 'success'
    ]);
}


    // Update quantity of a cart item via AJAX
    public function updateCartQuanity(Request $request){
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            $cart[$request->id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return response()->json([
            'message' => 'Quantity Updated',
            'alert-type' => 'success'
        ]);
    }

    // Remove an item from the cart
    public function CartRemove(Request $request){
        $cart = session()->get('cart', []);

        if (isset($cart[$request->id])) {
            unset($cart[$request->id]);
            session()->put('cart', $cart);
        }

        return response()->json([
            'message' => 'Cart Remove Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Apply a coupon to the current cart
    public function ApplyCoupon(Request $request){
        $coupon = Coupon::where('coupon_name', $request->coupon_name)
            ->where('validity', '>=', Carbon::now()->format('Y-m-d'))
            ->first();

        $cart = session()->get('cart', []);
        $totalAmount = 0;
        $clientIds = [];

        // Calculate total and collect all client IDs in cart
        foreach ($cart as $car) {
            $totalAmount += ($car['price'] * $car['quantity']);
            $pd = Product::find($car['id']);
            $cdid = $pd->client_id;
            array_push($clientIds, $cdid);
        }

        if ($coupon) {
            // Check if all items are from a single restaurant
            if (count(array_unique($clientIds)) === 1) {
                $cvendorId = $coupon->client_id;

                // Check if coupon belongs to the same restaurant
                if ($cvendorId == $clientIds[0]) {
                    Session::put('coupon', [
                        'coupon_name' => $coupon->coupon_name,
                        'discount' => $coupon->discount,
                        'discount_amount' => $totalAmount - ($totalAmount * $coupon->discount / 100),
                    ]);

                    return response()->json([
                        'validity' => true,
                        'success' => 'Coupon Applied Successfully',
                        'couponData' => Session()->get('coupon'),
                    ]);
                } else {
                    return response()->json(['error' => 'This Coupon Not Valid for this Restrurant']);
                }

            } else {
                return response()->json(['error' => 'This Coupon for one of the selected Restrurant']);
            }
        } else {
            return response()->json(['error' => 'Invalid Coupon']);
        }
    }

    // Remove applied coupon from session
    public function CouponRemove(){
        Session::forget('coupon');
        return response()->json(['success' => 'Coupon Remove Successfully']);
    }

    // Display the checkout view if the user is logged in
    public function ShopCheckout(){
        if (Auth::check()) {
            $cart = session()->get('cart', []);
            $totalAmount = 0;

            foreach ($cart as $car) {
                $totalAmount += $car['price'];
            }

            if ($totalAmount > 0) {
                return view('frontend.checkout.view_checkout', compact('cart'));
            } else {
                return redirect()->to('/')->with([
                    'message' => 'Shopping at least one item',
                    'alert-type' => 'error'
                ]);
            }
        } else {
            return redirect()->route('login')->with([
                'message' => 'Please Login First',
                'alert-type' => 'success'
            ]);
        }
    }
}
