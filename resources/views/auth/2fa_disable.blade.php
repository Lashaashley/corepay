<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Core Pay - Disable Two-Factor Authentication</title>

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
    /* Reuse styles from 2fa_verify */
    .bg-soft-primary { background-color: #cfe2ff; }
    .bg-soft-danger { background-color: #f8d7da; }
    .bg-soft-warning { background-color: #fff3cd; }
    .alert { transition: all 0.3s ease; }
    @media (max-width: 576px) {
        .container { padding: 10px; }
        .card-body { padding: 1.5rem; }
    }
    .action-buttons {
            padding: 1px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
.btn-warning {
    background: linear-gradient(135deg, #dc3545, #b02a37);
    color: white;
}
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
</style>
 <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert">
    <strong id="alert-title"></strong> <span id="alert-message"></span>
    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-danger text-white py-4 border-0">
                    <div class="text-center">
                        <i class="fas fa-shield-alt fa-3x mb-3"></i>
                        <h3 class="fw-bold mb-0">Disable 2FA Auth</h3>
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

                    <div class="alert alert-warning d-flex align-items-center mb-2" role="alert">
                        <i class="fas fa-exclamation-triangle me-3 fa-lg"></i>
                        <div>
                            <strong>Warning!</strong> Disabling 2FA will make your account less secure.
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <div class="bg-light p-3 rounded-3 d-inline-block">
                            <i class="fas fa-mobile-alt fa-4x text-danger"></i>
                        </div>
                    </div>

                    <p class="text-center mb-4">Please enter the 6-digit code from your authenticator app to confirm disabling 2FA.</p>

                    <form method="POST" action="{{ route('2fa.disable') }}" id="disableForm">
                        @csrf
                        
                        <div class="mb-2">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-key me-2 text-danger"></i>Authentication Code
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="text"
                                       name="otp"
                                       id="otp"
                                       class="form-control border-start-0 ps-0 text-center"
                                       placeholder="• • • • • •"
                                       maxlength="6"
                                       inputmode="numeric"
                                       pattern="[0-9]*"
                                       required
                                       autofocus>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-enhanced btn-warning btn-lg py-3 fw-semibold" id="submitBtn">
                                <i class="fas fa-shield-alt"></i>Disable 2FA
                            </button>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-lg py-2">
                                <i class="fas fa-arrow-left me-2"></i>Back to Profile
                            </a>
                        </div>
                    </form>
                </div>

                
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    const submitBtn = document.getElementById('submitBtn');
    
    otpInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    document.getElementById('disableForm').addEventListener('submit', function(e) {
        if (otpInput.value.length !== 6) {
            e.preventDefault();
           showAlert('danger', 'Error!', 'Please enter a valid 6-digit code.');
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