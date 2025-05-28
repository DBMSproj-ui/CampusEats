<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 
use App\Models\Client;
use App\Models\City;

class ClientController extends Controller
{
    public function ClientLogin(){
        return view('client.client_login');
   }
   // End Method 

   public function ClientRegister(){
    return view('client.client_register');
}
// End Method 

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
            'password' => Hash::make($request->password),
            'role' => 'client',
            'status' => '0', 
        ]);

        $notification = array(
            'message' => 'Client Register Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('client.login')->with($notification);

    }
    // End Method 

    public function ClientLoginSubmit(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $check = $request->all();
        $data = [
            'email' => $check['email'],
            'password' => $check['password'],
        ];
        if (Auth::guard('client')->attempt($data)) {
            return redirect()->route('client.dashboard')->with('success','Login Successfully');
        }else{
            return redirect()->route('client.login')->with('error','Invalid Creadentials');
        }

    }
// End Method 

    public function ClientDashboard()
{
    $clientId = Auth::guard('client')->id();

    // Total Orders (distinct order_id entries for this client)
    $totalOrders = DB::table('order_items')
        ->where('client_id', $clientId)
        ->distinct('order_id')
        ->count('order_id');

    // Total Revenue
    $totalRevenue = DB::table('order_items')
        ->where('client_id', $clientId)
        ->sum(DB::raw('qty * price'));

    // Total Transactions (all order items)
    $totalTransactions = DB::table('order_items')
        ->where('client_id', $clientId)
        ->count();

    // Total Products listed by client
    $totalProducts = DB::table('products')
        ->where('client_id', $clientId)
        ->count();

    // Pending Orders
    $pendingOrders = DB::table('orders')
    ->where('status', 'pending')
    ->whereIn('id', function ($query) use ($clientId) {
        $query->select('order_id')
              ->from('order_items')
              ->where('client_id', $clientId);
    })
    ->count();

$totalMenus = DB::table('menus')
    ->where('client_id', $clientId)
    ->count();

    $processingOrders = DB::table('orders')
    ->where('status', 'processing')
    ->whereIn('id', function ($query) use ($clientId) {
        $query->select('order_id')->from('order_items')->where('client_id', $clientId);
    })->count();

    $deliveredOrders = DB::table('orders')
    ->where('status', 'deliverd')
    ->whereIn('id', function ($query) use ($clientId) {
        $query->select('order_id')->from('order_items')->where('client_id', $clientId);
    })->count();

    // Active Coupons
    $activeCoupons = DB::table('coupons')
        ->where('client_id', $clientId)
        ->whereDate('validity', '>=', now())
        ->count();

    // Monthly revenue chart (last 6 months)
    $monthlyRevenue = [];
    $months = [];

    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $months[] = $month->format('M');

        $monthlyRevenue[] = DB::table('order_items')
            ->where('client_id', $clientId)
            ->whereMonth('created_at', $month->month)
            ->whereYear('created_at', $month->year)
            ->sum(DB::raw('qty * price'));
    }

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
    'months',
    'topProductNames',
    'topProductSales'
));
}
    // End Method 

    public function ClientLogout(){
        Auth::guard('client')->logout();
        return redirect()->route('client.login')->with('success','Logout Success');
    }
    // End Method 

    public function ClientProfile(){
        $city = City::latest()->get();
        $id = Auth::guard('client')->id();
        $profileData = Client::find($id);
        return view('client.client_profile',compact('profileData','city'));
     }
      // End Method 
 
      public function ClientProfileStore(Request $request){
        $id = Auth::guard('client')->id();
        $data = Client::find($id);

        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address;
        $data->city_id = $request->city_id;
        $data->shop_info = $request->shop_info; 

        $oldPhotoPath = $data->photo;

        if ($request->hasFile('photo')) {
           $file = $request->file('photo');
           $filename = time().'.'.$file->getClientOriginalExtension();
           $file->move(public_path('upload/client_images'),$filename);
           $data->photo = $filename;

           if ($oldPhotoPath && $oldPhotoPath !== $filename) {
             $this->deleteOldImage($oldPhotoPath);
           }

        }

        if ($request->hasFile('cover_photo')) {
            $file1 = $request->file('cover_photo');
            $filename1 = time().'.'.$file1->getClientOriginalExtension();
            $file1->move(public_path('upload/client_images'),$filename1);
            $data->cover_photo = $filename1; 
         }

        $data->save();

        $notification = array(
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification);
    }
     // End Method 
     private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path('upload/client_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
     }
     // End Private Method 

     public function ClientChangePassword(){
        $id = Auth::guard('client')->id();
        $profileData = Client::find($id);
        return view('client.client_change_Password',compact('profileData'));
     }
      // End Method 

      public function ClientPasswordUpdate(Request $request){
        $client = Auth::guard('client')->user();
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);
 
        if (!Hash::check($request->old_password,$client->password)) {
            $notification = array(
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            );
            return back()->with($notification);
        }
        /// Update the new password 
        Client::whereId($client->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

                $notification = array(
                'message' => 'Password Change Successfully',
                'alert-type' => 'success'
            );
            return back()->with($notification);
     }
      // End Method 


}