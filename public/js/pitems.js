/* ═══════════════════════════════════════════════════════════════
   pitems.js — updated for corepay.css modal system
═══════════════════════════════════════════════════════════════ */

/* ── DataTable init ─────────────────────────────────────────── */
$(document).ready(function () {
    $('#payrollCodesTable').DataTable({
        columnDefs: [{ targets: 'datatable-nosort', orderable: false }],
        language: {
            emptyTable:   'No payroll items found',
            zeroRecords:  'No matching items found',
            processing:   'Loading…'
        }
    });
});

/* ── Toast helper ───────────────────────────────────────────── */
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

// Legacy alias used throughout the old code
function showMessage(message, isError) {
    showToast(isError ? 'danger' : 'success', isError ? 'Error' : 'Success', message);
}

/* ── Modal helpers ──────────────────────────────────────────── */


/* ── ADD form: category → show/hide conditional fields ──────── */
function initAddFormBehavior() {
   
    const categorySelect = document.getElementById('category');
    const balanceOptions = document.getElementById('balanceOptions');
    const loanRateField  = document.getElementById('loanRateField');
    const loanRate       = document.getElementById('loanRate');
    const loanhelper     = document.getElementById('loanhelper');
    const loanhelperDesc = document.getElementById('loanhelperDesc');

    if (!categorySelect) return;

    categorySelect.addEventListener('change', function () {
        const v = this.value;
        balanceOptions.style.display = v === 'balance' ? 'flex' : 'none';
        loanRateField.style.display  = v === 'loan'    ? 'flex' : 'none';
        loanRate.style.display       = v === 'loan'    ? 'flex' : 'none';
        loanhelper.style.display     = v === 'loan'    ? 'flex' : 'none';
        loanhelperDesc.style.display = v === 'loan'    ? 'flex' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function () {

    

    /* ── ADD form: process type toggles formula readonly ────── */
    const calculationRadio = document.getElementById('calculationRadio');
    const amountRadio      = document.getElementById('amount');
    const inputField       = document.getElementById('inputField');

    function toggleReadOnly() {
        if (calculationRadio.checked) {
            inputField.removeAttribute('readonly');
        } else {
            inputField.setAttribute('readonly', true);
        }
    }

    if (calculationRadio && amountRadio) {
        toggleReadOnly();
        calculationRadio.addEventListener('change', toggleReadOnly);
        amountRadio.addEventListener('change', toggleReadOnly);
    }

    /* ── ADD form: formula field code validation ─────────────── */
    const feedback = document.getElementById('feedback');

    function checkCode(code) {
        $.ajax({
            type: 'POST', url: '',
            data: { code },
            success: function (response) {
                try {
                    const json = JSON.parse(response);
                    feedback.innerHTML = json.exists ? '' : `Code ${code} is invalid.`;
                } catch (e) {
                    feedback.innerHTML = 'Error in server response.';
                }
            }
        });
    }

    if (inputField) {
        inputField.addEventListener('keydown', function (event) {
            if (['+', '/', '*', '-', '='].includes(event.key)) {
                const codes = inputField.value.match(/[A-Za-z]+\d+/g);
                if (codes?.length) checkCode(codes[codes.length - 1]);
            }
        });
    }

    /* ── ADD form: mutually exclusive calc checkboxes ────────── */
    const cumulativeValueCheckbox = document.getElementById('cumulativeValue');
    const casualCheckbox          = document.getElementById('casual');

    if (cumulativeValueCheckbox && casualCheckbox) {
        cumulativeValueCheckbox.addEventListener('change', () => {
            if (cumulativeValueCheckbox.checked) casualCheckbox.checked = false;
        });
        casualCheckbox.addEventListener('change', () => {
            if (casualCheckbox.checked) cumulativeValueCheckbox.checked = false;
        });
    }

    /* ── ADD form: priority section ─────────────────────────── */
    const prosstySelect   = document.getElementById('prossty');
    const prioritySection = document.getElementById('prioritySection');
    const sortableList    = document.getElementById('sortableDeductions');
    const priorityInput   = document.getElementById('priorityInput');
    const codeInput       = document.getElementById('code');
    let sortableInstance  = null;

    function updateCurrentItemDisplay() {
        const code = codeInput?.value || 'New Code';
        document.getElementById('currentItemCode').textContent = code;
        document.getElementById('currentItemName').textContent = code;
    }

    if (codeInput) codeInput.addEventListener('input', updateCurrentItemDisplay);

    if (prosstySelect) {
        prosstySelect.addEventListener('change', function () {
            if (this.value === 'Deduction') {
                prioritySection.style.display = 'block';
                loadDeductionPriorities();
            } else {
                prioritySection.style.display = 'none';
                sortableInstance?.destroy();
                sortableInstance = null;
            }
        });
    }

    function loadDeductionPriorities() {
    sortableList.innerHTML = `
        <li class="list-group-item list-loading-state">
            <span class="material-icons list-loading-icon">sync</span>
            Loading deductions…
        </li>`;

    fetch(loadpriori, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            renderDeductionsList(data.deductions, sortableList, priorityInput, 'currentPriorityNumber');
            initializeSortable();
            updateCurrentPriority();
        } else {
            sortableList.innerHTML = '<li class="list-group-item list-error-state">Error loading deductions</li>';
        }
    })
    .catch(() => {
        sortableList.innerHTML = '<li class="list-group-item list-error-state">Failed to load deductions</li>';
    });
}

    function initializeSortable() {
        sortableInstance?.destroy();
        sortableInstance = new Sortable(sortableList, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            onEnd: () => {
                updateBadgeNumbers(sortableList, '.list-group-item');
                updateCurrentPriority();
            }
        });
    }

    function updateCurrentPriority() {
        const count = sortableList.querySelectorAll('.list-group-item').length;
        document.getElementById('currentPriorityNumber').textContent = count + 1;
        priorityInput.value = count + 1;
    }

    /* ── ADD form: sacco checkbox ────────────────────────────── */
    $('#saccocheck').on('change', function () {
        const checked = $(this).is(':checked');
        $('#sacconames').toggle(checked);
        $('#staffSelect7').prop('required', checked);
        if (!checked) $('#staffSelect7').val('').trigger('change');
    });

    /* ── ADD form: submit ────────────────────────────────────── */
    $('#payrollForm').on('submit', function (e) {
        e.preventDefault();

        if (!validateFormFields()) return;

        if (prosstySelect?.value === 'Deduction') {
            const items    = sortableList.querySelectorAll('.list-group-item');
            const newOrder = Array.from(items).map((item, i) => ({ id: item.dataset.id, priority: i + 1 }));
            if (newOrder.length) savePrioritiesOrder(newOrder);
        }

        const submitBtn   = $(this).find('button[type="submit"]');
        const originalHtml = submitBtn.html();
        submitBtn.html('<span class="material-icons" style="animation:spin 1s linear infinite;font-size:16px;">sync</span> Saving…').prop('disabled', true);

        $.ajax({
            type: 'POST', url: amanage,
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    showToast('success', 'Saved', 'Payroll item added successfully.');
                    $('#payrollForm')[0].reset();
                    $('#sacconames').hide();
                    $('#staffSelect7').removeAttr('required').val('');
                    closeModal('addModal');
                } else if (response.status === 'duplicate') {
                    showToast('danger', 'Duplicate', response.message);
                } else {
                    showToast('danger', 'Error', response.message || 'Submission failed.');
                }
            },
            error: function (xhr, status, error) {
                showToast('danger', 'Error', 'Submission failed: ' + error);
            },
            complete: function () {
                submitBtn.html(originalHtml).prop('disabled', false);
            }
        });
    });

    /* ── EDIT form: category change ──────────────────────────── */
    $('#editCategory').on('change', function () {
        const v = $(this).val();
        $('#editBalanceOptions').toggle(v === 'balance');
        $('#editLoanRateField').toggle(v === 'loan');
        $('#editLoanRate').toggle(v === 'loan');
        $('#editloanhelper').toggle(v === 'loan');
        $('#editloanhelperDesc').toggle(v === 'loan');
    });

    /* ── EDIT form: sacco checkbox ───────────────────────────── */
    $('#saccoeditcheck').on('change', function () {
        const checked = $(this).is(':checked');
        $(this).val(checked ? 'Yes' : 'No');
        $('#saccoeditnames').toggle(checked);
        $('#staffSelect8').prop('required', checked);
        if (!checked) $('#staffSelect8').val('').trigger('change');
    });

    /* ── EDIT form: save button ──────────────────────────────── */
    $('#saveChangesButton').on('click', function () {
        submitEditForm();
    });

    /* ── Close add modal on reset ────────────────────────────── */
    document.getElementById('addModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal('addModal');
    });
    document.getElementById('editModal').addEventListener('click', function (e) {
        if (e.target === this) closeModal('editModal');
    });

    // ── Modal open/close via data-action ─────────────────────────────
