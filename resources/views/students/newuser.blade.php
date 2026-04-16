<x-custom-admin-layout>
@vite(['resources/css/pages/newuser.css']) 
 
<div class="user-create-page">
 
    <div class="page-heading">
        <h1>New User</h1>
    </div>
 
    <div class="toast-wrap" id="toastWrap"></div>
 
    <div class="form-card">
 
        {{-- ── Section 1: Account Info ────────────────────────── --}}
        <div class="section-head">
            <div class="section-icon"><span class="material-icons">person</span></div>
            <h2 class="section-title">Account Information</h2>
        </div>
 
        <form name="createuser" id="createuser" method="POST" enctype="multipart/form-data">
        @csrf
 
        <div class="section-body">
            <div class="fgrid">
                <div class="field fc-3">
                    <label>Name <span class="req">*</span></label>
                    <input name="name" id="name" type="text" placeholder="Full name" required autocomplete="off">
                    <span class="field-error" id="name-error"></span>
                </div>
 
                <div class="field fc-4">
                    <label>Email <span class="req">*</span></label>
                    <input name="email" id="email" type="email" placeholder="user@company.com" required autocomplete="off">
                    <span class="field-error" id="email-error"></span>
                </div>
            </div>
        </div>
 
        {{-- ── Section 2: Password ─────────────────────────────── --}}
        <div class="section-head">
            <div class="section-icon"><span class="material-icons">lock</span></div>
            <h2 class="section-title">Password</h2>
        </div>
 
        <div class="section-body">
            <div class="fgrid">
 
                {{-- Password --}}
                <div class="field fc-4">
                    <label>Password <span class="req">*</span></label>
                    <div class="pw-wrap" id="pwWrap1">
                        <input id="newPassword" name="newpass" id="newpass" type="password"
                               placeholder="Min. 8 characters" required autocomplete="new-password"
                               minlength="8" >
                        <button type="button" class="pw-btn" id="pw-btn"
                                title="Show/hide password">
                            <span class="material-icons" id="eyeIcon1">visibility</span>
                        </button>
                        <button type="button" class="pw-gen-btn" id="generatePwBtn"
                                 title="Auto-generate a strong password">
                            <span class="material-icons">auto_fix_high</span>
                            Generate
                        </button>
                    </div>
                    <div class="pw-strength">
                        <div class="pw-strength-bar">
                            <div class="pw-strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="pw-strength-label" id="strengthLabel"></span>
                    </div>
                    <div class="gen-pw-badge" id="genPwBadge">
                        <span class="material-icons">key</span>
                        <code id="genPwText"></code>
                        <button type="button" class="gen-pw-copy" id="gen-pw-copy">
                            <span class="material-icons">content_copy</span> Copy
                        </button>
                    </div>
                    <span class="field-error" id="newpass-error"></span>
                </div>
 
                {{-- Confirm password --}}
                <div class="field fc-4">
                    <label>Confirm Password <span class="req">*</span></label>
                    <div class="pw-wrap" id="pwWrap2">
                        <input id="confirmPassword" name="confirm" type="password"
                               placeholder="Re-enter password" required autocomplete="new-password" minlength="8">
                        <button type="button" class="pw-btn" id="pw-btn2"
                                title="Show/hide password">
                            <span class="material-icons" id="eyeIcon2">visibility</span>
                        </button>
                    </div>
                    <span class="field-error" id="confirm-error"></span>
                </div>
 
                {{-- Rules hint --}}
                <div class="fc-12">
                    <span  class="fontpass">
                        Password must be at least 8 characters and include 3 of 4: uppercase, lowercase, numbers, symbols (~!@#$%^*_-+=|(){}[]:;&lt;&gt;,.?/)
                    </span>
                </div>
 
            </div>
        </div>
 
        {{-- ── Section 3: Access & Profile ────────────────────── --}}
        <div class="section-head">
            <div class="section-icon"><span class="material-icons">admin_panel_settings</span></div>
            <h2 class="section-title">Access &amp; Profile</h2>
        </div>
 
        <div class="section-body">
            <div class="fgrid">
 
                {{-- Allowed payrolls --}}
                <div class="field fc-4">
                    <label>Allowed Payrolls</label>
                    <div class="payroll-chips">
                        @if($payrollTypes->count() > 0)
                            @foreach($payrollTypes as $pt)
                                <div class="payroll-chip">
                                    <input type="checkbox" name="allowedPayroll[]"
                                           id="payroll{{ $pt->ID }}" value="{{ $pt->ID }}">
                                    <label for="payroll{{ $pt->ID }}">{{ $pt->pname }}</label>
                                </div>
                            @endforeach
                        @else
                            <span class= "payfont">No payroll types found.</span>
                        @endif
                    </div>
                    <span class="field-error" id="allowedPayroll-error"></span>
                </div>
 
                {{-- Profile photo --}}
                <div class="field fc-4">
                    <label>Profile Photo</label>
                    <div class="avatar-upload">
                        <div class="avatar-preview" id="avatarPreview" onclick="document.getElementById('profilepic').click()">
                            <span class="material-icons">person</span>
                            <img id="previewImg" src="" alt="Preview">
                        </div>
                        <div class="avatar-upload-info">
                            <label class="upload-trigger" for="profilepic">
                                <span class="material-icons">upload</span> Choose Photo
                            </label>
                            <input type="file" id="profilepic" name="profilepic"
                                   accept="image/*" 
                                   >
                            <div class="hint">JPG, PNG, GIF · Max 2 MB</div>
                            <span class="field-error" id="profilepic-error"></span>
                        </div>
                    </div>
                </div>
 
                {{-- Is approver --}}
                <div class="field fc-4">
                    <label>Role Flags</label>
                    <div class="approver-chip">
                        <input type="checkbox" id="approvelvl" name="approvelvl" value="YES">
                        <label for="approvelvl">
                            <span class="material-icons">verified_user</span>
                            Is Approver
                        </label>
                    </div>
                </div>
 
            </div>
        </div>
 
        {{-- ── Action bar ──────────────────────────────────────── --}}
        <div class="action-bar">
            <button type="reset" class="btn btn-reset" id="btn-reset" >
                <span class="material-icons">restart_alt</span> Reset
            </button>
            <button type="submit" class="btn btn-save">
                <span class="material-icons">person_add</span> Create User
            </button>
        </div>
 
        </form>
 
        <div id="alertContainer"></div>
 
    </div>
</div>
    
    
    <script nonce="{{ $cspNonce }}">
        const amanage = '{{ route("newuser.store") }}';
       
    </script>
    <script src="{{ asset('js/nuser.js') }}"></script>
    
     
   
</x-custom-admin-layout>