<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 
use App\Models\Client;

class ClientController extends Controller
{
    // Show the client login form
    public function ClientLogin(){
        return view('client.client_login');
    }

    // Show the client registration form
    public function ClientRegister(){
        return view('client.client_register');
    }

    // Handle registration form submission
    public function ClientRegisterSubmit(Request $request){
        $request->validate([
            'name' => ['required','string','max:200'],
            'email' => ['required','string','unique:clients']
        ]);

        Client::insert([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password), // Secure password
            'role' => 'client',
            'status' => '0', // Initially inactive
            'created_at' => now(),
        ]);

        return redirect()->route('client.login')->with([
            'message' => 'Client Register Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Handle login form submission
    public function ClientLoginSubmit(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('client')->attempt($credentials)) {
            return redirect()->route('client.dashboard')->with('success','Login Successfully');
        } else {
            return redirect()->route('client.login')->with('error','Invalid Credentials');
        }
    }

    // Display client dashboard with statistics
    public function ClientDashboard(){
        $clientId = Auth::guard('client')->id();

        $totalOrders = DB::table('order_items')->where('client_id', $clientId)->distinct('order_id')->count('order_id');
        $totalRevenue = DB::table('order_items')->where('client_id', $clientId)->sum(DB::raw('qty * price'));
        $totalTransactions = DB::table('order_items')->where('client_id', $clientId)->count();
        $totalProducts = DB::table('products')->where('client_id', $clientId)->count();
        $pendingOrders = DB::table('orders')
            ->where('status', 'pending')
            ->whereIn('id', function ($query) use ($clientId) {
                $query->select('order_id')->from('order_items')->where('client_id', $clientId);
            })->count();
        $processingOrders = DB::table('orders')
            ->where('status', 'confirm')
            ->whereIn('id', function ($query) use ($clientId) {
                $query->select('order_id')->from('order_items')->where('client_id', $clientId);
            })->count();
        $deliveredOrders = DB::table('orders')
            ->where('status', 'deliverd')
            ->whereIn('id', function ($query) use ($clientId) {
                $query->select('order_id')->from('order_items')->where('client_id', $clientId);
            })->count();
        $outfordelivery = DB::table('orders')
            ->where('status', 'processing')
            ->whereIn('id', function ($query) use ($clientId) {
                $query->select('order_id')->from('order_items')->where('client_id', $clientId);
            })->count();
        $totalMenus = DB::table('menus')->where('client_id', $clientId)->count();
        $activeCoupons = DB::table('coupons')->where('client_id', $clientId)->whereDate('validity', '>=', now())->count();

        // Monthly revenue calculation (last 6 months)
        $monthlyRevenue = [];
        $months = [];

        for ($i = 4; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M');
            $monthlyRevenue[] = DB::table('order_items')
                ->where('client_id', $clientId)
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum(DB::raw('qty * price'));
        }

        // Top 5 best-selling products
        $topProducts = DB::table('order_items')
            ->select('products.name', DB::raw('SUM(order_items.qty) as total_sold'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.client_id', $clientId)
            ->groupBy('order_items.product_id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $topProductNames = $topProducts->pluck('name')->toArray();
        $topProductSales = $topProducts->pluck('total_sold')->toArray();

        return view('client.index', compact(
            'totalOrders',
            'totalRevenue',
            'totalTransactions',
            'totalProducts',
            'pendingOrders',
            'totalMenus',
            'activeCoupons',
            'monthlyRevenue',
            'processingOrders',
            'deliveredOrders',
            'outfordelivery',
            'months',
            'topProductNames',
            'topProductSales'
        ));
    }

    // Logout client
    public function ClientLogout(){
        Auth::guard('client')->logout();
        return redirect()->route('client.login')->with('success','Logout Success');
    }

    // Show client profile edit page
    public function ClientProfile(){
        $id = Auth::guard('client')->id();
        $profileData = Client::find($id);
        return view('client.client_profile', compact('profileData'));
    }

    // Handle client profile update
    public function ClientProfileStore(Request $request){
        $id = Auth::guard('client')->id();
        $data = Client::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->shop_info = $request->shop_info; 

        $oldPhotoPath = $data->photo;

        // Handle profile photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/client_images'), $filename);
            $data->photo = $filename;

            if ($oldPhotoPath && $oldPhotoPath !== $filename) {
                $this->deleteOldImage($oldPhotoPath);
            }
        }

        // Handle cover photo upload
        if ($request->hasFile('cover_photo')) {
            $file1 = $request->file('cover_photo');
            $filename1 = time().'.'.$file1->getClientOriginalExtension();
            $file1->move(public_path('upload/client_images'), $filename1);
            $data->cover_photo = $filename1; 
        }

        $data->save();

        return redirect()->back()->with([
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Delete previous profile image from disk
    private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path('upload/client_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // Show password change form
    public function ClientChangePassword(){
        $id = Auth::guard('client')->id();
        $profileData = Client::find($id);
        return view('client.client_change_Password', compact('profileData'));
    }

    // Handle password update
    public function ClientPasswordUpdate(Request $request){
        $client = Auth::guard('client')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        // Check if old password matches
        if (!Hash::check($request->old_password, $client->password)) {
            return back()->with([
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            ]);
        }

        // Save new password
        Client::whereId($client->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with([
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        ]);
    }
}