document.addEventListener('click', function (e) {

    

    // Open
    const opener = e.target.closest('[data-action="open-modal"]');
    if (opener) {
        const target = opener.dataset.target;
        const modal  = document.getElementById(target);
        if (modal) modal.classList.add('open');
        initAddFormBehavior();
        return;
    }

    // Close button
    const closer = e.target.closest('[data-action="close-modal"]');
    if (closer) {
        const target = closer.dataset.target;
        const modal  = document.getElementById(target);
        if (modal) modal.classList.remove('open');
        return;
    }

    // Click on backdrop itself (outside modal-card)
    const backdrop = e.target.closest('.modal-backdrop-custom');
    if (backdrop && e.target === backdrop) {
        backdrop.classList.remove('open');
        return;
    }
});

// ── Escape key closes any open modal ─────────────────────────────
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-backdrop-custom.open').forEach(function (m) {
            m.classList.remove('open');
        });
    }
});
});

/* ═══════════════════════════════════════════════════
   Shared helpers
═══════════════════════════════════════════════════ */

/* Render a deduction list into a <ul> */
function renderDeductionsList(deductions, listEl, priorityInputEl, priorityNumId) {

    listEl.innerHTML = '';

    if (!deductions.length) {
        listEl.innerHTML = `
            <li class="list-group-item list-empty-state">
                <span class="material-icons list-state-icon">info</span>
                No existing deductions — this will be priority #1.
            </li>`;
        document.getElementById(priorityNumId).textContent = '1';
        priorityInputEl.value = '1';
        return;
    }

    deductions.forEach(function(d, i) {

        // ✅ <li> — data-* set via dataset, never string interpolation
        const li = document.createElement('li');
        li.className        = 'list-group-item';
        li.dataset.id       = String(d.id);       // ✅ dataset assignment, not attribute string
        li.dataset.priority = String(d.priority); // ✅ dataset assignment, not attribute string

        // ✅ Drag handle — purely static content, no server data
        const dragHandle = document.createElement('div');
        dragHandle.className = 'drag-handle';
        const dragIcon = document.createElement('span');
        dragIcon.className   = 'material-icons';
        dragIcon.textContent = 'drag_indicator';
        dragHandle.appendChild(dragIcon);

        // ✅ Priority number — integer from loop index, not server string
        const priorityNum = document.createElement('span');
        priorityNum.className   = 'priority-num';
        priorityNum.textContent = i + 1;

        // ✅ Content wrapper — cname and code via textContent
        const contentDiv = document.createElement('div');
        contentDiv.style.flex = '1';

        const nameStrong = document.createElement('strong');
        nameStrong.style.cssText = 'font-size:13px;color:var(--ink);';
        nameStrong.textContent   = d.cname;  // ✅ never parsed as HTML

        const codeDiv = document.createElement('div');
        codeDiv.style.cssText = 'font-size:11.5px;color:var(--muted);';
        codeDiv.textContent   = d.code;      // ✅ never parsed as HTML

        contentDiv.appendChild(nameStrong);
        contentDiv.appendChild(codeDiv);

        // ✅ Priority label — d.priority via textContent
        const priorityLabel = document.createElement('span');
        priorityLabel.style.cssText = 'font-size:11.5px;color:var(--muted);';
        priorityLabel.textContent   = 'Priority ' + String(d.priority); // ✅ safe

        // Assemble and append
        li.appendChild(dragHandle);
        li.appendChild(priorityNum);
        li.appendChild(contentDiv);
        li.appendChild(priorityLabel);
        listEl.appendChild(li);
    });
}

