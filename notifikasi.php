<?php
session_start();

include 'constant.php';
include 'config.php';
include 'core.php';
include 'arisanconnection.php';

$mode	= $_REQUEST['mode'];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$goUserid		= $_REQUEST['user_id'];
$goStatus		= $_REQUEST['goStatus'];

$perintah		= 'view';
$limit			= 1;
$folder			= '';
$query			= "SELECT * FROM apps.notifikasi WHERE user ='$goUserid' AND isread=0";

if ($goStatus == 'Arisan') {
	$resNotif		= $koneksi->query($query);
	if ($resNotif->num_rows > 0) {
		while ($notif = $resNotif->fetch_assoc()) {
			$idNotif		= $notif['id'];
			$textNotif	= $notif['text'];
		}
	}
} else {
	$response		= JalankanSQL($perintah, $query, $limit);
	$idNotif		= $response[0]->id;
	$textNotif	= $response[0]->text;
}

$dataNotifJs	= ['textNotif' => $textNotif];
$dataArrayNotifJs	= [$dataNotifJs];
$jsonDataNotifJs	= json_encode($dataArrayNotifJs);
echo $jsonDataNotifJs;
