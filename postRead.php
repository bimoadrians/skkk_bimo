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

$recentId				= strtolower($_REQUEST['recentId']);
$goStatus				= $_REQUEST['goStatus'];
$tableNotif			= 'apps';
$titleHeader		= 'Notifikasi';
$updateCommand	= 'update';
$limit					= 1;
$folder					= '';
if ($goStatus == 'Arisan') {
	$folder			= '/arisan';
} elseif ($goStatus == 'SDM') {
	$folder			= '/sdm';
} elseif ($goStatus == 'CalonSiswa') {
	$folder			= '/casis';
} elseif ($goStatus == 'Akademik') {
	$folder			= '/siswa';
}

$isReadSql		= "UPDATE $tableNotif.notifikasi SET isread = 1 WHERE id = '$recentId' AND folder = '$folder'";
$runCommand	= JalankanSQL($updateCommand, $isReadSql, $limit);
