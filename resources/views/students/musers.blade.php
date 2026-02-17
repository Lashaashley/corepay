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
   
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card-box mb-30">
                
                
                <div class="pb-20 px-20">
                    <table id="agents-table" class="data-table table stripe hover nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th class="table-plus">Full Name</th>
                                <th>User ID</th>
                                <th>Email</th>
                                <th>Password Exp</th>
                                <th>Payroll</th>
                                <th>Approver</th>
                                
                                <th class="datatable-nosort">Option</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="edituserModal" tabindex="-1" role="dialog" aria-labelledby="edituserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="edituserModalLabel">Edit User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form name="edituserForm" id="edituserForm" method="post">
                <div class="modal-body">
                    <input type="hidden" name="form_type" value="edit_user">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    
                    <!-- User Information Section -->
                    <h6 class="mb-3">User Information</h6>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="eusername">User Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eusername" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_userId">User ID</label>
                                <input type="text" class="form-control" id="edit_userId" name="userId" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                 <label for="email">Email <span class="text-danger">*</span></label>
                                 <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                     </div>
                    
                    <div class="form-group">
                        <label for="profile_photo">Profile Photo</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="profilepic" name="profilepic" accept="image/*">
                            <label class="custom-file-label" for="profilepic">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Max size: 2MB (jpeg, png, jpg, gif)</small>
                        <div id="current-photo-preview" class="mt-2" style="display:none;">
                            <img id="current-photo" src="" alt="Current photo" class="img-thumbnail" style="max-width: 100px;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Allowed Payrolls</label>
                        <div id="payroll-checkboxes">
                            <!-- Payroll checkboxes will be loaded here dynamically -->
                        </div>
                    </div>
                  
            <div class="form-group">
                <label>

                </label>
                <div class="form-check form-check-inline">
                    <input type="checkbox" id="approvelvl" name="approvelvl" value="YES" class="form-check-input">
                    <label for="approvelvl" class="form-check-label">Is Approver</label>
                </div>
            </div>
    
                    <hr class="my-4">
                    
                    <!-- Password Reset Section -->
                    <h6 class="mb-3">Reset Password <small class="text-muted">(Optional)</small></h6>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="enable_password_reset">
                            <label class="custom-control-label" for="enable_password_reset">
                                Change user password
                            </label>
                        </div>
                    </div>
                    
                    <div id="password-reset-section" style="display: none;">
                        <div class="form-group">
                            <label for="newpass">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="newpass" name="newpass" 
                                       minlength="8" 
                                       data-toggle="tooltip" 
                                       data-placement="top" 
                                       data-trigger="focus"
                                       title="Password must be at least 8 characters and match 3 of 4 rules: uppercase, lowercase, numbers, symbols">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="password-strength" class="mt-2"></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="newpass">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="newpass_confirmation" name="newpass_confirmation">
                            <div id="password-match-message" class="mt-1"></div>
                        </div>
                        
                        <div class="form-group">
                            <button type="button" class="btn btn-enhanced btn-draft" id="generate-password">
                                <i class="fa fa-key"></i> Generate Password
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-enhanced btn-finalize" id="save-user-btn">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Terminate Modal -->
     

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        const amanage = '{{ route("musers.data") }}';
         window.APP_URL = "{{ url('/') }}";
    window.STORAGE_URL = "{{ asset('storage') }}";
    window.UPLOADS_URL = "{{ asset('uploads') }}";

    const getpayroll = '{{ route("getPayroll.types") }}';
    const getuser = '{{ route("get.user", ":id") }}';
    const updateuser = '{{ route("update.user", ":id") }}';
    </script>
    <script src="{{ asset('js/musers.js') }}"></script>
    
    <script> 
     

// Show message function

    </script>
    
   
</x-custom-admin-layout>