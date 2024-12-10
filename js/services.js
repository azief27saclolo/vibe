$(document).ready(function() {

    var servicesData = $('#servicesList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'listServices' },
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

    $('#addServices').click(function() {
        console.log('Add Services button clicked');
        $('#servicesModal').modal('show');
        $('#servicesForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Services");
        $('#action').val("Add");
        $('#btn_action').val("addServices");
    });

    $(document).on('submit', '#servicesForm', function(event) {
        event.preventDefault();
        console.log('Add    Services fomr clicked');
        $('#action').attr('disabled', 'disabled');
        var formData = $(this).serialize();
        $.ajax({
            url: "action.php",
            method: "POST",
            data: formData,
            success: function(data) {
                console.log('Im in Submit');
                $('#servicesForm')[0].reset();
                $('#servicesModal').modal('hide');
                $('#action').attr('disabled', false);
                servicesData.ajax.reload();
            }
        })
    });

    $(document).on('click', '.delete', function() {
        var services_id = $(this).attr("id");  
        var btn_action = 'deleteServices';
        if (confirm("Are you sure you want to delete this service?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: { service_id: services_id, btn_action: btn_action },
                success: function(data) {
                    console.log('Im in Delete');
                    console.log('Response:', data); // Log the response from the server
                    servicesData.ajax.reload();
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                }
            });
        } else {
            return false;
        }
    });


    $(document).on('click', '.update', function() {
        var services_id = $(this).attr("id");
        var btn_action = 'getServicesDetails';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { services_id: services_id, btn_action: btn_action },
            dataType: "json",
            success: function(data) {
                $('#servicesModal').modal('show');
                $('#service_name').val(data.service_name);
                $('#service_price').val(data.service_price);
                $('.modal-title').html("<i class='fa fa-edit'></i> Update Service");
                $('#services_id').val(services_id);
                $('#action').val('Update');
                $('#btn_action').val("updateServices");
            }
        })
    });

});