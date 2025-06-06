<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Menu;
use App\Models\Product;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Carbon\Carbon;
use App\Models\Gllery;

class RestaurantController extends Controller
{
    ////////////////////// MENU MANAGEMENT //////////////////////

    // Show all menus created by the logged-in client
    public function AllMenu(){
        $id = Auth::guard('client')->id();
        $menu = Menu::where('client_id', $id)->orderBy('id', 'desc')->get();
        return view('client.backend.menu.all_menu', compact('menu'));
    }

    // Show form to add a new menu
    public function AddMenu(){
        return view('client.backend.menu.add_menu');
    }

    // Store a new menu in the database
    public function StoreMenu(Request $request){
        if ($request->file('image')) {
            // Process image upload
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(300,300)->save(public_path('upload/menu/'.$name_gen));
            $save_url = 'upload/menu/'.$name_gen;

            // Save menu record
            Menu::create([
                'menu_name' => $request->menu_name,
                'client_id' => Auth::guard('client')->id(),
                'image' => $save_url,
            ]);
        }

        return redirect()->route('all.menu')->with([
            'message' => 'Menu Inserted Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Show menu edit form
    public function EditMenu($id){
        $menu = Menu::find($id);
        return view('client.backend.menu.edit_menu', compact('menu'));
    }

    // Update an existing menu (with or without new image)
    public function UpdateMenu(Request $request){
        $menu_id = $request->id;

        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(300,300)->save(public_path('upload/menu/'.$name_gen));
            $save_url = 'upload/menu/'.$name_gen;

            Menu::find($menu_id)->update([
                'menu_name' => $request->menu_name,
                'image' => $save_url,
            ]);
        } else {
            Menu::find($menu_id)->update([
                'menu_name' => $request->menu_name,
            ]);
        }

        return redirect()->route('all.menu')->with([
            'message' => 'Menu Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Delete a menu and its image
    public function DeleteMenu($id){
        $item = Menu::find($id);
        unlink($item->image);
        $item->delete();

        return redirect()->back()->with([
            'message' => 'Menu Delete Successfully',
            'alert-type' => 'success'
        ]);
    }

    ////////////////////// PRODUCT MANAGEMENT //////////////////////

    // Show all products created by the client
    public function AllProduct(){
        $id = Auth::guard('client')->id();
        $product = Product::where('client_id', $id)->orderBy('id','desc')->get();
        return view('client.backend.product.all_product', compact('product'));
    }

    // Show form to add a product
    public function AddProduct(){
        $id = Auth::guard('client')->id();
        $category = Category::latest()->get();
        $menu = Menu::where('client_id',$id)->latest()->get();
        return view('client.backend.product.add_product', compact('category','menu'));
    }

    // Store a new product
    public function StoreProduct(Request $request){
        $pcode = IdGenerator::generate(['table' => 'products','field' => 'code', 'length' => 5, 'prefix' => 'PC']);

        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(300,300)->save(public_path('upload/product/'.$name_gen));
            $save_url = 'upload/product/'.$name_gen;

            Product::create([
                'name' => $request->name,
                'slug' => strtolower(str_replace(' ','-',$request->name)),
                'category_id' => $request->category_id,
                'menu_id' => $request->menu_id,
                'code' => $pcode,
                'qty' => $request->qty,
                'size' => $request->size,
                'price' => $request->price,
                'discount_price' => $request->discount_price,
                'client_id' => Auth::guard('client')->id(),
                'most_populer' => $request->most_populer,
                'best_seller' => $request->best_seller,
                'status' => 1,
                'created_at' => Carbon::now(),
                'image' => $save_url,
            ]);
        }

        return redirect()->route('all.product')->with([
            'message' => 'Product Inserted Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Show product edit form
    public function EditProduct($id){
        $cid = Auth::guard('client')->id();
        $category = Category::latest()->get();
        $menu = Menu::where('client_id',$cid)->latest()->get();
        $product = Product::find($id);
        return view('client.backend.product.edit_product', compact('category','menu','product'));
    }

    // Update product
    public function UpdateProduct(Request $request){
        $pro_id = $request->id;

        $updateData = [
            'name' => $request->name,
            'slug' => strtolower(str_replace(' ','-',$request->name)),
            'category_id' => $request->category_id,
            'menu_id' => $request->menu_id,
            'qty' => $request->qty,
            'size' => $request->size,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'most_populer' => $request->most_populer,
            'best_seller' => $request->best_seller,
            'created_at' => Carbon::now(),
        ];

        if ($request->file('image')) {
            $image = $request->file('image');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(300,300)->save(public_path('upload/product/'.$name_gen));
            $save_url = 'upload/product/'.$name_gen;
            $updateData['image'] = $save_url;
        }

        Product::find($pro_id)->update($updateData);

        return redirect()->route('all.product')->with([
            'message' => 'Product Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Delete a product and its image
    public function DeleteProduct($id){
        $item = Product::find($id);
        unlink($item->image);
        $item->delete();

        return redirect()->back()->with([
            'message' => 'Product Delete Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Toggle product status (e.g., active/inactive)
    public function ChangeStatus(Request $request){
        $product = Product::find($request->product_id);
        $product->status = $request->status;
        $product->save();

        return response()->json(['success' => 'Status Change Successfully']);
    }

    ////////////////////// GALLERY MANAGEMENT //////////////////////

    // Show all gallery images for the client
    public function AllGallery(){
        $cid = Auth::guard('client')->id();
        $gallery = Gllery::where('client_id', $cid)->latest()->get();
        return view('client.backend.gallery.all_gallery', compact('gallery'));
    }

    // Show form to upload to gallery
    public function AddGallery(){
        return view('client.backend.gallery.add_gallery');
    }

    // Store multiple gallery images
    public function StoreGallery(Request $request){
        $images = $request->file('gallery_img');

        foreach ($images as $gimg) {
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$gimg->getClientOriginalExtension();
            $img = $manager->read($gimg);
            $img->resize(800,800)->save(public_path('upload/gallery/'.$name_gen));
            $save_url = 'upload/gallery/'.$name_gen;

            Gllery::insert([
                'client_id' => Auth::guard('client')->id(),
                'gallery_img' => $save_url,
                'created_at' => now(),
            ]);
        }

        return redirect()->route('all.gallery')->with([
            'message' => 'Gallery Inserted Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Show gallery image edit form
    public function EditGallery($id){
        $gallery = Gllery::find($id);
        return view('client.backend.gallery.edit_gallery', compact('gallery'));
    }

    // Update gallery image
    public function UpdateGallery(Request $request){
        $gallery_id = $request->id;

        if ($request->hasFile('gallery_img')) {
            $image = $request->file('gallery_img');
            $manager = new ImageManager(new Driver());
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $img = $manager->read($image);
            $img->resize(800,800)->save(public_path('upload/gallery/'.$name_gen));
            $save_url = 'upload/gallery/'.$name_gen;

            $gallery = Gllery::find($gallery_id);
            if ($gallery->gallery_img) {
                unlink($gallery->gallery_img); // Delete old image
            }

            $gallery->update([
                'gallery_img' => $save_url,
            ]);

            return redirect()->route('all.gallery')->with([
                'message' => 'Gallery Updated Successfully',
                'alert-type' => 'success'
            ]);
        }

        return redirect()->back()->with([
            'message' => 'No Image Selected for Update',
            'alert-type' => 'warning'
        ]);
    }

    // Delete a gallery image
    public function DeleteGallery($id){
        $item = Gllery::find($id);
        unlink($item->gallery_img);
        $item->delete();

        return redirect()->back()->with([
            'message' => 'Gallery Delete Successfully',
            'alert-type' => 'success'
        ]);
    }
}
