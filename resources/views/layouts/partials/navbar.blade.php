
@vite(['resources/css/pages/navbar.css'])
<div class="header">
    <div class="header-left">
        <div class="menu-icon material-icons">menu</div>
        <div class="search-toggle-icon material-icons" data-toggle="header_search">search</div>
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
    <!-- Session timeout warning banner -->
<div id="sessionWarning" class="session-warning-banner hidden">
    <span class="material-icons">timer</span>
    <span id="sessionWarningText">Your session expires in 15 seconds. </span>
    <button id="sessionExtendBtn" data-action="extend-session">Stay Logged In</button>
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
                        <span class="material-icons">account_circle</span>
                        Profile
                    </a>
 
                    <div class="dropdown-divider"></div>
 
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0">
    @csrf
    <button type="submit" class="dropdown-item logout-item" style="background: none; border: none; width: 100%; text-align: left;">
        <span class="material-icons">logout</span> Log Out
    </button>
</form>
 
                </div>
            </div>
        </div>
 
        @else
            <script nonce="{{ $cspNonce }}">window.location.href = "{{ route('login') }}";</script>
        @endif
 
    </div>
</div>

<script nonce="{{ $cspNonce }}">
(function () {
    const LIFETIME_MS  = {{ config('session.lifetime') }} * 60 * 1000; // from session.php
    const WARN_BEFORE  = 15 * 1000;  // warn 15 seconds before expiry
    const CHECK_EVERY  = 1000;       // tick every second

    const banner   = document.getElementById('sessionWarning');
    const textEl   = document.getElementById('sessionWarningText');
    const extendBtn= document.getElementById('sessionExtendBtn');

    let sessionStart  = Date.now();
    let warningShown  = false;
    let countdownTick = null;

    function resetTimer() {
        sessionStart = Date.now();
        warningShown = false;
        banner.classList.add('hidden');
        clearInterval(countdownTick);
    }

    function showWarning(secondsLeft) {
        if (!warningShown) {
            warningShown = true;
            banner.classList.remove('hidden');

            // Countdown ticker
            countdownTick = setInterval(function () {
                const elapsed = Date.now() - sessionStart;
                const remaining = Math.max(0, Math.ceil((LIFETIME_MS - elapsed) / 1000));
                textEl.textContent = `Your session expires in ${remaining} second${remaining !== 1 ? 's' : ''}. `;

                if (remaining <= 0) {
                    clearInterval(countdownTick);
                    // Redirect to logout
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).finally(function () {
                        window.location.href = '{{ route("login") }}';
                    });
                }
            }, CHECK_EVERY);
        }
    }

    // Main watcher
    setInterval(function () {
        const elapsed   = Date.now() - sessionStart;
        const remaining = LIFETIME_MS - elapsed;

        if (remaining <= WARN_BEFORE) {
            showWarning(Math.ceil(remaining / 1000));
        }
    }, CHECK_EVERY);

    // Extend session — ping a lightweight route
    extendBtn.addEventListener('click', function () {
        fetch('{{ route("session.ping") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(function (r) {
            if (r.ok) resetTimer();
        });
    });

    // Reset timer on user activity (throttled)
    let activityThrottle = null;
    ['click', 'keydown', 'mousemove', 'scroll'].forEach(function (evt) {
        document.addEventListener(evt, function () {
            if (activityThrottle) return;
            activityThrottle = setTimeout(function () {
                // Only ping server if warning is already showing
                // Otherwise just reset the local timer
                if (warningShown) {
                    fetch('{{ route("session.ping") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json'
                        }
                    }).then(function (r) { if (r.ok) resetTimer(); });
                } else {
                    sessionStart = Date.now();
                }
                activityThrottle = null;
            }, 2000); // throttle activity resets to every 2s
        }, { passive: true });
    });
})();
</script>