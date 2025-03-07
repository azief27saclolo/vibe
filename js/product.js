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
            $(row).find('td:eq(0), td:eq(8)').addClass('text-center') // Update the target to match the correct number of columns
        },
    });

    $('#addProduct').click(function() {
        console.log('Add Replace button clicked');
        $('#productModal').modal('show');
        $('#productForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Product");
        $('#action').val("Add");
        $('#btn_action').val("addProduct");
        $('.text-danger').remove(); // Remove error messages
    });

    $(document).on('change', '#categoryid', function() {
        var categoryid = $('#categoryid').val();
        var btn_action = 'getCategoryBrand';
        var categoryname = $('#categoryid option:selected').text();

        if(categoryname == "Phone"){
            $('#part_select').removeClass('hidden');
        }else{
            $('#part_select').addClass('hidden');
        }

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
            var buttonHtml = '<button type="button" class="btn btn-secondary ml-1 mt-2 selected-part-button" data-part-id="' + selectedPartId + '">' + selectedPartName + '</button>';
            $('#selectedPartsContainer').append(buttonHtml);
            $("#partid option:selected").remove(); // Remove the selected option from the dropdown
            $("#partid").val(""); // Reset the dropdown
        }
    });


    $(document).on('click', '.selected-part-button', function() {
        var partId = $(this).data('part-id');
        var partName = $(this).text();
        if ($('#partid option[value="' + partId + '"]').length === 0) {
            $('#partid').append('<option value="' + partId + '">' + partName + '</option>'); // Add the option back to the dropdown
        }
        $(this).remove(); // Remove the button
    });

    //=======================
    $(document).on('submit', '#productForm', function(event) {
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        setTimeout(function() {
            $('#action').attr('disabled', false);
        }, 1000);
        var formData = new FormData(this); // Use FormData to handle file uploads
        // Collect selected parts
        $('.selected-part-button').each(function() {
            formData.append('selected_parts[]', $(this).data('part-id'));
        });

        $.ajax({
            url: "action.php",
            method: "POST",
            data: formData,
            contentType: false, // Required for FormData
            processData: false, // Required for FormData
            success: function(data) {
                $flag = false;
                if (data == 0) {
                    if ($('#pname').next('.text-danger').length === 0) {
                        $('#pname').after('<span class="text-danger">This product is already existed.</span>');
                    } else {
                        $('#pname').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                    $flag = true;
                }  if(data == 1 ) {
                    $('#alert_message').text('Product Updated');
                    $('#alertModal').modal('show');
                    $('#productForm')[0].reset();
                    $('#productModal').modal('hide');
                    $('#action').attr('disabled', false);
                    productData.ajax.reload();

                    // Remove buttons in #part_select
                    $('#part_select .selected-part-button').remove();
                    
                    // Restore all values in dropdown
                    $('#categoryid').val('').trigger('change');
                    $flag = true;
                } if (data == 2) {
                    if ($('#selling_price').next('.text-danger').length === 0) {
                        $('#selling_price').after('<span class="text-danger">Selling Price must be above base price..</span>');
                    } else {
                        $('#selling_price').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                    $flag = true;
                }if(!$flag){
                    var parts = JSON.parse(data);
                    if ($('#error').next('.text-danger').length === 0) {
                        $('#error').after('<span class="text-danger">Stock is 0 for the parts '+ parts.join(',') +'.</span>');
                    } else {
                        $('.text-danger').remove(); // Remove error messages
                        $('#error').after('<span class="text-danger">Stock is 0 for the parts '+ parts.join(',') +'.</span>');
                        $('#error').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                }

            
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
                $('#description').val(data.description);
                $('#quantity').val(data.quantity);
                $('#base_price').val(data.base_price);
                $('#selling_price').val(data.selling_price);
                $('#supplierid').val(data.supplier);
                $('#existing_image').val(data.image);
                if (data.image) {
                    $('#current_image').attr('src', 'img/' + data.image).show();
                } else {
                    $('#current_image').hide();
                }
                $('.modal-title').html("<i class='fa fa-edit'></i> Edit Product");
                $('#pid').val(pid);
                $('#action').val("Edit");
                $('#btn_action').val("updateProduct");
                $('.text-danger').remove(); // Remove error messages
                // Show "Part Replaced" section if category is Phone
                if ($('#categoryid option:selected').text() === "Phone") {
                    $('#part_select').removeClass('hidden');
                } else {
                    $('#part_select').addClass('hidden');
                }

                // Display replaced parts as buttons and remove them from the dropdown
                $('#selectedPartsContainer').empty();
                $('#partid').find('option').show(); // Show all options first
                if (data.parts_replaced && data.parts_replaced.length > 0) {
                    data.parts_replaced.forEach(function(part) {
                        var buttonHtml = '<button type="button" class="btn btn-secondary ml-1 mt-2 selected-part-button" data-part-id="' + part.part_pid + '">' + part.pname + '</button>';
                        $('#selectedPartsContainer').append(buttonHtml);
                        $('#partid').find('option[value="' + part.part_pid + '"]').remove(); // Remove the option from the dropdown
                    });
                }
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
                    $('#alert_message').text('Product Deleted');
                    $('#alertModal').modal('show');
                    productData.ajax.reload();
                }
            });
        } else {
            return false;
        }
    });
});