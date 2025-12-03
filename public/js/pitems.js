       $(document).ready(function() {
    $('#payrollCodesTable').DataTable();
});
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
function initializeFormBehavior() {
  const categorySelect = document.getElementById('category');
  const balanceOptions = document.getElementById('balanceOptions');
  const loanRate = document.getElementById('loanRate'); 
  const loanhelper = document.getElementById('loanhelper');

  if (categorySelect) {
    categorySelect.addEventListener('change', function() {
      if (this.value === 'balance') {
        balanceOptions.style.display = 'block';
        loanRate.style.display = 'none';
        loanhelper.style.display = 'none';
      } else if (this.value === 'loan') {
        balanceOptions.style.display = 'none';
        loanRate.style.display = 'block';
        loanhelper.style.display = 'block';
      } else {
        balanceOptions.style.display = 'none';
        loanRate.style.display = 'none';
        loanhelper.style.display = 'none';
      }
    });
  }
}

// Call this function when the modal is opened or when the DOM is ready
document.addEventListener('DOMContentLoaded', initializeFormBehavior);

// If using Bootstrap modal, you can also use its events
// Initialize form behavior when modal is shown
$('#payrollModal').on('shown.bs.modal', initializeFormBehavior);

document.addEventListener('DOMContentLoaded', function() {
    const calculationRadio = document.getElementById('calculationRadio');
    const amountRadio = document.getElementById('amount');
    const inputField = document.getElementById('inputField');
    const prosstySelect = document.getElementById('prossty');
    const prioritySection = document.getElementById('prioritySection');
    const sortableList = document.getElementById('sortableDeductions');
    const priorityInput = document.getElementById('priorityInput');
    const codeInput = document.getElementById('code'); // Your code input field
    const cnameInput = document.getElementById('cname'); // Your name input field
    
    let sortableInstance = null;
    let currentEditingId = null; // Set this if editing existing item
    
    // Monitor code/name changes to update current item display
    if (codeInput) {
        codeInput.addEventListener('input', updateCurrentItemDisplay);
    }
    if (cnameInput) {
        cnameInput.addEventListener('input', updateCurrentItemDisplay);
    }
    
    function updateCurrentItemDisplay() {
        const code = codeInput?.value || 'New Code';
        const name = cnameInput?.value || 'New Deduction';
        
        document.getElementById('currentItemCode').textContent = code;
        document.getElementById('currentItemName').textContent = name;
    }
    
    // Show/Hide Priority Section based on prossty selection
    prosstySelect.addEventListener('change', function() {
        if (this.value === 'Deduction') {
            prioritySection.style.display = 'block';
            loadDeductionPriorities();
        } else {
            prioritySection.style.display = 'none';
            if (sortableInstance) {
                sortableInstance.destroy();
                sortableInstance = null;
            }
        }
    });
    
    // Load existing deductions with priorities
    function loadDeductionPriorities() {
        sortableList.innerHTML = '<li class="list-group-item text-center text-muted py-1"><i class="fas fa-spinner fa-spin"></i> Loading deductions...</li>';
        
        fetch(loadpriori, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderDeductionsList(data.deductions);
                initializeSortable();
                updateCurrentPriority();
            } else {
                sortableList.innerHTML = '<li class="list-group-item text-center text-danger">Error loading deductions</li>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            sortableList.innerHTML = '<li class="list-group-item text-center text-danger">Failed to load deductions</li>';
        });
    }
    
    // Render deductions list
    function renderDeductionsList(deductions) {
        if (deductions.length === 0) {
            sortableList.innerHTML = `
                <li class="list-group-item text-center text-muted py-1">
                    <i class="fas fa-info-circle"></i> No existing deductions. This will be priority #1.
                </li>
            `;
            document.getElementById('currentPriorityNumber').textContent = '1';
            priorityInput.value = '1';
            return;
        }
        
        sortableList.innerHTML = deductions.map((deduction, index) => `
            <li class="list-group-item" data-id="${deduction.id}" data-priority="${deduction.priority}">
                <div class="d-flex align-items-center">
                    <div class="drag-handle mr-1">
                        <i class="fas fa-grip-vertical fa-lg"></i>
                    </div>
                    <span class="priority-badge bg-secondary text-white mr-1">
                        ${index + 1}
                    </span>
                    <div class="flex-grow-1">
                        <strong>${deduction.cname}</strong>
                       
                        <small class="text-muted">Code: ${deduction.code}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-info">Priority ${deduction.priority}</span>
                    </div>
                </div>
            </li>
        `).join('');
    }
    
    // Initialize Sortable.js
    function initializeSortable() {
        if (sortableInstance) {
            sortableInstance.destroy();
        }
        
        sortableInstance = new Sortable(sortableList, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                updatePriorities();
                updateCurrentPriority();
            }
        });
    }
    
    // Update priorities after drag/drop
    function updatePriorities() {
        const items = sortableList.querySelectorAll('.list-group-item');
        items.forEach((item, index) => {
            const badge = item.querySelector('.priority-badge');
            if (badge) {
                badge.textContent = index + 1;
            }
        });
    }
    
    // Update current item priority (will be inserted at the end by default)
    function updateCurrentPriority() {
        const items = sortableList.querySelectorAll('.list-group-item');
        const nextPriority = items.length + 1;
        
        document.getElementById('currentPriorityNumber').textContent = nextPriority;
        priorityInput.value = nextPriority;
    }
    function toggleReadOnly() {
        if (calculationRadio.checked) {
            inputField.removeAttribute('readonly');
        } else {
            inputField.setAttribute('readonly', true);
        }
    }
    toggleReadOnly();
    calculationRadio.addEventListener('change', toggleReadOnly);
    amountRadio.addEventListener('change', toggleReadOnly);

    const inputField2 = document.getElementById('inputField');
    const feedback = document.getElementById('feedback');
    // Function to validate the code with the database
    function checkCode(code) {
        $.ajax({
            type: 'POST',
            url: '',
            data: { code: code },
            success: function(response) {
                try {
                    var jsonResponse = JSON.parse(response); 
                    if (jsonResponse.exists) {
                        feedback.innerHTML = '';
                    } else {
                        feedback.innerHTML = `Code ${code} is invalid.`;
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                    feedback.innerHTML = 'Error in response from server.';
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
            }
        });
    }
    // Event listener for keydown on the input field
    inputField2.addEventListener('keydown', function(event) {
        const terminalSymbols = ['+', '/', '*', '-', '=']; // Add more terminals if needed
        const key = event.key;
        // If the key is a terminal symbol, validate the preceding code
        if (terminalSymbols.includes(key)) {
            const formula = inputField2.value;
            const codes = formula.match(/[A-Za-z]+\d+/g); // Matches codes like 'D998', 'C67', etc.
            if (codes && codes.length > 0) {
                const lastCode = codes[codes.length - 1];
                checkCode(lastCode); // Check the last entered code
            }
        }
    });
    const cumulativeValueCheckbox = document.getElementById('cumulativeValue');
    const casualCheckbox = document.getElementById('casual');

    // Function to handle the checkbox state
    function handleCheckboxChange(checkedCheckbox) {
        if (checkedCheckbox === cumulativeValueCheckbox && cumulativeValueCheckbox.checked) {
            casualCheckbox.checked = false;
        } else if (checkedCheckbox === casualCheckbox && casualCheckbox.checked) {
            cumulativeValueCheckbox.checked = false;
        }
    }

    // Event listeners for the checkboxes
    cumulativeValueCheckbox.addEventListener('change', function() {
        handleCheckboxChange(cumulativeValueCheckbox);
    });

    casualCheckbox.addEventListener('change', function() {
        handleCheckboxChange(casualCheckbox);
    }); 

    function savePrioritiesOrder(order) {
         console.log("Saving Order");
    $.ajax({
       
        url: updateorder,
        method: 'POST',
        data: JSON.stringify({ priorities: order }),
        contentType: 'application/json',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            // Optional: Show loading indicator
            console.log('Updating priorities...');
        },
        success: function(data) {
            if (data.status === 'success') {
                console.log('Priorities updated:', data);
                
                // Optional: Show success message
                showMessage('Priority order updated successfully', false);
            } else {
                console.error('Update failed:', data.message);
                showMessage(data.message || 'Failed to update priorities', true);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating priorities:', error);
            
            let errorMessage = 'An error occurred while updating priorities';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    let errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            showMessage(errorMessage, true);
        },
        complete: function() {
            // Optional: Hide loading indicator
            console.log('Priority update request completed');
        }
    });
}

    $('#payrollForm').on('submit', function(e) {
        e.preventDefault();
    
    
    if (!validateFormFields()) {
      
        return; // If validation fails, stop further execution
    }
    if (prosstySelect.value === 'Deduction') {
            // Get current priority
            const priority = priorityInput.value;
            
            // Get new order of existing items
            const items = sortableList.querySelectorAll('.list-group-item');
            const newOrder = Array.from(items).map((item, index) => ({
                id: item.dataset.id,
                priority: index + 1
            }));
            
            // Save the reordered priorities via AJAX (optional)
            if (newOrder.length > 0) {
                savePrioritiesOrder(newOrder);
            }
        }
    
    
    var formData = $('#payrollForm').serialize();
    
    const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Adding...').prop('disabled', true);
    $.ajax({
    type: 'POST',
     url: amanage,
    data: formData,
    dataType: 'json', // Add this line - jQuery will auto-parse JSON
    success: function(response) {
        // Now 'response' is already parsed, no need for JSON.parse()
        if (response.status === 'success') {
            showMessage('Payroll Item added successfully!', false);
            $('#payrollForm')[0].reset();
            $('#sacconames').attr('hidden', true);
            $('#staffSelect7').removeAttr('required').val('');
        } else if (response.status === 'duplicate') {
            showMessage(response.message, true);
        } else {
            showMessage('Form submission failed: ' + response.message, true);
        }
    },
    error: function(xhr, status, error) {
        console.error('XHR:', xhr.responseText); // See actual error
        showMessage('Form submission failed: ' + error, true);
    },
    complete: function() {
        submitBtn.html(originalText).prop('disabled', false);
    }
});
});

});
$(document).ready(function(){
$('#saccocheck').on('change', function () {
    if ($(this).is(':checked')) {
        $('#sacconames').val('Yes');       
        // Show the vehicle reg no field
        $('#sacconames').removeAttr('hidden');
        $('#staffSelect7').attr('required', true);
        
    } else {
        // Hide the vehicle reg no field
        $('#sacconames').attr('hidden', true);
        $('#staffSelect7').removeAttr('required');
        $('#sacconames').val('No');
        $('#staffSelect7').val('').trigger('change');
    }
});
$('#saccoeditcheck').on('change', function () {
    if ($(this).is(':checked')) {
        $('#saccoeditcheck').val('Yes');  
        // Show the vehicle reg no field
        $('#saccoeditnames').removeAttr('hidden');
        $('#staffSelect8').attr('required', true);
        
    } else {
        // Hide the vehicle reg no field
        $('#saccoeditnames').attr('hidden', true);
        $('#staffSelect8').removeAttr('required');
        $('#saccoeditcheck').val('No');
        $('#staffSelect8').val('').trigger('change');
    }
});
 });
