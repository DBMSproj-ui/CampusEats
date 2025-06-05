<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Product; 
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Review;
use Carbon\Carbon;

class ReviewController extends Controller
{
    // Store a new user review for a restaurant
    public function StoreReview(Request $request){
        $client = $request->client_id;

        // Validate that the comment field is present
        $request->validate([
            'comment' => 'required'
        ]);

        // Insert the review into the database with default status = 0 (pending)
        Review::insert([
            'client_id' => $client,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'rating' => $request->rating,
            'created_at' => Carbon::now(), 
        ]);

        // Prepare a success message to show after redirect
        $notification = array(
            'message' => 'Review Will Be Approved By Admin',
            'alert-type' => 'success'
        );

        // Redirect user back to the review tab on the restaurant details page
        $previousUrl = $request->headers->get('referer');
        $redirectUrl = $previousUrl ? $previousUrl . '#pills-reviews' : route('res.details', ['id' => $client]) . '#pills-reviews';
        return redirect()->to($redirectUrl)->with($notification);
    }
    // End Method 

    // Admin panel view: shows all pending reviews (status = 0)
    public function AdminPendingReview(){
        $pedingReview = Review::where('status', 0)->orderBy('id', 'desc')->get();
        return view('admin.backend.review.view_pending_review', compact('pedingReview'));
    }
    // End Method 

    // Admin panel view: shows all approved reviews (status = 1)
    public function AdminApproveReview(){
        $approveReview = Review::where('status', 1)->orderBy('id', 'desc')->get();
        return view('admin.backend.review.view_approve_review', compact('approveReview'));
    }
    // End Method 

    // Ajax request: Change review status (approve or reject)
    public function ReviewChangeStatus(Request $request){
        $review = Review::find($request->review_id);
        $review->status = $request->status;
        $review->save();

        return response()->json(['success' => 'Status Changed Successfully']);
    }
    // End Method 

    // Client dashboard: view all reviews written for their restaurant
    public function ClientAllReviews(){
        $id = Auth::guard('client')->id();

        // Fetch all approved reviews for this client (restaurant)
        $allreviews = Review::where('status', 1)
            ->where('client_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        return view('client.backend.review.view_all_review', compact('allreviews'));
    }
    // End Method 
}
