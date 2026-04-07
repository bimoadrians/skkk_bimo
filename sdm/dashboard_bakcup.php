<?php

session_start();



include '../constant.php';

include '../config.php';

include '../core.php';



header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

header("Cache-Control: post-check=0, pre-check=0", false);

header("Pragma: no-cache");



$mode				= $_REQUEST['mode'];



if ($mode != $mode_akses) {

	header("Location: " . $skkksurakarta);

	die();

}



$userId			= strtoupper($_REQUEST['user_id']);

$titleHeader	= 'Beranda';

$today			= date('Y-m-d');

$hariIni		= date('d-m-Y');

$perintah		= 'view';

$limit			= 1;



$dbSdm					= 'skk_presensigukar';

$sqlUser				= "SELECT user_id, nama, nomorinduk, hp, foto, modul

									FROM $dbSdm.user

									WHERE user_id = '$userId'";

$dataUser				= JalankanSQL($perintah, $sqlUser, $limit);

$userId					= $dataUser[0]->user_id;

$nama						= $dataUser[0]->nama;

$wa							= $dataUser[0]->hp;

$nomorInduk			= $dataUser[0]->nomorinduk;

$modul					= $dataUser[0]->modul;

$foto						= $dataUser[0]->foto;



$sqlHariIni			= "SELECT bidston AS bidstonhariini, jamdatang, lokasidatang as lokasidatanghariini, lokasipulang as lokasipulanghariini,

									fotodatang, jampulang, fotopulang FROM $dbSdm.presensi

									WHERE user_id = '$userId'

									AND hari = '$today'";

$dataPresensi		= JalankanSQL($perintah, $sqlHariIni, $limit);

$jamDatang			= $dataPresensi[0]->jamdatang;

$bidston				= $dataPresensi[0]->bidstonhariini;

$lokasiDatang		= $dataPresensi[0]->lokasidatanghariini;

$fotoDatang			= $dataPresensi[0]->fotodatang;

$jamPulang			= $dataPresensi[0]->jampulang;

$lokasiPulang		= $dataPresensi[0]->lokasipulanghariini;

$fotoPulang			= $dataPresensi[0]->fotopulang;



$dataJs					= [

	'userId'				=> $userId,

	'nama' 					=> $nama,

	'wa'						=> $wa,

	'jamDatang'			=> date('d-m-Y H:i:s', strtotime($jamDatang)),

	'bidston'				=> $bidston,

	'lokasiDatang'	=> $lokasiDatang,

	'fotoDatang'		=> $fotoDatang,

	'jamPulang'			=> date('d-m-Y H:i:s', strtotime($jamPulang)),

	'lokasiPulang'	=> $lokasiPulang,

	'fotoPulang'		=> $fotoPulang

];

$dataArrayJs = [$dataJs];

$jsonDataJs	= json_encode($dataArrayJs);



$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';

$host = $_SERVER['HTTP_HOST'];

$uri = $_SERVER['REQUEST_URI'];



$url_lengkap	= $protocol . '://' . $host . $uri;

$urlImg				= $protocol . '://' . $host;

$urlPresensi	= 'https://pr.skk.my.id';

?>



<!DOCTYPE html>

<html lang="en">



	<head>

		<?php include('webpart/head.php') ?>

		<script src="https://thunkable.github.io/webviewer-extension/thunkableWebviewerExtension.js" type="text/javascript"></script>

	</head>



	<body>

		<?php include('webpart/sidebar.php') ?>

		<div class="container-fluid">

			<main>

				<div class="row mb-4 bg-primary p-2 text-white">

					<div class="row mt-2 mb-3">

						<div class="col-4 col-md-2">

							<img class="img-fluid rounded" src="<?= $urlPresensi . '/images/staff/' . $foto ?>" alt="">

						</div>

						<div class="col-8 col-md-10 text-start text-white">

							<h4 class="fw-bold"><?= $nama ?></h4>

							<hr>

							<h6>N.I.K: <?= $nomorInduk ?></h6>

							<h6><?= $userId ?></h6>

						</div>

					</div>

					<div class="row">

						<h4 class="lead fw-bold">KEHADIRAN KERJA</h4>

						<h6><?= $hariIni ?></h6>

					</div>

				</div>



				<div class="row mt-3 p-3">

					<div class="card mb-3">

						<div class="card-body">

							<h5 class="card-title fw-bold text-primary">DATANG</h5>

							<div class="row">

								<div class="col-4">

									<?= ($fotoDatang != NULL) ? '<img src="' . $urlPresensi . '/images/buktipresensi/' . $fotoDatang . '" class="img-fluid rounded">' : '<h1 class="text-secondary"><i class="bi bi-person-bounding-box"></i></h1>'; ?>

								</div>

								<div class="col-8">

									<p>Jam: <?= ($jamDatang != '0000-00-00 00:00:00') ? date('H:i:s', strtotime($jamDatang)) : '-'; ?></p>

									<p>Bidston: <?= $bidston ?></p>

									<p>Gedung: <?= ($lokasiDatang != '') ? $lokasiDatang : '-' ?></p>

								</div>

							</div>

						</div>

					</div>



					<div class="card mb-3">

						<div class="card-body">

							<h5 class="card-title fw-bold text-primary">PULANG</h5>

							<div class="row">

								<div class="col-4">

									<?= ($fotoPulang != NULL) ? '<img src="' . $urlPresensi . '/images/buktipresensi/' . $fotoPulang . '" class="img-fluid rounded">' : '<h1 class="text-secondary"><i class="bi bi-person-bounding-box"></i></h1>'; ?>

								</div>

								<div class="col-8">

									<p>Jam: <?= ($jamPulang != '0000-00-00 00:00:00') ? date('H:i:s', strtotime($jamPulang)) : '-'; ?></p>

									<p>Gedung: <?= ($lokasiPulang != '') ? $lokasiPulang : '-' ?></p>

								</div>

							</div>

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

