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
<script src="js/purchase.js"></script>
<script src="js/common.js"></script>
<script src="js/service_availed.js"></script>
<?php include('inc/container.php');?>
<div class="container">		
		
	<?php include("menus.php"); ?> 

    <div class="row">
			<div class="col-lg-12">
				<div class="card card-default rounded-0 shadow">
                    <div class="card-header">
                    	<div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Manage Service Availed</h3>
                            </div>
                        
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-end">
                                <button type="button" name="add" id="addService_availed" class="btn btn-primary btn-sm rounded-0"><i class="far fa-plus-square"></i> New Service Availed</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="service_availedList" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>      
									<th>Customer Name</th>	
									<th>Service Name</th> 
									<th>Availed Date</th> 									
                                    <th>Action</th>
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
		</div>

        <div class="modal fade" id="service_availedModal" tabindex="-1" role="dialog" aria-labelledby="service_availedModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" id="service_availedForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="service_availedModalLabel">Add Service Availed</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="customer_id">Customer</label>
                                <select class="form-control" id="customer_id" name="customer_id" required>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="service_id">Service</label>
                                <select class="form-control" id="service_id" name="service_id" required>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                            <div class="form-group" style="display:none;">
                                <label for="availed_date">Availed Date</label>
                                <input type="date" class="form-control" id="availed_date" name="availed_date" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="service_availed_id" id="service_availed_id">
                            <input type="hidden" name="btn_action" id="btn_action" value="addServiceAvailed">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>	
<?php include('inc/footer.php');?>