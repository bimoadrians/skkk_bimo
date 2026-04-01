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
$nopendaftaran	= $_REQUEST["nopendaftaran"];
$bulansekarang	= $_REQUEST["bulansekarang"];
$tahunsekarang	= $_REQUEST["tahunsekarang"];

$titlePage			= 'Rincian Tagihan - ' . APPS_NAME;
$titleHeader		= 'Rincian Tagihan';
$userId					= strtoupper($_REQUEST['user_id']);
if($userId == 'DEMO'){
    $userId = '46257';
}
$perintah				= 'view';
$limit					= 1;
$sqlUser				= "SELECT
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

//BEGIN CARI BULAN TAHUN SEKARANG
$sqlBulan				= "SELECT MONTH(CURRENT_DATE()) AS bulan, YEAR(CURRENT_DATE()) AS tahun";
$dataBulan			= JalankanSQL($perintah, $sqlBulan, $limit);
$bulansekarang	= $dataBulan[0]->bulan;
$tahunsekarang	= $dataBulan[0]->tahun;
$bulantagihan		= realMonth($dataBulan[0]->bulan);
for ($i = 1; $i <= $bulantagihan; $i++) {
	$a .= 'b.by$i+';
	$b .= 'b.tg$i+';

	$t .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
				b.tg$i AS besartagihan,
				b.by$i AS tunggakan,
				$i AS tg
				FROM besarjtt b
				LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
				WHERE dp.aktif = 1
				AND dp.tunggakan = 1
				AND dp.idkategori = 'JTT'
				AND dp.type_pembayaran = 1
				AND dp.tahunberjalan = '1'
				AND b.nis='{$id_key}' UNION ALL ";

	$s .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
				b.tg$i AS besartagihan,
				b.by$i AS tunggakan,
				$i AS tg
				FROM besarjtt b
				LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
				WHERE dp.aktif = 1
				AND dp.spp = 1
				AND dp.idkategori = 'JTT'
				AND dp.type_pembayaran = 1
				AND dp.tahunberjalan = '1'
				AND b.nis='{$id_key}' UNION ALL ";

	$p .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
				b.tg$i AS besartagihan,
				b.by$i AS tunggakan,
				$i AS tg
				FROM besarjtt b
				LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
				WHERE dp.aktif = 1
				AND dp.pangkal = 1
				AND dp.idkategori = 'JTT'
				AND dp.type_pembayaran = 1
				AND dp.tahunberjalan = '1'
				AND b.nis='{$id_key}' UNION ALL ";

	$k .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
				b.tg$i AS besartagihan,
				b.by$i AS tunggakan,
				$i AS tg
				FROM besarjtt b
				LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
				WHERE dp.aktif = 1
				AND dp.kegiatan = 1
				AND dp.idkategori = 'JTT'
				AND dp.type_pembayaran = 1
				AND dp.tahunberjalan = '1'
				AND b.nis='{$id_key}' UNION ALL ";

	$l .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
				b.tg$i AS besartagihan,
				b.by$i AS tunggakan,
				$i AS tg
				FROM besarjtt b
				LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
				WHERE dp.aktif = 1
				AND dp.lainlain = 1
				AND dp.idkategori = 'JTT'
				AND dp.type_pembayaran = 1
				AND dp.tahunberjalan = '1'
				AND b.nis='{$id_key}' UNION ALL ";
}

