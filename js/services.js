$(document).ready(function() {

    var servicesData = $('#servicesList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'servicesList' },
            dataType: "json"
        },
        "columnDefs": [{
            "targets": [0, 3],
            "orderable": false,
        }, ],
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

    // $(document).on('click', '.delete', function() {
    //     var services_id = $(this).attr("id");  // Ensure it uses 'id' instead of 'replacement_id'
    //     var btn_action = 'deleteServices';
    //     if (confirm("Are you sure you want to delete this replace?")) {
    //         $.ajax({
    //             url: "action.php",
    //             method: "POST",
    //             data: { services_id: services_id, btn_action: btn_action },
    //             success: function(data) {
    //                 servicesData.ajax.reload();
    //             }
    //         });
    //     } else {
    //         return false;
    //     }
    // });


    // $(document).on('click', '.update', function() {
    //     var services_id = $(this).attr("id");
    //     var btnAction = 'getServices';
    //     $.ajax({
    //         url: "action.php",
    //         method: "POST",
    //         data: { services_id: services_id, btn_action: btnAction },
    //         dataType: "json",
    //         success: function(data) {
    //             $('#servicesModal').modal('show');
    //             $('#services_name').val(data.services_name);
    //             $('.modal-title').html("<i class='fa fa-edit'></i> Edit Services");
    //             $('#services_id').val(services_id);
    //             $('#action').val('Edit');
    //             $('#btn_action').val("updateServices");
    //         }
    //     })
    // });

});