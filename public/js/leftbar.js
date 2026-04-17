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