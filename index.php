<?php
session_start();

include "constant.php";
include "config.php";
include "core.php";

$token = $_COOKIE['remember'];
//echo $token;

$username = $_SESSION["username"];
$goUserid = $_SESSION["goUserid"];
$goStatus = $_SESSION["goStatus"];
$goPassword = $_SESSION["goPassword"];
$folder = $_SESSION["folder"];
$response = $_SESSION["response"];

if(isset($_GET['logout'])){
	unset($username);
	unset($goUserid);
	unset($goStatus);
	unset($goPassword);
	unset($folder);
	unset($response);
	setcookie("remember", "", time() - 3600, "/");
	
	$perintah = "delete";
	$limit = 1;
	
	$query = "DELETE FROM apps.user_tokens WHERE token='" . $token . "'";
	
	$response = JalankanSQL($perintah, $query, $limit);
	
	session_destroy();
	header("Location: signin.php?mode=" . $mode_akses);
	exit;
};

$mode = $_REQUEST["mode"];

if ($mode == $mode_akses) {
	
	$perintah = "view";
	$limit = 1;
	
	$query = "SELECT * FROM apps.user_tokens WHERE token='" . $token . "'";
	
	$response = JalankanSQL($perintah, $query, $limit);

	if ($response[0]->token != "" && $response[0]->token == $token) {
		$_SESSION["username"] = $response[0]->username;
		$_SESSION["goUserid"] = $response[0]->goUserid;
		$_SESSION["goStatus"] = $response[0]->goStatus;
		$_SESSION["goPassword"]	= $response[0]->goPassword;
		$_SESSION["folder"] = $response[0]->folder;
		$_SESSION["response"] = var_dump($response[0]->response);
		$_SESSION["logged_in"] = true;
		$_SESSION["notif"] = true;
	
		header("Location: " . $_SESSION["folder"] . "dashboard.php?mode=" . $mode_akses);
	} else {
		header("Location: signin.php?mode=" . $mode_akses);
		exit;
	}
	
	//if (!isset($_SESSION["logged_in"]) || !$_SESSION["logged_in"]) {
	//	header("Location: signin.php?mode=" . $mode_akses);
	//	exit;
	//} else {
	//	header("Location: " . $_SESSION["folder"] . "dashboard.php?mode=" . $mode_akses);
	//}
} else {
	header("Location: " . $skkksurakarta);
	die();
}
?>