document.addEventListener('DOMContentLoaded', function () {
 
    const modal       = document.getElementById('pdfModal');
    const modalTitle  = document.getElementById('pdfModalTitle');
    const modalBody   = document.getElementById('pdfModalBody');
    const loading     = document.getElementById('pdfLoading');
    const errorPanel  = document.getElementById('pdfError');
    const errorMsg    = document.getElementById('pdfErrorMsg');
    const downloadBtn = document.getElementById('downloadPdfBtn');
    const printBtn    = document.getElementById('printPdfBtn');
    const closeBtn    = document.getElementById('pdfModalClose');
 
    let currentPdfUrl = null;
 
    /* ── Open / close helpers ────────────────────────── */
    function openModal(title) {
        modalTitle.textContent = title;
        loading.style.display  = 'flex';
        errorPanel.style.display = 'none';
        downloadBtn.style.display = 'none';
        printBtn.style.display    = 'none';
 
        // Remove any existing iframe
        const old = modalBody.querySelector('iframe');
        if (old) old.remove();
 
        if (currentPdfUrl) {
            URL.revokeObjectURL(currentPdfUrl);
            currentPdfUrl = null;
        }
 
        modal.classList.add('open');
    }
 
    function closeModal() {
        modal.classList.remove('open');
        if (currentPdfUrl) {
            URL.revokeObjectURL(currentPdfUrl);
            currentPdfUrl = null;
        }
    }
 
    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
 
    /* ── Render PDF from base64 ──────────────────────── */
    function renderPdf(base64, filename) {
        const bytes   = Uint8Array.from(atob(base64), c => c.charCodeAt(0));
        const blob    = new Blob([bytes], { type: 'application/pdf' });
        currentPdfUrl = URL.createObjectURL(blob);
 
        const iframe  = document.createElement('iframe');
        iframe.id     = 'pdfFrame';
        iframe.src    = currentPdfUrl + '#toolbar=0&navpanes=0&scrollbar=0';
        iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
 
        loading.style.display = 'none';
        modalBody.appendChild(iframe);
 
        // Wire action buttons
        downloadBtn.style.display = '';
        printBtn.style.display    = '';
 
        downloadBtn.onclick = () => {
            const a = document.createElement('a');
            a.href     = currentPdfUrl;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        };
 
        printBtn.onclick = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        };
    }
 
    /* ── Show error inside modal ─────────────────────── */
    function showPdfError(msg) {
        loading.style.display      = 'none';
        errorPanel.style.display   = 'flex';
        errorMsg.textContent       = msg || 'Failed to generate the report.';
    }
 
    /* ── Fetch & display report ──────────────────────── */
    function fetchReport(url, title, filename) {
        openModal(title);
 
        fetch(url, {
            method:  'POST',
            headers: {
                'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept':        'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => {
            if (!r.ok) return r.json().then(d => Promise.reject(d.message || 'Server error'));
            return r.json();
        })
        .then(data => {
            if (data.success && data.pdf) {
                renderPdf(data.pdf, filename);
            } else {
                showPdfError(data.message || 'Failed to generate PDF.');
                showToast('danger', 'Report Error', data.message || 'Could not generate the report.');
            }
        })
        .catch(err => {
            const msg = typeof err === 'string' ? err : 'Error fetching report.';
            showPdfError(msg);
            showToast('danger', 'Request Failed', msg);
        });
    }
 
    /* ── Wire report buttons ─────────────────────────── */
    document.getElementById('openFullReport').addEventListener('click', () => {
        fetchReport(
            amanage,
            'Full Agent List',
            'Agent_Report_' + new Date().toISOString().split('T')[0] + '.pdf'
        );
    });
 
    // Add more report button wiring here as needed:
    // document.getElementById('openDeptReport')?.addEventListener('click', () => fetchReport(...));
 
    /* ── Toast ───────────────────────────────────────── */
    function showToast(type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success: 'check_circle', danger: 'error_outline' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type]}</span>
                       <div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }
 
});