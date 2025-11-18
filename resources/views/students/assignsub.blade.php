<x-custom-admin-layout>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
        
         :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            --danger-gradient: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            --secondary-gradient: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            --modal-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }

        .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: var(--modal-shadow);
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            overflow: hidden;
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            padding: 1.5rem 2rem;
            border: none;
            position: relative;
        }

        .modal-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 50%, rgba(255,255,255,0.3) 100%);
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.4rem;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .modal-title i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.8;
            transition: var(--transition);
        }

        .btn-close:hover {
            background: rgba(255, 255, 255, 0.3);
            opacity: 1;
            transform: rotate(90deg);
        }

        .form-section {
            background: white;
            border-radius: 15px;
            padding: 0.5rem;
            margin: 1rem;
            box-shadow: var(--card-shadow);
            border: 1px solid rgba(0, 0, 0, 0.05);
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

        .form-select:focus, .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            outline: none;
        }

        .student-list-container {
            background: white;
            border-radius: 15px;
            padding: 0.5rem;
            box-shadow: var(--card-shadow);
            height: 400px;
            display: flex;
            flex-direction: column;
        }

        .list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .list-title {
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            display: flex;
            align-items: center;
        }

        .list-title i {
            margin-right: 8px;
        }

        .student-count {
            background: var(--primary-gradient);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            z-index: 1;
        }

        .search-input {
            padding-left: 45px;
            border: 2px solid #e2e8f0;
            border-radius: 25px;
            background: #f8f9fa;
            transition: var(--transition);
        }

        .search-input:focus {
            background: white;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .student-list {
            flex: 1;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0;
            background: #f8f9fa;
            overflow-y: auto;
            min-height: 250px;
        }

        .student-item {
            padding: 0.75rem 1rem;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: var(--transition);
            display: block;
            width: 100%;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #2d3748;
        }

        .student-item:hover {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            transform: translateX(5px);
        }

        .student-item.selected {
            background: var(--primary-gradient);
            color: white;
            font-weight: 500;
        }

        .student-item:last-child {
            border-bottom: none;
        }

        .student-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
}

.student-name {
    font-weight: 500;
    margin-right: 10px;
}

