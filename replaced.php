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
<script src="js/replaced.js"></script>
<script src="js/common.js"></script>
<?php include('inc/container.php');?>
<div class="container">		
		
	<?php include("menus.php"); ?> 	
	
	<div class="row">
			<div class="col-lg-12">
				<div class="card card-default rounded-0 shadow">
                    <div class="card-header">
                    	<div class="row">
                            <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="card-title">Replace List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6 text-end">
                                <button type="button" name="add" id="addReplaced" class="btn btn-primary bg-gradient rounded-0 btn-sm"><i class="far fa-plus-square"></i> Add Replace</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="replacedList" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>      
									<th>Phone Name</th>	
									<th>Replacement list</th>									
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
     </div>


     <div id="replacedModal" class="modal fade">
            <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add Replace</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form method="post" id="replacedForm">
                                <input type="hidden" name="pid" id="pid" />
                                <input type="hidden" name="btn_action" id="btn_action" />
                                <input type="hidden" name="replacement_id" id="replacement_id" />
                                
                                <div class="mb-3">
                                    <label>PhoneName</label>
                                    <select name="phone" id="phone" class="form-select rounded-0" required>
                                        <option value="">Select Phone</option>
                                        <?php echo $inventory->phoneDropdownList();?>
                                    </select>
                                </div>
                               
                                <div class="mb-3">
                                    <label>Part Name</label>
                                    <select name="part" id="part" class="form-select rounded-0" required>
                                        <option value="">Select Product</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Quantity</label>
                                    <input type="number" name="quantity" id="quantity" class="form-control rounded-0"  />
                                </div>

                            </form>
                        </div>

                        <div class="modal-footer">
                            <input type="submit" name="action" id="action" class="btn btn-primary rounded-0 btn-sm" value="Add" form="replacedForm"/>
                            <button type="button" class="btn btn-default border rounded-0 btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
            </div>
        </div>
	

        
</div>	
<?php include('inc/footer.php');?>