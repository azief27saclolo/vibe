$(document).ready(function() {
    var url = window.location.pathname.split("/").pop();
    var page = url.substr(0, url.lastIndexOf('.'));
    $("a#" + page + "_menu").css({ 'color': '#FFF' });

    // Function to format numbers with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Fetch and display income data
    $.ajax({
        url: "action.php",
        method: "POST",
        data: { action: 'getIncomeData' },
        dataType: "json",
        success: function (data) {
            $('#total_income').text(formatNumber(data.total_income));
            $('#product_income').text(formatNumber(data.product_income));
            $('#service_income').text(formatNumber(data.service_income));
        }
    });

    var inventoryData = $('#inventoryDetails').DataTable({
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "order": [],
        "ajax": {
            url: "action.php",
            type: "POST",
            data: { action: 'getInventoryDetails' },
            dataType: "json"
        },
        'rowCallback': function(row, data, index) {
            $(row).find('td').addClass('align-middle text-end')
            $(row).find('td:eq(0)').removeClass('text-end').addClass('text-center')
            $(row).find('td:eq(1)').removeClass('text-end')
            if (data[2] > 0) {
                $(row).find('td:eq(2)').css({ 'color': 'green', 'font-weight': 'bold' });
            } else {
                $(row).find('td:eq(2)').css({ 'color': 'red', 'font-weight': 'bold' });
            }
        },
        "pageLength": 10
    });
});