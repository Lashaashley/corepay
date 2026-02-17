<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordExpiredController extends Controller
{
    public function show()
    {
        if (!session()->has('password_expired_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.password-expired');
    }

    public function update(Request $request)
    {
        $request->validate([
            'newpass' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $userId = session('password_expired_user_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        // prevent reusing old password
        if ($user->hasUsedPassword($request->newpass)) {
            return back()->withErrors([
                'newpass' => 'You have used this password recently. Please choose a different one.'
            ]);
        }

        // update using your model method
        $user->updatePassword($request->newpass, 90);

        session()->forget(['password_expired_user_id', 'password_expired_email']);

        return redirect()->route('login')
            ->with('status', 'Password updated successfully. Please login again.');
    }
}
