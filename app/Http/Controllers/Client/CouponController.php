<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Coupon;
use Carbon\Carbon;

class CouponController extends Controller
{
    // Show all coupons belonging to the currently logged-in client
    public function AllCoupon(){
        $cid = Auth::guard('client')->id(); // Get client ID
        $coupon = Coupon::where('client_id', $cid)->latest()->get(); // Fetch client's coupons
        return view('client.backend.coupon.all_coupon', compact('coupon')); // Load view
    }

    // Show form to add a new coupon
    public function AddCoupon(){
        return view('client.backend.coupon.add_coupon');
    }

    // Store a new coupon in the database
    public function StoreCoupon(Request $request){
        Coupon::create([
            'coupon_name' => strtoupper($request->coupon_name), // Convert name to uppercase
            'coupon_desc' => $request->coupon_desc,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'client_id' => Auth::guard('client')->id(), // Link to logged-in client
            'created_at' => Carbon::now(),
        ]);

        // Notification on success
        $notification = array(
            'message' => 'Coupon Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.coupon')->with($notification);
    }

    // Show the edit form for a specific coupon
    public function EditCoupon($id){
        $coupon = Coupon::find($id); // Fetch coupon by ID
        return view('client.backend.coupon.edit_coupon', compact('coupon'));
    }

    // Update an existing coupon
    public function UpdateCoupon(Request $request){
        $cop_id = $request->id;

        Coupon::find($cop_id)->update([
            'coupon_name' => strtoupper($request->coupon_name),
            'coupon_desc' => $request->coupon_desc,
            'discount' => $request->discount,
            'validity' => $request->validity,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Coupon Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.coupon')->with($notification);
    }

    // Delete a coupon by its ID
    public function DeleteCoupon($id){
        Coupon::find($id)->delete(); // Remove from database

        $notification = array(
            'message' => 'Coupon Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
}
