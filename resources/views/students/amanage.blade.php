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
        const amanage = '{{ route("agents.data") }}';
    </script>
    <script src="{{ asset('js/amanage.js') }}"></script>
    
    <script> 
      
    </script>
    
   
</x-custom-admin-layout>