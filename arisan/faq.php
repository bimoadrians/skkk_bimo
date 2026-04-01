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
	$titlePage	= 'FAQ - ' . APPS_NAME;
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
					<h5 class="text-primary"><b>FREQUENTLY ASKED QUESTION</b></h5>
				</div>
			</div>

			<div class="card p-3 mt-5 rounded-3 shadow">
				<div class="container">
					<div class="accordion accordion-flush" id="faq-accordion">
						<div class="accordion-item">
							<h2 class="accordion-header" id="heading-1">
								<button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-1" aria-expanded="false" aria-controls="collapse-1">
									<i class="bi bi-patch-question me-2 fs-3"></i>
									Kapan tanggal pembayaran maksimal tiap bulannya?
								</button>
							</h2>
							<div id="collapse-1" class="accordion-collapse collapse" aria-labelledby="heading-1" data-bs-parent="#faq-accordion">
								<div class="accordion-body">
									Pembayaran maksimal di tanggal 20 setiap bulannya. Jika belum terbayar, akan diakumulasi di bulan berikutnya.
								</div>
							</div>
						</div>
						<div class="accordion-item">
							<h2 class="accordion-header" id="heading-2">
								<button class="accordion-button collapsed text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-2" aria-expanded="false" aria-controls="collapse-2">
									<i class="bi bi-patch-question me-2 fs-3"></i>
									Apakah saya bisa mengganti nomor arisan saat periode sedang berjalan?
								</button>
							</h2>
							<div id="collapse-2" class="accordion-collapse collapse" aria-labelledby="heading-2" data-bs-parent="#faq-accordion">
								<div class="accordion-body">
									Tidak bisa.
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
	<?php include('webpart/navbar_bottom.php') ?>
	<?php include('webpart/js.php') ?>
</body>

</html>