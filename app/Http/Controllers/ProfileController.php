<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request)
{
    $request->validate([
        'profile_photo' => ['required', 'image', 'max:2048'] // 2MB max
    ]);

    $user = User::find(Auth::id());

    if ($request->hasFile('profile_photo')) {

        // Delete old photo if exists
        if ($user->profile_photo) {
            $oldPath = public_path('storage/' . $user->profile_photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            Storage::disk('public')->delete($user->profile_photo);
        }

        $file = $request->file('profile_photo');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store in storage/app/public/profile-photos
        $file->storeAs('profile-photos', $filename, 'public');

        // Ensure directory exists in public/storage/profile-photos
        $destinationPath = public_path('storage/profile-photos');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        // Copy to public/storage/profile-photos
        $file->move($destinationPath, $filename);

        // Save path in DB
        $user->profile_photo = 'profile-photos/' . $filename;
        $user->save();
    }

    return back()->with('status', 'Profile photo updated successfully');
}

}