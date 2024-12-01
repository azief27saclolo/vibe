$(document).ready(function() {

    var replacedData = $('#replacedList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'listReplaced' },
            dataType: "json"
        },
        "pageLength": 10,
        "columnDefs": [{
            "target": [0, 4],
            "orderable": false
        }],
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle')
            $(row).find('td:eq(0), td:eq(4)').addClass('text-center')
        },
    });

    $('#addReplaced').click(function() {
        console.log('Add Replace button clicked');
        $('#replacedModal').modal('show');
        $('#replacedForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Replace");
        $('#action').val("Add");
        $('#btn_action').val("addReplaced");
    });

    $(document).on('submit', '#replacedForm', function(event) {
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var formData = $(this).serialize();
        $.ajax({
            url: "action.php",
            method: "POST",
            data: formData,
            success: function(data) {
                console.log('Im in Submit');
                $('#replacedForm')[0].reset();
                $('#replacedModal').modal('hide');
                $('#action').attr('disabled', false);
                replacedData.ajax.reload();
            }
        })
    });

    $(document).on('click', '.delete', function() {
        var replaced_id = $(this).attr("id");  // Ensure it uses 'id' instead of 'replacement_id'
        var btn_action = 'deleteReplaced';
        if (confirm("Are you sure you want to delete this replace?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { replaced_id: replaced_id, btn_action: btn_action },
                success: function(data) {
                    replacedData.ajax.reload();
                }
            });
        } else {
            return false;
        }
    });
    

    $(document).on('click', '.update', function() {
        var replaced_id = $(this).attr("id");
        var btn_action = 'getReplacedDetails';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { replaced_id: replaced_id, btn_action: btn_action },
            dataType: "json",
            success: function(data) {
                if (data.error) {
                    alert(data.error);
                    return;
                }
    
                // Open the modal
                $('#replacedModal').modal('show');
    
                // Set the form fields with the returned data
                $('#replacement_id').val(data.replacement_id);
                $('#quantity').val(data.quantity);
    
                // Set selected values for phone and part dropdowns
                $('#phone').val(data.phone_pid);  // Set selected phone
                $('#part').val(data.part_pid);    // Set selected part
    
                // Update modal title and form action
                $('.modal-title').html("<i class='fa fa-edit'></i> Edit Replace");
                $('#action').val("Edit");
                $('#btn_action').val("updateReplaced");
            }
        });
    });
    
    

});