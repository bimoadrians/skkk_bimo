<?php
date_default_timezone_set('Asia/Jakarta');
header('Content-Type: application/json');
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';

/*$mode = $_REQUEST['mode'];
if ($mode != $mode_akses) {
	//header('Location: ' . $skkksurakarta);
	http_response_code(400);
	echo json_encode(["error" => "Mode tidak cocok: $mode"]);
	die();
}*/

$dbPresensi			= 'skk_presensigukar';
$dbInventori		= 'jbsinventori_2';
$userId					= strtoupper($_REQUEST['userId']);
$kodeInventori	= $_REQUEST['kodeInventori'];
$kondisi				= $_REQUEST['kondisi'];
$commandInsert	= 'insert';
$commandView		= 'view';
$limit					= 1;

$queryUser		= "SELECT nama FROM $dbPresensi.user WHERE user_id = '" . $userId . "'";
$runUser			= JalankanSQL($commandView, $queryUser, $limit);
$namaSarpras	= $runUser[0]->nama;
//cari inventori
$queryInventori			= "SELECT id_inventori FROM $dbInventori.inventori_detail_temp WHERE kode = '" . $kodeInventori . "'";
$responseInventori	= JalankanSQL($commandView, $queryInventori, $limit);
$idInventori				= $responseInventori[0]->id_inventori;

//cari periode
$queryPeriode			= "SELECT id, COUNT(id) AS sedangso FROM $dbInventori.stockopname_periode WHERE status_so = 1";
$responsePeriode	= JalankanSQL($commandView, $queryPeriode, $limit);
$masaSO						= $responsePeriode[0]->sedangso;
if($masaSO == 1){
	$idPeriode				= $responsePeriode[0]->id;
} else{
	$idPeriode				= 0;
}

$tglStockOpname			= date('Y-m-d H:i:s');
$berhasil						= 0;
if (isset($kodeInventori)) {
	if($masaSO == 1){
		$insertStockOpname	= "INSERT INTO $dbInventori.stockopname SET id_periode = '" . $idPeriode . "', id_inventori = '" . $idInventori . "', tgl_stockopname = '" . $tglStockOpname . "', status_barang = 1, kondisi = '" . $kondisi . "', sarpras = '" . $namaSarpras . "'";
		$responseInsert			= JalankanSQL($commandInsert, $insertStockOpname, $limit);
		$berhasil = 1;
	} elseif($masaSO == 0){
		$berhasil = 0;
	}
}

if ($berhasil == 1) {
	$isSuccess	= 'berhasil';
	$queryInsert = $insertStockOpname;
} else {
	$isSuccess	= 'gagal';
	$queryInsert = $insertStockOpname;
}
$dataJs	= ['hasil' => $isSuccess, 'q' => $queryInsert, 'kodeInv' => $kodeInventori];
$dataArray	= [$dataJs];
$jsonDataArray	= json_encode($dataArray);
echo $jsonDataArray;
