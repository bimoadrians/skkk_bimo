<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';
//include 'arisanconnection.php';

$mode = $_REQUEST["mode"];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$userId			= strtolower($_REQUEST['user_id']);
$today			= date('Y-m-d');
$hariIni		= date('d-m-Y');
$perintah		= 'view';
$limit			= 1;

$dbArisan			= 'socmyid_arisan_solo';
$titleHeader	= 'Home';
$sqlPeserta		= "SELECT * FROM $dbArisan.peserta WHERE user_id = '" . $userId . "'";
$dataUser			= JalankanSQLArisan($perintah, $sqlPeserta, $limit);
$idPeserta		= $dataUser[0]->id;
$nama					= $dataUser[0]->nama;
$alamat				= $dataUser[0]->alamat;
$telp					= $dataUser[0]->telp;
$jumlahNomor	= $dataUser[0]->jumlah_nomor;
$nomorPeserta	= $dataUser[0]->nomor_peserta;
$caraBayar		= $dataUser[0]->carabayar;
$nomorVa			= $dataUser[0]->nomor_va;
/*$resUser			= $koneksi->query($sqlPeserta);
if ($resUser->num_rows > 0) {
	while ($dataUser = $resUser->fetch_assoc()) {
		$idPeserta		= $dataUser['id'];
		$nama					= $dataUser['nama'];
		$alamat				= $dataUser['alamat'];
		$telp					= $dataUser['telp'];
		$jumlahNomor	= $dataUser['jumlah_nomor'];
		$nomorPeserta	= $dataUser['nomor_peserta'];
		$caraBayar		= $dataUser['carabayar'];
		$nomorVa			= $dataUser['nomor_va'];
	}
} else {
	$idPeserta		= '';
	$nama					= '';
	$alamat				= '';
	$telp					= '';
	$jumlahNomor	= '';
	$nomorPeserta	= '';
	$caraBayar		= '';
	$nomorVa			= '';
}*/



$qCount		= "SELECT COUNT(id) as numid FROM $dbArisan.notifikasi WHERE id_peserta = '" . $idPeserta . "'";
$qNum			= JalankanSQLArisan($perintah, $qCount, $limit);
$numNotif	= $qNum[0]->numid;
/*$resNum		= $koneksi->query($qCount);
while ($rowNum = $resNum->fetch_assoc()) {
	$numNotif	= $rowNum['numid'];
}*/

//PANGGIL PERIODE
$qPeriode			= "SELECT * FROM $dbArisan.periode WHERE aktif = 1";
$runQ					= JalankanSQLArisan($perintah, $qPeriode, $limit);
$kodePeriode	=	$runQ[0]->kode;
$namaPeriode	= $runQ[0]->periode;
$jumlahBulan	= $runQ[0]->jumlahbulan;
$nominal			= $runQ[0]->nominal;
$bulanAwal		= date('m', strtotime($runQ[0]->mulai));
$tahunAwal		= date('Y', strtotime($runQ[0]->mulai));

/*$resPeriode		= $koneksi->query($qPeriode);
while ($rowPeriode = $resPeriode->fetch_assoc()) {
	$kodePeriode	=	$rowPeriode[0]->kode;
	$namaPeriode	= $rowPeriode[0]->periode;
	$jumlahBulan	= $rowPeriode[0]->jumlahbulan;
	$nominal			= $rowPeriode[0]->nominal;
	$bulanAwal		= date('m', strtotime($rowPeriode[0]->mulai));
	$tahunAwal		= date('Y', strtotime($rowPeriode[0]->mulai));
}*/

$totalTagihan = $jumlahNomor * $nominal;
$namaBulan	= ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$bulan 			= ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
$bulanIni		= date('m');
$namaBulanIni	= '';
for ($i = 1; $i <= 12; $i++) {
	($bulan[$i] == $bulanIni) ? $namaBulanIni = $namaBulan[$i] : '';
}
$tahunIni		= date('Y');

//DATA TAGIHAN SAMPAI BULAN X
$qTagihan = "SELECT (SUM(a.tagihan) - IFNULL(SUM(c.nominal), 0)) AS jumlahtagihan
							FROM $dbArisan.pembayaran a
							LEFT JOIN $dbArisan.cicilan c ON a.id = c.id_pembayaran
							WHERE a.id_peserta = '" . $idPeserta . "'
							AND a.periode = '" . $kodePeriode . "'
							AND a.bulan BETWEEN '" . $bulanAwal . "' AND '" . $bulanIni . "'
							AND a.tahun BETWEEN '" . $tahunAwal . "' AND '" . $tahunIni . "'
							AND a.lunas <> 1";
$runqTagihan			= JalankanSQLArisan($perintah, $qTagihan, $limit);
$jumlahTagihan		= $runqTagihan[0]->jumlahtagihan;
/*		$resTagihan	= $koneksi->query($qTagihan);
if ($resTagihan->num_rows > 0) {
	while ($rowTagihan = $resTagihan->fetch_assoc()) {
		$jumlahTagihan		= $rowTagihan['jumlahtagihan'];
	}
} else {
	$jumlahTagihan		= 0;
}*/

$qLunas						= "SELECT tgl_bayar, lunas FROM $dbArisan.pembayaran WHERE id_peserta = $idPeserta AND bulan = '" . $bulanIni . "' AND tahun = '" . $tahunIni . "'";
$runqLunas				= JalankanSQLArisan($perintah, $qLunas, $limit);
$tglBayarBulanIni	= $runqLunas[0]->tgl_bayar;
$lunasBulanIni		= $runqLunas[0]->lunas;
/*
$resLunas	= $koneksi->query($qLunas);
if ($resLunas->num_rows > 0) {
	while ($rowLunas = $resLunas->fetch_assoc()) {
		$tglBayarBulanIni	= $rowLunas['tgl_bayar'];
		$lunasBulanIni		= $rowLunas['lunas'];
	}
} else {
	$tglBayarBulanIni	= 0;
	$lunasBulanIni		= 0;
}
*/
//INFORMAS TERKINI
$qInfo			= "SELECT * FROM $dbArisan.informasi ORDER BY id DESC";
$dataInfo		= JalankanSQLArisan($perintah, $qInfo, 3);
//$resInfo		= $koneksi->query($qInfo);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
</head>

