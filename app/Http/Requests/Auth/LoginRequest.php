<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log; // ✅ Add this
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'allowedPayroll' => ['nullable', 'array'],
            'allowedPayroll.*' => ['integer', 'exists:prolltypes,ID'],
        ];
    }

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // ✅ Log the incoming data
        Log::info('Login attempt', [
            'email' => $this->email,
            'allowedPayroll' => $this->input('allowedPayroll', [])
        ]);

        // Attempt authentication
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        // Get authenticated user
        $user = Auth::user();
        
        // ✅ Log user data
        Log::info('User authenticated', [
            'user_id' => $user->id,
            'user_allowedprol' => $user->allowedprol
        ]);
        
        // Get selected payroll types
        $selectedPayrolls = $this->input('allowedPayroll', []);
        $selectedPayrolls = array_map('intval', $selectedPayrolls);

        // ✅ Log selected payrolls
        Log::info('Selected payrolls', [
            'selectedPayrolls' => $selectedPayrolls
        ]);

        // Check if user is super admin
        $isSuperAdmin = method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();
        
        // ✅ Log super admin status
        Log::info('Super admin check', [
            'isSuperAdmin' => $isSuperAdmin
        ]);
        
        // Validate payroll access (skip for super admin)
        if (!$isSuperAdmin) {
            // Get user's allowed payroll types from database
            $userAllowedPayroll = !empty($user->allowedprol)
                ? array_map('intval', explode(',', $user->allowedprol))
                : [];

            // ✅ Log user allowed payrolls
            Log::info('User allowed payrolls', [
                'userAllowedPayroll' => $userAllowedPayroll
            ]);

            // If no payrolls selected, throw error
            if (empty($selectedPayrolls)) {
                Auth::logout();
                
                throw ValidationException::withMessages([
                    'allowedPayroll' => 'Please select at least one payroll type.',
                ]);
            }

            // Check if user selected payrolls they don't have access to
            $invalidPayrolls = array_diff($selectedPayrolls, $userAllowedPayroll);
            
            if (!empty($invalidPayrolls)) {
                Auth::logout();
                
                throw ValidationException::withMessages([
                    'allowedPayroll' => "You don't have permission for the selected payroll type(s).",
                ]);
            }
        } else {
            // If super admin and no payrolls selected, allow all
            if (empty($selectedPayrolls)) {
                $selectedPayrolls = \App\Models\Paytypes::pluck('ID')->toArray();
                Log::info('Super admin - all payrolls assigned', [
                    'selectedPayrolls' => $selectedPayrolls
                ]);
            }
        }

        // ✅ Store selected payroll types in session BEFORE regeneration
        session([
            'allowedPayroll' => $selectedPayrolls,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        // ✅ Log session data
        Log::info('Session data stored', [
            'session_allowedPayroll' => session('allowedPayroll'),
            'session_user_id' => session('user_id'),
            'all_session' => session()->all()
        ]);
    }

    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}