for ($y = 1; $y <= 12; $y++) {
	$y1 .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
					b.tg$y AS besartagihan,
					b.by$y AS tunggakan,
					$y AS tg
					FROM besarjtt b
					LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
					WHERE dp.aktif = 1
					AND dp.tunggakan = 1
					AND dp.idkategori = 'JTT'
					AND dp.type_pembayaran = 1
					AND dp.tahunberjalan <> '1'
					AND b.nis = '{$id_key}' UNION ALL ";
	$y2 .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
					b.tg$y AS besartagihan,
					b.by$y AS tunggakan,
					$y AS tg
					FROM besarjtt b
					LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
					WHERE dp.aktif = 1
					AND dp.spp = 1
					AND dp.idkategori = 'JTT'
					AND dp.type_pembayaran = 1
					AND dp.tahunberjalan <> '1'
					AND b.nis = '{$id_key}' UNION ALL ";
	$y3 .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
					b.tg$y AS besartagihan,
					b.by$y AS tunggakan,
					$y AS tg
					FROM besarjtt b
					LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
					WHERE dp.aktif = 1
					AND dp.pangkal = 1
					AND dp.idkategori = 'JTT'
					AND dp.type_pembayaran = 1
					AND dp.tahunberjalan <> '1'
					AND b.nis = '{$id_key}' UNION ALL ";
	$y4 .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
					b.tg$y AS besartagihan,
					b.by$y AS tunggakan,
					$y AS tg
					FROM besarjtt b
					LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
					WHERE dp.aktif = 1
					AND dp.kegiatan = 1
					AND dp.idkategori = 'JTT'
					AND dp.type_pembayaran = 1
					AND dp.tahunberjalan <> '1'
					AND b.nis = '{$id_key}' UNION ALL ";
	$y5 .= "SELECT CONCAT(dp.nama, ' - SIS') AS namatagihan,
					b.tg$y AS besartagihan,
					b.by$y AS tunggakan,
					$y AS tg
					FROM besarjtt b
					LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
					WHERE dp.aktif = 1
					AND dp.lainlain = 1
					AND dp.idkategori = 'JTT'
					AND dp.type_pembayaran = 1
					AND dp.tahunberjalan <> '1'
					AND b.nis = '{$id_key}' UNION ALL ";
}

$besarbul				= substr($a, 0, -1);
$besartg				= substr($b, 0, -1);
$caritunggakan	= substr($t, 0, -10) . " UNION ALL " . substr($y1, 0, -10);
$carispp				= substr($s, 0, -10) . " UNION ALL " . substr($y2, 0, -10);
$caripangkal		= substr($p, 0, -10) . " UNION ALL " . substr($y3, 0, -10);
$carikegiatan		= substr($k, 0, -10) . " UNION ALL " . substr($y4, 0, -10);
$carilainlain		= substr($l, 0, -10) . " UNION ALL " . substr($y5, 0, -10);
//END CARI BULAN TAHUN SEKARANG

//BEGIN CARI DETAIL TAGIHAN
$sqlTunggakan	= "SELECT * FROM
									(SELECT CONCAT(dp.nama,' - SIS') AS namatagihan,
									b.besar AS besartagihan,
									SUM(b.jumlah+b.diskon) AS tunggakan, '' AS tg 
									FROM besarjtt b 
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.tunggakan = 1
									AND dp.idkategori = 'JTT'
									AND dp.type_pembayaran = 0
									AND b.nis = '{$id_key}'
									GROUP BY b.idpenerimaan
								UNION ALL
								SELECT CONCAT(dp.nama,' - CS') AS namatagihan,
								b.besar AS besartagihan,
								SUM(b.jumlah+b.diskon) AS tunggakan, '' AS tg
								FROM besarjttcalon b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								LEFT JOIN jbsakad.calonsiswa c ON c.replid = b.idcalon
								WHERE dp.aktif = 1
								AND dp.idkategori = 'CSWJB'
								AND c.nopendaftaran = '{$nopendaftaran}'
								GROUP BY b.idpenerimaan
								UNION ALL
								SELECT * FROM ($caritunggakan) AS y) AS x WHERE besartagihan-tunggakan <> 0";
$responsetunggakan	= JalankanSQL($perintah, $sqlTunggakan, 100);

$sqlPangkal	= "SELECT * FROM
								(SELECT dp.nama AS namatagihan,
								b.besar AS besartagihan,
								SUM(b.jumlah+b.diskon) AS tunggakan,
								'' AS tg
								FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.pangkal = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 0
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan
								UNION ALL
								SELECT * FROM ($caripangkal) AS y) AS x
								WHERE besartagihan-tunggakan <> 0";
$responsepangkal = JalankanSQL($perintah, $sqlPangkal, 100);

$sqlSPP			= "SELECT * FROM
								(SELECT dp.nama AS namatagihan,
								b.besar AS besartagihan,
								SUM(b.jumlah+b.diskon) AS tunggakan,
								'' AS tg
								FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.spp = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 0
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan
								UNION ALL
								SELECT * FROM ($carispp) AS y) AS x
								WHERE besartagihan-tunggakan <> 0";
$responsespp = JalankanSQL($perintah, $sqlSPP, 100);

$sqlKegiatan = "SELECT * FROM
								(SELECT dp.nama AS namatagihan,
								b.besar AS besartagihan,
								SUM(b.jumlah+b.diskon) AS tunggakan,
								'' AS tg
								FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.kegiatan = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 0
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan
								UNION ALL
								SELECT * FROM ($carikegiatan) AS y) AS x
								WHERE besartagihan-tunggakan <> 0";
