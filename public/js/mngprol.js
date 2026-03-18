
(function () {
    'use strict';
 
    /* ── State ────────────────────────────────────────────── */
    let choicesInstance  = null;
    let modalOpenedOnce  = false;
 
    /* ── Modal open ────────────────────────────────────────── */
    $('#exampleModal').on('show.bs.modal', function () {
 
        /* Load payroll items into Select2 */
        $.ajax({
            url: getcodes,
            type: 'GET',
            success: function (response) {
                const $sel = $('#pitem');
                $sel.empty().append('<option value="">Select Item</option>');
 
                (response.data || []).forEach(function (item) {
                    $sel.append(
                        $('<option>', {
                            value:              item.cname,
                            'data-code':        item.code,
                            'data-category':    item.category,
                            'data-increredu':   item.increREDU,
                            'data-formular':    item.formularinpu
                        }).text(item.code + ' - ' + item.cname)
                    );
                });
 
                /* Init / reinit Select2 — destroy first to avoid duplicates */
                if ($sel.hasClass('select2-hidden-accessible')) $sel.select2('destroy');
 
                $sel.select2({
                    placeholder: 'Select Item',
                    allowClear: true,
                    dropdownParent: $('#exampleModal'),
                    width: '100%'
                });
            },
            error: function () {
               showToast('danger', 'Error!', 'Failed to load payroll items.');
            }
        });
 
        /* Init Choices.js on staff dropdown — destroy old instance first */
        const rawEl = document.getElementById('searchValue');
        if (!rawEl) return;
 
        if (choicesInstance) {
            try { choicesInstance.destroy(); } catch (e) {}
            choicesInstance = null;
        }
 
        choicesInstance = new Choices(rawEl, {
            searchEnabled:         true,
            placeholderValue:      'Search staff…',
            searchPlaceholderValue:'Type to search…',
            allowHTML:             true,
            shouldSort:            false,
            itemSelectText:        '',
            noResultsText:         'No matching staff',
            searchResultLimit:     50,   /* show up to 50 results */
        });
 
        /* Debounced AJAX search */
        let searchTimer = null;
 
        /* Listen on the internal Choices input */
        function attachChoicesSearch () {
            const inner = rawEl.closest('.choices')?.querySelector('.choices__input--cloned');
            if (!inner) { setTimeout(attachChoicesSearch, 60); return; }
 
            inner.addEventListener('input', function () {
                clearTimeout(searchTimer);
                const term = this.value.trim();
                searchTimer = setTimeout(() => fetchStaff(term), 280);
            });
        }
 
        attachChoicesSearch();
        fetchStaff('');   /* initial full load */
    });
 
    /* ── Fetch staff list ──────────────────────────────────── */
    function fetchStaff(term) {
        $.ajax({
            url:      staffsearch,
            method:   'GET',
            data:     { term },
            dataType: 'json',
            success: function (data) {
                const items = (data.results || []).map(r => ({ value: r.emp_id, label: r.label }));
                if (choicesInstance) {
                    choicesInstance.clearChoices();
                    choicesInstance.setChoices(items, 'value', 'label', true);
                }
            },
            error: function (xhr) {
                console.error('Staff search error:', xhr.responseText);
            }
        });
    }
 
    /* ── populateCategory (called by onchange on #pitem) ───── */
    window.populateCategory = function () {
        const pitem    = document.getElementById('pitem');
        const opt      = pitem.options[pitem.selectedIndex];
        const amount   = document.getElementById('amount');
        const balance  = document.getElementById('balance');
 
        amount.readOnly  = false; amount.value  = '';
        balance.readOnly = false; balance.value = '';
 
        document.getElementById('category').value  = opt.getAttribute('data-category')  || '';
        document.getElementById('increREDU').value = opt.getAttribute('data-increredu') || '';
        document.getElementById('codebal').value   = opt.getAttribute('data-code')      || '';
        document.getElementById('formular').value  = opt.getAttribute('data-formular')  || '';
 
        toggleHiddenContainer();
        toggleHiddenContainer2();
        toggleHiddenContainer4();
        cleartxt2();
    };
 
    /* ── Container toggles ─────────────────────────────────── */
    window.toggleHiddenContainer = function () {
        const cat = document.getElementById('category').value;
        document.getElementById('hiddenContainer').style.display  = cat === 'loan'    ? 'block' : 'none';
        document.getElementById('hiddenContainer2').style.display = cat === 'balance' ? 'block' : 'none';
    };
 
    window.toggleHiddenContainer2 = function () {
        const val = document.getElementById('pitem').value;
        document.getElementById('pensionContainer').style.display = val === 'Pension' ? 'block' : 'none';
    };
 
    window.toggleHiddenContainer4 = function () {
        const fVal   = (document.getElementById('formular').value || '').trim();
        const otBox  = document.getElementById('otContainer');
        const amount = document.getElementById('amount');
        const balance= document.getElementById('balance');
 
        if (fVal) {
            otBox.style.display     = 'block';
            amount.readOnly         = true;
            balance.readOnly        = true;
            document.getElementById('otdate').required = true;
        } else {
            otBox.style.display     = 'none';
            document.getElementById('formular').value = '';
        }
    };
 
    window.cleartxt2 = function () {
        ['balance','balend','duration','quantity','camountf'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
    };
 
    /* ── searchstaffdet (called by onchange on #searchValue) ── */
    window.searchstaffdet = function () { performSearch(); };
 
    function performSearch () {
        const searchValue = document.getElementById('searchValue').value;
        if (!searchValue.trim()) { clearFields(); return; }
 
        const category = document.getElementById('category').value;
        const code     = document.getElementById('codebal').value;
        const formula  = (document.getElementById('formular').value || '').trim();
        const codes    = formula ? (formula.match(/[A-Za-z]+\d+/g) || []) : [];
 
        $.ajax({
            url:    staffdet,
            method: 'POST',
            data: {
                _token:         $('meta[name="csrf-token"]').attr('content'),
                searchCategory: document.getElementById('searchCategory').value,
                searchValue,
                category,
                code,
                codes
            },
            success: function (data) {
                if (!data.success) { clearFields(); showToast('danger', 'Error!', data.message); return; }
 
                $('#surname').val(data.surname);
                $('#workNumber').val(data.workNumber);
                $('#othername').val(data.othername);
                $('#department').val(data.department);
                $('#departmentname').val(data.departmentname);
                $('#epmpenperce').val(data.epmpenperce);
                $('#emplopenperce').val(data.emplopenperce);
                $('#pensionable').val(data.totalPensionAmount);
 
                /* Deduction formula amounts */
                const amounts = codes.map(c => data.existingCodes?.[c] || '');
                $('#camountf').val(amounts.join(','));
 
                if (category === 'balance') {
                    $('#cbalance').val(data.balance);
                    $('#balance').val(data.balance);
                    $('#amount').val(data.Amount);
                    setToggle('activeinacToggle', 'toggleLabel2', data.statdeduc);
                }
 
                if (category === 'loan') {
                    $('#balance').val(data.balance);
                    $('#amount').val(data.Amount);
                    setToggle('activeinaclonToggle', 'toggleLabel3', data.statdeduc);
                }
 
                document.getElementById('amount').focus();
            },
            error: function (xhr, status) {
                showToast('danger', 'Error!', status);
            }
        });
    }
 
    function setToggle(toggleId, labelId, statdeduc) {
        const active = statdeduc === '1' || statdeduc === '';
        const toggle = document.getElementById(toggleId);
        const label  = document.getElementById(labelId);
        if (toggle) toggle.checked = active;
        if (label)  label.textContent = active ? 'Active' : 'Inactive';
    }
 
    /* ── Validate ──────────────────────────────────────────── */
    function validateForm () {
        const required = ['month','year','pitem','workNumber','department','amount','balance'];
 
        if ($('#pensionContainer').is(':visible')) required.push('epmpenperce','emplopenperce');
        if ($('#otContainer').is(':visible'))      required.push('quantity','otdate','formular');
 
        let valid = true;
 
        required.forEach(function (id) {
            const el  = document.getElementById(id);
            const val = el ? el.value.trim() : '';
            if (!val) {
                valid = false;
                el?.classList.add('is-invalid');
            } else {
                el?.classList.remove('is-invalid');
            }
        });
 
        if (!valid) showToast('danger', 'Invalid!', 'Please fill in all required fields.');
        return valid;
    }
 
    /* ── Submit ────────────────────────────────────────────── */
    function submitForm () {
        const selectedParameter = $('#pitem').val();
        const btn = $('#submitBtn');
        const origHtml = btn.html();
 
        btn.html('<span class="material-icons" style="font-size:14px;animation:spin 1s linear infinite">sync</span> Posting…').prop('disabled', true);
 
        const formData = {
            month:      $('#month').val(),
            year:       $('#year').val(),
            parameter:  selectedParameter,
            surname:    $('#surname').val(),
            othername:  $('#othername').val(),
            workNumber: $('#workNumber').val(),
            department: $('#department').val(),
            amount:     $('#amount').val(),
            category:   $('#category').val(),
            enddate:    $('#enddate').val(),
            months:     $('#months').val(),
            balance:    $('#balance').val()
        };
 
        if ($('#pensionContainer').is(':visible')) {
            formData.epmpenperce  = $('#epmpenperce').val();
            formData.emplopenperce= $('#emplopenperce').val();
        }
 
        if ($('#otContainer').is(':visible')) {
            formData.quantity = $('#quantity').val();
            formData.otdate   = $('#otdate').val();
            formData.formular = $('#formular').val();
        }
 
        /* Open value logic — consolidated */
        if ($('#Open').is(':visible'))
            formData.openvalue = $('#activeinacToggle').prop('checked') ? '1' : '0';
        else if ($('#hiddenContainer').is(':visible'))
            formData.openvalue = $('#activeinaclonToggle').prop('checked') ? '1' : '0';
        else if ($('#category').val() === 'normal')
            formData.openvalue = '1';
 
        $.ajax({
            url:  paysubmit,
            type: 'POST',
            data: formData,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json',
            success: function (result) {
                if (result.success) {
                    showToast('success', 'Success!', 'Payroll item saved.');
                    setTimeout(() => fetchData(selectedParameter), 100);
                    ['surname','workNumber','othername','department','departmentname',
                     'epmpenperce','emplopenperce','pensionable','balance','amount',
                     'quantity','otdate'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.value = '';
                    });
                    /* Clear Choices selection */
                    if (choicesInstance) choicesInstance.setChoiceByValue('');
                } else {
                    showToast('danger', 'Invalid!', result.message || 'An error occurred.');
                }
            },
            error: function (xhr, status, error) {
                showToast('danger', 'Error: ' + error, true);
            },
            complete: function () {
                btn.html(origHtml).prop('disabled', false);
            }
        });
    }
 
    $('#submitBtn').on('click', function (e) {
        e.preventDefault();
        if (validateForm()) submitForm();
    });
 
    /* ── Calculation helpers ─────────────────────────────────
       (unchanged logic, just cleaner) */
    window.calculateMonthsAndEndDate = function () {
        const balance = parseFloat($('#balance').val());
        const amount  = parseFloat($('#amount').val());
        if (!isNaN(balance) && !isNaN(amount) && amount !== 0) {
            const months = balance / amount;
            $('#months').val(months.toFixed(2));
            updateEndDate(months);
        }
    };
 
    window.recalculateAmount = function () {
        const balance = parseFloat($('#balance').val());
        const months  = parseFloat($('#months').val());
        if (!isNaN(balance) && !isNaN(months) && months !== 0) {
            $('#amount').val((balance / months).toFixed(2));
            updateEndDate(months);
        }
    };
 
    window.calcbalancedates = function () {
        const balance  = parseFloat($('#balance').val());
        const duration = parseFloat($('#duration').val());
        if (!isNaN(balance) && !isNaN(duration)) {
            $('#amount').val((balance / duration).toFixed(2));
            updateEndDate2(duration);
        }
    };
 
    function updateEndDate (months) {
        const d = new Date();
        const end = new Date(d.getFullYear(), d.getMonth() + Math.ceil(months), 0);
        $('#enddate').val(end.toLocaleString('default', { month: 'long' }) + ' ' + end.getFullYear());
    }
 
    function updateEndDate2 (duration) {
        const d = new Date();
        const end = new Date(d.getFullYear(), d.getMonth() + Math.ceil(duration), 0);
        $('#balend').val(end.toLocaleString('default', { month: 'long' }) + ' ' + end.getFullYear());
    }
 
    /* Wire calculation listeners once DOM is ready */
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('amount')  ?.addEventListener('input', calculateMonthsAndEndDate);
        document.getElementById('balance') ?.addEventListener('input', calculateMonthsAndEndDate);
        document.getElementById('months')  ?.addEventListener('input', recalculateAmount);
        document.getElementById('duration')?.addEventListener('input', calcbalancedates);
        document.getElementById('quantity')?.addEventListener('input', executeFormula); // existing function
 
        /* Toggle: Fixed / Open */
        document.getElementById('fixedOpenToggle')?.addEventListener('change', function () {
            const isOpen = this.checked;
            document.getElementById('toggleLabel').textContent   = isOpen ? 'Open' : 'Fixed';
            document.getElementById('Fixed').style.display       = isOpen ? 'none' : 'block';
            document.getElementById('Open').style.display        = isOpen ? 'flex' : 'none';
            updateAmountFieldState();
            cleartxt2();
        });
 
        /* Toggle: balance active/inactive */
        document.getElementById('activeinacToggle')?.addEventListener('change', function () {
            document.getElementById('toggleLabel2').textContent = this.checked ? 'Active' : 'Inactive';
        });
 
        /* Toggle: loan active/inactive */
        document.getElementById('activeinaclonToggle')?.addEventListener('change', function () {
            document.getElementById('toggleLabel3').textContent = this.checked ? 'Active' : 'Inactive';
        });
 
        function updateAmountFieldState () {
            const increREDU = document.getElementById('increREDU').value;
            const amount    = document.getElementById('amount');
            if (increREDU === 'Reducing') {
                amount.readOnly = true;
                amount.value    = '0';
            } else if (increREDU === 'Increasing') {
                amount.readOnly = false;
                amount.value    = '';
            }
        }
    });
 
    /* ── Table helpers ─────────────────────────────────────── */
    window.clearFields = function () {
        ['surname','workNumber','othername','department','departmentname',
         'epmpenperce','emplopenperce','pensionable','balance'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        const toggle = document.getElementById('activeinacToggle');
        if (toggle) { toggle.checked = false; }
        const label  = document.getElementById('toggleLabel2');
        if (label)  label.textContent = 'Inactive';
    };
 
    window.populateTable = function (data) {
        const table = $('#contentTable2').DataTable();
        table.clear();
        let total = 0;
        data.forEach(function (item) {
            table.row.add([
                item.Surname + ' ' + item.othername,
                item.WorkNo,
                item.dept,
                item.PCode,
                item.Amount
            ]);
            total += parseFloat(item.Amount) || 0;
        });
        table.draw();
        document.getElementById('totalsvar').value = total.toFixed(2);
    };
 
    window.fetchData = function (parameter) {
        $.ajax({
            url:  fetchitems,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), parameter },
            success: function (response) {
                if (response.success) populateTable(response.data);
            },
            error: function (xhr, status, error) {
                console.error('fetchData error:', status, error);
            }
        });
    };
 
})();

