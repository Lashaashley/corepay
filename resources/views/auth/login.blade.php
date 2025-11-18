<!-- resources/views/auth/login.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>School Asist - User Login</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    @vite('resources/css/app.css')
    @vite('resources/css/icon-font.min.css')
    @vite('resources/css/style.css')
    @vite('resources/js/app.js')
    @vite('resources/js/script.min.js')
    @vite('resources/js/process.js')
    @vite('resources/js/bootstrap.js')
    @vite('resources/js/show.js')

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
    @csrf <!-- CSRF Token -->

    <!-- Email Field -->
    <div class="input-group custom">
        <input type="email" class="form-control form-control-lg" placeholder="Email" 
               name="email" value="{{ old('email') }}" required autofocus>
        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="icon-copy fa fa-envelope-o" aria-hidden="true"></i>
            </span>
        </div>
    </div>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror
    <!-- Payroll Types -->
<div class="input-group custom mt-3">
    <div>
        @forelse ($payrollTypes as $type)
            <div class="custom-control custom-checkbox">
                <input class="custom-control-input"
                       type="checkbox"
                       name="allowedPayroll[]"
                       id="payroll{{ $type->ID }}"
                       value="{{ $type->ID }}">

                <label class="custom-control-label"
                       for="payroll{{ $type->ID }}">
                    {{ $type->pname }}
                </label>
            </div>
        @empty
            <p>No payroll types found.</p>
        @endforelse
    </div>
</div>

 
    <!-- Password Field -->
    <div class="input-group custom">
        <input type="password" class="form-control form-control-lg" placeholder="********" 
               name="password" required>
        <div class="input-group-append custom">
            <span class="input-group-text">
                <i class="material-icons visibility">visibility</i>
            </span>
        </div>
    </div>
    @error('password')
        <span class="text-danger">{{ $message }}</span>
    @enderror

    <div class="row pb-30">
        <div class="col-6">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
        </div>
        <div class="col-6 text-right">
            <a href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="row">
        <div class="col-sm-12">
            <button class="btn btn-primary btn-lg btn-block" type="submit">Sign In</button>
        </div>
    </div>
</form>

                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
   document.querySelector('.material-icons.visibility').addEventListener('click', togglePasswordVisibility);

function togglePasswordVisibility() {
    const passwordInput = document.querySelector('input[name="password"]');
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
}
</script>
</body>
</html>
