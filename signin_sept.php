<?php
session_start();

include 'constant.php';
include 'config.php';
include 'core.php';
include 'arisanconnection.php';

$mode		= $_REQUEST['mode'];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$goUserid		= $_REQUEST['goUserid'];
$goPassword	= $_REQUEST['goPassword'];
$goStatus		= $_REQUEST['goStatus'];
$fcmToken		= $_REQUEST['fcm_token'];

$perintah		= 'view';
$limit			= 1;
$folder			= '';
$query = '';
if ($goStatus == 'Calon Siswa') {
	$query = "SELECT *, nopendaftaran AS id_key FROM jbsakad.calonsiswa WHERE nopendaftaran='" . $goUserid . "' AND pinsiswa='" . $goPassword . "' ORDER BY replid DESC";
	$folder = "casis/";
} else if ($goStatus == 'Siswa') {
	$query = "SELECT s.replid as replid, s.nis AS id_key, nama, telponsiswa as telpon, hportu as hp, s.info1, s.info2, kelas as namakelas, alamatsiswa as alamattinggal, tingkat as namatingkat, s.keterangan FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t WHERE s.idkelas = k.replid AND t.replid = k.idtingkat AND s.nis='" . $goUserid . "' AND (s.pinsiswa='" . $goPassword . "' OR s.pinortu='" . $goPassword . "' OR s.pinortuibu='" . $goPassword . "') ORDER BY s.replid DESC";
	$folder = "siswa/";
} else if ($goStatus == 'Arisan') {
	$goUserid		= strtolower($goUserid);
	$query = "SELECT *, user_id AS id_key FROM socmyid_arisan_solo.peserta WHERE user_id = '" . $goUserid . "' AND token='" . $goPassword . "' AND aktif = 1";
	$folder = "arisan/";
} else if ($goStatus == 'SDM') {
	$goUserid		= strtoupper($goUserid);
	$query = "SELECT *, user_id AS id_key FROM skk_presensigukar.user WHERE user_id = '" . $goUserid . "' AND token = '" . $goPassword . "'";
	$folder = "sdm/";
}

$transposeUserId	= '';
if ($goStatus == 'Arisan') {
	$resUser					= $koneksi->query($query);
	if ($resUser->num_rows > 0) {
		while ($dataUser = $resUser->fetch_assoc()) {
			$modul		= $dataUser['modul'];
		}
		$isAccepted	= 'loginAccepted';
	} else {
		$isAccepted	= 'loginFailed';
	}
} else {
	$response = JalankanSQL($perintah, $query, $limit);
	if ($response[0]->id_key == strtoupper($goUserid)) {
		$isAccepted	= 'loginAccepted';
		$modul			= $response[0]->modul;
	} else {
		$isAccepted	= 'loginFailed';
	}
}


$urlUtama					= 'https://soc.my.id/';
$dataLoginJs			= ['urlUtama' => $urlUtama, 'jsonFolder' => $folder, 'modulMobileApps' => $modul, 'isAccepted' => $isAccepted];
$dataArrayLoginJs	= [$dataLoginJs];
$jsonDataLoginJs	= json_encode($dataArrayLoginJs);
echo $jsonDataLoginJs;
