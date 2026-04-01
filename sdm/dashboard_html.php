<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';

$mode = $_REQUEST["mode"];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
	header("Location: ../signin.php?mode=" . $mode_akses);
	exit;
} else {
	$dbSdm			= 'skk_presensigukar';
	$titlePage	= 'Dashboard - ' . APPS_NAME;
	$userName		= $_SESSION['username'];
	$goUserid		= $_SESSION['goUserid'];
	$goStatus		= $_SESSION['goStatus'];
	$goPassword	= $_SESSION['goPassword'];
	$folder			= $_SESSION['folder'];
	$response		= $_SESSION['response'];

	$id_key			= $response[0]->id_key;
	$idUser			= $response[0]->id;
	$nama				= $response[0]->nama;
	$hp					= $response[0]->hp;

	$today					= date('Y-m-d');
	$sqlPresensi		= "SELECT * FROM $dbSdm.presensi WHERE user_id = '" . $id_key . "' AND hari = '" . $today . "'";
	$runPresensi		= JalankanSQL('view', $sqlPresensi, 1);
	$hari						= $runPresensi[0]->hari;
	$jamDatang			= $runPresensi[0]->jamdatang;
	$statusDatang		= $runPresensi[0]->statusdatang;
	$lokasiDatang		= $runPresensi[0]->lokasidatang;
	$fotoDatang			= $runPresensi[0]->fotodatang;
	$bidston				= $runPresensi[0]->bidston;
	$jamPulang			= $runPresensi[0]->jampulang;
	$statusPulang		= $runPresensi[0]->statuspulang;
	$lokasiPulang		= $runPresensi[0]->lokasipulang;
	$fotoPulang			= $runPresensi[0]->fotopulang;
}

if (isset($_GET['logout'])) {
	unset($id_key);
	session_destroy();
	header("location: ../signin.php?mode=" . $mode_akses);
}
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('webpart/head.php') ?>
	</head>

	<body>
		<main>
			<?php include('webpart/topbar.php') ?>
			<div class="px-4 py-5 my-5 text-center">
				<img class="d-block mx-auto mb-4 img-fluid" src="assets/imgs/logo.png" alt="">
				<div class="col-lg-6 mx-auto">
					<p class="lead fw-bold text-primary mb-4">PRESENSI KEHADIRAN <?= date('d-m-Y') ?></p>
					<div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
						<table class="table">
							<tbody>
								<tr>
									<td>Nama</td>
									<td><?= $nama ?></td>
								</tr>
								<tr>
									<td>No. HP</td>
									<td><?= $hp ?></td>
								</tr>
							</tbody>
						</table>
					</div>

					<div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
						<p class="lead fw-bold text-primary">DATA PRESENSI HARI INI</p>
						<table class="table">
							<tbody>
								<tr>
									<th>Hari</th>
									<td><?= date('d-m-Y', strtotime($hari)) ?></td>
								</tr>
								<tr>
									<th>Kedatangan</th>
									<td><?= date('H:i:s', strtotime($jamDatang)) . '(Bidston: ' . $bidston . ' // Status: ' . $statusDatang . ' // Lokasi: ' . $lokasiDatang . ')' ?></td>
								</tr>
								<tr>
									<th>Foto datang</th>
									<td><img src="https://pr.skk.my.id/images/buktipresensi/<?= $fotoDatang ?>" class="img img-fluid"></td>
								</tr>
								<tr>
									<th>Kepulangan</th>
									<td><?= date('H:i:s', strtotime($jamPulang)) . ' // Status: ' . $statusPulang . ' // Lokasi: ' . $lokasiPulang ?>)</td>
								</tr>
								<tr>
									<th>Foto pulang</th>
									<td><img src="https://pr.skk.my.id/images/buktipresensi/<?= $fotoPulang ?>" class="img img-fluid"></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<?php include('webpart/navbar_bottom.php') ?>
			<?php include('webpart/js.php') ?>
		</main>
	</body>

</html>
