$(document).ready(function() {
    $('#addCustomer').click(function() {
        $('#customerModal').modal('show');
        $('#customerForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Customer");
        $('.text-danger').remove(); // Remove error messages
    });
    
    var userdataTable = $('#customerList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'customerList' },
            dataType: "json"
        },
        "columnDefs": [{
            "target": [0, 4],
            "orderable": false
        }],
        "pageLength": 25,
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle')
            $(row).find('td:eq(0), td:eq(3)').addClass('text-center')
            $(row).find('td:eq(4)').addClass('text-center')
        },
    });

    $(document).on('submit', '#customerForm', function(event) {
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
                if (data <= 0) {
                    if ($('#mobile').next('.text-danger').length === 0) {
                        $('#mobile').after('<span class="text-danger">This mobile number is already registered.</span>');
                    } else {
                        $('#mobile').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                } else if(data > 0 ) {
                    $('#customerForm')[0].reset();
                    $('#customerModal').modal('hide');
                    $('#alert_message').text('Customer Updated');
                    $('#alertModal').modal('show');
                    $('#action').attr('disabled', false);
                    userdataTable.ajax.reload();
                }
            }
        })
    });

    $(document).on('click', '.update', function() {
        var userid = $(this).attr("id");
        var btn_action = 'getCustomer';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { userid: userid, btn_action: btn_action },
            dataType: "json",
            success: function(data) {
                $('#customerModal').modal('show');
                $('#cname').val(data.name);
                $('#mobile').val(data.mobile);
                $('#address').val(data.address);
                $('.modal-title').html("<i class='fa fa-edit'></i> Edit Customer");
                $('#userid').val(userid);
                $('#btn_action').val('customerUpdate');
                $('.text-danger').remove(); // Remove error messages
            }
        })
    });

    $(document).on('click', '.delete', function() {
        var userid = $(this).attr("id");
        var btn_action = "customerDelete";
        if (confirm("Are you sure you want to delete this customer?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { userid: userid, btn_action: btn_action },
                success: function(data) {
                    $('#alert_message').text("Customer Deleted");
                    $('#alertModal').modal('show');
                    userdataTable.ajax.reload();
                }
            })
        } else {
            return false;
        }
    });


});