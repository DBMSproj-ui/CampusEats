<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Mail\Websitemail;
use App\Models\Admin;
use App\Models\Order;
use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Coupon;

class AdminController extends Controller
{
    // Show the admin login page
    public function AdminLogin(){
        return view('admin.login');
    }

    // Show the admin dashboard with summary statistics
    public function AdminDashboard() {
        // Count total users
        $totalUsers = User::count();
        // Count total clients
        $totalClients = Client::count();
        // Count total orders
        $totalOrders = Order::count();
        // Calculate total revenue from order amounts
        $totalRevenue = Order::sum('amount'); // Sum of all orders
        // Count clients pending approval
        $pendingClients = Client::where('status', 0)->count();
        // Count total products
        $totalProducts = Product::count();
        // Count active coupons
        $activeCoupons = Coupon::where('status', 1)->count();
        // Count pending orders
        $pendingOrders = Order::where('status', 'pending')->count();
        // Count delivered orders
        $deliveredOrders = Order::where('status', 'deliverd')->count();

        return view('admin.index', compact(
            'totalUsers',
            'totalClients',
            'totalOrders',
            'totalRevenue',
            'pendingClients',
            'totalProducts',
            'activeCoupons',
            'pendingOrders',
            'deliveredOrders'
        ));
    }

    // Handle admin login submission
    public function AdminLoginSubmit(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Extract credentials from request
        $credentials = $request->only('email', 'password');

        // Attempt to authenticate as admin
        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard')->with('success','Login Successfully');
        } else {
            return redirect()->route('admin.login')->with('error','Invalid Credentials');
        }
    }

    // Handle admin logout
    public function AdminLogout(){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('success','Logout Success');
    }

    // Show forget password form
    public function AdminForgetPassword(){
        return view('admin.forget_password');
    }

    // Handle password reset email submission
    public function AdminPasswordSubmit(Request $request){
        $request->validate([
            'email' => 'required|email'
        ]);

        // Find admin by email
        $admin_data = Admin::where('email',$request->email)->first();

        // If not found, return error
        if (!$admin_data) {
           return redirect()->back()->with('error','Email Not Found');
        }

        // Generate token and store
        $token = hash('sha256', time());
        $admin_data->token = $token;
        $admin_data->update();

        // Generate password reset link
        $reset_link = url('admin/reset-password/'.$token.'/'.$request->email);
        $subject = "Reset Password";
        $message = "Please click the link below to reset your password<br>";
        $message .= "<a href='".$reset_link."'>Click Here</a>";

        // Send password reset email
        \Mail::to($request->email)->send(new Websitemail($subject, $message));

        return redirect()->back()->with('success','Reset Password Link Sent to Your Email');
    }

    // Show reset password form if token and email match
    public function AdminResetPassword($token, $email){
        // Verify token and email
        $admin_data = Admin::where('email',$email)->where('token',$token)->first();

        // If invalid, redirect to login
        if (!$admin_data) {
            return redirect()->route('admin.login')->with('error','Invalid Token or Email');
        }

        return view('admin.reset_password', compact('token', 'email'));
    }

    // Handle password reset submission
    public function AdminResetPasswordSubmit(Request $request){
        $request->validate([
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        // Fetch admin by email and token
        $admin_data = Admin::where('email',$request->email)->where('token',$request->token)->first();

        // Update password and clear token
        $admin_data->password = Hash::make($request->password);
        $admin_data->token = "";
        $admin_data->update();

        return redirect()->route('admin.login')->with('success','Password Reset Successfully');
    }

    // Show admin profile page
    public function AdminProfile(){
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.admin_profile', compact('profileData'));
    }

    // Handle profile update submission
    public function AdminProfileStore(Request $request){
        $id = Auth::guard('admin')->id();
        $data = Admin::find($id);

        // Update profile fields
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address; 

        $oldPhotoPath = $data->photo;

        // If a new photo is uploaded, save and delete the old one
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('upload/admin_images'), $filename);
            $data->photo = $filename;

            // Delete old photo if it exists and is different
            if ($oldPhotoPath && $oldPhotoPath !== $filename) {
                $this->deleteOldImage($oldPhotoPath);
            }
        }

        // Save updated profile
        $data->save();

        return redirect()->back()->with([
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success'
        ]);
    }

    // Delete old profile image from disk
    private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path('upload/admin_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    // Show form to change admin password
    public function AdminChangePassword(){
        $id = Auth::guard('admin')->id();
        $profileData = Admin::find($id);
        return view('admin.admin_change_Password', compact('profileData'));
    }

    // Handle new password update
    public function AdminPasswordUpdate(Request $request){
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        // Check old password validity
        if (!Hash::check($request->old_password, $admin->password)) {
            return back()->with([
                'message' => 'Old Password Does Not Match!',
                'alert-type' => 'error'
            ]);
        }

        // Update to new password
        Admin::whereId($admin->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with([
            'message' => 'Password Changed Successfully',
            'alert-type' => 'success'
        ]);
    }
}
