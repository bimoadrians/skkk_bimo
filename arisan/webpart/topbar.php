<nav class="navbar bg-primary fixed-top navbar-scrollable">
	<div class="container-fluid">
		<a class="navbar-brand d-flex align-items-center justify-content-center ms-2 text-white">
			<img src="../assets/imgs/logo.png" alt="Logo" width="35" height="35" class="d-inline-block align-top">
			<span class="ms-2 text-white"><?= SCHOOL_NAME ?></span>
		</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#kkNavBar" aria-controls="kkNavBar" aria-label="Navigation">
			<i class="bi bi-list"></i>
		</button>
		<div class="offcanvas offcanvas-end" tabindex="-1" id="kkNavBar" aria-labelledby="kkNavBarLabel">
			<div class="offcanvas-header">
				<h5 class="offcanvas-title" id="kkNavBarLabel">Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body">
				<ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="dashboard.php?mode=<?= $mode_akses ?>">Dashboard</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="transaksi.php?mode=<?= $mode_akses ?>">Transaksi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="rekap.php?mode=<?= $mode_akses ?>">Rekap Kewajiban</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#" onclick="location.href='/index.php?logout'">Logout</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>