<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the form to edit the logged-in user's profile.
     */
    public function edit(Request $request): View
    {
        // Pass the authenticated user data to the profile edit view
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Handle the form submission to update user profile info.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Fill the user model with validated form input data
        $request->user()->fill($request->validated());

        // If the email is changed, mark it as unverified
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        // Save the updated user model
        $request->user()->save();

        // Redirect back to the profile edit page with a success message
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Handle user account deletion.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Validate that the correct password is provided before deletion
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'], // Laravel's built-in rule to check current password
        ]);

        // Get the currently authenticated user
        $user = $request->user();

        // Log the user out
        Auth::logout();

        // Delete the user from the database
        $user->delete();

        // Invalidate session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to homepage after deletion
        return Redirect::to('/');
    }
}
