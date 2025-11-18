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
        .billing-card {
        border-radius: 10px;
        box-shadow: 0 0 28px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }
    
    .billing-card:hover {
        box-shadow: 0 0 38px rgba(0, 0, 0, 0.1);
    }
    
    .billing-table {
        font-size: 0.875rem;
    }
    
    .billing-table thead th {
        padding: 0.25rem;
        background-color: #f8f9fc;
        border-bottom: 2px solid #e9ecef;
        font-weight: 600;
    }
    .billing-table tbody tr {
        padding: 0.25rem;
       
    }
    
    .billing-row {
        transition: background-color 0.2s;
   
    }
    
    .billing-row:hover {
        background-color: #f3f7fd;
    }
    
    .font-weight-medium {
        font-weight: 500;
    }
    
    .billing-header {
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
    }
    
    .billing-footer {
        background-color: #f8f9fc;
        border-top: 1px solid #e9ecef;
        margin-top: 0rem;
        padding-top: 0.35rem;
        font-weight: 600;
    }
    .billing-row td {
    padding: 0.25rem;
}

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
    padding: 5px 20px;
    font-size: 12.5px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff; /* Hide border bottom when active */
}



    /* Loader styles */
    .loader-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
    }
    
    .loader {
        border: 5px solid #f3f3f3;
        border-radius: 50%;
        border-top: 5px solid #3498db;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    #newreceiptModal .modal-dialog {
    max-width: 600px;
    width: 90%;
}
#receiptmodal .modal-dialog,
#printModal .modal-dialog {
    max-width: 800px;
    width: 90%;
}
#postModal .modal-header,
#perclasModal .modal-header,
#newreceiptModal .modal-header {
    background-color: #007bff; /* Blue header */
    color: white;
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
}
#newreceiptModal .modal-body {
    padding: 10px;
}
.compact-rows .row {
    margin-bottom: -0.9rem; /* or whatever value you prefer */
}
.compact-rows .form-group {
    margin-bottom: -0.9rem; /* reduces space between form groups */
}

#newreceiptModal .form-group label {
    font-weight: bold;
    color: #333;
}
.table-sm td,
.table-sm th {
    padding-top: 0.25rem;
    padding-bottom: 0.25rem;
    line-height: 1.2;
}
.form-group-border {
    border: 2px solid #ced4da;
            border-radius: 0.25rem;
            padding: 10px;
            position: relative;
            margin-top: -20px;
            margin-bottom: 10px;
        }
        .form-group-border legend {
            font-size: 1rem;
            font-weight: 400;
            width: auto;
            padding: 0 5px;
            margin-bottom: 0;
            position: absolute;
            top: -0.8rem;
            left: 1rem;
            background: white;
        }
    </style>
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                <strong id="alert-title"></strong> <span id="alert-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <h3 class="mb-30 text-center" style="margin-top: -50px;">Billing Module</h3>
            <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -40px;">
                <div class="row align-items-center">
                    <div class="col-md-2 col-sm-12">
                        <label >Campus:</label>
                        <select name="caid" id="campus" class="custom-select form-control" required>

                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12">
                        <label >Class:</label>
                        <select name="claid" id="class" class="custom-select form-control" required>
                            
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <label >Student:</label>
                        <select name="admno" id="student" class="custom-select form-control" required>
                            
                        </select>
                    </div>
                </div>
            </div>
            <div class="tab-container" style="margin-top: -20px;">
    <button class="tab-button" data-toggle="modal" data-target="#newreceiptModal">
        <i class="icon-copy dw dw-money-1 mr-1"></i> New Receipt
    </button>
    <button class="tab-button" data-toggle="modal" data-target="#postModal">
        <i class="icon-copy dw dw-add-file mr-1"></i> Post Item
    </button>
    <button class="tab-button" data-toggle="modal" data-target="#perclasModal">
        <i class="icon-copy fi-torsos-all"></i> Per Group
    </button>
    <button class="tab-button" data-toggle="modal" data-target="#viewreceiptModal">
        <i class="icon-copy dw dw-file-41 mr-1"></i> View Receipt
    </button>
    <button class="tab-button" data-toggle="modal" data-target="#printModal">
        <i class="icon-copy dw dw-print mr-1"></i> Open Statement
    </button>
    <button class="tab-button" data-toggle="modal" data-target="#emailModal">
        <i class="icon-copy dw dw-email mr-1"></i> Email Statement
    </button>
    <button class="tab-button" data-toggle="modal" data-target="#historyModal">
        <i class="icon-copy dw dw-invoice-1 mr-1"></i> History
    </button>
    <button class="tab-button" data-toggle="modal" data-target="#siblingsModal">
        <i class="icon-copy dw dw-group mr-1"></i> Siblings
    </button>
