<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';

$mode = $_REQUEST["mode"];
$hitungTagihan	= $_REQUEST['hitungtagihan'];
$daftarBulan		= $_REQUEST['daftarbulan'];
$bulanDipilih		= $_REQUEST['bulandipilih'];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
	header("Location: ../signin.php?mode=" . $mode_akses);
	exit;
} else {
	$dbArisan		= 'skk_arisan';
	$titlePage	= 'Simulasi Tagihan - ' . APPS_NAME;
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

	$q						= "SELECT * FROM $dbArisan.periode WHERE aktif = 1";
	$runQ					= JalankanSQL('view', $q, 1);
	$kodePeriode	=	$runQ[0]->kode;
	$namaPeriode	= $runQ[0]->periode;
	$jumlahBulan	= $runQ[0]->jumlahbulan;
	$nominal			= $runQ[0]->nominal;
	$bulanAwal		= date('m', strtotime($runQ[0]->mulai));
	$tahunAwal		= date('Y', strtotime($runQ[0]->mulai));
	$bulanAkhir		= date('m', strtotime($runQ[0]->selesai));
	$tahunAkhir		= date('Y', strtotime($runQ[0]->selesai));

	function getNamaBulan($bulanNumber)
	{
		$namaBulan	= ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
		$bulanNumber = str_pad($bulanNumber, 2, '0', STR_PAD_LEFT);
		if ($bulanNumber >= 1 && $bulanNumber <= 12) {
			return $namaBulan[$bulanNumber - 1];
		} else {
			return '-';
		}
	}
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
	<style>
		.container {
			margin-bottom: 80px;
		}

		.grid {
			display: grid;
			grid-template-columns: repeat(12, 1fr);
			grid-template-rows: auto;
		}

		.header-image {
			grid-area: 1 / 1 / span 12 / 2;
			background-size: cover;
		}

		.header-content {
			grid-area: 2 / 1 / span 12 / 3;
			background-color: rgba(0, 0, 0, 0.5);
			color: white;
			padding: 20px;
		}

		.card {
			min-height: 120px;
		}

		@media (min-width: 1024px) and (max-width: 1366px) {
			.card {
				background-size: 100% auto;
				margin-top: 50px;
			}
		}

		#btnfloat {
			position: fixed;
			bottom: 80px;
			right: 20px;
			display: none;
		}
	</style>
</head>

<body>
	<main>
		<?php include('webpart/topbar.php') ?>
		<div class="container">
			<header>
				<div class="row pt-5 no-gutters">
					<div class="card mt-2 border-0 shadow" style="background-image: url('../assets/imgs/layered-waves.png'); background-size: cover; background-position: center;">
						<div class="card-body mt-2 flex-column text-white">
							<h4 class="card-title text-warning">SIMULASI KEWAJIBAN</h4>
						</div>
					</div>

					<div class="card p-3 rounded-4 shadow-lg">
						<div class="card-body flex-column align-items-start">
							<h5 class="card-title text-primary">Total kewajiban <?= FormatRupiah($hitungTagihan) ?></h5><br />
							<div class="card-text">
								<div class="col-auto">
									<p>Kewajiban yang dipilih sebanyak <b><?= $bulanDipilih ?></b> bulan <b class="text-primary"><i><?= $daftarBulan ?>.</i></b></p>
									<p>Arisan Kalam Kudus telah menerima pembayaran melalui Virtual Account BCA. Silahkan transfer ke nomor VA Anda, <b><?= $nomorVa ?></b>, sejumlah <b><?= FormatRupiah($hitungTagihan) ?></b>.</p>
									<p>Terima kasih,<br />Tuhan memberkati</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</header>
		</div>
	</main>
	<?php include('webpart/navbar_bottom.php') ?>
	<?php include('webpart/js.php') ?>
	<script>
		$(document).ready(function() {
			$(window).scroll(function() {
				let scrollPosition = $(window).scrollTop();
				if (scrollPosition > 20) {
					$("#btnfloat").fadeIn(400);
				} else {
					$("#btnfloat").fadeOut(400);
				}
			});
		});
	</script>
</body>

</html>