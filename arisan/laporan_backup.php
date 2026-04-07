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
$userId				= strtolower($_REQUEST['user_id']);
$dbArisan			= 'socmyid_arisan_solo';
$titleHeader	= 'Laporan Pembayaran';
$sqlPeserta		= "SELECT * FROM peserta WHERE user_id = '" . $userId . "'";
$resUser			= $koneksi->query($sqlPeserta);
if ($resUser->num_rows > 0) {
	while ($dataUser = $resUser->fetch_assoc()) {
		$idPeserta		= $dataUser['id'];
		$nama					= $dataUser['nama'];
		$kodePeserta	= $dataUser['kode_peserta'];
		$alamat				= $dataUser['alamat'];
		$telp					= $dataUser['telp'];
		$jumlahNomor	= $dataUser['jumlah_nomor'];
		$nomorPeserta	= $dataUser['nomor_peserta'];
		$caraBayar		= $dataUser['carabayar'];
		$nomorVa			= $dataUser['nomor_va'];
		$customBayar	= $dataUser['custom_bayar'];
		$jumlahBulanPeserta	= $dataUser['jumlah_bulan'];
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
	$bulanAkhir		= date('m', strtotime($rowPeriode['selesai']));
	$tahunAkhir		= date('Y', strtotime($rowPeriode['selesai']));
}

$totalTagihan = $jumlahNomor * $nominal;
$namaBulan		= ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$bulan 				= ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
$bulanIni			= date('m');
$namaBulanIni	= '';
for ($i = 1; $i <= 12; $i++) {
	($bulan[$i] == $bulanIni) ? $namaBulanIni = $namaBulan[$i] : '';
}
$tahunIni				= date('Y');

$qBayar					= "SELECT * FROM pembayaran WHERE id_peserta = " . $idPeserta . " ORDER BY id ASC";
$resPembayaran	= $koneksi->query($qBayar);
$tabContent			= "";

$qLunas				= "SELECT SUM(nominal) AS nominallunas FROM pembayaran WHERE id_peserta = " . $idPeserta . " AND nominal > 0";
$resLunas		= $koneksi->query($qLunas);
while ($rowLunas = $resLunas->fetch_assoc()) {
	$nominalLunas	= $rowLunas['nominallunas'];
}

$qRecentPay						= "SELECT * FROM pembayaran WHERE id_peserta = " . $idPeserta . " AND lunas = 1 ORDER BY id DESC LIMIT 1";
$resRecentPay	= $koneksi->query($qRecentPay);
while ($rowRecentPay = $resRecentPay->fetch_assoc()) {
	$nominalLunasTerkini	= $rowRecentPay['nominal'];
	$tglLunasTerkini			= $rowRecentPay['tgl_bayar'];
	$bayarKe							= $rowRecentPay['pembayaran_ke'];
	$bulanTerkini					= $rowRecentPay['bulan'];
	$tahunTerkini					= $rowRecentPay['tahun'];
}

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

$alert	= '';
if (isset($_GET['status'])) {
	if ($_GET['status'] === 'success') {
		$alert = '<div id="alertBox" class="alert alert-success animate__animated animate__fadeInUp" role="alert">Anda berhasil mengatur nominal pembayaran.</div>';
	} elseif ($_GET['status'] === 'error') {
		$alert = '<div id="alertBox" class="alert alert-danger animate__animated animate__fadeInUp" role="alert">Anda gagal mengatur nominal pembayaran.</div>';
	}
}

// Pada bagian POST di laporan.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Pastikan input hanya angka
	$inputCustomBayar = preg_replace("/[^0-9]/", "", $_POST['custom_bayar']);
	$sqlCustomBayar   = "UPDATE peserta SET custom_bayar = ? WHERE id = ?";
	$stmt   = $koneksi->prepare($sqlCustomBayar);

	// Gunakan 'd' untuk double atau 'i' untuk integer, tapi pastikan variabelnya bersih
	$valBayar = (float)$inputCustomBayar;
	$stmt->bind_param('di', $valBayar, $idPeserta);

	if ($stmt->execute()) {
		// PENTING: Gunakan redirect yang bersih tanpa karakter aneh
		header("Location: laporan.php?mode=aksesmobileapps&user_id=" . trim($userId) . "&status=success");
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('webpart/head.php') ?>
		<script src="https://thunkable.github.io/webviewer-extension/thunkableWebviewerExtension.js" type="text/javascript"></script>
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

		.card {
			min-height: 280px;
		}

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
		<div class="container my-5">
			<main>
				<div class="card shadow animate__animated animate__fadeInUp rounded-4 border-0">
					<div class="card-header bg-primary text-white rounded-top-4">
						<h5 class="mb-0">PELUNASAN TERKINI</h5>
					</div>
					`
					<div class="card-body bg-white">
						<h5 class="fw-bold"><?= $nama ?></h5>
						<p class="text-muted mb-1">Total kewajiban yang sudah dibayar:</p>
						<h3 class="bg-warning text-dark rounded-2 p-2 fw-bold"><?= FormatRupiah($nominalLunas) ?></h3>

						<hr>
						<p class="mb-0">
							Pembayaran terkini: <span class="fw-bold text-dark"><?= FormatRupiah($nominalLunasTerkini) ?></span>
							<small class="text-muted">(<?= (!empty($tglLunasTerkini) && $tglLunasTerkini != '0000-00-00') ? date('d-M-Y', strtotime($tglLunasTerkini)) : 'Belum ada data' ?>)</small>
						</p>

						<hr class="my-4">
						<h5 class="fw-bold text-primary">Langkah Custom Pembayaran Arisan</h5>
						<ol class="list-group list-group-numbered">
							<li class="list-group-item list-group-item-primary">Pilih nominal di bawah ini. </li>
							<li class="list-group-item list-group-item-primary">Simpan pembayaran</li>
							<li class="list-group-item list-group-item-primary">Cek tagihan di VA BCA Anda.</li>
							<li class="list-group-item list-group-item-primary">Lakukan proses pembayaran</li>
						</ol>
						<?= $alert ?>
						<form id="custompembayaran" action="laporan.php?mode=aksesmobileapps&user_id=<?= $userId ?>" method="post" class="mt-4">
							<div class="mt-2 mb-3">
								<label for="custom_bayar" class="form-label fw-bold text-primary">Custom Pembayaran</label>
								<select name="custom_bayar" id="custom_bayar" class="form-select">
									<?php
								$tagihanPerBulan = (int)$jumlahNomor * (int)$nominal;
								$standar = (int)$jumlahNomor * (int)$jumlahBulanPeserta * (int)$nominal;
								$totalKewajiban = $standar - (int)$nominalLunas;

								// Pastikan nilai tidak nol untuk menghindari Infinite Loop
								if ($tagihanPerBulan > 0 && $totalKewajiban > 0) {
									for ($i = $tagihanPerBulan; $i <= $totalKewajiban; $i += $tagihanPerBulan) {
										$selected = ($customBayar == $i) ? 'selected' : '';
										echo '<option value="' . $i . '" ' . $selected . '>' . number_format($i, 0, ',', '.') . '</option>';
									}
								} else {
									echo '<option value="0">Tidak ada tagihan</option>';
								}
								?>
								</select>
								<div class="form-text fst-italic mt-1">
									Pilihan nominal adalah jumlah nomor x Rp 500.000. Maksimal pilihan nominal berdasarkan tagihan yang belum terbayar.
								</div>
							</div>

							<button type="button" onclick="this.form.submit();" class="btn btn-primary w-100">
								<i class="bi bi-save me-2"></i> Simpan
							</button>
						</form>

					</div>

					<!-- Dekorasi SVG -->
					<div class="wave-footer mt-3">
						<svg viewBox="0 0 500 150" preserveAspectRatio="none" style="width: 100%; height: 50px;">
							<path d="M0.00,49.98 C150.00,150.00 349.29,-50.00 500.00,49.98 L500.00,150.00 L0.00,150.00 Z" style="stroke: none; fill: #dc3545;"></path>
						</svg>
					</div>
				</div>

				<!-- Tab Tahun -->
				<ul class="nav nav-pills mt-4 mb-3" id="pills-tab" role="tablist">
					<?php for ($i = $tahunAwal; $i <= $tahunAkhir; $i++) { ?>
					<li class="nav-item" role="presentation">
						<button class="nav-link <?= ($i == date('Y')) ? 'active' : '' ?>" id="pills-<?= $i ?>-tab" data-bs-toggle="pill" data-bs-target="#pills-<?= $i ?>" type="button" role="tab" aria-selected="<?= ($i == date('Y')) ? 'true' : 'false' ?>"><?= $i ?></button>
					</li>
					<?php } ?>
				</ul>

				<!-- Tab Isi -->
				<div class="tab-content mb-4" id="pills-tabContent">
					<?php
				$currentYear = null;
				$firstYear = true;

				if ($resPembayaran->num_rows > 0) {
					while ($dataPembayaran = $resPembayaran->fetch_assoc()) {
						// Jika tahun berubah, tutup div tahun sebelumnya (kecuali untuk data pertama)
						if ($currentYear !== $dataPembayaran['tahun']) {
							if (!$firstYear) {
								echo '      </ol>
                          </div>
                        </div>';
							}

							$currentYear = $dataPembayaran['tahun'];
							$activeClass = ($currentYear == date('Y')) ? 'show active' : '';
							$firstYear = false;
				?>

					<div class="tab-pane fade <?= $activeClass ?>" id="pills-<?= $currentYear ?>" role="tabpanel" tabindex="0">
						<div class="card p-2 rounded-4 shadow-sm">
							<ol class="list-group list-group-numbered">
								<?php } ?>

								<li class="list-group-item d-flex justify-content-between align-items-start border-0 border-bottom p-3">
									<div class="ms-2 me-auto">
										<div class="fw-bold"><?= getNamaBulan($dataPembayaran['bulan']) ?> <?= $dataPembayaran['tahun'] ?></div>
										<span class="fs-6 text-primary">
											<?= (!empty($dataPembayaran['tgl_bayar']) && $dataPembayaran['tgl_bayar'] != '0000-00-00')
													? 'Tgl. Bayar: ' . date('d-M-Y', strtotime($dataPembayaran['tgl_bayar']))
													: '-' ?>
										</span>
									</div>

									<div class="d-flex align-items-center gap-2">
										<span class="badge bg-<?= ($dataPembayaran['lunas'] == 1) ? 'primary' : 'danger' ?> rounded-pill p-2">
											<?= FormatRupiah($dataPembayaran['nominal']) ?>
										</span>

										<?php if ($dataPembayaran['lunas'] == 1):
												$namaPesertaUnder = str_replace(' ', '_', $nama);
												$fileUrl = '/arisan_solo/arisan_files/kuitansi/' . $kodePeserta . '_' . $dataPembayaran['tahun'] . '_' . $dataPembayaran['bulan'] . '_Kuitansi_' . $namaPesertaUnder . '.pdf';
											?>
										<a href="<?= $fileUrl ?>" class="btn btn-sm btn-success d-flex align-items-center" target="_parent">
											<i class="bi bi-file-earmark-pdf-fill me-1"></i> Kuitansi
										</a>
										<?php endif; ?>
									</div>
								</li>

								<?php
						} // End while

						// TUTUP TAG TERAKHIR: Penting agar tag penutup selalu tercetak di akhir data
						echo '      </ol>
              </div>
            </div>';
					} else {
						echo '<div class="alert alert-info">Belum ada data pembayaran.</div>';
					}
							?>
						</div>
			</main>
		</div>

		<?php include('webpart/js.php') ?>
		<script>
		const alertBox = document.getElementById('alertBox');
		if (alertBox) {
			setTimeout(() => {
				alertBox.classList.remove('animate__fadeInUp');
				alertBox.classList.add('animate__fadeOut');
				setTimeout(() => {
					alertBox.style.display = 'none';
				}, 1000); // Waktu untuk fadeOut (1 detik)
			}, 3000); // Tampil selama 3 detik sebelum mulai fadeOut
		}
		</script>
		<script>
			// Sending a message
			ThunkableWebviewerExtension.postMessage('Hello from the Web Page!');

			// Sending a JSON object
			const data = { status: "success", score: 100 };
			ThunkableWebviewerExtension.postMessage(JSON.stringify(data));
		</script>
	</body>

</html>
