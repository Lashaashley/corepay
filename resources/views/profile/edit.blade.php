<x-custom-admin-layout>
    <style>
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
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-draft {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1);
            color: white;
        }
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #ffc107, #ff8c00);
            color: white;
        }  
        </style>
        <div class="row">
        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
            <div class="card-box pd-30 pt-10 height-100-p">
                <div class="profile-photo">
            <a href="modal" data-toggle="modal" data-target="#modal" class="edit-avatar"><i class="dw dw-user1"></i></a>
            <img src="{{ asset('storage/' . Auth::user()->profile_photo) ?? asset('images/NO-IMAGE-AVAILABLE.jpg') }}" alt="{{ Auth::user()->name }}" class="avatar-photo">
            <form method="POST" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="weight-500 col-md-12 pd-5">
                                <div class="form-group">
                                    <div class="custom-file">
                                        <input name="profile_photo" id="file" type="file" class="custom-file-input" accept="image/*" onchange="validateImage('file')">
                                        <label class="custom-file-label" for="file" id="selector">Choose file</label>
                                    </div>
                                    @error('profile_photo')
                                    <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="profile-info">
                <h5 class="mb-20 h5 text-blue">{{ __('Profile Info') }}</h5>
                <ul>
                    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')
                        <li>
                            <span>Name:</span>
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </li>
                        <li>
                            <span>Email:</span>
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div>
                                <p class="text-sm mt-2 text-gray-800">
                                    {{ __('Your email address is unverified.') }}
                                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('Click here to re-send the verification email.') }}
                                    </button>
                                </p>
                                @if (session('status') === 'verification-link-sent')
                                <p class="mt-2 font-medium text-sm text-green-600">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                                @endif
                            </div>
                            @endif
                        </li>
                        <div class="flex items-center gap-4">
                            <button class="btn btn-primary" data-toggle="modal">{{ __('Save') }}</button>
                            @if (session('status') === 'profile-updated')
                            <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600"
                            >{{ __('Saved.') }}</p>
                            @endif
                        </div>
                    </form>
                    {{-- In profile.edit view, as its own security card --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <div style="width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;
                     background:{{ Auth::user()->google2fa_secret ? '#e8f5e9' : '#fff3e0' }};">
                    <i class="fas fa-shield-alt" style="color:{{ Auth::user()->google2fa_secret ? '#28a745' : '#f57c00' }};font-size:1.1rem;"></i>
                </div>
                <div>
                    <p class="fw-600 mb-0">Two-Factor Authentication</p>
                    @if(Auth::user()->google2fa_secret)
                        <span class="small text-success"><i class="fas fa-check-circle me-1"></i>Active — your account is secured</span>
                    @else
                        <span class="small text-warning"><i class="fas fa-exclamation-circle me-1"></i>Not enabled — we recommend turning this on</span>
                    @endif
                </div>
            </div>

            @if(Auth::user()->google2fa_secret)
                <a href="{{ route('2fa.disable.form') }}" class="btn btn-sm btn-outline-danger" style="border-radius:8px;">
                    Disable
                </a>
            @else
                <a href="{{ route('2fa.setup') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#667eea,#764ba2);border:none;border-radius:8px;">
                    Enable
                </a>
            @endif
        </div>
    </div>
</div>
                </ul>
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            </div>

            </div>
        </div>
        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
            <div class="card-box pd-30 pt-10 height-100-p">
                <div class="profile-tab height-100-p">
			<div class="tab height-100-p">
				<div class="tab-content">
					<div class="tab-pane fade show active" id="timeline" role="tabpanel">
						<div class="pd-20">
							<div class="profile-timeline">
								<form name="changepassf" id="changepassf" method="post">
									@csrf

                                    <x-text-input id="userid" name="userid" type="text" class="mt-1 block w-full" :value="old('name', $user->id)" required autofocus autocomplete="userid" hidden />
                           
									
									<div class="profile-edit-list row">
										<div class="col-md-12"><h4 class="text-blue h5 mb-20">{{ __('Update Password') }}</h4></div>
										<div class="weight-500 col-md-6">
											<div class="form-group">
												<label>Current Password</label>
												<input id="current_password" name="current_password" type="password" class="form-control form-control-lg" autocomplete="current-password">
                                                <small class="text-danger" id="current_password-error"></small>
												<x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
											</div>
										</div>
										<div class="form-group">
											<label for="newpass">New Password <span class="text-danger">*</span></label>
											<div class="input-group">
												<input type="password" class="form-control" id="newpass" name="newpass" minlength="8" data-toggle="tooltip" data-placement="top" data-trigger="focus"title="Password must be at least 8 characters and match 3 of 4 rules: uppercase, lowercase, numbers, symbols">
												<div class="input-group-append">
													<button class="btn btn-outline-secondary" type="button" id="togglePassword">
														<i class="fa fa-eye"></i>
													</button>
												</div>
											</div>
											<div id="password-strength" class="mt-2"></div>
										</div>
										<div class="form-group">
											<label for="newpass_confirmation">Confirm Password <span class="text-danger">*</span></label>
											<input type="password" class="form-control" id="newpass_confirmation" name="newpass_confirmation">
											<div id="password-match-message" class="mt-1"></div>
										</div>
										<div class="form-group">
											<button type="button" class="btn btn-enhanced btn-draft" id="generate-password">
												<i class="fa fa-key"></i> Generate Password
											</button>
                                            <button type="submit" class="btn btn-enhanced btn-finalize" id="changepass">
                                                <i class="fas fa-shield-alt"></i> Change Password
                                            </button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
            </div>
        </div>
        </div>
    
    <script type="text/javascript">
		 function validateImage(id) {
		    var formData = new FormData();
		    var file = document.getElementById(id).files[0];
		    formData.append("Filedata", file);
		    var t = file.type.split('/').pop().toLowerCase();
		    if (t != "jpeg" && t != "jpg" && t != "png") {
		        alert('Please select a valid image file');
		        document.getElementById(id).value = '';
		        return false;
		    }
		    if (file.size > 1050000) {
		        alert('Max Upload size is 1MB only');
		        document.getElementById(id).value = '';
		        return false;
		    }

		    return true;
		}

	</script>
    <script>
	$(document).ready(function() {

    $('#changepassf').on('submit', function (e) {
        e.preventDefault();
        
            const password = $('#newpass').val();
            const confirmation = $('#newpass_confirmation').val();
            const current = $('#current_password').val();
            
            if (password !== confirmation) {
                showMessage('Passwords do not match', 'danger');
                return false;
            }
           if (!current) {
    showMessage('Current password is required', 'danger');
    return false;
}

            
            if (!validatePassword(password)) {
                showMessage('Password does not meet requirements', 'danger');
                return false;
            }
        
        
        const userId = $('#userid').val();
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        
        
        
        $('#changepass').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
             url: '{{ route("change.pass", ["id" => "__id__"]) }}'.replace('__id__', userId),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.status === 'success') {
                    showMessage(response.message, 'success');
                    
                    $('#changepassf')[0].reset();
                    
                }
            },
            error: function(xhr) {

    $('#changepass').prop('disabled', false).html('<i class="fas fa-shield-alt"></i> Change Password');

    // Clear previous errors
    $('#current_password-error').html('');
    $('#newpass-error').html('');
    $('#newpass_confirmation-error').html('');

    if (xhr.status === 422) {
        let errors = xhr.responseJSON.errors;

        // Show per-field errors
        if (errors.current_password) {
            $('#current_password-error').html(errors.current_password[0]);
        }

        if (errors.newpass) {
            $('#newpass-error').html(errors.newpass[0]);
        }

        if (errors.newpass_confirmation) {
            $('#newpass_confirmation-error').html(errors.newpass_confirmation[0]);
        }

        // Also show general message (optional)
        let msg = '';
        $.each(errors, function(key, value) {
            msg += value[0] + "<br>";
        });

        showMessage(msg, 'danger');

    } else {
        showMessage('Something went wrong!', 'danger');
    }
}

,
            complete: function() {
                $('#changepass').prop('disabled', false).html('<i class="fa fa-save"></i> Save Changes');
            }
        });
    });

	$('#togglePassword').click(function() {
        const passwordField = $('#newpass');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Generate strong password
    $('#generate-password').click(function() {
        const password = generateStrongPassword();
        $('#newpass, #newpass_confirmation').val(password).attr('type', 'text');
        checkPasswordStrength(password);
        showMessage('Password generated and copied to both fields', 'success');
    });
    
    // Check password strength on input
    $('#newpass').on('input', function() {
        const password = $(this).val();
        if (password.length > 0) {
            checkPasswordStrength(password);
        } else {
            $('#password-strength').html('');
        }
    });
    
    // Check password match
    $('#newpass_confirmation').on('input', function() {
        const password = $('#newpass').val();
        const confirmation = $(this).val();
        
        if (confirmation.length > 0) {
            if (password === confirmation) {
                $('#password-match-message').html('<small class="text-success"><i class="fa fa-check"></i> Passwords match</small>');
            } else {
                $('#password-match-message').html('<small class="text-danger"><i class="fa fa-times"></i> Passwords do not match</small>');
            }
        } else {
            $('#password-match-message').html('');
        }
    });

	});

	function generateStrongPassword() {
    const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const lowercase = 'abcdefghijklmnopqrstuvwxyz';
    const numbers = '0123456789';
    const symbols = '!@#$%^&*_-+=';
    
    const allChars = uppercase + lowercase + numbers + symbols;
    let password = '';
    
    // Ensure at least one of each type
    password += uppercase[Math.floor(Math.random() * uppercase.length)];
    password += lowercase[Math.floor(Math.random() * lowercase.length)];
    password += numbers[Math.floor(Math.random() * numbers.length)];
    password += symbols[Math.floor(Math.random() * symbols.length)];
    
    // Fill the rest randomly (total length 12-16 characters)
    const length = Math.floor(Math.random() * 5) + 12;
    for (let i = password.length; i < length; i++) {
        password += allChars[Math.floor(Math.random() * allChars.length)];
    }
    
    // Shuffle the password
    return password.split('').sort(() => Math.random() - 0.5).join('');
}

