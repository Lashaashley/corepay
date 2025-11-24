  $(document).ready(function(){

           $('#withholdingForm').on('submit', function(e) {
    e.preventDefault();

    var formData = $(this).serialize();

    $.ajax({
        url: amanage,
        type: 'POST',
        data: formData,
        dataType: 'json',    // ← IMPORTANT
        success: function(response){
            if(response.success) {
                showMessage(response.message, false);
            } else {
                showMessage(response.message, true);
            }
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});


            $('#withholding').on('show.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var type = 'whbracket'; // Set type to fetch data for shifbracket

        // Make an AJAX request to fetch the data
        $.ajax({
    url: getwuth,
    type: 'GET',
    data: { type: type },
    dataType: 'json',  // ✅ Important: automatically parses JSON for you
    success: function(data) {  // ✅ 'data' is already a JS object
        if (data.success) { 
            $('#cnamewh').val(data.cname);
            $('#codewh').val(data.code);
            $('#Percentagewl').val(data.wpercentage);

            $('#whCodesTable tbody').empty();
            if (data.groups && data.groups.length > 0) {
                $('#whCodesTable').show();
                data.groups.forEach(function(group) {
                    var newRow = $('<tr onclick="highlightRow(this)">' +
                        '<td hidden>' + group.ID + '</td>' +
                        '<td>' + group.code + '</td>' +
                        '<td>' + group.cname + '</td>' +
                    '</tr>');
                    $('#whCodesTable tbody').append(newRow);
                });
            } else {
                console.log('No withholding groups data available');
                $("#whCodesTable").hide();
            }
        } else {
            console.error('Error fetching data:', data.message);
        }
    },
    error: function(jqXHR, textStatus, errorThrown){
        console.error('AJAX Error:', textStatus, errorThrown);
        console.error(jqXHR.responseText);
    }
});

    });

$('#savewhGroup').click(function() {
    var pitem = $('#whitempen').val();
    var code  = $('#codewhg').val();

    $.ajax({
        url: storewith,
        type: 'POST',
        data: {
            pitem: pitem,
            code: code
        },
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                showMessage('WH group added.', false);

                var newRow = `
                    <tr onclick="highlightRow(this)">
                        <td hidden>${data.ID}</td>
                        <td>${code}</td>
                        <td>${pitem}</td>
                    </tr>
                `;

                $('#whCodesTable tbody').append(newRow);
                $('#addwhGroupModal').modal('hide');

            } else {
                showMessage('Error: ' + data.message, true);
            }
        },
        error: function(xhr) {
            console.error(xhr.responseText);
            showMessage('Error occurred while adding WH group.', true);
        }
    });
});
$('#deletewhGroup').click(function(e) {
    e.preventDefault();
    e.stopPropagation();

    var selectedRow = $('#whCodesTable tbody tr.highlight');

    if (selectedRow.length === 0) {
        showMessage('Please select an Item to delete.', true);
        return;
    }

    var id = selectedRow.find('td:first').text();

    if (confirm('Are you sure you want to delete this WH group?')) {
        $.ajax({
            url: delwith,
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if (data.success) {
                    selectedRow.remove();
                    showMessage('Withholding group removed.', false);
                } else {
                    showMessage('Error: ' + data.message, true);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                showMessage('Error occurred while deleting WH group.', true);
            }
        });
    }
});




    $('#wholdingcodes').on('click', function() {
   
    $.ajax({
        url: getcodes,
        type: "GET",
        success: function (response) {
            const dropdown = $('#whitempen');
            dropdown.empty();
            dropdown.append('<option value="">Select Item</option>');
            response.data.forEach(function (statutoryOption) {
                dropdown.append(
                    `<option data-category="${statutoryOption.code}" value="${statutoryOption.cname}">${statutoryOption.cname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load streams. Please try again.');
        },
    });
});
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
function populateCategory4() {
    var select = document.getElementById('whitempen');
    var code = document.getElementById('codewhg');
    var selectedOption = select.options[select.selectedIndex];
    code.value = selectedOption.getAttribute('data-category');
}
function highlightRow(row) {
    var parentTable = row.closest('table');
    
    var tableRows = parentTable.getElementsByTagName('tr');

    for (var i = 0; i < tableRows.length; i++) {
        tableRows[i].classList.remove('highlight');
    }
    row.classList.add('highlight');
}