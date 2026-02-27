<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    private function getGoogle2FA()
    {
        return app('pragmarx.google2fa');
    }
    private function logAudit(string $action, string $tableName, array $contextData = []): void
{
    try {
        \App\Models\AuditTrail::create([
            'user_id'      => Auth::id(),
            'action'       => $action,
            'table_name'   => $tableName,
            'record_id'    => Auth::id(), // the affected record is the user themselves
            'old_values'   => null,
            'new_values'   => null,
            'context_data' => json_encode($contextData),
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    } catch (\Exception $e) {
        Log::error('2FA audit log failed', [
            'user_id' => Auth::id(),
            'action'  => $action,
            'error'   => $e->getMessage(),
        ]);
    }
}

    // Show setup page
    public function setup()
    {
        $user = Auth::user();

        if ($user->google2fa_secret) {
            return redirect()->route('dashboard')->with('info', '2FA is already enabled.');
        }

        $google2fa = $this->getGoogle2FA();
        $secret = $google2fa->generateSecretKey();

        $qrUrl = $google2fa->getQRCodeUrl(config('app.name'), $user->email, $secret);

        $writer = new Writer(
            new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd())
        );

        $qrCodeSvg = base64_encode($writer->writeString($qrUrl));

        session(['2fa_secret' => $secret]);

        return view('auth.2fa_setup', compact('qrCodeSvg', 'secret'));
    }

    // Confirm OTP and save secret to DB
   public function enable(Request $request)
{
    // Return JSON validation errors
    $request->validate(['otp' => 'required|digits:6']);

    $user = Auth::user();
    $secret = session('2fa_secret');

    if (!$secret) {
        return response()->json([
            'message' => 'Session expired. Please start 2FA setup again.'
        ], 422);
    }

    $google2fa = $this->getGoogle2FA();
    $valid = $google2fa->verifyKey($secret, $request->otp);

    Log::info('2FA enable attempt', ['user_id' => $user->id, 'valid' => $valid]);

    if (!$valid) {
        return response()->json([
            'message' => 'Invalid OTP. Please try again.'
        ], 422);
    }

    $user->google2fa_secret = $secret;
    $user->save();

   $this->logAudit('INSERT', 'users', ['action' => '2fa_enabled']);

    session()->forget('2fa_secret');
    session(['2fa_verified' => true]);

    return response()->json([
        'message' => 'success',
        'redirect' => route('dashboard')
    ]);
}

    // Show the verify page (triggered after login)
    public function showVerify()
    {
        if (!Auth::user()->google2fa_secret) {
            return redirect()->route('dashboard');
        }

        return view('auth.2fa_verify');
    }

    // Handle OTP submission after login
    public function verify(Request $request)
    {
        $request->validate(['one_time_password' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = $this->getGoogle2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->one_time_password);

        Log::info('2FA verify attempt', ['user_id' => $user->id, 'valid' => $valid]);

        if (!$valid) {
        return response()->json([
            'message' => 'Invalid OTP. Please try again.'
        ], 422);
    }
    
$this->logAudit('SELECT', 'users', ['action' => '2fa_verified']);
        session(['2fa_verified' => true]);

        return response()->json([
        'message' => 'success',
        'redirect' => route('dashboard')
    ]);
    }

    // Show disable form
    public function showDisableForm()
    {
        if (!Auth::user()->google2fa_secret) {
            return redirect()->route('dashboard')->with('info', '2FA is not enabled.');
        }

        return view('auth.2fa_disable');
    }

    // Handle disable
    public function disable(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        $user = Auth::user();
        $google2fa = $this->getGoogle2FA();

        $valid = $google2fa->verifyKey($user->google2fa_secret, $request->otp);

        Log::info('2FA disable attempt', ['user_id' => $user->id, 'valid' => $valid]);

        if (!$valid) {
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        $user->google2fa_secret = null;
        $user->save();

       
$this->logAudit('DELETE', 'users', ['action' => '2fa_disabled']);
        session()->forget('2fa_verified');

        return redirect()->route('dashboard')->with('success', '2FA has been disabled.');
    }
}