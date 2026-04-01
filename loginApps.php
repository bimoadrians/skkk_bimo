<?php
include 'config.php';

$url					= $api_url . "proses.php";
$ch						= curl_init($url);
$dbGuKar			= 'jbssdm';
$dbArisan			= 'skk_arisan';
$usrnm				= $_REQUEST['usrnm'];
$statusUser		= $_REQUEST['status'];
/*if ($statusUser == 'Guru-Staff') {
	$query = "SELECT * FROM $dbGuKar.pegawai WHERE nip = '" . $usrnm . "'";
} elseif ($statusUser == 'Arisan') {
	$query = "SELECT * FROM $dbArisan.peserta WHERE telp = '" . $usrnm . "'";
}*/

$query = "SELECT * FROM $dbArisan.peserta ORDER BY id ASC";
$data = "api_key_post=" . $api_key . "&query=" . $query . "&limit=20&perintah=view";
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
$response = curl_exec($ch);
if (curl_error($ch)) {
	die('Error:' . curl_error($ch));
}
curl_close($ch);
$data = json_decode($response);
echo "<pre>";
print_r($data);
