<x-custom-admin-layout>
    <style>
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

    <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
        <strong id="alert-title"></strong> <span id="alert-message"></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    <div class="mobile-menu-overlay"></div>
        
            <div class="min-height-200px">
                <div class="pd-20 card-box mb-30">
                    <form id="feeassignModifyForm">
                        <div class="pd-ltr-20 xs-pd-20-10">
                            <h4 class="header-container">Modify Module Assignments</h4>
                            <input type="hidden" name="form_type" value="modifyassignform">
                            <div class="card-box pd-20 height-100-p mb-30">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <label for="classes">Classes</label>
                                        <select name="classid" id="classes" class="custom-select form-control" required>
                                            <option value="">Select class</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-box pd-20 height-100-p mb-30">
                                <h5 class="text-center mb-4">Fee Items</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="feeItemsContainer" class="row">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Update Fee Assignments</button>
                                    </div>
                               
                        </div>
                    </form>
                </div>
            </div>
      
   
    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="{{ asset('resources/js/core.js') }}"></script>
<script src="{{ asset('resources/js/script.min.js') }}"></script>
<script src="{{ asset('resources/js/process.js') }}"></script>
<script src="{{ asset('resources/js/layout-settings.js') }}"></script>
<!---<script src="{{ asset('js/custom-dropdown.js') }}"></script>-->
    <script>
    $(document).ready(function() {
        loadClassesDropdown();
        $('#classes').on('change', function() {
            const classId = $(this).val();
            if (classId) {
                loadFeeItemsWithAssignments(classId);
            } else {
                $('#feeItemsContainer').empty();
            }
        });
        $('#feeassignModifyForm').on('submit', function(e) {
            e.preventDefault();
            const classId = $('#classes').val();
            const selectedFeeItems = [];
            $('input[name="feeItems[]"]:checked').each(function() {
                selectedFeeItems.push($(this).val());
            });
            if (!classId) {
                showAlert('danger', 'Error!','Please select a class');
                return;
            }
            const formData = {
                classid: classId,
                feeItems: selectedFeeItems,
                _token: $('meta[name="csrf-token"]').attr('content')
            };
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
            $.ajax({
                url: "{{ route('feeassign.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    if (response.success) {
                        
                        loadFeeItemsWithAssignments(classId);
                        showAlert('success','Success!','Fee items updated successfully!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error occurred. Please try again.');
                    console.error(xhr.responseText);
                },
                complete: function() {
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        });
        function loadClassesDropdown() {
            $.ajax({
                url: "{{ route('classes.getDropdown3') }}",
                type: "GET",
                success: function(response) {
                    const dropdown = $('#classes');
                    dropdown.empty();
                    dropdown.append('<option value="">Select class</option>');
                    if (response.data && response.data.length > 0) {
                        response.data.forEach(function(classes) {
                            dropdown.append(`<option value="${classes.ID}">${classes.claname}</option>`);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error loading classes:', xhr.responseText);
                }
            });
        }
        function loadFeeItemsWithAssignments(classId) {
    // Show loading spinner
    const feeItemsContainer = $('#feeItemsContainer');
    feeItemsContainer.empty();
    feeItemsContainer.html(`
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Loading fee items...</p>
        </div>
    `);
    
    // First load all fee items
    $.ajax({
        url: "{{ route('a.getDropdown') }}",
        type: "GET",
        success: function(feeItemsResponse) {
            // Now get the assignments for this class
            $.ajax({
                url: "{{ route('feeassign.getAssignments') }}",
                type: "GET",
                data: { classid: classId },
                success: function(assignmentsResponse) {
                    // Clear the loading spinner
                    feeItemsContainer.empty();
                    
                    const assignedFeeIds = [];
                    if (assignmentsResponse.data && assignmentsResponse.data.length > 0) {
                        assignmentsResponse.data.forEach(function(assignment) {
                            assignedFeeIds.push(assignment.feeid.toString());
                        });
                    }
                    
                    feeItemsResponse.data.forEach(function(feeItem) {
                        const isChecked = assignedFeeIds.includes(feeItem.ID.toString()) ? 'checked' : '';
                        
                        const checkboxDiv = `
                            <div class="col-md-3 mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="feeItem${feeItem.ID}" 
                                           name="feeItems[]" value="${feeItem.ID}" ${isChecked}>
                                    <label class="custom-control-label" for="feeItem${feeItem.ID}">${feeItem.feename}</label>
                                </div>
                            </div>
                        `;
                        
                        feeItemsContainer.append(checkboxDiv);
                    });
                },
                error: function(xhr) {
                    feeItemsContainer.html(`
                        <div class="col-12 text-center text-danger">
                            <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Error loading assignments. Please try again.</p>
                        </div>
                    `);
                    console.error('Error loading assignments:', xhr.responseText);
                }
            });
        },
        error: function(xhr) {
            feeItemsContainer.html(`
                <div class="col-12 text-center text-danger">
                    <i class="fa fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>Error loading fee items. Please try again.</p>
                </div>
            `);
            console.error('Error loading fee items:', xhr.responseText);
        }
    });
}
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
