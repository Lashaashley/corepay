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
}
        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .form-label i {
            margin-right: 8px;
            color: #667eea;
        }
        .form-select, .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: var(--transition);
            background: white;
        }
        .marks-container {
            /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .marks-header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .marks-title {
            color: #2c3e50;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .marks-body {
            background: white;
            padding: 20px;
        }
        
        /* Optimized table for 50+ students */
        .marks-table-container {
            max-height: 60vh;
            overflow-y: auto;
            border-radius: 10px;
            box-shadow: inset 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .marks-table {
            margin: 0;
            font-size: 14px;
        }
        
        .marks-table thead th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 12px 8px;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
            text-align: center;
        }
        
        .marks-table tbody tr {
            height: 10px; /* Reasonable height for 50+ students */
            transition: all 0.2s ease;
        }
        
        .marks-table tbody tr:hover {
            background-color: #f8f9ff;
            transform: translateX(2px);
        }
        
        .marks-table tbody td {
            padding: 8px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        
        /* Compact student info */
        .student-admno {
            font-weight: 600;
            color: #495057;
            font-size: 13px;
        }
        
        .student-name {
            color: #2c3e50;
            font-size: 13px;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Enhanced mark input */
        .mark-input {
            height: 35px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .mark-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
            transform: scale(1.05);
        }
        
        .mark-input.valid {
            border-color: #28a745;
            background: #f8fff9;
        }
        
        .mark-input.invalid {
            border-color: #dc3545;
            background: #fff8f8;
        }
        
        /* Status badges */
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-draft {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-empty {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .status-finalized {
            background: #d4edda;
            color: #155724;
        }
        
        /* Action buttons */
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
            background: linear-gradient(135deg, #ffc107, #ff8c00);
            color: white;
        }
        
        .btn-finalize {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        /* Loading spinner */
        .loading-container {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Progress indicator */
        .progress-info {
            font-size: 12px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .progress-bar-mini {
            width: 100px;
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            transition: width 0.3s ease;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .marks-table {
                font-size: 12px;
            }
            
            .marks-table tbody tr {
                height: 45px;
            }
            
            .student-name {
                max-width: 120px;
            }
            
            .mark-input {
                height: 32px;
            }
        }
        
        /* Scrollbar styling */
        .marks-table-container::-webkit-scrollbar {
            width: 8px;
        }
        
        .marks-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .marks-table-container::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 1px;
        }
        
        .marks-table-container::-webkit-scrollbar-thumb:hover {
            background: #5a6fd8;
        }
/* Compact row styling for 50+ students */
.compact-row {
    height: 35px !important; /* Reduced from 50px */
    line-height: 1.2;
}

.compact-row td {
    padding: 4px 6px !important; /* Reduced from 8px */
    vertical-align: middle;
    font-size: 12px; /* Smaller font for compactness */
}

/* Compact input styling */
.compact-row .mark-input {
    height: 26px !important; /* Reduced from 35px */
    padding: 2px 6px !important;
    font-size: 12px;
    border-width: 1px;
}

.compact-row .mark-input:focus {
    transform: scale(1.02); /* Reduced scale effect */
}

/* Compact student info */
.compact-row .student-admno {
    font-size: 11px;
    font-weight: 600;
}

.compact-row .student-name {
    font-size: 11px;
    line-height: 1.3;
    max-width: 150px; /* Reduced width */
}

/* Compact status badges */
.compact-row .status-badge {
    padding: 2px 6px !important; /* Reduced from 4px 8px */
    font-size: 9px;
    border-radius: 8px;
}

/* Ultra-compact version for 50+ students */
.ultra-compact-row {
    height: 30px !important;
}

.ultra-compact-row td {
    padding: 2px 4px !important;
    font-size: 11px;
}

.ultra-compact-row .mark-input {
    height: 22px !important;
    padding: 1px 4px !important;
    font-size: 11px;
}

.ultra-compact-row .status-badge {
    padding: 1px 4px !important;
    font-size: 8px;
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
    
    <div class="min-height-200px"style="margin-top: -20px;">
        <div class="pd-20 card-box mb-30">
            <div class="row mb-4">
                <div class="col-md-3">
                     <label class="form-label">
                        <i class="fas fa-book"></i>
                        Subject:
                    </label>
                    <select name="subjectlist" id="subjectlist" class="form-select" required>

                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fas fa-project-diagram"></i>
                        Streams/Classes:
                    </label>
                    <select name="classlist" id="classlist" class="form-select" required onchange="loadStudents()">

                    </select>
                </div>
                 
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="fa fa-file-text-o"></i>
                        Exam:
                    </label>
                    <select name="ExamList" id="ExamList" class="form-select" required onchange="loadStudents()">

                    </select>
                </div>
               
            </div>
        </div>

    <!-- Students Marks Table -->
      <div class="pd-20 card-box mb-30 marks-container">
    <div id="marksSection">
        <!-- Enhanced Header -->
        <div class="marks-header">
            <h5 class="marks-title">
                <i class="fas fa-clipboard-check"></i>
                Enter Student Marks
            </h5>
        </div>
        
        <!-- Main Content -->
        <div class="marks-body">
            <!-- Loading State -->
            <div id="loadingState" class="loading-container" style="display: none;">
                <div class="loading-spinner"></div>
                <p class="mb-0">Loading students...</p>
                <small class="text-muted">Please wait while we fetch the student list</small>
            </div>
            
            <!-- Table Container -->
            <div id="tableContainer" style="display: none;">
                <div class="marks-table-container">
                    <table class="table table-bordered marks-table" id="marksTable">
                        <thead>
                            <tr style="line-height: 0.5;">
                                <th style="width: 15%;">Adm No</th>
                                <th style="width: 40%;">Student Name</th>
                                <th style="width: 25%;">Marks</th>
                                <th style="width: 20%;">Status</th>
                            </tr>
                        </thead>
                        <tbody id="marksTableBody">
                            <!-- Sample data for demonstration -->
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Action Buttons -->
        <div class="action-buttons">
            <div class="progress-info">
                <span>Progress:</span>
                <div class="progress-bar-mini">
                    <div class="progress-fill" style="width: 65%;"></div>
                </div>
                <span id="progressText">13/20 completed</span>
            </div>
            
            <div class="row">
                <div class="col-md-5">
                <button type="button" class="btn btn-enhanced btn-draft" id="saveDraftBtn">
                    <i class="fas fa-save"></i>
                     Draft
                </button>
                </div>
                <div class="col-md-3">
                <button type="button" class="btn btn-enhanced btn-finalize" id="finalizeBtn">
                    <i class="fas fa-check-circle"></i>
                    Finalize
                </button>
                </div>
            </div>
        </div>
    </div>
</div>
      </div>

</div>
                

    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<!--<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>



function loadStudents() {
    let subjectId = $('#subjectlist').val();
    let classId = $('#classlist').val();
    let examId = $('#ExamList').val();

    
    

    if (!subjectId || !classId || !examId) {
        showAlert('warning', 'Hint!', 'Please select an Exam type');
        return;
    }

  
    // Show loading state
    $('#loadingState').show();
    $('#tableContainer').hide();

    $.ajax({
        url: "{{ route('performance.students') }}",
        type: "GET",
        data: { subject_id: subjectId, class_id: classId, exam_id: examId  },
        success: function(response) {
            $('#marksTableBody').empty();

            if (!response.students || response.students.length === 0) {
                $('#loadingState').hide();
                showAlert('info', 'No Students', response.message || 'No students found for the selected criteria');
                return;
            }

            // Populate table with enhanced rows
            response.students.forEach(function(stu, index) {
    const statusClass = stu.marks !== null ? 'status-draft' : 'status-empty';
    const statusText = stu.marks !== null ? 'Draft' : 'Not Set';
    const inputClass = stu.marks !== null ? 'mark-input valid' : 'mark-input';
    
    $('#marksTableBody').append(`
        <tr class="table-row-animated compact-row" style="animation-delay: ${index * 0.05}s;">
            <td class="student-admno">${stu.admno}</td>
            <td class="student-name" title="${stu.sirname} ${stu.othername}">
                ${stu.sirname} ${stu.othername}
            </td>
            <td>
                <input type="number" class="form-control ${inputClass}"
                    data-admno="${stu.admno}"
                    value="${stu.marks ?? ''}" 
                    min="0" max="100" 
                    placeholder="0-100" />
            </td>
            <td>
                <span class="status-badge ${statusClass}">${statusText}</span>
            </td>
        </tr>
    `);
});

            // Hide loading and show table
            $('#loadingState').hide();
            $('#tableContainer').show();
            
            // Update progress
            updateProgress();
            
            // Add input validation
            attachInputValidation();
            
        },
        error: function(xhr) {
            $('#loadingState').hide();
            showAlert('danger', 'Error!', 'Unexpected error loading students');
            console.error(xhr.responseText);
        }
    });
}

// Input validation and real-time feedback
function attachInputValidation() {
    $('.mark-input').on('input', function() {
        const value = $(this).val();
        const $this = $(this);
        const $row = $this.closest('tr');
        const $statusBadge = $row.find('.status-badge');
        
        // Remove existing classes
        $this.removeClass('valid invalid');
        
        if (value === '') {
            $statusBadge.removeClass('status-draft status-finalized').addClass('status-empty').text('Not Set');
        } else if (value >= 0 && value <= 100) {
            $this.addClass('valid');
            $statusBadge.removeClass('status-empty status-finalized').addClass('status-draft').text('Draft');
        } else {
            $this.addClass('invalid');
        }
        
        // Update progress
        updateProgress();
    });
    
    // Focus effects
    $('.mark-input').on('focus', function() {
        $(this).closest('tr').addClass('table-active');
    }).on('blur', function() {
        $(this).closest('tr').removeClass('table-active');
    });
}
function loadclassBystream(campusId) {
  $.ajax({
    url: "{{ route('subjects.getBysub') }}",
    type: "GET",
    data: { campusId: campusId },
    success: function (response) {
      const dropdown = $('#classlist');
      dropdown.empty();
      dropdown.append('<option value="">Select Class/Stream</option>');

      if (response.data && response.data.length > 0) {
        const seen = new Set();

        response.data.forEach(function (item) {
          const key = item.type + '-' + item.entity_id; // unique by type+id
          if (!seen.has(key)) {
            seen.add(key);
            // store type as data attribute so you can send it back to server
            dropdown.append(
              `<option value="${item.entity_id}" data-type="${item.type}">${item.name}</option>`
            );
          }
        });
      } else {
        dropdown.append('<option value="">No classes found</option>');
      }
    },
    error: function () {
      alert('Failed to load classes. Please try again.');
    }
  });
}



// Progress tracking
function updateProgress() {
    const totalInputs = $('.mark-input').length;
    const filledInputs = $('.mark-input').filter(function() {
        return $(this).val() !== '' && $(this).val() >= 0 && $(this).val() <= 100;
    }).length;
    
    const percentage = totalInputs > 0 ? (filledInputs / totalInputs) * 100 : 0;
    
    $('.progress-fill').css('width', percentage + '%');
    $('#progressText').text(`${filledInputs}/${totalInputs} completed`);
}

// Placeholder for showAlert function (replace with your actual implementation)
function showAlert(type, title, message) {
    console.log(`${type.toUpperCase()}: ${title} - ${message}`);
    // Your existing alert implementation here
}

// Enhanced button interactions
$(document).ready(function() { 
    $('#saveDraftBtn').on('click', function() {
    let subjectId = $('#subjectlist').val();
    let classId = $('#classlist').val();
    let examId = $('#ExamList').val();
    
    let status = 'draft';

    let marks = [];
    $('.mark-input').each(function() {
        marks.push({
            admno: $(this).data('admno'),
            marks: $(this).val()
        });
    });

    const $btn = $(this);
    
    document.getElementById("finalizeBtn").disabled = true;
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');

    $.ajax({
        url: "{{ route('performance.save') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            subject_id: subjectId,
            class_id: classId,
            exam_id: examId,
            marks: marks,
            mstatus: status,
           
        },
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Draft');
            if (response.success) {
                showAlert('success', 'Success!', 'Draft saved successfully');
                document.getElementById("finalizeBtn").disabled = false;
            } else {
                showAlert('error', 'Error!', 'Save failed: ' + response.message);
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Draft');
            
            showAlert('error', 'Error!', 'Error saving draft.');
        }
    });
});

    
    $('#finalizeBtn').on('click', function() {
    let subjectId = $('#subjectlist').val();
    let classId = $('#classlist').val();
    let examId = $('#ExamList').val();
    
    let status = 'final';

    let marks = [];
    $('.mark-input').each(function() {
        marks.push({
            admno: $(this).data('admno'),
            marks: $(this).val()
        });
    });

    // check if any student is missing marks
    const emptyInputs = marks.filter(m => m.marks === '' || m.marks === null).length;
    if (emptyInputs > 0) {
        showAlert('warning', 'Incomplete!', `Please fill in marks for all students. ${emptyInputs} students still need marks.`);
        return;
    }

    const $btn = $(this);
    document.getElementById("saveDraftBtn").disabled = true;
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Finalizing...');

    $.ajax({
        url: "{{ route('performance.save') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            subject_id: subjectId,
            class_id: classId,
            exam_id: examId,
            marks: marks,
            mstatus: status,
            
        },
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Finalize');
            if (response.success) {
                showAlert('success', 'Success!', 'Marks finalized successfully');
                document.getElementById("saveDraftBtn").disabled = false;
            } else {
                showAlert('error', 'Error!', 'Save failed: ' + response.message);
            }
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fas fa-check-circle"></i> Finalize');
            showAlert('error', 'Error!', 'Error finalizing marks.');
        }
    });
});

    
    // Demo: Show table after a brief delay
    setTimeout(() => {
        $('#tableContainer').show();
        updateProgress();
        attachInputValidation();
    }, 1000);

     $('#subjectlist').on('change', function() {
          const selectedCampusId = $(this).val();
          if (selectedCampusId) {
            loadclassBystream(selectedCampusId);
           
          } else {
            // Clear classes dropdown if no campus is selected
          const classDropdown = $('#classlist');
          classDropdown.empty();
          classDropdown.append('<option value="">Select class</option>');

         
        }
        
      });
});

        $.ajax({
    url: "{{ route('subject.get') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#subjectlist');
        dropdown.empty();
        dropdown.append('<option value="">Select Subject</option>');
        response.data.forEach(function (teachers) {
            dropdown.append(
                `<option value="${teachers.ID}">${teachers.sname}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Classes. Please try again.');
    },
});

let currentPeriod = '';

$.ajax({
    url: "{{ route('periods.current') }}",
    type: "GET",
    success: function (response) {
        if (response.status === 'success') {
            currentPeriod = response.periodname;
        } else {
            currentPeriod = 'Unknown Period';
        }

        // After fetching period, fetch exams
        $.ajax({
            url: "{{ route('exam.getDropdown') }}",
            type: "GET",
            success: function (response) {
                const dropdown = $('#ExamList');
                dropdown.empty();
                dropdown.append('<option value="">Select Exam</option>');
                response.data.forEach(function (exam) {
                    dropdown.append(
                        `<option value="${exam.examname} - ${currentPeriod}">
                            ${exam.examname} - ${currentPeriod}
                         </option>`
                    );
                });
            },
            error: function () {
                alert('Failed to load exams. Please try again.');
            },
        });
    },
    error: function () {
        alert('Failed to load period info.');
    }
});

                
           function showAlert(type, title, message) {
    const statusMessage = $('#status-message');
    $('#alert-title').html(title);
    $('#alert-message').html(message);

    // Reset and show the alert with styles
    statusMessage
        .removeClass('alert-success alert-danger alert-warning alert-info') // Reset all types
        .addClass(`alert-${type}`)
        .css('display', 'block') // Make it block but still off-screen (opacity:0)
        .addClass('show'); // Slide it in and fade it in (opacity:1)

    // Auto hide after 5 seconds if not manually closed
    setTimeout(() => {
        if (statusMessage.hasClass('show')) {
            closeAlert(); // Use the centralized close function
        }
    }, 5000);
}

// Centralized function to close the alert with animation
function closeAlert() {
    const alert = $('#status-message');
    alert.removeClass('show'); // This triggers the slide-out/fade-out transition

    // Wait for the transition to finish before hiding it completely
    alert.one('transitionend webkitTransitionEnd oTransitionEnd', function() {
        if (!alert.hasClass('show')) { // Double-check it's still closed
            alert.hide();
        }
    });
}

// Use event delegation for the close button
$(document).on('click', '#status-message .close', function() {
    closeAlert();
});
    </script>
</x-custom-admin-layout>
