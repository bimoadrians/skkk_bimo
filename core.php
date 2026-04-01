<?php

function ConvertWAToHTML($message){
	$message = str_replace('~r', '<br />', $message);
	$styles = array ('*' => 'b', '_' => 'i', '~' => 'strike');
	return preg_replace_callback('/(?<!\w)([*~_])(.+?)\1(?!\w)/',
		function($m) use($styles) { 
			return '<'. $styles[$m[1]]. '>'. $m[2]. '</'. $styles[$m[1]]. '>';
		},
	$message);
}

function NamaBulan($bln) {
	if ($bln == 1)
		return "Januari";
	elseif ($bln == 2)
		return "Februari";		
	elseif ($bln == 3)
		return "Maret";		
	elseif ($bln == 4)
		return "April";		
	elseif ($bln == 5)
		return "Mei";
	elseif ($bln == 6)
		return "Juni";		
	elseif ($bln == 7)
		return "Juli";
	elseif ($bln == 8)
		return "Agustus";		
	elseif ($bln == 9)
		return "September";
	elseif ($bln == 10)
		return "Oktober";		
	elseif ($bln == 11)
		return "November";
	elseif ($bln == 12)
		return "Desember";		
}

function NameOfMonth($bln) {
	if ($bln == 1 OR $bln == 13 OR $bln == 25 OR $bln == 37 OR $bln == 49 OR $bln == 61)
		return "Juli";
	elseif ($bln == 2 OR $bln == 14 OR $bln == 26 OR $bln == 38 OR $bln == 50 OR $bln == 62)
		return "Agustus";		
	elseif ($bln == 3 OR $bln == 15 OR $bln == 27 OR $bln == 39 OR $bln == 51 OR $bln == 63)
		return "September";		
	elseif ($bln == 4 OR $bln == 16 OR $bln == 28 OR $bln == 40 OR $bln == 52 OR $bln == 64)
		return "Oktober";		
	elseif ($bln == 5 OR $bln == 17 OR $bln == 29 OR $bln == 41 OR $bln == 53 OR $bln == 65)
		return "November";
	elseif ($bln == 6 OR $bln == 18 OR $bln == 30 OR $bln == 42 OR $bln == 54 OR $bln == 66)
		return "Desember";		
	elseif ($bln == 7 OR $bln == 19 OR $bln == 31 OR $bln == 43 OR $bln == 55 OR $bln == 67)
		return "Januari";
	elseif ($bln == 8 OR $bln == 20 OR $bln == 32 OR $bln == 44 OR $bln == 56 OR $bln == 68)
		return "Februari";		
	elseif ($bln == 9 OR $bln == 21 OR $bln == 33 OR $bln == 45 OR $bln == 57 OR $bln == 69)
		return "Maret";
	elseif ($bln == 10 OR $bln == 22 OR $bln == 34 OR $bln == 46 OR $bln == 58 OR $bln == 70)
		return "April";		
	elseif ($bln == 11 OR $bln == 23 OR $bln == 35 OR $bln == 47 OR $bln == 59 OR $bln == 71)
		return "Mei";
	elseif ($bln == 12 OR $bln == 24 OR $bln == 36 OR $bln == 48 OR $bln == 60 OR $bln == 72)
		return "Juni";		
}

function ShortNameOfMonth($bln) {
	if ($bln == 1 OR $bln == 13 OR $bln == 25 OR $bln == 37 OR $bln == 49 OR $bln == 61)
		return "Jul";
	elseif ($bln == 2 OR $bln == 14 OR $bln == 26 OR $bln == 38 OR $bln == 50 OR $bln == 62)
		return "Agt";		
	elseif ($bln == 3 OR $bln == 15 OR $bln == 27 OR $bln == 39 OR $bln == 51 OR $bln == 63)
		return "Sep";		
	elseif ($bln == 4 OR $bln == 16 OR $bln == 28 OR $bln == 40 OR $bln == 52 OR $bln == 64)
		return "Okt";		
	elseif ($bln == 5 OR $bln == 17 OR $bln == 29 OR $bln == 41 OR $bln == 53 OR $bln == 65)
		return "Nov";
	elseif ($bln == 6 OR $bln == 18 OR $bln == 30 OR $bln == 42 OR $bln == 54 OR $bln == 66)
		return "Des";		
	elseif ($bln == 7 OR $bln == 19 OR $bln == 31 OR $bln == 43 OR $bln == 55 OR $bln == 67)
		return "Jan";
	elseif ($bln == 8 OR $bln == 20 OR $bln == 32 OR $bln == 44 OR $bln == 56 OR $bln == 68)
		return "Feb";		
	elseif ($bln == 9 OR $bln == 21 OR $bln == 33 OR $bln == 45 OR $bln == 57 OR $bln == 69)
		return "Mar";
	elseif ($bln == 10 OR $bln == 22 OR $bln == 34 OR $bln == 46 OR $bln == 58 OR $bln == 70)
		return "Apr";		
	elseif ($bln == 11 OR $bln == 23 OR $bln == 35 OR $bln == 47 OR $bln == 59 OR $bln == 71)
		return "Mei";
	elseif ($bln == 12 OR $bln == 24 OR $bln == 36 OR $bln == 48 OR $bln == 60 OR $bln == 72)
		return "Jun";		
}

