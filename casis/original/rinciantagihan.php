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
	
	//BEGIN CARI BULAN TAHUN SEKARANG
	$bulantagihan = realMonth($bulansekarang);
	for($i = 1; $i <= $bulantagihan; $i++) {
		$a .= 'b.by$i+';
		$b .= 'b.tg$i+';
			
		$t .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$i as besartagihan, b.by$i as tunggakan, $i as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.tunggakan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan='1' and b.idcalon='" . $idcalon . "' UNION ALL ";
		
		$s .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$i as besartagihan, b.by$i as tunggakan, $i as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.spp=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan='1' and b.idcalon='" . $idcalon . "' UNION ALL ";
		
		$p .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$i as besartagihan, b.by$i as tunggakan, $i as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.pangkal=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan='1' and b.idcalon='" . $idcalon . "' UNION ALL ";		
		
		$k .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$i as besartagihan, b.by$i as tunggakan, $i as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.kegiatan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan='1' and b.idcalon='" . $idcalon . "' UNION ALL ";		
		
		$l .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$i as besartagihan, b.by$i as tunggakan, $i as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.lainlain=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan='1' and b.idcalon='" . $idcalon . "' UNION ALL ";				
	}
	
	for ($y = 1; $y <= 12; $y++) {
		$y1 .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$y as besartagihan, b.by$y as tunggakan, $y as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.tunggakan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan<>'1' and b.idcalon='" . $idcalon . "' UNION ALL ";
		$y2 .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$y as besartagihan, b.by$y as tunggakan, $y as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.spp=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan<>'1' and b.idcalon='" . $idcalon . "' UNION ALL ";
		$y3 .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$y as besartagihan, b.by$y as tunggakan, $y as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.pangkal=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan<>'1' and b.idcalon='" . $idcalon . "' UNION ALL ";
		$y4 .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$y as besartagihan, b.by$y as tunggakan, $y as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.kegiatan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan<>'1' and b.idcalon='" . $idcalon . "' UNION ALL ";
		$y5 .= "SELECT CONCAT(dp.nama, ' - SIS') as namatagihan, b.tg$y as besartagihan, b.by$y as tunggakan, $y as tg FROM besarjttcalon b LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan WHERE dp.aktif=1 and dp.lainlain=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=1 and dp.tahunberjalan<>'1' and b.idcalon='" . $idcalon . "' UNION ALL ";
	}		
	
	$besarbul = substr($a, 0, -1);
	$besartg = substr($b, 0, -1);
	$caritunggakan = substr($t, 0, -10)." UNION ALL ".substr($y1, 0, -10);
	$carispp = substr($s, 0, -10)." UNION ALL ".substr($y2, 0, -10);
	$caripangkal = substr($p, 0, -10)." UNION ALL ".substr($y3, 0, -10);
	$carikegiatan = substr($k, 0, -10)." UNION ALL ".substr($y4, 0, -10);
	$carilainlain = substr($l, 0, -10)." UNION ALL ".substr($y5, 0, -10);
	//END CARI BULAN TAHUN SEKARANG
	
	//BEGIN CARI DETAIL TAGIHAN
	$query = "SELECT * FROM (select dp.nama as namatagihan, b.besar as besartagihan, sum(b.jumlah+b.diskon) as tunggakan, '' as tg
	from besarjttcalon b 
	left join datapenerimaan dp on dp.replid=b.idpenerimaan
	where dp.aktif=1 and dp.tunggakan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan
	UNION ALL
	SELECT * FROM ($carispp) as y) AS x WHERE besartagihan-tunggakan <> 0";
	$responsetunggakan = JalankanSQL("view", $query, 100);
	
	$query = "SELECT * FROM (select dp.nama as namatagihan, b.besar as besartagihan, sum(b.jumlah+b.diskon) as tunggakan, '' as tg
	from besarjttcalon b 
	left join datapenerimaan dp on dp.replid=b.idpenerimaan
	where dp.aktif=1 and dp.pangkal=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan
	UNION ALL
	SELECT * FROM ($caripangkal) as y) AS x WHERE besartagihan-tunggakan <> 0";
	$responsepangkal = JalankanSQL("view", $query, 100);

	$query = "SELECT * FROM (select dp.nama as namatagihan, b.besar as besartagihan, sum(b.jumlah+b.diskon) as tunggakan, '' as tg
	from besarjttcalon b 
	left join datapenerimaan dp on dp.replid=b.idpenerimaan
	where dp.aktif=1 and dp.spp=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan
	UNION ALL
	SELECT * FROM ($carispp) as y) AS x WHERE besartagihan-tunggakan <> 0";
	$responsespp = JalankanSQL("view", $query, 100);

	$query = "SELECT * FROM (select dp.nama as namatagihan, b.besar as besartagihan, sum(b.jumlah+b.diskon) as tunggakan, '' as tg
	from besarjttcalon b 
	left join datapenerimaan dp on dp.replid=b.idpenerimaan
	where dp.aktif=1 and dp.kegiatan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan
	UNION ALL
	SELECT * FROM ($carikegiatan) as y) AS x WHERE besartagihan-tunggakan <> 0";
	$responsekegiatan = JalankanSQL("view", $query, 100);
	
	$query = "SELECT * FROM (select dp.nama as namatagihan, b.besar as besartagihan, sum(b.jumlah+b.diskon) as tunggakan, '' as tg
	from besarjttcalon b 
	left join datapenerimaan dp on dp.replid=b.idpenerimaan
	where dp.aktif=1 and dp.lainlain=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan
	UNION ALL
	SELECT * FROM ($carilainlain) as y) AS x WHERE besartagihan-tunggakan <> 0";
	$responselainlain = JalankanSQL("view", $query, 100);
	//END CARI DETAIL TAGIHAN
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
					<h5 class="card-title text-primary">Informasi Detail Tagihan <?=NamaBulan($bulansekarang) . " " . $tahunsekarang ?></h5><br />
					<div class="card-text">
						<div class="col-auto">
							<?php if ($responsetunggakan) { ?>
								<h6 class="text-primary">Data Tunggakan</h5>
								<table class="table">
								  <thead>
									<tr>
									  <th scope="col" width="2">No</th>
									  <th scope="col" width="20">Nama</th>
									  <th scope="col" width="40">Besar</th>
									  <th scope="col" width="40">Tagihan</th>
									</tr>
								  </thead>
								  <tbody>
								  <?php 
									  $i=0;
									  foreach($responsetunggakan as $mydata) {
										  $i = $i+1;
										  $totaltunggakan+=$mydata->besartagihan-$mydata->tunggakan;
								  ?>
									<tr>
										  <th scope="row"><?=$i?></th>
										  <td><?=$mydata->namatagihan . " " . NameOfMonth($mydata->tg)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan-$mydata->tunggakan)?></td>
									</tr>										  
								  <? } ?>
								  </tbody>
								</table>									
							<? } ?>
							<?php if ($responsespp) { ?>
								<br />
								<h6 class="text-primary">Data Uang SPP</h5>
								<table class="table">
								  <thead>
									<tr>
									  <th scope="col" width="2">No</th>
									  <th scope="col" width="20">Nama</th>
									  <th scope="col" width="40">Besar</th>
									  <th scope="col" width="40">Tagihan</th>
									</tr>
								  </thead>
								  <tbody>
								  <?php 
									  $i=0;
									  foreach($responsespp as $mydata) {
										  $i = $i+1;
										  $totalspp+=$mydata->besartagihan-$mydata->tunggakan
								  ?>
									<tr>
										  <th scope="row"><?=$i?></th>
										  <td><?=$mydata->namatagihan . " " . NameOfMonth($mydata->tg)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan-$mydata->tunggakan)?></td>
									</tr>										  
								  <? } ?>
								  </tbody>
								</table>									
							<? } ?>
							<?php if ($responsepangkal) { ?>
								<br />
								<h6 class="text-primary">Data Uang Pangkal</h5>
								<table class="table">
								  <thead>
									<tr>
									  <th scope="col" width="2">No</th>
									  <th scope="col" width="20">Nama</th>
									  <th scope="col" width="40">Besar</th>
									  <th scope="col" width="40">Tagihan</th>
									</tr>
								  </thead>
								  <tbody>
								  <?php 
									  $i=0;
									  foreach($responsepangkal as $mydata) {
										  $i = $i+1;
										  $totalpangkal+=$mydata->besartagihan-$mydata->tunggakan;
								  ?>
									<tr>
										  <th scope="row"><?=$i?></th>
										  <td><?=$mydata->namatagihan . " " . NameOfMonth($mydata->tg)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan-$mydata->tunggakan)?></td>
									</tr>										  
								  <? } ?>
								  </tbody>
								</table>									
							<? } ?>
							<?php if ($responsekegiatan) { ?>
								<br />
								<h6 class="text-primary">Data Uang Kegiatan</h5>
								<table class="table">
								  <thead>
									<tr>
									  <th scope="col" width="2">No</th>
									  <th scope="col" width="20">Nama</th>
									  <th scope="col" width="40">Besar</th>
									  <th scope="col" width="40">Tagihan</th>
									</tr>
								  </thead>
								  <tbody>
								  <?php 
									  $i=0;
									  foreach($responsekegiatan as $mydata) {
										  $i = $i+1;
										  $totalkegiatan+=$mydata->besartagihan-$mydata->tunggakan;
								  ?>
									<tr>
										  <th scope="row"><?=$i?></th>
										  <td><?=$mydata->namatagihan . " " . NameOfMonth($mydata->tg)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan-$mydata->tunggakan)?></td>
									</tr>										  
								  <? } ?>
								  </tbody>
								</table>
							<? } ?>
							<?php if ($responselainlain) { ?>
							<br />
								<h6 class="text-primary">Data Uang Lain-Lain</h5>
								<table class="table">
								  <thead>
									<tr>
									  <th scope="col" width="2">No</th>
									  <th scope="col" width="20">Nama</th>
									  <th scope="col" width="40">Besar</th>
									  <th scope="col" width="40">Tagihan</th>
									</tr>
								  </thead>
								  <tbody>
								  <?php 
									  $i=0;
									  foreach($responselainlain as $mydata) {
										  $i = $i+1;
										  $totallainlain+=$mydata->besartagihan-$mydata->tunggakan;
								  ?>
									<tr>
										  <th scope="row"><?=$i?></th>
										  <td><?=$mydata->namatagihan . " " . NameOfMonth($mydata->tg)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan)?></td>
										  <td><?=FormatRupiah($mydata->besartagihan-$mydata->tunggakan)?></td>
									</tr>										  
								  <? } ?>
								  </tbody>
								</table>
							<? } ?>
							<? $totalbayar = $totaltunggakan + $totalpangkal + $totalspp + $totalkegiatan + $totallainlain; ?>
							<br />
							<p class="fs-6">Total tagihan : <?=FormatRupiah($totalbayar)?></p>
							<p class="fs-6">Batas bayar : 10-<?=$bulansekarang?>-<?=$tahunsekarang?></p>
						</div>
						<div class="col-auto">
							<a href="dashboard.php?mode=<?=$mode_akses?>" class="btn btn-primary"><i class="bi bi-eye"></i> Kembali</a>
							
							<a href="prosespembayaran.php?mode=<?=$mode_akses?>&nominalbayar=<?=$totalbayar?>&pilihbayar=0" class="btn btn-danger"><i class="bi bi-cash"></i> Bayar tagihan</a>							
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