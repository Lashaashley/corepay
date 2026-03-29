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
    // ✅ VALIDATION 1: Check if base64 is valid format
    if (!base64 || typeof base64 !== 'string') {
        console.error('Invalid PDF data: empty or wrong type');
        showUserError('Invalid PDF data received');
        return;
    }
    
    // ✅ VALIDATION 2: Check base64 format
    if (!isValidBase64(base64)) {
        console.error('Invalid base64 format');
        showUserError('Invalid PDF format');
        return;
    }
    
    try {
        // ✅ VALIDATION 3: Check size limits (prevent DoS)
        const maxSizeBytes = 10 * 1024 * 1024; // 10MB limit
        const estimatedSize = base64.length * 0.75; // Base64 to bytes ratio
        if (estimatedSize > maxSizeBytes) {
            console.error('PDF too large:', estimatedSize);
            showUserError('PDF file too large');
            return;
        }
        
        // Decode base64 with error handling
        let bytes;
        try {
            bytes = Uint8Array.from(atob(base64), c => c.charCodeAt(0));
        } catch (decodeError) {
            console.error('Base64 decode failed:', decodeError);
            showUserError('Invalid PDF encoding');
            return;
        }
        
        // ✅ VALIDATION 4: Validate PDF magic bytes (header)
        if (!isValidPdfHeader(bytes)) {
            console.error('Invalid PDF header - possible XSS attempt');
            showUserError('Invalid PDF file format');
            return;
        }
        
        // ✅ VALIDATION 5: Check for suspicious PDF features
        if (containsSuspiciousPdfContent(bytes)) {
            console.error('PDF contains suspicious content (JavaScript)');
            // Option 1: Reject completely
            showUserError('PDF contains unsafe content');
            return;
            // Option 2: Log and continue but with extra sandbox restrictions
        }
        
        // Create blob with proper MIME type
        const blob = new Blob([bytes], { type: 'application/pdf' });
        currentPdfUrl = URL.createObjectURL(blob);
        
        // ✅ SECURITY: Create iframe with sandbox restrictions
        const iframe = document.createElement('iframe');
        iframe.id = 'pdfFrame';
        iframe.src = currentPdfUrl + '#toolbar=0&navpanes=0&scrollbar=0';
        iframe.style.cssText = 'width:100%;height:100%;border:none;display:block;';
        
        // ✅ CRITICAL: Add sandbox attribute to prevent script execution
        iframe.sandbox = 'allow-same-origin allow-popups allow-modals';
        // NOTE: 'allow-scripts' is OMITTED to prevent PDF JavaScript execution
        // If you need scripts, add 'allow-scripts' but understand the risk
        
        // ✅ Add security headers via iframe csp (if supported)
        iframe.csp = "default-src 'none'; style-src 'unsafe-inline'";
        
        // Clear previous content and show loading state
        while (modalBody.firstChild) {
            modalBody.removeChild(modalBody.firstChild);
        }
        
        loading.style.display = 'none';
        modalBody.appendChild(iframe); // Now safe due to sandbox
        
        downloadBtn.style.display = '';
        printBtn.style.display = '';
        
        // ✅ SECURE DOWNLOAD HANDLER
        downloadBtn.onclick = () => {
            // Validate URL scheme
            if (!currentPdfUrl || !currentPdfUrl.startsWith('blob:')) {
                console.error('Unexpected PDF URL scheme, aborting download');
                showUserError('Cannot download PDF: Invalid URL');
                return;
            }
            
            // ✅ Sanitize filename - more strict version
            let safeFilename = sanitizeFilename(filename);
            
            const a = document.createElement('a');
            a.href = currentPdfUrl;
            a.download = safeFilename;
            a.rel = 'noopener noreferrer'; // Security for older browsers
            
            document.body.appendChild(a);
            a.click();
            
            // Cleanup
            setTimeout(() => {
                document.body.removeChild(a);
            }, 100);
        };
        
        // ✅ SECURE PRINT HANDLER with error handling
        printBtn.onclick = () => {
            try {
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } else {
                    console.error('Iframe not ready for printing');
                    showUserError('Please wait for PDF to load before printing');
                }
            } catch (printError) {
                console.error('Print failed:', printError);
                showUserError('Unable to print PDF. Please download and print instead.');
            }
        };
        
        // ✅ Clean up blob URL when modal closes
        $('#reportModal').on('hidden.bs.modal', function() {
            if (currentPdfUrl) {
                URL.revokeObjectURL(currentPdfUrl);
                currentPdfUrl = null;
            }
        });
        
    } catch (error) {
        console.error('PDF rendering error:', error);
        showUserError('Failed to load PDF. Please try again.');
    }
}

