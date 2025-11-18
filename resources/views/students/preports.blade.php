<x-custom-admin-layout>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .custom-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    z-index: 9999;
    opacity: 0;
    transform: translateX(400px);
    transition: all 0.5s ease;
    display: none; /* Initially hidden via JS, but transition handled by opacity/transform */
}

.custom-alert.show {
    opacity: 1;
    transform: translateX(0);
    display: block; /* Needed to make it visible */
}

.alert-success {
    animation: successPulse 1s ease-in-out;
}

@keyframes successPulse {
    0% { transform: scale(0.95); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}  	.tab-container {
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
    font-size: 14px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff; /* Hide border bottom when active */
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}
.header-container{
    font-size: 0.5rem;
    display: flex;
    justify-content: center; /* Center horizontally */
    align-items: center;

}
.load-button-container {
    margin-top: 20px;
   
}
.toggle-container {
  position: relative;
  display: inline-block;
  width: 200px;
  height: 34px;
  background-color: #f0f0f0;
  border-radius: 34px;
  overflow: hidden;
  border: 1px solid #ccc;
  margin-bottom: 5px;
}

.toggle-container input {
  display: none;
}

.toggle-container label {
  display: inline-block;
  float: left;
  width: 50%;
  height: 100%;
  line-height: 34px;
  text-align: center;
  cursor: pointer;
  position: relative;
  z-index: 2;
  transition: color 0.3s;
  color: black;
}

.slider {
  position: absolute;
  top: 2px;
  left: 2px;
  width: 98px;
  height: 30px;
  border-radius: 30px;
  transition: transform 0.3s, background-color 0.3s;
}
#recint-toggle #recintre:checked ~ .slider {
  transform: translateX(0);
  background-color: #800080; /* Blue color for Variable */
}

#recint-toggle #separate:checked ~ .slider {
  transform: translateX(100px);
  background-color: #fa2007; /* Green color for Fixed */
}
#progress-modal {
  display: none; /* Hide by default */
}

#progress-modal .modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000; /* Ensure the modal is on top of other content */
}

#progress-modal .modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  text-align: center;
  width: 300px; /* Adjust width as needed */
}

#progress-modal #progress-bar-container {
  width: 100%;
  background-color: #f3f3f3;
  border: 1px solid #ddd;
  border-radius: 4px;
  overflow: hidden;
  height: 20px;
  margin-top: 10px;
}

#progress-modal #progress-bar {
  height: 100%;
  background-color: #4caf50;
  width: 0%;
}
	.btn-enhanced {
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    text-decoration: none;
    font-size: 0.875rem;
    cursor: pointer;
    min-width: 80px;
}

