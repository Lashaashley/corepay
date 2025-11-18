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
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                
                
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
                
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Fee Items</h2>
                                <section>
                                    <form id="feeitemsf">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Item:</label>
                                                <input name="feename" type="text" class="form-control" required="true" autocomplete="off">
                                                <span class="text-danger" id="feename-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Fee category:</label>
                                                <select name="category" id="category" class="custom-select form-control" required>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" hidden>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >House:</label>
                                                <select name="house" id="house" class="custom-select form-control">
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Amount:</label>
                                                <input name="amount" type="text" class="form-control" required="true" autocomplete="off">
                                                <span class="text-danger" id="amount-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" form="feeitemsf" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Fee Items List</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Item</th>
                                                <th>Amount</th>
                                                <th>Categoty</th>
                                                <th>House</th>
                                                <th>Options</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody id="feeitems-table-body">

                                        </tbody>
                                    </table>
                                    <div id="pagination-controls" class="mt-3"></div>
                                    </div>
                                </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               
                
                
                
               
                
            </div>
        </div>
        
</div>

<div class="modal fade" id="editfitemModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Fee item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editfitemForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                
                    <div class="form-group"> 
                        <div class="form-group">
                            <label >Item:</label>
                            <input type="text" class="form-control" id="edititem" name="feename">
                        </div>
                        <label for="schoolPobox">Name:</label>
                        <input type="text" class="form-control" id="editamount" name="amount">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editfitemForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<!---<script src="{{ asset('js/custom-dropdown.js') }}"></script>---->
    <script>
        $(document).ready(function() { 
            loaddesigs();
            $('#feeitemsf').on('submit', function(e) {
    e.preventDefault();
    $('.text-danger').html('');
    
    // Create FormData object
    let formData = new FormData(this);
    
    // If category is not 4, remove house field from FormData if it exists
    if ($('#category').val() != '4') {
        formData.delete('house');
    }
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: "{{ route('feeitems.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            $('#feeitemsf')[0].reset();
            loaddesigs();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                showAlert('danger', 'Error!', 'Please check the form for errors.');
            } else {
                showAlert('danger', 'Error!', 'Error adding fee item');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
            $(document).on('click', '[data-target="#editfitemModal"]', function () {
    const id = $(this).data('id');
    
    const item = $(this).data('feename');
    const amount = $(this).data('amount');
    

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values  
    const form = $('#editfitemForm');
    form.find('#ID').val(id);
    form.find('#edititem').val(item);
    form.find('#editamount').val(amount);
   
});
$('#editfitemForm').on('submit', function (e) { 
                e.preventDefault();
                const id = $('#editfitemModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: `{{ url('feeitems') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editfitemModal').modal('hide');
                        loaddesigs(); // Reload the table
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
            $.ajax({
                url: "{{ route('fcategories.getDropdown') }}",
                type: "GET",
                success: function (response) {
                    const dropdown = $('#category');
                    dropdown.empty();
                    dropdown.append('<option value="">Select category</option>');
                    response.data.forEach(function (categories) {
                        dropdown.append(
                            `<option value="${categories.ID}">${categories.catename}</option>`
                        );
                    });
                },
                error: function () {
                    alert('Failed to load fcategories. Please try again.');
                },
            });
            $.ajax({
                url: "{{ route('houses.getDropdown') }}",
                type: "GET",
                success: function (response) {
                    const dropdown = $('#house');
                    dropdown.empty();
                    dropdown.append('<option value="">Select house</option>');
                    response.data.forEach(function (house) {
                        dropdown.append(
                            `<option value="${house.ID}">${house.housen}</option>`
                        );
                    });
                },
                error: function () {
                    alert('Failed to load fcategories. Please try again.');
                },
            });
            $('#category').on('change', function() {
                var selectedValue = $(this).val();
                var houseRow = $('#house').closest('.row');
                if (selectedValue == '4') {
                    houseRow.removeAttr('hidden');
                    $('#house').attr('required', true);
                } else {
                    houseRow.attr('hidden', true);
                    $('#house').removeAttr('required');
                    $('#house').val('');
                }
            });
            $('#category').trigger('change');
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
                    response.data.forEach(function (row) {
                        const tr = $('<tr>');
                        tr.append(`
                        <td>${row.ID}</td>
                        <td>${row.feename}</td>
                        <td>${row.amount}</td>
                        <td>${row.catename}</td>
                        <td>${row.housen || 'N/A'}</td>
                        <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editfitemModal"
                                    data-id="${row.ID}"
                                    data-feename="${row.feename}"
                                    data-amount="${row.amount}"
                                    data-category="${row.category_name}"
                                    data-house="${row.housen || ''}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.feename}')">
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
                loaddesigs(page);
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
