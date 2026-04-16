<!-- resources/views/auth/login.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Core Pay — Login</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

   
     @vite(['resources/css/pages/login.css'])
     @vite('resources/css/app.scss')

   
</head>
<body>

<div class="page-shell">

    <!-- Top bar -->
    <header class="topbar">
        <div class="brand">
            <img src="{{ asset('images/schaxist.png') }}" alt="Core Pay">
        </div>
        <span class="tagline">Core Pay</span>
    </header>

    <!-- Center stage -->
    <main class="stage">
        <div class="card">
            <div class="card-accent"></div>

            <div class="card-body">
                <h1 class="card-title">Welcome back</h1>
                <p class="card-subtitle">Sign in to your Core Pay account</p>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="field">
                        <label for="email">Email address</label>
                        <div class="input-wrap">
                            <span class="icon material-icons">mail_outline</span>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email') }}"
                                   placeholder="you@company.com"
                                   required autofocus>
                        </div>
                        @error('email')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payroll Types -->
                    @if($payrollTypes->isNotEmpty())
                    <div class="payroll-group">
                        <label class="group-label">Payroll access</label>
                        <div class="payroll-options">
                            @foreach ($payrollTypes as $type)
                            <div class="payroll-chip">
                                <input type="checkbox"
                                       name="allowedPayroll[]"
                                       id="payroll{{ $type->ID }}"
                                       value="{{ $type->ID }}">
                                <label for="payroll{{ $type->ID }}">{{ $type->pname }}</label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Password -->
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <span class="icon material-icons">lock_outline</span>
                            <input type="password" id="password" name="password"
                                   placeholder="••••••••" required>
                            <span class="icon icon-right material-icons" id="toggle-pw">visibility</span>
                        </div>
                        @error('password')
                            <p class="field-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember / Forgot -->
                    <div class="meta-row">
                        <label class="remember">
                            <input type="checkbox" name="remember" id="remember">
                            Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-login">
                        <span class="material-icons">login</span>
                        Sign in
                    </button>

                </form>
            </div><!-- /card-body -->
        </div><!-- /card -->
    </main>

</div><!-- /page-shell -->

<script nonce="{{ $cspNonce }}">
    const toggleBtn = document.getElementById('toggle-pw');
    const pwInput   = document.getElementById('password');

    toggleBtn.addEventListener('click', () => {
        const isPassword = pwInput.type === 'password';
        pwInput.type = isPassword ? 'text' : 'password';
        toggleBtn.textContent = isPassword ? 'visibility_off' : 'visibility';
    });
</script>
</body>
</html>