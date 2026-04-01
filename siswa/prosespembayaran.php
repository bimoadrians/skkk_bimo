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

$titlePage		= 'Proses Pembayaran - ' . APPS_NAME;
$titleHeader	= 'Proses Pembayaran';
$userId				= strtoupper($_REQUEST['user_id']);
if($userId == 'DEMO'){
    $userId = '46257';
}
$today				= date('Y-m-d');
$hariIni			= date('d-m-Y');
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

$pilihbayar					= $_REQUEST["pilihbayar"];
$nominalbayar				= $_REQUEST["nominalbayar"];
$totalnominalbayar	= "";

if ($pilihbayar == 0) {
	$totalnominalbayar = $nominalbayar;
}

if ($pilihbayar == 1) {
	for ($i = 0; $i < count($nominalbayar); $i++) {
		$totalnominalbayar = (int)$totalnominalbayar + (int)$nominalbayar[$i];
	}
}

//BEGIN CARI NOPENDAFTARAN CASIS
$sqlCariNo	= "SELECT tds.teks as nopendaftaran
							FROM jbsakad.tambahandatasiswa tds
							LEFT JOIN jbsakad.siswa s on s.nis = tds.nis
							LEFT JOIN jbsakad.tambahandata td ON td.replid = tds.idtambahan
							WHERE td.kolom = 'NP_CalonSiswa' and tds.nis = '{$id_key}'";
$dataCariNo				= JalankanSQL($perintah, $sqlCariNo, $limit);
$nopendaftaran	= $dataCariNo[0]->nopendaftaran;
//END CARI NOPENDAFTARAN CASIS

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

//BEGIN CARI BULAN TAHUN SEKARANG
$sqlBulanTahun	= "SELECT MONTH(CURRENT_DATE()) AS bulan, YEAR(CURRENT_DATE()) AS tahun";
$dataBulanTahun	= JalankanSQL($perintah, $sqlBulanTahun, $limit);
$bulansekarang	= $dataBulanTahun[0]->bulan;
$tahunsekarang	= $dataBulanTahun[0]->tahun;
$bulantagihan		= realMonth($dataBulanTahun[0]->bulan);
//END CARI BULAN TAHUN SEKARANG	

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
						<h6 class="card-title">N I S : <?= $id_key . " / " . $noPendaftaran ?></h6>
						<div class="card-text">
							<p class="fs-3 text-warning"><?= $nama ?></p>
							<p class="fs-6">Kelas : <?= $namakelas ?></p>
							<p class="fs-6">VA <?= $bank ?>: <span id="nomor-va"><?= $kode_va_siswa . $virtualAccount ?></span> <button id="copy-button" class="btn btn-sm btn-warning"><i class="bi bi-clipboard"></i> Salin</button></p>
						</div>
					</div>
				</div>

				<div class="card p-3 rounded-4 shadow-lg">
					<div class="card-body flex-column align-items-start">
						<h5 class="card-title text-primary">Proses Pembayaran <?= FormatRupiah($totalnominalbayar) ?></h5><br />
						<div class="card-text">
							<div class="col-auto">
								<p>Silahkan transfer ke nomor rekening <b><?= $bank ?></b> dengan nomor virtual account <b><?= $kode_va_siswa . $virtualAccount ?></b>, sejumlah <b><?= FormatRupiah($totalnominalbayar) ?></b>.</p>
								<p>Terima kasih,<br />Tuhan memberkati</p>
							</div>

							<div class="col-auto">
								<a href="rinciantagihan.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="btn btn-primary"><i class="bi bi-arrow-bar-left"></i> Kembali ke Rincian Tagihan</a>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<?php include('webpart/js.php') ?>
	</body>

</html>