// ✅ Helper: Validate base64 format
function isValidBase64(str) {
    // Check length is multiple of 4
    if (str.length % 4 !== 0) return false;
    
    // Check characters are valid base64
    const base64Regex = /^[A-Za-z0-9+/]*={0,2}$/;
    if (!base64Regex.test(str)) return false;
    
    // Additional check: shouldn't contain HTML tags even when decoded
    try {
        const decoded = atob(str);
        // Block obvious HTML/script tags
        if (/<script|javascript:|onerror|onload/i.test(decoded)) {
            console.warn('Potential XSS detected in base64 content');
            return false;
        }
    } catch (e) {
        return false;
    }
    
    return true;
}

// ✅ Helper: Validate PDF header (magic bytes)
function isValidPdfHeader(bytes) {
    if (!bytes || bytes.length < 8) return false;
    
    // PDF signature: %PDF- (bytes: 37 80 68 70 45)
    const isValid = bytes[0] === 37 &&  // %
                    bytes[1] === 80 &&  // P
                    bytes[2] === 68 &&  // D
                    bytes[3] === 70 &&  // F
                    bytes[4] === 45;    // -
    
    if (!isValid) return false;
    
    // Check for version (1.0-1.7, 2.0)
    const version = String.fromCharCode(bytes[5], bytes[6], bytes[7]);
    if (!/^\d\.\d$/.test(version)) return false;
    
    return true;
}

// ✅ Helper: Check for suspicious PDF content (JavaScript)
function containsSuspiciousPdfContent(bytes) {
    // Convert to string for pattern matching
    const str = new TextDecoder('latin1').decode(bytes.slice(0, 10000)); // Check first 10KB
    
    // Look for JavaScript indicators in PDF
    const suspiciousPatterns = [
        /\/JavaScript/i,
        /\/JS\s/i,
        /\/Launch\s/i,
        /\/EmbeddedFile/i,
        /\/RichMedia\s/i,
        /\/AA\s/i,        // Additional Actions
        /\/OpenAction\s/i,
        /\/SubmitForm/i
    ];
    
    for (const pattern of suspiciousPatterns) {
        if (pattern.test(str)) {
            console.warn('Suspicious PDF feature detected:', pattern);
            return true;
        }
    }
    
    return false;
}

// ✅ Helper: Sanitize filename strictly
function sanitizeFilename(filename) {
    if (!filename || typeof filename !== 'string') {
        return 'download.pdf';
    }
    
    // Remove path traversal attempts
    let safe = filename.replace(/\.\./g, '');
    
    // Keep only alphanumeric, dash, underscore, dot
    safe = safe.replace(/[^a-zA-Z0-9\-_.]/g, '');
    
    // Ensure it ends with .pdf
    if (!safe.toLowerCase().endsWith('.pdf')) {
        safe += '.pdf';
    }
    
    // Limit length
    safe = safe.substring(0, 255);
    
    // Fallback if empty
    return safe || 'document.pdf';
}

// ✅ Helper: Show user-friendly error
function showUserError(message) {
    if (typeof showMessage === 'function') {
        showMessage(message, true);
    } else {
        alert(message); // Fallback
    }
    
    // Clear modal content
    if (modalBody) {
        modalBody.innerHTML = `<div class="alert alert-danger m-3">${escapeHtml(message)}</div>`;
    }
}

// ✅ Helper: Escape HTML for error messages
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
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
    const icons = {
        success: 'check_circle',
        danger:  'error_outline',
        warning: 'warning_amber'
    };

    const t = document.createElement('div');
    t.className = `toast-msg ${type}`;

    // ✅ Build structure via DOM — never touches innerHTML with external data
    const $icon = document.createElement('span');
    $icon.className = 'material-icons';
    // ✅ icons[type] whitelisted — but fall back to '' if type is unexpected
    $icon.textContent = icons[type] || '';

    const $content = document.createElement('div');

    const $title = document.createElement('strong');
    $title.textContent = title;      // ✅ .textContent, not innerHTML

    // ✅ message as a text node — never parsed as HTML
    const $message = document.createTextNode(' ' + message);

    $content.appendChild($title);
    $content.appendChild($message);

    t.appendChild($icon);
    t.appendChild($content);

    wrap.appendChild(t);

    const dismiss = () => {
        t.classList.add('leaving');
        setTimeout(() => t.remove(), 300);
    };

    t.addEventListener('click', dismiss);
    setTimeout(dismiss, 5000);
}
 
});