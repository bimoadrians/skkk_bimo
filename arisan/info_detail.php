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
	$dbArisan		= 'skk_arisan';
	$titlePage	= 'Informasi - ' . APPS_NAME;
	$userName		= $_SESSION['username'];
	$goUserid		= $_SESSION["goUserid"];
	$goStatus		= $_SESSION["goStatus"];
	$goPassword	= $_SESSION["goPassword"];
	$folder			= $_SESSION["folder"];
	$response		= $_SESSION["response"];

	$id_key				= $response[0]->id_key;
	$idPeserta		= $response[0]->id;
	$nama					= $response[0]->nama;
	$alamat				= $response[0]->alamat;
	$telp					= $response[0]->telp;
	$jumlahNomor	= $response[0]->jumlah_nomor;
	$nomorPeserta	= $response[0]->nomor_peserta;
	$caraBayar		= $response[0]->carabayar;
	$nomorVa			= $response[0]->nomor_va;

	$idInfo			= $_REQUEST['id'];
	$q					= "SELECT * FROM $dbArisan.informasi WHERE id = $idInfo";
	$runQ				= JalankanSQL('view', $q, 1);
	$judul			= $runQ[0]->judul;
	$tgl				= $runQ[0]->tgl;
	$konten			= $runQ[0]->konten;

	$qCount		= "SELECT COUNT(id) as numid FROM $dbArisan.notifikasi WHERE id_peserta = $idPeserta";
	$qNum			= JalankanSQL('view', $qCount, 1);
	$numNotif	= $qNum[0]->numid;
}


if (isset($_GET['logout'])) {
	unset($user_id);
	session_destroy();
	header("location: ../signin.php?mode=" . $mode_akses);
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
</head>

<body>
	<main>
		<?php include('webpart/topbar.php') ?>
		<div class="container-fluid">
			<div class="row pt-5 py-1 my-4 text-start">
				<div class="col-12 text-center">
					<h5 class="text-primary text-uppercase"><b><?= $judul ?></b></h5>
				</div>
			</div>

			<div class="card p-3 my-3 rounded-3 shadow">

				<p><?= $konten ?></p>
				<!--<div class="col-3 col-md-2 col-lg-2 col-xxl-2">
						<img class="img-thumbnail rounded-circle mx-auto d-block" src="../assets/imgs/logo.png" alt="">
					</div>
					<div class="col-9 col-md-10 col-lg-10 col-xxl-10">
						<div class="fs-5 fw-bold text-primary"><?= $userName; ?></div>
						<small class="text-secondary"><?= $telp ?></small>
					</div>-->

			</div>
		</div>

	</main>
	<?php include('webpart/navbar_bottom.php') ?>
	<?php include('webpart/js.php') ?>
</body>

</html>