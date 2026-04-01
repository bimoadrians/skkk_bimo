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

$titlePage		= 'Beranda - ' . APPS_NAME;
$titleHeader	= 'Beranda';
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

//BEGIN CARI BULAN TAHUN SEKARANG
$sqlBulanTahun	= "SELECT MONTH(CURRENT_DATE()) AS bulan, YEAR(CURRENT_DATE()) AS tahun";
$dataBulanTahun	= JalankanSQL($perintah, $sqlBulanTahun, $limit);
$bulansekarang	= $dataBulanTahun[0]->bulan;
$tahunsekarang	= $dataBulanTahun[0]->tahun;
$bulantagihan		= realMonth($dataBulanTahun[0]->bulan);
//END CARI BULAN TAHUN SEKARANG

//BEGIN CARI BANK & VA
$sqlVa		= "SELECT b.bank, v.virtualaccount
						FROM jbsakad.va v
						LEFT JOIN jbsakad.calonsiswa s ON s.nopendaftaran = v.nis 
						LEFT JOIN jbsakad.bank b ON b.replid = v.bank
						WHERE s.nopendaftaran = '{$id_key}'
						AND v.statusaktif = 1 AND jenis = 0";
$dataVa		= JalankanSQL($perintah, $sqlVa, $limit);
$bank						= $dataVa[0]->bank;
$virtualAccount	= $dataVa[0]->virtualaccount;
//END CARI BANK & VA

//BEGIN CARI TUNGGAKAN
$sqlTunggakan	= "SELECT SUM(besar) AS besar
								FROM
									(SELECT SUM(b.besar) AS besar
									FROM besarjttcalon b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.tunggakan = 1
									AND dp.idkategori = 'CSWJB'
									AND dp.type_pembayaran = 0
									AND b.idcalon = '{$idcalon}'
									GROUP BY b.idpenerimaan) AS x";
$dataTunggakan		= JalankanSQL($perintah, $sqlTunggakan, $limit);
$bbesartunggakan	= $dataTunggakan[0]->besar;

$sqlTunggakan2	= "SELECT SUM(total) AS total
									FROM
										(SELECT SUM(b.jumlah+b.diskon) AS total
										FROM besarjttcalon b
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.tunggakan = 1
										AND dp.idkategori = 'CSWJB'
										AND dp.type_pembayaran = 0
										AND b.idcalon = '{$idcalon}'
										GROUP BY b.idpenerimaan) AS x";
$dataTunggakan2	= JalankanSQL($perintah, $sqlTunggakan2, $limit);
$btunggakan			= $dataTunggakan2[0]->total;
$jtunggakan			= $bbesartunggakan - $btunggakan;
//END CARI TUNGGAKAN

//BEGIN CARI SPP
$sqlSPP		= "SELECT SUM(besar) AS besar
						FROM
							(SELECT SUM(b.besar) AS besar
							FROM besarjttcalon b
							LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
							WHERE dp.aktif = 1
							AND dp.spp = 1
							AND dp.idkategori = 'CSWJB'
							AND dp.type_pembayaran = 0
							AND b.idcalon = '{$idcalon}'
							GROUP BY b.idpenerimaan)
						AS x";
$dataSPP	= JalankanSQL($perintah, $sqlSPP, $limit);
$bbesarspp	= $dataSPP[0]->besar;

$sqlSPP2		= "SELECT SUM(total) AS total
							FROM
								(SELECT SUM(b.jumlah+b.diskon) AS total
								FROM besarjttcalon b
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.spp = 1
								AND dp.idkategori = 'CSWJB'
								AND dp.type_pembayaran = 0
								AND b.idcalon = '{$idcalon}'
								GROUP BY b.idpenerimaan)
							AS x";
$dataSPP2		= JalankanSQL($perintah, $sqlSPP2, $limit);
$bspp				= $dataSPP2[0]->total;
$jspp				= $bbesarspp - $bspp;
//END CARI SPP

//BEGIN CARI PANGKAL
$sqlPangkal		= "SELECT SUM(besar) AS besar
								FROM
									(SELECT SUM(b.besar) AS besar
									FROM besarjttcalon b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.pangkal = 1
									AND dp.idkategori = 'CSWJB'
									AND dp.type_pembayaran = 0
									AND b.idcalon = '{$idcalon}'
									GROUP BY b.idpenerimaan) AS x";
$dataPangkal		= JalankanSQL($perintah, $sqlPangkal, $limit);
$bbesarpangkal	= $dataPangkal[0]->besar;

$sqlPangkal2		= "SELECT SUM(total) AS total
								FROM
									(SELECT SUM(b.jumlah+b.diskon) AS total
									FROM besarjttcalon b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.pangkal = 1
									AND dp.idkategori = 'CSWJB'
									AND dp.type_pembayaran = 0
									AND b.idcalon = '{$idcalon}'
									GROUP BY b.idpenerimaan) AS x";
