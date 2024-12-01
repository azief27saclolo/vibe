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
<script src="js/category.js"></script>
<script src="js/common.js"></script>
<?php include('inc/container.php');?>	

<div class="container">
    <?php include("menus.php"); ?>

    <div class="row">
		<div class="col-lg-12">
			<div class="card card-default rounded-0 shadow">
                <div class="card-header">
                        <div class="row">
							<div class="col-lg-8 col-md-8 col-sm-8 col-xs-6">
									<h3 class="card-title">Revenue</h3>
							</div>
					</div>
                    <div style="clear:both"></div>
                </div>
                <div class="card-body">
                    <div class="row">
                    	<div class="col-sm-12 table-responsive">
                    		<table id="categoryList" class="table table-bordered table-striped">
                    			<thead><tr>
									<th>ID</th>
									<th>Product</th>
									<th>Sales</th>
									<th>Action</th>
								</tr></thead>
                    		</table>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>