<?php
	$username = $_SESSION["username"];
	$goUserid = $_SESSION["goUserid"];
	$goStatus = $_SESSION["goStatus"];
	$goPassword = $_SESSION["goPassword"];
	$folder = $_SESSION["folder"];
?>

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
				<h5 class="offcanvas-title" id="offcanvasNavbarLabel">Pilih Menu</h5>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body">
				<ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
					<li class="nav-item">
						<a class="nav-link active" aria-current="page" href="dashboard.php?mode=<?=$mode_akses?>">Home</a>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Akademik
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="#">Presensi</a></li>
							<li><a class="dropdown-item" href="#">Nilai Harian</a></li>
							<li><a class="dropdown-item" href="#">Nilai Ujian</a></li>
							<li><a class="dropdown-item" href="#">Rapor Nilai</a></li>
						</ul>
					</li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Keuangan
						</a>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="kartutagihan.php?mode=<?=$mode_akses?>">Kartu Tagihan</a></li>
							<li><a class="dropdown-item" href="rekappembayaran.php?mode=<?=$mode_akses?>">Rekap Pembayaran</a></li>
							<li><a class="dropdown-item" href="bayartagihan.php?mode=<?=$mode_akses?>">Bayar Tagihan</a></li>
						</ul>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="notifikasi.php?mode=<?=$mode_akses?>">Notifikasi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="https://skkk.us/hubungi/">Hubungi Admin</a>
					</li>					
					<li class="nav-item">
						<a class="nav-link" href="#" onclick="location.href='/index.php?logout'">Logout</a>
					</li>
				</ul>
			</div>
		</div>
	</div>
</nav>