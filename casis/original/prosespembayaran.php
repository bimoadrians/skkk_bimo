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
	$titlePage = 'Dashboard - ' . APPS_NAME;
	$username = $_SESSION["username"];
	$goUserid = $_SESSION["goUserid"];
	$goStatus = $_SESSION["goStatus"];
	$goPassword = $_SESSION["goPassword"];
	$folder = $_SESSION["folder"];
	$response = $_SESSION["response"];
	
	$pilihbayar = $_REQUEST["pilihbayar"];
	$nominalbayar = $_REQUEST["nominalbayar"];
	$totalnominalbayar = "";
	
	if ($pilihbayar == 0) {
		$totalnominalbayar = $nominalbayar;
	}
	
	if ($pilihbayar == 1) {
        for ($i=0; $i < count($nominalbayar) ; $i++){
            $totalnominalbayar = (int)$totalnominalbayar + (int)$nominalbayar[$i];
        }
	}
	
	//BEGIN EKSTRAK RESPONSE
	$id_key = $response[0]->id_key;
	$idcalon = $response[0]->replid;
	$nama = $response[0]->nama;
	$telpon = $response[0]->telpon;
	$hp1 = $response[0]->hp;
	$hp2 = $response[0]->info1;
	$hp3 = $response[0]->info2;
	$kelompok = $response[0]->kelompok;
	$proses = $response[0]->proses;
	$alamattinggal = $response[0]->alamattinggal;
	$keterangancasis = $response[0]->keterangan;
	//END EKSTRAK RESPONSE
	
	//BEGIN CARI BULAN TAHUN SEKARANG
	$query = "SELECT MONTH(CURRENT_DATE()) AS bulan, YEAR(CURRENT_DATE()) AS tahun";
	$response = JalankanSQL("view", $query, 1);
	$bulansekarang = $response[0]->bulan;
	$tahunsekarang = $response[0]->tahun;
	$bulantagihan = realMonth($response[0]->bulan);
	//END CARI BULAN TAHUN SEKARANG
	
	//BEGIN CARI BANK & VA
	$query = "SELECT b.bank, v.virtualaccount FROM jbsakad.va v LEFT JOIN jbsakad.calonsiswa s ON s.nopendaftaran=v.nis LEFT JOIN jbsakad.bank b ON b.replid=v.bank WHERE s.nopendaftaran='" . $id_key . "' and v.statusaktif=1 and jenis=0";
	$response = JalankanSQL("view", $query, 1);
	$bank = $response[0]->bank;
	$virtualaccount = $response[0]->virtualaccount;
	//END CARI BANK & VA
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
	<?php include('webpart/js.php') ?>
</head>

<body>
	<main>
		<?php include('webpart/topbar.php') ?>
		<div class="container-fluid">
			<div class="row pt-5 py-1 my-4 text-start">
				<div class="col-9">
					<h5 class="text-primary"><!-- BISA DIISI --></h5>
				</div>
			</div>

			<div class="card p-3 my-3 bg-image rounded-4 shadow" style="background-image: url('../assets/imgs/card.png'); ">
				<div class="card-body flex-column text-white">
					<h6 class="card-title">No. Pendaftaran : <?=$id_key?></h6>
					<div class="card-text">
						<p class="fs-3 text-warning"><?=$nama?></p>
						<p class="fs-6">Proses : <?=$proses?></p>
						<p><small>VA <?=$bank?> : <?=$kode_va_siswa . $virtualaccount?></small></p>
					</div>
				</div>
			</div>

			<div class="card p-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">
					<h5 class="card-title text-primary">Proses Pembayaran <?=FormatRupiah($totalnominalbayar)?></h5><br />
					<div class="card-text">
						<div class="col-auto">
							<p>Silahkan transfer ke nomor rekening <b><?=$bank?></b> dengan nomor virtual account <b><?=$kode_va_siswa . $virtualaccount?></b>, sejumlah <b><?=FormatRupiah($totalnominalbayar)?></b>.</p><p>Terima kasih,<br />Tuhan memberkati</p> 
						</div>
					</div>
				</div>
			</div>
			<div class="row pt-5 py-1 my-4 text-start">
				<div class="col-9">
					<h5 class="text-primary"><!-- BISA DIISI --></h5>
				</div>
			</div>			
		</div>
	</main>
	<?php include('webpart/navbar_bottom.php') ?>
</body>

</html>