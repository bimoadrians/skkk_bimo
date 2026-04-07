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
$titlePage		= 'Profil Siswa - ' . APPS_NAME;
$titleHeader	= 'Profil Siswa';
$userId				= strtoupper($_REQUEST['user_id']);
if($userId == 'DEMO'){
    $userId = '46257';
}
$today				= date('Y-m-d');
$hariIni			= date('d-m-Y');
$perintah			= 'view';
$limit				= 1;
$sqlUser			= "SELECT
									s.replid AS replid,
									s.nis AS id_key,
									s.idkelas,
									nama,
									telponsiswa AS telpon,
									hpsiswa,
									hportu,
									hportu2,
									s.info1,
									s.info2,
									kelas AS namakelas,
									alamatsiswa AS alamattinggal,
									tingkat AS namatingkat,
									s.keterangan
								FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t
								WHERE s.idkelas = k.replid
									AND t.replid = k.idtingkat
									AND s.nis = '{$userId}'";
$dataUser					= JalankanSQL($perintah, $sqlUser, $limit);
$id_key						= $dataUser[0]->id_key;
$nama							= $dataUser[0]->nama;
$telpon						= $dataUser[0]->telpon;
$hpsiswa					= $dataUser[0]->hpsiswa;
$hpayah						= $dataUser[0]->hportu;
$hpibu						= $dataUser[0]->hportu2;
$namatingkat			= $dataUser[0]->namatingkat;
$idKelas					= $dataUser[0]->idkelas;
$namakelas				= $dataUser[0]->namakelas;
$alamattinggal		= $dataUser[0]->alamattinggal;
$keterangansiswa	= $dataUser[0]->keterangan;
//END EKSTRAK RESPONSE

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
			<div class="row py-1 my-4 text-start">
				<div class="col-9">
					<h5 class="text-primary"><?= $nama ?></h5>
					<p class="fs-6">Kelas : <?= $namakelas ?></p>
				</div>
				<div class="col-3">
					<img class="img-thumbnail rounded mx-auto d-block" src="../assets/imgs/logo.png" alt="">
				</div>
			</div>

			<div class="card card-custom p-3 my-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">

					<!-- Judul -->
					<h5 class="card-title fw-bold text-primary text-center mb-3">
						<i class="bi bi-person-circle"></i> Profil Siswa
					</h5>

					<!-- Data Profil -->
					<ul class="list-group list-group-flush w-100">
						<li class="list-group-item d-flex justify-content-between align-items-start">
							<div>
								<i class="bi bi-person-fill text-primary"></i>
								<span class="fw-bold ms-2">Nama</span>
							</div>
							<span><?= $nama ?></span>
						</li>

						<li class="list-group-item d-flex justify-content-between align-items-start">
							<div>
								<i class="bi bi-geo-alt-fill text-success"></i>
								<span class="fw-bold ms-2">Alamat</span>
							</div>
							<span><?= $alamattinggal ?></span>
						</li>

						<li class="list-group-item d-flex justify-content-between align-items-start">
							<div>
								<i class="bi bi-telephone-fill text-warning"></i>
								<span class="fw-bold ms-2">HP Siswa</span>
							</div>
							<span><?= $hpsiswa ?></span>
						</li>

						<li class="list-group-item d-flex justify-content-between align-items-start">
							<div>
								<i class="bi bi-telephone-fill text-success"></i>
								<span class="fw-bold ms-2">HP Ayah</span>
							</div>
							<span><?= $hpayah ?></span>
						</li>

						<li class="list-group-item d-flex justify-content-between align-items-center">
							<div>
								<i class="bi bi-telephone-fill text-info"></i>
								<span class="fw-bold ms-2">HP Ibu</span>
							</div>
							<span><?= $hpibu ?></span>
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