<?php

session_start();



include '../constant.php';

include '../config.php';

include '../core.php';



$mode = $_REQUEST['mode'];

if ($mode != $mode_akses) {

	header('Location: ' . $skkksurakarta);

	die();

}



$dbPresensi			= 'skk_presensigukar';

$titlePage			= 'Form Cuti - ' . APPS_NAME;

$userId					= $_REQUEST['user_id'];

$ins						= 'insert';

$vie						= 'view';

$limit					= 1;

$queryUser			= "SELECT nomorinduk, nama, departemen FROM $dbPresensi.user WHERE user_id = '" . $userId . "'";

$runUser				= JalankanSQL($vie, $queryUser, $limit);

$nomorInduk			= $runUser[0]->nomorinduk;

$nama						= $runUser[0]->nama;

$dept						= $runUser[0]->departemen;

$tglPengajuan		= date('Y-m-d', strtotime($_REQUEST['tglPengajuan']));

$tglCutiMulai		= date('Y-m-d', strtotime($_REQUEST['tglCutiMulai']));

$tglCutiSelesai	= date('Y-m-d', strtotime($_REQUEST['tglCutiSelesai']));

$alasan					= $_REQUEST['alasan'];



$qNumCuti				= "SELECT COUNT(*) AS jumlahdata FROM $dbPresensi.cuti WHERE user_id = '" . $userId . "'";

$numCuti				= JalankanSQL($vie, $qNumCuti, $limit);

$cutiKu					= $numCuti[0]->jumlahdata;

$queryDataCuti	= "SELECT * FROM $dbPresensi.cuti WHERE user_id = '" . $userId . "'";

$runDataCuti		= JalankanSQL($vie, $queryDataCuti, $cutiKu);

$successQuery		= 0;



function set_flashdata($key, $value)

{

	$_SESSION['flashdata'][$key] = $value;

}



function get_flashdata($key)

{

	if (isset($_SESSION['flashdata'][$key])) {

		$value = $_SESSION['flashdata'][$key];

		unset($_SESSION['flashdata'][$key]);

		return $value;

	} else {

		return null;

	}

}



if (isset($_REQUEST['submitcuti'])) {

	$qCuti						= "INSERT INTO $dbPresensi.cuti

												SET	user_id						=	'" . $userId . "',

														nomorinduk				= '" . $nomorInduk . "',

														nama							= '" . $nama . "',

														departemen				= '" . $dept . "',

														tgl_pengajuan			= '" . $tglPengajuan . "',

														tgl_cuti_mulai		= '" . $tglCutiMulai . "',

														tgl_cuti_selesai	= '" . $tglCutiSelesai . "',

														alasan						= '" . $alasan . "',

														status						= 'Pending',

														created_by				= '" . $userId . "'";

	$runCuti					= JalankanSQL($ins, $qCuti, $limit);

	if ($runCuti) {

		set_flashdata('success', 'Anda sudah mengajukan permohonan cuti');

	} else {

		set_flashdata('error', 'Gagal mengajukan permohonan cuti. Silahkan coba lagi.');

	}

	echo '<script>

					document.location.href = "cuti.php?mode=aksesmobileapps&user_id=' . $userId . '";

				</script>';

	exit();

}

$success_message = get_flashdata('success');

$error_message = get_flashdata('error');

