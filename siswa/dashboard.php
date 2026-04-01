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
$userId				= strtoupper($_REQUEST['user_id']);
if ($userId == 'DEMO') {
	$userId = '46257';
}
$today				= date('Y-m-d');
$hariIni			= date('d-m-Y');
$perintah			= 'view';
$limit				= 1;
$sqlUser			= "SELECT
									s.replid AS replid,
									s.nis AS id_key,
									s.idkelas,
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
$idKelas					= $dataUser[0]->idkelas;
$namakelas				= $dataUser[0]->namakelas;
$alamattinggal		= $dataUser[0]->alamattinggal;
$keterangansiswa	= $dataUser[0]->keterangan;
//END EKSTRAK RESPONSE

//BEGIN CARI BULAN TAHUN SEKARANG
$sqlBulan				= "SELECT MONTH(CURRENT_DATE()) AS bulan, YEAR(CURRENT_DATE()) AS tahun";
$dataBulan			= JalankanSQL($perintah, $sqlBulan, $limit);
$bulansekarang	= $dataBulan[0]->bulan;
$tahunsekarang	= $dataBulan[0]->tahun;
$bulantagihan		= realMonth($dataBulan[0]->bulan);
for ($i = 1; $i <= $bulantagihan; $i++) {
	$a .= 'b.by' . $i . '+';
	$b .= 'b.tg' . $i . '+';
	$c .= '(b.tg' . $i . ')-(' . 'b.by' . $i . ') + ';
	$x .= '(b.tg' . $i . ') + ';
	$xx .= '(b.by' . $i . ') + ';
	$t .= "SELECT b.replid, b.nis, b.tg$i-b.by$i AS hasil, $i AS tg FROM besarjtt b UNION ALL ";
}

$d						= substr($c, 0, -1);
$dd						= substr($x, 0, -1);
$ddd					= substr($xx, 0, -1);
$besarbul			= substr($a, 0, -1);
$besartg			= substr($b, 0, -1);
$caribulan		= substr($d, 0, -1);
$caribesar		= substr($dd, 0, -1);
$caritagihan	= substr($ddd, 0, -1);
//END CARI BULAN TAHUN SEKARANG

//BEGIN CARI NOPENDAFTARAN CASIS
$sqlNoDaftar		= "SELECT tds.teks AS nopendaftaran
									FROM jbsakad.tambahandatasiswa tds
									LEFT JOIN jbsakad.siswa s ON s.nis = tds.nis
									LEFT JOIN jbsakad.tambahandata td ON td.replid = tds.idtambahan
									WHERE td.kolom = 'NP_CalonSiswa'
									AND tds.nis = '{$id_key}'";
$dataNoDaftar		= JalankanSQL($perintah, $sqlNoDaftar, $limit);
$noPendaftaran	= $dataNoDaftar[0]->nopendaftaran;
//END CARI NOPENDAFTARAN CASIS

//BEGIN CARI BANK & VA
$sqlVA					= "SELECT b.bank, v.virtualaccount
									FROM jbsakad.va v
									LEFT JOIN jbsakad.siswa s ON s.nis = v.nis
									LEFT JOIN jbsakad.bank b ON b.replid = v.bank
									WHERE s.nis = '{$id_key}'
									AND v.statusaktif = 1
									AND jenis = 1";
$response				= JalankanSQL($perintah, $sqlVA, $limit);
$bank						= $response[0]->bank;
$virtualAccount	= $response[0]->virtualaccount;
//END CARI BANK & VA

