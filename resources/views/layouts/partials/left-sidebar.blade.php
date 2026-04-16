{{-- resources/views/layouts/partials/left-sidebar.blade.php --}}
@vite(['resources/css/pages/leftsidebar.css'])

<div class="left-side-bar">

    <div class="brand-logo">
        <a href="{{ route('dashboard') }}">
            <img src="{{ asset('images/schaxist.png') }}" alt="Core Pay" class="dark-logo">
            <img src="{{ asset('images/schaxist.png') }}" alt="Core Pay" class="light-logo">
        </a>
        {{-- Close button with reliable JS handler --}}
        <div class="close-sidebar" id="sidebarCloseBtn" data-toggle="left-sidebar-close">
            <span class="material-icons">close</span>
        </div>
    </div>

    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">

                {{-- Dashboard --}}
                <li class="dropdown">
                    <a href="{{ route('dashboard') }}" class="dropdown-toggle no-arrow">
                        <span class="material-icons">dashboard</span>
                        <span class="mtext">Dashboard</span>
                    </a>
                </li>

                {{-- Dynamic items --}}
                @if(isset($menuItems) && !empty($menuItems))
                    @foreach($menuItems as $item)
                        <li class="{{ !empty($item['children']) ? 'dropdown' : '' }}">
                            <a href="{{ !empty($item['children']) ? '#' : $item['href'] }}"
   data-is-parent="{{ !empty($item['children']) ? 'true' : 'false' }}"
   class="dropdown-toggle {{ empty($item['children']) ? 'no-arrow' : '' }}">

                                @if(!empty($item['icon']))
                                    @if(Str::contains($item['icon'], ['.png','.jpg','.jpeg','.svg']))
                                        <img src="{{ asset($item['icon']) }}"
                                             alt="{{ $item['name'] }}"
                                             class="micon">
                                    @else
                                        <span class="micon {{ $item['icon'] }}"></span>
                                    @endif
                                @else
                                    <span class="micon dw dw-library"></span>
                                @endif

                                <span class="mtext">{{ $item['name'] }}</span>
                            </a>

                            @if(!empty($item['children']))
                                <ul class="submenu">
                                    @foreach($item['children'] as $child)
                                        <li>
                                            <a href="{{ $child['href'] }}">{{ $child['name'] }}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @else
                    <li>
                        <span style="padding:8px 12px;color:rgba(255,255,255,.3);font-size:12px;display:block;">
                            No menu items available
                        </span>
                    </li>
                @endif

            </ul>
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-footer-avatar">
            <span class="material-icons">payments</span>
        </div>
        <div class="sidebar-footer-text">
            <strong>Core Pay</strong>
            Payroll Management
        </div>
    </div>

</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {

    /* ── Close button ─────────────────────────────────────
       DeskApp binds to [data-toggle="left-sidebar-close"].
       We also add a direct listener as a reliable fallback.
    ─────────────────────────────────────────────────────── */
    const closeBtn = document.getElementById('sidebarCloseBtn');
    if (closeBtn) {
        closeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            // DeskApp's body classes for sidebar state
            document.body.classList.remove('sidebar-open', 'open', 'left-sidebar-open');
            // Some DeskApp versions add this class to the sidebar itself
            const sidebar = document.querySelector('.left-side-bar');
            if (sidebar) sidebar.classList.remove('open');
            // Trigger DeskApp's own handler if it's listening on data-toggle
            closeBtn.dispatchEvent(new MouseEvent('click', { bubbles: false }));
        });
    }

    /* ── Active page highlight ──────────────────────────── */
    const current = window.location.pathname.replace(/\/$/, '');

    document.querySelectorAll('.left-side-bar .submenu li a').forEach(function (a) {
        const raw = (a.getAttribute('href') || '').trim();
        if (!raw || raw === '#' || raw.startsWith('javascript')) return;
        let path;
        try { path = raw.startsWith('http') ? new URL(raw).pathname : '/' + raw.replace(/^\//, ''); }
        catch (e) { return; }
        if (path.replace(/\/$/, '') === current) {
            a.classList.add('active');
            a.closest('li')?.classList.add('active');
            const top = a.closest('#accordion-menu > li');
            if (top) {
                top.classList.add('open', 'active');
                const sub = top.querySelector('.submenu');
                if (sub) sub.style.display = 'block';
            }
        }
    });

    document.querySelectorAll('#accordion-menu > li > a.no-arrow').forEach(function (a) {
        const raw = (a.getAttribute('href') || '').trim();
        if (!raw || raw === '#') return;
        let path;
        try { path = raw.startsWith('http') ? new URL(raw).pathname : '/' + raw.replace(/^\//, ''); }
        catch (e) { return; }
        if (path.replace(/\/$/, '') === current) {
            a.closest('li')?.classList.add('active');
        }
    });
    /* ── Accordion dropdown toggle ──────────────────────── */
document.querySelectorAll('#accordion-menu > li.dropdown > a').forEach(function(a) {
    a.addEventListener('click', function(e) {
        e.preventDefault();      // stops the # from hitting the URL bar
        e.stopPropagation();

        const li = this.closest('li.dropdown');
        const sub = li.querySelector('.submenu');
        const isOpen = li.classList.contains('open');

        // Close all other open dropdowns
        document.querySelectorAll('#accordion-menu > li.dropdown.open').forEach(function(openLi) {
            if (openLi !== li) {
                openLi.classList.remove('open', 'active');
                const s = openLi.querySelector('.submenu');
                if (s) s.style.display = 'none';
            }
        });

        // Toggle this one
        if (isOpen) {
            li.classList.remove('open', 'active');
            if (sub) sub.style.display = 'none';
        } else {
            li.classList.add('open', 'active');
            if (sub) sub.style.display = 'block';
        }
    });
});
});
</script>