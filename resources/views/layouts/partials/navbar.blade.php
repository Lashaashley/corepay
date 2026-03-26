<style nonce="{{ $cspNonce }}">
    .notification-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .payroll-types {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    background-color: #f0f0f0;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 0.9em;
    max-width: 400px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.header-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
 
/* User dropdown trigger */
.header-right .user-info-dropdown .dropdown > a {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 5px 10px 5px 5px;
    border-radius: 100px;
    border: 1.5px solid #e5e7eb;
    background: #fafafa;
    text-decoration: none;
    color: #0d1117;
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    font-weight: 500;
    transition: background .18s, border-color .18s, box-shadow .18s;
    cursor: pointer;
}
 
.header-right .user-info-dropdown .dropdown > a:hover {
    background: #f3f4f8;
    border-color: #d1d5db;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}
 
/* Avatar */
.header-right .user-icon {
    display: flex;
    align-items: center;
    flex-shrink: 0;
}
 
.header-right .user-icon img {
    width: 50px; height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 1.5px solid #e5e7eb;
    display: block;
}
 
/* Name */
.header-right .user-name {
    font-size: 13px;
    font-weight: 500;
    color: #374151;
    max-width: 140px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
 
/* Caret */
.header-right .user-info-dropdown .dropdown > a::after {
    font-family: 'Material Icons';
    content: '\e5cf';     /* expand_more */
    font-size: 16px;
    color: #9ca3af;
    line-height: 1;
    /* Override Bootstrap caret */
    border: none;
    vertical-align: middle;
    flex-shrink: 0;
}
 
/* Hide Bootstrap caret */
.header-right .dropdown-toggle::after {
    display: none !important;
}
 
/* Restore ours */
.header-right .user-info-dropdown .dropdown > a::after {
    display: inline-block !important;
}
 
/* ── Dropdown menu ─────────────────────────────────────── */
.header-right .dropdown-menu {
    border: 1.5px solid #e5e7eb !important;
    border-radius: 14px !important;
    box-shadow: 0 8px 24px rgba(0,0,0,.1) !important;
    padding: 6px !important;
    min-width: 180px;
    margin-top: 8px !important;
    font-family: 'DM Sans', sans-serif;
    overflow: hidden;
}
 
.header-right .dropdown-menu .dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px !important;
    border-radius: 9px !important;
    font-size: 13px;
    color: #374151;
    font-weight: 500;
    transition: background .15s, color .15s;
}
 
.header-right .dropdown-menu .dropdown-item i,
.header-right .dropdown-menu .dropdown-item .material-icons {
    font-size: 15px;
    color: #9ca3af;
    width: 18px;
    text-align: center;
    flex-shrink: 0;
    transition: color .15s;
}
 
.header-right .dropdown-menu .dropdown-item:hover {
    background: #f3f4f8 !important;
    color: #0d1117 !important;
}
 
.header-right .dropdown-menu .dropdown-item:hover i,
.header-right .dropdown-menu .dropdown-item:hover .material-icons {
    color: #1a56db;
}
 
/* Logout item — subtle red tint on hover */
.header-right .dropdown-menu .dropdown-item.logout-item:hover {
    background: #fef2f2 !important;
    color: #dc2626 !important;
}
 
.header-right .dropdown-menu .dropdown-item.logout-item:hover i {
    color: #dc2626;
}
 
/* Divider */
.header-right .dropdown-menu .dropdown-divider {
    margin: 4px 8px !important;
    border-color: #e5e7eb !important;
}
 
/* User info header inside dropdown */
.dropdown-user-info {
    padding: 10px 12px 8px;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 4px;
}
 
.dropdown-user-info .dui-name {
    font-size: 13px;
    font-weight: 600;
    color: #0d1117;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
 
.dropdown-user-info .dui-role {
    font-size: 11.5px;
    color: #9ca3af;
    margin-top: 1px;
}
 
/* ── Responsive ────────────────────────────────────────── */
@media (max-width: 640px) {
    .navbar-centre { display: none; }
    .header-right .user-name { display: none; }
    .header { padding: 0 14px; }
}
</style>

<div class="header">
    <div class="header-left">
        <div class="menu-icon dw dw-menu"></div>
        <div class="search-toggle-icon dw dw-search2" data-toggle="header_search"></div>
    </div>
    <div class="payroll-types d-flex align-items-center justify-content-center flex-grow-1">
        @if(isset($hasPayrollAccess) && $hasPayrollAccess)
            <span id="current-payroll" 
                  class="font-weight-bold text-success" 
                  title="{{ $payrollTypesDisplay }}"
                  data-payroll-ids="{{ json_encode($payrollIds ?? []) }}">
                <i class="fa fa-check-circle"></i> Accessed Payroll: {{ $payrollTypesDisplay }}
            </span>
        @else
            <span id="current-payroll" 
                  class="font-weight-bold text-warning">
                <i class="fa fa-exclamation-triangle"></i> {{ $payrollTypesDisplay ?? 'No payroll access' }}
            </span>
        @endif
    </div>
    

    <div class="header-right">
 
        @if(Auth::check())
 
        @php
            $photoPath = Auth::user()->profile_photo;
            $photoUrl  = $photoPath && file_exists(public_path('storage/' . $photoPath))
                       ? asset('storage/' . $photoPath)
                       : asset('images/NO-IMAGE-AVAILABLE.jpg');
        @endphp
 
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="{{ $photoUrl }}" alt="{{ Auth::user()->name }}">
                    </span>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </a>
 
                <div class="dropdown-menu dropdown-menu-right">
 
                    {{-- User info header --}}
                    <div class="dropdown-user-info">
                        <div class="dui-name">{{ Auth::user()->name }}</div>
                        <div class="dui-role">{{ Auth::user()->email }}</div>
                    </div>
 
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="dw dw-user1"></i> Profile
                    </a>
 
                    <div class="dropdown-divider"></div>
 
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
                        @csrf
                        <a href="{{ route('logout') }}"
                           class="dropdown-item logout-item"
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="dw dw-logout"></i> Log Out
                        </a>
                    </form>
 
                </div>
            </div>
        </div>
 
        @else
            <script nonce="{{ $cspNonce }}">window.location.href = "{{ route('login') }}";</script>
        @endif
 
    </div>
</div>
