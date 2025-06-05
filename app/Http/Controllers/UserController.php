<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; 
use App\Models\User;

class UserController extends Controller
{ 
    // Load the homepage
    public function Index(){
        return view('frontend.index');
    } 
    // End Method

    // Store user profile updates
    public function ProfileStore(Request $request){
        // Get the authenticated user's ID
        $id = Auth::user()->id;
        // Fetch user data from DB
        $data = User::find($id);

        // Update basic fields
        $data->name = $request->name;
        $data->email = $request->email;
        $data->phone = $request->phone;
        $data->address = $request->address; 

        $oldPhotoPath = $data->photo;

        // Handle profile image upload
        if ($request->hasFile('photo')) {
           $file = $request->file('photo');
           $filename = time().'.'.$file->getClientOriginalExtension();
           $file->move(public_path('upload/user_images'),$filename);
           $data->photo = $filename;

           // Delete old image if new one is uploaded
           if ($oldPhotoPath && $oldPhotoPath !== $filename) {
             $this->deleteOldImage($oldPhotoPath);
           }
        }

        // Save the updated profile
        $data->save();

        // Return with success message
        $notification = array(
            'message' => 'Profile Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }
    // End Method 

    // Helper to delete old profile photo
    private function deleteOldImage(string $oldPhotoPath): void {
        $fullPath = public_path('upload/user_images/'.$oldPhotoPath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
    // End Private Method 

    // Log out the authenticated user
    public function UserLogout(){
        Auth::guard('web')->logout(); // Logout the user
        return redirect()->route('login')->with('success','Logout Successfully');
    }
    // End Method 

    // Load change password page
    public function ChangePassword(){
        return view('frontend.dashboard.change_password');
    }
    // End Method 

    // Update user password securely
    public function UserPasswordUpdate(Request $request){
        $user = Auth::guard('web')->user();

        // Validate input
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);
 
        // Check if old password matches
        if (!Hash::check($request->old_password,$user->password)) {
            return back()->with([
                'message' => 'Old Password Does not Match!',
                'alert-type' => 'error'
            ]);
        }

        // Update with new password
        User::whereId($user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with([
            'message' => 'Password Change Successfully',
            'alert-type' => 'success'
        ]);
    }
    // End Method 
}