/* Update priority number badges after drag */
function updateBadgeNumbers(listEl, itemSelector) {
    listEl.querySelectorAll(itemSelector).forEach((item, i) => {
        const badge = item.querySelector('.priority-num');
        if (badge) badge.textContent = i + 1;
    });
}

/* Save reordered priorities */
function savePrioritiesOrder(order) {
    $.ajax({
        url: updateorder, method: 'POST',
        data: JSON.stringify({ priorities: order }),
        contentType: 'application/json',
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (data) {
            if (data.status === 'success') {
                showToast('success', 'Priorities', 'Order updated successfully.');
            } else {
                showToast('danger', 'Error', data.message || 'Failed to update priorities.');
            }
        },
        error: function () {
            showToast('danger', 'Error', 'Could not update priority order.');
        }
    });
}

/* ═══════════════════════════════════════════════════
   openEditModal — reads data-id from the clicked link,
   then reads hidden <td> cells from the same <tr>
═══════════════════════════════════════════════════ */


/* Set a segmented toggle radio by value */
function setSegToggle(toggleId, radioName, value, fallback) {
    const target = value || fallback;
    const radios  = document.querySelectorAll(`#${toggleId} input[name="${radioName}"]`);
    let matched   = false;

    radios.forEach(r => {
        if (r.value === target) { r.checked = true; matched = true; }
        else                    { r.checked = false; }
    });

    /* Fall back to first option if no match */
    if (!matched && radios.length) radios[0].checked = true;
}