if (typeof Swal === 'undefined') {
    console.warn('Local SweetAlert2 not found, using CDN');
    // CDN will define Swal globally
}
        const currentMonth = $('#currentMonth').val();
const currentYear = $('#currentYear').val();
 
// Add event listener to the button
document.getElementById('preview-totals-btn').addEventListener('click', function() {
    // Get current month and year (make sure these variables are defined)
    const currentMonth = $('#currentMonth').val();
const currentYear = $('#currentYear').val();
    
    // Confirmation message
    const confirmationMessage = `Are you sure you want to process totals for ${currentMonth} ${currentYear}?`;

    // Use SweetAlert for confirmation
    Swal.fire({
        title: 'Are you sure?',
        text: confirmationMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, process totals!',
        cancelButtonText: 'No, cancel!',
        customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            processPayrollTotals(currentMonth, currentYear);
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Cancelled',
                text: 'Processing was cancelled.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
});

/**
 * Process payroll totals with SSE progress tracking
 */
function processPayrollTotals(month, year) {
    // Show progress modal
    Swal.fire({
        title: 'Processing Payroll',
        html: `
            <div class="progress" style="height: 25px;">
                <div id="swal-progress-bar" 
                     class="progress-bar progress-bar-striped progress-bar-animated" 
                     role="progressbar" 
                     style="width: 0%;" 
                     aria-valuenow="0" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    0%
                </div>
            </div>
            <p id="swal-progress-message" class="mt-3 mb-0">Initializing process...</p>
        `,
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Create EventSource for SSE
     const url = autocalc + "?month=" + encodeURIComponent(month) + "&year=" + encodeURIComponent(year) + "&t=" + Date.now();
    const evtSource = new EventSource(url);
    
    let lastPercent = 0;
    
    // Progress update event
    evtSource.addEventListener('progress', function(event) {
        try {
            const data = JSON.parse(event.data);
            updateProgressBar(data.percent, data.message);
            lastPercent = data.percent;
        } catch (e) {
            console.error('Error parsing progress event:', e);
        }
    });
    
    // Completion event
    evtSource.addEventListener('complete', function(event) {
        try {
            const data = JSON.parse(event.data);
            
            // Update to 100%
            updateProgressBar(100, 'Processing complete!');
            
            // Close connection
            evtSource.close();
            
            // Show success message
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Processing Completed!',
                    html: `
                        <p>${data.message || 'Totals processed successfully!'}</p>
                        ${data.totalGrossPays ? `<p class="mb-0"><strong>Total Gross Pays:</strong> ${formatCurrency(data.totalGrossPays)}</p>` : ''}
                    `,
                    confirmButtonText: 'OK',
                    timer: 5000
                }).then(() => {
                    // Optionally reload or redirect
                    // window.location.reload();
                });
            }, 500);
        } catch (e) {
            console.error('Error parsing complete event:', e);
            evtSource.close();
            showError('Processing completed but response parsing failed.');
        }
    });
    
    // Error event from server
    evtSource.addEventListener('error', function(event) {
        console.error('SSE error event:', event);
        evtSource.close();
        
        let errorMessage = 'An error occurred while processing totals.';
        
        try {
            if (event.data) {
                const data = JSON.parse(event.data);
                if (data && data.message) {
                    errorMessage = data.message;
                }
            }
        } catch (e) {
            console.error('Error parsing error event data:', e);
        }
        
        showError(errorMessage);
    });
    
    // Connection error handler
    evtSource.onerror = function(error) {
        console.error('EventSource connection error:', error);
        
        // Only show error if we haven't completed successfully
        if (lastPercent < 100) {
            evtSource.close();
            
            // Check if it's a connection error vs server error
            if (evtSource.readyState === EventSource.CLOSED) {
                showError('Connection lost. Please check your internet connection and try again.');
            } else {
                showError('An unexpected error occurred. Please try again.');
            }
        }
    };
}