.btn-enhanced:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-draft {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-draft:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: white;
}
.btn-final {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
                #staffrpt-pdf-container iframe {
    width: 100%;
    height: 80vh;
    border: none;
}
.btn-download {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
.btn-download:hover {
    background: linear-gradient(135deg, #218838 0%, #17a589 100%);
}

/* --- Print Button (Info Gradient) --- */
.btn-print {
    background: linear-gradient(135deg, #007bff 0%, #00b4d8 100%);
}
.btn-print:hover {
    background: linear-gradient(135deg, #0069d9 0%, #0096c7 100%);
}
.modal-xl {
    max-width: 90%;
}

.modal-body {
    padding: 0;
}
@keyframes slideIn {
    from { right: -100px; opacity: 0; }
    to { right: 20px; opacity: 1; }
}

@keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
}
       
    </style>

    <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert">
    <strong id="alert-title"></strong> <span id="alert-message"></span>
    <button type="button" class="close" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


    <div class="mobile-menu-overlay"></div>
    <div class="pd-ltr-20">
            <h1 class="header-container">Reports Query</h1>
            <div class="tab-container" style="margin-top: -20px;">
                <button class="tab-button active" onclick="openTab(event, 'deductions')">Employees</button>
                <button class="tab-button" id="summaries-tab" onclick="openTab(event, 'summaries')">Summaries</button>
                <button class="tab-button" id="overview-tab" onclick="openTab(event, 'overview')">Overview</button>
                <button class="tab-button" id="variance-tab" onclick="openTab(event, 'variance')">Variance Reports</button>
                <button class="tab-button" id="binterface-tab" onclick="openTab(event, 'binterface')">Bank Interface</button>
            </div>
            <div id="deductions" class="tab-content active" style="margin-top: -20px;">
                <div class="card-box pd-20 height-100-p mb-30">
                    <div class="row align-items-center">
                        <div class="col-md-4 user-icon">
                            <form method="post">
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Staffs :</label>
                                        <select name="staffid" id="staffid" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Staff</option>
                                        </select>

                                    </div>
                                </div>
                               
                               
                                <div class="row">
                                    <div class="col-md-12">
                                        <label id="periodLabel">Select Period:</label>
                                        <select name="period" id="period" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                            
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 load-button-container">
                                        
                                        <button class="view-slip btn btn-enhanced btn-draft" id="vpslip">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                    <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                        <h5 class="text-center mb-4">Range Report</h5>
                        <form id="ragereportForm">
                            <div class="row align-items-center">
                                <div class="col-md-12 user-icon">
                                    <div class="row align-items-end">
                                        <div class="col-md-4">
                                            <label>Staff List:</label>
                                            <select name="staffSelect1" id="staffSelect1" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Staff List:</label>
                                            <select name="staffSelect2" id="staffSelect2" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
        
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label id="periodLabel">Period</label>
                                            <select name="periodto" id="periodto" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Period</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            
                                            <button class="forecast-slip btn btn-enhanced btn-draft" id="vpslip">
                                                <i class="fas fa-file-pdf"></i> Open
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="variance" class="tab-content">
        <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
            <h5 class="text-center mb-4">Payroll Items Variance</h5>
            <form id="summForm">
                <div class="row align-items-center">
                    <div class="col-md-12 user-icon">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label id="periodLabel">Payroll Item</label>
                                <select name="p2name" id="p2name" class="custom-select form-control" required="true" autocomplete="off">
                                    <option value="">Select Item</option>
                                </select>
                            </div>
                            <select name="staffSelectst" id="staffSelectst" class="custom-select form-control" required="true" autocomplete="off" hidden>
                                <option value="">Select Staff</option>
                            </select>
                            <select name="staffSelectnd" id="staffSelectnd" class="custom-select form-control" required="true" autocomplete="off" hidden>
                                <option value="">Select Staff</option>
                            </select>
                            <div class="col-md-2"> 
                                <label id="periodLabel">1<sup>st</sup> Period</label>
                                <select name="1stperiod" id="1stperiod" class="custom-select form-control" required="true" autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label id="periodLabel">2<sup>nd</sup> Period</label>
                                <select name="2ndperiod" id="2ndperiod" class="custom-select form-control" required="true" autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" type="button" id="varitem">
                                    <img src="../vendors/images/file.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                    <span>View</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
            <h5 class="text-center mb-4">Summary Variance</h5>
            <form id="summForm">
                <div class="row align-items-center">
                    <div class="col-md-12 user-icon">
                        <div class="row align-items-end">
                           
                            <div class="col-md-2"> 
                                <label id="periodLabel">1<sup>st</sup> Period</label>
                                <select name="s1stperiod" id="s1stperiod" class="custom-select form-control" required="true" autocomplete="off">
                                    <option value="">Select Period</option>
                                </select> 
                            </div>
                            <div class="col-md-2">
                                <label id="periodLabel">2<sup>nd</sup> Period</label>
                                <select name="s2ndperiod" id="s2ndperiod" class="custom-select form-control" required="true" autocomplete="off">
                                    <option value="">Select Period</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-primary w-100" type="button" id="varsitem">
                                    <img src="../vendors/images/file.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                    <span>View</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
            <div id="summaries" class="tab-content" style="margin-top: -20px;">
                <div class="card-box pd-20 height-100-p mb-30" >
                    <h5 class="text-center mb-4">Summaries</h5>
                    <form id="forecastForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto" id="periodoveral" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input class="btn btn-primary w-100" type="button" value="Open" id="openovral">
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Items Listing</h5>
                    <form id="forecastForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-3">
                                        <label id="periodLabel">Payroll Item</label>
                                        <select name="pname" id="pname" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                            <label>Staff List:</label>
                                            <select name="staffSelect3" id="staffSelect3" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Staff List:</label>
                                            <select name="staffSelect4" id="staffSelect4" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto2" id="periodoveral2" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input class="btn btn-primary w-100" type="button" value="Open" id="openitems">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Statutories Returns</h5>
                    <form id="forecastForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label id="periodLabel">Statutory Item</label>
                                        <select name="statutory" id="statutory" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Item</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                            <label>Staff List:</label>
                                            <select name="staffSelect5" id="staffSelect5" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Staff List:</label>
                                            <select name="staffSelect6" id="staffSelect6" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label id="periodLabel">Period</label>
                                            <select name="periodto3" id="periodoveral3" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Period</option>
                                            </select>
                                        </div>
                                    <div class="col-md-2">
                                        
                                        <button class="btn btn-primary w-100 d-flex align-items-center justify-content-center" type="button" id="openstatutory">
                                        <img src="../vendors/images/file.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                        <span>Open</span>
                                    </button>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <button class="btn btn-info w-100" type="button" id="downstatutory">
                                        <img src="../vendors/images/excel.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                        <span>Download</span>
                                    </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="overview" class="tab-content" >
            <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Payroll Summary</h5>
                    <form id="summForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                            <label>Staff List:</label>
                                            <select name="staffSelect7" id="staffSelect7" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Staff List:</label>
                                            <select name="staffSelect8" id="staffSelect8" class="custom-select form-control" required="true" autocomplete="off">
                                                <option value="">Select Staff</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto4" id="periodoveral4" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <button class="btn btn-primary w-100" type="button" id="prolsum">
                                            <img src="../vendors/images/file.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>View</span>
                                        </button>
                                        
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-info w-100" type="button" id="excelsum">
                                            <img src="../vendors/images/excel.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>Download</span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">P10 KRA</h5>
                    <form id="p10kraForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto5" id="periodoveral5" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <button class="btn btn-info w-100" type="button" id="p10kra">
                                            <img src="../vendors/images/excel.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>Download</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Payment Advices</h5>
                    <form id="etransForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto6" id="periodoveral6" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="toggle-container" id="recint-toggle">
                                        <input type="radio" id="recintre" name="recintres" value="Etransfer" required checked>
                                        <label for="recintre">Bank</label>
                                        <input type="radio" id="separate" name="recintres" value="cheque">
                                        <label for="separate">Cheque</label>
                                        <span class="slider"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <button class="btn btn-primary w-100" type="button" id="banktrans">
                                            <img src="../vendors/images/file.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>View</span>
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        
                                        <button class="btn btn-info w-100" type="button" id="banktransexce">
                                            <img src="../vendors/images/excel.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>Download</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div id="binterface" class="tab-content" >
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Immediate Fund Transfer(IFT)</h5>
                    <form id="iftForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto7" id="periodoveral7" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-info w-100" type="button" id="iftgen">
                                            <img src="../vendors/images/excel.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>Download</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Electronic Fund Transfer(EFT)</h5>
                    <form id="eftgenForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto8" id="periodoveral8" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-info w-100" type="button" id="eftgen">
                                            <img src="../vendors/images/excel.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>Download</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-box pd-20 height-100-p mb-30" style="margin-top: -20px;">
                    <h5 class="text-center mb-4">Real-Time Gross Settlement (RTGS)</h5>
                    <form id="etransForm">
                        <div class="row align-items-center">
                            <div class="col-md-12 user-icon">
                                <div class="row align-items-end">
                                    <div class="col-md-2">
                                        <label id="periodLabel">Period</label>
                                        <select name="periodto9" id="periodoveral9" class="custom-select form-control" required="true" autocomplete="off">
                                            <option value="">Select Period</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-info w-100" type="button" id="rtgsgen">
                                            <img src="../vendors/images/excel.png" alt="Gmail Icon" class="img-fluid" style="width: 25px; height: 25px;">
                                            <span>View</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    
    

    
    
    
 <div class="modal fade" id="staffreportModal" tabindex="-1" role="dialog" aria-labelledby="staffreportModalLabel" aria-hidden="true"> <div class="modal-dialog modal-lg" role="document"> <div class="modal-content"> <div class="modal-header"> <h5 class="modal-title">Report Viewer</h5> <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> </div> <div class="modal-body"> <div id="staffrpt-pdf-container" style="height: 600px; overflow: hidden;"> <p class="text-center">Loading report...</p> </div> </div> </div> </div> </div> 
    <div id="progress-modal">
        <div class="modal-overlay">
            <div class="modal-content">
                <h6>Downloading</h6>
                <div id="progress-bar-container">
                    <div id="progress-bar"></div>
                </div>
                <p id="progress-message">Downloading...</p>
            </div>
        </div>
    </div>
                

    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<!--<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#staffid').select2({
    placeholder: "Select Staff",
    allowClear: true,
    ajax: { 
        url: '{{ route("preports.search") }}', // Use Laravel route
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page || 1
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;
            
            return {
                results: data.results,
                pagination: {
                    more: data.pagination && data.pagination.more
                }
            };
        },
        cache: true
    },
    minimumInputLength: 0,
    templateResult: formatStaff,
    templateSelection: formatStaffSelection
});

