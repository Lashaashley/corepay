<x-custom-admin-layout>
    <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Your other head content -->
</head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    z-index: 9999;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.5s ease;
    display: none; /* Initially hidden via JS, but transition handled by opacity/transform */
}

.custom-alert.show {
    opacity: 1;
    transform: translateX(0);
    display: block; /* Needed to make it visible */
}

.alert-success {
    animation: successPulse 1s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(0.95); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}  	.tab-container {
    display: flex;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
}

.tab-button {
    background-color: #f8f9fa;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 20px;
    font-size: 14px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff; /* Hide border bottom when active */
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}

	.btn-enhanced {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    cursor: pointer;
    min-width: 80px;
}

.btn-enhanced:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-draft {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #ffc107, #ff8c00);
            color: white;
        }  
.btn-final {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
                #staffrpt-pdf-container iframe {
    width: 100%;
    height: 80vh;
    border: none;
}
.btn-download {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
.btn-download:hover {
    background: linear-gradient(135deg, #218838 0%, #17a589 100%);
}

/* --- Print Button (Info Gradient) --- */
.btn-print {
    background: linear-gradient(135deg, #007bff 0%, #00b4d8 100%);
}
.btn-print:hover {
    background: linear-gradient(135deg, #0069d9 0%, #0096c7 100%);
}
.modal-xl {
    max-width: 90%;
}

.modal-body {
    padding: 0;
}
@keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
       
    </style>

    <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert">
    <strong id="alert-title"></strong> <span id="alert-message"></span>
    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


    <div class="mobile-menu-overlay"></div>
    <div class="pd-ltr-20">
            
            <div class="tab-container" style="margin-top: -20px;">
                <button class="tab-button active" onclick="openTab(event, 'deductions')">Create Roles</button>
                <button class="tab-button" id="summaries-tab" onclick="openTab(event, 'summaries')">Modules Allocation</button>
                
            </div>
            <div id="deductions" class="tab-content active" style="margin-top: -20px;">
               <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Roles</h2>
                                <section>
                                    <form id="rolesform" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Name:</label> 
                                                <input name="rolename" id="rolename" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Description:</label> 
                                                <textarea name="rdesc" id="rdesc" type="text" class="form-control"  autocomplete="off"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-save"></i>Create
                                    </button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Roles</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="roles-table-body"></tbody>
                                </table>
                                <div id="pagination-controls" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            
            <div id="summaries" class="tab-content" style="margin-top: -20px;">
                <div class="card-box pd-20 height-100-p mb-30" >
                    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Assign Modules to Roles</h4>
                </div>
                <div class="card-body">
                    <form id="moduleAssignForm">
                        @csrf
                        
                        <!-- User Selection -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="users">Select Role <span class="text-danger">*</span></label>
                                <select name="roleid" id="roleid" class="custom-select form-control" required>
                                </select>
                                <small class="text-danger" id="roleid-error"></small>
                            </div>
                        </div>

                        <!-- Modules/Buttons Section -->
                        <div class="card-box pd-20 mb-4" style="max-height: 500px; overflow-y: auto;">
                            <h5 class="text-center mb-4">Available Modules</h5>
                            
                            <div id="modulesContainer">
                                @php
                                    function renderButtons($buttons, $parentId = null) {
                                        $html = '<ul class="list-unstyled">';
                                        
                                        foreach ($buttons as $button) {
                                            if ($button->parentid == $parentId) {
                                                $html .= '<li style="margin-left: ' . ($parentId ? '20px' : '0') . ';">';
                                                $html .= '<div class="form-check mb-2">';
                                                $html .= '<input class="form-check-input module-checkbox" type="checkbox" name="modules[]" value="' . $button->ID . '" id="module' . $button->ID . '"';
                                                
                                                // Add parent class if it's a parent button
                                                if ($button->isparent == 'YES') {
                                                    $html .= ' data-parent="true" data-button-id="' . $button->ID . '"';
                                                } else if ($parentId) {
                                                    $html .= ' data-child-of="' . $parentId . '"';
                                                }
                                                
                                                $html .= '>';
                                                $html .= '<label class="form-check-label" for="module' . $button->ID . '">';
                                                
                                                // Add icon if exists
                                                if ($button->icon) {
                                                    $html .= '<i class="' . $button->icon . '"></i> ';
                                                }
                                                
                                                $html .= htmlspecialchars($button->Bname);
                                                $html .= '</label>';
                                                $html .= '</div>';
                                                
                                                // Recursively render children if this is a parent
                                                if ($button->isparent == 'YES') {
                                                    $html .= renderButtons($buttons, $button->ID);
                                                }
                                                
                                                $html .= '</li>';
                                            }
                                        }
                                        
                                        $html .= '</ul>';
                                        return $html;
                                    }
                                    
                                    echo renderButtons($buttons);
                                @endphp
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row">
                            <div class="col-md-3">
                                <button type="button" class="btn btn-enhanced btn-finalize" id="assignBtn">
                                    <i class="fas fa-check"></i> Assign Modules
                                </button>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="button" class="btn btn-enhanced btn-draft" id="selectAllBtn">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                            </div>
                            
                            <div class="col-md-2">
                                <button type="button" class="btn btn-enhanced btn-cancel" id="deselectAllBtn">
                                    <i class="fas fa-times"></i> Deselect All
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('roles.report') }}" class="btn btn-enhanced btn-draft" target="_blank">
                                    <i class="fa fa-file-pdf"></i>Roles Report
                                </a>
                            </div>
                            </div>
                        </div>
                    </form>

                    <!-- Alert Container -->
                    <div id="alertContainer" class="mt-4"></div>
                </div>
            </div>
        </div>
                    
                </div>
                
            </div>
           
            <div id="binterface" class="tab-content" >
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Immediate Fund Transfer(IFT)</h5>
                    
                </div>
                
                
            </div>
        </div>
<div class="modal fade" id="editstreamModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stream</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editstreamForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                
                    <div class="form-group">
                        <label for="schoolPobox">Name:</label>
                        <input type="text" class="form-control" id="editstrmname" name="strmname">
                        <span class="text-danger" id="strmname-error"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editstreamForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="editcampusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editcampuslForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                
                    <div class="form-group">
                        <label for="schoolPobox">Role:</label>
                        <input type="text" class="form-control" id="editrolename" name="rolename">
                        <span class="text-danger" id="rolename-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="schoolPobox">Description:</label> 
                        <textarea name="rdesc" id="editrdesc" type="text" class="form-control" autocomplete="off"></textarea>
                   
                        <span class="text-danger" id="rdesc-error"></span>
                    </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editcampuslForm" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-check-circle"></i>Save changes
                                    </button>
                        
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<!--<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            loadroles();
            $('#rolesform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('roles.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#rolesform')[0].reset();
                        loadroles();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $(document).on('click', '[data-target="#editcampusModal"]', function () {
    const id = $(this).data('id');
    const rolename = $(this).data('rolename');
     const rdesc = $(this).data('rdesc');
    

    // Clear previous errors
    $('.text-danger').html(''); 
    
    // Set form values
    const form = $('#editcampuslForm');
    form.find('#ID').val(id);
    form.find('#editrolename').val(rolename);
    form.find('#editrdesc').val(rdesc);
   
});

$('#editcampuslForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editcampusModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({ 
                    url: `{{ url('roles') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editcampusModal').modal('hide');
                        loadroles(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
         });
          
function openTab(evt, tabName) { 
    var i, tabContent, tabButton;

    // Hide all tab content
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }

    // Remove the "active" class from all tab buttons
    tabButton = document.getElementsByClassName("tab-button");
    for (i = 0; i < tabButton.length; i++) {
        tabButton[i].className = tabButton[i].className.replace(" active", "");
    }

    // Show the current tab and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

function loadroles(page = 1) {
    $.ajax({
        url: "{{ route('roles.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#roles-table-body');
            const paginationControls = $('#pagination-controls');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.rolename}</td>
                    <td>${row.rdesc}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editcampusModal"
                                    data-id="${row.ID}"
                                    data-rolename="${row.rolename}"
                                    data-rdesc="${row.rdesc}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.branchname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

           
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
$(document).on('click', '#pagination-controls button', function () {
    const page = $(this).data('page');
    loadcampuses(page);
});
$('#summaries-tab').on('click', function() {
$.ajax({
        url: "{{ route('roles.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#roleid');

            dropdown.empty();
            dropdown.append('<option value="">Select Role</option>');
            response.data.forEach(function (roles) {
                dropdown.append(
                    `<option value="${roles.ID}">${roles.rolename}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load roles. Please try again.');
        },
    });

     $('#roleid').on('change', function() {
        const roleid = $(this).val();
        
        // Clear all checkboxes first
        $('.module-checkbox').prop('checked', false);
        
        if (!roleid) {
            return;
        }

        // Show loading state
        $('#modulesContainer').css('opacity', '0.5');
        
        $.ajax({
            url: "{{ route('modules.getRoleModules') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                roleid: roleid
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Check the assigned modules
                    response.buttonIds.forEach(function(buttonId) {
                        $('#module' + buttonId).prop('checked', true);
                    });
                    
                    showAlert('info', 'Loaded', 'Role modules loaded successfully');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error', 'Failed to load Role modules');
            },
            complete: function() {
                $('#modulesContainer').css('opacity', '1');
            }
        });
    });
});
$('#assignBtn').on('click', function() {
        const roleid = $('#roleid').val();
        const selectedModules = [];
        
        $('.module-checkbox:checked').each(function() {
            selectedModules.push($(this).val());
        });
        
        // Clear previous errors
        $('.text-danger').html('');
        
        if (!roleid) {
            $('#users-error').html('Please select a Role');
            showAlert('warning', 'Validation Error', 'Please select a Role');
            return;
        }
        
        if (selectedModules.length === 0) {
            showAlert('warning', 'Validation Error', 'Please select at least one module');
            return;
        }

        const btn = $(this);
        const originalText = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Assigning...').prop('disabled', true);
        
        $.ajax({
            url: "{{ route('modules.save') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                roleid: roleid,
                modules: selectedModules
            },
            success: function(response) {
                if (response.status === 'success') {
                    showAlert('success', 'Success!', response.message);
                    // Optionally reset form
                    // $('#moduleAssignForm')[0].reset();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').html(value[0]);
                    });
                    showAlert('danger', 'Validation Error', 'Please check the form for errors');
                } else {
                    showAlert('danger', 'Error', xhr.responseJSON?.message || 'Failed to assign modules');
                }
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });
 $('#selectAllBtn').on('click', function() {
        $('.module-checkbox').prop('checked', true);
        showAlert('info', 'Selected', 'All modules selected');
    });

    // Deselect all modules
    $('#deselectAllBtn').on('click', function() {
        $('.module-checkbox').prop('checked', false);
        showAlert('info', 'Cleared', 'All selections cleared');
    });

    // Auto-select children when parent is checked
    $(document).on('change', '.module-checkbox[data-parent="true"]', function() {
        const buttonId = $(this).data('button-id');
        const isChecked = $(this).is(':checked');
        
        // Check/uncheck all children
        $('.module-checkbox[data-child-of="' + buttonId + '"]').prop('checked', isChecked);
    });
function showAlert(type, title, message) {
                const statusMessage = $('#status-message');
                $('#alert-title').html(title);
                $('#alert-message').html(message);
                
                statusMessage
                    .removeClass('alert-success alert-danger')
                    .addClass(`alert-${type}`)
                    .css('display', 'block')
                    .addClass('show');
                
                // Auto hide after 5 seconds if not manually closed
                setTimeout(() => {
                    if (statusMessage.hasClass('show')) {
                        statusMessage.removeClass('show');
                        setTimeout(() => {
                            statusMessage.hide();
                        }, 500);
                    }
                }, 5000);
            }
            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => {
                    alert.hide();
                }, 500);
            });

    </script>
</x-custom-admin-layout>