$titleHeader	= 'Cuti';

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

				<div class="row mb-3 p-2">

					<div class="row mt-2 mb-3">

						<h5 class="fw-bold mb-3">FORM PERMOHONAN CUTI</h5>

						<div class="col-12">

							<ol class="list-group list-group-numbered">

								<li class="list-group-item list-group-item-primary">Permohonan akan di review oleh kepala sekolah / kabag dan SDM</li>

								<li class="list-group-item list-group-item-primary">Keputusan dapat dilihat di tabel di bawah.</li>

								<li class="list-group-item list-group-item-primary">Klik tombol "Tampilkan Form" untuk proses pengajuan.</li>

							</ol>

						</div>

					</div>

				</div>



				<div class="row p-3 mb-3">

					<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">

						Tampilkan Form

					</button>

				</div>



				<div class="collapse row mb-3" id="collapseExample">

					<div class="card card-body">

						<form id="cuti" method="post" enctype="multipart/form-data">

							<input type="hidden" name="usrid" id="usrid" class="form-control" value="<?= $_REQUEST['user_id'] ?>">

							<div class="row mb-3">

								<div class="col-12">

									<label for="nomorinduk" class="fw-bold form-label">No. Induk</label>

									<input type="text" name="nomorinduk" id="nomorinduk" class="form-control" value="<?= $nomorInduk ?>" readonly required>

								</div>

							</div>

							<div class="row mb-3">

								<div class="col-12">

									<label for="nama" class="fw-bold form-label">Nama</label>

									<input type="text" name="nama" id="nama" class="form-control" value="<?= $nama ?>" readonly required>

								</div>

							</div>

							<div class="row mb-3">

								<div class="col-12">

									<label for="departemen" class="fw-bold form-label">Departemen</label>

									<input type="text" name="departemen" id="departemen" class="form-control" value="<?= $dept ?>" readonly required>

								</div>

							</div>

							<div class="row mb-3">

								<div class="col-12">

									<label for="tglPengajuan" class="fw-bold form-label">Tgl. Pengajuan</label>

									<div class="input-group">

										<span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>

										<input type="date" name="tglPengajuan" id="tglPengajuan" class="form-control" aria-label="Tanggal Pengajuan" aria-describedby="tglPengajuan" onfocus="this.showPicker()" required>

									</div>

								</div>

							</div>

							<div class="row mb-3">

								<div class="col-12">

									<label for="tglCutiMulai" class="fw-bold form-label">Tgl. Mulai Cuti</label>

									<div class="input-group">

										<span class="input-group-text"><i class="bi bi-calendar-plus"></i></span>

										<input type="date" name="tglCutiMulai" id="tglCutiMulai" class="form-control" aria-label="Tanggal Mulai Cuti" aria-describedby="tglCutiMulai" onfocus="this.showPicker()" required>

									</div>

								</div>

							</div>

							<div class="row mb-3">

								<div class="col-12">

									<label for="tglCutiSelesai" class="fw-bold form-label">Tgl. Akhir Cuti</label>

									<div class="input-group">

										<span class="input-group-text"><i class="bi bi-calendar2-check"></i></span>

										<input type="date" name="tglCutiSelesai" id="tglCutiSelesai" class="form-control" aria-label="Tanggal Akhir Cuti" aria-describedby="tglCutiSelesai" onfocus="this.showPicker()" required>

									</div>

								</div>

							</div>

							<div class="row mb-3">

								<div class="col-12">

									<label for="alasan" class="fw-bold form-label">Alasan</label>

									<input type="text" name="alasan" id="alasan" class="form-control" maxlength="255" required>

								</div>

							</div>

							<div class="row mb-3">

								<button type="submit" name="submitcuti" id="submitcuti" class="btn btn-primary"><i class="bi bi-floppy-fill"></i> Ajukan Permohonan</button>

							</div>

						</form>

					</div>

				</div>



				<div class="table-responsive">

					<h4 class="fw-bold text-primary"><?= $nama ?></h4>

					<table class="table">

						<thead>

							<tr>

								<th scope="col" class="align-middle">No.</th>

								<th scope="col" class="align-middle">Tgl. Cuti</th>

								<th scope="col" class="align-middle">Alasan</th>

								<th scope="col" class="align-middle">Status</th>

							</tr>

						</thead>

						<tbody>

							<?php $no = 1;

						if ($cutiKu > 0) {

							foreach ($runDataCuti as $c) { ?>

							<tr>

								<td scope="row"><?= $no ?></td>

								<td><?= date('d-m-Y', strtotime($c->tgl_cuti_mulai)) . ' s/d ' . date('d-m-Y', strtotime($c->tgl_cuti_selesai)) ?></td>

								<td><?= $c->alasan ?></td>

								<td><?= $c->status ?></td>

							</tr>

							<?php

								$no++;

							}

						} else {

							echo '<tr class="text-center"><td colspan="4" class="text-secondary fst-italic">Belum ada permohonan cuti</td></tr>';

						}

						?>

						</tbody>

					</table>

				</div>

			</main>

		</div>
		
		<script>
			// Sending a message
			ThunkableWebviewerExtension.postMessage('Hello from the Web Page!');

			// Sending a JSON object
			const data = { status: "success", score: 100 };
			ThunkableWebviewerExtension.postMessage(JSON.stringify(data));
		</script>

	</body>

	<?php include('webpart/js.php');

if ($success_message) { ?>

	<script>

	Swal.fire({

		icon: "success",

		title: "Berhasil",

		html: "<?= $success_message ?>",

		timer: 2000,

		timerProgressBar: true,

		didOpen: () => {

			Swal.showLoading();

			const b = Swal.getHtmlContainer().querySelector("b");

			timerIntervalCreate = setInterval(() => {

				b.textContent = (Swal.getTimerLeft() / 1000).toFixed(0);

			}, 10);

		},

		willClose: () => {

			clearInterval(timerIntervalCreate);

		}

	}).then((result) => {});

	</script>

	<?php } elseif ($error_message) { ?>

	<script>

	Swal.fire({

		icon: 'error',

		title: 'Gagal!',

		text: '<?= $error_message; ?>'

	});

	</script>

	<?php } ?>



</html>

