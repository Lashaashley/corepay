<x-custom-admin-layout>
@vite(['resources/css/pages/musers.css']) 


<div class="users-page">

    <div class="page-header">
        <div class="page-heading">
            <h1>Manage Users</h1>
            <p>View and manage all system user accounts.</p>
        </div>
    </div>

    <div class="toast-wrap" id="toastWrap"></div>

    <!-- Table card -->
    <div class="table-card">

        <div class="table-toolbar">
            <div class="toolbar-left">
                <div class="toolbar-icon"><span class="material-icons">manage_accounts</span></div>
                <div>
                    <div class="toolbar-title">All Users</div>
                    <div class="toolbar-subtitle" id="recordCount">Loading…</div>
                </div>
            </div>
            <div class="toolbar-right">
                <div class="search-box">
                    <span class="material-icons">search</span>
                    <input type="text" id="dt-search" placeholder="Search users…">
                </div>
                <select id="dt-length" class="page-length-select">
                    <option value="10">10 / page</option>
                    <option value="25" selected>25 / page</option>
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                </select>
            </div>
        </div>

        <div class="table-wrap">
            <table id="users-table" class="stripe hover nowrap" >
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>User ID</th>
                        <th>Email</th>
                        <th>Password Exp</th>
                        <th>Payroll</th>
                        <th>Approver</th>
                        <th class="datatable-nosort">Option</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

<!-- Edit User Modal — pure custom, no Bootstrap dependency -->
<div class="modal-backdrop-custom" id="edituserModalBackdrop">
    {{-- NOTE: id="edituserModal" kept here so musers.js $(...).modal() shim works --}}
    <div id="edituserModal">
        <div class="modal-card">

            <div class="modal-header">
                <div class="modal-header-icon"><span class="material-icons">manage_accounts</span></div>
                <span class="modal-header-title">Edit User</span>
                <button class="modal-close-btn" data-dismiss="modal" id="modalCloseBtn">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="modal-body">
                <form name="edituserForm" id="edituserForm" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="form_type" value="edit_user">
                    <input type="hidden" name="user_id" id="edit_user_id">

                    <!-- Account info -->
                    <p class="modal-section-label">Account Information</p>

                    <div class="mgrid">
                        <div class="mfield mc-4">
                            <label>Name <span class="req">*</span></label>
                            <input type="text" id="eusername" name="name" required>
                        </div>
                        <div class="mfield mc-4">
                            <label>User ID</label>
                            <input type="text" id="edit_userId" name="userId" readonly>
                        </div>
                        <div class="mfield mc-4">
                            <label>Email <span class="req">*</span></label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>

                    <!-- Profile photo -->
                    <p class="modal-section-label">Profile Photo</p>

                    <div class="avatar-edit" >
                        <img id="current-photo" src="" alt="Photo"
                             class="avatar-current"
                             onerror="this.style.display='none'">
                        <div>
                            <label class="avatar-upload-label" for="profilepic">
                                <span class="material-icons">upload</span> Choose Photo
                            </label>
                            <input type="file" id="profilepic" class="profilepic" name="profilepic"
                                   accept="image/*"
                                   >
                            <div class="avatar-hint">Max 2 MB · JPG, PNG, GIF</div>
                        </div>
                    </div>

                    <!-- Payroll access -->
                    <p class="modal-section-label">Payroll Access</p>

                    <div id="payroll-checkboxes" class="payroll-chips">
                        {{-- Populated dynamically by musers.js --}}
                    </div>

                    <!-- Approver -->
                    <div class="marginbot">
    <div class="approver-chip">
        <input type="checkbox" id="approvelvl" name="approvelvl" value="YES">
        <label for="approvelvl">
            <span class="material-icons">verified_user</span>
            Is Approver
        </label>
    </div>
    <div class="approver-chip">
        <input type="checkbox" id="mfa" name="mfa" value="ON">
        <label for="mfa">
            <span class="material-icons">shield</span>
            Two Factor Enabled
        </label>
    </div>
    <div class="approver-chip">
        <input type="checkbox" id="activeacc" name="activeacc" value="ACTIVE">
        <label for="activeacc">
            <span class="material-icons">check_circle</span>
            Active Account
        </label>
    </div>
</div>

                    <!-- Password reset -->
                    <p class="modal-section-label passreset">Password Reset <span>(optional)</span></p>

                    <div class="marginbot">
                        <label class="pw-toggle-check">
                            <input type="checkbox" id="enable_password_reset">
                            Change user password
                        </label>
                    </div>

                    <div id="password-reset-section">
                        <div class="mgrid">
                            <div class="mfield mc-6">
                                <label>New Password <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" id="newpass" name="newpass" minlength="8"
                                           placeholder="Min. 8 characters" autocomplete="new-password"
                                          >
                                    <button type="button" class="pw-btn" id="togglePassword"
                                            onclick="toggleEditPw('newpass','editEye1')">
                                        <span class="material-icons" id="editEye1">visibility</span>
                                    </button>
                                    <button type="button" class="pw-gen-btn" id="generate-password"
                                           >
                                        <span class="material-icons">auto_fix_high</span> Generate
                                    </button>
                                </div>
                                <div class="pw-strength-bar"><div class="pw-strength-fill" id="editStrengthFill"></div></div>
                                <span class="pw-strength-label" id="editStrengthLabel"></span>
                            </div>

                            <div class="mfield mc-6">
                                <label>Confirm Password <span class="req">*</span></label>
                                <div class="pw-wrap">
                                    <input type="password" id="newpass_confirmation" name="newpass_confirmation"
                                           placeholder="Re-enter password" autocomplete="new-password"
                                           >
                                    <button type="button" class="pw-btn"
                                            onclick="toggleEditPw('newpass_confirmation','editEye2')">
                                        <span class="material-icons" id="editEye2">visibility</span>
                                    </button>
                                </div>
                                <div class="pw-match" id="password-match-message"></div>
                            </div>
                        </div>
                    </div>

                
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" data-dismiss="modal" id="modalCancelBtn">
                    <span class="material-icons">close</span> Cancel
                </button>
                <button type="submit" class="btn btn-save" id="save-user-btn">
                    <span class="material-icons">save</span> Save Changes
                </button>
            </div>
            </form>

        </div>
    </div>
</div>

<script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>



{{-- musers.js loads here — AFTER the shim so $.fn.modal is already patched --}}
<script src="{{ asset('js/musers.js') }}"></script>


</x-custom-admin-layout>






