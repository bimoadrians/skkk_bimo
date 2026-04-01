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
	$titlePage	= 'Rekap - ' . APPS_NAME;
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

	//PANGGIL PERIODE
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

	$qLunas				= "SELECT SUM(nominal) AS nominallunas FROM $dbArisan.pembayaran WHERE id_peserta = $idPeserta AND nominal > 0";
	$runqLunas		= JalankanSQL('view', $qLunas, 1);
	$nominalLunas	= $runqLunas[0]->nominallunas;

	$qRecentPay						= "SELECT * FROM $dbArisan.pembayaran WHERE id_peserta = $idPeserta AND lunas = 1 ORDER BY id DESC";
	$runqRecentPay				= JalankanSQL('view', $qRecentPay, 1);
	$nominalLunasTerkini	= $runqRecentPay[0]->nominal;
	$tglLunasTerkini			= $runqRecentPay[0]->tgl_bayar;
	$bayarKe							= $runqRecentPay[0]->pembayaran_ke;
	$bulanTerkini					= $runqRecentPay[0]->bulan;
	$tahunTerkini					= $runqRecentPay[0]->tahun;

	$namaBulan	= ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
	$bulan 			= ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
	$indexBulan = array_search($bulanTerkini, $bulan);
	if ($indexBulan !== false) {
		$namaBulanTerkini = $namaBulan[$indexBulan];
	}

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
		min-height: 280px;
	}

	@media (min-width: 1024px) and (max-width: 1366px) {
		.card {
			background-size: 100% auto;
			margin-top: 50px;
		}
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
							<h4 class="card-title text-warning"><?= $nama ?></h4>
							<div class="card-text">
								<p class="fs-3 text-warning">Rp <?= str_replace(',', '.', number_format($totalTagihan, 0)) ?> <span class="text-light fs-6 fst-italic">(Kewajiban per bulan.)</span></p>
								<p class="fs-6">VA BCA: <span id="nomor-va"><?= $nomorVa ?></span> <button id="copy-button" class="btn btn-sm btn-warning"><i class="bi bi-clipboard"></i> Salin</button></p>
								<p><small>No. Arisan: <?= $nomorPeserta ?> (<?= $jumlahNomor ?> nomor)</small></p>
							</div>
						</div>
					</div>
				</div>
			</header>
			<ul class="nav nav-pills mt-3 mb-2" id="pills-tab" role="tablist">
				<?php for ($i = $tahunAwal; $i <= $tahunAkhir; $i++) { ?>
				<li class="nav-item" role="presentation">
					<button class="nav-link <?= ($i == date('Y')) ? 'active' : '' ?>" id="pills-<?= $i ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?= $i ?>" type="button" role="tab" aria-controls="pills-home" aria-selected="<?= ($i == date('Y')) ? 'true' : 'false' ?>"><?= $i ?></button>
				</li>
				<?php } ?>
			</ul>
			<div class="tab-content mb-4" id="pills-tabContent">
				<?php
				$tahunIni = null;
				$no = 1;
				foreach ($dataPembayaran as $d) {
					$bulanIni = intval($d->bulan);
					if ($tahunIni != $d->tahun) {
						$tahunIni = $d->tahun; ?>
				<div class="tab-pane fade <?= ($d->tahun == date('Y')) ? 'show active' : '' ?>" id="pills-<?= $d->tahun ?>" role="tabpanel" aria-labelledby="pills-<?= $d->tahun ?>-tab" tabindex="0">
					<div class="card p-2 rounded-3 shadow-lg">
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>No</th>
										<th>Informasi</th>
									</tr>
								</thead>
								<tbody>
									<?php } //end if 
										?>
									<tr>
										<td><?= $no ?></td>
										<td>
											<div class="fw-bold text-primary"><?= getNamaBulan($d->bulan) ?> <?= $d->tahun ?></div>
											<small>
												<ul class="list-group list-group-flush">
													<li class="list-group-item d-flex justify-content-between align-items-start">
														<div class="ms-2 me-auto">
															<b>Tanggal: </b><?= (isset($d->tgl_bayar)) ? date('d-m-Y', strtotime($d->tgl_bayar)) : '-' ?>
															<br><b>Nominal: </b><?= FormatRupiah($d->nominal) ?>
															<br><b>Keterangan: </b><?= $d->keterangan ?>
														</div>
														<?= ($d->lunas == 1) ? '<span class="badge text-bg-success rounded-pill"><i class="bi bi-check-circle-fill"></i> Lunas</span>' : '' ?>
													</li>
												</ul>
											</small>
										</td>
									</tr>
									<?php if ($bulanIni == 12 and $tahunIni == $d->tahun) { ?>
								</tbody>
							</table>
						</div>
					</div> <!-- card -->
				</div><!-- tab -->
				<?php } // end if
										$no++;
									} ?>
			</div>
		</div>
	</main>
	<?php include('webpart/navbar_bottom.php') ?>
	<?php include('webpart/js.php') ?>
	<script>
	$(document).ready(() => {
		$('#copy-button').click(() => {
			const nomorVA = $('#nomor-va').text();
			navigator.clipboard.writeText(nomorVA);
			const pesanKonfirmasi = $('<p class="pesan-konfirmasi text-light">Nomor VA telah disalin!</p>');
			pesanKonfirmasi.hide();
			$('#copy-button').after(pesanKonfirmasi);
			pesanKonfirmasi.slideDown();
			setTimeout(() => {
				pesanKonfirmasi.slideUp(() => {
					pesanKonfirmasi.remove();
				});
			}, 2000);
		});
	});
	</script>
</body>

</html>