/* ═══════════════════════════════════════════════════
   submitEditForm
═══════════════════════════════════════════════════ */
function submitEditForm() {
    const formData = {
        id:        $('#editid').val(),
        code:      $('#editCode').val(),
        cname:     $('#editDescription').val(),
        formula:   $('#editinputField').val(),
        procctype: $('input[name="editProcessType"]:checked').val(),
        cumcas:    $('input[name="editcalctype"]:checked').val(),
        varorfixed:$('input[name="editVarOrFixed"]:checked').val(),
        taxaornon: $('input[name="editTaxOrNon"]:checked').val(),
        category:  $('#editCategory').val(),
        prossty:   $('#editProcessSty').val(),
        relief:    $('input[name="editRelief"]:checked').val(),
        saccocheck:$('#saccoeditcheck').val(),
        poster:    $('#staffSelect8').val(),
        increREDU: $('#editCategory').val() === 'balance' ? $('input[name="editBalanceType"]:checked').val() : null,
        rate:      $('#editCategory').val() === 'loan'    ? $('#editRate').val()          : null,
        intrestcode:$('#editCategory').val() === 'loan'   ? $('#editinterestcode').val()  : null,
        codename:  $('#editCategory').val() === 'loan'    ? $('#editinterestdesc').val()  : null,
        recintres: $('#editCategory').val() === 'loan'    ? $('input[name="editrecintres"]:checked').val() : null,
    };

    /* Save priority order if deduction */
    if ($('#editProcessSty').val() === 'Deduction') {
        const editsortableList = document.getElementById('editsortableDeductions');
        const newOrder = Array.from(editsortableList.querySelectorAll('.list-group-item'))
            .map((item, i) => ({ id: item.dataset.id, priority: i + 1 }));
        if (newOrder.length) savePrioritiesOrder(newOrder);
    }

    const saveBtn      = document.getElementById('saveChangesButton');
    const originalHtml = saveBtn.innerHTML;
    saveBtn.innerHTML  = '<span class="material-icons" style="animation:spin 1s linear infinite;font-size:16px;">sync</span> Saving…';
    saveBtn.disabled   = true;

    $.ajax({
        url: update, type: 'POST',
        data: formData,
        dataType: 'json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function (response) {
            if (response.success) {
                showToast('success', 'Updated', 'Payroll code updated successfully.');
                updateTableRow(formData);
                closeModal('editModal');
            } else {
                showToast('danger', 'Error', response.message || 'Update failed.');
            }
        },
        error: function (xhr, status, error) {
            showToast('danger', 'Error', 'Update failed: ' + error);
        },
        complete: function () {
            saveBtn.innerHTML = originalHtml;
            saveBtn.disabled  = false;
        }
    });
}