function realMonth($bln){
	if ($bln == 1 OR $bln == 13 OR $bln == 25 OR $bln == 37 OR $bln == 49 OR $bln == 61)
		return "7";
	elseif ($bln == 2 OR $bln == 14 OR $bln == 26 OR $bln == 38 OR $bln == 50 OR $bln == 62)
		return "8";		
	elseif ($bln == 3 OR $bln == 15 OR $bln == 27 OR $bln == 39 OR $bln == 51 OR $bln == 63)
		return "9";		
	elseif ($bln == 4 OR $bln == 16 OR $bln == 28 OR $bln == 40 OR $bln == 52 OR $bln == 64)
		return "10";		
	elseif ($bln == 5 OR $bln == 17 OR $bln == 29 OR $bln == 41 OR $bln == 53 OR $bln == 65)
		return "11";
	elseif ($bln == 6 OR $bln == 18 OR $bln == 30 OR $bln == 42 OR $bln == 54 OR $bln == 66)
		return "12";		
	elseif ($bln == 7 OR $bln == 19 OR $bln == 31 OR $bln == 43 OR $bln == 55 OR $bln == 67)
		return "1";
	elseif ($bln == 8 OR $bln == 20 OR $bln == 32 OR $bln == 44 OR $bln == 56 OR $bln == 68)
		return "2";		
	elseif ($bln == 9 OR $bln == 21 OR $bln == 33 OR $bln == 45 OR $bln == 57 OR $bln == 69)
		return "3";
	elseif ($bln == 10 OR $bln == 22 OR $bln == 34 OR $bln == 46 OR $bln == 58 OR $bln == 70)
		return "4";		
	elseif ($bln == 11 OR $bln == 23 OR $bln == 35 OR $bln == 47 OR $bln == 59 OR $bln == 71)
		return "5";
	elseif ($bln == 12 OR $bln == 24 OR $bln == 36 OR $bln == 48 OR $bln == 60 OR $bln == 72)
		return "6";		
}

function FormatRupiah($value)
{
	$value = number_format($value, '0', ",", "");
	$duit = (string)$value;
	$duit = trim($duit);
	if (strlen($duit) == 0) return "";
	if (strstr($duit, "E"))
		return "Rp " . $duit;

	$posmin = strpos($duit, "-");
	if ($posmin === false)
		$negatif = 0;
	else
		$negatif = 1;
	if ($negatif)
		$duit = str_replace("-", "", $duit);
	$len = strlen($duit);
	$nPoint = (int)($len / 3);
	if (($len % 3) == 0)
		$nPoint--;
	$rp = "";
	for ($i = 0; $i < $nPoint; $i++) {
		$j = 0;
		$temp = "";
		while ((strlen($duit) >= 0) && ($j++ < 3)) {
			$temp = substr($duit, strlen($duit) - 1, 1) . $temp;
			if (strlen($duit) >= 2)
				$duit = substr($duit, 0, strlen($duit) - 1);
			else
				$duit = "";
		}
		if (strlen($rp) > 0)
			$rp = $temp . "." . $rp;
		else
			$rp = $temp;
	}
	if (strlen($duit) > 0)
		$rp = $duit . "." . $rp;
	if ($negatif)
		return "(Rp " . $rp . ")";
	else
		return "Rp " . $rp;
}

function UnformatRupiah($value)
{
	$pos = strpos($value, "(");
	$negatif = true;
	if ($pos === false)
		$negatif = false;
	$value = str_replace("Rp", "", $value);
	$value = str_replace(".", "", $value);
	$value = str_replace(" ", "", $value);
	$value = str_replace("(", "", $value);
	$value = str_replace(")", "", $value);
	if ($negatif)
		$num = "-" . $value;
	else
		$num = $value;

	return (float)$num;
}

function rpad($string, $padchar, $length)
{
	$result = trim($string);
	if (strlen($result) < $length) {
		$nzero = $length - strlen($result);
		$zero = "";
		for ($i = 0; $i < $nzero; $i++)
			$zero .= "0";
		$result = $zero . $result;
	}
	return $result;
}

function JalankanSQL($perintah, $query, $limit)
{
	include 'config.php';

	$url = $api_url . "proses.php";
	$ch = curl_init($url);

	//Parameter perintah ada 3 yaitu : view, insert, update
	//Tidak bisa melakukan penghapusan record data menggunakan API
	$data = "api_key_post=" . urlencode($api_key) . "&query=" . urlencode($query) . "&limit=" . urlencode($limit) . "&perintah=" . urlencode($perintah);

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
	return $data;
	//echo "<pre>";
	//print_r($data);
}

function JalankanSQLArisan($perintah, $query, $limit)
{
	include 'config.php';

	$url = $api_url_arisan . "proses.php";
	$ch = curl_init($url);

	//Parameter perintah ada 3 yaitu : view, insert, update
	//Tidak bisa melakukan penghapusan record data menggunakan API
	$data = "api_key_post=" . urlencode($api_key) . "&query=" . urlencode($query) . "&limit=" . urlencode($limit) . "&perintah=" . urlencode($perintah);

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
	return $data;
	//echo "<pre>";
	//print_r($data);
}
