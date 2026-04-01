<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$mode = $_REQUEST["mode"];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$titlePage		= 'Notifikasi - ' . APPS_NAME;
$titleHeader	= 'Notifikasi';
$userId				= $_REQUEST['user_id'];
if ($userId == 'DEMO' || $userId == 'demo') {
	$userId = '52627250047';
}
$today				= date('Y-m-d');
$hariIni			= date('d-m-Y');
$perintah			= 'view';
$limit				= 1;
$sqlUser			= "SELECT s.nopendaftaran,
												s.replid,
												s.nama,
												s.hportu,
												p.proses,
												s.alamatsiswa,
												s.keterangan
								FROM jbsakad.calonsiswa s
								LEFT JOIN jbsakad.prosespenerimaansiswa p ON s.idproses = p.replid
								WHERE s.nopendaftaran = '{$userId}'";
$dataUser			= JalankanSQL($perintah, $sqlUser, $limit);
//BEGIN EKSTRAK RESPONSE
$id_key						= $dataUser[0]->nopendaftaran;
$idcalon					= $dataUser[0]->replid;
$nama							= $dataUser[0]->nama;
//$telpon						= $dataUser[0]->telpon;
$hp1							= $dataUser[0]->hportu;
//$hp2							= $dataUser[0]->info1;
//$hp3							= $dataUser[0]->info2;
//$kelompok					= $dataUser[0]->kelompok;
$proses						= $dataUser[0]->proses;
$alamattinggal		= $dataUser[0]->alamatsiswa;
$keterangancasis	= $dataUser[0]->keterangan;
//END EKSTRAK RESPONSE

//BEGIN CARI BANK & VA
$sqlVa		= "SELECT b.bank, v.virtualaccount
						FROM jbsakad.va v
						LEFT JOIN jbsakad.calonsiswa s ON s.nopendaftaran = v.nis 
						LEFT JOIN jbsakad.bank b ON b.replid = v.bank
						WHERE s.nopendaftaran = '{$id_key}'
						AND v.statusaktif = 1 AND jenis = 0";
$dataVa		= JalankanSQL($perintah, $sqlVa, $limit);
$bank						= $dataVa[0]->bank;
$virtualAccount	= $dataVa[0]->virtualaccount;
//END CARI BANK & VA

//BEGIN CARI REKAP PEMBAYARAN
$sqlCariRekap		= "SELECT InsertIntoDB AS tanggal, Text AS isipesan
									FROM jbssms.outboxhistory
									WHERE Text LIKE '%" . $nama . "%' ORDER BY id DESC";
$dataCariRekap	= JalankanSQL($perintah, $sqlCariRekap, 100);
//END CARI REKAP PEMBAYARAN
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
			<div class="card animate__animated animate__fadeInUp p-2 my-3 bg-image rounded-4 shadow" style="background-image: url('../assets/imgs/card.png'); ">
				<div class="card-body flex-column text-white">
					<h6 class="card-title">No. Pendaftaran : <?= $id_key ?></h6>
					<div class="card-text">
						<p class="fs-3 text-warning"><?= $nama ?></p>
						<p class="fs-6">Proses : <?= $proses ?></p>
						<p class="fs-6">VA <?= $bank ?>: <span id="nomor-va"><?= $kode_va_siswa . $virtualAccount ?></span> <button id="copy-button" class="btn btn-sm btn-warning"><i class="bi bi-clipboard"></i> Salin</button></p>
					</div>
				</div>
			</div>

			<div class="card card-custom p-2 my-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">
					<h5 class="card-title text-primary">Notifikasi</h5>
					<div class="card-text">
						<div class="table-responsive">
							<?php if ($dataCariRekap) { ?>
								<table class="table">
									<thead>
										<tr>
											<th scope="col" width="2">No</th>
											<th scope="col" width="20">Informasi</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($dataCariRekap as $mydata) {
											$i = $i + 1;
											$tanggal = date("d-m-Y", strtotime($mydata->tanggal));
										?>
											<tr>
												<th scope="row"><?= $i ?></th>
												<td><sup><b>Tanggal :</b> <?= $tanggal ?>
														<hr /><?= ConvertWAToHTML($mydata->isipesan) ?>
													</sup></td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							<?php } else {
								echo "Tidak ditemukan data pembayaran";
							}
							?>
						</div>
						<br />
						<div class="col-auto">
							<a href="dashboard.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="btn btn-primary"><i class="bi bi-arrow-bar-left"></i> Kembali ke Dashboard</a>
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
	<?php include('webpart/js.php') ?>
</body>

</html>