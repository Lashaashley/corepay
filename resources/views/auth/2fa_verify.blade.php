<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Pay — Two-Factor Verification</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    @vite(['resources/css/pages/2faverify.css'])
    @vite('resources/css/app.scss')
</head>
<body>

<div class="page-shell">

    <!-- Top bar -->
    <header class="topbar">
        <div class="brand">
            <img src="{{ asset('images/schaxist.png') }}" alt="Core Pay">
        </div>
        <span class="tagline">Payroll Management System</span>
    </header>

    <!-- Stage -->
    <main class="stage">
        <div class="card">
            <div class="card-accent"></div>

            <div class="card-body">

                <!-- Icon -->
                <div class="shield-wrap">
                    <span class="material-icons">shield</span>
                </div>

                <h1 class="card-title">Two-Factor Verification</h1>
                <p class="card-subtitle">Enter the 6-digit code from your authenticator app</p>

                <!-- Session error -->
                @if(session('error'))
                <div class="inline-alert danger">
                    <span class="material-icons">error_outline</span>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <!-- Info hint -->
                <div class="inline-alert info">
                    <span class="material-icons">info_outline</span>
                    <span>Open your authenticator app and enter the current code to continue.</span>
                </div>

                <!-- 2FA Form -->
                <form id="twoFactorForm" method="POST" data-verify-url="{{ route('2fa.check') }}">
                    @csrf
                    <!-- Hidden input sent on submit -->
                    <input type="text" id="one_time_password" name="one_time_password" readonly>

                    <label class="otp-label">Authentication code</label>

                    <!-- 6 visual digit boxes -->
                    <div class="otp-boxes" id="otpBoxes">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="0">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="1">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="2">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="3">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="4">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="5">
                    </div>

                    <p class="field-error" id="otp-error">
                        @error('one_time_password') {{ $message }} @enderror
                    </p>

                    <!-- Countdown -->
                    <div class="countdown-row">
                        <span class="material-icons">schedule</span>
                        Code expires in <span id="countdown">5:00</span>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-verify" id="submitBtn" disabled>
                        <span class="material-icons">verified_user</span>
                        <span id="btnLabel">Verify and Continue</span>
                    </button>
                </form>

                <div class="divider">or</div>

                <div class="footer-links">
    <a href="#" id="logoutLink">
        <span class="material-icons">logout</span> Back to Login
    </a>
    <div class="sep"></div>
    <a href="#" id="openRecovery">
        <span class="material-icons">key</span> Use Recovery Code
    </a>
</div>

<!-- Hidden logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
    @csrf
</form>

            </div><!-- /card-body -->

            <div class="card-footer">
                <span class="material-icons">lock</span>
                Secured with two-factor authentication
            </div>
        </div><!-- /card -->
    </main>
</div>

<!-- Toast container -->
<div class="toast-wrap" id="toastWrap"></div>

<!-- Recovery Code Modal -->
<div class="modal-backdrop" id="recoveryModal">
    <div class="modal-card">
        <div class="modal-header">
            <div class="modal-header-left">
                <span class="material-icons">key</span>
                Use Recovery Code
            </div>
            <button class="modal-close" id="closeRecovery">
                <span class="material-icons">close</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Lost access to your authenticator? Enter one of your one-time recovery codes below.</p>
            <form method="POST" id="recoveryForm">
                @csrf
                <div class="modal-field">
                    <label for="recovery_code">Recovery code</label>
                    <input type="text" name="recovery_code" id="recovery_code"
                           placeholder="xxxx-xxxx-xxxx" autocomplete="off" required>
                </div>
                <button type="submit" class="btn-recovery">Verify Recovery Code</button>
            </form>
        </div>
    </div>
</div>



@vite(['resources/js/verify.js'])

</body>
</html>