</div>
            <div class="card-box pd-20 height-100-p mb-30 billing-card" style="margin-top: -10px;">
                <form id="billing-form" enctype="multipart/form-data">
                    @csrf
                    <!-- Loading spinner -->
                     <div id="loading-spinner" class="loader-container d-none">
                        <div class="loader"></div>
                        <div class="ml-3">Loading billing details...</div>
                    </div>
                    <div id="billing-details-container" class="d-none">
                        <div class="row mb-3 billing-header">
                            <div class="col-12">
                                <h5 id="student-name-display" class="text-primary mb-0"></h5>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover billing-table">
                                <thead>
                                    <tr>
                                        <th><i class="icon-copy dw dw-calendar mr-1"></i>Date Posted</th>
                                        <th><i class="icon-copy dw dw-file mr-1"></i>Fee Item</th>
                                        <th class="text-right"><i class="icon-copy dw dw-invoice mr-1"></i> Amount</th>
                                        <th class="text-right"><i class="icon-copy dw dw-money mr-1"></i> Paid</th>
                                        <th class="text-right"><i class="icon-copy dw dw-balance mr-1"></i> Balance</th>
                                        <th class="text-center"><i class="icon-copy dw dw-flag mr-1"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody id="billing-table-body">
                                    <!-- Billing details will be inserted here -->
                                </tbody>
                                <tfoot>
                                    <tr  class="billing-footer">
                                        <td style="padding: 0.25rem;" colspan="2" class="text-right">
                                            <i class="icon-copy dw dw-analytics-6 mr-1"></i> <strong>Totals:</strong>
                                        </td>
                                        <td style="padding: 0.25rem;"  id="total-amount" class="text-right font-weight-bold">0.00</td>
                                        <td style="padding: 0.25rem;" id="total-paid" class="text-right font-weight-bold">0.00</td>
                                        <td style="padding: 0.25rem;" id="total-balance" class="text-right font-weight-bold">0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div id="no-data-message" class="text-center p-5">
                        <i class="icon-copy dw dw-search2 fa-3x text-muted mb-3"></i>
                        <h5>Select a student to view billing details</h5>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="newreceiptModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newreceiptModallabel">Post Fee Receipt</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body compact-rows"> 
                    <form id="postfeeform">
                        @csrf
                            <input type="text" id="admno" name="admno" hidden>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="icon-copy dw dw-user1" style="font-weight: bolder;"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="studentname" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-4">
                                    <label>Receipt Date:</label>
                                    <input name="receiptdate" id="receiptdate" type="text" class="form-control date-picker" required="true" autocomplete="off" required>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label>Receipt No.:</label>
                                    <input name="receiptno" id="receiptno" type="text" class="form-control" required="true" autocomplete="off" required>
                                </div>
                                <div class="form-group col-sm-4">
                                    <label>Amount:</label>
                                    <input name="pamount" id="pamount" type="text" class="form-control" required="true" autocomplete="off" required>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 1rem;">
                                <div class="form-group col-sm-4">
                                    <label>Pay mode:</label>
                                    <select name="pmethod" id="pmethod" class="custom-select form-control" required>
                                        <!-- Payment methods will be loaded here -->
                                    </select>
                                </div>
                                <div id="tcodefield" class="form-group col-sm-4" hidden>
                                    <label>Transaction Code:</label>
                                    <input name="tcode" id="tcode" type="text" class="form-control" autocomplete="off">
                                </div>
                                <div id="chequenofield" class="form-group col-sm-4" hidden>
                                    <label>Cheque No:</label>
                                    <input name="chequeno" id="chequeno" type="text" class="form-control" autocomplete="off">
                                </div>
                                <div id="banknfield" class="form-group col-sm-4" hidden>
                                    <label>Bank:</label>
                                    <input name="bankn" id="bankn" type="text" class="form-control" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="margin-top: 1rem;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" form="postfeeform" class="btn btn-primary">Save</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="postModallabel">Post New Item</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body compact-rows"> 
                    <form id="postindvform">
                        @csrf
                            <input type="text" id="admnopost" name="admnopost" hidden>
                            <input type="text" id="classidpost" name="classidpost" hidden>
                            <div class="row">
                                <div class="form-group col-sm-12">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="icon-copy dw dw-user1" style="font-weight: bolder;"></i>
                                            </span>
                                        </div>
                                        <input type="text" class="form-control" id="studentnamep" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label>Fee Item:</label>
                                    <select name="fitems" id="fitems" class="custom-select form-control" required>
                                        <!-- Payment methods will be loaded here -->
                                    </select>
                                </div>
                                <div class="form-group col-sm-6">
                                    <label>Amount:</label>
                                    <input name="famount" id="famount" type="text" class="form-control" required="true" autocomplete="off" readonly>
                                </div>
                                
                            </div>
                            
                        </div>
                        <div class="modal-footer" style="margin-top: 1rem;">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" form="postindvform" class="btn btn-primary">Save</button>
                        </div>
                    </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="viewreceiptModal" tabindex="-1" aria-labelledby="viewreceiptModal" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewreceiptModallabel">
                    <i class="icon-copy dw dw-file-41 mr-2"></i>
                    Student Receipts
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <!-- Loading indicator -->
                <div id="receipts-loading" class="text-center" style="display: none;">
                    <div class="spinner-border" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <p class="mt-2">Loading receipts...</p>
                </div>

                <!-- Error message -->
                <div id="receipts-error" class="alert alert-danger" style="display: none;">
                    <i class="icon-copy dw dw-warning"></i>
                    <span id="error-message"></span>
                </div>

                <!-- Receipts content -->
                <div id="receipts-content" style="display: none;">
                    <!-- Summary -->
                    <div class="row mb-0">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Receipts</h6>
                                    <h4 id="total-receipts">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Amount</h6>
                                    <h4 id="total-amount">KSh 0.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Receipts table -->
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Receipt No.</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Reference</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="receipts-table-body">
                                <!-- Dynamic content will be loaded here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- No receipts message -->
                    <div id="no-receipts" class="text-center py-4" style="display: none;">
                        <i class="icon-copy dw dw-file-41" style="font-size: 48px; color: #ccc;"></i>
                        <p class="mt-2 text-muted">No receipts found for this student.</p>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-receipts">
                    <i class="icon-copy dw dw-print"></i>
                    Print All
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printModalLabel">
                    <i class="icon-copy dw dw-print mr-2"></i>
                    Print Student Statement
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
           <div class="modal-body">
    <!-- Loading indicator -->
    <div id="print-loading" class="text-center" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="sr-only">Generating PDF...</span>
        </div>
        <p class="mt-2">Preparing your statement...</p>
    </div>

    <!-- Error message -->
    <div id="print-error" class="alert alert-danger" style="display: none;">
        <i class="icon-copy dw dw-warning"></i>
        <span id="print-error-message"></span>
    </div>

    <!-- PDF viewer will be injected here -->
