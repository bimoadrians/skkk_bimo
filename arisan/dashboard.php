<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';
include '../arisanconnection.php';

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
$titlePage		= 'Beranda';
$sqlPeserta		= "SELECT * FROM peserta WHERE user_id = '" . $userId . "'";
$resUser			= $koneksi->query($sqlPeserta);
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
	$jumlahNomor	= 0;
	$nomorPeserta	= '';
	$caraBayar		= '';
	$nomorVa			= '';
}

$qCount		= "SELECT COUNT(id) as numid FROM $dbArisan.notifikasi WHERE id_peserta = '" . $idPeserta . "'";
$resNum		= $koneksi->query($qCount);
while ($rowNum = $resNum->fetch_assoc()) {
	$numNotif	= $rowNum['numid'];
}

//PANGGIL PERIODE
$qPeriode			= "SELECT * FROM $dbArisan.periode WHERE aktif = 1";
$resPeriode		= $koneksi->query($qPeriode);
while ($rowPeriode = $resPeriode->fetch_assoc()) {
	$kodePeriode	=	$rowPeriode['kode'];
	$namaPeriode	= $rowPeriode['periode'];
	$jumlahBulan	= $rowPeriode['jumlahbulan'];
	$nominal			= $rowPeriode['nominal'];
	$bulanAwal		= date('m', strtotime($rowPeriode['mulai']));
	$tahunAwal		= date('Y', strtotime($rowPeriode['mulai']));
}

$totalTagihan = $jumlahNomor * $nominal;
$namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$bulanIni = date('n'); // 'n' menghasilkan 1 sampai 12 tanpa nol di depan
$namaBulanIni = $namaBulan[$bulanIni - 1]; // Kurangi 1 agar sesuai dengan indeks array (0-11)
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
$resTagihan	= $koneksi->query($qTagihan);
if ($resTagihan->num_rows > 0) {
	while ($rowTagihan = $resTagihan->fetch_assoc()) {
		$jumlahTagihan		= $rowTagihan['jumlahtagihan'];
	}
} else {
	$jumlahTagihan		= 0;
}

// Ambil bulan dan tahun sekarang dengan format yang sesuai database (01, 02, dst)
$bulanSekarang = date('m');
$tahunSekarang = date('Y');

$qLunas = "SELECT tgl_bayar, lunas FROM $dbArisan.pembayaran 
					WHERE id_peserta = '$idPeserta' 
					AND bulan = '$bulanSekarang' 
					AND tahun = '$tahunSekarang' 
					LIMIT 1";
$resLunas = $koneksi->query($qLunas);

// Reset variabel sebelum diisi data baru
$tglBayarBulanIni = null;
$lunasBulanIni = 0;

if ($resLunas && $resLunas->num_rows > 0) {
	$rowLunas = $resLunas->fetch_assoc();
	$tglBayarBulanIni = $rowLunas['tgl_bayar'];
	$lunasBulanIni = (int)$rowLunas['lunas'];
}

/*$qLunas						= "SELECT tgl_bayar, lunas FROM $dbArisan.pembayaran WHERE id_peserta = $idPeserta AND bulan = '" . $bulanIni . "' AND tahun = '" . $tahunIni . "'";
$resLunas					= $koneksi->query($qLunas);
if ($resLunas->num_rows > 0) {
	while ($rowLunas = $resLunas->fetch_assoc()) {
		$tglBayarBulanIni	= $rowLunas['tgl_bayar'];
		$lunasBulanIni		= $rowLunas['lunas'];
	}
} else {
	$tglBayarBulanIni	= 0;
	$lunasBulanIni		= 0;
}*/

//cek lunas semua atau belum pernah bayar
// CEK STATUS LUNAS TOTAL (Semua bulan dalam periode)
$isLunasSemua = false;
$qCekSemua = "SELECT MIN(lunas) as status_total FROM $dbArisan.pembayaran WHERE id_peserta = '$idPeserta' AND periode = '$kodePeriode'";
$resCekSemua = $koneksi->query($qCekSemua);

if ($resCekSemua->num_rows > 0) {
	$rowCek = $resCekSemua->fetch_assoc();
	// Jika nilai terkecil adalah 1, berarti tidak ada angka 0 (semua lunas)
	if ($rowCek['status_total'] == 1 && $rowCek['status_total'] !== null) {
		$isLunasSemua = true;
	}
}