// Validate form fields
function validateFormFields() {
    let isValid = true; // Variable to track form validity

    // Check for required fields
    $('#payrollForm').find('input, select').each(function() {
        const isReadOnly = $(this).prop('readonly');
        
        // If the field is not readonly and required, validate it
        if (!isReadOnly && $(this).prop('required') && $(this).val().trim() === '') {
            $(this).addClass('is-invalid'); // Add invalid class for styling
            isValid = false;
        } else {
            $(this).removeClass('is-invalid'); // Remove invalid class if valid
        }
    });

    // Check for specific input validation (like numeric fields)
    if (!validateNumericFields()) {
        isValid = false;
    }

    return isValid;
}

// Validate numeric fields
function validateNumericFields() {
    let isNumericValid = true;
    
    // Example: Check if 'rate' is a valid number if displayed and not readonly
    const rateField = $('#rate');
    if (rateField.is(':visible') && !rateField.prop('readonly') && isNaN(rateField.val())) {
        rateField.addClass('is-invalid');
        isNumericValid = false;
    } else {
        rateField.removeClass('is-invalid');
    }
    
    // Check if recintres is 0, and if so, validate interestcode and interestdesc fields
    
        const loanhelperDiv = $('#loanhelper');
        
        if (loanhelperDiv.is(':visible')) {
            const recintresValue = $('input[name="recintres"]').val();
    const recintresValueByID = $('#separate').val();
    const recintresRadioValue = $('input[name="recintres"]:checked').val();
    
    
    
    // Check if any of these are "0"
    if (recintresValue === '0' || recintresValueByID === '0' || recintresRadioValue === '0') {
        console.log("Detected recintres = 0, validating interest fields");
        
        const interestcodeField = $('#interestcode');
        const interestdescField = $('#interestdesc');
        
       
        
        // Dynamically set these fields as required
       // interestcodeField.prop('required', true);
      //  interestdescField.prop('required', true);
        
        // Validate interestcode
        console.log("interestcode value:", interestcodeField.val());
        if (!interestcodeField.val() || !interestcodeField.val().trim()) {
            interestcodeField.addClass('is-invalid');
            isNumericValid = false;
           // console.log("interestcode invalid");
        } else {
            interestcodeField.removeClass('is-invalid');
        }
        
        // Validate interestdesc
       // console.log("interestdesc value:", interestdescField.val());
        if (!interestdescField.val() || !interestdescField.val().trim()) {
            interestdescField.addClass('is-invalid');
            isNumericValid = false;
          //  console.log("interestdesc invalid");
        } else {
            interestdescField.removeClass('is-invalid');
        }
    }
        } else {
        // If recintres is not 0, remove the required attribute
        $('#interestcode').prop('required', false);
        $('#interestdesc').prop('required', false);
        //console.log("recintres is not 0, no validation needed for interest fields");
    }
    
    
    return isNumericValid;
}



