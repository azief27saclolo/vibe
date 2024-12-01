$(document).ready(function () {
    var url = window.location.pathname.split("/").pop();
    var page = url.substr(0, url.lastIndexOf('.'));
    $("a#" + page + "_menu").css({ 'color': '#FFF' });

        var inventoryData = $('#getRevenueData').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "action.php",
                type: "POST",
                data: { action: 'getRevenueData' },
                dataType: "json"
            },
            "columns": [
                { "data": "#" },
                { "data": "product" },
                { "data": "price" },
                { "data": "pcs_sold" },
                { "data": "sales" },
                { "data": "profit" }
            ],
            "order": [[0, "asc"]],
            "pageLength": 10,
            "lengthChange": false,
            "rowCallback": function (row, data, index) {
                $(row).find('td:eq(4)').css({ 'font-weight': 'bold' });
                if (data['profit'] < 0) {
                    $(row).find('td:eq(5)').css({ 'color': 'red', 'font-weight': 'bold' });
                } else {
                    $(row).find('td:eq(5)').css({ 'color': 'green', 'font-weight': 'bold' });
                }
            }
        });
});    