</div>

            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="icon-copy dw dw-cancel"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-info" id="preview-statement">
                    <i class="icon-copy dw dw-eye"></i>
                    Preview
                </button>
                <button type="button" class="btn btn-primary" id="download-statement">
                    <i class="icon-copy dw dw-download"></i>
                    Download PDF
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="perclasModal" tabindex="-1" aria-labelledby="perclasModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="perclasModalLabel">Post Per Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="fitemspgroup">Fee Item:</label>
                    <select name="fitemspgroup" id="fitemspgroup" class="custom-select form-control" required>
                        <option value="">Select Fee Item</option>
                    </select>
                </div>
                <div class="form-group col-sm-6">
                    <label for="amountpgroup">Amount:</label>
                    <input name="amountpgroup" id="amountpgroup" type="text" class="form-control" required autocomplete="off" readonly>
                </div>
                    
            </div>
            <form id="postpercampf">
                @csrf
                <input type="text" id="feeidcamp" name="feeidcamp" hidden> 
                <input type="text" id="famountcamp" name="famountcamp" hidden>
                <div class="modal-body compact-rows">
                    <div class="form-group-border">
                        <legend>Post Fee Item Per Campus</legend>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <div class="input-group">
                                    <select name="caid" id="campuspgroup" class="custom-select form-control" required>
                                        <option value="">Select Campus</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                 <button type="submit" form="postpercampf" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
            <form id="postperhousef">
                @csrf
                <input type="text" id="feeidhous" name="feeidhous" hidden> 
                <input type="text" id="famounthous" name="famounthous" hidden>
                <div class="modal-body compact-rows">
                    <div class="form-group-border">
                        <legend>Post Fee Item Per House</legend>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <div class="input-group">
                                    <select name="house" id="house" class="custom-select form-control" required>
                                        <option value="">Select House</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                 <button type="submit" form="postperhousef" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
            <form id="postperclass">
                @csrf
                <input type="text" id="feeidcla" name="feeidcla" hidden> 
                <input type="text" id="famountcla" name="famountcla" hidden>
                <div class="modal-body compact-rows">
                    <div class="form-group-border">
                        <legend>Post Fee Item Per Class</legend>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <div class="input-group">
                                    <select name="claid" id="classgroup" class="custom-select form-control" required>
                                        <option value="">Select Class</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="form-group col-sm-6">
                                 <button type="submit" form="postperclass" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="receiptmodal" tabindex="-1" aria-labelledby="receiptmodalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="receiptmodalLabel">
                    <i class="icon-copy dw dw-print mr-2"></i>
                    Print Student Statement
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
           <div class="modal-body">
    <!-- Loading indicator -->
    <div id="print-loadingreci" class="text-center" style="display: none;">
        <div class="spinner-border" role="status">
            <span class="sr-only">Generating PDF...</span>
        </div>
        <p class="mt-2">Preparing your receipt...</p>
    </div>

    <!-- Error message -->
    <div id="print-errorreci" class="alert alert-danger" style="display: none;">
        <i class="icon-copy dw dw-warning"></i>
        <span id="print-error-message"></span>
    </div>

    <!-- PDF viewer will be injected here -->
</div>

            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="icon-copy dw dw-cancel"></i>
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
<!-- HTML container (somewhere on your page) -->
<div id="alert-container2" class="p-2"></div>


