<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Pay — Setup Two-Factor Authentication</title>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    
     @vite(['resources/css/pages/2fasetup.css'])
     @vite('resources/css/app.scss')

    
</head>
<body>

<div class="toast-wrap" id="toastWrap"></div>

<div class="page-shell">

    <!-- Topbar -->
    <header class="topbar">
        <div class="brand">
            <img src="{{ asset('images/schaxist.png') }}" alt="Core Pay">
        </div>
        <span class="tagline">Payroll Management System</span>
    </header>

    <!-- Stage -->
    <main class="stage">
        <div class="setup-card">
            <div class="card-accent"></div>

            <!-- Header -->
            <div class="card-header">
                <div class="header-icon">
                    <span class="material-icons">shield</span>
                </div>
                <h1>Set Up Two-Factor Auth</h1>
                <p>Protect your account with an authenticator app</p>
            </div>

            <!-- Body -->
            <div class="card-body">

                @if(session('error'))
                <div class="inline-alert danger">
                    <span class="material-icons">error_outline</span>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                <!-- Step 1: QR Code -->
                <div class="step-label">
                    <div class="step-num">1</div>
                    <h2>Scan QR Code</h2>
                </div>

                <div class="qr-block">
    <div class="qr-wrap">
       
        {!! $qrCodeSvg !!}
    </div>
</div>

                <div class="inline-alert info">
                    <span class="material-icons">info_outline</span>
                    <span>Scan with <strong>Google Authenticator</strong>, <strong>Authy</strong>, or any TOTP-compatible app.</span>
                </div>

                <hr class="section-divider">

                <!-- Step 2: Manual entry -->
                <div class="step-label">
                    <div class="step-num">2</div>
                    <h2>Can't Scan? Manual Entry</h2>
                </div>

                <div class="secret-block">
                    <code id="secretKey">{{ $secret }}</code>
                    <button type="button" class="copy-btn" id="copy-btn" >
                        <span class="material-icons">content_copy</span> Copy
                    </button>
                </div>

                <p class="paragraph">
                    Enter this secret key manually in your authenticator app if the QR scan doesn't work.
                </p>

                <hr class="section-divider">

                <!-- Step 3: Verify -->
                <div class="step-label">
                    <div class="step-num">3</div>
                    <h2>Verify Setup</h2>
                </div>

                <form id="setupForm" method="POST" data-setup-url="{{ route('2fa.enable') }}"> 
                    @csrf

                    <span class="otp-label">Enter the 6-digit code from your app</span>

                    <!-- Six digit boxes -->
                    <div class="otp-boxes" id="otpBoxes">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="0">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="1">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="2">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="3">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="4">
                        <input class="otp-box" type="text" inputmode="numeric" maxlength="1" data-index="5">
                    </div>

                    <p class="field-error" id="otp-error"></p>

                    {{-- Hidden input — populated by JS before submit --}}
                    <input type="text" id="otp" name="otp" readonly>

                    <button type="submit" class="btn-enable" id="submitBtn" disabled>
                        <span class="material-icons">verified_user</span>
                        <span id="btnLabel">Enable Two-Factor Authentication</span>
                    </button>

                    <a href="{{ route('profile.edit') }}" class="btn-cancel">
                        <span class="material-icons">close</span>
                        Cancel
                    </a>
                </form>

            </div>
        </div>
    </main>
</div>
@vite(['resources/js/app.js'])
@vite(['resources/js/setup2fs.js'])

</body>
</html>