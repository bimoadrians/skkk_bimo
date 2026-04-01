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
$titleHeader	= 'Profil';
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

$alert	= '';
if (isset($_GET['status'])) {
	if ($_GET['status'] === 'success') {
		$alert = '<div id="alertBox" class="alert alert-success animate__animated animate__fadeInUp" role="alert">Data berhasil diperbarui.</div>';
	} elseif ($_GET['status'] === 'error') {
		$alert = '<div id="alertBox" class="alert alert-danger animate__animated animate__fadeInUp" role="alert">Gagal memperbarui data.</div>';
	}
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$inputNama		= trim($_POST['nama']);
	$inputAlamat	= trim($_POST['alamat']);
	$inputTelp		= trim($_POST['telp']);
	$sqlUpdate		= "UPDATE peserta SET nama = ?, alamat = ?, telp = ? WHERE id = ?";
	$stmt		= $koneksi->prepare($sqlUpdate);
	$stmt->bind_param('sssi', $inputNama, $inputAlamat, $inputTelp, $idPeserta);
	if ($stmt->execute()) {
		header("Location: profil.php?mode=aksesmobileapps&user_id=" . $userId . "&status=success");
		exit;
	} else {
		header("Location: profil.php?mode=aksesmobileapps&user_id=" . $userId . "&status=error");
		exit;
	}
}
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
				<div class="card shadow rounded-4 animate__animated animate__fadeInUp border-0">
					<div class="card-header bg-primary text-white rounded-top-4">
						<h5 class="mb-0">DATA PESERTA ARISAN</h5>
					</div>

					<div class="container position-relative">
						<div class="card-body">
							<?= $alert ?>
							<form name="profil" action="profil.php?mode=aksesmobileapps&user_id=<?= $userId ?>" id="formData" method="post" enctype="multipart/form-data">
								<div class="mb-3">
									<label for="nama" class="form-label fw-bold">Nama</label>
									<input type="text" class="form-control" id="nama" name="nama" value="<?= $nama ?>" maxlength="40" required>
									<div class="invalid-feedback">Nama tidak boleh mengandung karakter yang dilarang.</div>
								</div>

								<div class="mb-3">
									<label for="alamat" class="form-label fw-bold">Alamat</label>
									<input type="text" class="form-control" id="alamat" name="alamat" value="<?= $alamat ?>" maxlength="50" required>
									<div class="invalid-feedback">Alamat tidak boleh mengandung karakter yang dilarang.</div>
								</div>

								<div class="mb-3">
									<label for="telp" class="form-label fw-semibold">Nomor HP</label>
									<input type="tel" class="form-control" id="telp" name="telp" value="<?= $telp ?>" aria-describedby="telpHelp" required>
									<div id="telpHelp" class="form-text fst-italic">Nomor HP yang terhubung dengan whatsapp</div>
								</div>

								<button type="submit" name="simpan" id="simpan" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
									<i class="bi bi-save me-2"></i> Simpan
								</button>
							</form>
						</div>
					</div>
				</div>
			</main>
		</div>
		<?php include('webpart/js.php') ?>
		<script>
		$(document).ready(function() {
			const forbidden = /['"<>;\\]/;

			function showError(input, message) {
				input.addClass('is-invalid');
				input.next('.invalid-feedback').text(message).show();
			}

			function clearError(input) {
				input.removeClass('is-invalid');
				input.next('.invalid-feedback').hide();
			}

			$('#nama').on('input', function() {
				let value = $(this).val();
				if (forbidden.test(value)) {
					showError($(this), "Nama tidak boleh mengandung karakter ', \", <, >, ;, atau \\.");
				} else {
					clearError($(this));
				}
			});

			$('#alamat').on('input', function() {
				let value = $(this).val();
				if (forbidden.test(value)) {
					showError($(this), "Alamat tidak boleh mengandung karakter ', \", <, >, ;, atau \\.");
				} else {
					clearError($(this));
				}
			});

			$('#telp').on('input', function() {
				let value = $(this).val();
				let cleaned = value.replace(/[^0-9]/g, '');
				$(this).val(cleaned);
				if (value !== cleaned) {
					showError($(this), "Nomor HP hanya boleh angka.");
				} else {
					clearError($(this));
				}
			});
		});

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
	</body>

</html>
