<?php
session_start();

include 'constant.php';
include 'config.php';
include 'core.php';

$token	= $_COOKIE['remember'];
$mode		= $_REQUEST['mode'];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$goUserid		= $_POST["goUserid"];
$goPassword	= $_POST["goPassword"];
$goStatus		= $_POST["goStatus"];
$version		= $_POST['version'];

$goUserid		= mysql_real_escape_string($goUserid);
$goPassword	= mysql_real_escape_string($goPassword);

$perintah		= "view";
$limit			= 1;

if ($goStatus == "casis") {
	$query = "SELECT *, nopendaftaran AS id_key FROM jbsakad.calonsiswa WHERE nopendaftaran='" . $goUserid . "' AND pinsiswa='" . $goPassword . "' ORDER BY replid DESC";
	$folder = "casis/";
} else if ($goStatus == "siswa") {
	$query = "SELECT s.replid as replid, s.nis AS id_key, nama, telponsiswa as telpon, hportu as hp, s.info1, s.info2, kelas as namakelas, alamatsiswa as alamattinggal, tingkat as namatingkat, s.keterangan FROM jbsakad.siswa s, jbsakad.kelas k, jbsakad.tingkat t WHERE s.idkelas = k.replid AND t.replid = k.idtingkat AND s.nis='" . $goUserid . "' AND (s.pinsiswa='" . $goPassword . "' OR s.pinortu='" . $goPassword . "' OR s.pinortuibu='" . $goPassword . "') ORDER BY s.replid DESC";
	$folder = "siswa/";
} else if ($goStatus == "arisan") {
	$query = "SELECT *, user_id AS id_key FROM skk_arisan.peserta WHERE user_id = '" . $goUserid . "' AND token='" . $goPassword . "' AND aktif = 1 ORDER BY id DESC";
	$folder = "arisan/";
} else if ($goStatus == "sdm") {
	$query = "SELECT *, user_id AS id_key FROM skk_presensigukar.user WHERE user_id = '" . $goUserid . "' AND token = '" . $goPassword . "'";
	$folder = "sdm/";
}

$response = JalankanSQL($perintah, $query, $limit);

if ($response[0]->id_key == $goUserid) {
	$username = $response[0]->nama;
	$_SESSION['username']		= $username;
	$_SESSION['goUserid']		= $goUserid;
	$_SESSION['goStatus']		= $goStatus;
	$_SESSION['goPassword']	= $goPassword;
	$_SESSION['folder']			= $folder;
	$_SESSION['response']		= $response;
	$_SESSION['logged_in']	= true;
	$_SESSION['notif']			= true;
	$jumlahhari		= 1095;
	$token				= bin2hex(random_bytes(32));
	$expires			= date('Y-m-d H:i:s', time() + ($jumlahhari * 24 * 60 * 60));

	$cmd = "insert";
	$limit = 1;
	$queryIns = "INSERT INTO apps.user_tokens SET username='" . $username . "', goUserid='" . $goUserid . "', goStatus='" . $goStatus . "', goPassword='" . $goPassword . "', folder='" . $folder . "', token='" . $token . "', expires='" . $expires . "'";
	$response = JalankanSQL($cmd, $queryIns, $limit);

	setcookie("remember", $token, time() + ($jumlahhari * 24 * 60 * 60), "/", "", false, true);

	//header("Location: " . $folder . "dashboard.php?mode=" . $mode_akses);
	echo 'berhasil';
	//exit;
} else {
	//$message = ["Peringatan", "Username/password salah!", "danger"];
	echo 'gagal';
}
//exit();