<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
    <script>
      

        $(document).ready(function() {
            function showAlert2(type, title, message) {
    $('#alert-container2').html(`
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${title}</strong><br>${message.replace(/\n/g, '<br>')}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    `);
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

            $('#add-student-form').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('billing.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#add-student-form')[0].reset();
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
            $.ajax({
                url: "{{ route('branches.getDropdown') }}",
                type: "GET",
                success: function (response) {
                    const dropdown = $('#campus');
                    dropdown.empty();
                    dropdown.append('<option value="">Select campus</option>');
                    response.data.forEach(function (branch) {
                        dropdown.append(
                            `<option value="${branch.ID}">${branch.branchname}</option>`
                        );
                    });
                },
                error: function () {
                    alert('Failed to load streams. Please try again.');
                },
            });
            $('#campus').on('change', function() {
                const selectedCampusId = $(this).val();
                if (selectedCampusId) {
                    loadClassesByCampus(selectedCampusId);
                    const classDropdown = $('#student');
                    classDropdown.empty();
                    classDropdown.append('<option value="">Select Student</option>');
                    clearBillingDetails();
                } else {
                    const classDropdown = $('#class');
                    classDropdown.empty();
                    classDropdown.append('<option value="">Select Class</option>');
                }
            });
            $('#class').on('change', function() {
                const selectedclassId = $(this).val();
                if (selectedclassId) {
                    getstudents(selectedclassId);
                    clearBillingDetails();
                } else {
                    const classDropdown = $('#student');
                    classDropdown.empty();
                    classDropdown.append('<option value="">Select Student</option>');
                }
            });
            $('#pmethod').on('change', function() {
                const selectedmethod = $(this).val();
                if (selectedmethod) {
                    getrequired(selectedmethod);
                } else {
                    $('#tcodefield').attr('hidden', true);
                    $('#chequenofield').attr('hidden', true);
                    $('#banknfield').attr('hidden', true);
                    $('#tcode').prop('required', false);
                    $('#chequeno').prop('required', false);
                    $('#bankn').prop('required', false);
                }
            });
            $('#student').on('change', function() {
        var admno = $(this).val();
        if (admno) {
            loadBillingDetails(admno);
        } else {
            clearBillingDetails();
        }
    });

    $('#newreceiptModal').on('show.bs.modal', function (event) { 
    var selectedOption = $('#student option:selected');
    var fullText = selectedOption.text();  
    var admno = selectedOption.val();     
    var stdname = selectedOption.text();

    var modal = $(this);
    modal.find('#admno').val(admno);
    modal.find('#studentname').val(stdname);

    $.ajax({
        url: "{{ route('pmodes.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#pmethod');
           
            
            dropdown.empty();
         
            

            // Add default options
            dropdown.append('<option value="">Select Mode</option>');
            
           

            // Populate with branches
            response.data.forEach(function (pmodes) {
                dropdown.append(
                    `<option value="${pmodes.ID}">${pmodes.pname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load pmodes. Please try again.');
        },
    });
});
$('#postModal').on('show.bs.modal', function (event) {  
    var selectedOption = $('#student option:selected');
    var selectedclass = $('#class').val();
    
    var admno = selectedOption.val();
    var stdname = selectedOption.text();classidpost

    var modal = $(this);
    modal.find('#admnopost').val(admno);
    modal.find('#classidpost').val(selectedclass);
    modal.find('#studentnamep').val(stdname);

    $.ajax({
        url: "{{ route('a.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#fitems');
            dropdown.empty();
            dropdown.append('<option value="">Select Item</option>');
            response.data.forEach(function (feeitems) {
                dropdown.append(
                    `<option value="${feeitems.ID}" data-amount="${feeitems.amount}">${feeitems.feename}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load Items. Please try again.');
        },
    });
});
$('#perclasModal').on('show.bs.modal', function (event) { 
    $.ajax({
        url: "{{ route('a.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#fitemspgroup');
            dropdown.empty();
            dropdown.append('<option value="">Select Item</option>');
            response.data.forEach(function (feeitems) {
                dropdown.append(
                    `<option value="${feeitems.ID}" data-amount="${feeitems.amount}">${feeitems.feename}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load Items. Please try again.');
        },
    });
    $.ajax({
        url: "{{ route('branches.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#campuspgroup');
            dropdown.empty();
            dropdown.append('<option value="">Select campus</option>');
            dropdown.append('<option value="0">All</option>');
            response.data.forEach(function (branch) {
                dropdown.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load streams. Please try again.');
        },
    });
    $.ajax({
        url: "{{ route('houses.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#house');
            dropdown.empty();
            dropdown.append('<option value="">Select House</option>');
            dropdown.append('<option value="0">All</option>');
            response.data.forEach(function (house) {
                dropdown.append(
                    `<option value="${house.ID}">${house.housen}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load classes. Please try again.');
        }
    });
   $.ajax({
        url: "{{ route('classes.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#classgroup');
            dropdown.empty();
            dropdown.append('<option value="">Select Class</option>');
           
            response.data.forEach(function (classes) {
                        dropdown.append(
                            `<option value="${classes.ID}">${classes.claname}</option>`
                        );
                    });
        },
        error: function () {
            alert('Failed to load classes. Please try again.');
        }
    });
});
// Set amount when item is selected
$('#fitems').on('change', function () {
    var amount = $(this).find(':selected').data('amount');
    $('#famount').val(amount || '');
});
$('#fitemspgroup').on('change', function () {    
     var feeid = $(this).val();
    var amount = $(this).find(':selected').data('amount');
    $('#amountpgroup').val(amount || '');
    $('#famountcamp').val(amount || '');
     $('#famounthous').val(amount || '');
      $('#famountcla').val(amount || '');
    $('#feeidcamp').val(feeid);
    $('#feeidhous').val(feeid);
    $('#feeidcla').val(feeid);
     
});
$('#postfeeform').on('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = {
        admno: $('#admno').val(),
        receiptdate: $('#receiptdate').val(),
        receiptno: $('#receiptno').val(),
        pamount: $('#pamount').val(),
        pmethod: $('#pmethod').val(),
        tcode: $('#tcode').val(),
        chequeno: $('#chequeno').val(),
        bankn: $('#bankn').val(),
        _token: $('meta[name="csrf-token"]').attr('content') // Add CSRF token
    };
    
    // Validate data
    if (!formData.admno) {
        alert('Please search for a student first');
        return;
    }
    
   const submitBtn = $(this).find('button[type="submit"]');
   const originalText = submitBtn.html();
   submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    // Submit payment
    $.ajax({
        url: "postFeePayment",
        type: "POST",
        data: formData,
        success: function(response) {
            if (response.success) {
                // Show success message with standard alert
                showAlert('success', 'Success!','Payment posted successfully');
                
                
                
               
                
                var admno = $('#admno').val();
                if (admno) {
                    loadBillingDetails(admno);
                } else {
                    clearBillingDetails();
                }
                $('#newreceiptModal').modal('hide');
                $('#admno').val('');
                $('#receiptdate').val('');
                $('#receiptno').val('');
                $('#pamount').val('');
                $('#pmethod').val('');
                $('#tcode').val('');
                $('#chequeno').val('');
                $('#bankn').val('');
            }
        },
        error: function(xhr) {
            console.error('Error posting payment:', xhr.responseText);
            
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.errors) {
                    // Display validation errors
                    let errorMessage = '\n';
                    
                    $.each(response.errors, function(field, errors) {
                        errorMessage += '- ' + errors[0] + '\n';
                    });
                    
                    showAlert('danger', 'error!', errorMessage);
                } else if (response.error) {
                    showAlert('danger', 'error!', response.error);
                } else {
                    showAlert('danger', 'error!','An error occurred while posting the payment');
                }
            } catch (e) {
                showAlert('danger', 'error!','An error occurred while processing the server response');
            }
        },
        complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
    });
});
$('#postindvform').on('submit', function(e) {
    e.preventDefault();
    const formData = {
        admno: $('#admnopost').val(),
        feeid: $('#fitems').val(),
        famount: $('#famount').val(),
        classid: $('#classidpost').val(),
        _token: $('meta[name="csrf-token"]').attr('content') // Add CSRF token
    };
    if (!formData.admno) {
        showAlert('warning', 'Warning!', 'Please search for a student first');
        return;
    }
    
    if (!formData.feeid) {
        showAlert('warning', 'Warning!', 'Please select a fee item');
        return;
    }
    
    if (!formData.famount || formData.famount <= 0) {
        showAlert('warning', 'Warning!', 'Please enter a valid amount');
        return;
    }
    $('#payment-loading').show();
    $('#payment-submit-btn').prop('disabled', true);
    $.ajax({
        url: "postnewitem",
        type: "POST",
        data: formData,
        success: function(response) {
            if (response.success) {
                // Show success message
                showAlert('success', 'Success!', response.message || 'Fee item posted successfully');
                
                // Close modal
                $('#postModal').modal('hide');
                
                // Reload billing details
                var admno = $('#admnopost').val();
                if (admno) {
                    loadBillingDetails(admno);
                } else {
                    clearBillingDetails();
                }
                
                // Clear form
                $('#postindvform')[0].reset();
                $('#admnopost').val('');
                $('#famount').val('');
                
            }
        },
        error: function(xhr) {
            console.error('Error posting fee item:', xhr.responseText);
            
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (xhr.status === 409) {
                    // Handle duplicate fee item (conflict)
                    showAlert('warning', 'Duplicate Item!', response.error || 'This fee item already exists for the student');
                } else if (response.errors) {
                    // Display validation errors
                    let errorMessage = '';
                    
                    $.each(response.errors, function(field, errors) {
                        errorMessage += '• ' + errors[0] + '\n';
                    });
                    
                    showAlert('danger', 'Validation Error!', errorMessage);
                } else if (response.error) {
                    // Handle other specific errors
                    let alertType = xhr.status === 404 ? 'warning' : 'danger';
                    let alertTitle = xhr.status === 404 ? 'Not Found!' : 'Error!';
                    showAlert(alertType, alertTitle, response.error);
                } else {
                    showAlert('danger', 'Error!', 'An error occurred while posting the fee item');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showAlert('danger', 'Error!', 'An error occurred while processing the server response');
            }
        },
        complete: function() {
            // Hide loading indicator
            $('#payment-loading').hide();
            $('#payment-submit-btn').prop('disabled', false);
        }
    });
});
$('#postpercampf').on('submit', function(e) { 
    e.preventDefault();
    
    const formData = {
        feeid: $('#feeidcamp').val(),
        famount: $('#famountcamp').val(),
        caid: $('#campuspgroup').val(),
        _token: $('meta[name="csrf-token"]').attr('content') // Add CSRF token
    };
    
    // Enhanced validation
    if (!formData.feeid) {
        showAlert('warning', 'Warning!', 'Please Select Fee Item');
        return;
    }
    
    if (!formData.famount || formData.famount <= 0) {
        showAlert('warning', 'Warning!', 'Please Enter Valid Fee Amount');
        return;
    }
    
    if (!formData.caid) {
        showAlert('warning', 'Warning!', 'Please Select Campus');
        return;
    }
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
    
    $.ajax({
        url: "postpercampus",
        type: "POST",
        data: formData,
        success: function(response) {
             console.log('Success response:', response);
            if (response.success) {
                // Enhanced success message with processing details
                let successMessage = response.message || 'Fee item posted successfully.';
        
        if (response.processed !== undefined) {
            successMessage += `\n\nProcessing Summary:`;
            successMessage += `\n• Students processed: ${response.processed}`;
            successMessage += `\n• Students skipped: ${response.skipped}`;
            successMessage += `\n• Total students in campus: ${response.total_students}`;
        }

        // Replace newlines for HTML rendering
        showAlert2('success', 'Success!', successMessage.replace(/\n/g, '<br>'));
       // showAlert('success', 'Success!', response.message);
                
                // Clear form
                $('#postpercampf')[0].reset();
                
                // Reset select elements to their default state
                $('#feeidcamp').val('').trigger('change');
                $('#campuspgroup').val('').trigger('change');
                $('#famountcamp').val('');
                
                var admno = $('#student').val();
                if (admno) {
                    loadBillingDetails(admno);
                } else {
                    clearBillingDetails();
                }
                // Close modal after a short delay to allow user to read the message
                setTimeout(function() {
                    $('#perclasModal').modal('hide');
                }, 2000);
            }
        },
        error: function(xhr) {
            console.error('Error posting fee item per campus:', xhr.responseText);
            
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (xhr.status === 422) {
                    // Handle validation errors or business logic errors
                    if (response.errors) {
                        // Display validation errors
                        let errorMessage = 'Please correct the following errors:\n';
                        
                        $.each(response.errors, function(field, errors) {
                            errorMessage += '• ' + errors[0] + '\n';
                        });
                        
                        showAlert('danger', 'Validation Error!', errorMessage);
                    } else if (response.error) {
                        // Handle business logic errors (no active period, no students, etc.)
                        showAlert('warning', 'Processing Error!', response.error);
                    }
                } else if (xhr.status === 404) {
                    // Handle not found errors
                    showAlert('warning', 'Not Found!', response.error || 'Requested resource not found');
                } else if (xhr.status === 500) {
                    // Handle server errors
                    showAlert('danger', 'Server Error!', 'An internal server error occurred. Please try again later.');
                } else {
                    // Handle other errors
                    showAlert('danger', 'Error!', response.error || 'An unexpected error occurred');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showAlert('danger', 'Error!', 'An error occurred while processing the server response');
            }
        },
        complete: function() {
            // Always restore the button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
})
$('#postperhousef').on('submit', function(e) { 
    e.preventDefault();
    
    const formData = {
        feeid: $('#feeidhous').val(),
        famount: $('#famounthous').val(),
        houseid: $('#house').val(),
        _token: $('meta[name="csrf-token"]').attr('content') // Add CSRF token
    };
    
    // Enhanced validation
    if (!formData.feeid) {
        showAlert('warning', 'Warning!', 'Please Select Fee Item');
        return;
    }
    
    
    
    if (!formData.houseid) {
        showAlert('warning', 'Warning!', 'Please Select a House');
        return;
    }
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
    
    $.ajax({
        url: "postperhouse",
        type: "POST",
        data: formData,
        success: function(response) {
             console.log('Success response:', response);
            if (response.success) {
                // Enhanced success message with processing details
                let successMessage = response.message || 'Fee item posted successfully.';
        
        if (response.processed !== undefined) {
            successMessage += `\n\nProcessing Summary:`;
            successMessage += `\n• Students processed: ${response.processed}`;
            successMessage += `\n• Students skipped: ${response.skipped}`;
            successMessage += `\n• Total students in House: ${response.total_students}`;
        }

        // Replace newlines for HTML rendering
        showAlert2('success', 'Success!', successMessage.replace(/\n/g, '<br>'));
       // showAlert('success', 'Success!', response.message);
                
                // Clear form
                $('#postperhousef')[0].reset();
                
                // Reset select elements to their default state
                $('#feeidhous').val('').trigger('change');
                $('#house').val('').trigger('change');
                $('#famounthous').val('');
                
                var admno = $('#student').val();
                if (admno) {
                    loadBillingDetails(admno);
                } else {
                    clearBillingDetails();
                }
                // Close modal after a short delay to allow user to read the message
                setTimeout(function() {
                    $('#perclasModal').modal('hide');
                }, 2000);
            }
        },
        error: function(xhr) {
            console.error('Error posting fee item per campus:', xhr.responseText);
            
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (xhr.status === 422) {
                    // Handle validation errors or business logic errors
                    if (response.errors) {
                        // Display validation errors
                        let errorMessage = 'Please correct the following errors:\n';
                        
                        $.each(response.errors, function(field, errors) {
                            errorMessage += '• ' + errors[0] + '\n';
                        });
                        
                        showAlert('danger', 'Validation Error!', errorMessage);
                    } else if (response.error) {
                        // Handle business logic errors (no active period, no students, etc.)
                        showAlert('warning', 'Processing Error!', response.error);
                    }
                } else if (xhr.status === 404) {
                    // Handle not found errors
                    showAlert('warning', 'Not Found!', response.error || 'Requested resource not found');
                } else if (xhr.status === 500) {
                    // Handle server errors
                    showAlert('danger', 'Server Error!', 'An internal server error occurred. Please try again later.');
                } else {
                    // Handle other errors
                    showAlert('danger', 'Error!', response.error || 'An unexpected error occurred');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showAlert('danger', 'Error!', 'An error occurred while processing the server response');
            }
        },
        complete: function() {
            // Always restore the button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
})
$('#postperclass').on('submit', function(e) { 
    e.preventDefault();
    
    const formData = {
        feeid: $('#feeidcla').val(),
        famount: $('#famountcla').val(),
        claid: $('#classgroup').val(),
        _token: $('meta[name="csrf-token"]').attr('content') // Add CSRF token
    };
    
    // Enhanced validation
    if (!formData.feeid) {
        showAlert('warning', 'Warning!', 'Please Select Fee Item');
        return;
    }
    
    
    
    if (!formData.claid) {
        showAlert('warning', 'Warning!', 'Please Select a House');
        return;
    }
    
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
    
    $.ajax({
        url: "postperclass",
        type: "POST",
        data: formData,
        success: function(response) {
             console.log('Success response:', response);
            if (response.success) {
                // Enhanced success message with processing details
                let successMessage = response.message || 'Fee item posted successfully.';
        
        if (response.processed !== undefined) {
            successMessage += `\n\nProcessing Summary:`;
            successMessage += `\n• Students processed: ${response.processed}`;
            successMessage += `\n• Students skipped: ${response.skipped}`;
            successMessage += `\n• Total students in Class: ${response.total_students}`;
        }

        // Replace newlines for HTML rendering
        showAlert2('success', 'Success!', successMessage.replace(/\n/g, '<br>'));
       // showAlert('success', 'Success!', response.message);
                
                // Clear form
                $('#postperclass')[0].reset();
                
                // Reset select elements to their default state
                $('#feeidcla').val('').trigger('change');
                $('#classgroup').val('').trigger('change');
                $('#famountcla').val('');
                
                var admno = $('#student').val();
                if (admno) {
                    loadBillingDetails(admno);
                } else {
                    clearBillingDetails();
                }
                // Close modal after a short delay to allow user to read the message
                setTimeout(function() {
                    $('#perclasModal').modal('hide');
                }, 2000);
            }
        },
        error: function(xhr) {
            console.error('Error posting fee item per campus:', xhr.responseText);
            
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (xhr.status === 422) {
                    // Handle validation errors or business logic errors
                    if (response.errors) {
                        // Display validation errors
                        let errorMessage = 'Please correct the following errors:\n';
                        
                        $.each(response.errors, function(field, errors) {
                            errorMessage += '• ' + errors[0] + '\n';
                        });
                        
                        showAlert('danger', 'Validation Error!', errorMessage);
                    } else if (response.error) {
                        // Handle business logic errors (no active period, no students, etc.)
                        showAlert('warning', 'Processing Error!', response.error);
                    }
                } else if (xhr.status === 404) {
                    // Handle not found errors
                    showAlert('warning', 'Not Found!', response.error || 'Requested resource not found');
                } else if (xhr.status === 500) {
                    // Handle server errors
                    showAlert('danger', 'Server Error!', 'An internal server error occurred. Please try again later.');
                } else {
                    // Handle other errors
                    showAlert('danger', 'Error!', response.error || 'An unexpected error occurred');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                showAlert('danger', 'Error!', 'An error occurred while processing the server response');
            }
        },
        complete: function() {
            // Always restore the button state
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
})
let currentAdmno = '';
    
    // Handle modal show event
    $('#viewreceiptModal').on('show.bs.modal', function (event) {
       
        var selectedOption = $('#student option:selected');
        currentAdmno = selectedOption.val();
        
        if (currentAdmno) {
            loadStudentReceipts(currentAdmno);
        } else {
            showError('Student admission number not found');
        }
    });
    // Function to load student receipts via AJAX
    function loadStudentReceipts(admno) {
        // Show loading indicator
        showLoading();
        
        $.ajax({
            url: '{{ route("get.student.receipts") }}', // Update with your actual route
            method: 'POST',
            data: {
                admno: admno,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    displayReceipts(response.receipts, response.total_amount, response.count);
                } else {
                    showError('Failed to load receipts');
                }
            },
            error: function(xhr, status, error) {
                hideLoading();
                
                let errorMessage = 'Failed to load receipts';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                
                showError(errorMessage);
            }
        });
    }

    // Function to display receipts in the table
    function displayReceipts(receipts, totalAmount, count) {
        const tbody = $('#receipts-table-body');
        tbody.empty();

        // Update summary
        $('#total-receipts').text(count);
        $('#total-amount').text('KSh ' + parseFloat(totalAmount).toLocaleString('en-US', {minimumFractionDigits: 2}));

        if (receipts.length === 0) {
            $('#receipts-content').hide();
            $('#no-receipts').show();
            return;
        }

        // Populate table
        receipts.forEach(function(receipt, index) {
            const row = `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${receipt.receiptno}</strong></td>
                    <td>${formatDate(receipt.receiptdate)}</td>
                    <td class="text-right">
                        <strong>KSh ${parseFloat(receipt.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong>
                    </td>
                    <td>
                        <span class="badge badge-info">${receipt.payment_method || 'N/A'}</span>
                    </td>
                    <td>
                        ${getReference(receipt)}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary print-receipt" data-receipt-id="${receipt.ID}">
                            <i class="icon-copy dw dw-print"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-info view-details" data-receipt='${JSON.stringify(receipt)}'>
                            <i class="icon-copy dw dw-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        $('#no-receipts').hide();
        $('#receipts-content').show();
    }

    // Helper function to get reference information
    function getReference(receipt) {
        if (receipt.tcode) {
            return `<small class="text-muted">Code: ${receipt.tcode}</small>`;
        } else if (receipt.chequeno) {
            return `<small class="text-muted">Cheque: ${receipt.chequeno}<br>Bank: ${receipt.bankn || 'N/A'}</small>`;
        } else {
            return '<small class="text-muted">-</small>';
        }
    }

    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Show loading state
    function showLoading() {
        $('#receipts-loading').show();
        $('#receipts-content').hide();
        $('#receipts-error').hide();
        $('#no-receipts').hide();
    }

    // Hide loading state
    function hideLoading() {
        $('#receipts-loading').hide();
    }

    // Show error message
    function showError(message) {
        $('#error-message').text(message);
        $('#receipts-error').show();
        $('#receipts-content').hide();
        $('#no-receipts').hide();
    }

    // Handle individual receipt print
    $(document).on('click', '.print-receipt', function() {
        var receiptId = $(this).data('receipt-id');
        var admno = $('#student').val();
        
         $('#receiptmodal').modal('show');

         if (!admno) {
        showPrintError('No student selected');
        return;
        }
        showPrintLoadingreci();

        $.ajax({
        url: '{{ route("preview.student.receipt") }}',
        method: 'POST',
        xhrFields: {
            responseType: 'blob' // This ensures we get binary PDF data
        },
        data: {
            admno: admno,
            receiptId: receiptId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            hidePrintLoadingreci();

            var blob = new Blob([data], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);

            var pdfViewer = `
                <iframe src="${url}" width="100%" height="500px" style="border: none;"></iframe>
            `;

            $('#receiptmodal .modal-body').html(pdfViewer);
        },
        error: function (xhr) {
            hidePrintLoadingreci();
            showPrintError('Failed to generate statement. Please try again.');
        }
    });
        
    });

    // Handle view receipt details
    $(document).on('click', '.view-details', function() {
        const receipt = $(this).data('receipt');
        // Show detailed view (you can create another modal or expand the row)
        showReceiptDetails(receipt);
    });

    // Handle print all receipts
    $('#print-receipts').on('click', function() {
        if (currentAdmno) {
            printAllReceipts(currentAdmno);
        }
    });

    // Placeholder functions - implement based on your needs
    function printReceipt(receiptId) {
        // Implement individual receipt printing
        window.open(`/print-receipt/${receiptId}`, '_blank');
    }

    function printAllReceipts(admno) {
        // Implement printing all receipts for student
        window.open(`/print-all-receipts/${admno}`, '_blank');
    }

    function showReceiptDetails(receipt) {
        // Show detailed receipt information
        alert('Receipt Details:\n' + JSON.stringify(receipt, null, 2));
        // You could create a detailed modal here instead
    }
    // Add this JavaScript to your existing script section

// Handle print modal show event
$('#printModal').on('show.bs.modal', function (event) {
    // Get current student information
    var selectedOption = $('#student option:selected');
    var admno = selectedOption.val();
    var studentText = selectedOption.text();
    
    if (admno && studentText !== 'Select Student') {
        // Parse student information (assuming format: "ADMNO - Name (Class)")
        var parts = studentText.split(' - ');
        var name = parts.length > 1 ? parts[1].split(' (')[0] : 'N/A';
        var classPart = studentText.match(/\(([^)]+)\)/);
        var className = classPart ? classPart[1] : 'N/A';
        
        $('#student-admno').text(admno);
        $('#student-name').text(name);
        $('#student-class').text(className);
        
        // Store current admission number for printing
        $('#printModal').data('current-admno', admno);
        
        // Reset form
        resetPrintForm();
    } else {
        showPrintError('Please select a student first');
    }
});

// Preview statement
$('#preview-statement').on('click', function () {
    var admno = $('#printModal').data('current-admno');

    if (!admno) {
        showPrintError('No student selected');
        return;
    }

    showPrintLoading();

    $.ajax({
        url: '{{ route("preview.student.statement") }}',
        method: 'POST',
        xhrFields: {
            responseType: 'blob' // This ensures we get binary PDF data
        },
        data: {
            admno: admno,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (data) {
            hidePrintLoading();

            var blob = new Blob([data], { type: 'application/pdf' });
            var url = URL.createObjectURL(blob);

            var pdfViewer = `
                <iframe src="${url}" width="100%" height="500px" style="border: none;"></iframe>
            `;

            $('#printModal .modal-body').html(pdfViewer);
        },
        error: function (xhr) {
            hidePrintLoading();
            showPrintError('Failed to generate statement. Please try again.');
        }
    });
});


// Download statement
$('#download-statement').on('click', function() {
    var admno = $('#printModal').data('current-admno');
    
    if (!admno) {
        showPrintError('No student selected');
        return;
    }
    
    showPrintLoading();
    
    $.ajax({
        url: '{{ route("print.student.statement") }}',
        method: 'POST',
        data: {
            admno: admno,
            format: $('#pdf-format').val(),
            orientation: $('#pdf-orientation').val(),
            include_summary: $('#include-summary').is(':checked'),
            include_header: $('#include-school-header').is(':checked'),
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        xhrFields: {
            responseType: 'blob'
        },
        success: function(data, status, xhr) {
            hidePrintLoading();
            
            // Create blob link to download
            var blob = new Blob([data], { type: 'application/pdf' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            
            // Get filename from response header or create default
            var filename = 'student_statement_' + admno + '_' + new Date().toISOString().slice(0, 10) + '.pdf';
            var disposition = xhr.getResponseHeader('Content-Disposition');
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) {
                    filename = matches[1].replace(/['"]/g, '');
                }
            }
            
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
            
            // Close modal after successful download
            $('#printModal').modal('hide');
            
            // Show success message
            showAlert('success', 'Statement downloaded successfully!');
        },
        error: function(xhr, status, error) {
            hidePrintLoading();
            
            let errorMessage = 'Failed to generate statement';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMessage = xhr.responseJSON.error;
            } else if (xhr.responseText) {
                try {
                    var errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.error || errorMessage;
                } catch (e) {
                    // If response is not JSON, use default message
                }
            }
            
            showPrintError(errorMessage);
        }
    });
});

// Helper functions for print modal
function showPrintLoading() {
    $('#print-loading').show();
    $('#print-options').hide();
    $('#print-error').hide();
    $('#preview-statement, #download-statement').prop('disabled', true);
}
function showPrintLoadingreci() {
    $('#print-loadingreci').show();
    $('#print-optionsreci').hide();
    $('#print-errorreci').hide();
    
}
function hidePrintLoading() {
    $('#print-loading').hide();
    $('#print-options').show();
    $('#preview-statement, #download-statement').prop('disabled', false);
}
function hidePrintLoadingreci() {
    $('#print-loadingreci').hide();
    $('#print-optionsreci').show();
   
}

function showPrintError(message) {
    $('#print-error-message').text(message);
    $('#print-error').show();
    $('#print-loading').hide();
    $('#print-options').show();
    $('#preview-statement, #download-statement').prop('disabled', false);
}

function resetPrintForm() {
    $('#print-error').hide();
    $('#print-loading').hide();
    $('#print-options').show();
    $('#pdf-format').val('A4');
    $('#pdf-orientation').val('portrait');
    $('#include-summary').prop('checked', true);
    $('#include-school-header').prop('checked', true);
    $('#preview-statement, #download-statement').prop('disabled', false);
}

function showAlert(type, message) {
    // Create alert element
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var iconClass = type === 'success' ? 'dw-checkmark' : 'dw-warning';
    
    var alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="icon-copy dw ${iconClass}"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Show alert at top of page or in a designated container
    if ($('#alert-container').length) {
        $('#alert-container').html(alertHtml);
    } else {
        $('body').prepend('<div id="alert-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' + alertHtml + '</div>');
    }
    
    // Auto-hide success alerts after 5 seconds
    if (type === 'success') {
        setTimeout(function() {
            $('#alert-container .alert').fadeOut();
        }, 5000);
    }
}
        });
    
        function loadClassesByCampus(campusId) {
            $.ajax({
                url: "{{ route('classes.getByCampus') }}",
                type: "GET",
                data: { campusId: campusId },
                success: function (response) {
                    const dropdown = $('#class');
                    dropdown.empty();
                    dropdown.append('<option value="">Select Class</option>');
                    response.data.forEach(function (classes) {
                        dropdown.append(
                            `<option value="${classes.ID}">${classes.claname}</option>`
                        );
                    });
                },
                error: function () {
                    alert('Failed to load classes. Please try again.');
                }
            });
        }
        function getstudents(selectedclassId) {
            $.ajax({
                url: "{{ route('billing.getstudents') }}",
                type: "GET",
                data: { selectedclassId: selectedclassId },
                success: function(response) {
                    const dropdown = $('#student');
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
        }
        function getrequired(selectedmethod) {
    $.ajax({
        url: "{{ route('pmodes.getrequired') }}",
        type: "GET",
        data: { pmethod: selectedmethod },
        success: function(response) {
            if (response.data && response.data.length > 0) {
                const pmode = response.data[0];
                
                // Handle tcode field
                if (pmode.tcode === 'Yes') {
                    $('#tcodefield').attr('hidden', false);
                    $('#tcode').prop('required', true);
                } else {
                    $('#tcodefield').attr('hidden', true);
                    $('#tcode').prop('required', false);
                }
                
                // Handle chequeno field
                if (pmode.chequeno === 'Yes') {
                    $('#chequenofield').attr('hidden', false);
                    $('#chequeno').prop('required', true);
                } else {
                    $('#chequenofield').attr('hidden', true);
                    $('#chequeno').prop('required', false);
                }
                
                // Handle bankn field
                if (pmode.bankn === 'Yes') {
                    $('#banknfield').attr('hidden', false);
                    $('#bankn').prop('required', true);
                } else {
                    $('#banknfield').attr('hidden', true);
                    $('#bankn').prop('required', false);
                }
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            alert('Failed to load payment method requirements. Please try again.');
        }
    });
}
        function loadBillingDetails(admno) {
    // Show loading spinner
    $('#billing-details-container').addClass('d-none');
    $('#no-data-message').addClass('d-none');
    $('#loading-spinner').removeClass('d-none');
    
    $.ajax({
        url: '{{ route("getStudentBillingDetails") }}',
        type: 'GET',
        data: { admno: admno },
        dataType: 'json',
        success: function(response) {
            displayBillingDetails(response);
            // Hide loading spinner
            $('#loading-spinner').addClass('d-none');
        },
        error: function(xhr, status, error) {
            console.error('Error loading billing details:', error);
            alert('Failed to load billing details. Please try again.');
            // Hide loading spinner
            $('#loading-spinner').addClass('d-none');
            $('#no-data-message').removeClass('d-none');
        }
    });
}

// Function to format date as DD/MMM/YYYY
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = date.getDate().toString().padStart(2, '0');
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = monthNames[date.getMonth()];
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Function to display billing details
function displayBillingDetails(data) {
    // Show billing details container and hide no data message
    $('#billing-details-container').removeClass('d-none');
    $('#no-data-message').addClass('d-none');
    
    // Display student name with icon
    $('#student-name-display').html('<i class="icon-copy dw dw-user1 mr-2"></i> Billing Details for: <span class="font-weight-bold">' + data.student.studentname + '</span>');
    
    // Clear previous data
    $('#billing-table-body').empty();
    
    // Check if there are billing details
    if (data.billing_details.length === 0) {
        $('#billing-table-body').html('<tr><td colspan="6" class="text-center py-0"><i class="icon-copy dw dw-folder-4 mr-2"></i>No billing records found for this student</td></tr>');
        $('#total-amount').text('0.00');
        $('#total-paid').text('0.00');
        $('#total-balance').text('0.00');
        return;
    }
    
    // Add each billing item to the table
    $.each(data.billing_details, function(index, item) {
        var formattedDate = formatDate(item.date_posted);
        var statusBadge = '';
        
        // Create status badge based on status value
        if(item.status === 'Paid') {
            statusBadge = '<span class="badge badge-success"><i class="icon-copy dw dw-check mr-1"></i>Paid</span>';
        } else if(item.status === 'Partial') {
            statusBadge = '<span class="badge badge-warning"><i class="icon-copy dw dw-time mr-1"></i>Partial</span>';
        } else {
            statusBadge = '<span class="badge badge-danger"><i class="icon-copy dw dw-close mr-1"></i>Unpaid</span>';
        }
        
        var row = `
            <tr class="billing-row">
                <td style="padding: 0.25rem;" class="py-2"><i class="icon-copy dw dw-calendar-3 mr-1 text-primary"></i>${formattedDate}</td>
                <td style="padding: 0.25rem;" class="py-2"><i class="icon-copy dw dw-file-31 mr-1 text-muted"></i>${item.feename}</td>
                <td style="padding: 0.25rem;" class="py-2 text-right font-weight-medium">${parseFloat(item.amount).toFixed(2)}</td>
                <td style="padding: 0.25rem;" class="py-2 text-right text-success font-weight-medium"><i class="icon-copy dw dw-money-1 mr-1"></i>${parseFloat(item.paid).toFixed(2)}</td>
                <td style="padding: 0.25rem;" class="py-2 text-right text-danger font-weight-medium"><i class="icon-copy dw dw-analytics-8 mr-1"></i>${parseFloat(item.balance).toFixed(2)}</td>
                <td style="padding: 0.25rem;" class="py-2 text-center">${statusBadge}</td>
            </tr>
        `;
        $('#billing-table-body').append(row);
    });
    
    // Update totals with icons and formatting
    $('#total-amount').html(`<i class="icon-copy dw dw-analytics mr-1"></i>${parseFloat(data.totals.total_amount).toFixed(2)}`);
    $('#total-paid').html(`<i class="icon-copy dw dw-money-2 mr-1"></i><span class="text-success">${parseFloat(data.totals.total_paid).toFixed(2)}</span>`);
    $('#total-balance').html(`<i class="icon-copy dw dw-analytics-11 mr-1"></i><span class="text-danger">${parseFloat(data.totals.total_balance).toFixed(2)}</span>`);
}
    
    // Function to clear billing details
    function clearBillingDetails() {
        $('#billing-details-container').addClass('d-none');
        $('#no-data-message').removeClass('d-none');
        $('#billing-table-body').empty();
        $('#student-name-display').text('');
        $('#total-amount').text('0.00');
        $('#total-paid').text('0.00');
        $('#total-balance').text('0.00');
    }
    </script>
</x-custom-admin-layout>