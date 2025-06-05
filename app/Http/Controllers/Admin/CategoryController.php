<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Category;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;

class CategoryController extends Controller
{
    // Display all categories in descending order of creation
    public function AllCategory(){
        $category = Category::latest()->get(); // Get all categories, latest first
        return view('admin.backend.category.all_category', compact('category')); // Pass to view
    }

    // Show form to add a new category
    public function AddCategory(){
        return view('admin.backend.category.add_category'); // Load the add category page
    }

    // Store a new category with image upload and resizing
    public function StoreCategory(Request $request){
        // Check if an image file is uploaded
        if ($request->file('image')) {
            $image = $request->file('image'); // Get uploaded image
            $manager = new ImageManager(new Driver()); // Create image manager using GD driver
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension(); // Generate unique filename
            $img = $manager->read($image); // Read image into Intervention
            $img->resize(300, 300)->save(public_path('upload/category/' . $name_gen)); // Resize and save
            $save_url = 'upload/category/' . $name_gen; // Set the image path

            // Insert new category into database
            Category::create([
                'category_name' => $request->category_name,
                'image' => $save_url,
                'created_at' => Carbon::now(), // Set timestamp
            ]);
        }

        // Redirect with success message
        return redirect()->route('all.category')->with([
            'message' => 'Category Inserted Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Load the edit category form with existing data
    public function EditCategory($id){
        $category = Category::find($id); // Fetch category by ID
        return view('admin.backend.category.edit_category', compact('category')); // Pass to edit view
    }

    // Update an existing category with or without a new image
    public function UpdateCategory(Request $request){
        $cat_id = $request->id; // Get category ID from request

        // If a new image is uploaded, handle image replacement
        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(300, 300)->save(public_path('upload/category/' . $name_gen));
            $save_url = 'upload/category/' . $name_gen;

            // Update category name and new image
            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
                'image' => $save_url,
            ]);
        } else {
            // Update only the name if no new image is uploaded
            Category::find($cat_id)->update([
                'category_name' => $request->category_name,
            ]);
        }

        // Redirect with update success message
        return redirect()->route('all.category')->with([
            'message' => 'Category Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Delete a category and its associated image
    public function DeleteCategory($id){
        $item = Category::find($id); // Fetch category by ID
        $img = $item->image; // Get image path
        unlink($img); // Delete image file from disk
        $item->delete(); // Delete category record from DB

        // Redirect with deletion success message
        return redirect()->back()->with([
            'message' => 'Category Delete Successfully',
            'alert-type' => 'success'
        ]);
    }
}
