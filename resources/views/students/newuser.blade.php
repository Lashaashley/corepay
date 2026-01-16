<x-custom-admin-layout>
    <style>

@keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
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
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            transform: translateX(400px);
            transition: all 0.5s ease;
        }
        
        .custom-alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            animation: successPulse 1s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
   
    </style>
    <div class="mobile-menu-overlay"></div>
   
	<h1 class="header-container">User Management</h1>
		<div class="pd-ltr-20 xs-pd-20-10">
        
			<div class="min-height-200px" style="margin-top: -20px;">

					<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 mb-30">
    <div class="card-box pd-30 pt-10 height-100-p">
       

        <h2 class="mb-30 h4">User Credentials</h2>
        <form name="createuser" id="createuser" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label>Name <span class="text-danger">*</span></label>
                <input name="name" id="name" type="text" class="form-control" required autocomplete="off">
                <small class="text-danger" id="name-error"></small>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                <label>Email <span class="text-danger">*</span></label>
                <input name="email" id="email" type="email" class="form-control" required autocomplete="off">
                <small class="text-danger" id="email-error"></small>
            </div>
        </div>
        <div class="col-md-3">
    <div class="form-group">
        <label>Password <span class="text-danger">*</span></label>
        <div class="input-group">
            <input id="newPassword" name="newpass" type="password" 
                   class="form-control" required autocomplete="off" 
                   minlength="8"
                   data-toggle="tooltip" 
                   data-placement="top"
                   data-trigger="focus"
                   title="Password must be at least 8 characters and match 3 of 4 rules: uppercase, lowercase, numbers, symbols (~!@#$%^*_-+=`|(){}[]:;&quot;<>,.?/&)">
            <div class="input-group-append">
                <span class="input-group-text" style="cursor: pointer;" 
                      onclick="togglePasswordVisibility('newPassword', 'newPasswordIcon')">
                    <i id="newPasswordIcon" class="fas fa-eye"></i>
                </span>
            </div>
        </div>
        <small class="text-danger" id="newpass-error"></small>
    </div>
</div>

<div class="col-md-3">
    <div class="form-group">
        <label>Confirm Password <span class="text-danger">*</span></label>
        <div class="input-group">
            <input id="confirmPassword" name="confirm" type="password" 
                   class="form-control" required autocomplete="off" 
                   minlength="8"
                   data-toggle="tooltip" 
                   data-placement="top"
                   data-trigger="focus"
                   title="Password must be at least 8 characters and match 3 of 4 rules: uppercase, lowercase, numbers, symbols (~!@#$%^*_-+=`|(){}[]:;&quot;<>,.?/&)">
            <div class="input-group-append">
                <span class="input-group-text" style="cursor: pointer;" 
                      onclick="togglePasswordVisibility('confirmPassword', 'confirmPasswordIcon')">
                    <i id="confirmPasswordIcon" class="fas fa-eye"></i>
                </span>
            </div>
        </div>
        <small class="text-danger" id="confirm-error"></small>
    </div>
</div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Allowed Payrolls</label>
                <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                    @if($payrollTypes->count() > 0)
                        @foreach($payrollTypes as $payrollType)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       name="allowedPayroll[]" 
                                       id="payroll{{ $payrollType->ID }}" 
                                       value="{{ $payrollType->ID }}">
                                <label class="form-check-label" for="payroll{{ $payrollType->ID }}">
                                    {{ $payrollType->pname }}
                                </label>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No payroll types found.</p>
                    @endif
                </div>
                <small class="text-danger" id="allowedPayroll-error"></small>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
                <label>Profile Photo</label>
                <input type="file" class="form-control" id="profilepic" 
                       name="profilepic" accept="image/*">
                <small class="form-text text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                <small class="text-danger" id="profilepic-error"></small>
                
                <!-- Preview -->
                <div id="imagePreview" style="margin-top: 10px; display: none;">
                    <img id="previewImg" src="" alt="Preview" 
                         style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-enhanced btn-draft">
                <i class="fas fa-save"></i> Create User
            </button>
            <button type="reset" class="btn btn-secondary">
                <i class="fas fa-undo"></i> Reset
            </button>
        </div>
    </div>
</form>

<!-- Alert Container -->
<div id="alertContainer" style="margin-top: 20px;"></div>
    </div>
</div>
						
					</div>

				</div>

				
		</div>
    
    

    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script>
        const amanage = '{{ route("newuser.store") }}';
       
    </script>
    <script src="{{ asset('js/nuser.js') }}"></script>
    
     
   
</x-custom-admin-layout>