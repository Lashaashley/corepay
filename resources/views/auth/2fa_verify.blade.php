<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Core Pay - Two-Factor Verification</title>

    <!-- Site favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16x16.png') }}">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
    @vite(['resources/css/app.scss', 'resources/css/icon-font.min.css', 'resources/css/style.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    /* Custom styles for 2FA pages */
          .custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    z-index: 9999;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.5s ease;
    display: none; /* Initially hidden via JS, but transition handled by opacity/transform */
}

.custom-alert.show {
    opacity: 1;
    transform: translateX(0);
    display: block; /* Needed to make it visible */
}

.alert-success {
    animation: successPulse 1s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(0.95); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
} 
    .bg-soft-primary {
        background-color: #cfe2ff;
    }
    
    .bg-soft-success {
        background-color: #d1e7dd;
    }
    
    .bg-soft-danger {
        background-color: #f8d7da;
    }
    
    .otp-input {
        letter-spacing: 0.5rem;
        font-size: 1.5rem;
        text-align: center;
        font-weight: 600;
    }
    
    .qr-code-container {
        background: white;
        padding: 1rem;
        border-radius: 1rem;
        display: inline-block;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    
    .secret-key {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 0.5rem;
        font-family: monospace;
        font-size: 1.1rem;
        letter-spacing: 2px;
        border: 1px dashed #dee2e6;
    }
    
    .step-indicator {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }
    
    .step {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #6c757d;
        position: relative;
    }
    
    .step.active {
        background: #0d6efd;
        color: white;
    }
    
    .step.completed {
        background: #198754;
        color: white;
    }
    
    .step:not(:last-child)::after {
        content: '';
        position: absolute;
        width: 4rem;
        height: 2px;
        background: #e9ecef;
        left: 2.5rem;
        top: 50%;
        transform: translateY(-50%);
    }
    
    .step.completed:not(:last-child)::after {
        background: #198754;
    }
    
    .alert {
        transition: all 0.3s ease;
    }
    
    @media (max-width: 576px) {
        .container {
            padding: 10px;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .step:not(:last-child)::after {
            width: 2rem;
        }
    }
    @keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
  
</style>
<div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert">
    <strong id="alert-title"></strong> <span id="alert-message"></span>
    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white py-4 border-0">
                    <div class="text-center">
                        <i class="fas fa-shield-alt fa-3x mb-3"></i>
                        <h3 class="fw-bold mb-0">Two-Factor Authentication</h3>
                        <p class="mb-0 mt-2 text-white-50">Verify your identity</p>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                            <div>{{ session('error') }}</div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-info-circle me-3 fa-lg"></i>
                        <div>
                            <strong>Secure your account!</strong> Please enter the 6-digit code from your authenticator app.
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <div class="bg-light p-3 rounded-3 d-inline-block">
                            <i class="fas fa-mobile-alt fa-4x text-primary"></i>
                        </div>
                    </div>

                    <form id="twoFactorForm">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-key me-2 text-primary"></i>Authentication Code
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="text"
                                       name="one_time_password"
                                       id="one_time_password"
                                       class="form-control border-start-0 ps-0 otp-input @error('one_time_password') is-invalid @enderror"
                                       placeholder="• • • • • •"
                                       maxlength="6"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       required
                                       autofocus>
                            </div>
                            <span id="one_time_password-error" class="text-danger small mt-1 d-block"></span>
                            @error('one_time_password')
                                <div class="text-danger small mt-1">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Auto-submit when 6 digits entered -->
                        <div class="text-center mb-3 small text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Code expires in <span id="countdown">5:00</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-semibold" id="submitBtn">
                            <i class="fas fa-check-circle me-2"></i>Verify and Continue
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted small mb-2">Having trouble?</p>
                        <a href="{{ route('login') }}" 
                           class="text-decoration-none me-3"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-1"></i> Back to Login
                        </a>
                        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#recoveryModal">
                            <i class="fas fa-redo-alt me-1"></i> Use Recovery Code
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>

                <div class="card-footer bg-light border-0 py-3">
                    <div class="text-center text-muted small">
                        <i class="fas fa-shield-alt me-1"></i>
                        Two-factor authentication adds an extra layer of security
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recovery Code Modal -->
<div class="modal fade" id="recoveryModal" tabindex="-1" aria-labelledby="recoveryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark fw-bold" id="recoveryModalLabel">
                    <i class="fas fa-redo-alt me-2"></i>Use Recovery Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Enter one of your recovery codes to access your account.</p>
                <form method="POST" >
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Recovery Code</label>
                        <input type="text" 
                               name="recovery_code" 
                               class="form-control" 
                               placeholder="Enter recovery code"
                               required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Verify Recovery Code</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function () {

    // OTP auto-submit when 6 digits entered
    $('#one_time_password').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length === 6) {
            $('#twoFactorForm').submit();
        }
    });

    $('#twoFactorForm').on('submit', function (e) {
        e.preventDefault();

        const submitBtn = $('#submitBtn');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin me-2"></i>Verifying...').prop('disabled', true);

        $.ajax({
            url: "{{ route('2fa.check') }}",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function (response) {
                showAlert('success', 'Verified!', response.message);
                setTimeout(function () {
                    window.location.href = response.redirect;
                }, 1500);
            },
            error: function (xhr) {
                const json = xhr.responseJSON;
                if (xhr.status === 422 && json) {
                    if (json.errors) {
                        $.each(json.errors, function (key, value) {
                            $('#' + key + '-error').html(
                                '<i class="fas fa-exclamation-circle me-1"></i>' + value[0]
                            );
                        });
                    }
                    if (json.message) {
                        showAlert('danger', 'Invalid Code', json.message);
                    }
                } else {
                    showAlert('danger', 'Error', 'Something went wrong. Please try again.');
                }
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

});
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('one_time_password');
    const submitBtn = document.getElementById('submitBtn');

    
    // Countdown timer (5 minutes)
    let timeLeft = 300; // 5 minutes in seconds
    const countdownEl = document.getElementById('countdown');
    
    const timer = setInterval(() => {
        if (timeLeft <= 0) {
            clearInterval(timer);
            countdownEl.innerHTML = 'Expired';
            countdownEl.classList.add('text-danger');
        } else {
            timeLeft--;
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            countdownEl.innerHTML = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
    }, 1000);
    
    // Prevent form submission if not 6 digits
    document.getElementById('twoFactorForm').addEventListener('submit', function(e) {
        if (otpInput.value.length !== 6) {
            e.preventDefault();
            alert('Please enter a valid 6-digit code.');
        }
    });
});
function showAlert(type, title, message) {
                const statusMessage = $('#status-message');
                $('#alert-title').html(title);
                $('#alert-message').html(message);
                
                statusMessage
                    .removeClass('alert-success alert-danger')
                    .addClass(`alert-${type}`)
                    .css('display', 'block')
                    .addClass('show');
                
                // Auto hide after 5 seconds if not manually closed
                setTimeout(() => {
                    if (statusMessage.hasClass('show')) {
                        statusMessage.removeClass('show');
                        setTimeout(() => {
                            statusMessage.hide();
                        }, 500);
                    }
                }, 5000);
            }
            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => {
                    alert.hide();
                }, 500);
            });
</script>