<nav class="navbar bg-primary fixed-top">
	<div class="container-fluid">
		<a class="navbar-brand" href="#">
			<img src="../assets/imgs/logo.png" alt="Logo" width="35" height="35" class="d-inline-block align-top">
			<span class="ms-2 text-white"><?= SCHOOL_NAME ?></span>
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
			<i class="bi bi-list"></i>
		</button>
		<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
			<div class="offcanvas-header">
				<h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body">
				<ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="#">Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Link</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Dropdown
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="#">Action</a></li>
							<li><a class="dropdown-item" href="#">Another action</a></li>
							<li>
								<hr class="dropdown-divider">
							</li>
							<li><a class="dropdown-item" href="#" onclick="location.href='/index.php?logout'">Logout</a></li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#" onclick="location.href='/index.php?logout'">Logout</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>