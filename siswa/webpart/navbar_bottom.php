<nav class="navbar navbar-dark bg-primary navbar-expand d-md-none d-lg-none d-xl-none fixed-bottom">
	<ul class="navbar-nav nav-justified w-100">
		<li class="nav-item">
			<a href="dashboard.php?mode=<?= $mode_akses ?>" class="fs-4 nav-link text-white"><i class="bi bi-house-door-fill"></i></a>
		</li>
		<li class="nav-item">
			<a href="rekappembayaran.php?mode=<?=$mode_akses?>" class="fs-4 nav-link text-white"><i class="bi bi-cash-stack"></i></a>
		</li>
		<li class="nav-item">
			<a href="notifikasi.php?mode=<?=$mode_akses?>" class="fs-4 nav-link text-white"><i class="bi bi-bell-fill"></i></a>
		</li>
		<li class="nav-item">
			<a href="#" onclick="location.href='/index.php?logout'" class="fs-4 nav-link text-white"><i class="bi bi-person-fill"></i></a>
		</li>
	</ul>
</nav>