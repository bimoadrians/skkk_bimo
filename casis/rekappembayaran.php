<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

$mode = $_REQUEST["mode"];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$titlePage		= 'Rekap Pembayaran - ' . APPS_NAME;
$titleHeader	= 'Rekap Pembayaran';
$userId				= $_REQUEST['user_id'];
if ($userId == 'DEMO' || $userId == 'demo') {
	$userId = '52627250047';
}
$today				= date('Y-m-d');
$hariIni			= date('d-m-Y');
$perintah			= 'view';
$limit				= 1;
$sqlUser			= "SELECT s.nopendaftaran,
												s.replid,
												s.nama,
												s.hportu,
												p.proses,
												s.alamatsiswa,
												s.keterangan
								FROM jbsakad.calonsiswa s
								LEFT JOIN jbsakad.prosespenerimaansiswa p ON s.idproses = p.replid
								WHERE s.nopendaftaran = '{$userId}'";
$dataUser			= JalankanSQL($perintah, $sqlUser, $limit);
//BEGIN EKSTRAK RESPONSE
$id_key						= $dataUser[0]->nopendaftaran;
$idcalon					= $dataUser[0]->replid;
$nama							= $dataUser[0]->nama;
//$telpon						= $dataUser[0]->telpon;
$hp1							= $dataUser[0]->hportu;
//$hp2							= $dataUser[0]->info1;
//$hp3							= $dataUser[0]->info2;
//$kelompok					= $dataUser[0]->kelompok;
$proses						= $dataUser[0]->proses;
$alamattinggal		= $dataUser[0]->alamatsiswa;
$keterangancasis	= $dataUser[0]->keterangan;
//END EKSTRAK RESPONSE

//BEGIN CARI BANK & VA
$sqlBulanTahun	= "SELECT MONTH(CURRENT_DATE()) AS bulan, YEAR(CURRENT_DATE()) AS tahun";
$dataBulanTahun	= JalankanSQL($perintah, $sqlBulanTahun, $limit);
$bulansekarang	= $dataBulanTahun[0]->bulan;
$tahunsekarang	= $dataBulanTahun[0]->tahun;
$bulantagihan		= realMonth($dataBulanTahun[0]->bulan);
//END CARI BANK & VA

//BEGIN CARI REKAP PEMBAYARAN
$query		= "SELECT * FROM konversiva WHERE nis = '{$id_key}' ORDER BY replid DESC";
$response	= JalankanSQL($perintah, $query, 300);
//END CARI REKAP PEMBAYARAN

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
</head>

<body>
	<?php include('webpart/sidebar.php') ?>
	<div class="container-fluid">
		<main>
			<div class="card animate__animated animate__fadeInUp p-2 my-3 bg-image rounded-4 shadow" style="background-image: url('../assets/imgs/card.png'); ">
				<div class="card-body flex-column text-white">
					<h6 class="card-title">No. Pendaftaran : <?= $id_key ?></h6>
					<div class="card-text">
						<p class="fs-3 text-warning"><?= $nama ?></p>
						<p class="fs-6">Proses : <?= $proses ?></p>
						<p class="fs-6">VA <?= $bank ?>: <span id="nomor-va"><?= $kode_va_siswa . $virtualAccount ?></span> <button id="copy-button" class="btn btn-sm btn-warning"><i class="bi bi-clipboard"></i> Salin</button></p>
					</div>
				</div>
			</div>

			<div class="card card-custom p-2 my-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">
					<h5 class="card-title text-primary">Informasi Rekap Pembayaran</h5><br />
					<div class="card-text">
						<div class="table-responsive">
							<?php if ($response) { ?>
								<table class="table">
									<thead>
										<tr>
											<th scope="col" width="2">No</th>
											<th scope="col" width="20">Informasi</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($response as $mydata) {
											$i = $i + 1;
											$tanggaltransaksi = date("d-m-Y", strtotime($mydata->tanggaltransaksi));
											$keteranganalokasi = str_replace("<+>", "<br />", $mydata->keteranganalokasi);
											$keteranganalokasi = preg_replace_callback("/@(\d+)@/", function ($matches) {
												return ShortNameOfMonth((int)$matches[1] + 0);
											}, $keteranganalokasi);
											if (($mydata->nominal) - ($mydata->info3) == 0) {
												$jenis = "primary";
												$colorClass = 'text-dark';
											} else {
												$jenis = "danger";
												$colorClass = 'text-danger';
											}
										?>
											<tr>
												<th scope="row"><?= $i ?></th>
												<td>
													<sup>
														<b>- Tanggal :</b> <?= $tanggaltransaksi ?><br />
														<b>- Transfer VA :</b> <?= FormatRupiah(($mydata->nominal)) ?><br />
														<b>- Deposit :</b> <span class="<?= $colorClass ?>"><?= FormatRupiah(($mydata->nominal) - ($mydata->info3)) ?></span><br /><br />
														<b>Alokasi</b><br /><?= str_replace("<+>", "<br />", $keteranganalokasi) ?>
													</sup>
												</td>
											</tr>
										<?php } ?>
									</tbody>
								</table>
							<?php } else {
								echo "Tidak ditemukan data pembayaran";
							}
							?>
						</div>
						<br />
						<div class="col-auto">
							<a href="dashboard.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="btn btn-primary"><i class="bi bi-eye"></i> Kembali</a>
						</div>
					</div>
				</div>
			</div>
		</main>
	</div>
	<?php include('webpart/js.php') ?>
</body>

</html>