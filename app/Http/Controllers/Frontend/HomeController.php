<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Menu;
use App\Models\Gllery;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Wishlist;
use App\Models\Review;

class HomeController extends Controller
{
    // Display detailed information for a restaurant (client)
    public function RestaurantDetails($id){
        $client = Client::find($id); // Get restaurant by ID

        // Get menus that belong to this client and have at least one product
        $menus = Menu::where('client_id', $client->id)
            ->get()
            ->filter(function($menu){
                return $menu->products->isNotEmpty(); // Only menus with products
            });

        // Get gallery images for this client
        $gallerys = Gllery::where('client_id', $id)->get();

        // Get approved reviews for this client
        $reviews = Review::where('client_id', $client->id)
            ->where('status', 1)
            ->get();

        // Calculate average rating
        $totalReviews = $reviews->count();
        $ratingSum = $reviews->sum('rating');
        $averageRating = $totalReviews > 0 ? $ratingSum / $totalReviews : 0;
        $roundedAverageRating = round($averageRating, 1);

        // Count ratings by each star (1 to 5)
        $ratingCounts = [
            '5' => $reviews->where('rating', 5)->count(),
            '4' => $reviews->where('rating', 4)->count(),
            '3' => $reviews->where('rating', 3)->count(),
            '2' => $reviews->where('rating', 2)->count(),
            '1' => $reviews->where('rating', 1)->count(),
        ];

        // Calculate percentage for each star rating
        $ratingPercentages = array_map(function ($count) use ($totalReviews) {
            return $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
        }, $ratingCounts);

        return view('frontend.details_page', compact(
            'client',
            'menus',
            'gallerys',
            'reviews',
            'roundedAverageRating',
            'totalReviews',
            'ratingCounts',
            'ratingPercentages'
        ));
    }

    // Add a restaurant to user's wishlist
    public function AddWishList(Request $request, $id){
        if (Auth::check()) {
            // Check if already in wishlist
            $exists = Wishlist::where('user_id', Auth::id())
                ->where('client_id', $id)
                ->first();

            if (!$exists) {
                // Add to wishlist
                Wishlist::insert([
                    'user_id' => Auth::id(),
                    'client_id' => $id,
                    'created_at' => Carbon::now(),
                ]);
                return response()->json(['success' => 'Your Wishlist Added Successfully']);
            } else {
                return response()->json(['error' => 'This restaurant is already in your wishlist']);
            }
        } else {
            return response()->json(['error' => 'Please login to add to wishlist']);
        }
    }

    // Show all wishlist items for the logged-in user
    public function AllWishlist(){
        $wishlist = Wishlist::where('user_id', Auth::id())->get();
        return view('frontend.dashboard.all_wishlist', compact('wishlist'));
    }

    // Remove an item from the wishlist
    public function RemoveWishlist($id){
        Wishlist::find($id)->delete();

        return redirect()->back()->with([
            'message' => 'Wishlist Deleted Successfully',
            'alert-type' => 'success'
        ]);
    }
}
