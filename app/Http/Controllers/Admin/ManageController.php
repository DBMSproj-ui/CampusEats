<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Menu;
use App\Models\Client;
use App\Models\Product;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use App\Models\Gllery;
use App\Models\Banner;

class ManageController extends Controller
{
    // View all products for admin
    public function AdminAllProduct()
    {
        $product = Product::orderBy('id', 'desc')->get(); // Get all products, latest first
        return view('admin.backend.product.all_product', compact('product'));
    }

    // Show the form to add a new product
    public function AdminAddProduct()
    {
        $category = Category::latest()->get();
        $menu = Menu::latest()->get();
        $client = Client::latest()->get();
        return view('admin.backend.product.add_product', compact('category', 'menu', 'client'));
    }

    // Store a new product in the database
    public function AdminStoreProduct(Request $request)
    {
        // Generate a unique product code (e.g., PC001)
        $pcode = IdGenerator::generate(['table' => 'products', 'field' => 'code', 'length' => 5, 'prefix' => 'PC']);

        // Handle image upload
        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(300, 300)->save(public_path('upload/product/' . $name_gen));
            $save_url = 'upload/product/' . $name_gen;

            // Create new product entry
            Product::create([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ', '-', $request->name)),
                'category_id' => $request->category_id,
                'menu_id' => $request->menu_id,
                'code' => $pcode,
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'client_id' => $request->client_id,
                'most_populer' => $request->most_populer,
                'best_seller' => $request->best_seller,
                'status' => 1,
                'created_at' => Carbon::now(),
                'image' => $save_url,
            ]);
        }

        // Redirect with success message
        return redirect()->route('admin.all.product')->with([
            'message' => 'Product Inserted Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Show edit form for a specific product
    public function AdminEditProduct($id)
    {
        $category = Category::latest()->get();
        $menu = Menu::latest()->get();
        $client = Client::latest()->get();
        $product = Product::find($id); // Fetch product by ID
        return view('admin.backend.product.edit_product', compact('category', 'menu', 'product', 'client'));
    }

    // Update a specific product
    public function AdminUpdateProduct(Request $request)
    {
        $pro_id = $request->id;

        // Prepare update data
        $updateData = [
            'name' => $request->name,
            'slug' => strtolower(str_replace(' ', '-', $request->name)),
            'category_id' => $request->category_id,
            'menu_id' => $request->menu_id,
            'client_id' => $request->client_id,
            'qty' => $request->qty,
            'size' => $request->size,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'most_populer' => $request->most_populer,
            'best_seller' => $request->best_seller,
            'created_at' => Carbon::now(),
        ];

        // If image uploaded, handle and update it
        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(300, 300)->save(public_path('upload/product/' . $name_gen));
            $save_url = 'upload/product/' . $name_gen;
            $updateData['image'] = $save_url;
        }

        // Update product
        Product::find($pro_id)->update($updateData);

        return redirect()->route('admin.all.product')->with([
            'message' => 'Product Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Delete a product by ID
    public function AdminDeleteProduct($id)
    {
        $item = Product::find($id);
        unlink($item->image); // Remove image file
        $item->delete(); // Delete DB record

        return redirect()->back()->with([
            'message' => 'Product Delete Successfully',
            'alert-type' => 'success'
        ]);
    }

    // View all pending restaurant (client) registrations
    public function PendingRestaurant()
    {
        $client = Client::where('status', 0)->get(); // Get unapproved clients
        return view('admin.backend.restaurant.pending_restaurant', compact('client'));
    }

    // Change status of a restaurant (approve/reject)
    public function ClientChangeStatus(Request $request)
    {
        $client = Client::find($request->client_id);
        $client->status = $request->status;
        $client->save();

        return response()->json(['success' => 'Status Change Successfully']);
    }

    // View all approved restaurants
    public function ApproveRestaurant()
    {
        $client = Client::where('status', 1)->get(); // Get approved clients
        return view('admin.backend.restaurant.approve_restaurant', compact('client'));
    }

    // View all banners
    public function AllBanner()
    {
        $banner = Banner::latest()->get();
        return view('admin.backend.banner.all_banner', compact('banner'));
    }

    // Store a new banner
    public function BannerStore(Request $request)
    {
        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(400, 400)->save(public_path('upload/banner/' . $name_gen));
            $save_url = 'upload/banner/' . $name_gen;

            // Create new banner
            Banner::create([
                'url' => $request->url,
                'image' => $save_url,
                'created_at' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with([
            'message' => 'Banner Inserted Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Return a specific banner as JSON (for editing via AJAX)
    public function EditBanner($id)
    {
        $banner = Banner::find($id);
        if ($banner) {
            $banner->image = asset($banner->image); // Convert image path to full URL
        }
        return response()->json($banner);
    }

    // Update a banner
    public function BannerUpdate(Request $request)
    {
        $banner_id = $request->banner_id;

        if ($request->file('image')) {
            // Process and save new image
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(400, 400)->save(public_path('upload/banner/' . $name_gen));
            $save_url = 'upload/banner/' . $name_gen;

            // Update with new image
            Banner::find($banner_id)->update([
                'url' => $request->url,
                'image' => $save_url,
            ]);
        } else {
            // Update without changing image
            Banner::find($banner_id)->update([
                'url' => $request->url,
            ]);
        }

        return redirect()->route('all.banner')->with([
            'message' => 'Banner Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Delete a banner
    public function DeleteBanner($id)
    {
        $item = Banner::find($id);
        unlink($item->image); // Delete banner image from disk
        $item->delete(); // Delete DB record

        return redirect()->back()->with([
            'message' => 'Banner Delete Successfully',
            'alert-type' => 'success'
        ]);
    }
}
