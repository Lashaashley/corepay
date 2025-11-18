<x-custom-admin-layout>
    <style>
                	.tab-container {
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
    font-size: 12.5px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff;
}

.tab-button i {
    color: #667eea;
    font-size: 16px;
    transition: color 0.3s;
}

.tab-content {
    display: none;
    padding: 20px;
}
.tab-button.active i {
    color: #7360ff;
}
.tab-content.active {
    display: block;
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
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .form-select, .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: var(--transition);
            background: white;
        } 

    </style>
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="tab-container" style="margin-top: -40px;">
    <button class="tab-button active" onclick="openTab(event, 'taborgstruct')">
        <i class="fas fa-edit"></i> Exam Types
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabgrading')">
        <i class="fas fa-sort-numeric-down"></i> Grading
    </button>
    <button class="tab-button" id="tab-marks" onclick="openTab(event, 'tabjoinmarks')">
        <i class="fas fa-user-graduate"></i> Join Marks
    </button>
    <!---<button class="tab-button" onclick="openTab(event, 'tabclass')">
        <i class="fas fa-chalkboard-teacher"></i> Classes
    </button>
    <button class="tab-button" id="tab-streams" onclick="openTab(event, 'tabstreams')">
        <i class="fas fa-project-diagram"></i> Streams
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabperiod')">
        <i class="fas fa-calendar-alt"></i> Periods
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabfcategories')">
        <i class="fas fa-tags"></i> Fee categories
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabfpaymodes')">
        <i class="fas fa-credit-card"></i> Pay modes
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabdepts')">
        <i class="fas fa-building"></i> Departments
    </button>--->
</div>
                
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
                <div id="taborgstruct" class="tab-content active" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Exam Types</h2>
                                <section>
                                    <form id="examtypesf">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Exam Type Name:</label>
                                                <input name="examname" type="text" class="form-control" required="true" autocomplete="off">
                                                <span class="text-danger" id="examname-error"></span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <button type="submit" form="examtypesf" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Exam Types List</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Exam Type</th>
                                                <th>Options</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody id="examtypes-table-body">

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
    <div id="tabgrading" class="tab-content" style="margin-top: -170px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Grading</h2>
                                <section>
                                    <form id="gradingform" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Grade:</label> 
                                                <input name="Grade" id="Grade" type="text" class="form-control" required="true" autocomplete="off">
                                                <small id="Grade-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Minimum Marks:</label> 
                                                <input name="Min" id="Min" type="number" class="form-control" required="true" autocomplete="off">
                                                <small id="Min-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Maximum Marks:</label> 
                                                <input name="Max" id="Max" type="number" class="form-control" required="true" autocomplete="off">
                                                <small id="Max-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Remarks:</label> 
                                                <input name="Remarks" id="Remarks" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-save"></i>Save
                                    </button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Campuses</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="campuses-table-body"></tbody>
                                </table>
                                <div id="pagination-controls" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
<div id="tabjoinmarks" class="tab-content" style="margin-top: -170px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Students Joining Performace</h2>
                                <section>
                                    <form id="jmarksform" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Student:</label> 
                                                
                                                <select name="admno" id="admno" class="form-select" required>
                                                </select>
                                                <small id="admno-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                            <label >Exam Types:</label>
                                            <select name="examtype" id="examtype" class="custom-select" required="true" autocomplete="off">
                                            <option value="">Select Examtype</option>
                                            <option value="End Term">End Term</option>
                                            <option value="KPSEA">KPSEA</option>
                                            <option value="KJSEA">KJSEA</option>
                                           
                                        </select>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Exam Year:</label> 
                                                <input name="examyear" id="examyear" type="text" class="form-control" required="true" autocomplete="off">
                                                <small id="examyear-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Total Marks:</label> 
                                                <input name="marks" id="marks" type="number" class="form-control" required="true" autocomplete="off">
                                                <small id="marks-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>                                    
                                    <button type="submit" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-save"></i>Save
                                    </button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Campuses</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="campuses-table-body"></tbody>
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
        $(document).ready(function() { 
           loaddesigs();
            $('#examtypesf').on('submit', function(e) {
    e.preventDefault();
    $('.text-danger').html('');
    
    // Create FormData object
    let formData = new FormData(this);
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: "{{ route('examtypes.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            $('#examtypesf')[0].reset();
            //loaddesigs();
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

$('#gradingform').on('submit', function(e) {
    e.preventDefault();
    $('.text-danger').html('');
    
    // Create FormData object
    let formData = new FormData(this);
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: "{{ route('grading.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            $('#gradingform')[0].reset();
            //loaddesigs();
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

$('#jmarksform').on('submit', function(e) {
    e.preventDefault();
    $('.text-danger').html('');
    
    // Create FormData object
    let formData = new FormData(this);
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    $.ajax({
        url: "{{ route('jmarks.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            $('#jmarksform')[0].reset();
            //loaddesigs();
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
            $('#tab-marks').on('click', function() {

                $.ajax({
                url: "{{ route('jranks.getstudents2') }}",
                type: "GET",
                success: function(response) {
                    const dropdown = $('#admno');
                    dropdown.empty();
                    dropdown.append('<option value="">Select student</option>');
                    if (Array.isArray(response)) {
                        response.forEach(function(student) {
                            dropdown.append(
                                `<option value="${student.admno}">${student.admno} - ${student.studentname}</option>`
                            );
                        });
                        dropdown.select2({
                            placeholder: "Select Student",
                            allowClear: true,
                            width: '100%'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText);
                    alert('Failed to load students. Please try again.');
                }
            });

            });

            
            
        });
        function loaddesigs(page = 1) {
            $.ajax({
                url: "{{ route('examtypes.getall') }}?page=" + page,
                type: "GET",
                success: function (response) {
                    const tableBody = $('#examtypes-table-body');
                    const paginationControls = $('#pagination-controls');
                    tableBody.empty();
                    paginationControls.empty();
                    response.data.forEach(function (row) {
                        const tr = $('<tr>');
                        tr.append(`
                        <td>${row.ID}</td>
                        <td>${row.examname}</td>
                        
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
