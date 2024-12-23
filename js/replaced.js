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
        $('.text-danger').remove(); // Remove error messages
        
    });

    $('#phone').change(function() {
        var phoneId = $(this).val();
        if (phoneId) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { action: 'getAvailableParts', phone_id: phoneId },
                success: function(data) {
                    $('#part').html(data);
                }
            });
        } else {
            $('#part').html('<option value="">Select Product</option>');
        }
    });

    $(document).on('submit', '#replacedForm', function(event) {
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        setTimeout(function() {
            $('#action').attr('disabled', false);
        }, 1000);   
        var formData = $(this).serialize();
        $.ajax({
            url: "action.php",
            method: "POST",
            data: formData,
            success: function(data) {
                if(data <= 0) {
                    if ($('#quantity').next('.text-danger').length === 0) {
                        $('#quantity').after('<span class="text-danger">The Quantity is Exceeded than the Stock.</span>');
                    } else {
                        $('#quantity').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                
                }else if(data > 0){
                    $('#replacedForm')[0].reset();
                    $('#replacedModal').modal('hide');
                    $('#action').attr('disabled', false);
                    
                    replacedData.ajax.reload();
                }
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
    
                // Set selected values for phone dropdown
                $('#phone').val(data.phone_pid);

                // Fetch available parts for the selected phone
                $.ajax({
                    url: "action.php",
                    method: "POST",
                    data: { action: 'getAvailableParts', phone_id: data.phone_pid, current_part_id: data.part_pid },
                    success: function(partsData) {
                        console.log("Parts Data:", partsData); // Debug log
                        $('#part').html(partsData);
                        // Set selected part
                        $('#part').val(data.part_pid);
                        console.log("Parts Default:", data.part_pid); // Debug log
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching parts:", error); // Debug log
                    }
                });
    
                // Update modal title and form action
                $('.modal-title').html("<i class='fa fa-edit'></i> Edit Replace");
                $('#action').val("Edit");
                $('#btn_action').val("updateReplaced");
                $('.text-danger').remove(); // Remove error messages
            },
            error: function(xhr, status, error) {
                console.error("Error fetching replacement details:", error); // Debug log
            }
        });
    });
    
    
});