.student-details {
    font-size: 0.85rem;
    opacity: 0.8;
    text-align: right;
}

        .transfer-controls {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            padding: 2rem 0;
        }

        .transfer-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .transfer-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: var(--transition);
        }

        .transfer-btn:hover::before {
            left: 100%;
        }

        .btn-assign {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }

        .btn-assign:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        .btn-unassign {
            background: var(--danger-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(255, 65, 108, 0.3);
        }

        .btn-unassign:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(255, 65, 108, 0.4);
        }

        .transfer-all-btn {
            width: 45px;
            height: 35px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .modal-footer {
            background: #f8f9fa;
            border: none;
            padding: 1.5rem 2rem;
            border-radius: 0 0 20px 20px;
        }

        .btn-footer {
            padding: 0.75rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition);
            border: none;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-secondary:hover {
            background: #cbd5e0;
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--success-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(17, 153, 142, 0.4);
        }

        .modal.fade .modal-dialog {
            transition: var(--transition);
            transform: scale(0.8) translateY(-50px);
        }

        .modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }

        /* Custom scrollbar */
        .student-list::-webkit-scrollbar {
            width: 6px;
        }

        .student-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .student-list::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 3px;
        }

        .student-list::-webkit-scrollbar-thumb:hover {
            background: #5a67d8;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modal-dialog {
                margin: 1rem;
                max-width: none;
            }
            
            .transfer-controls {
                flex-direction: row;
                padding: 1rem;
            }
            
            .transfer-btn {
                width: 40px;
                height: 40px;
            }
        }

        /* Loading state */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
        .btn-finalize {
            background: linear-gradient(135deg, #ffc107, #ff8c00);
            color: white;
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
                <h4 class="header-container">Assign Subjects</h4>
                <input type="hidden" name="form_type" value="assignform">
                
                <!-- Classes Card -->
                <div class="card-box pd-20 height-100-p mb-30">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-project-diagram"></i>Streams:
                            </label>
                            <select name="classes" id="classes" class="custom-select form-control" required>

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label ></label>
                            <button type="button" id="assignsub" class="btn btn-enhanced btn-finalize" data-toggle="modal" data-target="#electiveModal">
                                <i class="fas fa-plus-square"></i>Assign per subject
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Fee Items Card -->
                <div class="card-box pd-20 height-100-p mb-30">
                    <h5 class="text-center mb-4"><i class="fas fa-book" style="color: #667eea; margin-right: 8px;"></i>Subjects</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="subjectsContainer" class="row">
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        
                        <button type="submit" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-share-square"></i>Assign Subjects
                                    </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
   <div class="modal fade" id="electiveModal" tabindex="-1" aria-labelledby="electiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="electiveModalLabel">
                        <i class="fas fa-graduation-cap"></i>
                        Assign Students
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Selection Section -->
                <div class="form-section">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-users"></i>Class:
                            </label>
                            <select name="classlist" id="classlist" class="form-select" required>

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-book"></i>
                                Subject:
                            </label>
                            <select name="subjectlist" id="subjectlist" class="form-select" required onchange="openElectiveModal()">
                                
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="fas fa-chalkboard-teacher"></i>
                                Teacher:
                            </label>
                            <select name="teacherlist" id="teacherlist" class="form-select" required>
                               
                                
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Available Students -->
                        <div class="col-lg-5">
                            <div class="student-list-container">
                                <div class="list-header">
                                    <h6 class="list-title">
                                        <i class="fas fa-users text-primary"></i>
                                        Available Students
                                    </h6>
                                    <span class="student-count" id="availableCount">0</span>
                                </div>
                                <div class="search-container">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchAvailable" class="form-control search-input" placeholder="Search by AdmNo or Name...">
                                </div>
                                <div id="availableStudents" class="student-list">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Transfer Controls -->
                        <div class="col-lg-2">
                            <div class="transfer-controls">
                                <button type="button" class="transfer-btn btn-assign" onclick="moveSelected('availableStudents','assignedStudents')" title="Assign Selected Students">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                <button type="button" class="transfer-btn transfer-all-btn btn-assign" onclick="moveAll('availableStudents','assignedStudents')" title="Assign All Students">
                                    <i class="fas fa-angle-double-right"></i>
                                </button>
                                <button type="button" class="transfer-btn transfer-all-btn btn-unassign" onclick="moveAll('assignedStudents','availableStudents')" title="Remove All Students">
                                    <i class="fas fa-angle-double-left"></i>
                                </button>
                                <button type="button" class="transfer-btn btn-unassign" onclick="moveSelected('assignedStudents','availableStudents')" title="Remove Selected Students">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Assigned Students -->
                        <div class="col-lg-5">
                            <div class="student-list-container">
                                <div class="list-header">
                                    <h6 class="list-title">
                                        <i class="fas fa-user-check text-success"></i>
                                        Assigned Students
                                    </h6>
                                    <span class="student-count" id="assignedCount">0</span>
                                </div>
                                <div class="search-container">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="searchAssigned" class="form-control search-input" placeholder="Search by AdmNo or Name...">
                                </div>
                                <div id="assignedStudents" class="student-list">
                                    <div class="loading">
                                        <div class="spinner"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-footer" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success btn-footer" onclick="saveElectiveStudents()">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    

<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

<!--<script src="{{ asset('js/custom-dropdown.js') }}"></script>--->
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#feeassignF').on('submit', function(e) {
                e.preventDefault();
                const classId = $('#classes').val();
                const selectedSubjects = [];
                let validationFailed = false;
                $('input[name="subjects[]"]:checked').each(function() {
                    const subjectId = $(this).val();
                    const teacherId = $(`select[name="teachers[${subjectId}]"]`).val();
                    // Require teacher for each selected subject
                    if (!teacherId) {
                        showAlert('danger', 'Error!', 'Please select a teacher for all chosen subjects.');
                        validationFailed = true;
                        return false;
                    }
                    selectedSubjects.push({
                        subid: subjectId,
                        teacherid: teacherId
                    });
                });
                if (validationFailed) return;
                if (!classId) {
                    showAlert('danger', 'Error!', 'Please select a class');
                    return;
                }
                if (selectedSubjects.length === 0) {
                    showAlert('danger', 'Error!', 'Please select at least one Subject');
                    return;
                }
                const formData = {
                    classid: classId,
                    subjects: selectedSubjects,
                    _token: $('meta[name="csrf-token"]').attr('content')
                };
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                $.ajax({
                    url: "{{ route('feesubassign.store') }}",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(formData),
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', 'Success!', 'Subjects assigned successfully!');
                            $('#feeassignF')[0].reset();
                $('.teacher-select').val(null).trigger('change');
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

// Cache teachers for reuse
let TEACHERS = [];
function fetchTeachers() {
    return $.ajax({
        url: "{{ route('teachers.getDropdown') }}",
        type: "GET"
    }).then(function (response) {
        TEACHERS = response.data || []; // { ID, teachername }
    });
}
function buildTeacherOptions(selectedId) {
    let html = '<option value="">Select Teacher</option>';
    const selectedStr = selectedId != null ? String(selectedId) : '';
    TEACHERS.forEach(t => {
        const isSelected = String(t.ID) === selectedStr ? ' selected' : '';
        html += `<option value="${t.ID}"${isSelected}>${t.teachername}</option>`;
    });
    return html;
}
function loadSubjectsForClass(classId) {
    const $container = $('#subjectsContainer');
    

     $container.empty();
    $container.html(`
        <div class="col-12 text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Loading Subjects & Teachers...</p>
        </div>
    `);

    $.ajax({
        url: "{{ route('sub.getDropdown') }}",
        type: "GET",
        data: { classid: classId }, // backend { data: [...], assigned: { [subid]: teacherid } }
        success: function (response) {
            const assigned = response.assigned || {};
            $container.empty();

            response.data.forEach(function (subject) {
                const subId = subject.ID;
                const assignedTeacher = assigned[subId] || '';
                const isAll = subject.isall === 'Yes';

                // If subject is compulsory or previously assigned, mark as checked.
                const checkedAttr  = (isAll || assignedTeacher) ? 'checked' : '';
                const disabledAttr = isAll ? 'disabled' : '';
                const hiddenInput  = isAll ? `<input type="hidden" name="subjects[]" value="${subId}">` : '';

                const teacherOptions = buildTeacherOptions(assignedTeacher);
                const teacherDisabled = checkedAttr ? '' : ' disabled';

                const row = `
                    <div class="col-md-4 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="custom-control custom-checkbox mr-3">
                                <input type="checkbox"
                                       class="custom-control-input subject-checkbox"
                                       id="subject${subId}"
                                       name="subjects[]"
                                       value="${subId}" ${checkedAttr} ${disabledAttr}>
                                <label class="custom-control-label" for="subject${subId}">
                                    ${subject.sname}
                                </label>
                                ${hiddenInput}
                            </div>
                            <div class="flex-grow-1">
                                <select name="teachers[${subId}]"
                                        class="form-control teacher-select"${teacherDisabled}>
                                    ${teacherOptions}
                                </select>
                            </div>
                        </div>
                    </div>
                `;
                $container.append(row);
            });

            // Init/refresh select2 cleanly
            $('.teacher-select').each(function () {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            }).select2({
                placeholder: "Select a teacher",
                allowClear: true,
                width: '100%'
            });
        },
        error: function () {
            alert('Failed to load Subjects. Please try again.');
        }
    });
}

// 3) Hook class change -> ensure teachers loaded, then load subjects
$(document).on('change', '#classes', function () {
    const classId = $(this).val();
    $('#subjectsContainer').empty(); // clear when no class
    if (!classId) return;

    const ensureTeachers = TEACHERS.length ? Promise.resolve() : fetchTeachers();
    ensureTeachers.then(() => loadSubjectsForClass(classId));
});

// 4) Enable/disable teacher dropdown when user checks/unchecks a subject
$(document).on('change', '.subject-checkbox', function () {
    const subId = $(this).val();
    const $select = $(`select[name="teachers[${subId}]"]`);
    const enable = $(this).is(':checked');
    $select.prop('disabled', !enable).trigger('change.select2'); // keep Select2 UI in sync
});




    $.ajax({
    url: "{{ route('streams.getDropdown') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#classes');
        dropdown.empty();
        dropdown.append('<option value="">Select Stream</option>');
        response.data.forEach(function (streams) {
            dropdown.append(
                `<option value="${streams.ID}">${streams.strmname}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Streams. Please try again.');
    },
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
$.ajax({
    url: "{{ route('teachers.getDropdown') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#teacherlist');
        dropdown.empty();
        dropdown.append('<option value="">Select Teacher</option>');
        response.data.forEach(function (teachers) {
            dropdown.append(
                `<option value="${teachers.ID}">${teachers.teachername}</option>`
            );
        });
    },
    error: function () {
        alert('Failed to load Classes. Please try again.');
    },
});
setupSearch();
});
function filterSelect(inputId, selectId) {
    let filter = $(`#${inputId}`).val().toLowerCase();

    $(`#${selectId} option`).each(function() {
        let text = $(this).text().toLowerCase(); 
        // Match either admno or name in the option text
        if (text.indexOf(filter) > -1) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
}

// Attach listeners

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
            $('#assignsub').on('click', function() {
                 $.ajax({
                    url: "{{ route('classes.getDropdown') }}",
                    type: "GET",
                    success: function (response) {
                        const dropdown = $('#classlist');
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

             let currentSubjectId = null;
             let currentclassId = null;
             let availableStudentsData = [];
             let assignedStudentsData = [];

function openElectiveModal() {
   
    currentSubjectId = $('#subjectlist').val();
    currentclassId = $('#classlist').val();
    showLoading('availableStudents');
    showLoading('assignedStudents');
    $('#availableStudents').empty();
    $('#assignedStudents').empty();
    

    // Fetch students for this subject
   setTimeout(() => {
               // populateStudentLists(demoData);
            }, 1000);
            $.ajax({
                url: "{{ route('subjects.students', ['id' => ':id']) }}"
                .replace(':id', currentSubjectId)
                + "?classid=" + currentclassId,
                type: "GET",
                success: function(response) {
                    populateStudentLists(response);
                },
                error: function() {
                    showError('Failed to load students.');
                }
            });

            
}

function showLoading(containerId) {
            document.getElementById(containerId).innerHTML = `
                <div class="loading">
                    <div class="spinner"></div>
                </div>
            `;
        }

        function populateStudentLists(response) {
            availableStudentsData = response.available;
            assignedStudentsData = response.assigned;
            
            renderStudentList('availableStudents', availableStudentsData);
            renderStudentList('assignedStudents', assignedStudentsData);
            updateCounts();
        }

        function renderStudentList(containerId, students) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            if (students.length === 0) {
                container.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>No students found</p>
                    </div>
                `;
                return;
            }
            students.forEach(student => {
                const studentElement = document.createElement('div');
                studentElement.className = 'student-item';
                studentElement.dataset.admno = student.admno;
                studentElement.innerHTML = `
                <div class="student-info">
                <span class="student-name">${student.admno}-${student.sirname} ${student.othername}</span>
                <span class="student-details"> ${student.strmname}</span>
                </div>
                `;
                studentElement.addEventListener('click', function() {
                    this.classList.toggle('selected');
                });
                container.appendChild(studentElement);
            });
        }

        function moveSelected(fromId, toId) {
            const fromContainer = document.getElementById(fromId);
            const toContainer = document.getElementById(toId);
            const selected = fromContainer.querySelectorAll('.student-item.selected');
            
            if (selected.length === 0) {
                showNotification('Please select students to move', 'warning');
                return;
            }
            
            selected.forEach(item => {
                const admno = item.dataset.admno;
                
                // Move data between arrays
                if (fromId === 'availableStudents') {
                    const student = availableStudentsData.find(s => s.admno === admno);
                    if (student) {
                        assignedStudentsData.push(student);
                        availableStudentsData = availableStudentsData.filter(s => s.admno !== admno);
                    }
                } else {
                    const student = assignedStudentsData.find(s => s.admno === admno);
                    if (student) {
                        availableStudentsData.push(student);
                        assignedStudentsData = assignedStudentsData.filter(s => s.admno !== admno);
                    }
                }
            });
            
            // Re-render both lists
            renderStudentList('availableStudents', availableStudentsData);
            renderStudentList('assignedStudents', assignedStudentsData);
            updateCounts();
            
            showNotification(`Moved ${selected.length} student(s)`, 'success');
        }

        function moveAll(fromId, toId) {
            if (fromId === 'availableStudents') {
                assignedStudentsData = [...assignedStudentsData, ...availableStudentsData];
                availableStudentsData = [];
            } else {
                availableStudentsData = [...availableStudentsData, ...assignedStudentsData];
                assignedStudentsData = [];
            }
            
            renderStudentList('availableStudents', availableStudentsData);
            renderStudentList('assignedStudents', assignedStudentsData);
            updateCounts();
            
            showNotification('All students moved', 'success');
        }

        function updateCounts() {
            document.getElementById('availableCount').textContent = availableStudentsData.length;
            document.getElementById('assignedCount').textContent = assignedStudentsData.length;
        }

        function setupSearch() {
            document.getElementById('searchAvailable').addEventListener('input', function() {
                filterStudents(this.value, 'availableStudents');
            });
            
            document.getElementById('searchAssigned').addEventListener('input', function() {
                filterStudents(this.value, 'assignedStudents');
            });
        }

        function filterStudents(searchTerm, containerId) {
            const container = document.getElementById(containerId);
            const students = container.querySelectorAll('.student-item');
            
            students.forEach(student => {
                const text = student.textContent.toLowerCase();
                if (text.includes(searchTerm.toLowerCase())) {
                    student.style.display = 'block';
                } else {
                    student.style.display = 'none';
                }
            });
        }
// Save elective student assignments
function saveElectiveStudents() {
    currentSubjectId = $('#subjectlist').val();
    teacherId = $('#teacherlist').val();
    classid = $('#classlist').val();
            if (!currentSubjectId) {
                showNotification('Please select a subject first', 'warning');
                return;
            }
            if (!classid) {
                showNotification('Please select a class first', 'warning');
                return;
            }

            if (!teacherId) {
                showNotification('Please select a teacher for this class', 'warning');
                return;
            }
            
            const assignedAdmnos = assignedStudentsData.map(s => s.admno);
            
            // Show saving state
            const saveBtn = document.querySelector('.btn-success');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;
            
            // Simulate save - replace with your actual AJAX call
            setTimeout(() => {
                showNotification('Students assigned successfully!', 'success');
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                
                // Close modal after successful save
                const modal = bootstrap.Modal.getInstance(document.getElementById('electiveModal'));
                modal.hide();
            }, 1500);

            $.ajax({
    url: "{{ route('subjects.students.save', ['id' => ':id']) }}".replace(':id', currentSubjectId),
    type: 'POST',
    data: {
        _token: '{{ csrf_token() }}', // required in Laravel
        students: assignedAdmnos,     // match controller
        trid: teacherId,
        classid: classid               // will pick from request
    },
    success: function(response) {
        if (response.success) {
            showNotification('Students assigned successfully!', 'success');
            $('#electiveModal').modal('hide');
        } else {
            showNotification(response.message || 'Failed to save changes', 'error');
        }
    },
    error: function() {
        showNotification('Failed to save changes', 'error');
    },
    complete: function() {
        saveBtn.innerHTML = originalText;
        saveBtn.disabled = false;
    }
});

        
        }
function showNotification(message, type) {
            // Create a simple toast notification
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'warning'} position-fixed`;
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'exclamation-circle'}"></i>
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 5000);
        }
    </script>
</x-custom-admin-layout>
