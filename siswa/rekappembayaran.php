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
$userId				= strtoupper($_REQUEST['user_id']);
if($userId == 'DEMO'){
    $userId = '46257';
}
$perintah			= 'view';
$limit				= 1;
$sqlUser			= "SELECT
									s.replid AS replid,
									s.nis AS id_key,
									nama,
									telponsiswa AS telpon,
									hportu AS hp,
									s.info1,
									s.info2,
									kelas AS namakelas,
									alamatsiswa AS alamattinggal,
									tingkat AS namatingkat,
									s.keterangan
								FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t
								WHERE s.idkelas = k.replid
									AND t.replid = k.idtingkat
									AND s.nis = '{$userId}'";
$dataUser					= JalankanSQL($perintah, $sqlUser, $limit);
$id_key						= $dataUser[0]->id_key;
$nama							= $dataUser[0]->nama;
$telpon						= $dataUser[0]->telpon;
$hp1							= $dataUser[0]->hp;
$hp2							= $dataUser[0]->info1;
$hp3							= $dataUser[0]->info2;
$namatingkat			= $dataUser[0]->namatingkat;
$namakelas				= $dataUser[0]->namakelas;
$alamattinggal		= $dataUser[0]->alamattinggal;
$keterangansiswa	= $dataUser[0]->keterangan;
//END EKSTRAK RESPONSE

//BEGIN CARI BANK & VA
$sqlCariBank		= "SELECT b.bank, v.virtualaccount
									FROM jbsakad.va v
									LEFT JOIN jbsakad.siswa s ON s.nis = v.nis
									LEFT JOIN jbsakad.bank b ON b.replid = v.bank
									WHERE s.nis = '{$id_key}'
									AND v.statusaktif = 1
									AND jenis = 1";
$dataCariBank		= JalankanSQL($perintah, $sqlCariBank, $limit);
$bank						= $dataCariBank[0]->bank;
$virtualAccount	= $dataCariBank[0]->virtualaccount;
//END CARI BANK & VA	

//BEGIN CARI REKAP PEMBAYARAN
$sqlRekapBayar	= "SELECT * FROM konversiva WHERE nis = '{$id_key}'
									ORDER BY replid DESC";
$dataRekapBayar	= JalankanSQL($perintah, $sqlRekapBayar, 300);
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
						<h6 class="card-title">N I S : <?= $id_key ?></h6>
						<div class="card-text">
							<p class="fs-3 text-warning"><?= $nama ?></p>
							<p class="fs-6">Kelas : <?= $namakelas ?></p>
							<p class="fs-6">VA <?= $bank ?>: <span id="nomor-va"><?= $kode_va_siswa . $virtualAccount ?></span> <button id="copy-button" class="btn btn-sm btn-warning"><i class="bi bi-clipboard"></i> Salin</button></p>
						</div>
					</div>
				</div>

				<div class="card card-custom p-2 my-3 rounded-4 shadow-lg">
					<div class="card-body flex-column align-items-start">
						<h5 class="card-title text-primary">Informasi Rekap Pembayaran</h5><br />
						<div class="card-text">
							<div class="table-responsive">
								<?php if ($dataRekapBayar) { ?>
								<table class="table">
									<thead>
										<tr>
											<th scope="col" width="2">No</th>
											<th scope="col" width="20">Informasi</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($dataRekapBayar as $mydata) {
											$i = $i + 1;
											$tanggaltransaksi		= date("d-m-Y", strtotime($mydata->tanggaltransaksi));
											$keteranganalokasi	= str_replace("<+>", "<br />", $mydata->keteranganalokasi);
											$keteranganalokasi	= preg_replace_callback("/@(\d+)@/", function ($matches) {
												return ShortNameOfMonth((int)$matches[1] + 0);
											}, $keteranganalokasi);
											if (($mydata->nominal) - ($mydata->info3) == 0) {
												$jenis = 'primary';
												$warnaClass = '';
											} else {
												$jenis = 'danger';
												$warnaClass = 'danger';
											}
										?>
										<tr>
											<th scope="row"><?= $i ?></th>
											<td><b>- Tanggal :</b> <?= $tanggaltransaksi ?><br /><b>- Transfer VA :</b> <?= FormatRupiah(($mydata->nominal)) ?><br /><b>- Deposit :</b> <span class="<?= $warnaClass ?>"><?= $warna . FormatRupiah(($mydata->nominal) - ($mydata->info3)) ?></span><br /><br /><b>Alokasi</b><br /><?= str_replace("<+>", "<br />", $keteranganalokasi) ?></td>
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
								<a href="dashboard.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="btn btn-primary"><i class="bi bi-arrow-bar-left"></i> Kembali ke Dashboard</a>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<?php include('webpart/js.php') ?>
	</body>

</html>
