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
	
	//BEGIN CARI TUNGGAKAN
	$query = "SELECT DISTINCT b.replid AS id, b.besar, b.tg1, b.tg2, b.tg3, b.tg4, b.tg5, b.tg6, b.tg7, b.tg8, b.tg9, b.tg10, b.tg11, b.tg12, b.by1, b.by2, b.by3, b.by4, b.by5, b.by6, b.by7, b.by8, b.by9, b.by10, b.by11, b.by12, b.lunas, b.keterangan, b.jumlah, b.diskon, d.nama, d.type_pembayaran, d.departemen, d.tahun_ajar FROM besarjttcalon b, datapenerimaan d WHERE b.idpenerimaan = d.replid AND b.idcalon='" . $idcalon . "' AND d.type_pembayaran='0' ORDER BY d.tahun_ajar DESC";
	$response = JalankanSQL("view", $query, 700);
	$kartutagihanbebas = $response;
	
	$query = "SELECT DISTINCT b.replid AS id, b.besar, b.tg1, b.tg2, b.tg3, b.tg4, b.tg5, b.tg6, b.tg7, b.tg8, b.tg9, b.tg10, b.tg11, b.tg12, b.by1, b.by2, b.by3, b.by4, b.by5, b.by6, b.by7, b.by8, b.by9, b.by10, b.by11, b.by12, b.lunas, b.keterangan, b.jumlah, b.diskon, d.nama, d.type_pembayaran, d.departemen, d.tahun_ajar FROM besarjttcalon b, datapenerimaan d WHERE b.idpenerimaan = d.replid AND b.idcalon='" . $idcalon . "' AND d.type_pembayaran='1' ORDER BY d.tahun_ajar DESC";
	$response = JalankanSQL("view", $query, 700);
	$kartutagihanbulanan = $response;
	//END CARI LAIN-LAIN
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
			
			<form method="post" action="prosespembayaran.php?mode=<?=$mode_akses?>&pilihbayar=1">
				<div class="card p-3 rounded-4 shadow-lg">
					<div class="card-body flex-column align-items-start">
						<h5 class="card-title text-primary">Kartu Tagihan</h5><br />
						<div class="card-text">
							<div class="col-auto">
								<?php if ($kartutagihanbebas) { ?>
									<h6 class="text-primary">Tagihan Non Bulan</h5>
									<table class="table">
									  <thead>
										<tr>
										  <th scope="col" width="2"></th>
										  <th scope="col" width="20">Nama</th>
										  <th scope="col" width="40">Besar</th>
										  <th scope="col" width="40">Tagihan</th>
										</tr>
									  </thead>
									  <tbody>
									  <?php 
										  $i=0;
										  foreach($kartutagihanbebas as $mydata) {
											  $i = $i+1;
											  $totalbebas+=$mydata->besar-($mydata->jumlah+$mydata->diskon);
											  if ($mydata->besar-($mydata->jumlah+$mydata->diskon) != 0) {
									  ?>
										<tr>
											  <th scope="row"><input type="checkbox" name="nominalbayar[]" value="<?=$mydata->besar-($mydata->jumlah+$mydata->diskon)?>" checked onclick="return false" /></th>
											  <td><?=$mydata->nama?></td>
											  <td><?=FormatRupiah($mydata->besar)?></td>
											  <td><?=FormatRupiah($mydata->besar-($mydata->jumlah+$mydata->diskon))?></td>
										</tr>
												<? } ?>
									  <? } ?>
									  </tbody>
									</table>									
								<? } ?>
								<?php if ($kartutagihanbulanan) { ?>
									<br />
									<h6 class="text-primary">Tagihan Bulanan</h5>
									<table class="table">
									  <thead>
										<tr>
										  <th scope="col" width="2"></th>
										  <th scope="col" width="20">Nama</th>
										  <th scope="col" width="40">Besar</th>
										  <th scope="col" width="40">Tagihan</th>
										</tr>
									  </thead>
									  <tbody>
									  <?php 
										  $i=0;
										  foreach($kartutagihanbulanan as $mydata) {
											  $i = $i+1;
											  $cek = 0;
											  
											  $array1 = array($mydata->tahun_ajar, $mydata->tg1, $mydata->tg2, $mydata->tg3, $mydata->tg4, $mydata->tg5, $mydata->tg6, $mydata->tg7, $mydata->tg8, $mydata->tg9, $mydata->tg10, $mydata->tg11, $mydata->tg12);
											  
											  $array2 = array($mydata->tahun_ajar, $mydata->by1, $mydata->by2, $mydata->by3, $mydata->by4, $mydata->by5, $mydata->by6, $mydata->by7, $mydata->by8, $mydata->by9, $mydata->by10, $mydata->by11, $mydata->by12);
											  
											  for ($x = 1; $x <= $bulantagihan; $x++) {
												  $totalbulansekarang+=$array1[$x]-$array2[$x];
											  }
											  
											  for ($x = 1; $x <= 12; $x++) {
												  if ($x <= $bulantagihan) {
													  $kunci = " checked ";
													  $js = " return false  ";
												  } else {
													  $kunci = "";
													  $js = " return true  ";
												  }
												  
												  if ($array1[$x]-$array2[$x] != 0) {
									  ?>
										<tr>
											  <th scope="row"><input type="checkbox" name="nominalbayar[]" value="<?=$array1[$x]-$array2[$x]?>" <?=$kunci?> onclick="<?=$js?>" /></th>
											  <td><?=$mydata->nama. " - " . ShortNameOfMonth($x)?></td>
											  <td><?=FormatRupiah($array1[$x])?></td>
											  <td><?=FormatRupiah($array1[$x]-$array2[$x]) ?></td>
										</tr>										  
									  <?
												  }
												  $cek = $x;
												  $totalbulanan+=$array1[$x]-$array2[$x];
											  }
										  } 
									  ?>
									  </tbody>
									</table>									
								<? } ?>							
								<br />
								<p class="fs-6">Tagihan non bulan : <?=FormatRupiah($totalbebas)?></p>
								<p class="fs-6">Tagihan bulanan : <?=FormatRupiah($totalbulanan)?></p>
								<p class="fs-6">Tagihan bulanan <?=NameOfMonth(1) . " - " . NamaBulan($bulansekarang) . " " . $tahunsekarang ?> : <?=FormatRupiah($totalbulansekarang)?></p>
								<hr />
								<p class="fs-6">Total tagihan <?=NamaBulan($bulansekarang) . " " . $tahunsekarang ?> : <?=FormatRupiah($totalbebas+$totalbulansekarang)?><br />
								<span class="fs-6 text-secondary"><sup>Total tagihan = Tagihan non bulan + Tagihan bulanan <?=NameOfMonth(1) . " - " . NamaBulan($bulansekarang) . " " . $tahunsekarang ?></sup></span></p>
							</div>
							<br />
							<div class="col-auto">
								<button type="submit" class="btn btn-danger"><i class="bi bi-cash"></i> Bayar tagihan</button>
							</div>
						</div>
					</div>
				</div>
			</form>
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