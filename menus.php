<nav class="navbar navbar-dark bg-dark bg-gradient navbar-expand-lg navbar-expand-md my-3">
	<div class="container-fluid">
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
			aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="nav navbar-nav mx-auto">
			<li class="nav-item"><a class="nav-link" href="statistic.php"><i class="fas fa-dollar-sign"></i> Statistics</a></li>
			<li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Inventory</a></li>
			<li class="nav-item"><a class="nav-link" href="services.php"><i class="fas fa-cog"></i> Services</a></li>
			<li class="nav-item"><a class="nav-link" href="customer.php"><i class="fas fa-users"></i> Customer</a></li>
			<li class="nav-item"><a class="nav-link" href="category.php"><i class="fas fa-th-list"></i> Category</a></li>
			<li class="nav-item"><a class="nav-link" href="brand.php"><i class="fas fa-tag"></i> Brand</a></li>
			<li class="nav-item"><a class="nav-link" href="supplier.php"><i class="fas fa-truck"></i> Supplier</a></li>
			<li class="nav-item"><a class="nav-link" href="product.php"><i class="fas fa-cogs"></i> Product</a></li>
			<li class="nav-item"><a class="nav-link" href="replaced.php"><i class="fas fa-info-circle"></i> Replaced</a></li>
			<li class="nav-item"><a class="nav-link" href="purchase.php"><i class="fas fa-cart-plus"></i> Purchase</a></li>
			<li class="nav-item"><a class="nav-link" href="order.php"><i class="fas fa-box"></i> Orders</a></li>
			<li class="nav-item"><a class="nav-link" href="service_availed.php"><i class="fas fa-shield-alt"></i> Service Availed</a></li>
			
		</ul>

		</div>
		<ul class="nav navbar-nav">
		<li class="dropdown position-relative">
			<button type="button" class="badge bg-light border px-3 text-dark rounded-pill dropdown-toggle" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
				<img src="img/dean.jpg" class="rounded-circle" width="30" height="30" alt="User Avatar"> <?php echo $_SESSION['name']; ?>
			</button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
				<li><a class="dropdown-item" href="action.php?action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
			</ul>
		</li>

		</ul>
	</div>
</nav>
<script>
	document.addEventListener('DOMContentLoaded', () => {
    const links = document.querySelectorAll('.nav-link');
    links.forEach(link => {
        link.addEventListener('click', function () {
            links.forEach(item => item.classList.remove('active')); // Remove active class from all
            this.classList.add('active'); // Add active class to clicked item
        });
    });
});
</script>