$responsekegiatan = JalankanSQL($perintah, $sqlKegiatan, 100);

$query = "SELECT * FROM
								(SELECT dp.nama AS namatagihan,
								b.besar AS besartagihan,
								SUM(b.jumlah+b.diskon) AS tunggakan,
								'' AS tg
								FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.lainlain = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 0
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan
								UNION ALL
								SELECT * FROM ($carilainlain) AS y) AS x
								WHERE besartagihan-tunggakan <> 0";
$responselainlain = JalankanSQL($perintah, $query, 100);
//END CARI DETAIL TAGIHAN

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
						<h5 class="card-title text-primary">Informasi Detail Tagihan <?= NamaBulan($bulansekarang) . " " . $tahunsekarang ?></h5><br />
						<div class="card-text">
							<div class="table-responsive">
								<?php if ($responsetunggakan) { ?>
								<h6 class="text-primary">Data Tunggakan</h6>
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
										$i = 0;
										foreach ($responsetunggakan as $mydata) {
											$i = $i + 1;
											$totaltunggakan += $mydata->besartagihan - $mydata->tunggakan;
										?>
										<tr>
											<th scope="row"><?= $i ?></th>
											<td><?= $mydata->namatagihan . " " . NameOfMonth($mydata->tg) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan - $mydata->tunggakan) ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
								<?php } ?>
								<?php if ($responsespp) { ?>
								<br />
								<h6 class="text-primary">Data Uang SPP</h6>
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
										$i = 0;
										foreach ($responsespp as $mydata) {
											$i = $i + 1;
											$totalspp += $mydata->besartagihan - $mydata->tunggakan
										?>
										<tr>
											<th scope="row"><?= $i ?></th>
											<td><?= $mydata->namatagihan . " " . NameOfMonth($mydata->tg) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan - $mydata->tunggakan) ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
								<?php } ?>
								<?php if ($responsepangkal) { ?>
								<br />
								<h6 class="text-primary">Data Uang Pangkal</h6>
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
										$i = 0;
										foreach ($responsepangkal as $mydata) {
											$i = $i + 1;
											$totalpangkal += $mydata->besartagihan - $mydata->tunggakan;
										?>
										<tr>
											<th scope="row"><?= $i ?></th>
											<td><?= $mydata->namatagihan . " " . NameOfMonth($mydata->tg) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan - $mydata->tunggakan) ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
								<?php } ?>
								<?php if ($responsekegiatan) { ?>
								<br />
								<h6 class="text-primary">Data Uang Kegiatan</h6>
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
										$i = 0;
										foreach ($responsekegiatan as $mydata) {
											$i = $i + 1;
											$totalkegiatan += $mydata->besartagihan - $mydata->tunggakan;
										?>
										<tr>
											<th scope="row"><?= $i ?></th>
											<td><?= $mydata->namatagihan . " " . NameOfMonth($mydata->tg) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan - $mydata->tunggakan) ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
								<?php } ?>
								<?php if ($responselainlain) { ?>
								<br />
								<h6 class="text-primary">Data Uang Lain-Lain</h6>
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
										$i = 0;
										foreach ($responselainlain as $mydata) {
											$i = $i + 1;
											$totallainlain += $mydata->besartagihan - $mydata->tunggakan;
										?>
										<tr>
											<th scope="row"><?= $i ?></th>
											<td><?= $mydata->namatagihan . " " . NameOfMonth($mydata->tg) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan) ?></td>
											<td><?= FormatRupiah($mydata->besartagihan - $mydata->tunggakan) ?></td>
										</tr>
										<?php } ?>
									</tbody>
								</table>
								<?php } ?>
								<?php $totalbayar = $totaltunggakan + $totalpangkal + $totalspp + $totalkegiatan + $totallainlain; ?>
								<br />
								<p class="fs-6">Total tagihan : <?= FormatRupiah($totalbayar) ?></p>
								<p class="fs-6">Batas bayar : 10-<?= $bulansekarang ?>-<?= $tahunsekarang ?></p>
							</div>
							<div class="col-auto">
								<!--<a href="dashboard.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>" class="btn btn-primary"><i class="bi bi-arrow-bar-left"></i> Kembali ke Dashboard</a>-->

								<a href="prosespembayaran.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>&nominalbayar=<?= $totalbayar ?>&pilihbayar=0" class="btn btn-danger"><i class="bi bi-cash"></i> Bayar tagihan</a>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
		<?php include('webpart/js.php') ?>
	</body>

</html>
