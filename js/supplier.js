$(document).ready(function() {
    $('#addSupplier').click(function() {
        $('#supplierModal').modal('show');
        $('#supplierForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Customer");
        $('#action').val("Add");
        $('#btn_action').val("addSupplier");
        $('.text-danger').remove(); // Remove error messages
    });
    var supplierDataTable = $('#supplierList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'supplierList' },
            dataType: "json"
        },
        "columnDefs": [{
            "target": [0, 4],
            "orderable": false
        }],
        "pageLength": 25,
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle')
            $(row).find('td:eq(0), td:eq(3), td:eq(4)').addClass('text-center')
        },
    });

    $(document).on('submit', '#supplierForm', function(event) {
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
                if (data == 1) {
                    $('.text-danger').remove(); // Remove error messages
                    if ($('#mobile').next('.text-danger').length === 0) {
                        $('#mobile').after('<span class="text-danger">This mobile number is already registered.</span>');
                    } else {
                        $('#mobile').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                } 
                if(data <= 0 ) {
                    $('.text-danger').remove(); // Remove error messages
                    if ($('#supplier_name').next('.text-danger').length === 0) {
                        $('#supplier_name').after('<span class="text-danger">This supplier is already existed.</span>');
                    } else {
                        $('#supplier_name').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                }
                 if(data == 2){
                    $('#supplierForm')[0].reset();
                    $('#supplierModal').modal('hide');
                    $('#action').attr('disabled', false);
                    supplierDataTable.ajax.reload();
                    $('#alert_message').text('Supplier Updated');
                    $('#alertModal').modal('show');
                }
                
            }
        })
    });

    $(document).on('click', '.update', function() {
        var supplier_id = $(this).attr("id");
        var btn_action = 'getSupplier';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { supplier_id: supplier_id, btn_action: btn_action },
            dataType: "json",
            success: function(data) {
                $('#supplierModal').modal('show');
                $('#supplier_name').val(data.supplier_name);
                $('#address').val(data.address);
                $('#mobile').val(data.mobile);
                $('.modal-title').html("<i class='fa fa-edit'></i> Edit Supplier");
                $('#supplier_id').val(supplier_id);
                $('#action').val('Update');
                $('#btn_action').val('updateSupplier');
                $('.text-danger').remove(); // Remove error messages
            }
        })
    });

    $(document).on('click', '.delete', function() {
        var supplier_id = $(this).attr("id");
        var btn_action = "deleteSupplier";
        if (confirm("Are you sure you want to delete this supplier?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { supplier_id: supplier_id, btn_action: btn_action },
                success: function(data) {
                    supplierDataTable.ajax.reload();
                }
            })
        } else {
            return false;
        }
    });

});