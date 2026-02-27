<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Core Pay - Setup Two-Factor Authentication</title>

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
    /* Reuse styles from 2fa_verify */
    .bg-soft-primary { background-color: #cfe2ff; }
    .bg-soft-success { background-color: #d1e7dd; }
    .bg-soft-danger { background-color: #f8d7da; }
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
    .step.active { background: #0d6efd; color: white; }
    .step.completed { background: #198754; color: white; }
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
    .step.completed:not(:last-child)::after { background: #198754; }
    .alert { transition: all 0.3s ease; }
    @media (max-width: 576px) {
        .container { padding: 10px; }
        .card-body { padding: 1.5rem; }
        .step:not(:last-child)::after { width: 2rem; }
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
<div class="container mt-3">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-success text-white py-4 border-0">
                    <div class="text-center">
                        <i class="fas fa-shield-alt fa-3x mb-3"></i>
                        <h3 class="fw-bold mb-0">Set Up Two-Factor Authentication</h3>
                        <p class="mb-0 mt-2 text-white-50">Enhance your account security</p>
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

                    <!-- Step 1: Scan QR Code -->
                    <div class="text-center mb-4">
                        <h5 class="fw-semibold mb-3">Step 1: Scan QR Code</h5>
                        <div class="qr-code-container mb-3">
                            <img src="data:image/svg+xml;base64,{{ $qrCodeSvg }}" alt="QR Code" style="width:200px;height:200px;">
                        </div>
                        
                        <div class="alert alert-info d-flex align-items-center text-start" role="alert">
                            <i class="fas fa-info-circle me-3 fa-lg"></i>
                            <div>Scan this QR code with Google Authenticator, Authy, or any TOTP app</div>
                        </div>
                    </div>

                    <!-- Step 2: Manual Entry (Optional) -->
                    <div class="mb-4">
                        <h5 class="fw-semibold mb-3">
                            <i class="fas fa-keyboard me-2 text-success"></i>
                            Step 2: Manual Entry (Optional)
                        </h5>
                        <p class="small text-muted mb-2">If you can't scan the QR code, enter this secret key manually:</p>
                        <div class="secret-key text-center mb-2">
                            <span id="secretKey">{{ $secret }}</span>
                        </div>
                        <button class="btn btn-sm btn-outline-success w-100" onclick="copySecretKey()">
                            <i class="fas fa-copy me-1"></i> Copy Secret Key
                        </button>
                    </div>

                    <!-- Step 3: Verify -->
                    <h5 class="fw-semibold mb-3">
                        <i class="fas fa-check-circle me-2 text-success"></i>
                        Step 3: Verify Setup
                    </h5>

                    <form method="POST" id="setupForm">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-key me-2 text-success"></i>Enter 6-digit Code
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
                                       <span id="otp-error" class="text-danger small"></span>
                            </div>
                            
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-enhanced btn-finalize btn-lg py-3 fw-semibold" id="submitBtn">
                                <i class="fas fa-check-circle me-2"></i>Enable Two-Factor Authentication
                            </button>
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-lg py-2">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>

                
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
function copySecretKey() {
    const secretKey = document.getElementById('secretKey').innerText;
    navigator.clipboard.writeText(secretKey).then(() => {
        showAlert('success', 'Success!','Secret key copied to clipboard!');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    const submitBtn = document.getElementById('submitBtn');
    
    otpInput.addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

   document.getElementById('setupForm').addEventListener('submit', function(e) {
    e.preventDefault();
    $('.text-danger').html('');

    let formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Enabling...').prop('disabled', true);

    $.ajax({
        url: "{{ route('2fa.enable') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            // Redirect after short delay so user sees the success message
            setTimeout(function() {
                window.location.href = response.redirect;
            }, 1500);
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let json = xhr.responseJSON;

                // Laravel validation errors (errors object)
                if (json.errors) {
                    $.each(json.errors, function(key, value) {
                        $('#' + key + '-error').html(value[0]);
                    });
                    showAlert('danger', 'Error!', 'Please check the form for errors.');
                }

                // Custom message errors (session expired, invalid OTP)
                if (json.message) {
                    showAlert('danger', 'Error!', json.message);
                }
            } else {
                showAlert('danger', 'Error!', 'Something went wrong. Please try again.');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
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