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

<div class="container-fluid">
    <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
    <div class="card">
        <div class="card-header">
            <h4>Pending KYC Updates</h4>
        </div>
        <div class="card-body">
            @if($pendingUpdates->count() > 0)
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Agent ID</th>
                            <th>Agent Name</th>
                            <th>Submitted By</th>
                            <th>Submitted At</th>
                            <th>Changes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingUpdates as $update)
                        <tr>
                            <td>{{ $update->empid }}</td>
                            <td>{{ $update->employee->FirstName ?? '' }} {{ $update->employee->LastName ?? '' }}</td>
                            <td>{{ $update->submitter->name ?? 'Unknown' }}</td>
                            <td>{{ $update->submitted_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge badge-info">
                                    {{ count(array_filter($update->pending_data, function($value, $key) use ($update) {
                                        return ($update->original_data[$key] ?? null) != $value;
                                    }, ARRAY_FILTER_USE_BOTH)) }} fields
                                </span>
                            </td>
                            <td>
                                <button type="button"
                                class="btn btn-enhanced btn-draft reviewBtn"
                                data-id="{{ $update->id }}">
                                <i class="fas fa-eye"></i> Review
                            </button>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-center text-muted">No pending updates</p>
            @endif
        </div>
    </div>
</div>
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Review KYC Changes</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <input type="hidden" id="pending_update_id">


      <div class="modal-body" id="reviewModalBody">
        <p class="text-center text-muted">Loading...</p>
      </div>
      <textarea class="form-control mt-1" id="rejection_reason" placeholder="Reason for rejection (required)"></textarea>


      <div class="modal-footer">
        
        <button type="button" id="rejectbtn" class="btn btn-enhanced btn-cancel">
            <i class="fas fa-window-close"></i> Reject
        </button>
        <button type="submit" id="approvebtn" class="btn btn-enhanced btn-finalize">
                <i class="fas fa-check-double"></i> Approve
            </button>
      </div>

    </div>
  </div>
</div>

  
    

    <script src="{{ asset('src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    
  
  <script>
$(document).on('click', '.reviewBtn', function () {

    let id = $(this).data('id');

  
   

    $('#reviewModalBody').html('<p class="text-center text-muted">Loading...</p>');
    $('#reviewModal').modal('show');

    $.ajax({
        url: "{{ route('registration.approvals.show', ':id') }}".replace(':id', id),
        type: "GET",
        success: function (response) {

            if(response.status === "success") {

                let pending = response.pendingUpdate;
                let changes = response.changes;

                let html = `
                    <div class="mb-2">
                        <strong>Agent ID:</strong> ${pending.empid}<br>
                        <strong>Submitted By:</strong> ${pending.submitter ? pending.submitter.name : 'Unknown'}<br>
                        <strong>Status:</strong> ${pending.status}<br>
                        <strong>Submitted At:</strong> ${pending.submitted_at}
                    </div>

                    <hr>

                    <h6>Changed Fields</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Old Value</th>
                                <th>New Value</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                if(Object.keys(changes).length > 0){
                    $.each(changes, function(field, values){
                        html += `
                            <tr>
                                <td>${field}</td>
                                <td>${values.old ?? ''}</td>
                                <td>${values.new ?? ''}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += `
                        <tr>
                            <td colspan="3" class="text-center text-muted">No changes found</td>
                        </tr>
                    `;
                }

                html += `
                        </tbody>
                    </table>
                `;

                $('#pending_update_id').val(id);

                $('#reviewModalBody').html(html);
            }
        },
        error: function (xhr) {
            $('#reviewModalBody').html('<p class="text-danger text-center">Failed to load details.</p>');
            console.log(xhr.responseText);
        }
    });

});
$(document).on('click', '#approvebtn', function(e){
    e.preventDefault();

    let id = $('#pending_update_id').val();

    if(!id){
        showAlert("danger", "Error", "No pending update selected.");
        return;
    }

    $('#approvebtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Approving...');

    $.ajax({
        url: "{{ route('registration.approvals.approve', ':id') }}".replace(':id', id),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function(response){
            if(response.success){
                showAlert("success", "Approved", response.message);

                $('#reviewModal').modal('hide');

                // Optional: remove row from table
                $('button.reviewBtn[data-id="'+id+'"]').closest('tr').remove();
            }else{
                showAlert("danger", "Error", response.message);
            }
        },
        error: function(xhr){
            console.log(xhr.responseText);
            showAlert("danger", "Error", "Failed to approve update.");
        },
        complete: function(){
            $('#approvebtn').prop('disabled', false).html('<i class="fas fa-check-double"></i> Approve');
        }
    });
});
$(document).on('click', '#rejectbtn', function(e){
    e.preventDefault();

    let id = $('#pending_update_id').val();
    let reason = $('#rejection_reason').val();

    if(!id){
        showAlert("danger", "Error", "No pending update selected.");
        return;
    }

    if(!reason || reason.trim() === ""){
        showAlert("danger", "Rejected", "Rejection reason is required.");
        return;
    }

    $('#rejectbtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Rejecting...');

    $.ajax({
        url: "{{ route('registration.approvals.reject', ':id') }}".replace(':id', id),
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            rejection_reason: reason
        },
        success: function(response){
            if(response.success){
                showAlert("success", "Rejected", response.message);

                $('#reviewModal').modal('hide');

                // Optional: remove row from table
                $('button.reviewBtn[data-id="'+id+'"]').closest('tr').remove();
            }else{
                showAlert("danger", "Error", response.message);
            }
        },
        error: function(xhr){
            console.log(xhr.responseText);

            if(xhr.status === 422){
                showAlert("danger", "Validation Error", "Rejection reason is required.");
            }else{
                showAlert("danger", "Error", "Failed to reject update.");
            }
        },
        complete: function(){
            $('#rejectbtn').prop('disabled', false).html('<i class="fas fa-window-close"></i> Cancel');
        }
    });
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