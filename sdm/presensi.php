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

$titlePage	= 'Rekap Presensi - ' . APPS_NAME;
$dbPresensi	= 'skk_presensigukar';
$userId			= $_REQUEST['user_id'];
$perintah		= 'view';
$limit			= 1;
$startDate	= isset($_REQUEST['tglAwal']) ? $_REQUEST['tglAwal'] : date('m/d/Y');
$endDate		= isset($_REQUEST['tglAkhir']) ? $_REQUEST['tglAkhir'] : date('m/d/Y');
$tglAwal		= date('Y-m-d', strtotime($startDate));
$tglAkhir		= date('Y-m-d', strtotime($endDate));
$startTimestamp	= strtotime($tglAwal);
$endTimestamp		= strtotime($tglAkhir);
$selisihDetik		= $endTimestamp - $startTimestamp;
$selisihHari		= ($selisihDetik / 86400) + 1;
if (isset($_REQUEST['cari'])) {
	$queryRekap	= "SELECT * FROM $dbPresensi.presensi WHERE user_id = '" . $userId . "' AND hari BETWEEN '" . $tglAwal . "' AND '" . $tglAkhir . "'";
	$runRekap		= JalankanSQL($perintah, $queryRekap, $selisihHari);

	$queryUser	= "SELECT nama FROM $dbPresensi.user WHERE user_id = '" . $userId . "'";
	$runUser		= JalankanSQL($perintah, $queryUser, $limit);
}

$titleHeader	= 'Presensi';
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
				<div class="row mb-4 bg-primary p-2 text-white">
					<h5 class="fw-bold mb-4">LIHAT DATA</h5>
					<form id="presensi" method="post" enctype="multipart/form-data">
						<input type="hidden" name="usrid" id="usrid" class="form-control" value="<?= $_REQUEST['user_id'] ?>">
						<div class="row mb-3">
							<div class="col-12">
								<label for="tglAkhir" class="fw-bold form-label">Tanggal Awal</label>
								<div class="input-group">
									<span class="input-group-text" id="tglAwal"><i class="bi bi-calendar-plus"></i></span>
									<input type="date" name="tglAwal" id="tglAwal" class="form-control" value="<?= $startDate ?>" aria-label="Tanggal Awal" aria-describedby="tglAwal" required>
								</div>
							</div>
							<div class="col-12">
								<label for="tglAkhir" class="fw-bold form-label">Tanggal Akhir</label>
								<div class="input-group">
									<span class="input-group-text" id="tglAkhir"><i class="bi bi-calendar2-check"></i></span>
									<input type="date" name="tglAkhir" id="tglAkhir" class="form-control" value="<?= $endDate ?>" aria-label="Tanggal Akhir" aria-describedby="tglAkhir" required>
								</div>
							</div>
						</div>
						<div class="row mb-3">
							<button type="submit" name="cari" id="cari" class="btn btn-light"><i class="bi bi-search"></i> Cari</button>
						</div>
					</form>
				</div>

				<div class="row p-2">
					<h4 class="fw-bold card-title text-primary"><?= $runUser[0]->nama; ?></h4>
				</div>
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th class="align-middle" scope="col">Tanggal</th>
								<th class="align-middle" scope="col">Datang</th>
								<th class="align-middle" scope="col">Pulang</th>
							</tr>
						</thead>
						<tbody>
							<?php
						$no = 1;
						foreach ($runRekap as $r) { ?>
							<tr>
								<td class="fw-bold text-primary"><?= date('d-m-Y', strtotime($r->hari)) ?></td>
								<td><?= date('H:i:s', strtotime($r->jamdatang)) . '<br>Bidston: ' . $r->bidston . '<br>Gedung: ' . $r->lokasidatang ?></td>
								<td><?= (($r->jampulang != '00:00:00') ? date('H:i:s', strtotime($r->jampulang)) : '-') . '<br>Gedung: ' . (($r->lokasipulang != '') ? $r->lokasipulang : '-') ?></td>
							</tr>
							<?php $no++;
						} ?>
						</tbody>
					</table>
				</div>
			</main>
		</div>
	</body>
	<?php include('webpart/js.php') ?>

</html>
