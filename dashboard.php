<?php
session_start();
include 'constant.php';
include 'config.php';
include 'core.php';

$token = $_COOKIE['remember'];
//echo $token;

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
	header('Location: signin.php');
	exit;
} else {
	$titlePage		= 'Dashboard - ' . APPS_NAME;
	$userName	= $_SESSION['username'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
</head>

<body>
	<main>
		<?php include('webpart/topbar.php') ?>
		<div class="px-4 py-5 my-5 text-center">
			<img class="d-block mx-auto mb-4 img-fluid" src="assets/imgs/logo.png" alt="">
			<h1 class="display-5 fw-bold text-body-emphasis"><?= SCHOOL_NAME ?></h1>
			<div class="col-lg-6 mx-auto">
				<h2>Welcome, <?= $userName; ?></h2>
				<p class="lead mb-4">Quickly design and customize responsive mobile-first sites with Bootstrap, the world’s most popular front-end open source toolkit, featuring Sass variables and mixins, responsive grid system, extensive prebuilt components, and powerful JavaScript plugins.</p>
				<div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
					<button type="button" class="btn btn-primary btn-lg px-4 gap-3">Primary button</button>
					<button type="button" class="btn btn-outline-secondary btn-lg px-4">Secondary</button>
				</div>
			</div>
		</div>
		<?php include('webpart/navbar_bottom.php') ?>
	</main>
</body>

</html>