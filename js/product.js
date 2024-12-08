$(document).ready(function() {

    var productData = $('#productList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'listProduct' },
            dataType: "json"
        },
        "columnDefs": [{
            "targets": [0, 9], // Update the target to match the correct number of columns
            "orderable": false,
        }],
        "pageLength": 10,
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle')
            $(row).find('td:eq(0), td:eq(9)').addClass('text-center') // Update the target to match the correct number of columns
        },
    });

    $('#addProduct').click(function() {
        console.log('Add Replace button clicked');
        $('#productModal').modal('show');
        $('#productForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Product");
        $('#action').val("Add");
        $('#btn_action').val("addProduct");
    });

    $(document).on('change', '#categoryid', function() {
        var categoryid = $('#categoryid').val();
        var btn_action = 'getCategoryBrand';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { categoryid: categoryid, btn_action: btn_action },
            success: function(data) {
                $('#brandid').html(data);
            }
        });
    });

    $(document).on('change', '#partid', function() {
        var selectedPartName = $("#partid option:selected").text();
        var selectedPartId = $("#partid").val();
        if (selectedPartName && selectedPartId) {
            var buttonHtml = '<button type="button" class="btn btn-secondary mt-2 selected-part-button" data-part-id="' + selectedPartId + '">' + selectedPartName + '</button>';
            $('#selectedPartsContainer').append(buttonHtml);
            $("#partid option:selected").remove(); // Remove the selected option from the dropdown
            $("#partid").val(""); // Reset the dropdown
        }
    });

    $(document).on('click', '.selected-part-button', function() {
        var partId = $(this).data('part-id');
        var partName = $(this).text();
        $('#partid').append('<option value="' + partId + '">' + partName + '</option>'); // Add the option back to the dropdown
        $(this).remove(); // Remove the button
    });

    $(document).on('submit', '#productForm', function(event) {
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var formData = $(this).serializeArray();
        
        // Collect selected parts
        $('.selected-part-button').each(function() {
            formData.push({ name: 'selected_parts[]', value: $(this).data('part-id') });
        });

        $.ajax({
            url: "action.php",
            method: "POST",
            data: formData,
            success: function(data) {
            $('#productForm')[0].reset();
            $('#productModal').modal('hide');
            $('#action').attr('disabled', false);
            productData.ajax.reload();
        }
    });
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
        var pid = $(this).attr("id");
        var btn_action = 'getProductDetails';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { pid: pid, btn_action: btn_action },
            dataType: "json",
            success: function(data) {
                $('#productModal').modal('show');
                $('#categoryid').val(data.categoryid);
                $('#brandid').html(data.brand_select_box);
                $('#brandid').val(data.brandid);
                $('#pname').val(data.pname);
                $('#pmodel').val(data.model);
                $('#description').val(data.description);
                $('#quantity').val(data.quantity);
                $('#base_price').val(data.base_price);
                $('#supplierid').val(data.supplier);
                $('.modal-title').html("<i class='fa fa-edit'></i> Edit Product");
                $('#pid').val(pid);
                $('#action').val("Edit");
                $('#btn_action').val("updateProduct");

                // Display parts replaced
                var partsReplacedHtml = '';
                if (data.parts_replaced.length > 0) {
                    partsReplacedHtml = '<ul>';
                    data.parts_replaced.forEach(function(part) {
                        partsReplacedHtml += '<li>' + part + '</li>';
                    });
                    partsReplacedHtml += '</ul>';
                } else {
                    partsReplacedHtml = 'None';
                }
                $('#selectedPartsContainer').html(partsReplacedHtml);
            }
        })
    });

    $(document).on('click', '.delete', function() {
        var pid = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'deleteProduct';
        if (confirm("Are you sure you want to delete this product?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { pid: pid, status: status, btn_action: btn_action },
                success: function(data) {
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">' + data + '</div>');
                    productData.ajax.reload();
                }
            });
        } else {
            return false;
        }
    });
});