$(document).ready(function() {
    $('#categoryAdd').click(function() {
        $('#categoryForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Category");
        $('#action').val('Add');
        $('#btn_action').val('categoryAdd');
        $('.text-danger').remove(); // Remove error messages
    });
    var categoryData = $('#categoryList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'categoryList' },
            dataType: "json"
        },
        "columnDefs": [{
            "targets": [0, 3],
            "orderable": false,
        }],
        "pageLength": 25,
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle')
            $(row).find('td:eq(0), td:eq(3)').addClass('text-center')
        }
    });
    $(document).on('submit', '#categoryForm', function(event) {
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
                    if ($('#category').next('.text-danger').length === 0) {
                        $('#category').after('<span class="text-danger">This category is already exist.</span>');
                    } else {
                        $('#category').next('.text-danger').fadeOut(100).fadeIn(100).fadeOut(100).fadeIn(100);
                    }
                } else if(data > 0 ) {
                    $('#alert_message').text('Category Updated');
                    $('#alertModal').modal('show');
                    $('#categoryForm')[0].reset();
                    $('#categoryModal').modal('hide');
                    $('#action').attr('disabled', false);
                    categoryData.ajax.reload();
                }
                
            }
        })
    });
    $(document).on('click', '.update', function() {
        var categoryId = $(this).attr("id");
        var btnAction = 'getCategory';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { categoryId: categoryId, btn_action: btnAction },
            dataType: "json",
            success: function(data) {
                $('#categoryModal').modal('show');
                $('#category').val(data.name);
                $('.modal-title').html("<i class='fa fa-edit'></i> Edit Category");
                $('#categoryId').val(categoryId);
                $('#action').val('Edit');
                $('#btn_action').val("updateCategory");
                $('.text-danger').remove(); // Remove error messages
            }
        })
    });
    $(document).on('click', '.delete', function() {
        var categoryId = $(this).attr('id');
        var status = $(this).data("status");
        var btn_action = 'deleteCategory';
        if (confirm("Are you sure you want to delete this category?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { categoryId: categoryId, status: status, btn_action: btn_action },
                success: function(data) {
                    $('#alert_message').text("Category Deleted");
                    $('#alertModal').modal('show');
                    categoryData.ajax.reload();
                }
            })
        } else {
            return false;
        }
    });
});