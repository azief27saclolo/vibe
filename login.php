<?php 
ob_start();
session_start();
include('inc/header.php');
$loginError = '';
if (!empty($_POST['email']) && !empty($_POST['pwd'])) {
	include 'Inventory.php';
	$inventory = new Inventory();
	$login = $inventory->login($_POST['email'], $_POST['pwd']); 
	if(!empty($login)) {
		$_SESSION['userid'] = $login[0]['userid'];
		$_SESSION['name'] = $login[0]['name'];			
		header("Location:index.php");
	} else {
		$loginError = "Invalid email or password!";
	}
}
?>
<style>
html, body {
    height: 100%;
    width: 100%;
    background-color: #f0f8ff; /* Soft light blue */
    font-family: 'Poppins', sans-serif;
    color: #333;
}

body>.container {
	display: flex;
	flex-direction: column;
	align-items: center;
	justify-content: center;
	height: 100%;
}

#title {
	font-size: 2.5rem;
	font-weight: 600;
	color: #1e3a5f; /* Dark navy blue */
	margin-bottom: 20px;
	text-align: center;
}

.card {
	background-color: #ffffff;
	border: 1px solid #d1e7ff; /* Light blue border */
	box-shadow: 0 4px 8px rgba(30, 58, 95, 0.1);
	border-radius: 10px;
}

.card-header {
	background-color: #e3f2fd; /* Very light blue */
	color: #1e3a5f;
	font-size: 1.25rem;
	font-weight: 500;
	text-align: center;
	border-bottom: 1px solid #d1e7ff;
}

.card-body {
	background-color: #ffffff;
	padding: 20px;
}

.btn-primary {
	background-color: #007bff; /* Medium blue */
	border: none;
	color: #ffffff;
	border-radius: 5px;
	padding: 10px 15px;
	font-size: 1rem;
}

.btn-primary:hover {
	background-color: #0056b3; /* Darker blue */
}

.alert-danger {
	background-color: #f8d7da;
	color: #721c24;
	border: 1px solid #f5c6cb;
	border-radius: 5px;
	padding: 10px;
}

.form-control {
	background-color: #ffffff;
	color: #333;
	border: 1px solid #d1e7ff; /* Light blue */
	border-radius: 5px;
	padding: 10px;
}

.form-control:focus {
	border-color: #007bff;
	box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}
</style>
<?php include('inc/container.php');?>

<h1 id="title">VIBE Inventory Management System</h1>
<div class="col-lg-4 col-md-6 col-sm-10">
	<div class="card">
		<div class="card-header">Login</div>
		<div class="card-body">
			<?php if ($loginError) { ?>
				<div class="alert alert-danger">
					<i class="fas fa-exclamation-circle"></i> <?php echo $loginError; ?>
				</div>
			<?php } ?>
			<form method="post" action="">
				<div class="mb-3">
					<label for="email" class="form-label">Email Address</label>
					<input name="email" id="email" type="email" class="form-control" placeholder="Enter your email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>" required>
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Password</label>
					<input type="password" class="form-control" id="password" name="pwd" placeholder="Enter your password" required>
				</div>
				<div class="d-grid">
					<button type="submit" name="login" class="btn btn-primary">
						<i class="fas fa-sign-in-alt"></i> Login
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?php include('inc/footer.php');?>
