<?php 
ob_start();
session_start();
include('inc/header.php');
include 'Inventory.php';
$inventory = new Inventory();
$inventory->checkLogin();
?>

<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/common.js"></script>

<?php include('inc/container.php');?>

<div class="container-fluid">
<?php include("menus.php"); ?>   

    <div class="row">
        <div class="col-md-12">
            <div class="card card-default rounded-0 shadow">
            
            <div class="top_holder">
                <h4>Total Income</h4>
                    <div class="card_top green">
                        <p class="black">Total Income</p>
                        <h4 class="white" id="total_income_all">0</h4>
                    </div>
                    <div class="card_top blue">
                        <p class="black">Product Income</p>
                        <h4 class="white" id="product_income_all">0</h4>
                    </div>
                    <div class="card_top blue">
                        <p class="black">Service Income</p>
                        <h4 class="white" id="service_income_all">0</h4>  
                    </div>
                </div>
                <div class="spacers"></div>
                <h4 class="center">Monthly Income</h4>
                <div class="card_holder_top">
                    <div class="">
                        <canvas id="incomeChartMonth" width="400" height="200"></canvas>
                    </div>
                </div>
                <hr>
                <h4 class="center">Daily Income</h4>
                <div class="card_holder_top">
                    <div class="">
                        <canvas id="incomeChartLast7Days" width="400" height="200"></canvas>
                    </div>
                </div>
                <div class="spacers"></div>
            </div>
        </div>
    </div>
</div>

<?php include('inc/footer.php');?>