//BEGIN CARI TUNGGAKAN
$sqlTunggakan		= "SELECT SUM(besar) AS besar
									FROM
										(SELECT SUM(b.besar) AS besar FROM besarjtt b 
										LEFT JOIN datapenerimaan dp ON dp.replid=b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.tunggakan = 1
										AND dp.idkategori = 'JTT'
										AND dp.type_pembayaran = 0
										AND b.nis = '{$id_key}'
										GROUP BY b.idpenerimaan
									UNION ALL
										SELECT SUM(besar)
										FROM
											(SELECT ((b.tg1) + (b.tg2) + (b.tg3) + (b.tg4) + (b.tg5) + (b.tg6) + (b.tg7) + (b.tg8) + (b.tg9) + (b.tg10) + (b.tg11) + (b.tg12)) AS besar
											FROM besarjtt b
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.tunggakan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan <> '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS y
											UNION ALL
										SELECT SUM(besar)
										FROM(
											SELECT ($caribesar) AS besar
											FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.tunggakan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan = '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS x)
										AS theo";
$dataTunggakan		= JalankanSQL($perintah, $sqlTunggakan, $limit);
$bBesarTunggakan	= $dataTunggakan[0]->besar;

$sqlBTunggakan		= "SELECT SUM(total) AS total
										FROM
											(SELECT SUM(b.jumlah + b.diskon) AS total FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.tunggakan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 0
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan
										UNION ALL
										SELECT SUM(total)
										FROM
											(SELECT ((b.by1) + (b.by2) + (b.by3) + (b.by4) + (b.by5) + (b.by6) + (b.by7) + (b.by8) + (b.by9) + (b.by10) + (b.by11) + (b.by12)) AS total FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.tunggakan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan <> '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS y
										UNION ALL
										SELECT SUM(total)
										FROM
											(SELECT ($caritagihan) AS total FROM besarjtt b
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.tunggakan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan = '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan)
										AS x) AS theo";
$dataBTunggakan	= JalankanSQL($perintah, $sqlBTunggakan, $limit);
$bTunggakan			= $dataBTunggakan[0]->total;

$sqlBesarJTT	= "SELECT SUM(b.besar) AS besar FROM besarjttcalon b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan 
								LEFT JOIN jbsakad.calonsiswa c ON c.replid = b.idcalon
								WHERE dp.aktif = 1
								AND dp.idkategori = 'CSWJB'
								AND c.nopendaftaran = '{$noPendaftaran}'
								GROUP BY b.idcalon";
$dataBesarJTT	= JalankanSQL($perintah, $sqlBesarJTT, $limit);
$bBesarTunggakanCalon	= $dataBesarJTT[0]->besar;

$sqlBesarJTT2 = "SELECT SUM(b.jumlah + b.diskon) AS total FROM besarjttcalon b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								LEFT JOIN jbsakad.calonsiswa c ON c.replid = b.idcalon
								WHERE dp.aktif = 1
								AND dp.idkategori = 'CSWJB'
								AND c.nopendaftaran = '{$noPendaftaran}'
								GROUP BY b.idcalon";
$dataBesarJTT2		= JalankanSQL($perintah, $sqlBesarJTT2, $limit);
$bTunggakanCalon	= $dataBesarJTT2[0]->total;

$jtunggakan				= ($bBesarTunggakan - $bTunggakan) + ($bBesarTunggakanCalon - $bTunggakanCalon);
//END CARI TUNGGAKAN

//BEGIN CARI SPP
$sqlCariSPP		= "SELECT SUM(besar) AS besar
								FROM
									(SELECT SUM(b.besar) AS besar FROM besarjtt b 
									LEFT JOIN datapenerimaan dp
									ON dp.replid = b.idpenerimaan 
									WHERE dp.aktif = 1
									AND dp.spp = 1
									AND dp.idkategori = 'JTT'
									AND dp.type_pembayaran = 0
									AND b.nis = '{$id_key}'
									GROUP BY b.idpenerimaan
								UNION ALL
								SELECT SUM(besar)
								FROM
									(SELECT ((b.tg1) + (b.tg2) + (b.tg3) + (b.tg4) + (b.tg5) + (b.tg6) + (b.tg7) + (b.tg8) + (b.tg9) + (b.tg10) + (b.tg11) + (b.tg12)) AS besar
									FROM besarjtt b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.spp = 1
									AND dp.idkategori = 'JTT'
									AND dp.type_pembayaran = 1
									AND dp.tahunberjalan <> '1'
									AND b.nis = '{$id_key}'
									GROUP BY b.idpenerimaan) AS y
								UNION ALL
								SELECT SUM(besar)
								FROM
									(SELECT ($caribesar) AS besar FROM besarjtt b 
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.spp = 1
									AND dp.idkategori = 'JTT'
									AND dp.type_pembayaran = 1
									AND dp.tahunberjalan = '1'
									AND b.nis = '{$id_key}'
									GROUP BY b.idpenerimaan) AS x)
								AS theo";
$dataCariSPP	= JalankanSQL($perintah, $sqlCariSPP, $limit);
$bbesarspp		= $dataCariSPP[0]->besar;

$sqlCariTagihan	= "SELECT SUM(total) AS total
									FROM
										(SELECT SUM(b.jumlah+b.diskon) AS total
										FROM besarjtt b 
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.spp = 1
										AND dp.idkategori = 'JTT'
										AND dp.type_pembayaran = 0
										AND b.nis = '{$id_key}'
										GROUP BY b.idpenerimaan
									UNION ALL
									SELECT SUM(total)
									FROM
										(SELECT ((b.by1) + (b.by2) + (b.by3) + (b.by4) + (b.by5) + (b.by6) + (b.by7) + (b.by8) + (b.by9) + (b.by10) + (b.by11) + (b.by12)) AS total
										FROM besarjtt b 
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.spp = 1
										AND dp.idkategori = 'JTT'
										AND dp.type_pembayaran = 1
										AND dp.tahunberjalan <> '1'
										AND b.nis = '{$id_key}'
										GROUP BY b.idpenerimaan) AS y
									UNION ALL
									SELECT SUM(total)
									FROM
										(SELECT ($caritagihan) AS total FROM besarjtt b
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.spp = 1
										AND dp.idkategori = 'JTT'
										AND dp.type_pembayaran = 1
										AND dp.tahunberjalan = '1'
										AND b.nis = '{$id_key}'
										GROUP BY b.idpenerimaan) AS x) AS theo";
$dataTagihan	= JalankanSQL($perintah, $sqlCariTagihan, $limit);
$bspp					= $dataTagihan[0]->total;
$jspp					= $bbesarspp - $bspp;

//BEGIN CARI PANGKAL
$sqlCariPangkal	= "SELECT SUM(besar) AS besar
									FROM
										(SELECT SUM(b.besar) AS besar
										from besarjtt b 
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan 
										WHERE dp.aktif = 1
										AND dp.pangkal = 1
										AND dp.idkategori = 'JTT'
										AND dp.type_pembayaran = 0
										AND b.nis = '{$id_key}'
										GROUP BY b.idpenerimaan
									UNION ALL
									SELECT SUM(besar)
									FROM
										(SELECT ((b.tg1) + (b.tg2) + (b.tg3) + (b.tg4) + (b.tg5) + (b.tg6) + (b.tg7) + (b.tg8) + (b.tg9) + (b.tg10) + (b.tg11) + (b.tg12)) AS besar FROM besarjtt b 
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.pangkal = 1
										AND dp.idkategori = 'JTT'
										AND dp.type_pembayaran = 1
										AND dp.tahunberjalan <> '1'
										AND b.nis = '{$id_key}'
										GROUP BY b.idpenerimaan) AS y
									UNION ALL
									SELECT SUM(besar)
									FROM
										(SELECT ($caribesar) AS besar FROM besarjtt b 
										LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
										WHERE dp.aktif = 1
										AND dp.pangkal = 1
										AND dp.idkategori = 'JTT'
										AND dp.type_pembayaran = 1
										AND dp.tahunberjalan = '1'
										AND b.nis = '{$id_key}'
										GROUP BY b.idpenerimaan) AS x)
										AS theo";
$dataCariPangkal	= JalankanSQL($perintah, $sqlCariPangkal, $limit);
$bbesarpangkal		= $dataCariPangkal[0]->besar;

$dataCariPangkal2	= "SELECT SUM(total) AS total
										FROM
											(SELECT SUM(b.jumlah+b.diskon) AS total FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.pangkal = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 0
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan
										UNION ALL
										SELECT SUM(total)
										FROM
											(SELECT ((b.by1) + (b.by2) + (b.by3) + (b.by4) + (b.by5) + (b.by6) + (b.by7) + (b.by8) + (b.by9) + (b.by10) + (b.by11) + (b.by12)) AS total FROM besarjtt b
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.pangkal = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan <> '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS y
										UNION ALL
										SELECT SUM(total)
										FROM
											(SELECT ($caritagihan) AS total FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.pangkal = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan = '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS x)
										AS theo";
$sqlCariPangkal2	= JalankanSQL($perintah, $dataCariPangkal2, $limit);
$bpangkal					= $sqlCariPangkal2[0]->total;
$jpangkal					= $bbesarpangkal - $bpangkal;
//END CARI PANGKAL

//BEGIN CARI KEGIATAN
$sqlCariKegiatan	= "SELECT SUM(besar) AS besar
										FROM
											(SELECT SUM(b.besar) AS besar FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan 
											WHERE dp.aktif = 1
											AND dp.kegiatan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 0
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan
										UNION ALL
										SELECT SUM(besar)
										FROM
											(SELECT ((b.tg1) + (b.tg2) + (b.tg3) + (b.tg4) + (b.tg5) + (b.tg6) + (b.tg7) + (b.tg8) + (b.tg9) + (b.tg10) + (b.tg11) + (b.tg12)) AS besar FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.kegiatan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan <> '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS y
										UNION ALL
										SELECT SUM(besar)
										FROM
											(SELECT ($caribesar) AS besar FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.kegiatan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan = '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS x)
										AS theo";
$dataCariKegiatan = JalankanSQL($perintah, $sqlCariKegiatan, $limit);
$bbesarkegiatan		= $dataCariKegiatan[0]->besar;

$sqlCariKegiatan2	= "SELECT SUM(total) AS total
										FROM
										(SELECT	 SUM(b.jumlah+b.diskon) AS total FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.kegiatan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 0
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan
										UNION ALL
										SELECT SUM(total)
										FROM
											(SELECT ((b.by1) + (b.by2) + (b.by3) + (b.by4) + (b.by5) + (b.by6) + (b.by7) + (b.by8) + (b.by9) + (b.by10) + (b.by11) + (b.by12)) AS total FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.kegiatan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan <> '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS y
										UNION ALL
										SELECT SUM(total)
										FROM
											(SELECT ($caritagihan) AS total FROM besarjtt b 
											LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
											WHERE dp.aktif = 1
											AND dp.kegiatan = 1
											AND dp.idkategori = 'JTT'
											AND dp.type_pembayaran = 1
											AND dp.tahunberjalan = '1'
											AND b.nis = '{$id_key}'
											GROUP BY b.idpenerimaan) AS x)
										AS theo";
$dataCariKegiatan2	= JalankanSQL($perintah, $sqlCariKegiatan2, $limit);
$bkegiatan					= $dataCariKegiatan2[0]->total;
$jkegiatan					= $bbesarkegiatan - $bkegiatan;
//END CARI KEGIATAN

//BEGIN CARI LAIN-LAIN
$sqlCariLain	= "SELECT SUM(besar) AS besar
								FROM
									(SELECT SUM(b.besar) AS besar FROM besarjtt b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan 
									WHERE dp.aktif = 1
									AND dp.lainlain = 1
									AND dp.idkategori = 'JTT'
									AND dp.type_pembayaran = 0
									AND b.nis = '{$id_key}'
									GROUP BY b.idpenerimaan
								UNION ALL
								SELECT SUM(besar)
								FROM
									(SELECT ((b.tg1) + (b.tg2) + (b.tg3) + (b.tg4) + (b.tg5) + (b.tg6) + (b.tg7) + (b.tg8) + (b.tg9) + (b.tg10) + (b.tg11) + (b.tg12)) AS besar FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.lainlain = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 1
								AND dp.tahunberjalan <> '1'
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan) AS y
							UNION ALL
							SELECT SUM(besar)
							FROM
								(SELECT ($caribesar) AS besar FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.lainlain = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 1
								AND dp.tahunberjalan = '1'
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan) AS x)
							AS theo";
$dataCariLain		= JalankanSQL($perintah, $sqlCariLain, $limit);
$bbesarlainlain	= $dataCariLain[0]->besar;

$sqlCariLain2	= "SELECT SUM(total) AS total
								FROM
									(SELECT SUM(b.jumlah+b.diskon) AS total FROM besarjtt b
									LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
									WHERE dp.aktif = 1
									AND dp.lainlain = 1
									AND dp.idkategori = 'JTT'
									AND dp.type_pembayaran = 0
									AND b.nis = '{$id_key}'
									GROUP BY b.idpenerimaan
								UNION ALL
								SELECT SUM(total)
								FROM
									(SELECT ((b.by1) + (b.by2) + (b.by3) + (b.by4) + (b.by5) + (b.by6) + (b.by7) + (b.by8) + (b.by9) + (b.by10) + (b.by11) + (b.by12)) AS total FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.lainlain = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 1
								AND dp.tahunberjalan <> '1'
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan) AS y
							UNION ALL
							SELECT SUM(total)
							FROM
								(SELECT ($caritagihan) AS total FROM besarjtt b 
								LEFT JOIN datapenerimaan dp ON dp.replid = b.idpenerimaan
								WHERE dp.aktif = 1
								AND dp.lainlain = 1
								AND dp.idkategori = 'JTT'
								AND dp.type_pembayaran = 1
								AND dp.tahunberjalan = '1'
								AND b.nis = '{$id_key}'
								GROUP BY b.idpenerimaan) AS x)
							AS theo";
$dataCariLain2	= JalankanSQL($perintah, $sqlCariLain2, $limit);
$blainlain			= $dataCariLain2[0]->total;
$jlainlain			= $bbesarlainlain - $blainlain;
//END CARI LAIN-LAIN

$totalbayar = $jtunggakan + $jspp + $jpangkal + $jkegiatan + $jlainlain;

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$uri = $_SERVER['REQUEST_URI'];

$url_lengkap	= $protocol . '://' . $host . $uri;
$urlImg				= $protocol . '://' . $host; ?>

<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
</head>

<body>
	<?php include('webpart/sidebar.php') ?>
	<div id="main" class="container-fluid">
		<main>
			<div class="row py-1 my-4 text-start">
				<div class="col-9">
					<h5>Halo,</h5>
					<h5 class="text-primary"><?= $nama ?></h5>
				</div>
				<div class="col-3">
					<img class="img-thumbnail rounded mx-auto d-block" src="../assets/imgs/logo.png" alt="">
				</div>
			</div>

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
			<!--
			<div class="card card-custom p-2 my-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">
					<h5 class="card-title fw-bold text-primary">PRESENSI KALAM KUDUS FAIR <?= date('Y') ?></h5>
					<div class="card-text">
						<?php
						$combineId = $idKelas . ' - ' . $id_key; ?>
						<div class="row mb-3">
							<div class="col d-flex justify-content-center align-items-center">
								<img src="https://api.qrserver.com/v1/create-qr-code/?data=<?= $combineId ?>&size=200x200" class="img-thumbnail p-1" alt="" title="QR Code" />
							</div>
						</div>
						<div class="row">
							<div class="col">
								<h5 class="fw-bold text-primary">Petunjuk Presensi</h5>
								<ul class="list-group">
									<li class="list-group-item">Silakan menuju booth presensi KK Fair</li>
									<li class="list-group-item">Arahkan QR Code di atas ke QR Code scanner</li>
									<li class="list-group-item">Tunggu sampai muncul notifikasi berhasil</li>
									<li class="list-group-item">Presensi cukup 1x di masing-masing hari</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>-->

			<div class="card card-custom p-2 my-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">
					<h5 class="card-title text-primary">Informasi Tagihan <?= NamaBulan($bulansekarang) . " " . $tahunsekarang ?></h5><br />
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
							<!--<a href="rinciantagihan.php?mode=<?= $mode_akses ?>&user_id=<?= $userId ?>&bulansekarang=<?= $bulansekarang ?>&tahunsekarang=<?= $tahunsekarang ?>&nopendaftaran=<?= $noPendaftaran ?>" class="btn btn-primary"><i class="bi bi-eye"></i> Lihat rincian tagihan</a>

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