  $(document).ready(function() {
            // Initialize DataTable with server-side processing
            var table = $('#agents-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: amanage,
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