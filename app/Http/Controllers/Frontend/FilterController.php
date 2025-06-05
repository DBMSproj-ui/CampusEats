<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FilterController extends Controller
{
    // Display all products in the "List Restaurant" view (initial unfiltered view)
    public function ListRestaurant(){
        $products = Product::all(); // Fetch all products
        return view('frontend.list_restaurant', compact('products'));
    }
    // End Method

    // Handle filtering of products based on category and menu
    public function FilterProducts(Request $request)
    {
        // Optional: Log the incoming filter data for debugging
        // Log::info('request data', $request->all());

        // Get selected category and menu IDs from request
        $categoryId = $request->input('categorys'); // expected to be an array
        $menuId = $request->input('menus');         // expected to be an array

        // If city filter is needed, you can add this later
        // $cityId = $request->input('citys');

        // Begin a query builder for the Product model
        $products = Product::query();

        // Filter by category if selected
        if ($categoryId) {
            $products->whereIn('category_id', $categoryId);
        }

        // Filter by menu if selected
        if ($menuId) {
            $products->whereIn('menu_id', $menuId);
        }

        // You can add city filtering logic here later if needed
        // if ($cityId) {
        //     $products->whereIn('city_id', $cityId);
        // }

        // Execute the query and get the filtered result
        $filterProducts = $products->get();

        // Return the rendered Blade HTML (possibly via AJAX)
        return view('frontend.product_list', compact('filterProducts'))->render();
    }
    // End Method
}
