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
$titlePage		= 'Konseling - ' . APPS_NAME;
$titleHeader	= 'Konseling';
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
									hportu AS hp,
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
$hp1							= $dataUser[0]->hp;
$hp2							= $dataUser[0]->info1;
$hp3							= $dataUser[0]->info2;
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

			<div class="card card-custom p-2 my-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-center text-center">

					<!-- Gambar Ilustrasi Konseling -->
					<img src="../assets/imgs/counseling.png" alt="Konseling"
						class="img-fluid mb-3" style="max-width: 230px;">

					<!-- Judul -->
					<h5 class="card-title fw-bold text-primary">Halaman Konseling Siswa</h5>

					<!-- Pesan Pemberitahuan -->
					<p class="card-text text-muted px-2">
						Halo <b><?= $nama ?></b>, fitur untuk memantau
						<b>perkembangan karakter dan konseling siswa</b> saat ini masih dalam tahap
						<b>pengembangan</b>.
					</p>
					<p class="card-text text-muted">
						Orangtua nantinya dapat melihat catatan konseling, perkembangan karakter,
						dan laporan pembinaan siswa melalui halaman ini.
					</p>

					<!-- Progress Status -->
					<div class="mt-3 w-75 mx-auto">
						<div class="progress" style="height: 25px;">
							<div class="progress-bar progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 40%"></div>
						</div>
						<p class="small text-muted mt-2">Sedang dalam tahap pengembangan...</p>
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