<body>
	<main>
		<?php include('webpart/sidebar.php') ?>
		<div class="container-fluid">
			<main>
				<div class="row py-1 my-4 text-start">
					<div class="col-9">
						<h5>Halo,</h5>
						<h5 class="text-primary"><?= $nama ?></h5>
					</div>
					<div class="col-3">
						<img class="img-thumbnail rounded mx-auto d-block" src="../assets/imgs/logo.png" alt="">
					</div>
				</div>

				<div class="card p-2 my-3 bg-image rounded-4 shadow" style="background-image: url('../assets/imgs/card.png'); ">
					<div class="card-body flex-column text-white">
						<h6 class="card-title">Arisan Kalam Kudus <?= $namaPeriode ?></h6>
						<div class="card-text">
							<p class="fs-3 text-warning">Rp <?= str_replace(',', '.', number_format($totalTagihan, 0)) ?> <span class="text-light fs-6 fst-italic">(Kewajiban per bulan.)</span></p>
							<p class="fs-6">VA BCA: <span id="nomor-va"><?= $nomorVa ?></span> <button id="copy-button" class="btn btn-sm btn-warning"><i class="bi bi-clipboard"></i> Salin</button></p>
							<p><small>No. Arisan: <?= $nomorPeserta ?> (<?= $jumlahNomor ?> nomor)</small></p>
						</div>
					</div>
				</div>

				<div class="card p-2 my-3 rounded-4 shadow-lg">
					<div class="card-body flex-column align-items-start">
						<h5 class="card-title text-primary">Kewajiban <?= $namaBulanIni ?> <?= $tahunIni ?></h5>
						<div class="card-text">
							<div class="row">
								<div class="col-9">
									<p class="fs-6">Tagihan s/d bulan ini: Rp <?= str_replace(',', '.', number_format($jumlahTagihan, 0)) ?></p>
									<?php
									if (isset($tglBayarBulanIni)) {
										echo '<p class="fs-6">Tanggal pelunasan: ' . date('d-m-Y', strtotime($tglBayarBulanIni)) . '</p>';
									}
									?>
								</div>
								<div class="col-3 text-center">
									<?php if ($lunasBulanIni == 1) {
										echo '<h1 class="text-success"><i class="bi bi-check-circle-fill"></i></h1>';
										echo '<h6 class="text-success">LUNAS</h6>';
									} elseif ($lunasBulanIni == 0) {
										echo '<h1 class="text-danger"><i class="bi bi-cash"></i></h1>';
										echo '<h6 class="text-danger">BELUM LUNAS</h6>';
									} ?>
								</div>
							</div>
							<div class="row">
								<div class="col-auto">
									<div class="btn-group">
										<a href="transaksi.php?mode=<?= $mode_akses ?>" class="btn btn-primary"><i class="bi bi-eye"></i> Laporan</a>
										<a href="simulasi.php?mode=<?= $mode_akses ?>" class="btn btn-danger"><i class="bi bi-cash"></i> Simulasi</a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="card card-custom p-2 rounded-4 shadow-lg">
					<div class="card-body flex-column align-items-start">
						<h5 class="card-title text-primary">Informasi Terkini</h5>
						<div class="card-text">
							<ul class="list-group mb-3">
								<?php /*if ($resInfo->num_rows > 0) {
									while ($rowInfo = $resInfo->fetch_assoc()) {
										$judulBerita	= $rowInfo['judul'];
										$idBerita			= $rowInfo['id']; ?>
									<li class="list-group-item d-flex justify-content-between align-items-center">
										<div class="d-flex align-items-center">
											<!--<img src="../assets/imgs/logo.png" class="img-thumbnail rounded mr-3" alt="Thumbnail 1" width="50" height="50">-->
											<i class="bi bi-file-earmark-richtext-fill text-primary fs-1 rounded mr-3"></i>
											<div style="display: flex; flex-direction: column;">
												<p class="fw-bold ms-3 lh-sm">
													<span class="fs-6 text-primary"><?= $judulBerita ?></span>
													<br>
													<a href="info_detail.php?mode=<?= $mode_akses ?>&id=<?= $idBerita ?>" class="btn btn-sm btn-warning fw-bold mt-2"><i class="bi bi-arrow-right"></i> Baca selengkapnya</a>
												</p>
											</div>
										</div>
									</li>
									<?php }
								}*/ ?>
							</ul>
						</div>
					</div>
				</div>
			</main>
		</div>
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
		<?php if ($_SESSION['notif'] == TRUE) { ?>
			<script>
				let Welcome
				Swal.fire({
					title: 'Selamat datang <?= $userName ?>!',
					text: 'di aplikasi SKKK Surakarta.',
					html: 'Notifikasi ditutup dalam <b></b> detik',
					timer: 3000,
					timerProgressBar: true,
					didOpen: () => {
						Swal.showLoading();
						const timer = Swal.getPopup().querySelector("b");
						Welcome = setInterval(() => {
							timer.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
						}, 100);
					},
					willClose: () => {
						clearInterval(Welcome);
					}
				});
			</script>
		<?php
			unset($_SESSION['notif']);
		} ?>
</body>

</html>