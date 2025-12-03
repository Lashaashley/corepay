<x-custom-admin-layout>
    <style>

@keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
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
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            transform: translateX(400px);
            transition: all 0.5s ease;
        }
        
        .custom-alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            animation: successPulse 1s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
   
    </style>
    <div class="mobile-menu-overlay"></div>
   
	<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Assign Modules to Users</h4>
                </div>
                <div class="card-body">
                    <form id="moduleAssignForm">
                        @csrf
                        
                        <!-- User Selection -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="users">Select User <span class="text-danger">*</span></label>
                                <select name="users" id="users" class="custom-select form-control" required>
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="users-error"></small>
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
                            <div class="col-md-12">
                                <button type="button" class="btn btn-primary" id="assignBtn">
                                    <i class="fas fa-check"></i> Assign Modules
                                </button>
                                <button type="button" class="btn btn-secondary" id="selectAllBtn">
                                    <i class="fas fa-check-double"></i> Select All
                                </button>
                                <button type="button" class="btn btn-warning" id="deselectAllBtn">
                                    <i class="fas fa-times"></i> Deselect All
                                </button>
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
    

    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    
    
     
    <script>

      $(document).ready(function() {
    
    // Load user modules when user is selected
    $('#users').on('change', function() {
        const userId = $(this).val();
        
        // Clear all checkboxes first
        $('.module-checkbox').prop('checked', false);
        
        if (!userId) {
            return;
        }

        // Show loading state
        $('#modulesContainer').css('opacity', '0.5');
        
        $.ajax({
            url: "{{ route('modules.getUserModules') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                workNo: userId
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Check the assigned modules
                    response.buttonIds.forEach(function(buttonId) {
                        $('#module' + buttonId).prop('checked', true);
                    });
                    
                    showAlert('info', 'Loaded', 'User modules loaded successfully');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error', 'Failed to load user modules');
            },
            complete: function() {
                $('#modulesContainer').css('opacity', '1');
            }
        });
    });

    // Assign modules
    $('#assignBtn').on('click', function() {
        const userId = $('#users').val();
        const selectedModules = [];
        
        $('.module-checkbox:checked').each(function() {
            selectedModules.push($(this).val());
        });
        
        // Clear previous errors
        $('.text-danger').html('');
        
        if (!userId) {
            $('#users-error').html('Please select a user');
            showAlert('warning', 'Validation Error', 'Please select a user');
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
            url: "{{ route('modules.assign') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                workNo: userId,
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

    // Select all modules
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

    // Show alert function
    function showAlert(type, title, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <strong>${title}:</strong> ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        
        $('#alertContainer').html(alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }
});
    </script>
</x-custom-admin-layout>