//INFORMAS TERKINI
$qInfo			= "SELECT * FROM $dbArisan.informasi ORDER BY id DESC";
$resInfo		= $koneksi->query($qInfo);
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('webpart/head.php') ?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

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

		/*.card {
			min-height: 280px;
		}*/

		@media (min-width: 1024px) and (max-width: 1366px) {
			.card {
				background-size: 100% auto;
				margin-top: 50px;
			}
		}

		.card {
			animation: fadeInUp 0.8s ease;
		}

		@keyframes fadeInUp {
			from {
				transform: translateY(20px);
				opacity: 0;
			}

			to {
				transform: translateY(0);
				opacity: 1;
			}
		}

		.wave-footer {
			position: relative;
			margin-top: -20px;
		}

		.wave-footer svg {
			display: block;
			width: 100%;
			height: 50px;
		}

		</style>
	</head>

	<body>
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

				<div class="card animate__animated animate__fadeInUp p-2 my-3 bg-image rounded-4 shadow" style="background-image: url('../assets/imgs/card.png'); ">
					<div class="card-body flex-column text-white">
						<h6 class="card-title">Arisan Kalam Kudus <?= $namaPeriode ?></h6>
						<div class="card-text">
							<p class="fs-3 text-warning">Rp <?= number_format((float)($totalTagihan ?? 0), 0, ',', '.') ?> <span class="text-light fs-6 fst-italic">(Kewajiban per bulan.)</span></p>
							<p class="fs-6">VA BCA: <span id="nomor-va"><?= $nomorVa ?></span> <button id="copy-button" class="btn btn-sm btn-warning"><i class="bi bi-clipboard"></i> Salin</button></p>
							<p><small>No. Arisan: <?= $nomorPeserta ?> (<?= $jumlahNomor ?> nomor)</small></p>
						</div>
					</div>
				</div>

				<div class="card card-custom p-2 my-3 rounded-4 shadow-lg <?= ($isLunasSemua) ? 'card-success' : '' ?>">
					<div class="card-body flex-column align-items-start">
						<h5 class="card-title <?= ($isLunasSemua) ? 'text-success' : 'text-primary' ?>">
							<?= ($isLunasSemua) ? 'Status Keanggotaan' : 'Kewajiban ' . $namaBulanIni . ' ' . $tahunIni ?>
						</h5>

						<div class="card-text w-100">
							<div class="row align-items-center">
								<div class="col-9">
									<p class="fs-6">Tagihan s/d bulan ini: Rp <?= number_format((float)($jumlahTagihan ?? 0), 0, ',', '.') ?></p>

									<?php if ($isLunasSemua): ?>
									<p class="fs-6 text-success fw-bold">Status: Seluruh Periode Lunas</p>
									<?php elseif ($lunasBulanIni === 1): ?>
									<p class="fs-6 text-success">
										<i class="bi bi-calendar-check"></i> Pelunasan:
										<?= (!empty($tglBayarBulanIni) && $tglBayarBulanIni != '0000-00-00')
											? date('d-M-Y', strtotime($tglBayarBulanIni))
											: 'Sudah Terbayar' ?>
									</p>
									<?php else: ?>
									<p class="fs-6 text-danger fst-italic">Belum ada pelunasan bulan ini</p>
									<?php endif; ?>
								</div>

								<div class="col-3 text-center">
									<?php if ($isLunasSemua): ?>
									<h1 class="text-success"><i class="bi bi-patch-check-fill"></i></h1>
									<h6 class="text-success">LUNAS</h6>
									<?php elseif ($lunasBulanIni == 1): ?>
									<h1 class="text-success"><i class="bi bi-check-circle-fill"></i></h1>
									<h6 class="text-success">LUNAS</h6>
									<?php else: ?>
									<h1 class="text-danger"><i class="bi bi-cash"></i></h1>
									<h6 class="text-danger">BELUM LUNAS</h6>
									<?php endif; ?>
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
								<?php if ($resInfo->num_rows > 0) {
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
							} ?>
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
		<?php if (isset($_SESSION['notif']) && $_SESSION['notif'] == TRUE) { ?>
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
