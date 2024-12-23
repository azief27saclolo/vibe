$(document).ready(function() {
    
    var orderData = $('#orderList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'listOrder' },
            dataType: "json"
        },
        "pageLength": 10,
        "columnDefs": [{
            "target": [0, 4, 5], // Updated to include the new column index
            "orderable": false
        }],
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle')
            $(row).find('td:eq(0), td:eq(4), td:eq(5)').addClass('text-center') // Updated to include the new column index
        },
    });

    $('#addOrder').click(function() {
        $('#orderModal').modal('show');
        $('#orderForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Order");
        $('#action').val("Add");
        $('#btn_action').val("addOrder");
        $('.text-danger').remove(); 
    });


    $(document).on('submit', '#orderForm', function(event) {
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
                    if ($('#sold').next('.text-danger').length === 0) {
                        $('#sold').after('<span class="text-danger">The Quantity is Exceeded than the Stock.</span>');
                    } else {
                        $('#sold').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                } else {
                    $('#orderForm')[0].reset();
                    $('#orderModal').modal('hide');
                    $('#action').attr('disabled', false);
                    orderData.ajax.reload();
                    location.reload(); // Refresh the page
                }
            }
        })
    });

    $(document).on('click', '.view', function() {
        var pid = $(this).attr("id");
        var btn_action = 'viewProduct';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { pid: pid, btn_action: btn_action },
            success: function(data) {
                $('#productViewModal').modal('show');
                $('#productDetails').html(data);
            }
        })
    });

    $(document).on('click', '.update', function() {
        var order_id = $(this).attr("id");
        $('.text-danger').remove(); 
        var btn_action = 'getOrderDetails';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { order_id: order_id, btn_action: btn_action },
            dataType: "json",
            success: function(data) {
                if (data <= 0) {
                    if ($('#sold').next('.text-danger').length === 0) {
                        $('#sold').after('<span class="text-danger">The Quantity is Exceeded than the Stock.</span>');
                    } else {
                        $('#sold').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                } else {
                    $('#orderModal').modal('show');
                    $('#product').val(data.product_id);
                    $('#sold').val(data.total_sell);
                    $('#customer').val(data.customer_id);
                    $('.modal-title').html("<i class='fa fa-edit'></i> Edit Order");
                    $('#order_id').val(order_id);
                    $('#action').val("Edit");
                    $('#btn_action').val("updateOrder");
                }
                
            }
        })
    });

    $(document).on('click', '.delete', function() {
        var order_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'deleteOrder';
        if (confirm("Are you sure you want to delete this order?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { order_id: order_id, status: status, btn_action: btn_action },
                success: function(data) {
                    $('.text-danger').remove(); 
                    orderData.ajax.reload();
                  
                }
            });
        } else {
            return false;
        }
    });
});