<?php
$host			= "localhost"; // atau IP server, misalnya 127.0.0.1
$username	= "socmyid_arisan_solo"; // username MySQL
$password	= "w#g1f{U5?&[q"; // password MySQL
$database	= "socmyid_arisan_solo"; // ganti dengan nama database kamu

// Membuat koneksi
$koneksi	= new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($koneksi->connect_error) {
	die("Koneksi gagal: " . $koneksi->connect_error);
}
