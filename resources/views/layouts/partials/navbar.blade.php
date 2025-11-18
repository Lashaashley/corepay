<style>
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
        <div class="notification-icon">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <img src="{{ asset('images/bell.png') }}" style="width: 25px; height: 25px;" alt="Notifications" />
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="#"><i class="dw dw-check"></i> New Task Assigned</a>
                </div>
            </div>
        </div>
        
        <div class="dashboard-setting user-notification">
            <div class="dropdown">
                <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
                    <i class="dw dw-settings2"></i>
                </a>
            </div>
        </div>
        
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <span class="user-icon">
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo) ?? asset('images/NO-IMAGE-AVAILABLE.jpg') }}" alt="{{ Auth::user()->name }}">
                    </span>
                    <span class="user-name">{{ Auth::user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                        <i class="dw dw-user1"></i> Profile
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <a href="{{ route('logout') }}" 
                           class="dropdown-item" 
                           onclick="event.preventDefault(); this.closest('form').submit();">
                            <i class="dw dw-logout"></i> Log Out
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
   
</script>
