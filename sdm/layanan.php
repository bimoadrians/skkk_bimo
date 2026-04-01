<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';

$mode = $_REQUEST['mode'];

if ($mode != $mode_akses) {
	header('Location: ' . $skkksurakarta);
	die();
}

$titlePage		= 'Layanan - ' . APPS_NAME;
$dbPresensi		= 'skk_presensigukar';
$userId				= $_REQUEST['user_id'];
$cmdView			= 'view';
$limit				= 1;
$queryUser		= "SELECT nomorinduk, nama, departemen FROM $dbPresensi.user WHERE user_id = '" . $userId . "'";
$runUser			= JalankanSQL($cmdView, $queryUser, $limit);
$nomorInduk		= $runUser[0]->nomorinduk;
$nama					= $runUser[0]->nama;
$titleHeader	= 'Layanan';
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('webpart/head.php') ?>
	</head>

	<body>
		<?php include('webpart/sidebar.php') ?>
		<div class="container-fluid">
			<main>
				<div class="row mb-4 bg-primary p-2">
					<div class="row mt-2 mb-3">
						<h5 class="fw-bold text-white">GURU - KARYAWAN</h5>
						<small class="text-warning fst-italic"><?= $nama ?></h4>
					</div>
				</div>

				<div class="row p-2">

				</div>

				<div class="row g-4 p-2">
					<div class="col-lg-12 col-md-6 col-lg-3">
						<a href="presensi.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="text-decoration-none">
							<div class="card card-custom card-primary">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h3 class="text-primary mb-0">Data <span class="fw-bold">Presensi</span></h3>
									</div>
									<i class="bi bi-fingerprint icon text-primary"></i>
								</div>
							</div>
						</a>
					</div>

					<div class="col-lg-12 col-md-6 col-lg-3">
						<a href="cuti.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="text-decoration-none">
							<div class="card card-custom card-warning">
								<div class="d-flex justify-content-between align-items-center">
									<div>
										<h3 class="text-dark mb-0">Pengajuan <span class="fw-bold">Cuti</span></h3>
									</div>
									<i class="bi bi-file-earmark-richtext icon text-dark"></i>
								</div>
							</div>
						</a>
					</div>
				</div>
			</main>
		</div>
	</body>
	<?php include('webpart/js.php') ?>

</html>
