<?php
class Inventory {
    private $host  = 'localhost';
    private $user  = 'root';
    private $password   = '';
    private $database  = 'vibe_db';   
	private $userTable = 'user';	
    private $customerTable = 'customer';
	private $categoryTable = 'category';
	private $brandTable = 'brand';
	private $productTable = 'product';
	private $supplierTable = 'supplier';
	private $purchaseTable = 'purchase';

	private $servicesTable = 'services';

	private $service_availedTable = 'service_availed';
	private $orderTable = 'orders';

	private  $replacedTable = 'product_replacement_parts';

	private $dbConnect = false;
    public function __construct(){
        if(!$this->dbConnect){ 
            $conn = new mysqli($this->host, $this->user, $this->password, $this->database);
            if($conn->connect_error){
                die("Error failed to connect to MySQL: " . $conn->connect_error);
            }else{
                $this->dbConnect = $conn;
            }
        }
    }
	private function getData($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$data= array();
		while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
			$data[]=$row;            
		}
		return $data;
	}
	private function getNumRows($sqlQuery) {
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		if(!$result){
			die('Error in query: '. mysqli_error());
		}
		$numRows = mysqli_num_rows($result);
		return $numRows;
	}
	public function login($email, $password){
		$password = md5($password);
		$sqlQuery = "
			SELECT userid, email, password, name, type, status
			FROM ".$this->userTable." 
			WHERE email='".$email."' AND password='".$password."'";
        return  $this->getData($sqlQuery);
	}	
	public function checkLogin(){
		if(empty($_SESSION['userid'])) {
			header("Location:login.php");
		}
	}
	public function getCustomer(){
		$sqlQuery = "
			SELECT * FROM ".$this->customerTable." 
			WHERE id = '".$_POST["userid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	
	public function getCustomerList(){		
		$sqlQuery = "SELECT * FROM ".$this->customerTable." ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= '(name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR address LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$customerData = array();	
		$increment = 1;
		while( $customer = mysqli_fetch_assoc($result) ) {		
			$customerRows = array();
			$customerRows[] = $increment++;
			$customerRows[] = $customer['name'];
			$customerRows[] = $customer['address'];			
			$customerRows[] = $customer['mobile'];	
			$customerRows[] = '<button type="button" name="update" id="'.$customer["id"].'" class="btn btn-primary btn-sm rounded-0 update" title="update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$customer["id"].'" class="btn btn-danger btn-sm rounded-0 delete" ><i class="fa fa-trash"></button>';
			$customerRows[] = '';
			$customerData[] = $customerRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$customerData
		);
		echo json_encode($output);
	}

	public function saveCustomer() {
    $sqlCheck = "SELECT * FROM ".$this->customerTable." WHERE mobile = '".$_POST['mobile']."'";
    $result = mysqli_query($this->dbConnect, $sqlCheck);
    if (mysqli_num_rows($result) > 0) {
        echo '0';
    } else {
        $sqlInsert = "
            INSERT INTO ".$this->customerTable."(name, address, mobile) 
            VALUES ('".$_POST['cname']."', '".$_POST['address']."', '".$_POST['mobile']."')";
        $success = mysqli_query($this->dbConnect, $sqlInsert);
        if($success) {
            echo '1';
        } else {
            echo '0';
        }
    }
}			
	public function updateCustomer() {
    if($_POST['userid']) {
        $sqlCheck = "SELECT * FROM ".$this->customerTable." WHERE mobile = '".$_POST['mobile']."' AND id != '".$_POST['userid']."'";
        $result = mysqli_query($this->dbConnect, $sqlCheck);
        if (mysqli_num_rows($result) > 0) {
            echo '0';
        } else {
            $sqlInsert = "
                UPDATE ".$this->customerTable." 
                SET name = '".$_POST['cname']."', address= '".$_POST['address']."', mobile = '".$_POST['mobile']."' 
                WHERE id = '".$_POST['userid']."'";
            mysqli_query($this->dbConnect, $sqlInsert);
            echo '1';
        }
    }
}	
	public function deleteCustomer(){
		$sqlQuery = "
			DELETE FROM ".$this->customerTable." 
			WHERE id = '".$_POST['userid']."'";		
		mysqli_query($this->dbConnect, $sqlQuery);		
	}
	// Category functions
	public function getCategoryList(){		
		$sqlQuery = "SELECT * FROM ".$this->categoryTable." ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= 'WHERE (name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR status LIKE "%'.$_POST["search"]["value"].'%") ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY categoryid DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$categoryData = array();	
		$increment = 1;
		while( $category = mysqli_fetch_assoc($result) ) {		
			$categoryRows = array();
			$status = '';
			if($category['status'] == 'active')	{
				$status = '<span class="label label-success">Active</span>';
			} else {
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$categoryRows[] = $increment++;
			$categoryRows[] = $category['name'];
			$categoryRows[] = $status;			
			$categoryRows[] = '<button type="button" name="update" id="'.$category["categoryid"].'" class="btn btn-primary btn-sm rounded-0 update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$category["categoryid"].'" class="btn btn-danger btn-sm rounded-0 delete"  title="Delete"><i class="fa fa-trash"></i></button>';
			$categoryData[] = $categoryRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$categoryData
		);
		echo json_encode($output);
	}
	public function saveCategory() {	
		$sqlInsert = "
			SELECT * FROM ".$this->categoryTable." WHERE name = '".$_POST['category']."'";		
		$result = mysqli_query($this->dbConnect, $sqlInsert);
		if(mysqli_num_rows($result) > 0) {
			echo '0';
		} else {
			$sqlInsert = "
				INSERT INTO ".$this->categoryTable."(name) 
				VALUES ('".$_POST['category']."')";		
			mysqli_query($this->dbConnect, $sqlInsert);
			echo '1';
		}
	}	
	public function getCategory(){
		$sqlQuery = "
			SELECT * FROM ".$this->categoryTable." 
			WHERE categoryid = '".$_POST["categoryId"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function updateCategory() {
		if($_POST['category']) {	
			$sqlInsert = "
			SELECT * FROM ".$this->categoryTable." WHERE name = '".$_POST['category']."' 
			 AND categoryid != '".$_POST['categoryId']."'";		
			$result = mysqli_query($this->dbConnect, $sqlInsert);
				if(mysqli_num_rows($result) > 0) {
					echo '0';
				} else {
					$sqlInsert = "
						UPDATE ".$this->categoryTable." 
						SET name = '".$_POST['category']."'
						WHERE categoryid = '".$_POST["categoryId"]."'";	
						mysqli_query($this->dbConnect, $sqlInsert);	
						echo '1';
				}
		}	
	}	
	public function deleteCategory(){
		$sqlQuery = "
			DELETE FROM ".$this->categoryTable." 
			WHERE categoryid = '".$_POST["categoryId"]."'";		
		mysqli_query($this->dbConnect, $sqlQuery);		
	}
	// Brand management 
	public function getBrandList(){				
		$sqlQuery = "SELECT * FROM ".$this->brandTable." as b 
			INNER JOIN ".$this->categoryTable." as c ON c.categoryid = b.categoryid ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= 'WHERE b.bname LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR c.name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR b.status LIKE "%'.$_POST["search"]["value"].'%" ';		
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY b.id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$brandData = array();	
		$increment = 1;
		while( $brand = mysqli_fetch_assoc($result) ) {			
			$status = '';
			if($brand['status'] == 'active')	{
				$status = '<span class="label label-success">Active</span>';
			} else {
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$brandRows = array();
			$brandRows[] = $increment++;
			$brandRows[] = $brand['bname'];
			$brandRows[] = $brand['name'];
			$brandRows[] = $status;
			$brandRows[] = '<button type="button" name="update" id="'.$brand["id"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$brand["id"].'" class="btn btn-danger btn-sm rounded-0  delete" data-status="'.$brand["status"].'" title="Delete"><i class="fa fa-trash"></i></button>';
			$brandData[] = $brandRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$brandData
		);
		echo json_encode($output);
	}
	public function categoryDropdownList(){		
		$sqlQuery = "SELECT * FROM ".$this->categoryTable." 
			WHERE status = 'active' 
			ORDER BY name ASC";	
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$categoryHTML = '';
		while( $category = mysqli_fetch_assoc($result)) {
			$categoryHTML .= '<option value="'.$category["categoryid"].'">'.$category["name"].'</option>';	
		}
		return $categoryHTML;
	}
	public function saveBrand() {		
		$sqlInsert = "
			SELECT bname FROM ".$this->brandTable." WHERE bname = '".$_POST['bname']."'";		
			$result = mysqli_query($this->dbConnect, $sqlInsert);

			if($result->num_rows > 0) {
				echo '0';
		}else{
			$sqlInsert = "
			INSERT INTO ".$this->brandTable."(categoryid, bname) 
			VALUES ('".$_POST["categoryid"]."', '".$_POST['bname']."')";		
			mysqli_query($this->dbConnect, $sqlInsert);
			echo '1';
		}
		
	}	
	public function getBrand(){
		$sqlQuery = "
			SELECT * FROM ".$this->brandTable." 
			WHERE id = '".$_POST["id"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}	
	public function updateBrand() {		
		$sqlInsert = "
			SELECT bname FROM ".$this->brandTable." WHERE bname = '".$_POST['bname']."' AND id != '".$_POST['id']."'";		
			$result = mysqli_query($this->dbConnect, $sqlInsert);

		if($result->num_rows > 0) {
			echo '0';
		}else{
			if($_POST['id']) {	
				$sqlUpdate = "UPDATE ".$this->brandTable." SET bname = '".$_POST['bname']."', categoryid='".$_POST['categoryid']."' WHERE id = '".$_POST["id"]."'";
				mysqli_query($this->dbConnect, $sqlUpdate);	
				echo '1';
			}
		}
	}	
	public function deleteBrand(){
		$sqlQuery = "
			DELETE FROM ".$this->brandTable." 
			WHERE id = '".$_POST["id"]."'";	
		mysqli_query($this->dbConnect, $sqlQuery);		
	}
	// Product management 
	public function getProductList(){				
		$sqlQuery = "SELECT p.*, b.bname, c.name as category_name, s.supplier_name 
					 FROM ".$this->productTable." as p
					 INNER JOIN ".$this->brandTable." as b ON b.id = p.brandid
					 INNER JOIN ".$this->categoryTable." as c ON c.categoryid = p.categoryid 
					 INNER JOIN ".$this->supplierTable." as s ON s.supplier_id = p.supplier ";
		if(isset($_POST["search"]["value"])) {
			$sqlQuery .= 'WHERE b.bname LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR c.name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR p.pname LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR p.quantity LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR s.supplier_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= 'OR p.pid LIKE "%'.$_POST["search"]["value"].'%" ';
		}
		if(isset($_POST['order'])) {
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY p.pid DESC ';
		}
		if($_POST['length'] != -1) {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}		
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$productData = array();	
		$increment = 1;
		while( $product = mysqli_fetch_assoc($result) ) {			

			// Fetch parts replaced
			$sqlPartsQuery = "
				SELECT p.pname 
				FROM ".$this->replacedTable." as r
				INNER JOIN ".$this->productTable." as p ON r.part_pid = p.pid
				WHERE r.phone_pid = '".$product['pid']."'";
			$partsResult = mysqli_query($this->dbConnect, $sqlPartsQuery);
			$partsReplaced = [];
			while ($part = mysqli_fetch_assoc($partsResult)) {
				$partsReplaced[] = $part['pname'];
			}
			$partsReplacedHtml = !empty($partsReplaced) ? implode(', ', $partsReplaced) : 'None';

			$productRow = array();
			$productRow[] = $increment++;
			$productRow[] = $product['category_name'];
			$productRow[] = $product['bname'];
			$productRow[] = '<img src="img/'.$product['image'].'" alt="'.$product['pname'].'" style="width:50px;height:50px;">';
			$productRow[] = $product['pname'];	
			$productRow[] = $product["quantity"];
			$productRow[] = $product["base_price"];
			$productRow[] = $product["selling_price"];
			$productRow[] = $product['supplier_name'];
			$productRow[] = $partsReplacedHtml; // Add parts replaced column
			$productRow[] = '<div class="btn-group btn-group-sm"><button type="button" name="view" id="'.$product["pid"].'" class="btn btn-light bg-gradient border text-dark btn-sm rounded-0  view" title="View"><i class="fa fa-eye"></i></button><button type="button" name="update" id="'.$product["pid"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$product["pid"].'" class="btn btn-danger btn-sm rounded-0  delete" title="Delete"><i class="fa fa-trash"></i></button></div>';
			$productData[] = $productRow;
		}
		$outputData = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$productData
		);
		echo json_encode($outputData);
	}
	public function getCategoryBrand($categoryid){	
		$sqlQuery = "SELECT * FROM ".$this->brandTable." 
			WHERE status = 'active' AND categoryid = '".$categoryid."'	ORDER BY bname ASC";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $brand = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$brand["id"].'">'.$brand["bname"].'</option>';
		}
		return $dropdownHTML;
	}
	public function supplierDropdownList(){	
		$sqlQuery = "SELECT * FROM ".$this->supplierTable."";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $supplier = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$supplier["supplier_id"].'">'.$supplier["supplier_name"].':</option>';
		}
		return $dropdownHTML;
	}
	public function addProduct() {	
		$array = null;
					//checking if the parts have enough stock
					if (!empty($_POST['selected_parts'])) {
						$array = $_POST['selected_parts'];
						$zeroQuantityParts = []; // Initialize an array to collect parts with zero quantity
					
						if ($array != null) {
							foreach ($array as $partId) {
								$sqlcheck = "SELECT pname FROM ".$this->productTable."
											 WHERE pid = ".$partId."
											 AND quantity <= 0";
								$result = mysqli_query($this->dbConnect, $sqlcheck);
								$row = $result->fetch_assoc();
								if ($row) {
									$zeroQuantityParts[] = $row['pname']; // Add part name to the array
								}
							}
					
							if (!empty($zeroQuantityParts)) {
								echo json_encode($zeroQuantityParts); // Convert the array to a JSON string before echoing
								return;
							}
						}
					}
		
		if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
			$imageName = $_FILES['product_image']['name'];
			$imageTmpName = $_FILES['product_image']['tmp_name'];
			$imageDestination = 'img/' . $imageName;
			move_uploaded_file($imageTmpName, $imageDestination);
		} else {
			$imageName = '';
		}

		$sqlInsert = "
        SELECT * FROM ".$this->productTable."
		WHERE pname = '".$_POST['pname']."'";		
		$result = mysqli_query($this->dbConnect, $sqlInsert);

		if(mysqli_num_rows($result) > 0) {
			echo '0';
		}elseif($_POST['base_price'] >= $_POST['selling_price']){
			echo '2';
		}elseif(mysqli_num_rows($result) <= 0){

			$sqlInsert = "
				INSERT INTO ".$this->productTable."(categoryid, brandid, pname, description, quantity, base_price, selling_price,image, minimum_order, supplier) 
				VALUES ('".$_POST["categoryid"]."', '".$_POST['brandid']."', '".$_POST['pname']."', '".$_POST['description']."', '".$_POST['quantity']."', '".$_POST['base_price']."', '".$_POST['selling_price']."', '".$imageName."', 1, '".$_POST['supplierid']."')";		
			mysqli_query($this->dbConnect, $sqlInsert);
			$productId = mysqli_insert_id($this->dbConnect); // Get the last inserted product ID
		
			// Insert selected parts
			if (!empty($_POST['selected_parts'])) {
				foreach ($_POST['selected_parts'] as $partId) {
					$sqlInsertPart = "
						INSERT INTO ".$this->replacedTable."(phone_pid, part_pid, quantity) 
						VALUES ('".$productId."', '".$partId."', '1')"; // Assuming quantity is 1 for each part
					mysqli_query($this->dbConnect, $sqlInsertPart);

					$sqlUpdate = "
					UPDATE ".$this->productTable."
					SET quantity = quantity - 1
					WHERE pid = '".$partId."'";
					mysqli_query($this->dbConnect, $sqlUpdate);
				}
			}
			echo '1';
    	}

}	
	public function getProductDetails(){
		$sqlQuery = "
			SELECT * FROM ".$this->productTable." 
			WHERE pid = '".$_POST["pid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);			
		while( $product = mysqli_fetch_assoc($result)) {
			$output['pid'] = $product['pid'];
			$output['categoryid'] = $product['categoryid'];
			$output['brandid'] = $product['brandid'];
			$output["brand_select_box"] = $this->getCategoryBrand($product['categoryid']);
			$output[] = '<img src="img/'.$product['image'].'" alt="'.$product['pname'].'" style="width:50px;height:50px;">';
			$output['pname'] = $product['pname'];
			$output['description'] = $product['description'];
			$output['quantity'] = $product['quantity'];
			$output['base_price'] = $product['base_price'];
			$output['selling_price'] = $product['selling_price'];
			$output['image'] = $product['image'];
			$output['supplier'] = $product['supplier'];
		}

		// Fetch parts replaced
		$sqlPartsQuery = "
			SELECT r.part_pid, p.pname 
			FROM ".$this->replacedTable." as r
			INNER JOIN ".$this->productTable." as p ON r.part_pid = p.pid
			WHERE r.phone_pid = '".$_POST["pid"]."'";
		$partsResult = mysqli_query($this->dbConnect, $sqlPartsQuery);
		$partsReplaced = [];
		while ($part = mysqli_fetch_assoc($partsResult)) {
			$partsReplaced[] = $part;
		}
		$output['parts_replaced'] = $partsReplaced;

		echo json_encode($output);
	}
	public function updateProduct() {		


		$sqlInsert = "
        SELECT * FROM ".$this->productTable."
		WHERE pname = '".$_POST['pname']."' AND pid != '".$_POST['pid']."'";		
		$result = mysqli_query($this->dbConnect, $sqlInsert);
		if(mysqli_num_rows($result) > 0) {
			echo '0';
		}elseif($_POST['base_price'] >= $_POST['selling_price']){
			echo '2';
		}elseif(mysqli_num_rows($result) <= 0){
				if($_POST['pid']) {	
					$array = null;
					//checking if the parts have enough stock
					if (!empty($_POST['selected_parts'])) {
						$array = $_POST['selected_parts'];
						$zeroQuantityParts = []; // Initialize an array to collect parts with zero quantity
					
						if ($array != null) {
							foreach ($array as $partId) {
								$sqlcheck = "SELECT pname FROM ".$this->productTable."
											 WHERE pid = ".$partId."
											 AND quantity <= 0";
								$result = mysqli_query($this->dbConnect, $sqlcheck);
								$row = $result->fetch_assoc();
								if ($row) {
									$zeroQuantityParts[] = $row['pname']; // Add part name to the array
								}
							}
					
							if (!empty($zeroQuantityParts)) {
								echo json_encode($zeroQuantityParts); // Convert the array to a JSON string before echoing
								return;
							}
						}
					}

					if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
						$imageName = $_FILES['product_image']['name'];
						$imageTmpName = $_FILES['product_image']['tmp_name'];
						$imageDestination = 'img/' . $imageName;
						move_uploaded_file($imageTmpName, $imageDestination);
					} else {
						$imageName = $_POST['existing_image'];
					}

					$sqlUpdate = "UPDATE ".$this->productTable." 
					SET categoryid = '".$_POST['categoryid']."', brandid='".$_POST['brandid']."', image='".$imageName."', pname='".$_POST['pname']."', description='".$_POST['description']."', quantity='".$_POST['quantity']."',  base_price='".$_POST['base_price']."',  selling_price='".$_POST['selling_price']."', supplier='".$_POST['supplierid']."' WHERE pid = '".$_POST["pid"]."'";			
					mysqli_query($this->dbConnect, $sqlUpdate);

					//adding 1 each quantity before deleting
					$sqlgetparts = "SELECT part_pid FROM ".$this->replacedTable." WHERE phone_pid = '".$_POST["pid"]."'";
					$result = mysqli_query($this->dbConnect, $sqlgetparts);
					$array = $result->fetch_assoc();
					if($array != null){
						foreach ($array as $partId) {
							$sqlUpdate = "
							UPDATE ".$this->productTable."
							SET quantity = quantity + 1
							WHERE pid = '".$partId."'";
							mysqli_query($this->dbConnect, $sqlUpdate);
						}
					}
				
					
					// Remove existing replaced parts
					$sqlDeleteParts = "DELETE FROM ".$this->replacedTable." WHERE phone_pid = '".$_POST["pid"]."'";
					mysqli_query($this->dbConnect, $sqlDeleteParts);

					// Insert updated replaced parts
					if (!empty($_POST['selected_parts'])) {
						foreach ($_POST['selected_parts'] as $partId) {
							$sqlInsertPart = "
								INSERT INTO ".$this->replacedTable."(phone_pid, part_pid, quantity) 
								VALUES ('".$_POST["pid"]."', '".$partId."', '1')"; // Assuming quantity is 1 for each part
							mysqli_query($this->dbConnect, $sqlInsertPart);

							$sqlUpdate = "
							UPDATE ".$this->productTable."
							SET quantity = quantity - 1
							WHERE pid = '".$partId."'";
							mysqli_query($this->dbConnect, $sqlUpdate);
						}
					}
					echo '1';
				}
	        }	
		
    }	 
	public function deleteProduct(){

		//adding 1 each quantity before deleting
		$sqlgetparts = "SELECT part_pid FROM ".$this->replacedTable." WHERE phone_pid = '".$_POST["pid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlgetparts);
		$array = $result->fetch_assoc();
		if($array != null){
			foreach ($array as $partId) {
				$sqlUpdate = "
				UPDATE ".$this->productTable."
				SET quantity = quantity + 1
				WHERE pid = '".$partId."'";
				mysqli_query($this->dbConnect, $sqlUpdate);
			}
		}

		$sqlQuery = "
			DELETE FROM ".$this->productTable." 
			WHERE pid = '".$_POST["pid"]."'";	
		mysqli_query($this->dbConnect, $sqlQuery);		
	}	
	public function viewProductDetails(){
		$sqlQuery = "SELECT * FROM ".$this->productTable." as p
			INNER JOIN ".$this->brandTable." as b ON b.id = p.brandid
			INNER JOIN ".$this->categoryTable." as c ON c.categoryid = p.categoryid 
			INNER JOIN ".$this->supplierTable." as s ON s.supplier_id = p.supplier 
			WHERE p.pid = '".$_POST["pid"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$productDetails = '<div class="table-responsive">
				<table class="table table-bordered">';
		while( $product = mysqli_fetch_assoc($result) ) {
			$status = '';
			if($product['status'] == 'active') {
				$status = '<span class="label label-success">Active</span>';
			} else {
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$productDetails .= '
			<tr>
				<td>Product Name</td>
				<td>'.$product["pname"].'</td>
			</tr>
			<tr>
				<td>Product Description</td>
				<td>'.$product["description"].'</td>
			</tr>
			<tr>
				<td>Category</td>
				<td>'.$product["name"].'</td>
			</tr>
			<tr>
				<td>Brand</td>
				<td>'.$product["bname"].'</td>
			</tr>			
			<tr>
				<td>Available Quantity</td>
				<td>'.$product["quantity"].'</td>
			</tr>
			<tr>
				<td>Base Price</td>
				<td>'.$product["base_price"].'</td>
			</tr>
			<tr>
				<td>Enter By</td>
				<td>'.$product["supplier_name"].'</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>';

			// Fetch and display replaced parts
			$sqlPartsQuery = "
				SELECT p.pname 
				FROM ".$this->replacedTable." as r
				INNER JOIN ".$this->productTable." as p ON r.part_pid = p.pid
				WHERE r.phone_pid = '".$product['pid']."'";
			$partsResult = mysqli_query($this->dbConnect, $sqlPartsQuery);
			$partsReplaced = [];
			while ($part = mysqli_fetch_assoc($partsResult)) {
				$partsReplaced[] = $part['pname'];
			}
			$partsReplacedHtml = !empty($partsReplaced) ? implode(', ', $partsReplaced) : 'None';

			$productDetails .= '
			<tr>
				<td>Parts Replaced</td>
				<td>'.$partsReplacedHtml.'</td>
			</tr>';
		}
		$productDetails .= '
			</table>
		</div>';
		echo $productDetails;
	}
	// supplier 
	public function getSupplierList(){		
		$sqlQuery = "SELECT * FROM ".$this->supplierTable." ";
		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= 'WHERE (supplier_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= '(address LIKE "%'.$_POST["search"]["value"].'%" ';			
		}
		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY supplier_id DESC ';
		}
		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}	
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$supplierData = array();	
		$increment = 1;
		while( $supplier = mysqli_fetch_assoc($result) ) {	
			$supplierRows = array();
			$supplierRows[] = $increment++;
			$supplierRows[] = $supplier['supplier_name'];	
			$supplierRows[] = $supplier['mobile'];			
			$supplierRows[] = $supplier['address'];	
			$supplierRows[] = '<div class="btn-group btn-group-sm"><button type="button" name="update" id="'.$supplier["supplier_id"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$supplier["supplier_id"].'" class="btn btn-danger btn-sm rounded-0  delete"  title="Delete"><i class="fa fa-trash"></i></button></div>';
			$supplierData[] = $supplierRows;
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$supplierData
		);
		echo json_encode($output);
	}
	public function addSupplier() {	
		$sqlInsert = "
			SELECT * FROM ".$this->supplierTable."
			WHERE supplier_name = '".$_POST['supplier_name']."'";		
		$result = mysqli_query($this->dbConnect, $sqlInsert);

		$sqlInsert2 = "
			SELECT * FROM ".$this->supplierTable."
			WHERE mobile = '".$_POST['mobile']."'";		
		$result2 = mysqli_query($this->dbConnect, $sqlInsert2);

		if(mysqli_num_rows($result) > 0) {
			echo '0';
		}
		else if(mysqli_num_rows($result2) > 0){
			echo '1';
		}
		
		if(mysqli_num_rows($result) <= 0 && mysqli_num_rows($result2) <= 0){
			$sqlInsert = "
				INSERT INTO ".$this->supplierTable."(supplier_name, mobile, address) 
				VALUES ('".$_POST['supplier_name']."', '".$_POST['mobile']."', '".$_POST['address']."')";		
			mysqli_query($this->dbConnect, $sqlInsert);
			echo '2';
		}
	
	}			
	public function getSupplier(){
		$sqlQuery = "
			SELECT * FROM ".$this->supplierTable." 
			WHERE supplier_id = '".$_POST["supplier_id"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function updateSupplier() {
		if($_POST['supplier_id']) {	
			$sqlInsert = "
			SELECT * FROM ".$this->supplierTable."
			WHERE supplier_name = '".$_POST['supplier_name']."'
			AND supplier_id != '".$_POST['supplier_id']."'";		
		$result = mysqli_query($this->dbConnect, $sqlInsert);

		$sqlInsert2 = "
			SELECT * FROM ".$this->supplierTable."
			WHERE mobile = '".$_POST['mobile']."'
			AND supplier_id != '".$_POST['supplier_id']."'";		
		$result2 = mysqli_query($this->dbConnect, $sqlInsert2);

		if(mysqli_num_rows($result) > 0) {
			echo '0';
		}
		else if(mysqli_num_rows($result2) > 0){
			echo '1';
		}
		
		if(mysqli_num_rows($result) <= 0 && mysqli_num_rows($result2) <= 0){
			$sqlUpdate = "
				UPDATE ".$this->supplierTable." 
				SET supplier_name = '".$_POST['supplier_name']."', mobile= '".$_POST['mobile']."' , address= '".$_POST['address']."'	WHERE supplier_id = '".$_POST['supplier_id']."'";		
			mysqli_query($this->dbConnect, $sqlUpdate);	
			echo '2';

		}
		}	
	}	
	public function deleteSupplier(){
		$sqlQuery = "
			DELETE FROM ".$this->supplierTable." 
			WHERE supplier_id = '".$_POST['supplier_id']."'";		
		mysqli_query($this->dbConnect, $sqlQuery);		
	}
	// purchase
	public function listPurchase(){		
		$sqlQuery = "SELECT ph.*, p.pname, s.supplier_name FROM ".$this->purchaseTable." as ph
			INNER JOIN ".$this->productTable." as p ON p.pid = ph.product_id 
			INNER JOIN ".$this->supplierTable." as s ON s.supplier_id = ph.supplier_id ";
		if(isset($_POST['order'])) {
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY ph.purchase_id DESC ';
		}
		if($_POST['length'] != -1) {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}		
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$purchaseData = array();	
		$increment = 1;
		while( $purchase = mysqli_fetch_assoc($result) ) {			
			$productRow = array();
			$productRow[] = $increment++;
			$productRow[] = $purchase['pname'];
			$productRow[] = $purchase['quantity'];			
			$productRow[] = $purchase['supplier_name'];			
			$productRow[] = '<div class="btn-group btn-group-sm"><button type="button" name="update" id="'.$purchase["purchase_id"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$purchase["purchase_id"].'" class="btn btn-danger btn-sm rounded-0  delete" title="Delete"><i class="fa fa-trash"></i></button></div>';
			$purchaseData[] = $productRow;
						
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$purchaseData
		);
		echo json_encode($output);		
	}
	public function productDropdownList(){	


		$sqlQuery = "SELECT * FROM ".$this->productTable." ORDER BY pname ASC";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $product = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$product["pid"].'">'.$product["pname"].' ('.$product["quantity"].' in stock)</option>';
		}
		return $dropdownHTML;
	}
	public function addPurchase() {
		// Fetch the current quantity
		$productId = $_POST['product'];
		$supplierId = $_POST['supplierid'];
		$newQuantity = $_POST['quantity'];
	
		// Step 1: Get the current quantity
		$stmt = $this->dbConnect->prepare("SELECT quantity FROM " . $this->productTable . " WHERE pid = ?");
		$stmt->bind_param("i", $productId); // 'i' for integer
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		$stmt->close(); // Close the statement
	
		// Calculate the total quantity
		$oldQuantity = $row['quantity'] ?? 0; // Default to 0 if no record found
		$totalQuantity = $oldQuantity + $newQuantity;
	
		// Step 2: Update the product quantity
		$sqlUpdate = "UPDATE " . $this->productTable . " SET quantity = ? WHERE pid = ?";
		$stmt = $this->dbConnect->prepare($sqlUpdate);
		$stmt->bind_param("ii", $totalQuantity, $productId); // Two integers
		$stmt->execute();
	
		// Check if the update was successful
		if ($stmt->affected_rows > 0) {
			echo "Product quantity updated successfully.";
		} else {
			echo "Failed to update product quantity or no changes were made.";
		}
		$stmt->close(); // Close the statement
	
		// Step 3: Insert a new record into the purchase table
		$sqlInsert = "INSERT INTO " . $this->purchaseTable . " (product_id, quantity, supplier_id) VALUES (?, ?, ?)";
		$stmt = $this->dbConnect->prepare($sqlInsert);
		$stmt->bind_param("iii", $productId, $newQuantity, $supplierId); // Three integers
		$stmt->execute();
		$stmt->close(); // Close the statement
	
		echo 'New Purchase Added';
	}
		public function getPurchaseDetails(){
		$sqlQuery = "
			SELECT * FROM ".$this->purchaseTable." 
			WHERE purchase_id = '".$_POST["purchase_id"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function updatePurchase() {
		if($_POST['purchase_id']) {	
			$sqlUpdate = "
				UPDATE ".$this->purchaseTable." 
				SET product_id = '".$_POST['product']."', quantity= '".$_POST['quantity']."' , supplier_id= '".$_POST['supplierid']."'	WHERE purchase_id = '".$_POST['purchase_id']."'";		
			mysqli_query($this->dbConnect, $sqlUpdate);	
			echo 'Purchase Edited';
		}	
	}	
	public function deletePurchase(){
		$sqlQuery = "DELETE FROM " . $this->purchaseTable . " WHERE purchase_id = ?";
		$stmt = $this->dbConnect->prepare($sqlQuery);
		$stmt->bind_param("i", $_POST['purchase_id']);  // 'i' for integer type
		$stmt->execute();

		// Check if the deletion was successful
		if ($stmt->affected_rows > 0) {
			echo "Purchase record deleted successfully.";
		} else {
			echo "No record found to delete or error in deletion.";
		}

		stmt->close();

	}
	// order
	public function listOrders(){		
    $sqlQuery = "SELECT o.*, c.name as customer_name, p.pname as product_name 
                 FROM ".$this->orderTable." as o
                 INNER JOIN ".$this->customerTable." as c ON c.id = o.customer_id
                 INNER JOIN ".$this->productTable." as p ON p.pid = o.product_id ";		
    if(isset($_POST['order'])) {
        $sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
    } else {
        $sqlQuery .= 'ORDER BY o.order_id DESC ';
    }
    if($_POST['length'] != -1) {
        $sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }		
    $result = mysqli_query($this->dbConnect, $sqlQuery);
    $numRows = mysqli_num_rows($result);
    $orderData = array();	
    $increment = 1;
    while( $order = mysqli_fetch_assoc($result) ) {			
        $orderRow = array();
        $orderRow[] = $increment++;
        $orderRow[] = $order['product_name'];
        $orderRow[] = $order['total_sell'];	
        $orderRow[] = $order['customer_name'];
        $orderRow[] = $order['order_date']; // Add order date to the data array
        $orderRow[] = '<div class="btn-group btn-group-sm"><button type="button" name="update" id="'.$order["order_id"].'" class="btn btn-primary btn-sm rounded-0  update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$order["order_id"].'" class="btn btn-danger btn-sm rounded-0  delete" title="Delete"><i class="fa fa-trash"></i></button></div>';
        $orderData[] = $orderRow;
    }
    $output = array(
        "draw"				=>	intval($_POST["draw"]),
        "recordsTotal"  	=>  $numRows,
        "recordsFiltered" 	=> 	$numRows,
        "data"    			=> 	$orderData
    );
    echo json_encode($output);		
}
public function addOrder() {
    // Check if there is enough stock
    $sqlQuery = "SELECT quantity FROM ".$this->productTable." WHERE pid = '".$_POST['product']."'";
    $result = mysqli_query($this->dbConnect, $sqlQuery);
    $product = mysqli_fetch_assoc($result);

    if ($product['quantity'] < $_POST['sold']) {
        echo '0';
        return;
    }

    $sqlInsert = "
        INSERT INTO ".$this->orderTable."(product_id, total_sell, customer_id) 
        VALUES ('".$_POST['product']."', '".$_POST['sold']."', '".$_POST['customer']."')";
    mysqli_query($this->dbConnect, $sqlInsert);

    $sqlUpdate = "
        UPDATE ".$this->productTable."
        SET quantity = quantity - '".$_POST['sold']."'
        WHERE pid = '".$_POST['product']."'";
    mysqli_query($this->dbConnect, $sqlUpdate);

    echo '1';
}		
	public function getOrderDetails(){
		$sqlQuery = "
			SELECT * FROM ".$this->orderTable." 
			WHERE order_id = '".$_POST["order_id"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}
	public function updateOrder() {
    $sqlQuery = "
        SELECT * FROM ".$this->orderTable." 
        WHERE order_id = '".$_POST["order_id"]."'";
    $result = mysqli_query($this->dbConnect, $sqlQuery);	
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    $old_name = $row['product_id'];
    $new_name = $_POST['product'];

    if ($old_name != $new_name) {
        $sqlUpdate = "
        UPDATE ".$this->productTable."
        SET quantity = quantity + '".$row['total_sell']."'
        WHERE pid = '".$old_name."'";
        mysqli_query($this->dbConnect, $sqlUpdate);

        $sqlUpdate = "
        UPDATE ".$this->productTable."
        SET quantity = quantity - '".$row['total_sell']."'
        WHERE pid = '".$new_name."'";
        mysqli_query($this->dbConnect, $sqlUpdate);
    }

    $old_order = $row['total_sell'];
    $update_order = $_POST['sold'];
    $order = 0;

    if ($update_order > $old_order) {
        $order = $update_order - $old_order;

        // Check if there is enough stock
        $sqlQuery = "SELECT quantity FROM ".$this->productTable." WHERE pid = '".$_POST['product']."'";
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        $product = mysqli_fetch_assoc($result);

        if ($product['quantity'] < $order) {
            echo '0';
            return;
        }

        $sqlUpdate = "
        UPDATE ".$this->productTable."
        SET quantity = quantity - '".$order."'
        WHERE pid = '".$_POST['product']."'";
        mysqli_query($this->dbConnect, $sqlUpdate);

    } else {
        $order = $old_order - $update_order;

        $sqlUpdate = "
        UPDATE ".$this->productTable."
        SET quantity = quantity + '".$order."'
        WHERE pid = '".$_POST['product']."'";
        mysqli_query($this->dbConnect, $sqlUpdate);
    }

    if ($_POST['order_id']) {	
        $sqlUpdate = "
            UPDATE ".$this->orderTable." 
            SET product_id = '".$_POST['product']."', total_sell='".$_POST['sold']."', customer_id='".$_POST['customer']."' WHERE order_id = '".$_POST['order_id']."'";		
        mysqli_query($this->dbConnect, $sqlUpdate);	

        echo '1';
    }	
}
	public function deleteOrder(){
        // Fetch the order details to get the product ID and quantity sold
        $sqlQuery = "
            SELECT product_id, total_sell 
            FROM ".$this->orderTable." 
            WHERE order_id = '".$_POST['order_id']."'";
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        $order = mysqli_fetch_assoc($result);

        // Update the product quantity
        $sqlUpdate = "
            UPDATE ".$this->productTable."
            SET quantity = quantity + '".$order['total_sell']."'
            WHERE pid = '".$order['product_id']."'";
        mysqli_query($this->dbConnect, $sqlUpdate);

        // Delete the order
        $sqlQuery = "
            DELETE FROM ".$this->orderTable." 
            WHERE order_id = '".$_POST['order_id']."'";
        mysqli_query($this->dbConnect, $sqlQuery);
    }
	public function customerDropdownList(){	
		$sqlQuery = "SELECT * FROM ".$this->customerTable." ORDER BY name ASC";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $customer = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$customer["id"].'">'.$customer["name"].'</option>';
		}
		return $dropdownHTML;
	}
	// Revenue
	public function getRevenueData() {
		if ($_POST['action'] == 'getRevenueData') {
			$sqlQuery = "SELECT p.pid AS id, p.pname AS product, p.price,
						SUM(o.quantity) AS pcs_sold, 
						SUM(o.quantity * p.price) AS sales,
						(SUM(o.quantity * p.price) - SUM(o.cost_price * o.quantity)) AS profit
						FROM ".$this->productTable." p
						LEFT JOIN ".$this->orderTable." o ON p.pid = o.product_id
						GROUP BY p.pid;";
		
			if (isset($_POST['order'])) {
				$sqlQuery .= ' ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST['order']['0']['dir'];
			} else {
				$sqlQuery .= ' ORDER BY id ASC';
			}
		
			if ($_POST['length'] != -1) {
				$sqlQuery .= ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
			}
		
			$result = mysqli_query($this->dbConnect, $sqlQuery);
			if (!$result) {
				die('Query failed: ' . mysqli_error($this->dbConnect));
			}
			$numRows = mysqli_num_rows($result);
		
			$data = [];
			while ($row = mysqli_fetch_assoc($result)) {
				$data[] = $row;
			}
		
			$output = array(
				"draw" => intval($_POST["draw"]),
				"recordsTotal" => $numRows,
				"recordsFiltered" => $numRows,
				"data" => !empty($data) ? $data : []
			);			
			
			echo json_encode($output);
			exit;

		}		
	}
	public function getInventoryDetails(){		
		$sqlQuery = "SELECT p.pid, p.pname, p.quantity as product_quantity, p.selling_price,p.base_price,
						(SELECT SUM(s.quantity) FROM ".$this->purchaseTable." as s WHERE s.product_id = p.pid) as recieved_quantity, 
						(SELECT COALESCE(SUM(r.total_sell), 0) FROM ".$this->orderTable." as r WHERE r.product_id = p.pid) as total_sell,
						(SELECT COALESCE(SUM(rp.quantity), 0) FROM ".$this->replacedTable." as rp WHERE rp.part_pid = p.pid) as total_replaced
					FROM ".$this->productTable." as p
					GROUP BY p.pid"; // Group by product ID to sum quantities
					
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$inventoryData = array();	
		$i = 1;
		while( $inventory = mysqli_fetch_assoc($result) ) {	
			if(!$inventory['recieved_quantity']) {
				$inventory['recieved_quantity'] = 0;
			}
			$totalSell = $inventory['total_sell'] + $inventory['total_replaced'];
			$revenue = $totalSell * $inventory['selling_price'];
			$based_price = $inventory['base_price'] * $totalSell;
			if($totalSell > 0){
				$income = $revenue - $based_price;
			}else{
				$income = 0;
			}
			$inventoryRow = array();
			$inventoryRow[] = $i++;
			$inventoryRow[] = "<div class='lh-1'><div>{$inventory['pname']}</div><div class='fw-bolder text-muted'</div></div>";
			$inventoryRow[] = $inventory['product_quantity'];
			$inventoryRow[] = $totalSell;
			$inventoryRow[] = $inventory['base_price'];
			$inventoryRow[] = $inventory['selling_price'];
			$inventoryRow[] = $revenue;
			$inventoryRow[] = $income;
			$inventoryData[] = $inventoryRow;						
		}
		$output = array(
			"draw"				=>	intval($_POST["draw"]),
			"recordsTotal"  	=>  $numRows,
			"recordsFiltered" 	=> 	$numRows,
			"data"    			=> 	$inventoryData
		);
		echo json_encode($output);		
	}
	
	
	
	// REplaced 
	public function phoneDropdownList(){	
		$sqlQuery = "SELECT * FROM ".$this->productTable. " as p 
							JOIN ".$this->categoryTable ." as c ON c.categoryid = p.categoryid
							Where c.name = 'Phone'
							ORDER BY pname ASC";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $product = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$product["pid"].'">'.$product["pname"].'</option>';
		}
		return $dropdownHTML;
	}
	
	public function partsDropdownList(){	
		$sqlQuery = "SELECT * FROM ".$this->productTable. " as p 
							JOIN ".$this->categoryTable ." as c ON c.categoryid = p.categoryid
							Where c.name = 'Parts'
							ORDER BY pname ASC";
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$dropdownHTML = '';
		while( $product = mysqli_fetch_assoc($result) ) {	
			$dropdownHTML .= '<option value="'.$product["pid"].'">'.$product["pname"]." (".$product["quantity"] .') </option>';
		}
		return $dropdownHTML;
	}
	
	public function addReplaced() {
		// Query to get the quantity of the product
		$sqlSelect = "
			SELECT quantity FROM ".$this->productTable." WHERE pid = '".$_POST['part']."' LIMIT 1";		
		$result = mysqli_query($this->dbConnect, $sqlSelect);
		
		if ($result) {
			$row = mysqli_fetch_assoc($result);
			$quantity = $row['quantity'];
			
			// Check if the quantity is sufficient before inserting the replacement
			if ($quantity >= $_POST['quantity']) {
				$sqlInsert = "
					INSERT INTO ".$this->replacedTable."(phone_pid, part_pid, quantity) 
					VALUES ('".$_POST['phone']."', '".$_POST['part']."', '".$_POST['quantity']."')";		
				if (mysqli_query($this->dbConnect, $sqlInsert)) {
					// Subtract the quantity from the product table
					$sqlUpdate = "
						UPDATE ".$this->productTable."
						SET quantity = quantity - '".$_POST['quantity']."'
						WHERE pid = '".$_POST['part']."'";
					mysqli_query($this->dbConnect, $sqlUpdate);
					echo '1';
				}
			} else {
				echo '0';
			}
		} 
	}
		

	
	public function listReplaced() {
		$sqlQuery = "SELECT rt.replacement_id, 
							rt.phone_pid, 
							rt.part_pid, 
							rt.quantity, 
							p1.pname AS phone_name, 
							p2.pname AS part_name
					 FROM ".$this->replacedTable." as rt
					 INNER JOIN ".$this->productTable." as p1 ON p1.pid = rt.phone_pid
					 INNER JOIN ".$this->productTable." as p2 ON p2.pid = rt.part_pid"; // Join for both phone and part
		
		// Apply ordering if set
		if (isset($_POST['order'])) {
			$sqlQuery .= ' ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST['order']['0']['dir'] . ' ';
		} else {
			$sqlQuery .= ' ORDER BY p1.pname DESC ';
		}
		
		// Apply pagination if set
		if ($_POST['length'] != -1) {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
		
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$replacedData = array();
		$num = 1;
		while ($purchase = mysqli_fetch_assoc($result)) {
			// Format each row based on the fields in your table
			$replacedRow = array();
			$replacedRow[] = $num; // Replacement ID
			$replacedRow[] = $purchase['phone_name'];    // Phone name
			$replacedRow[] = $purchase['part_name'];     // Part name
			$replacedRow[] = $purchase['quantity'];      // Quantity of parts replaced
			$replacedRow[] = '<div class="btn-group btn-group-sm">
								 <button type="button" name="update" id="' . $purchase["replacement_id"] . '" 
										 class="btn btn-primary btn-sm rounded-0 update" title="Update">
									 <i class="fa fa-edit"></i>
								 </button>
								 <button type="button" name="delete" id="' . $purchase["replacement_id"] . '" 
										 class="btn btn-danger btn-sm rounded-0 delete" title="Delete">
									 <i class="fa fa-trash"></i>
								 </button>
							  </div>'; // Action buttons
			$num++;
			$replacedData[] = $replacedRow;
		}
	
		// Output the data as a JSON response
		$output = array(
			"draw" => intval($_POST["draw"]),
			"recordsTotal" => $numRows,
			"recordsFiltered" => $numRows, // If filtering is applied, adjust accordingly
			"data" => $replacedData
		);
	
		echo json_encode($output); // Return the JSON response
	}
	
	
	public function deleteReplaced(){
		$sqlQuery2 = "SELECT quantity, 	part_pid 
					FROM ".$this->replacedTable." 
					WHERE replacement_id = '".$_POST["replaced_id"]."'";
				$result = 	mysqli_query($this->dbConnect, $sqlQuery2);
				$row = mysqli_fetch_assoc($result);
				$quantity = $row['quantity'];
				$part_id = $row['part_pid'];

		$sqlQuery = "
			DELETE FROM ".$this->replacedTable." 
			WHERE replacement_id = '".$_POST["replaced_id"]."'";	
		if(mysqli_query($this->dbConnect, $sqlQuery)){
		
			$sqlUpdate = "
				UPDATE ".$this->productTable." 
				SET quantity = quantity + '". $quantity ."'
				WHERE pid = '". $part_id ."'";
			if(mysqli_query($this->dbConnect, $sqlUpdate)){
				echo '1';
			}else{
				echo '0';
			}

		}		

	}

	public function getReplacedDetails(){
		$sqlQuery = "
			SELECT * FROM ".$this->replacedTable." 
			WHERE replacement_id  = '".$_POST["replaced_id"]."'";
		$result = mysqli_query($this->dbConnect, $sqlQuery);	
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		echo json_encode($row);
	}


	public function updateReplaced() {
		if ($_POST['replacement_id']) {
			// Fetch the current quantity of the part
			$sqlSelectPart = "
				SELECT quantity 
				FROM ".$this->productTable." 
				WHERE pid = '".$_POST['part']."' 
				LIMIT 1";
			$resultPart = mysqli_query($this->dbConnect, $sqlSelectPart);
			$partRow = mysqli_fetch_assoc($resultPart);
			$partQuantity = $partRow['quantity'];
	
			// Fetch the current quantity of the replacement
			$sqlSelectReplacement = "
				SELECT quantity 
				FROM ".$this->replacedTable." 
				WHERE replacement_id = '".$_POST['replacement_id']."' 
				LIMIT 1";
			$resultReplacement = mysqli_query($this->dbConnect, $sqlSelectReplacement);
			$replacementRow = mysqli_fetch_assoc($resultReplacement);
			$currentReplacementQuantity = $replacementRow['quantity'];
	
			// Calculate the difference
			$newReplacementQuantity = $_POST['quantity'];
			$quantityDifference = $newReplacementQuantity - $currentReplacementQuantity;
	
			// Check if the part can accommodate the increased quantity
			if ($quantityDifference > 0 && $partQuantity >= $quantityDifference) {
				// Subtract the added quantity from the part
				$sqlUpdatePart = "
					UPDATE ".$this->productTable." 
					SET quantity = quantity - '".$quantityDifference."' 
					WHERE pid = '".$_POST['part']."'";
				mysqli_query($this->dbConnect, $sqlUpdatePart);
			} elseif ($quantityDifference < 0) {
				// Add the decreased quantity to the part
				$sqlUpdatePart = "
					UPDATE ".$this->productTable." 
					SET quantity = quantity + '".abs($quantityDifference)."' 
					WHERE pid = '".$_POST['part']."'";
				mysqli_query($this->dbConnect, $sqlUpdatePart);
			} elseif ($quantityDifference > 0 && $partQuantity < $quantityDifference) {
				echo '0';
				return;
			}
	
			// Update the replacement record
			$sqlUpdateReplacement = "
				UPDATE ".$this->replacedTable." 
				SET phone_pid = '".$_POST['phone']."', part_pid = '".$_POST['part']."', quantity = '".$_POST['quantity']."' 
				WHERE replacement_id = '".$_POST['replacement_id']."'";
			mysqli_query($this->dbConnect, $sqlUpdateReplacement);
	
			echo '1';
		}
	}



	//SERVICES

	public function addServices() {
    if($_POST['service_name'] && $_POST['service_price']){
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
            $imageName = $_FILES['service_image']['name'];
            $imageTmpName = $_FILES['service_image']['tmp_name'];
            $imageDestination = 'img/' . $imageName;
            move_uploaded_file($imageTmpName, $imageDestination);
        } else {
            $imageName = '';
        }

        $sqlInsert = "
            SELECT * FROM ".$this->servicesTable." WHERE service_name = '".$_POST['service_name']."'";        
        $result = mysqli_query($this->dbConnect, $sqlInsert);
        
        if(mysqli_num_rows($result) > 0){
            echo '0';
        } else {
            $sqlInsert = "
            INSERT INTO ".$this->servicesTable."(service_name, service_price, image) 
            VALUES ('".$_POST['service_name']."', '".$_POST['service_price']."', '".$imageName."')";        
            $success = mysqli_query($this->dbConnect, $sqlInsert);
            if($success) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }
}

	public function listServices() {
		$sqlQuery = "SELECT * FROM ".$this->servicesTable; 
		// Apply ordering if set
		if (isset($_POST['order'])) {
			$sqlQuery .= ' ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST['order']['0']['dir'] . ' ';
		} else {
			$sqlQuery .= ' ORDER BY service_name DESC ';
		}
		
		// Apply pagination if set
		if ($_POST['length'] != -1) {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
		
		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$servicesData = array();
		$intermediate = 1;
		while ($service = mysqli_fetch_assoc($result)) {
			$serviceRow = array();
			$serviceRow[] = $intermediate++;
			$serviceRow[] = '<img src="img/'.$service['image'].'" alt="'.$service['service_name'].'" style="width:50px;height:50px;">';
			$serviceRow[] = $service['service_name'];
			$serviceRow[] = $service['service_price'];
			$serviceRow[] = '<div class="btn-group btn-group-sm"><button type="button" name="update" id="'.$service["service_id"].'" class="btn btn-primary btn-sm rounded-0 update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$service["service_id"].'" class="btn btn-danger btn-sm rounded-0 delete" title="Delete"><i class="fa fa-trash"></i></button></div>';
			$servicesData[] = $serviceRow;
		}
		$output = array(
			"draw" => intval($_POST["draw"]),
			"recordsTotal" => $numRows,
			"recordsFiltered" => $numRows,
			"data" => $servicesData
		);
		echo json_encode($output);
	}

	public function deleteServices(){
		$sqlQuery = "DELETE FROM ".$this->servicesTable." WHERE service_id = '".$_POST["service_id"]."'";	
		if (mysqli_query($this->dbConnect, $sqlQuery)) {
			echo "Service deleted successfully.";
		} else {
			echo "Error deleting service: " . mysqli_error($this->dbConnect);
		}
	}


    public function updateProductQuantity($productId, $quantity) {
        $query = "UPDATE ".$this->productTable." SET quantity = quantity - ? WHERE pid = ?";
        $statement = $this->dbConnect->prepare($query);
        $statement->bind_param("ii", $quantity, $productId);
        $statement->execute();
    }

	public function getServicesDetails() {
        $sqlQuery = "
            SELECT * FROM ".$this->servicesTable." 
            WHERE service_id = '".$_POST["services_id"]."'";
        $result = mysqli_query($this->dbConnect, $sqlQuery);    
        $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
        $row['image'] = 'img/' . $row['image'];
        echo json_encode($row);
    }

    public function updateServices() {
    if($_POST['services_id']) {
        if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] == 0) {
            $imageName = $_FILES['service_image']['name'];
            $imageTmpName = $_FILES['service_image']['tmp_name'];
            $imageDestination = 'img/' . $imageName;
            move_uploaded_file($imageTmpName, $imageDestination);
        } else {
            $imageName = $_POST['existing_image'];
        }

        $sqlInsert = "
            SELECT * FROM ".$this->servicesTable." WHERE service_name = '".$_POST['service_name']."'
            AND service_id != '".$_POST['services_id']."'";        
        $result = mysqli_query($this->dbConnect, $sqlInsert);
        
        if(mysqli_num_rows($result) > 0){
            echo '0';
        } else {
            $sqlUpdate = "
            UPDATE ".$this->servicesTable." 
            SET service_name = '".$_POST['service_name']."', service_price = '".$_POST['service_price']."', image = '".$imageName."' 
            WHERE service_id = '".$_POST['services_id']."'";        
            $success =  mysqli_query($this->dbConnect, $sqlUpdate);    
            
            if($success) {
                echo '1';
            } else {
                echo '0';
            }
        }
    }
}

	//service availed

	
public function service_availedList() {
    $sqlQuery = "
        SELECT sa.id, c.name as customer_name, s.service_name, sa.availed_date 
        FROM ".$this->service_availedTable." sa
        JOIN ".$this->customerTable." c ON sa.customer_id = c.id
        JOIN ".$this->servicesTable." s ON sa.service_id = s.service_id";
    
    // Apply ordering if set
    if (isset($_POST['order'])) {
        $sqlQuery .= ' ORDER BY ' . $_POST['order']['0']['column'] . ' ' . $_POST['order']['0']['dir'] . ' ';
    } else {
        $sqlQuery .= ' ORDER BY sa.id DESC ';
    }
    
    // Apply pagination if set
    if ($_POST['length'] != -1) {
        $sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
    }
    
    $result = mysqli_query($this->dbConnect, $sqlQuery);
    $numRows = mysqli_num_rows($result);
    $servicesData = array();
    $intermediate = 1;
    while ($service = mysqli_fetch_assoc($result)) {
        $serviceRow = array();
        $serviceRow[] = $intermediate++;
        $serviceRow[] = $service['customer_name'];
        $serviceRow[] = $service['service_name'];
        $serviceRow[] = $service['availed_date'];
        $serviceRow[] = '<div class="btn-group btn-group-sm"><button type="button" name="update" id="'.$service["id"].'" class="btn btn-primary btn-sm rounded-0 update" title="Update"><i class="fa fa-edit"></i></button><button type="button" name="delete" id="'.$service["id"].'" class="btn btn-danger btn-sm rounded-0 delete" title="Delete"><i class="fa fa-trash"></i></button></div>';
        $servicesData[] = $serviceRow;
    }
    $output = array(
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $numRows,
        "recordsFiltered" => $numRows,
        "data" => $servicesData
    );
    
    echo json_encode($output);
}

public function addServiceAvailed() {
    $sqlInsert = "
        INSERT INTO ".$this->service_availedTable."(customer_id, service_id, availed_date) 
        VALUES ('".$_POST['customer_id']."', '".$_POST['service_id']."', '".$_POST['availed_date']."')";
    mysqli_query($this->dbConnect, $sqlInsert);
    echo 'Service Availed Added';
}

public function getServiceAvailedDetails() {
    $sqlQuery = "
        SELECT * FROM ".$this->service_availedTable." 
        WHERE id = '".$_POST["service_availed_id"]."'";
    $result = mysqli_query($this->dbConnect, $sqlQuery);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    echo json_encode($row);
}

public function updateServiceAvailed() {
    if($_POST['service_availed_id']) {
        $sqlUpdate = "
            UPDATE ".$this->service_availedTable." 
            SET customer_id = '".$_POST['customer_id']."', service_id = '".$_POST['service_id']."', availed_date = '".$_POST['availed_date']."' 
            WHERE id = '".$_POST['service_availed_id']."'";
        mysqli_query($this->dbConnect, $sqlUpdate);
        echo 'Service Availed Updated';
    }
}

public function deleteServiceAvailed() {
    $sqlQuery = "
        DELETE FROM ".$this->service_availedTable." 
        WHERE id = '".$_POST["service_availed_id"]."'";
    mysqli_query($this->dbConnect, $sqlQuery);
    echo 'Service Availed Deleted';
}
//ADD the debug in save and update service===============================================================================================================
public function getCustomerListDropdown() {
    $sqlQuery = "SELECT id, name FROM ".$this->customerTable." ORDER BY name ASC";
    $result = mysqli_query($this->dbConnect, $sqlQuery);
    $dropdownHTML = '<option value="">Select Customer</option>';
    while ($customer = mysqli_fetch_assoc($result)) {
        $dropdownHTML .= '<option value="'.$customer["id"].'">'.$customer["name"].'</option>';
    }
    echo $dropdownHTML;
}

public function getServiceListDropdown() {
    $sqlQuery = "SELECT service_id, service_name FROM ".$this->servicesTable." ORDER BY service_name ASC";
    $result = mysqli_query($this->dbConnect, $sqlQuery);
    $dropdownHTML = '<option value="">Select Service</option>';
    while ($service = mysqli_fetch_assoc($result)) {
        $dropdownHTML .= '<option value="'.$service["service_id"].'">'.$service["service_name"].'</option>';
    }
    echo $dropdownHTML;
}

    public function getAvailableParts() {
        $phoneId = $_POST['phone_id'];
        $currentPartId = isset($_POST['current_part_id']) ? $_POST['current_part_id'] : 0;
        $sqlQuery = "SELECT p.pid, p.pname, p.quantity 
                     FROM ".$this->productTable." p 
                     JOIN ".$this->categoryTable." c ON c.categoryid = p.categoryid
                     WHERE c.name = 'Parts' 
                     AND (p.pid NOT IN (SELECT part_pid FROM ".$this->replacedTable." WHERE phone_pid = '".$phoneId."') OR p.pid = '".$currentPartId."')
                     ORDER BY p.pname ASC";
        $result = mysqli_query($this->dbConnect, $sqlQuery);
        if (!$result) {
            die('Error in query: ' . mysqli_error($this->dbConnect)); // Debug log
        }
        $dropdownHTML = '<option value="">Select Product</option>';
        while ($product = mysqli_fetch_assoc($result)) {
            $dropdownHTML .= '<option value="'.$product["pid"].'">'.$product["pname"]." (".$product["quantity"].') </option>';
        }
        echo $dropdownHTML;
    }

	//==
	
	public function getIncomeDataAll() {
		$currentMonth = date('m'); // Get current month
		$currentYear = date('Y'); // Get current year

		$sqlQuery = "SELECT p.pid, p.pname, p.quantity as product_quantity, p.selling_price, p.base_price,
						(SELECT SUM(s.quantity) FROM ".$this->purchaseTable." as s WHERE s.product_id = p.pid ) as recieved_quantity, 
						(SELECT COALESCE(SUM(r.total_sell), 0) FROM ".$this->orderTable." as r WHERE r.product_id = p.pid ) as total_sell,
						(SELECT COALESCE(SUM(rp.quantity), 0) FROM ".$this->replacedTable." as rp WHERE rp.part_pid = p.pid ) as total_replaced
					FROM ".$this->productTable." as p
					GROUP BY p.pid"; // Group by product ID to sum quantities

		$result = mysqli_query($this->dbConnect, $sqlQuery);
		$numRows = mysqli_num_rows($result);
		$inventoryData = array();    
		$i = 1;
		$total_income_product = 0;
		while( $inventory = mysqli_fetch_assoc($result) ) {    
			if(!$inventory['recieved_quantity']) {
				$inventory['recieved_quantity'] = 0;
			}
			$totalSell = $inventory['total_sell'] + $inventory['total_replaced'];
			$revenue = $totalSell * $inventory['selling_price'];
			$based_price = $inventory['base_price'] * $totalSell;
			if($totalSell > 0){
				$income = $revenue - $based_price;
			}else{
				$income = 0;
			}
			$total_income_product += $income;
		}
		$productIncome = $total_income_product;

		$sqlServiceIncome = "
			SELECT SUM(s.service_price) AS service_income
			FROM ".$this->service_availedTable." sa
			JOIN ".$this->servicesTable." s ON sa.service_id = s.service_id
			WHERE MONTH(sa.availed_date) = '$currentMonth' AND YEAR(sa.availed_date) = '$currentYear'";
		$resultService = mysqli_query($this->dbConnect, $sqlServiceIncome);
		$serviceIncome = mysqli_fetch_assoc($resultService)['service_income'];
		
		$totalIncome = $productIncome + $serviceIncome;

		$data = array(
			"total_income" => $totalIncome,
			"product_income" => $productIncome,
			"service_income" => $serviceIncome
		);

		echo json_encode($data);
	}

	

		
	public function getMonthlyIncomeData() {
		$currentYear = date('Y'); // Get current year

		$monthlyIncomeData = [];
		for ($month = 1; $month <= 12; $month++) {
			$sqlQuery = "SELECT p.pid, p.pname, p.quantity as product_quantity, p.selling_price, p.base_price,
							(SELECT COALESCE(SUM(s.quantity), 0) FROM ".$this->purchaseTable." as s WHERE s.product_id = p.pid AND MONTH(s.purchase_date) = '$month' AND YEAR(s.purchase_date) = '$currentYear') as recieved_quantity, 
							(SELECT COALESCE(SUM(r.total_sell), 0) FROM ".$this->orderTable." as r WHERE r.product_id = p.pid AND MONTH(r.order_date) = '$month' AND YEAR(r.order_date) = '$currentYear') as total_sell,
							(SELECT COALESCE(SUM(rp.quantity), 0) FROM ".$this->replacedTable." as rp WHERE rp.part_pid = p.pid AND MONTH(rp.replacement_date) = '$month' AND YEAR(rp.replacement_date) = '$currentYear') as total_replaced
						FROM ".$this->productTable." as p
						GROUP BY p.pid"; // Group by product ID to sum quantities

			$result = mysqli_query($this->dbConnect, $sqlQuery);
			if (!$result) {
				echo json_encode(['error' => mysqli_error($this->dbConnect)]);
				return;
			}

			$total_income_product = 0;

			while ($inventory = mysqli_fetch_assoc($result)) {
				if (!$inventory['recieved_quantity']) {
					$inventory['recieved_quantity'] = 0;
				}
				$totalSell = $inventory['total_sell'] + $inventory['total_replaced'];
				$revenue = $totalSell * $inventory['selling_price'];
				$based_price = $inventory['base_price'] * $totalSell;
				$income = $totalSell > 0 ? $revenue - $based_price : 0;
				$total_income_product += $income;
			}
			$productIncome = $total_income_product;

			$sqlServiceIncome = "
				SELECT COALESCE(SUM(s.service_price), 0) AS service_income
				FROM ".$this->service_availedTable." sa
				JOIN ".$this->servicesTable." s ON sa.service_id = s.service_id
				WHERE MONTH(sa.availed_date) = '$month' AND YEAR(sa.availed_date) = '$currentYear'";
			$resultService = mysqli_query($this->dbConnect, $sqlServiceIncome);
			if (!$resultService) {
				echo json_encode(['error' => mysqli_error($this->dbConnect)]);
				return;
			}
			$serviceIncome = mysqli_fetch_assoc($resultService)['service_income'];

			$totalIncome = $productIncome + $serviceIncome;

			$monthlyIncomeData[] = [
				"month" => $month,
				"total_income" => $totalIncome,
				"product_income" => $productIncome,
				"service_income" => $serviceIncome
			];
		}

		header('Content-Type: application/json'); // Ensure the response is JSON
		echo json_encode($monthlyIncomeData);
	}
	
	
	public function getIncomeDataToday() {
		$data = [];
		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d', strtotime("-$i days")); // Get the date for the last 7 days

			$sqlQuery = "SELECT p.pid, p.pname, p.quantity as product_quantity, p.selling_price, p.base_price,
							(SELECT COALESCE(SUM(s.quantity), 0) FROM ".$this->purchaseTable." as s WHERE s.product_id = p.pid AND DATE(s.purchase_date) = '$date') as recieved_quantity, 
							(SELECT COALESCE(SUM(r.total_sell), 0) FROM ".$this->orderTable." as r WHERE r.product_id = p.pid AND DATE(r.order_date) = '$date') as total_sell,
							(SELECT COALESCE(SUM(rp.quantity), 0) FROM ".$this->replacedTable." as rp WHERE rp.part_pid = p.pid AND DATE(rp.replacement_date) = '$date') as total_replaced
						FROM ".$this->productTable." as p
						GROUP BY p.pid"; // Group by product ID to sum quantities

			$result = mysqli_query($this->dbConnect, $sqlQuery);
			$total_income_product = 0;
			while ($inventory = mysqli_fetch_assoc($result)) {
				$recieved_quantity = $inventory['recieved_quantity'] ?? 0;
				$totalSell = ($inventory['total_sell'] ?? 0) + ($inventory['total_replaced'] ?? 0);
				$revenue = $totalSell * $inventory['selling_price'];
				$based_price = $inventory['base_price'] * $totalSell;
				$income = $totalSell > 0 ? $revenue - $based_price : 0;
				$total_income_product += $income;
			}
			$productIncome = $total_income_product;

			$sqlServiceIncome = "
				SELECT COALESCE(SUM(s.service_price), 0) AS service_income
				FROM ".$this->service_availedTable." sa
				JOIN ".$this->servicesTable." s ON sa.service_id = s.service_id
				WHERE DATE(sa.availed_date) = '$date'";
			$resultService = mysqli_query($this->dbConnect, $sqlServiceIncome);
			$serviceIncome = mysqli_fetch_assoc($resultService)['service_income'] ?? 0;

			$totalIncome = $productIncome + $serviceIncome;

			$data[] = [
				"date" => $date,
				"total_income" => $totalIncome,
				"product_income" => $productIncome,
				"service_income" => $serviceIncome
			];
		}

		echo json_encode(array_reverse($data)); // Reverse the array to have the most recent date first
	}

}
?>

