<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Product;
use Illuminate\Support\Facades\Session; 
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class OrderController extends Controller
{
    // Handle placing an order using Cash On Delivery
    public function CashOrder(Request $request){

        // Validate required user inputs
        $validateData = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'address' => 'required',
        ]);

        // Retrieve current cart from session
        $cart = session()->get('cart', []);
        $totalAmount = 0;

        // Calculate total cart amount
        foreach($cart as $car){
            $totalAmount += ($car['price'] * $car['quantity']);
        }

        // Check for applied coupon and get discounted total if available
        if (Session()->has('coupon')) {
            $tt = Session()->get('coupon')['discount_amount'];
        } else {
            $tt = $totalAmount;
        }

        // Insert a new order and get the inserted order ID
        $order_id = Order::insertGetId([
            'user_id'       => Auth::id(),
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'payment_type'  => 'Cash On Delivery',
            'payment_method'=> 'Cash On Delivery',

            'amount'        => $totalAmount,           // original amount
            'total_amount'  => $tt,                    // discounted amount if any
            'invoice_no'    => 'CampusEats' . mt_rand(10000000,99999999), // random invoice
            'order_date'    => Carbon::now()->format('d F Y'),
            'order_month'   => Carbon::now()->format('F'),
            'order_year'    => Carbon::now()->format('Y'),

            'status'        => 'Pending',              // initial order status
            'created_at'    => Carbon::now(), 
        ]);

        // Insert each item in the cart into order_items table
        foreach ($cart as $cartItem) {
            OrderItem::insert([
                'order_id'   => $order_id,
                'product_id' => $cartItem['id'],
                'client_id'  => $cartItem['client_id'],
                'qty'        => $cartItem['quantity'],
                'price'      => $cartItem['price'],
                'created_at' => Carbon::now(), 
            ]);
        }

        // Clear the coupon from session after placing the order
        if (Session::has('coupon')) {
            Session::forget('coupon');
        }

        // Clear the cart from session
        if (Session::has('cart')) {
            Session::forget('cart');
        }

        // Send success message to the thank you page
        $notification = array(
            'message' => 'Order Placed Successfully',
            'alert-type' => 'success'
        );

        // Show thank you view with notification
        return view('frontend.checkout.thanks')->with($notification);
    }
    // End Method
}