$dataPangkal2		= JalankanSQL($perintah, $sqlPangkal2, $limit);
$bpangkal				= $dataPangkal2[0]->total;
$jpangkal				= $bbesarpangkal - $bpangkal;
//END CARI PANGKAL

//BEGIN CARI KEGIATAN
$sqlKegiatan	= "SELECT SUM(besar) AS besar
								FROM
									(SELECT SUM(b.besar) AS besar
									FROM besarjttcalon b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.kegiatan = 1
									AND dp.idkategori = 'CSWJB'
									AND dp.type_pembayaran = 0
									AND b.idcalon = '{$idcalon}'
									GROUP BY b.idpenerimaan)
								AS x";
$dataKegiatan		= JalankanSQL($perintah, $sqlKegiatan, $limit);
$bbesarkegiatan	= $dataKegiatan[0]->besar;

$sqlKegiatan2	= "SELECT SUM(total) AS total
								FROM
									(SELECT SUM(b.jumlah+b.diskon) AS total
									FROM besarjttcalon b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.kegiatan = 1
									AND dp.idkategori = 'CSWJB'
									AND dp.type_pembayaran = 0
									AND b.idcalon = '{$idcalon}'
									GROUP BY b.idpenerimaan)
								AS x";
$dataKegiatan2	= JalankanSQL($perintah, $sqlKegiatan2, $limit);
$bkegiatan			= $dataKegiatan2[0]->total;
$jkegiatan			= $bbesarkegiatan - $bkegiatan;
//END CARI KEGIATAN

//BEGIN CARI LAIN-LAIN
$sqlLain				= "SELECT SUM(besar) AS besar
									FROM
										(SELECT SUM(b.besar) AS besar
										FROM besarjttcalon b
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.lainlain = 1
										AND dp.idkategori = 'CSWJB'
										AND dp.type_pembayaran = 0
										AND b.idcalon = '{$idcalon}'
										GROUP BY b.idpenerimaan)
									AS x";
$dataLain				= JalankanSQL($perintah, $sqlLain, $limit);
$bbesarlainlain	= $dataLain[0]->besar;

$sqlLain2				= "SELECT SUM(total) AS total
									FROM
										(SELECT SUM(b.jumlah+b.diskon) AS total
										FROM besarjttcalon b
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.lainlain = 1
										AND dp.idkategori = 'CSWJB'
										AND dp.type_pembayaran = 0
										AND b.idcalon = '{$idcalon}'
										GROUP BY b.idpenerimaan)
									AS x";
$dataLain2			= JalankanSQL($perintah, $sqlLain2, $limit);
$blainlain			= $dataLain2[0]->total;
$jlainlain			= $bbesarlainlain - $blainlain;
//END CARI LAIN-LAIN
$totalbayar			= $jtunggakan + $jspp + $jpangkal + $jkegiatan + $jlainlain;
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
						<h5 class="card-title text-primary">Informasi Tagihan <?= NamaBulan($bulansekarang) . " " . $tahunsekarang ?></h5>
						<div class="card-text">
							<div class="table-responsive">
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
											<td><?= FormatRupiah($jtunggakan) ?></td>
										</tr>
										<tr>
											<th scope="row">2</th>
											<td>Uang SPP</td>
											<td><?= FormatRupiah($jspp) ?></td>
										</tr>
										</tr>
										<tr>
											<th scope="row">3</th>
											<td>Uang Pangkal</td>
											<td><?= FormatRupiah($jpangkal) ?></td>
										</tr>
										<tr>
											<th scope="row">4</th>
											<td>Uang Kegiatan</td>
											<td><?= FormatRupiah($jkegiatan) ?></td>
										</tr>
										<tr>
											<th scope="row">5</th>
											<td>Uang Lain-lain</td>
											<td><?= FormatRupiah($jlainlain) ?></td>
										</tr>
									</tbody>
								</table>
								<br />
								<p class="fs-6">Total tagihan : <?= FormatRupiah($totalbayar) ?></p>
								<p class="fs-6">Batas bayar : 10-<?= $bulansekarang ?>-<?= $tahunsekarang ?></p>
							</div>
							<div class="col-auto">
								<!--<a href="rinciantagihan.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="btn btn-primary"><i class="bi bi-eye"></i> Lihat rincian tagihan</a>

								<a href="prosespembayaran.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>&nominalbayar=<?= $totalbayar ?>&pilihbayar=0" class="btn btn-danger"><i class="bi bi-cash"></i> Bayar tagihan</a>-->
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<?php include('webpart/js.php') ?>
	</body>

</html>
