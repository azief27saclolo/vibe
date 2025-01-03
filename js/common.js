$(document).ready(function() {
    var url = window.location.pathname.split("/").pop();
    var page = url.substr(0, url.lastIndexOf('.'));
    $("a#" + page + "_menu").css({ 'color': '#FFF' });

    // Function to format numbers with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

   

    //==
    
    // Fetch and display income data for the last 7 days
    $.ajax({
        url: "action.php",
        method: "POST",
        data: { action: 'getIncomeDataToday' },
        dataType: "json",
        success: function(data) {
            const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            const labels = data.map(item => {
                const date = new Date(item.date);
                const dayName = dayNames[date.getDay()];
                const dayDate = date.getDate();
                return `${dayName} ${dayDate}`;
            });
            const totalIncomeData = data.map(item => item.total_income);
            const productIncomeData = data.map(item => item.product_income);
            const serviceIncomeData = data.map(item => item.service_income);

            const ctx = document.getElementById('incomeChartLast7Days');
            if (ctx) {
                const currentMonth = monthNames[new Date().getMonth()];
                const incomeChart = new Chart(ctx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Total Income',
                                data: totalIncomeData,
                                backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1,
                                fill: false
                            },
                            {
                                label: 'Product Income',
                                data: productIncomeData,
                                backgroundColor: 'rgba(12, 124, 36, 0.5)',
                                borderColor: 'rgb(21, 105, 46)',
                                borderWidth: 1,
                                fill: false
                            },
                            {
                                label: 'Service Income',
                                data: serviceIncomeData,
                                backgroundColor: 'rgba(255, 206, 86, 0.5)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 1,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        plugins: {
                            title: {
                                display: true,
                                text: `Income Data for ${currentMonth}`
                            },
                            datalabels: {
                                anchor: 'end',
                                align: 'end',
                                formatter: function(value) {
                                    return formatNumber(value); // Format the number if needed
                                },
                                color: 'black'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Register the plugin
                });
            } else {
                console.error('Element with ID "incomeChartLast7Days" not found.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching income data:', error);
            console.log(xhr.responseText); // Log the server response
        }
    });

    
    //==

    // Fetch and display income data All
    $.ajax({
        url: "action.php",
        method: "POST",
        data: { action: 'getIncomeDataAll' },
        dataType: "json",
        success: function (data) {
            $('#total_income_all').text(formatNumber(data.total_income));
            $('#product_income_all').text(formatNumber(data.product_income));
            $('#service_income_all').text(formatNumber(data.service_income));
        }
    });
    //==
    
    
        // Fetch and display monthly income data
        $.ajax({
            url: "action.php",
            method: "POST",
            data: { action: 'getMonthlyIncomeData' },
            dataType: "json",
            success: function(data) {
                try {
                    console.log(data); // Log the server response
                    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                    const currentYear = new Date().getFullYear(); // Get the current year
                    const labels = data.map(item => `${monthNames[item.month - 1]} ${currentYear}`);
                    const totalIncomeData = data.map(item => item.total_income);
                    const productIncomeData = data.map(item => item.product_income);
                    const serviceIncomeData = data.map(item => item.service_income);
    
                    // Render the line graph using Chart.js
                    const ctx = document.getElementById('incomeChartMonth').getContext('2d');
                    const incomeChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Total Income',
                                    data: totalIncomeData,
                                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1,
                                    fill: false
                                },
                                {
                                    label: 'Product Income',
                                    data: productIncomeData,
                                    backgroundColor: 'rgba(19, 190, 47, 0.5)',
                                    borderColor: 'rgb(32, 134, 71)',
                                    borderWidth: 1,
                                    fill: false
                                },
                                {
                                    label: 'Service Income',
                                    data: serviceIncomeData,
                                    backgroundColor: 'rgba(255, 206, 86, 0.5)',
                                    borderColor: 'rgba(255, 206, 86, 1)',
                                    borderWidth: 1,
                                    fill: false
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    formatter: function(value) {
                                        return formatNumber(value); // Format the number if needed
                                    },
                                    color: 'black'
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        },
                        plugins: [ChartDataLabels] // Register the plugin
                    });
                } catch (error) {
                    console.error('Error parsing income data:', error);
                    console.log('Response data:', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching income data:', error);
                console.log(xhr.responseText); // Log the server response
            }
        });
    
    
    //==
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