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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Inventory</h3>
                </div>
                <div class="card-body">
                  
					<div class="row">
						<div class="col-sm-12">
							<table id="inventoryDetails" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>ID</th>
										<th>Product/Code</th>
										<th>In Stock</th>
										<th>Sold</th>
                                        <th>Base Price</th>
                                        <th>Selling Price</th>
                                        <th>Revenue</th>
                                        <th>Income</th>
									</tr>
							</thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('inc/footer.php');?>






