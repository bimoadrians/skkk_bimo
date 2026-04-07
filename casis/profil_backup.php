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
$titlePage		= 'Laporan Nilai - ' . APPS_NAME;
$titleHeader	= 'Laporan Nilai';
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

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];

$url_lengkap	= $protocol . '://' . $host . $uri;
$urlImg				= $protocol . '://' . $host; ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
	<script src="https://thunkable.github.io/webviewer-extension/thunkableWebviewerExtension.js" type="text/javascript"></script>
</head>

<body>
	<?php include('webpart/sidebar.php') ?>
	<div id="main" class="container-fluid">
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

			<div class="card card-custom p-3 my-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">

					<!-- Judul -->
					<h5 class="card-title fw-bold text-primary text-center mb-3">
						<i class="bi bi-person-circle"></i> Profil Calon Siswa
					</h5>

					<!-- Data Profil -->
					<ul class="list-group list-group-flush w-100">
						<li class="list-group-item align-items-start">
							<div>
								<i class="bi bi-person-fill text-primary"></i>
								<span class="fw-bold ms-2">Nama</span>
							</div>
							<span><?= $nama ?></span>
						</li>

						<li class="list-group-item align-items-start">
							<div>
								<i class="bi bi-geo-alt-fill text-primary"></i>
								<span class="fw-bold ms-2">Alamat</span>
							</div>
							<span><?= $alamattinggal ?></span>
						</li>

						<li class="list-group-item align-items-start">
							<div>
								<i class="bi bi-telephone-fill text-primary"></i>
								<span class="fw-bold ms-2">HP Orangtua</span>
							</div>
							<span><?= $hportu ?></span>
						</li>
					</ul>

					<!-- Catatan -->
					<div class="alert alert-info mt-4 mb-0 text-center" role="alert">
						<i class="bi bi-info-circle-fill"></i>
						Jika terdapat kesalahan data, silakan hubungi bagian administrasi sekolah.
					</div>
				</div>
			</div>
		</main>
	</div>
	<?php include('webpart/js.php') ?>
	<script>
		// Sending a message
		ThunkableWebviewerExtension.postMessage('Hello from the Web Page!');

		// Sending a JSON object
		const data = { status: "success", score: 100 };
		ThunkableWebviewerExtension.postMessage(JSON.stringify(data));
	</script>
</body>

</html>