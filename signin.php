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

$userId			= $_REQUEST['goUserid'];
$psswd			= $_REQUEST['goPassword'];
$status			= $_REQUEST['goStatus'];
$fcmToken		= $_REQUEST['fcm_token'];

// Jika input berupa variasi dari kata "demo" (misal: DeMo, DEMO), paksa menjadi "demo"
if (strtolower($goUserid) == 'demo') {
	$goUserid = 'demo';
}
if (strtolower($goPassword) == 'demo') {
	$goPassword = 'demo';
}
// ---------------------------

$perintah		= 'view';
$limit			= 1;
$folder			= '';
$query = '';

if ($userId == 'demo' and $psswd == 'demo') {
	if ($status == 'Calon Siswa') {
		$userId		= '62627260166';
		$psswd	= '93482';
	} else if ($status == 'Siswa') {
		$userId		= '46257';
		$psswd	= '71445';
	} else if ($status == 'SDM') {
		$userId		= 'demo';
		$psswd	= '99999';
	} else if ($status == 'Arisan') {
		$userId		= 'demo';
		$psswd	= '9999';
	}
}

if ($status == 'Calon Siswa') {
	$query = "SELECT nopendaftaran AS id_key FROM jbsakad.calonsiswa WHERE nopendaftaran='$userId' AND info3='$psswd'";
	$folder = "casis/";
} else if ($status == 'Siswa') {
	$query = "SELECT s.replid as replid, s.nis AS id_key, nama, telponsiswa as telpon, hportu as hp, s.info1, s.info2, kelas as namakelas, alamatsiswa as alamattinggal, tingkat as namatingkat, s.keterangan FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t WHERE s.idkelas = k.replid AND t.replid = k.idtingkat AND s.nis='$userId' AND (s.pinsiswa='$psswd' OR s.pinortu='$psswd' OR s.pinortuibu='$psswd') ORDER BY s.replid DESC";
	$folder = "siswa/";
} else if ($status == 'Arisan') {
	$userId		= strtolower($userId);
	$query = "SELECT *, user_id AS id_key FROM socmyid_arisan_solo.peserta WHERE user_id = '$userId' AND token='$psswd' AND aktif = 1";
	$folder = "arisan/";
} else if ($status == 'SDM') {
	$userId		= strtoupper($userId);
	/*if ($userId == 'demo'){
        $query = "SELECT *, user_id AS id_key, modul FROM skk_presensigukar.user WHERE user_id = 'demo' AND token = '99999'";
    } else {*/
	$query = "SELECT *, user_id AS id_key, modul FROM skk_presensigukar.user WHERE user_id = '$userId' AND token = '$psswd'";
	//}

	$folder = "sdm/";
}

$transposeUserId	= '';
if ($status == 'Arisan') {
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
	if ($status == 'SDM') {
		$nowUserId = strtoupper($userId);
	} else {
		$nowUserId = $userId;
	}
	if ($response[0]->id_key == $nowUserId) {
		$isAccepted	= 'loginAccepted';
		$modul		= $response[0]->modul;
	} else {
		$isAccepted	= 'loginFailed';
	}
}

$xxx = $response[0]->id_key . '-' . $nowUserId;


$urlUtama					= 'https://soc.my.id/';
$dataLoginJs			= ['urlUtama' => $urlUtama, 'jsonFolder' => $folder, 'modulMobileApps' => $modul, 'isAccepted' => $isAccepted, 'query' => $query];
$dataArrayLoginJs	= [$dataLoginJs];
$jsonDataLoginJs	= json_encode($dataArrayLoginJs);
echo $jsonDataLoginJs;
