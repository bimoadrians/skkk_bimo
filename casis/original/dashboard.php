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
	$query = "select sum(besar) as besar from(select sum(b.besar) as besar from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.tunggakan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bbesartunggakan = $response[0]->besar;
	
	$query = "select sum(total) as total from(select sum(b.jumlah+b.diskon) as total from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.tunggakan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$btunggakan = $response[0]->total;
	
	$jtunggakan = $bbesartunggakan - $btunggakan;
	//END CARI TUNGGAKAN
	
	//BEGIN CARI SPP
	$query = "select sum(besar) as besar from(select sum(b.besar) as besar from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.spp=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bbesarspp = $response[0]->besar;
	
	$query = "select sum(total) as total from(select sum(b.jumlah+b.diskon) as total from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.spp=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bspp = $response[0]->total;
	
	$jspp = $bbesarspp - $bspp;
	//END CARI SPP	
	
	//BEGIN CARI PANGKAL
	$query = "select sum(besar) as besar from(select sum(b.besar) as besar from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.pangkal=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bbesarpangkal = $response[0]->besar;
	
	$query = "select sum(total) as total from(select sum(b.jumlah+b.diskon) as total from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.pangkal=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bpangkal = $response[0]->total;
	
	$jpangkal = $bbesarpangkal - $bpangkal;
	//END CARI PANGKAL
	
	//BEGIN CARI KEGIATAN
	$query = "select sum(besar) as besar from(select sum(b.besar) as besar from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.kegiatan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bbesarkegiatan = $response[0]->besar;
	
	$query = "select sum(total) as total from(select sum(b.jumlah+b.diskon) as total from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.kegiatan=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bkegiatan = $response[0]->total;
	
	$jkegiatan = $bbesarkegiatan - $bkegiatan;
	//END CARI KEGIATAN
	
	//BEGIN CARI LAIN-LAIN
	$query = "select sum(besar) as besar from(select sum(b.besar) as besar from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.lainlain=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$bbesarlainlain = $response[0]->besar;
	
	$query = "select sum(total) as total from(select sum(b.jumlah+b.diskon) as total from besarjttcalon b left join datapenerimaan dp on dp.replid=b.idpenerimaan where dp.aktif=1 and dp.lainlain=1 and dp.idkategori='CSWJB' and dp.type_pembayaran=0 and b.idcalon='" . $idcalon . "' group by b.idpenerimaan) as x";
	//echo $query;
	$response = JalankanSQL("view", $query, 1);
	$blainlain = $response[0]->total;
	
	$jlainlain = $bbesarlainlain - $blainlain;
	//END CARI LAIN-LAIN
	
	$totalbayar = $jtunggakan + $jspp + $jpangkal + $jkegiatan + $jlainlain;
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
					<h5 class="card-title text-primary">Informasi Tagihan <?=NamaBulan($bulansekarang) . " " . $tahunsekarang ?></h5><br />
					<div class="card-text">
						<div class="col-auto">
							<table class="table">
							  <thead>
								<tr>
								  <th scope="col">No</th>
								  <th scope="col">Tagihan</th>
								  <th scope="col">Nominal</th>
								</tr>
							  </thead>
							  <tbody>
								<tr>
								  <th scope="row">1</th>
								  <td>Tunggakan</td>
								  <td><?=FormatRupiah($jtunggakan)?></td>
								</tr>
								<tr>
								  <th scope="row">2</th>
								  <td>Uang SPP</td>
								  <td><?=FormatRupiah($jspp)?></td>
								</tr>
								</tr>
								<tr>
								  <th scope="row">3</th>
								  <td>Uang Pangkal</td>
								  <td><?=FormatRupiah($jpangkal)?></td>
								</tr>
								<tr>
								  <th scope="row">4</th>
								  <td>Uang Kegiatan</td>
								  <td><?=FormatRupiah($jkegiatan)?></td>
								</tr>
								<tr>
								  <th scope="row">5</th>
								  <td>Uang Lain-lain</td>
								  <td><?=FormatRupiah($jlainlain)?></td>
								</tr>									
							  </tbody>
							</table>
							<br />
							<p class="fs-6">Total tagihan : <?=FormatRupiah($totalbayar)?></p>
							<p class="fs-6">Batas bayar : 10-<?=$bulansekarang?>-<?=$tahunsekarang?></p>
						</div>
						<div class="col-auto">
							<a href="rinciantagihan.php?mode=<?=$mode_akses?>" class="btn btn-primary"><i class="bi bi-eye"></i> Lihat rincian tagihan</a>
							
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