<x-custom-admin-layout>
   
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
                
                
                <div class="pb-20 px-20">
                    <table id="agents-table" class="data-table table stripe hover nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th class="table-plus">Full Name</th>
                                <th>Work No</th>
                                <th>Staff Type</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>State</th>
                                <th class="datatable-nosort">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Staff Modal -->
    <div class="modal fade" id="editstaffModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Staff</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Edit form will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Terminate Modal -->
    <div class="modal fade" id="terminatModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Terminate Staff</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to terminate this staff member?</p>
                    <input type="hidden" id="terminate-agent-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-terminate">Terminate</button>
                </div>
            </div>
        </div>
    </div>

    
    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable with server-side processing
            var table = $('#agents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("agents.data") }}',
                    type: 'GET',
                    error: function(xhr, error, thrown) {
                        console.error('DataTable Ajax error:', error);
                        showMessage('Error loading data', 'danger');
                    }
                },
                columns: [
                    {
                        data: null,
                        orderable: true,
                        render: function(data, type, row) {
                            return `
                                <div class="name-avatar d-flex align-items-center">
                                    <div class="avatar mr-2 flex-shrink-0">
                                        <img src="${row.profile_photo}" 
                                             class="border-radius-100 shadow" 
                                             width="40" 
                                             height="40" 
                                             alt="${row.full_name}"
                                             onerror="this.src='{{ asset('uploads/NO-IMAGE-AVAILABLE.jpg') }}'">
                                    </div>
                                    <div class="txt">
                                        <div class="weight-600">${row.full_name}</div>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    { data: 'emp_id', orderable: true },
                    { data: 'stafftype', orderable: true },
                    { data: 'department', orderable: true },
                    { data: 'designation', orderable: true },
                    {
                        data: 'status',
                        orderable: true,
                        render: function(data, type, row) {
                            var color = data === 'ACTIVE' ? 'green' : 'red';
                            return `<span style="color: ${color}; font-weight: bold;">${data}</span>`;
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="dropdown">
                                    <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" 
                                       href="#" 
                                       role="button" 
                                       data-toggle="dropdown">
                                        <i class="dw dw-more"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                        <a class="dropdown-item edit-agent" 
                                           href="#" 
                                           data-id="${data}">
                                            <i class="dw dw-edit2"></i> Edit
                                        </a>
                                        <a class="dropdown-item terminate-agent" 
                                           href="#" 
                                           data-id="${data}">
                                            <i class="icon-copy fa fa-ban"></i> Terminate
                                        </a>
                                    </div>
                                </div>
                            `;
                        }
                    }
                ],
                order: [[1, 'asc']], // Order by emp_id
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
                    emptyTable: "No staff members found",
                    zeroRecords: "No matching staff members found"
                }
            });

            // Edit agent
            $('#agents-table').on('click', '.edit-agent', function(e) {
                e.preventDefault();
                var agentId = $(this).data('id');
                // Load edit form via AJAX
                // You can implement this based on your needs
                $('#editstaffModal').modal('show');
            });

            // Terminate agent
            $('#agents-table').on('click', '.terminate-agent', function(e) {
                e.preventDefault();
                var agentId = $(this).data('id');
                $('#terminate-agent-id').val(agentId);
                $('#terminatModal').modal('show');
            });

            // Confirm termination
            $('#confirm-terminate').on('click', function() {
                var agentId = $('#terminate-agent-id').val();
                
                $.ajax({
                    url: `/agents/${agentId}/terminate`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            $('#terminatModal').modal('hide');
                            table.ajax.reload(null, false); // Reload table without resetting pagination
                        } else {
                            showMessage(response.message, 'danger');
                        }
                    },
                    error: function(xhr) {
                        showMessage('Error terminating staff member', 'danger');
                    }
                });
            });

            // Show message function
            function showMessage(message, type) {
                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                $('#status-message')
                    .removeClass('alert-success alert-danger')
                    .addClass(alertClass)
                    .find('#alert-message').text(message);
                $('#status-message').fadeIn().delay(3000).fadeOut();
            }
        });
    </script>
    
   
</x-custom-admin-layout>