// Validate password
function validatePassword(password) {
    if (password.length < 8) return false;
    
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSymbol = /[~!@#$%^*_\-+=`|(){}[\]:;"'<>,.?/]/.test(password);
    
    const rulesMatched = [hasUppercase, hasLowercase, hasNumber, hasSymbol].filter(Boolean).length;
    
    return rulesMatched >= 3;
}

// Check password strength
function checkPasswordStrength(password) {
    const hasUppercase = /[A-Z]/.test(password);
    const hasLowercase = /[a-z]/.test(password);
    const hasNumber = /[0-9]/.test(password);
    const hasSymbol = /[~!@#$%^*_\-+=`|(){}[\]:;"'<>,.?/]/.test(password);
    
    const rulesMatched = [hasUppercase, hasLowercase, hasNumber, hasSymbol].filter(Boolean).length;
    const length = password.length;
    
    let strength = '';
    let strengthClass = '';
    let requirements = [];
    
    if (length < 8) {
        strength = 'Too Short';
        strengthClass = 'text-danger';
    } else if (rulesMatched < 3) {
        strength = 'Weak';
        strengthClass = 'text-warning';
    } else if (rulesMatched === 3) {
        strength = 'Good';
        strengthClass = 'text-info';
    } else {
        strength = 'Strong';
        strengthClass = 'text-success';
    }
    
    requirements.push(`<small>${hasUppercase ? '✓' : '✗'} Uppercase</small>`);
    requirements.push(`<small>${hasLowercase ? '✓' : '✗'} Lowercase</small>`);
    requirements.push(`<small>${hasNumber ? '✓' : '✗'} Number</small>`);
    requirements.push(`<small>${hasSymbol ? '✓' : '✗'} Symbol</small>`);
    requirements.push(`<small>${length >= 8 ? '✓' : '✗'} 8+ characters</small>`);
    
    $('#password-strength').html(`
        <div class="${strengthClass}">
            <strong>Strength: ${strength}</strong><br>
            ${requirements.join(' | ')}
        </div>
    `);
}

            // Show message function
            function showMessage(message, type) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                $('#status-message')
                    .removeClass('alert-success alert-danger')
                    .addClass(alertClass)
                    .find('#alert-message').text(message);
                $('#status-message').fadeIn().delay(3000).fadeOut();
            }
       
	</script>
</x-custom-admin-layout>