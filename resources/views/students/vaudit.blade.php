<x-custom-admin-layout>
 <style>
    .action-buttons {
            padding: 1px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-draft {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1);
            color: white;
        }
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #e93a04ff, #d62f05ff);
            color: white;
        }  
 </style>  
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="card-box mb-30">
                <!-- Universal Filter Panel -->
<div class="card mb-4">
    <div class="card-header">
        <h5>Audit Trail Filters</h5>
    </div>
    <div class="card-body">
        <form id="auditFilterForm">
            <div class="row">
                <!-- Report Type -->
                <div class="col-md-3">
                    <label>Report Type</label>
                    <select name="report_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="user_activity">User Activity</option>
                        <option value="action_type">Action Type</option>
                        <option value="record_history">Record History</option>
                        <option value="table_activity">Table Activity</option>
                        <option value="comprehensive">Comprehensive</option>
                    </select>
                </div>
                
                <!-- User Filter (Dynamic - shows based on report type) -->
                <div class="col-md-3" id="user_filter" style="display:none;">
                    <label>User</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        <!-- Load users dynamically -->
                    </select>
                </div>
                
                <!-- Action Filter -->
                <div class="col-md-3" id="action_filter" style="display:none;">
                    <label>Action</label>
                    <select name="action" class="form-control">
                        <option value="">All Actions</option>
                        <option value="INSERT">INSERT</option>
                        <option value="UPDATE">UPDATE</option>
                        <option value="DELETE">DELETE</option>
                        <option value="LOGIN">LOGIN</option>
                        <option value="LOGOUT">LOGOUT</option>
                        <option value="ERROR">ERROR</option>
                        <option value="VIEW">VIEW</option>
                    </select>
                </div>
                
                <!-- Table Filter -->
                <div class="col-md-3" id="table_filter" style="display:none;">
                    <label>Table</label>
                    <select name="table_name" class="form-control">
                        <option value="">All Tables</option>
                        <option value="users">Users</option>
                        <option value="prolltypes">Payroll Types</option>
                        <!-- Add other tables -->
                    </select>
                </div>
                
                <!-- Record ID Filter -->
                <div class="col-md-3" id="record_filter" style="display:none;">
                    <label>Record ID</label>
                    <input type="text" name="record_id" class="form-control" placeholder="Enter Record ID">
                </div>
                
                <!-- Date Range -->
                <div class="col-md-3">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>
                
                <div class="col-md-3">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" required>
                </div>
                
                <!-- Quick Date Ranges -->
                <div class="col-md-3">
                    <label>Quick Range</label>
                    <select class="form-control" id="quick_range">
                        <option value="">Custom</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last7">Last 7 Days</option>
                        <option value="last30">Last 30 Days</option>
                        <option value="thismonth">This Month</option>
                        <option value="lastmonth">Last Month</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="row mt-3">
                    <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-search"></i> View Report
                    </button>
                    </div>
                    <div class="col-md-2">
                    <button type="button" class="btn btn-enhanced btn-finalize" id="export-excel">
                        <i class="fa fa-file-excel"></i> Export Excel
                    </button>
                    </div>
                    <div class="col-md-2">
                    <button type="button" class="btn btn-enhanced btn-cancel" id="export-pdf">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </button>
                    </div>
                </div>
                
            </div>
        </form>
    </div>
</div>
                
                
            </div>
        </div>
    </div>
    
</div>
<!-- Add this modal to your layout -->
<!-- PDF Preview Modal -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pdfPreviewModalLabel">Audit Trail PDF Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Download Button at the top -->
                <div class="p-3 bg-light border-bottom">
                    <button type="button" id="downloadPdfBtn" class="btn btn-primary">
                        <i class="fas fa-download"></i> Download PDF
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
                
                <!-- PDF Embed Container -->
                <div id="pdfContainer" style="height: 70vh; overflow: auto;">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading PDF preview...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Terminate Modal -->
    

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    
    <script> 
     