/* Update the table row in place after edit */
function updateTableRow(f) {
    const row = $('#payrollCodesTable tbody tr').filter(function () {
        return $(this).find('td:eq(0)').text().trim() === f.id;
    });

    row.find('td:eq(1) .code-primary').text(f.code);
    row.find('td:eq(1) .code-desc').text(f.cname);
    row.find('td:eq(2)').text(f.procctype);
    row.find('td:eq(3)').html(`<span class="type-badge ${(f.varorfixed||'').toLowerCase()}">${f.varorfixed||''}</span>`);
    row.find('td:eq(4)').html(`<span class="type-badge ${f.taxaornon === 'Non-taxable' ? 'nontaxable' : 'taxable'}">${f.taxaornon||''}</span>`);
    row.find('td:eq(5)').html(`<span class="type-badge ${(f.category||'').toLowerCase()}">${f.category||''}</span>`);
    /* Update hidden cells */
    row.find('td:eq(6)').text(f.relief    || '');
    row.find('td:eq(7)').text(f.prossty   || '');
    row.find('td:eq(8)').text(f.rate      || '');
    row.find('td:eq(9)').text(f.increREDU || '');
    row.find('td:eq(10)').text(f.recintres    || '');
    row.find('td:eq(11)').text(f.formula      || '');
    row.find('td:eq(12)').text(f.cumcas       || '');
    row.find('td:eq(13)').text(f.intrestcode  || '');
    row.find('td:eq(14)').text(f.codename     || '');
    row.find('td:eq(15)').text(f.saccocheck   || '');
    row.find('td:eq(16)').text(f.poster       || '');
}

/* ═══════════════════════════════════════════════════
   deletePayrollCode
═══════════════════════════════════════════════════ */


/* ── Validation ─────────────────────────────────────────────── */
function validateFormFields() {
    let isValid = true;

    $('#payrollForm').find('input:not([type="radio"]):not([type="checkbox"]), select').each(function () {
        if (!$(this).prop('readonly') && $(this).prop('required') && !$(this).val().trim()) {
            $(this).css('border-color', 'var(--danger)');
            isValid = false;
        } else {
            $(this).css('border-color', '');
        }
    });

    if (!validateNumericFields()) isValid = false;
    return isValid;
}

function validateNumericFields() {
    let valid = true;
    const rateField = $('#rate');

    if (rateField.is(':visible') && !rateField.prop('readonly') && isNaN(rateField.val())) {
        rateField.css('border-color', 'var(--danger)');
        valid = false;
    } else {
        rateField.css('border-color', '');
    }

    if ($('#loanhelper').is(':visible')) {
        const recintresVal = $('input[name="recintres"]:checked').val();
        if (recintresVal === '0') {
            ['#interestcode', '#interestdesc'].forEach(sel => {
                if (!$(sel).val()?.trim()) {
                    $(sel).css('border-color', 'var(--danger)');
                    valid = false;
                } else {
                    $(sel).css('border-color', '');
                }
            });
        }
    }

    return valid;
}

/* ── Spin keyframe (injected once) ──────────────────────────── */
if (!document.getElementById('spin-style')) {
    const s = document.createElement('style');
    s.id = 'spin-style';
    s.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(s);
}