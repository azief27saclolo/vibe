$(document).ready(function() {
    $('#addService_availed').click(function() {
        $('#service_availedModal').modal('show');
        $('#service_availedForm')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Service Availed");
        $('#btn_action').val('addServiceAvailed');
        loadDropdowns();
        setTodayDate();
    });

    var userdataTable = $('#service_availedList').DataTable({
        "lengthChange": false,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'service_availedList' },
            dataType: "json"
        },
        "columnDefs": [{
            "targets": [0, 4],
            "orderable": false
        }],
        "pageLength": 25,
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle');
            $(row).find('td:eq(0), td:eq(3)').addClass('text-center');
            $(row).find('td:eq(4)').addClass('text-center');
        }
    });

    $('#service_availedForm').on('submit', function(event) {
        event.preventDefault();
        var availedDate = new Date($('#availed_date').val());
        var today = new Date();
        today.setHours(0, 0, 0, 0); // Set time to 00:00:00 for comparison

        if (availedDate < today) {
            alert("Date must be today.");
            return false;
        }

        $.ajax({
            url: "action.php",
            method: "POST",
            data: $(this).serialize(),
            success: function(data) {
                $('#service_availedForm')[0].reset();
                $('#service_availedModal').modal('hide');
                userdataTable.ajax.reload();
            }
        });
    });

    $(document).on('click', '.update', function() {
        var service_availed_id = $(this).attr("id");
        var btn_action = 'getServiceAvailedDetails';
        $.ajax({
            url: "action.php",
            method: "POST",
            data: {service_availed_id: service_availed_id, btn_action: btn_action},
            dataType: "json",
            success: function(data) {
                $('#service_availedModal').modal('show');
                $('#service_availed_id').val(service_availed_id);
                $('#availed_date').val(data.availed_date);
                $('#btn_action').val('updateServiceAvailed');
                loadDropdowns(data.customer_id, data.service_id);
            }
        });
    });

    $(document).on('click', '.delete', function() {
        var service_availed_id = $(this).attr("id");
        var btn_action = 'deleteServiceAvailed';
        if (confirm("Are you sure you want to delete this record?")) {
            $.ajax({
                url: "action.php",
                method: "POST",
                data: {service_availed_id: service_availed_id, btn_action: btn_action},
                success: function(data) {
                    userdataTable.ajax.reload();
                }
            });
        }
    });

    function loadDropdowns(selectedCustomerId = null, selectedServiceId = null) {
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { action: 'getCustomerList' },
            success: function(data) {
                $('#customer_id').html(data);
                if (selectedCustomerId) {
                    $('#customer_id').val(selectedCustomerId);
                }
            }
        });

        $.ajax({
            url: "action.php",
            method: "POST",
            data: { action: 'getServiceList' },
            success: function(data) {
                $('#service_id').html(data);
                if (selectedServiceId) {
                    $('#service_id').val(selectedServiceId);
                }
            }
        });
    }

    function setTodayDate() {
        var today = new Date().toISOString().split('T')[0];
        $('#availed_date').val(today);
    }
});