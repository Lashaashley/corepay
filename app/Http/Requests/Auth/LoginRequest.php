<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
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
            'email'            => ['required', 'string', 'email', 'max:255'],
            'password'         => ['required', 'string', 'max:1024'],
            'allowedPayroll'   => ['nullable', 'array', 'max:50'],
            'allowedPayroll.*' => ['integer', 'exists:prolltypes,ID'],
        ];
    }

    /**
     * Remove sensitive fields from the logged request data.
     * Prevents passwords appearing in Laravel's exception handler logs.
     */
    protected $dontFlash = ['password', 'password_confirmation'];

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // ── SECURITY: Do NOT log the email at attempt time.
        // Logging failed login emails can leak valid email addresses into log
        // files, which may be accessible to more people than the auth system.
        // Log only after successful authentication using the user ID instead.

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey(), 60);

            // ── SECURITY: Generic error message — do not distinguish between
            // "email not found" and "wrong password". Distinct messages allow
            // user enumeration attacks.
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $user = Auth::user();

        // ── Account status check ─────────────────────────────────────────────
        // Must happen immediately after Auth::attempt() succeeds, before any
        // session data is written or any further processing occurs.
        if ($user->Status !== 'ACTIVE') {
            Auth::logout();

            // Generic message — do not confirm whether the account exists
            throw ValidationException::withMessages([
                'email' => 'Your account is disabled. Please contact your administrator.',
            ]);
        }

        // ── Payroll access validation ────────────────────────────────────────
        $selectedPayrolls = array_map('intval', $this->input('allowedPayroll', []));
        $isSuperAdmin     = method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin();

        if (! $isSuperAdmin) {
            $userAllowedPayroll = ! empty($user->allowedprol)
                ? array_map('intval', explode(',', $user->allowedprol))
                : [];

            if (empty($selectedPayrolls)) {
                Auth::logout();

                throw ValidationException::withMessages([
                    'allowedPayroll' => 'Please select at least one payroll type.',
                ]);
            }

            $invalidPayrolls = array_diff($selectedPayrolls, $userAllowedPayroll);

            if (! empty($invalidPayrolls)) {
                Auth::logout();

                // ── SECURITY: Do not reveal which specific IDs were invalid.
                // The original message confirmed valid IDs exist, which aids
                // enumeration. Generic message used instead.
                throw ValidationException::withMessages([
                    'allowedPayroll' => "You don't have permission for the selected payroll type(s).",
                ]);
            }
        } else {
            if (empty($selectedPayrolls)) {
                $selectedPayrolls = \App\Models\Paytypes::pluck('ID')->toArray();
            }
        }

        // ── Session data ─────────────────────────────────────────────────────
        // Store before session regeneration (which happens in the controller
        // immediately after authenticate() returns).
        session([
            'allowedPayroll' => $selectedPayrolls,
            'user_id'        => $user->id,
            'user_name'      => $user->name,
            'user_email'     => $user->email,
            '_session_created_at'     => now()->timestamp,
        ]);

        // ── Audit trail ──────────────────────────────────────────────────────
        // Log AFTER all validation passes — only log successful authentications.
        // Use user ID, never email, in log entries (email is PII).
        Log::info('User authenticated successfully', [
            'user_id'   => $user->id,
            'payrolls'  => $selectedPayrolls,
            'ip'        => $this->ip(),
            // ── SECURITY: Do not log user_allowedprol (full permission set).
            // Logging authorisation data creates an audit trail of what
            // access each user has, which has its own exposure risk.
        ]);

        logAuditTrail(
            $user->id,
            'LOGIN',
            'users_table',
            (string) $user->id,
            null,
            null,
            [
                'action'  => 'User authenticated',
                'user_id' => $user->id,
                'ip'      => $this->ip(),
            ]
        );
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
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}