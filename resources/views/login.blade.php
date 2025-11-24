<!-- resources/views/auth/login.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Core Pay - User Login</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<<<<<<< HEAD
     @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])
=======
    @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])
>>>>>>> a35bdf2590d2140b39b372eba001d1f0f37e432a

    <style>
        /* public/css/custom.css */
.custom-control-checkbox {
    padding-left: 1.5rem;
}

.custom-control-label {
    cursor: pointer;
    user-select: none;
}

.cursor-pointer {
    cursor: pointer;
}

.input-group.custom .w-100 {
    padding: 10px 15px;
    background-color: #f8f9fa;
    border-radius: 5px;
    margin-bottom: 15px;
}
    </style>
</head>
<body class="login-page">
    <div class="login-header box-shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <img src="{{ asset('images/schaxist.png') }}" alt="Example Image">
            </div>
        </div>
    </div>

    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-7">
                    <img src="{{ asset('images/login-page-img.png') }}" alt="Login Image">
                </div>
                <div class="col-md-6 col-lg-5">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center text-primary">User Login</h2>
                        </div>

                        <!-- Laravel Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Field -->
                            <div class="input-group custom">
                                <input type="email" 
                                       class="form-control form-control-lg" 
                                       placeholder="Email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus>
                                <div class="input-group-append custom">
                                    <span class="input-group-text">
                                        <i class="icon-copy fa fa-envelope-o" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <!-- ✅ Payroll Types Selection -->
                            <!-- ✅ Payroll Types Selection -->
<div class="input-group custom">
    <div class="w-100">
        <label class="font-weight-bold mb-2">Select Payroll Types:</label>
        
        @php
            $payrollTypes = $payrollTypes ?? collect(); // Fallback to empty collection
        @endphp
        
        @if($payrollTypes->count() > 0)
            @foreach($payrollTypes as $type)
                <div class="custom-control custom-checkbox mb-0">
                    <input class="custom-control-input" 
                           type="checkbox" 
                           name="allowedPayroll[]" 
                           id="payroll{{ $type->ID }}" 
                           value="{{ $type->ID }}"
                           {{ in_array($type->ID, old('allowedPayroll', [])) ? 'checked' : '' }}>
                    <label class="custom-control-label" 
                           for="payroll{{ $type->ID }}">
                        {{ $type->pname }}
                    </label>
                </div>
            @endforeach
        @else
            <p class="text-muted">No payroll types found.</p>
        @endif
    </div>
</div>

                            <!-- Password Field -->
                            <div class="input-group custom">
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       placeholder="********" 
                                       name="password" 
                                       required>
                                <div class="input-group-append custom">
                                    <span class="input-group-text cursor-pointer" onclick="togglePasswordVisibility()">
                                        <i class="material-icons visibility">visibility</i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            <!-- Remember Me & Forgot Password -->
                            <div class="row pb-30">
                                <div class="col-6">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="remember" 
                                               name="remember"
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Remember Me
                                        </label>
                                    </div>
                                </div>
                                <div class="col-6 text-right">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                                    @endif
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <button class="btn btn-primary btn-lg btn-block" type="submit">
                                        Sign In
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.querySelector('input[name="password"]');
            const icon = document.querySelector('.material-icons.visibility');
            
            if (passwordInput.getAttribute('type') === 'password') {
                passwordInput.setAttribute('type', 'text');
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.setAttribute('type', 'password');
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>