// Format the staff option display
function formatStaff(staff) {
    if (staff.loading) {
        return staff.text;
    }
    return $('<span>' + staff.text + '</span>');
}

// Format the selected staff display
function formatStaffSelection(staff) {
    return staff.text || staff.id;
}
$('#period')
        .html('<option value="">Loading...</option>');
    
    $.ajax({
        url: '{{ route("summary.data") }}',
        type: 'GET',
        dataType: 'json',
        cache: true,
        timeout: 30000,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.error) {
                console.error("Error: " + data.error);
                
                // Handle session expiration
                if (data.error === 'Session expired' || data.error === 'Unauthorized access') {
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = '{{ route("login") }}';
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#period').html(periodHtml);
               
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            
            if (xhr.status === 403) {
                showMessage('Security token expired. Please refresh the page.', true);
                location.reload();
            } else if (xhr.status === 401) {
                showMessage('Your session has expired. Please login again.', true);
                window.location.href = '{{ route("login") }}';
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
    $(document).on('click', '#openovral', function (e) {
    e.preventDefault();

   var period = $('#periodoveral').val();
   if (!period) {
        showMessage('Please select a Period', true);
        return;
    }
    var actionTaken = false;

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
    url: '{{ route("reports.overall-summary") }}',
    method: 'POST',
    dataType: 'json',
    data: { 
        period: period,
        _token: '{{ csrf_token() }}'
    },
    success: function (response) {
        if (response.pdf) {
            var pdfBlob = new Blob(
                [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                { type: 'application/pdf' }
            );
            var pdfUrl = URL.createObjectURL(pdfBlob);

            // Log "OPEN"
            //logaudit(period, 'OPEN', `Company Summary ${period}`);

            var pdfViewerHTML = `
                <div class="pdf-viewer-wrapper">
                    <div class="pdf-actions mb-1">
                        <button id="downloadPdfBtn" class="btn btn-enhanced btn-download">
                            <i class="fas fa-download"></i> Download
                        </button>
                        <button id="printPdfBtn" class="btn btn-enhanced btn-print">
                            <i class="icon-copy fa fa-print"></i> Print
                        </button>
                    </div>
                    <iframe 
                        id="staffrptPdfFrame" 
                        src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                        width="100%" 
                        height="80vh" 
                        style="border:1px solid #ddd;"
                    ></iframe>
                </div>`;

            $('#staffrpt-pdf-container').html(pdfViewerHTML);

            // PRINT button handler
            $('#printPdfBtn').on('click', function () {
                var iframe = document.getElementById('staffrptPdfFrame');
                iframe.contentWindow.focus();
                iframe.contentWindow.print();

                if (!actionTaken) {
                    actionTaken = true;
                   // logaudit(period, 'PRINT', `Company Summary ${period}`);
                }
            });

            // DOWNLOAD button handler
            $('#downloadPdfBtn').on('click', function () {
                var link = document.createElement('a');
                link.href = pdfUrl;
                link.download = `Company Summary_${period}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                if (!actionTaken) {
                    actionTaken = true;
                   // logaudit(period, 'DOWNLOAD', `Company Summary ${period}`);
                }
            });
        } else {
            $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Failed to generate PDF.</p>');
        }
    },
    error: function (xhr, status, error) {
        console.error("AJAX error:", error);
        $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Error fetching report.</p>');
    }
});
});
    $(document).on('click', '.view-slip', function (e) {
    e.preventDefault();

    var staffid = $('#staffid').val();
    var period = $('#period').val();
    var actionTaken = false;
    
    if (!staffid) {
        showMessage('Please select a Staff', true);
        return;
    }
    
    if (!period) {
        showMessage('Please select a Period', true);
        return;
    }

    // Reset modal content before loading
    $('#staffrpt-pdf-container').html('<p class="text-center m-4">Loading report...</p>');
    $('#staffreportModal').modal('show');

    $.ajax({
        url: '{{ route("payslip.generate") }}', // Laravel route
        method: 'POST',
        dataType: 'json',
        data: { 
            staffid: staffid, 
            period: period,
            _token: '{{ csrf_token() }}' // CSRF token
        },
        success: function (response) {
            if (response.pdf) {
                var pdfBlob = new Blob(
                    [Uint8Array.from(atob(response.pdf), c => c.charCodeAt(0))],
                    { type: 'application/pdf' }
                );
                var pdfUrl = URL.createObjectURL(pdfBlob);

                // Log "OPEN"
                //logaudit(staffid, 'OPEN', `Payslip for ${period}`);

                var pdfViewerHTML = `
                    <div class="pdf-viewer-wrapper">
                        <div class="pdf-actions mb-1">
                            <button id="downloadPdfBtn" class="btn btn-enhanced btn-download">
                                <i class="fas fa-download"></i> Download
                            </button>
                            <button id="printPdfBtn" class="btn btn-enhanced btn-print">
                                <i class="icon-copy fa fa-print"></i> Print
                            </button>
                        </div>
                        <iframe 
                            id="staffrptPdfFrame" 
                            src="${pdfUrl}#toolbar=0&navpanes=0&scrollbar=0" 
                            width="100%" 
                            height="80vh" 
                            style="border:1px solid #ddd;"
                        ></iframe>
                    </div>`;

                $('#staffrpt-pdf-container').html(pdfViewerHTML);

                // PRINT button handler
                $('#printPdfBtn').on('click', function () {
                    var iframe = document.getElementById('staffrptPdfFrame');
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();

                    if (!actionTaken) {
                        actionTaken = true;
                       // logaudit(staffid, 'PRINT', `Payslip for ${period}`);
                    }
                });

                // DOWNLOAD button handler
                $('#downloadPdfBtn').on('click', function () {
                    var link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `payslip_${staffid}_${period}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    if (!actionTaken) {
                        actionTaken = true;
                        //logaudit(staffid, 'DOWNLOAD', `Payslip for ${period}`);
                    }
                });
            } else {
                $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Failed to generate PDF.</p>');
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", error);
            $('#staffrpt-pdf-container').html('<p class="text-danger text-center mt-3">Error fetching report.</p>');
        }
    });
});
         });
        $('#summaries-tab').on('click', function() {
    // Show loading state
    $('#periodoveral, #pname, #staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6, #periodoveral2, #periodoveral3, #statutory')
        .html('<option value="">Loading...</option>');
    
    $.ajax({
        url: '{{ route("summary.data") }}',
        type: 'GET',
        dataType: 'json',
        cache: true,
        timeout: 30000,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.error) {
                console.error("Error: " + data.error);
                
                // Handle session expiration
                if (data.error === 'Session expired' || data.error === 'Unauthorized access') {
                    showMessage('Your session has expired. Please login again.', true);
                    window.location.href = '{{ route("login") }}';
                    return;
                }
                
                showMessage('Error loading data: ' + data.error, true);
            } else if (data.success) {
                // Populate period dropdowns
                const periodHtml = '<option value="">Select Period</option>' + 
                    data.periodOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#periodoveral, #periodoveral2, #periodoveral3').html(periodHtml);
                
                // Populate pname dropdown
                const pnameHtml = '<option value="">Select Item</option>' + 
                    data.pnameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#pname').html(pnameHtml);
                
                // Populate staff dropdowns
                const staffHtml = '<option value="">Select Staff</option>' + 
                    data.snameOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#staffSelect3, #staffSelect4, #staffSelect5, #staffSelect6').html(staffHtml);
                
                // Populate statutory dropdown
                const statutoryHtml = '<option value="">Select Item</option>' + 
                    data.statutoryOptions.map(opt => 
                        `<option value="${opt.value}">${opt.text}</option>`
                    ).join('');
                $('#statutory').html(statutoryHtml);
                
                // Initialize Select2 for pname
                if (!$('#pname').hasClass("select2-hidden-accessible")) {
                    $('#pname').select2({
                        placeholder: "Select Item",
                        allowClear: true
                    });
                }
                
                // Initialize Select2 for staff selects
                ['#staffSelect3', '#staffSelect4', '#staffSelect5', '#staffSelect6'].forEach(function(selector) {
                    if (!$(selector).hasClass("select2-hidden-accessible")) {
                        $(selector).select2({
                            placeholder: selector.includes('3') || selector.includes('6') ? "Select Staff" : "Search",
                            allowClear: true
                        });
                    }
                });
                
                // Auto-select first and last staff for range selections
                var options3 = $('#staffSelect3 option:not([value=""])');
                if (options3.length > 0) {
                    $('#staffSelect3').val(options3.first().val()).trigger('change');
                    $('#staffSelect4').val(options3.last().val()).trigger('change');
                }
                
                var options5 = $('#staffSelect5 option:not([value=""])');
                if (options5.length > 0) {
                    $('#staffSelect5').val(options5.first().val()).trigger('change');
                    $('#staffSelect6').val(options5.last().val()).trigger('change');
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
            
            if (xhr.status === 403) {
                showMessage('Security token expired. Please refresh the page.', true);
                location.reload();
            } else if (xhr.status === 401) {
                showMessage('Your session has expired. Please login again.', true);
                window.location.href = '{{ route("login") }}';
            } else {
                showMessage('Failed to load data. Please refresh the page.', true);
            }
        }
    });
});function openTab(evt, tabName) { 
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

function showMessage(message, isError) {
    let messageDiv = $('#messageDiv');
    const backgroundColor = isError ? '#f44336' : '#4CAF50';
    
    if (messageDiv.length === 0) {
        // Create new message div with proper background color
        messageDiv = $(`
            <div id="messageDiv" style="
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 5px;
                color: white;
                z-index: 1051;
                display: block;
                font-weight: bold;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                animation: slideIn 0.5s, fadeOut 0.5s 2.5s;
                background-color: ${backgroundColor};
            ">
                ${message}
            </div>
        `);
        $('body').append(messageDiv);
    } else {
        // Update existing message div
        messageDiv.text(message)
                 .show()
                 .css('background-color', backgroundColor);
    }
    
    // Clear any existing timeout
    if (messageDiv.data('timeout')) {
        clearTimeout(messageDiv.data('timeout'));
    }
    
    // Set new timeout and store reference
    const timeoutId = setTimeout(() => {
        messageDiv.fadeOut();
    }, 3000);
    
    messageDiv.data('timeout', timeoutId);
}

    </script>
</x-custom-admin-layout>
