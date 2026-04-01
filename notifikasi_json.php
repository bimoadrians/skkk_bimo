<?php
session_start();

include 'constant.php';
include 'config.php';
include 'core.php';
//include 'arisanconnection.php';

$mode	= $_REQUEST['mode'];
if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$userId				= strtolower($_REQUEST['user_id']);
$goStatus			= $_REQUEST['goStatus'];
$tableNotif		= 'apps';
$titleHeader	= 'Notifikasi';
$perintah			= 'view';
$limit				= 999;
$limitOne			= 1;
$folder				= '';
if ($goStatus == 'Arisan') {
	$folder			= '/arisan';
} elseif ($goStatus == 'SDM') {
	$folder			= '/sdm';
} elseif ($goStatus == 'CalonSiswa') {
	$folder			= '/casis';
} elseif ($goStatus == 'Akademik') {
	$folder			= '/siswa';
}

$qCount		= "SELECT 
							(SELECT COUNT(id) FROM $tableNotif.notifikasi 
							WHERE user = '$userId' AND folder = '$folder' AND isread=0) AS numid,
						n.id, n.judul, n.berita
						FROM $tableNotif.notifikasi n
						WHERE n.user = '$userId' AND n.folder = '$folder' AND n.isread = 0
						ORDER BY n.id DESC";
$dataNum	= JalankanSQL($perintah, $qCount, $limit);
$jumlahNotifBelumBaca	= $dataNum[0]->numid;
$idBaru			= $dataNum[0]->id;
$judulBaru	= $dataNum[0]->judul;
$teksBaru		= $dataNum[0]->berita;


$dataNotifJs	= ['belumBaca' => $jumlahNotifBelumBaca, 'idBaru' => $idBaru, 'judulBaru' => $judulBaru, 'teksBaru' => $teksBaru];
$dataArrayNotifJs	= [$dataNotifJs];
$jsonDataNotifJs	= json_encode($dataArrayNotifJs);
echo $jsonDataNotifJs;