// Clear form fields after successful submission
function clearFormFields() {
    $('#payrollForm')[0].reset();
    $('.required').removeClass('is-invalid');
    $('.help-block').hide();
}

// Event listeners
$('#payrollModal').on('hidden.bs.modal', function () {
    clearFormFields();
});


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

$(document).ready(function() {
    $('#payrollCodesTable').DataTable();
});
function openEditModal(element) {
    const row = $(element).closest('tr');
    const id = row.find('td:eq(0)').text();
    const code = row.find('td:eq(1)').text();
    const description = row.find('td:eq(2)').text();
    const processType = row.find('td:eq(3)').text();
    const varorfixed = row.find('td:eq(4)').text();
    const taxornontax = row.find('td:eq(5)').text();
    const category = row.find('td:eq(6)').text();
    const relief = row.find('td:eq(7)').text();
    const prossty = row.find('td:eq(8)').text();
    const rate = row.find('td:eq(9)').text();
    const incredu = row.find('td:eq(10)').text();
    const recintres = row.find('td:eq(11)').text();
    const formularinpu = row.find('td:eq(12)').text();
    const cumcas = row.find('td:eq(13)').text();
    const intrestcode = row.find('td:eq(14)').text();
    const codename = row.find('td:eq(15)').text();
    const issaccorel = row.find('td:eq(16)').text();
    const sposter = row.find('td:eq(17)').text();
    const editprosstySelect = document.getElementById('editProcessSty');
    const prioreSection = document.getElementById('prioreSection');
    const editsortableDeductions = document.getElementById('editsortableDeductions');
    const editpriorityInput = document.getElementById('editpriorityInput');
    const editCode = document.getElementById('editCode'); // Your code input field
    const editDescription = document.getElementById('editDescription'); // Your name input field
    $('#editCode').val(code);
    $('#editid').val(id);
    $('#editDescription').val(description);
    if (processType === 'Amount') {
        $('#editAmount').prop('checked', true);
        $('#editinputField').prop('readonly', true);
    } else {
        $('#editCalculation').prop('checked', true);
        $('#editinputField').prop('readonly', false);
    }
    if (cumcas === 'cumulative') {
        $('#editcumulative').prop('checked', true);
    } else if (cumcas === 'casual') {
        $('#editcasual').prop('checked', true);
    }else{
        $('#editcumulative').prop('checked', false);
        $('#editcasual').prop('checked', false);
    }

    $('#editinputField').val(formularinpu);
    $('#editCategory').val(category);
    
    $('#editProcessSty').val(prossty);
    let esortableInstance = null;
    let currentEditingId = null; // Set this if editing existing item
    
    // Monitor code/name changes to update current item display
    if (editCode) {
        editCode.addEventListener('input', updateCurrentItemDisplay2);
    
    if (editDescription) {
        editDescription.addEventListener('input', updateCurrentItemDisplay2);
    }
    
    function updateCurrentItemDisplay2() {
        const code = editCode?.value || 'New Code';
        const name = editDescription?.value || 'New Deduction';
        
        document.getElementById('currentItemCode').textContent = code;
        document.getElementById('currentItemName').textContent = name;
    }
    
    // Show/Hide Priority Section based on prossty selection
    editprosstySelect.addEventListener('change', function() {
        if (this.value === 'Deduction') {
            prioreSection.style.display = 'block';
            loadDeductionPriorities2();
        } else {
            prioreSection.style.display = 'none';
            if (esortableInstance) {
                esortableInstance.destroy();
                esortableInstance = null;
            }
        }
    });
    
    // Load existing deductions with priorities
    function loadDeductionPriorities2() {
        editsortableDeductions.innerHTML = '<li class="editlist-group-item text-center text-muted py-4"><i class="fas fa-spinner fa-spin"></i> Loading deductions...</li>';
        
        fetch(loadpriori, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                renderDeductionsList2(data.deductions);
                initializeSortable2();
                updateCurrentPriority2();
            } else {
                editsortableDeductions.innerHTML = '<li class="editlist-group-item text-center text-danger">Error loading deductions</li>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            editsortableDeductions.innerHTML = '<li class="editlist-group-item text-center text-danger">Failed to load deductions</li>';
        });
    }
    
    // Render deductions list
    function renderDeductionsList2(deductions) {
        if (deductions.length === 0) {
            editsortableDeductions.innerHTML = `
                <li class="editlist-group-item text-center text-muted py-4">
                    <i class="fas fa-info-circle"></i> No existing deductions. This will be priority #1.
                </li>
            `;
            document.getElementById('editPriorityNumber').textContent = '1';
            editpriorityInput.value = '1';
            return;
        }
        
        editsortableDeductions.innerHTML = deductions.map((deduction, index) => `
            <li class="editlist-group-item" data-id="${deduction.id}" data-priority="${deduction.priority}">
                <div class="d-flex align-items-center">
                    <div class="drag-handle mr-3">
                        <i class="fas fa-grip-vertical fa-lg"></i>
                    </div>
                    <span class="priority-badge bg-secondary text-white mr-3">
                        ${index + 1}
                    </span>
                    <div class="flex-grow-1">
                        <strong>${deduction.cname}</strong>
                        <br>
                        <small class="text-muted">Code: ${deduction.code}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-info">Priority ${deduction.priority}</span>
                    </div>
                </div>
            </li>
        `).join('');
    }
    
    // Initialize Sortable.js
    function initializeSortable2() {
        if (esortableInstance) {
            esortableInstance.destroy();
        }
        
        esortableInstance = new Sortable(editsortableDeductions, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost',
            dragClass: 'sortable-drag',
            onEnd: function(evt) {
                updatePriorities2();
                updateCurrentPriority2();
            }
        });
    }
    
    // Update priorities after drag/drop
    function updatePriorities2() {
        const items = editsortableDeductions.querySelectorAll('.editlist-group-item');
        items.forEach((item, index) => {
            const badge = item.querySelector('.priority-badge');
            if (badge) {
                badge.textContent = index + 1;
            }
        });
    }
    
    // Update current item priority (will be inserted at the end by default)
    function updateCurrentPriority2() {
        const items = editsortableDeductions.querySelectorAll('.editlist-group-item');
        const nextPriority = items.length + 1;
        
        document.getElementById('editPriorityNumber').textContent = nextPriority;
        editpriorityInput.value = nextPriority;
    }
}
    if (prossty === 'Deduction') {
       prioreSection.style.display = 'block';
            loadDeductionPriorities2();
    } else {
        prioreSection.style.display = 'none';
            if (esortableInstance) {
                esortableInstance.destroy();
                esortableInstance = null;
            }
    }
    if (category === 'balance') {
        $('#editBalanceOptions').show();
        if (incredu === 'Increasing') {
        $('#editIncreasing').prop('checked', true);
    } else {
        $('#editReducing').prop('checked', true);
    }
    } else {
        $('#editBalanceOptions').hide();
    }
    if (category === 'loan') {
        $('#editLoanRate').show();
        $('#editloanhelper').show();
        $('#editRate').val(rate);
        $('#editinterestcode').val(intrestcode);
        $('#editinterestdesc').val(codename);
        recintToggle(recintres);
        $('#recint-toggleedit input[type="radio"]').on('change', function() {
            recintToggle($(this).val());
        });
    } else {
        $('#editLoanRate').hide();
        $('#editloanhelper').hide();
    }

    setTimeout(function() {
        if (issaccorel === 'Yes') {
            $('#saccoeditcheck').prop('checked', true);
            $('#saccoeditnames').removeAttr('hidden');
            $('#staffSelect8').attr('required', true);
            $('#staffSelect8').val(sposter).trigger('change');
        } else {
            $('#saccoeditcheck').prop('checked', false);
            $('#saccoeditnames').attr('hidden', true);
             $('#saccoeditcheck').val('No');
            $('#staffSelect8').removeAttr('required');
            $('#staffSelect8').val('').trigger('change');
        }
    }, 200);
    updateReliefToggle(relief);
    $('#editReliefToggle input[type="radio"]').on('change', function() {
        updateReliefToggle($(this).val());
    });
    updatetaxableToggle(taxornontax);
    $('#editTaxableToggle input[type="radio"]').on('change', function() {
        updatetaxableToggle($(this).val());
    });
    updatevarfixToggle(varorfixed);
    $('#editVarOrFixedToggle input[type="radio"]').on('change', function() {
        updatevarfixToggle($(this).val());
    });
    $('#editpitemsModal').modal('show');
}
function updatevarfixToggle(varorfixed) {
    const slider = $('#editVarOrFixedToggle .slider');
    let transform, backgroundColor;

    switch(varorfixed) {
        case 'Variable':
            $('#editVariable').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
            break;
        case 'Fixed':
            $('#editFixed').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#2ecc71';
            break;
        default:
            $('#editVariable').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#editVarOrFixedToggle label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}
function recintToggle(recintres) {
    const slider = $('#recint-toggleedit .slider');
    let transform, backgroundColor;

    switch(recintres) {
        case '1':
            $('#recintredit').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3d0678';
            break;
        case '0':
            $('#separatedit').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#fa2007';
            break;
        default:
            $('#recintredit').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3d0678';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#recint-toggleedit label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}
function updatetaxableToggle(taxornontax) {
    const slider = $('#editTaxableToggle .slider');
    let transform, backgroundColor;

    switch(taxornontax) {
        case 'Taxable':
            $('#editTaxable').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#e74c3c';
            break;
        case 'Non-taxable':
            $('#editNonTax').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#f39c12';
            break;
        default:
            $('#editNonTax').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#f39c12';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#editTaxableToggle label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}
function updateReliefToggle(relief) {
    const slider = $('#editReliefToggle .slider');
    let transform, backgroundColor;

    switch(relief) {
        case 'NONE':
            $('#editNone').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
            break;
        case 'RELIEF ON TAXABLE':
            $('#editRNT').prop('checked', true);
            transform = 'translateX(100px)';
            backgroundColor = '#2ecc71';
            break;
        case 'Relief on Paye':
            $('#editRNP').prop('checked', true);
            transform = 'translateX(200px)';
            backgroundColor = '#e74c3c';
            break;
        default:
            $('#editNone').prop('checked', true);
            transform = 'translateX(0)';
            backgroundColor = '#3498db';
    }

    slider.css({ transform, backgroundColor });

    // Update label colors
    $('#editReliefToggle label').css('color', function() {
        return $(this).prev('input').is(':checked') ? '#fff' : '#333';
    });
}

        // Handle Category Change to Show/Hide Balance and Loan Fields
        $('#editCategory').on('change', function() {
            const selectedCategory = $(this).val();

            if (selectedCategory === 'balance') {
                $('#editBalanceOptions').slideDown();
                $('#editLoanRate').slideUp();
                $('#editloanhelper').slideUp();
            } else if (selectedCategory === 'loan') {
                $('#editBalanceOptions').slideUp();
                $('#editLoanRate').slideDown();
                $('#editloanhelper').slideDown();
            } else {
                $('#editBalanceOptions').slideUp();
                $('#editLoanRate').slideUp();
                $('#editloanhelper').slideUp();
            }
        });

        function updateToggle(toggleId, firstOptionId, firstColor, secondColor) {
    $(`#${toggleId} input[type="radio"]`).on('change', function() {
        const slider = $(`#${toggleId} .slider`);
        const isFirstOption = $(`#${firstOptionId}`).is(':checked');
        
        slider.css({
            'transform': isFirstOption ? 'translateX(0)' : 'translateX(100px)',
            'background-color': isFirstOption ? firstColor : secondColor
        });

        $(`#${toggleId} label`).css('color', function() {
            return $(this).prev('input').is(':checked') ? '#fff' : '#333';
        });
    });
}
$(document).ready(function() {
    // Attach the click event to the button with id
    $('#saveChangesButton').on('click', function() {
        submitEditForm(); // Call the function to handle AJAX submission
    });
});
function savePrioritiesOrder(order) {
         console.log("Saving Order");
    $.ajax({
       
        url: updateorder,
        method: 'POST',
        data: JSON.stringify({ priorities: order }),
        contentType: 'application/json',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function() {
            // Optional: Show loading indicator
            console.log('Updating priorities...');
        },
        success: function(data) {
            if (data.status === 'success') {
                console.log('Priorities updated:', data);
                
                // Optional: Show success message
                showMessage('Priority order updated successfully', false);
            } else {
                console.error('Update failed:', data.message);
                showMessage(data.message || 'Failed to update priorities', true);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating priorities:', error);
            
            let errorMessage = 'An error occurred while updating priorities';
            
            // Try to parse error response
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    let errorData = JSON.parse(xhr.responseText);
                    errorMessage = errorData.message || errorMessage;
                } catch (e) {
                    // Response is not JSON
                }
            }
            
            showMessage(errorMessage, true);
        },
        complete: function() {
            // Optional: Hide loading indicator
            console.log('Priority update request completed');
        }
    });
}
function submitEditForm() {

    // Gather form data
    const formData = {
    id: $('#editid').val(),
    code: $('#editCode').val(),
    cname: $('#editDescription').val(),
    formula: $('#editinputField').val(),
    procctype: $('input[name="editProcessType"]:checked').val(),
    cumcas: $('input[name="editcalctype"]:checked').val(),
    varorfixed: $('input[name="editVarOrFixed"]:checked').val(),
    taxaornon: $('input[name="editTaxOrNon"]:checked').val(),
    category: $('#editCategory').val(),
    increREDU: $('#editCategory').val() === 'balance' ? $('input[name="editBalanceType"]:checked').val() : null,
    rate: $('#editCategory').val() === 'loan' ? $('#editRate').val() : null, 
    intrestcode: $('#editCategory').val() === 'loan' ? $('#editinterestcode').val() : null,
    codename: $('#editCategory').val() === 'loan' ? $('#editinterestdesc').val() : null,
    recintres: $('#editCategory').val() === 'loan' ? $('input[name="editrecintres"]:checked').val() : null, // Added condition
    prossty: $('#editProcessSty').val(),
    relief: $('input[name="editRelief"]:checked').val(),
    saccocheck :  $('#saccoeditcheck').val(),
    poster : $('#staffSelect8').val()
    
};
if ($('#editProcessSty').val() === 'Deduction') {
            // Get current priority
            const priority =$('#editpriorityInput').val();
            
            // Get new order of existing items
            const items = editsortableDeductions.querySelectorAll('.editlist-group-item');
            const newOrder = Array.from(items).map((item, index) => ({
                id: item.dataset.id,
                priority: index + 1
            }));
            
            // Save the reordered priorities via AJAX (optional)
            if (newOrder.length > 0) {
                savePrioritiesOrder(newOrder);
            }
        }

    

    $.ajax({
        url: update,
        type: 'POST',
        data: formData,
        dataType: 'json', // Expected response format
         headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            
            if (response.success) {
                showMessage('Payroll code updated successfully!', false);
                // Optionally close the modal or perform other UI updates
                $('#editPayrollModal').modal('hide'); // Close modal
                // Refresh table data
                updateTableRow(formData);
            } else {
                alert('Error updating payroll code: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            alert('An error occurred while updating the payroll code. ' + error);
        }
    });
}

function updateTableRow(formData) {
    // Locate and update the table row with matching ID
    const row = $('#payrollCodesTable tbody tr').filter(function() {
        return $(this).find('td:first').text() === formData.id;
    });

    // Update each column with new form data
    row.find('td:eq(1)').text(formData.code);
    row.find('td:eq(2)').text(formData.cname);
    row.find('td:eq(3)').text(formData.procctype);
    row.find('td:eq(4)').text(formData.varorfixed);
    row.find('td:eq(5)').text(formData.taxaornon);
    row.find('td:eq(6)').text(formData.category);
    row.find('td:eq(7)').text(formData.relief);
    row.find('td:eq(8)').text(formData.prossty);
    row.find('td:eq(9)').text(formData.rate);
    row.find('td:eq(10)').text(formData.increREDU);
    row.find('td:eq(11)').text(formData.recintres);
    row.find('td:eq(12)').text(formData.formula);
    row.find('td:eq(13)').text(formData.cumcas);
    row.find('td:eq(14)').text(formData.intrestcode);
    row.find('td:eq(15)').text(formData.codename);
    row.find('td:eq(16)').text(formData.saccocheck);
    row.find('td:eq(17)').text(formData.poster);
}

function deletePayrollCode(element) {
    const id = $(element).data('id');
    const code = $(element).data('code');
    swal({
        title: 'Are you sure?',
        text: `Are you sure you want to delete the payroll item "${code}"? Some Staff may be Currently Assigned to It.`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        if (result.value) {
            // Perform the AJAX request for deletion
            $.ajax({
                url: '', // URL to your server-side script
                type: 'POST',
                data: {
                    action: 'ptypes', // Action parameter to indicate deletion
                    id: id,
                    code: code
                },
                dataType: 'json', // Expecting JSON response
                success: function(response) {
                    if (response.success) {
                        // Show success SweetAlert
                        swal({
                            title: 'Deleted!',
                            text: 'Payroll Item deleted!',
                            icon: 'success',
                            buttons: false,
                            timer: 2000
                        });

                        // Optionally reload data or refresh the table
                         $(element).closest('tr').remove(); // Uncomment if needed
                    } else {
                        // Show error SweetAlert
                        swal({
                            title: 'Error!',
                            text: response.message || 'Failed to delete, Try again later.',
                            icon: 'error',
                            buttons: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error); // Log any AJAX error
                    swal({
                        title: 'Failed!',
                        text: 'An error occurred while deleting the leave type. Please try again.',
                        icon: 'error',
                        buttons: true
                    });
                }
            });
        }
    });
}