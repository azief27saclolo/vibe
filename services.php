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
<script src="js/services.js"></script>
<script src="js/common.js"></script>
<?php include('inc/container.php');?>
<div class="container">		
	<?php include("menus.php"); ?> 	


	<div class="row">
			<div class="col-lg-12">
				<div class="card card-default rounded-0 shadow">
                    <div class="card-header">

                    <div class="card-body">
					<div id="alertModal" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Message</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p id="alert_message"></p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    	<div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Services List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-end">
                                <button type="button" name="add" id="addServices" class="btn btn-primary bg-gradient rounded-0 btn-sm"><i class="far fa-plus-square"></i> Add Service</button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="servicesList" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>      
                                    <th>Service Image</th>	
									<th>Service Name</th>	
									<th>Service Price</th>						
                                    <th>Action</th>
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
     </div>

	<div id="servicesModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add Service</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="servicesForm" enctype="multipart/form-data">
                                <input type="hidden" name="btn_action" id="btn_action" />
                                <input type="hidden" name="services_id" id="services_id" />
                                <input type="hidden" name="existing_image" id="existing_image" />
                                
								<div class="form-group">
                                    <label>Service Name </label>
                                    <input type="text" name="service_name" id="service_name" class="form-control rounded-0 " required />
                                </div>
                                <div class="form-group">
                                    <label>Service Image</label>
                                    <input type="file" name="service_image" id="service_image" class="form-control rounded-0" accept="image/*" />
                                    <img id="current_image" src="" alt="Current Image" style="max-width: 100px; max-height: 100px; display: none;" />
                                </div>
                                
                                <div class="form-group">
                                    <label>Service Price</span></label>
                                    <input type="number" name="service_price" id="service_price" class="form-control rounded-0"   required/>
                                </div>


                            </form>
                        </div>
                        <div class="modal-body">
                                    <p id="alert_message"></p>
                                </div>
                        <div class="modal-footer">
                            <input type="submit" name="action" id="action" class="btn btn-primary rounded-0 btn-sm" value="Add" form="servicesForm"/>
                            <button type="button" class="btn btn-default border rounded-0 btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
            </div>
        </div>
</div>	

<?php include('inc/footer.php');?>