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
    <div class="min-height-200px"style="margin-top: -50px;">
        <form id="feeassignF">
            <div class="pd-ltr-20 xs-pd-20-10">
                <h4 class="header-container">Modules Assign</h4>
                <input type="hidden" name="form_type" value="assignform">
                
                <!-- Classes Card -->
                <div class="card-box pd-20 height-100-p mb-30">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <label id="class">Classes</label>
                            <select name="classes" id="classes" class="custom-select form-control" required>

                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Fee Items Card -->
                <div class="card-box pd-20 height-100-p mb-30">
                    <h5 class="text-center mb-4">Fee Items</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="feeItemsContainer" class="row">
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">Assign Fees</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<!--<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
    <script>
        $(document).ready(function() {
            loaddesigs();
            // Add submit event handler to the form
$('#feeassignF').on('submit', function(e) {
    e.preventDefault();
    
    // Get the selected class ID
    const classId = $('#classes').val();
    
    // Get all checked fee items
    const selectedFeeItems = [];
    $('input[name="feeItems[]"]:checked').each(function() {
        selectedFeeItems.push($(this).val());
    });
    
    // Validate inputs
    if (!classId) {
        showAlert('danger', 'Error!','Please select a class');
        return;
    }
    
    if (selectedFeeItems.length === 0) {
        showAlert('danger', 'Error!','Please select at least one fee item');
        return;
    }
    
    // Prepare data for submission
    const formData = {
        classid: classId,
        feeItems: selectedFeeItems,
        _token: $('meta[name="csrf-token"]').attr('content')
    };
    const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    // Submit the data
    $.ajax({
        url: "{{ route('feeassign.store') }}",
        type: "POST",
        data: formData,
        success: function(response) {
            if (response.success) {
                
                showAlert('success','Success!','Fee items assigned successfully!');
                // Reset form or redirect as needed
                $('#feeassignF')[0].reset();
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
           
            $.ajax({
        url: "{{ route('a.getDropdown') }}",
        type: "GET",
        success: function(response) {
            const feeItemsContainer = $('#feeItemsContainer');
            
            // Clear existing content
            feeItemsContainer.empty();
            
            
            // Populate with fee items as checkboxes in columns of three
            response.data.forEach(function(feeItem, index) {
                const checkboxDiv = `
                    <div class="col-md-3 mb-3">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="feeItem${feeItem.ID}" name="feeItems[]" value="${feeItem.ID}">
                            <label class="custom-control-label" for="feeItem${feeItem.ID}">${feeItem.feename}</label>
                        </div>
                    </div>
                `;
                
                feeItemsContainer.append(checkboxDiv);
            });
        },
        error: function() {
            alert('Failed to load Fee Items. Please try again.');
        }
    });
    $.ajax({
    url: "{{ route('classes.getDropdown2') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#classes');
        dropdown.empty();
        dropdown.append('<option value="">Select class</option>');
        response.data.forEach(function (classes) {
            dropdown.append(
                `<option value="${classes.ID}">${classes.claname}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Classes. Please try again.');
    },
});
});

        function loaddesigs(page = 1) {
    $.ajax({
        url: "{{ route('feeitems.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#feeitems-table-body');
            const paginationControls = $('#pagination-controls');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.feename}</td>
                    <td>${row.amount}</td> <!-- Display branchname instead of brid -->
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editfitemModal"
                                    data-id="${row.ID}"
                                    data-feename="${row.feename}"
                                    data-amount="${row.amount}"> <!-- Include branchname -->
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

            // Add click event for pagination buttons
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loaddesigs(page); // Load houses for the clicked page
            });
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
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