// Audit Trail DataTable
var auditTable = $('#audit-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '{{ route("audit.getData") }}',
        type: 'GET',
        data: function(d) {
            d.report_type = $('select[name="report_type"]').val();
            d.user_id = $('select[name="user_id"]').val();
            d.action = $('select[name="action"]').val();
            d.table_name = $('select[name="table_name"]').val();
            d.record_id = $('input[name="record_id"]').val();
            d.from_date = $('input[name="from_date"]').val();
            d.to_date = $('input[name="to_date"]').val();
        }
    },
    columns: [
        {
            data: null,
            orderable: false,
            className: 'details-control text-center',
            defaultContent: '<i class="fa fa-plus-circle text-primary" style="cursor:pointer;"></i>',
            width: '30px'
        },
        { data: 'id', title: 'ID' },
        { 
            data: 'created_at', 
            title: 'Date & Time',
            render: function(data) {
                return moment(data).format('YYYY-MM-DD HH:mm:ss');
            }
        },
        { 
            data: 'user_name', 
            title: 'User',
            render: function(data, type, row) {
                return `${data} (ID: ${row.user_id})`;
            }
        },
        { 
            data: 'action', 
            title: 'Action',
            render: function(data) {
                const badges = {
                    'INSERT': 'success',
                    'UPDATE': 'info',
                    'DELETE': 'danger',
                    'LOGIN': 'primary',
                    'LOGOUT': 'secondary',
                    'ERROR': 'danger',
                    'VIEW': 'light'
                };
                return `<span class="badge badge-${badges[data]}">${data}</span>`;
            }
        },
        { data: 'table_name', title: 'Table' },
        { data: 'record_id', title: 'Record ID' },
        { data: 'ip_address', title: 'IP Address' }
    ],
    order: [[1, 'desc']], // Sort by ID descending (newest first)
    pageLength: 50,
    dom: 'Bfrtip',
    buttons: [
        'copy', 'csv', 'excel', 'print'
    ]
});

// Expandable row for details
$('#audit-table tbody').on('click', 'td.details-control', function() {
    var tr = $(this).closest('tr');
    var row = auditTable.row(tr);
    var icon = $(this).find('i');

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
        icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
    } else {
        row.child(formatDetails(row.data())).show();
        tr.addClass('shown');
        icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
    }
});

// Format the child row with old/new values comparison
function formatDetails(d) {
    let oldValues = d.old_values ? JSON.parse(d.old_values) : {};
    let newValues = d.new_values ? JSON.parse(d.new_values) : {};
    let contextData = d.context_data ? JSON.parse(d.context_data) : {};
    
    let html = '<div class="row p-3">';
    
    // Old vs New Values Comparison
    if (Object.keys(oldValues).length > 0 || Object.keys(newValues).length > 0) {
        html += '<div class="col-md-12"><h6>Changes:</h6><table class="table table-sm table-bordered">';
        html += '<thead><tr><th>Field</th><th>Old Value</th><th>New Value</th></tr></thead><tbody>';
        
        let allKeys = new Set([...Object.keys(oldValues), ...Object.keys(newValues)]);
        allKeys.forEach(key => {
            let oldVal = oldValues[key] || '<em class="text-muted">N/A</em>';
            let newVal = newValues[key] || '<em class="text-muted">N/A</em>';
            let changed = oldVal !== newVal ? 'table-warning' : '';
            
            html += `<tr class="${changed}"><td><strong>${key}</strong></td><td>${oldVal}</td><td>${newVal}</td></tr>`;
        });
        
        html += '</tbody></table></div>';
    }
    
    // Context Data
    if (Object.keys(contextData).length > 0) {
        html += '<div class="col-md-6"><h6>Context:</h6><pre>' + JSON.stringify(contextData, null, 2) + '</pre></div>';
    }
    
    // User Agent
    if (d.user_agent) {
        html += '<div class="col-md-6"><h6>User Agent:</h6><p><small>' + d.user_agent + '</small></p></div>';
    }
    
    html += '</div>';
    return html;
}
 // Export to Excel
$('#export-excel').click(function() {
    const formData = $('#auditFilterForm').serialize();
    window.location.href = '{{ route("audit.exportExcel") }}?' + formData;
});

// Export to PDF
let currentFilterData = '';

$('#export-pdf').click(function(e) {
    e.preventDefault();
    
    // Get form data
    currentFilterData = $('#auditFilterForm').serialize();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
    modal.show();
    
    // Build URL with filters
    const previewUrl = '{{ route("audit.viewPdf") }}?' + currentFilterData;
    
    // Load PDF in iframe
    $('#pdfContainer').html(`
        <iframe 
            src="${previewUrl}#toolbar=0&navpanes=0&scrollbar=0" 
            width="100%" height="500px" 
            style="border: none; min-height: 70vh;"
            title="PDF Preview"
        ></iframe>
    `);
});

// Handle download button click
$(document).on('click', '#downloadPdfBtn', function() {
    // Trigger actual download
    const downloadUrl = '{{ route("audit.exportPdf") }}?' + currentFilterData;
    window.location.href = downloadUrl;
});

// Clean up on modal close
$('#pdfPreviewModal').on('hidden.bs.modal', function () {
    $('#pdfContainer').html(`
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading PDF preview...</p>
        </div>
    `);
});
  </script>
    
   
</x-custom-admin-layout>