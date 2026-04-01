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

function getNamaBulan($bulanNumber)
{
	$namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
	$bulanNumber = str_pad($bulanNumber, 2, '0', STR_PAD_LEFT);
	if ($bulanNumber >= 1 && $bulanNumber <= 12) {
		return $namaBulan[$bulanNumber - 1];
	} else {
		return '-';
	}
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

	$totalTagihan = $jumlahNomor * $nominal;
	$namaBulan		= ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
	$bulan 				= ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
	$bulanIni			= date('m');
	$namaBulanIni	= '';
	for ($i = 1; $i <= 12; $i++) {
		($bulan[$i] == $bulanIni) ? $namaBulanIni = $namaBulan[$i] : '';
	}
	$tahunIni				= date('Y');
	$qBayar					= "SELECT * FROM $dbArisan.pembayaran WHERE id_peserta = $idPeserta ORDER BY id ASC";
	$runqBayar			= JalankanSQL('view', $qBayar, 36);
	$dataPembayaran	= $runqBayar;
	$tabContent			= "";

	$bulanSekarang	= date('m');
	$tahunSekarang	= date('Y');

	if (isset($_REQUEST['buatsimulasi'])) {
		$hitungTagihan	= 0;
		$pilihan				= $_POST['pilihx'];
		$daftarBulan		= array();
		$bulanDipilih		= 0;
		foreach ($pilihan as $i => $k) {
			if (isset($_POST['pilihx'][$i])) {
				$qTagihan			= "SELECT bulan, tahun, tagihan FROM $dbArisan.pembayaran WHERE id = " . $pilihan[$i];
				$runqTagihan	= JalankanSQL('view', $qTagihan, 1);
				$hitungTagihan += $runqTagihan[0]->tagihan;
				$daftarBulan[getNamaBulan($runqTagihan[0]->bulan) . ' ' . $runqTagihan[0]->tahun] = true;
				$bulanDipilih += 1;
			}
		}
		$daftarBulan = implode(", ", array_keys($daftarBulan));
		header("location: simulasi_detail.php?mode=" . $mode_akses . "&hitungtagihan=" . $hitungTagihan . "&daftarbulan=" . $daftarBulan . "&bulandipilih=" . $bulanDipilih);
	}
}

if (isset($_GET['logout'])) {
	unset($user_id);
	session_destroy();
	header("location: ../signin.php?mode=" . $mode_akses);
};
?>
<!DOCTYPE html>
<html lang=" en">

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
			/*display: none;*/
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

					<div class="row mt-3 d-flex justify-content-center">
						<div class="col-12">
							<div class="table-responsive">
								<small class="text-secondary fst-italic">** Kewajiban bulan ini maupun sebelumnya yang belum lunas otomatis terpilih oleh sistem.</small>
								<form method="post" action="simulasi.php?mode=<?= $mode_akses ?>" enctype="multipart/form-data">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th scope="col">Kewajiban</th>
												<th scope="col">Keterangan</th>
												<th scope="col">Nominal</th>
											</tr>
										</thead>
										<tbody>
											<?php foreach ($dataPembayaran as $p) { ?>
												<tr <?= ($p->lunas == 1) ? 'class="table-success"' : '' ?>>
													<td scope="row"><?= getNamaBulan($p->bulan) ?> <?= $p->tahun ?></td>
													<td>
														<?php if ($p->lunas == 1) {
															echo '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Lunas</span>';
														} else { ?>
															<div class="form-check">
																<input type="checkbox" name="pilihx[]" class="form-check-input" value="<?= $p->id ?>" id="pilih<?= $p->id ?>" <?= ($bulanSekarang >= $p->bulan && $tahunSekarang == $p->tahun && $p->lunas != 1) ? 'checked onclick="return false"' : '' ?>>
																<label class="form-check-label" for="pilih<?= $p->id ?>">
																	Pilih
																</label>
															</div>
														<?php	} ?>
													</td>
													<td><?= FormatRupiah($p->tagihan) ?></td>
												</tr>
											<?php } ?>
										</tbody>
									</table>
									<button type="submit" name="buatsimulasi" class="btn btn-danger btn-floating" id="btnfloat">
										<i class="bi bi-plus-circle-fill"></i> Hitung
									</button>
								</form>
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
		/*$(document).ready(function() {
		$(window).scroll(function() {
			let scrollPosition = $(window).scrollTop();
			if (scrollPosition > 20) {
				$("#btnfloat").fadeIn(400);
			} else {
				$("#btnfloat").fadeOut(400);
			}
		});
	});*/
	</script>
</body>

</html>