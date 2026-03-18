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

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:       #0d1117;
            --surface:   #ffffff;
            --muted:     #6b7280;
            --border:    #e5e7eb;
            --accent:    #1a56db;
            --accent-lt: #eff6ff;
            --success:   #059669;
            --danger:    #dc2626;
            --radius:    14px;
            --shadow-md: 0 4px 24px rgba(0,0,0,.08);
            --shadow-lg: 0 12px 48px rgba(0,0,0,.12);
        }

        html, body {
            height: 100%;
            font-family: 'DM Sans', sans-serif;
            background: #f3f4f8;
            color: var(--ink);
        }

        /* ── Layout ─────────────────────────────────── */
        .page-shell {
            min-height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr;
        }

        /* ── Top bar ─────────────────────────────────── */
        .topbar {
            padding: 18px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
        }

        .topbar .brand img {
            height: 36px;
            object-fit: contain;
        }

        .topbar .tagline {
            font-size: 13px;
            color: var(--muted);
            letter-spacing: .02em;
        }

        /* ── Center stage ────────────────────────────── */
        .stage {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 20px;
        }

        /* ── Card ─────────────────────────────────────── */
        .card {
            background: var(--surface);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            width: 100%;
            max-width: 460px;
            overflow: hidden;
            animation: rise .55s cubic-bezier(.22,.61,.36,1) both;
        }

        @keyframes rise {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── Card header accent strip ─────────────────── */
        .card-accent {
            height: 5px;
            background: linear-gradient(90deg, #1a56db 0%, #6366f1 60%, #8b5cf6 100%);
        }

        /* ── Card body ────────────────────────────────── */
        .card-body {
            padding: 40px 44px 44px;
        }

        .card-title {
            font-family: 'Syne', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .card-subtitle {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 36px;
        }

        /* ── Form fields ──────────────────────────────── */
        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
            letter-spacing: .01em;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 18px;
            pointer-events: none;
            transition: color .2s;
        }

        .input-wrap .icon-right {
            left: auto;
            right: 14px;
            pointer-events: auto;
            cursor: pointer;
        }

        .input-wrap input {
            width: 100%;
            height: 48px;
            padding: 0 44px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            color: var(--ink);
            background: #fafafa;
            outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }

        .input-wrap input:focus {
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 4px rgba(26,86,219,.1);
        }

        .input-wrap input:focus ~ .icon { color: var(--accent); }

        .field-error {
            font-size: 12px;
            color: var(--danger);
            margin-top: 5px;
        }

        /* ── Payroll checkboxes ───────────────────────── */
        .payroll-group {
            margin-bottom: 20px;
        }

        .payroll-group label.group-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 10px;
        }

        .payroll-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .payroll-chip {
            position: relative;
        }

        .payroll-chip input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            width: 0; height: 0;
        }

        .payroll-chip label {
            display: inline-flex;
            align-items: center;
            padding: 7px 16px;
            border: 1.5px solid var(--border);
            border-radius: 100px;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            transition: all .2s;
            background: #fafafa;
        }

        .payroll-chip input:checked + label {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-lt);
        }

        .payroll-chip label:hover {
            border-color: #9ca3af;
            color: var(--ink);
        }

        /* ── Row: remember + forgot ───────────────────── */
        .meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
            cursor: pointer;
        }

        .remember input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 13px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: opacity .2s;
        }

        .forgot-link:hover { opacity: .75; }

        /* ── Submit button ────────────────────────────── */
        .btn-login {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #1a56db, #4f46e5);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: .02em;
            transition: transform .2s, box-shadow .2s, filter .2s;
            box-shadow: 0 4px 16px rgba(26,86,219,.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(26,86,219,.4);
            filter: brightness(1.05);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login .material-icons {
            font-size: 20px;
        }

        /* ── Empty state ──────────────────────────────── */
        .no-payroll {
            font-size: 13px;
            color: var(--muted);
            font-style: italic;
        }

        /* ── Responsive ───────────────────────────────── */
        @media (max-width: 520px) {
            .card-body { padding: 32px 24px 36px; }
            .topbar { padding: 16px 20px; }
        }
    </style>
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

<script>
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