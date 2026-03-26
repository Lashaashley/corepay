
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Core Pay - Password Change</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
     @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])

</head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style nonce="{{ $cspNonce }}">
    /* Custom styles for password expired page */
    .bg-soft-danger {
        background-color: #f8d7da;
    }
    
    .bg-soft-info {
        background-color: #d1ecf1;
    }
    
    #passwordRequirements .text-success i {
        color: #28a745;
    }
    
    #passwordMatchMessage.text-success i {
        color: #28a745;
    }
    
    #passwordMatchMessage.text-danger i {
        color: #dc3545;
    }
    
    .input-group .btn-outline-secondary {
        border-color: #ced4da;
    }
    
    .input-group .btn-outline-secondary:hover {
        background-color: #f8f9fa;
    }
    
    /* Animation for alerts */
    .alert {
        transition: all 0.3s ease;
    }
    
    /* Responsive adjustments */
    @media (max-width: 576px) {
        .container {
            padding: 10px;
        }
        
        .card-body {
            padding: 1.5rem;
        }
    }
</style>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-danger text-white py-4 border-0">
                    <div class="text-center">
                        <i class="fas fa-lock fa-3x mb-3"></i>
                        <h3 class="fw-bold mb-0">Password Expired</h3>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                        <div>
                            <strong>Your password has expired!</strong> Please set a new password to continue accessing your account.
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger border-0 bg-soft-danger rounded-3 mb-4">
                            <ul class="mb-0 list-unstyled">
                                @foreach ($errors->all() as $error)
                                    <li class="d-flex align-items-center">
                                        <i class="fas fa-times-circle me-2 text-danger"></i>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.expired.update') }}" id="passwordExpiredForm">
                        @csrf

                        <!-- Password Strength Indicator -->
                        <div class="mb-4">
                            <div class="progress mb-2" style="height: 5px;">
                                <div id="passwordStrengthBar" class="progress-bar" role="progressbar" style="width: 0%; background-color: #dc3545;"></div>
                            </div>
                            
                            <div class="mb-3 position-relative">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-key me-2 text-danger"></i>New Password
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i class="fas fa-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           name="newpass" 
                                           id="newpass"
                                           class="form-control border-start-0 ps-0 @error('newpass') is-invalid @enderror" 
                                           placeholder="Enter new password"
                                           required
                                           autocomplete="new-password"
                                           autofocus>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye" id="toggleNewPasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Password Requirements Checklist -->
                            <div class="bg-light p-3 rounded-3 small" id="passwordRequirements">
                                <p class="mb-2 text-muted">Password must contain:</p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-1" id="lengthCheck">
                                        <i class="fas fa-circle text-secondary me-2 fa-xs"></i>
                                        At least 8 characters
                                    </li>
                                    <li class="mb-1" id="uppercaseCheck">
                                        <i class="fas fa-circle text-secondary me-2 fa-xs"></i>
                                        At least one uppercase letter
                                    </li>
                                    <li class="mb-1" id="lowercaseCheck">
                                        <i class="fas fa-circle text-secondary me-2 fa-xs"></i>
                                        At least one lowercase letter
                                    </li>
                                    <li class="mb-1" id="numberCheck">
                                        <i class="fas fa-circle text-secondary me-2 fa-xs"></i>
                                        At least one number
                                    </li>
                                    <li class="mb-1" id="specialCheck">
                                        <i class="fas fa-circle text-secondary me-2 fa-xs"></i>
                                        At least one special character
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-check-circle me-2 text-danger"></i>Confirm New Password
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" 
                                       name="newpass_confirmation" 
                                       id="newpass_confirmation"
                                       class="form-control border-start-0 ps-0" 
                                       placeholder="Confirm new password"
                                       required
                                       autocomplete="new-password">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                                </button>
                            </div>
                            <div id="passwordMatchMessage" class="mt-2 small"></div>
                        </div>

                        <!-- Password Tips -->
                        

                        <button type="submit" class="btn btn-danger btn-lg w-100 py-3 fw-semibold" id="submitBtn">
                            <i class="fas fa-sync-alt me-2"></i>Update Password
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="{{ route('logout') }}" 
                           class="text-muted text-decoration-none"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout and try again later
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

                <div class="card-footer bg-light border-0 py-3">
                    <div class="text-center text-muted small">
                        <i class="fas fa-shield-alt me-1"></i>
                        Your security is our priority
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Font Awesome if not already included -->


<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const newpass = document.getElementById('newpass');
    const confirmpass = document.getElementById('newpass_confirmation');
    const submitBtn = document.getElementById('submitBtn');
    
    // Toggle password visibility
    document.getElementById('toggleNewPassword').addEventListener('click', function() {
        const type = newpass.getAttribute('type') === 'password' ? 'text' : 'password';
        newpass.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const type = confirmpass.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmpass.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Password strength checker
    function checkPasswordStrength(password) {
        const checks = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };
        
        // Update checklist
        for (let [key, passed] of Object.entries(checks)) {
            const element = document.getElementById(`${key}Check`);
            if (element) {
                const icon = element.querySelector('i');
                if (passed) {
                    icon.className = 'fas fa-check-circle text-success me-2 fa-xs';
                    element.style.color = '#28a745';
                } else {
                    icon.className = 'fas fa-circle text-secondary me-2 fa-xs';
                    element.style.color = 'inherit';
                }
            }
        }
        
        // Calculate strength percentage
        const passedCount = Object.values(checks).filter(Boolean).length;
        const strengthPercentage = (passedCount / 5) * 100;
        
        // Update progress bar
        const strengthBar = document.getElementById('passwordStrengthBar');
        strengthBar.style.width = strengthPercentage + '%';
        
        // Set color based on strength
        if (strengthPercentage <= 40) {
            strengthBar.style.backgroundColor = '#dc3545'; // Red
        } else if (strengthPercentage <= 70) {
            strengthBar.style.backgroundColor = '#ffc107'; // Yellow
        } else {
            strengthBar.style.backgroundColor = '#28a745'; // Green
        }
        
        return passedCount === 5;
    }
    
    // Password match checker
    function checkPasswordMatch() {
        const matchMessage = document.getElementById('passwordMatchMessage');
        const confirmGroup = confirmpass.closest('.mb-4');
        
        if (confirmpass.value === '') {
            matchMessage.innerHTML = '';
            matchMessage.className = 'mt-2 small';
            return false;
        }
        
        if (newpass.value === confirmpass.value) {
            matchMessage.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i>Passwords match';
            matchMessage.className = 'mt-2 small text-success';
            return true;
        } else {
            matchMessage.innerHTML = '<i class="fas fa-exclamation-circle text-danger me-1"></i>Passwords do not match';
            matchMessage.className = 'mt-2 small text-danger';
            return false;
        }
    }
    
    // Real-time validation
    newpass.addEventListener('input', function() {
        const isValid = checkPasswordStrength(this.value);
        checkPasswordMatch();
        
        // Enable/disable submit button
        updateSubmitButton();
    });
    
    confirmpass.addEventListener('input', function() {
        checkPasswordMatch();
        updateSubmitButton();
    });
    
    function updateSubmitButton() {
        const isStrong = checkPasswordStrength(newpass.value);
        const doMatch = newpass.value === confirmpass.value && confirmpass.value !== '';
        
        if (isStrong && doMatch) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-danger');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-danger');
            submitBtn.classList.add('btn-secondary');
        }
    }
    
    // Form submission
    document.getElementById('passwordExpiredForm').addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            alert('Please ensure your password meets all requirements and matches the confirmation.');
        }
    });
    
    // Initialize button state
    updateSubmitButton();
});
</script>