/**
 * Update progress bar and message
 */
function updateProgressBar(percent, message) {
    const progressBar = document.getElementById('swal-progress-bar');
    const progressMessage = document.getElementById('swal-progress-message');
    
    if (progressBar) {
        progressBar.style.width = percent + '%';
        progressBar.setAttribute('aria-valuenow', percent);
        progressBar.textContent = Math.round(percent) + '%';
    }
    
    if (progressMessage) {
        progressMessage.textContent = message;
    }
}

/**
 * Show error message
 */
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Processing Error',
        text: message,
        confirmButtonText: 'OK'
    });
}
function formatCurrency(amount) {
    return new Intl.NumberFormat('en-KE', {
        style: 'currency',
        currency: 'KES'
    }).format(amount);
}
    let currentPage = 1;
        let currentSearch = '';

        // Load deductions
        function loadDeductions(page = 1, search = '') {
            currentPage = page;
            currentSearch = search;

            $.ajax({
                url: getwuth,
                type: 'GET',
                data: {
                    page: page,
                    search: search,
                    per_page: 10
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="7" class="text-center">
                                <i class="fa fa-spinner fa-spin"></i> Loading...
                            </td>
                        </tr>
                    `);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Update period info
                        $('#period-info').html(`
                            <i class="fa fa-calendar"></i> 
                            <strong>Active Period:</strong> ${response.period.month} ${response.period.year}
                        `);

                        // Update table
                        renderTable(response.data);

                        // Update pagination
                        renderPagination(response.pagination);

                        // Update record count
                        $('#total-records').text(`Total Records: ${response.pagination.total}`);
                        $('#showing-info').text(
                            `Showing ${response.pagination.from || 0} to ${response.pagination.to || 0} of ${response.pagination.total} entries`
                        );
                    } else {
                        $('#tableBody').html(`
                            <tr>
                                <td colspan="7" class="text-center text-danger">
                                    ${response.message}
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Load error:', error);
                    $('#tableBody').html(`
                        <tr>
                            <td colspan="7" class="text-center text-danger">
                                Error loading data. Please try again.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Render table rows
        function renderTable(data) {
            if (data.length === 0) {
                $('#tableBody').html(`
                    <tr>
                        <td colspan="7" class="text-center">No records found</td>
                    </tr>
                `);
                return;
            }

            let html = '';
            data.forEach(row => {
                html += `
                    <tr class="clickable-row" onclick="highlightRow(this)">
                        <td>${row.full_name}</td>
                        <td>${row.work_no}</td>
                        <td>${row.department}</td>
                        <td>${row.code}</td>
                        <td>${row.name}</td>
                        <td>${row.category}</td>
                        <td class="text-right">${row.amount}</td>
                    </tr>
                `;
            });
            $('#tableBody').html(html);
        }

        // Render pagination
        function renderPagination(pagination) {
            if (pagination.last_page <= 1) {
                $('#pagination').html('');
                return;
            }

            let html = '';
            
            // Previous button
            if (pagination.current_page > 1) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
                </li>`;
            }

            // Page numbers
            for (let i = 1; i <= pagination.last_page; i++) {
                if (
                    i === 1 || 
                    i === pagination.last_page || 
                    (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)
                ) {
                    html += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>`;
                } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                    html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }
            }

            // Next button
            if (pagination.current_page < pagination.last_page) {
                html += `<li class="page-item">
                    <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
                </li>`;
            }

            $('#pagination').html(html);
        }

        // Highlight row on click
        function highlightRow(row) {
            $(row).siblings().removeClass('table-active');
            $(row).addClass('table-active');
        }

        // Event handlers
        $(document).ready(function() {
            $('#contentTable2').DataTable({
        columnDefs: [
            { targets: 2, width: '80px' } // Department column
        ],
        autoWidth: false,
        data: [], // Start with empty data
        columns: [
            { title: "Name" },
            { title: "Work number" },
            { title: "Department" },
            { title: "Parameter Code" },
            { title: "Amount" }
        ]
    });

                $('#pitem').on('change', function() {
        var selectedParameter = $(this).val();
        if (selectedParameter) {
            fetchData(selectedParameter);
        } else {
            function clearTable() {
        $('#contentTable2 tbody').empty();
    }
        }
    });
            // Initial load
            loadDeductions();

            // Search button
            $('#searchBtn').on('click', function() {
                const search = $('#searchInput').val();
                loadDeductions(1, search);
            });

            // Clear button
            $('#clearBtn').on('click', function() {
                $('#searchInput').val('');
                loadDeductions(1, '');
            });

            // Search on Enter key
            $('#searchInput').on('keypress', function(e) {
                if (e.which === 13) {
                    $('#searchBtn').click();
                }
            });

            // Pagination click
            $(document).on('click', '#pagination a', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    loadDeductions(page, currentSearch);
                }
            });
            $('.toggle-checkbox').on('change', function () {
    let checkbox = $(this);
    let isChecked = checkbox.is(':checked');
    let model = checkbox.data('model');
    let fieldName = checkbox.next('label').text();
    let action = isChecked ? 'activate' : 'deactivate';

    // Revert temporarily
    checkbox.prop('checked', !isChecked);

    if (confirm(`Are you sure you want to ${action} ${fieldName}?`)) {

        $.ajax({
            url: tstatus,
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                model: model,
                status: isChecked ? 'ACTIVE' : 'INACTIVE'
            },
            success: function (response) {
                checkbox.prop('checked', isChecked);
                showToast('success', 'Success!', fieldName + " updated successfully.");
            },
            error: function () {
                showToast('danger', 'Invalid file', "Error updating " + fieldName);
            }
        });

    }
});



        });


function cleartxt2(){
        $('#balance').val('');
        $('#balend').val('');
        $('#duration').val('');
        $('#quantity').val('');
        $('#camountf').val('');
    }
    function searchstaffdet(){ 
    performSearch();
}

function showToast(type, title, message) {
        const wrap  = document.getElementById('toastWrap');
        const icons = { success: 'check_circle', danger: 'error_outline', warning: 'warning_amber' };
        const t = document.createElement('div');
        t.className = `toast-msg ${type}`;
        t.innerHTML = `<span class="material-icons">${icons[type]}</span>
                       <div><strong>${title}</strong> ${message}</div>`;
        wrap.appendChild(t);
        const dismiss = () => { t.classList.add('leaving'); setTimeout(() => t.remove(), 300); };
        t.addEventListener('click', dismiss);
        setTimeout(dismiss, 5000);
    }








    
    $('#viewp').on('click', function () {
    var staffid = $('#WorkNo').val();
    var selperiod = $('#periodPick').val();

    if (!staffid) {
        showToast('danger', 'Invalid!', 'Work Number cannot be empty');
        return;
    }

    if (!selperiod) {
        showToast('danger', 'Invalid!', 'Please select a period');
        return;
    }

    var [year, month] = selperiod.split('-');
    var monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    var monthName = monthNames[parseInt(month) - 1];
    var period = monthName + year;

    $.ajax({
        url: 'payview', // Your PHP route
        method: 'POST',
        data: { staffid: staffid, period: period },
        success: function (response) {
            if (response.pdf) {
                // Create PDF blob and show it in modal
                var pdfBlob = new Blob([Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))], { type: 'application/pdf' });
                var pdfUrl = URL.createObjectURL(pdfBlob);

                $('#pdfModal .modal-body').html(
                    `<iframe src="${pdfUrl}" width="100%" height="600px" style="border:none;"></iframe>`
                );
                $('#pdfModal').modal('show');
            } else {
                showMessage("PDF generation failed.", true);
            }
        },
        error: function () {
            showMessage("Error generating payslip.", true);
        }
    });
});



    document.getElementById('recalcButton').addEventListener('click', function() {
    var workNo = document.getElementById('WorkNo').value.trim();
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;

    // Validate that WorkNo is not empty
    if (!workNo) {
        showToast('danger', 'Invalid!','Select a staff to process');
        return; // Exit the function if WorkNo is empty
    }

    // Create a new XMLHttpRequest object
    var xhr = new XMLHttpRequest();

    // Configure it: POST-request to autocalc2.php
    xhr.open('POST', 'autocalc2.php', true);

    // Set the Content-Type header to JSON
    xhr.setRequestHeader('Content-Type', 'application/json');

    // Send the request with the data as JSON
    xhr.send(JSON.stringify({
        WorkNo: workNo,
        month: month,
        year: year
    }));

    // Define what happens on successful data submission
    xhr.onload = function() {
        if (xhr.status === 200) {
            showToast('success', 'Success!','Recalculation successful');
            var response = JSON.parse(xhr.responseText);
            console.log(response);
            // You can add code here to handle the response from autocalc2.php
        } else {
            console.error('Recalculation failed. Status:', xhr.status);
            showToast('danger', 'Error!', 'Recalculation failed. Please try again.');
        }
    };
});
        $(document).ready(function() {
   
    $('#employeeModal').on('hidden.bs.modal', function () { 
        $('#employeeDataTable tbody').empty();
    });

    // Attach click event to table rows in employeeListTable
    /*$('#employeeListTable').on('click', 'tr', function() {
        var workNo = $(this).find('td:first').text(); // Get WorkNo from the first column of the clicked row
        $('#WorkNo').val(workNo); // Set the WorkNo input field
        performSearch2(); // Call the search function
        $('#employeeListModal').modal('hide'); // Hide the modal
    });*/

document.getElementById('inputPCode').addEventListener('keydown', function(event) {
    // Check if the key pressed is Tab (9) or Enter (13)
    if (event.key === 'Tab' || event.key === 'Enter') {
        var pCode = this.value;

        if (pCode.length > 0) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'sepcode.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);

                    if (response.success) {
                        document.getElementById('inputname').value = response.cname;
                    } else {
                        document.getElementById('inputname').value = '';

                        // Populate the modal with the list of codes
                        var codeList = document.getElementById('codeList');
                        codeList.innerHTML = '';  // Clear previous list

                        response.codeList.forEach(function(codeObj) {
                            var li = document.createElement('li');
                            li.className = 'list-group-item';
                            li.textContent = codeObj.code + ' - ' + codeObj.cname;
                            li.setAttribute('data-code', codeObj.code);
                            li.setAttribute('data-cname', codeObj.cname);
                            codeList.appendChild(li);
                        });

                        // Show the modal
                        $('#codeSelectionModal').modal('show');
                    }
                }
            };
            xhr.send('PCode=' + encodeURIComponent(pCode));
        } else {
            document.getElementById('inputname').value = '';
        }

        // Prevent the default behavior of the Tab key (i.e., moving focus)
        if (event.key === 'Tab') {
            event.preventDefault();
        }
    }
});

// Handle code selection from the modal
document.getElementById('codeList').addEventListener('click', function(e) {
    if (e.target && e.target.nodeName == "LI") {
        var selectedCode = e.target.getAttribute('data-code');
        var selectedCname = e.target.getAttribute('data-cname');

        // Set the selected code and cname to the respective input fields
        document.getElementById('inputPCode').value = selectedCode;
        document.getElementById('inputname').value = selectedCname;

        // Hide the modal
        $('#codeSelectionModal').modal('hide');
    }
});


// Handle code selection from the modal
document.getElementById('codeList').addEventListener('click', function(e) {
    if (e.target && e.target.nodeName == "LI") {
        var selectedCode = e.target.getAttribute('data-code');
        var selectedCname = e.target.getAttribute('data-cname');

        // Set the selected code and cname to the respective input fields
        document.getElementById('inputPCode').value = selectedCode;
        document.getElementById('inputname').value = selectedCname;

        // Hide the modal
        $('#codeSelectionModal').modal('hide');
    }
});


// Search functionality


// Submit button functionality
document.getElementById('submitButton').addEventListener('click', function() {
    var id = document.getElementById('inputID').value;
    var pCode = document.getElementById('inputPCode').value;
    var name = document.getElementById('inputname').value;
    var amount = document.getElementById('inputAmount').value;
    var balance = document.getElementById('inputBalance').value;
    var WorkNo = document.getElementById('WorkNo').value;
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;

    

    if (!pCode || !name || !amount || !balance || !WorkNo) {
        showErrorMessage('Please fill in the required inputs');
        return;
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'subEpcode.php', true); 
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                showMessage('Payroll Item submitted ', false);
                
                console.log(response);
                // Clear the form
                document.getElementById('inputID').value = '';
                document.getElementById('inputPCode').value = '';
                document.getElementById('inputname').value = '';
                document.getElementById('inputAmount').value = '';
                document.getElementById('inputBalance').value = '';
                // Refresh the table
                performSearch2(); // Use this instead of loadEmployeeData()
            } else {
                alert('Error submitting data: ' + response.message);
            }
        }
    };
    xhr.send('id=' + encodeURIComponent(id) +
         '&pCode=' + encodeURIComponent(pCode) +
         '&name=' + encodeURIComponent(name) +
         '&amount=' + encodeURIComponent(amount) +
         '&balance=' + encodeURIComponent(balance) +
         '&WorkNo=' + encodeURIComponent(WorkNo) +
         '&month=' + encodeURIComponent(month) +
         '&year=' + encodeURIComponent(year));
});

function highlightAndPopulateRow(row) {
    $('#employeeDataTable tbody tr').removeClass('highlighted');
    $(row).addClass('highlighted');
    
    $('#inputID').val($(row).find('td:eq(0)').text());
    $('#inputPCode').val($(row).find('td:eq(1)').text());
    $('#inputname').val($(row).find('td:eq(2)').text());
    $('#inputAmount').val($(row).find('td:eq(3)').text());
    $('#inputBalance').val($(row).find('td:eq(4)').text());
}

function highlightRow(row) {
    $('#employeeListTable tbody tr').removeClass('highlighted');
    $(row).addClass('highlighted');
    
    $('#WorkNo').val($(row).find('td:eq(0)').text());
    $('#employeeListModal').modal('hide');
    
    performSearch2();
}

$('<style>')
    .prop("type", "text/css")
    .html(`
        .highlighted {
            background-color: #e6f3ff !important;
        }
    `)
    .appendTo("head");

$('#searchButton').on('click', performSearch2);




// Add event listener for selecting an employee from the list
/*$(document).on('click', '.select-employee', function(e) {
    e.preventDefault();
    var workNo = $(this).data('workno');
    $('#WorkNo').val(workNo);
    $('#employeeListModal').modal('hide');
    performSearch2();
});*/
});
function highlightAndPopulateRow(row) {
    // Remove highlight from all rows
    $('#employeeDataTable tbody tr').removeClass('highlight');
    
    // Add highlight to clicked row
    $(row).addClass('highlight');
    
    // Populate input fields
    $('#inputID').val($(row).find('td:eq(0)').text());
    $('#inputPCode').val($(row).find('td:eq(1)').text());
    $('#inputname').val($(row).find('td:eq(2)').text());
    $('#inputAmount').val($(row).find('td:eq(3)').text());
    $('#inputBalance').val($(row).find('td:eq(4)').text());
}

function cleartxt(){
                    $('#inputID').val('');
                    $('#inputPCode').val('');
                    $('#inputname').val('');
                    $('#inputAmount').val('');
                    $('#inputBalance').val('');
}
    // Get the current date, month, and year










     function highlightRow(row) {
    var tableRows = document.getElementById('contentTable').getElementsByTagName('tr');
    for (var i = 0; i < tableRows.length; i++) {
        tableRows[i].classList.remove('highlight');
    }
    row.classList.add('highlight');
}

function showErrorMessage(message) {
    let errorDiv = document.getElementById('errorMessage');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = 'errorMessage';
        document.body.appendChild(errorDiv);
    }
    
    errorDiv.textContent = message;
    errorDiv.style.position = 'fixed';
    errorDiv.style.top = '20px';
    errorDiv.style.left = '20px';
    errorDiv.style.backgroundColor = '#f44336';
    errorDiv.style.color = 'white';
    errorDiv.style.padding = '15px';
    errorDiv.style.borderRadius = '5px';
    errorDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
    errorDiv.style.zIndex = '1051'; 
    errorDiv.style.display = 'block';
    
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    var rows = document.querySelectorAll("#contentTable tbody tr");
    var viewButton = document.querySelector('.btn-info');

    rows.forEach(function(row) {
        row.addEventListener("click", function() {
            highlightRow(this);
        });
    });

    viewButton.addEventListener("click", function() {
        var selectedRow = document.querySelector("#contentTable tbody tr.highlight");
        if (selectedRow) {
            var loanshares = selectedRow.cells[5].textContent; 
            var workNo = selectedRow.cells[1].textContent; 
            var pCode = selectedRow.cells[3].textContent; 

            if (loanshares.toLowerCase() === 'loan') {
                window.location.href = 'loansched.php?empid=' + encodeURIComponent(workNo) + '&loantype=' + encodeURIComponent(pCode);
            } else {
                showErrorMessage('Please select a loan category.');
            }
        } else {
            showErrorMessage('Please select a row first.');
        }
    });
});


    









function clearFields2() {     
    $('#empname').val('');
    $('#empname1').val('');
    $('#inputPCode').val('');
    $('#inputname').val('');
    $('#inputAmount').val('');
    $('#inputBalance').val('');
}
function searchstaffdet(){ 
    performSearch();
}
function searchstaffdet2(){ 
    performSearch2();
}
function performSearch2() {
    var workNo = $('#WorkNo').val();
    var month = $('#month').val();
    var year = $('#year').val();
    cleartxt();
    
    $.ajax({
        url: 'search2.php',
        method: 'POST',
        data: {
            workNo: workNo,
            month: month,
            year: year
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#empname1').val(response.employee.Surname);
                $('#empname').val(response.employee.othername);
                $('#empdept').val(response.employee.dept);
                $('#WorkNo').val(workNo);
                
                var tableBody = $('#employeeDataTable tbody');
                tableBody.empty();
                
                $.each(response.deductions, function(index, deduction) {
                    var formattedDate = new Date(deduction.dateposted);
                    var options = { year: 'numeric', month: 'short', day: '2-digit' };
                    var formattedDateString = formattedDate.toLocaleDateString('en-US', options);
                    var row = '<tr onclick="highlightAndPopulateRow(this)">' +
                        '<td hidden>' + deduction.ID + '</td>' +
                        '<td>' + deduction.PCode + '</td>' +
                        '<td>' + deduction.pcate + '</td>' +
                        
                        '<td>' + deduction.Amount + '</td>' +
                        '<td>' + deduction.balance + '</td>' +
                        '<td>' + formattedDateString + '</td>' +
                        '<td hidden>' + deduction.loanshares + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
            } else {
                var tableBody = $('#employeeListTable tbody');
                tableBody.empty();
                
                $.each(response.employeeList, function(index, employee) {
                    var row = '<tr onclick="highlightRow(this)">' +
                        '<td>' + employee.WorkNo + '</td>' +
                        '<td>' + employee.Surname + ' ' + employee.othername + '</td>' +
                        '</tr>';
                    tableBody.append(row);
                });
                
                $('#employeeListModal').modal('show');
            }
        },
        error: function() {
            alert('An error occurred while searching');
        }
    });
}




       
function executeFormula() {
    var formula = $('#formular').val(); // Get the formula
    var amounts = $('#camountf').val().split(','); // Get amounts from #camountf
    var quantity = parseFloat($('#quantity').val()); // Get the quantity value
    var codes = formula.match(/[A-Za-z]+\d+/g); // Extract codes from the formula

    // Replace codes in the formula with corresponding amounts or quantity
    codes.forEach(function(code, index) {
        var amount = amounts[index];
        if (amount === '') {
            amount = quantity; // Use quantity if the amount is missing
        }
        formula = formula.replace(code, amount);
    });

    try {
        // Evaluate the formula safely
        var result = eval(formula);
        // Format the result to 2 decimal places
        $('#amount').val(result.toFixed(2)); // Display the result with 2 decimal places
    } catch (error) {
        console.error('Error evaluating formula:', error);
        $('#amount').val('Error'); // Indicate an error if evaluation fails
    }
}
$(document).ready(function() {
     // Simple implementation for your dropdown

  const choices2 = new Choices('#WorkNo', {
    searchEnabled: true,
    placeholderValue: 'Select Staff',
     searchPlaceholderValue: 'Search staff...',
    allowHTML: true
}); 
    document.getElementById('quantity').addEventListener('input', executeFormula);

    $('#pitem').on('change', function() {
        var selectedParameter = $(this).val();
        if (selectedParameter) {
            fetchData(selectedParameter);
        } else {
            function clearTable() {
        $('#contentTable2 tbody').empty();
    }
        }
    });

   

   




    function clearTable() {
        $('#contentTable2 tbody').empty();
    }

    

    function showErrorMessage(message) {
        let errorDiv = document.getElementById('errorMessage');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'errorMessage';
            document.body.appendChild(errorDiv);
        }

        errorDiv.textContent = message;
        errorDiv.style.position = 'fixed';
        errorDiv.style.top = '20px';
        errorDiv.style.left = '20px';
        errorDiv.style.backgroundColor = '#f44336';
        errorDiv.style.color = 'white';
        errorDiv.style.padding = '15px';
        errorDiv.style.borderRadius = '5px';
        errorDiv.style.boxShadow = '0 2px 4px rgba(0,0,0,0.2)';
        errorDiv.style.zIndex = '1051'; // Higher than the modal's z-index
        errorDiv.style.display = 'block';

        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 2000);
    }
    
});

$(document).ready(function() {
    $('#exampleModal').on('hidden.bs.modal', function () {
        // Reset the form
        clearFields();
        //$('#payrollForm')[0].reset();
        $('#pitem').val('').trigger('change');
        //$('#searchValue').val('').trigger('change');
       //choicesSearchValue.removeActiveItems();


    });
    $('#employeeModal').on('hidden.bs.modal', function () {
        // Reset the form
        clearFields2();
    });
    

  
    
$('#btnopenot').on('click', function(e) {
    // Get values
    const workNumber = $('#workNumber').val();
    const otdate = $('#otdate').val();
    const codebal = $('#codebal').val();

    var yea2r = document.getElementById('otdate').value;

    if (!yea2r) {
        showErrorMessage('Please select a date');
        return;
    }
    
    // Parse the month and year from the otdate
    const dateObj = new Date(otdate);
    const month = dateObj.getMonth() + 1; // Months are 0-indexed
    const year = dateObj.getFullYear();

    // Send data to the server via AJAX
    $.ajax({
        url: 'searchot.php', // Update with the actual server-side URL
        type: 'POST',
        data: {
            workNumber: workNumber,
            codebal: codebal,
            month: month,
            year: year
        },
        success: function(response) {
            // Assuming the response is JSON containing records
            const records = JSON.parse(response);

            // Populate the table in the modal
            let tableBody = $('#otListTable tbody');
            tableBody.empty(); // Clear any existing rows
            
            records.forEach(record => {
                const row = `<tr>
                                <td>${record.odate}</td>
                                <td>${record.quantity}</td>
                                <td>${record.tamount}</td>
                             </tr>`;
                tableBody.append(row);
            });

            // Show the modal
            $('#otListModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error("Error fetching records: " + error);
        }
    });
});




    
});
