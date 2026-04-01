<?php
session_start();

include 'constant.php';
include 'config.php';
include 'core.php';

$token = $_COOKIE['remember'];

$mode = $_REQUEST["mode"];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

$titlePage	= 'Sign in - ' . APPS_NAME;

$status = '';
if (isset($_POST['captcha']) && ($_POST['captcha'] != "")) {
	if (strcasecmp($_SESSION['captcha'], $_POST['captcha']) != 0) {
		$message = ["Peringatan", "Kode captcha yang dimasukkan tidak cocok! Silakan coba lagi.", "error"];
	} else if (isset($_REQUEST['masuk'])) {
		$goUserid = $_REQUEST["goUserid"];
		$goStatus = $_REQUEST["goStatus"];
		$goPassword = $_REQUEST["goPassword"];

		$perintah = "view";
		$limit = 1;

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
			$_SESSION["username"] = $username;
			$_SESSION["goUserid"] = $goUserid;
			$_SESSION["goStatus"] = $goStatus;
			$_SESSION["goPassword"]	= $goPassword;
			$_SESSION["folder"] = $folder;
			$_SESSION["response"] = $response;
			$_SESSION["logged_in"] = true;
			$_SESSION["notif"] = true;

			$jumlahhari = 1095;

			$token = bin2hex(random_bytes(32));
			$expires = date('Y-m-d H:i:s', time() + ($jumlahhari * 24 * 60 * 60)); // 365 hari

			$perintah = "insert";
			$limit = 1;
			$query = "INSERT INTO apps.user_tokens SET username='" . $username . "', goUserid='" . $goUserid . "', goStatus='" . $goStatus . "', goPassword='" . $goPassword . "', folder='" . $folder . "', token='" . $token . "', expires='" . $expires . "'";

			$response = JalankanSQL($perintah, $query, $limit);

			setcookie("remember", $token, time() + ($jumlahhari * 24 * 60 * 60), "/", "", false, true);

			header("Location: " . $folder . "dashboard.php?mode=" . $mode_akses);
			exit;
		} else {
			$message = ["Peringatan", "Username/password salah!", "danger"];
		}
	} else {
		$message = ["Informasi", "Kode captcha Anda cocok.", "success"];
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
	<?php include('webpart/js.php') ?>
	<link rel="stylesheet" href="assets/css/signin.css">
	<script>
		function refreshCaptcha() {
			var img = document.images['captcha_image'];
			img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + Math.random() * 1000;
		}

		$(document).ready(function() {
			setTimeout(function() {
				$(".alert").alert('close');
			}, 3000);
		});

		function onlyLetters(event) {
			var charCode = event.which ? event.which : event.keyCode;
			if (charCode === 39 || charCode === 34) {
				return false;
			}
			return true;
		}
	</script>
</head>

<body class="d-flex align-items-center py-4 bg-body-tertiary">
	<main class="form-signin w-100 m-auto">
		<form id="signin" method="post" enctype="multipart/form-data">
			<img class="d-block mx-auto mb-4" src="assets/imgs/logo-text.png" alt="" width="80%">
			<div class="form-floating">
				<input type="text" name="goUserid" id="goUserid" class="form-control" id="floatingInput" onkeypress="return onlyLetters(event)" required>
				<label for="floatingInput">Masukkan User ID</label>
			</div>
			<br />
			<div class="form-floating">
				<input type="password" name="goPassword" id="goPassword" class="form-control" id="floatingPassword" required>
				<label for="floatingPassword">Masukkan Password</label>
			</div>
			<br />
			<div class="form-floating">
				<select name="goStatus" id="goStatus" class="form-select" id="floatingStatus" required>
					<option value="">[ Pilih ]</option>
					<option value="casis">Calon Siswa</option>
					<option value="siswa">Siswa</option>
					<option value="arisan">Arisan</option>
					<option value="sdm">Guru - Staff</option>
				</select>
				<label for="floatingStatus">Pilih Status</label>
			</div>
			<br />
			<div class="form-floating">
				<?php
				//echo $token = bin2hex(random_bytes(32));
				?>
				<input type="text" name="captcha" class="form-control" id="floatingCaptcha" required>
				<label for="floatingCaptcha">Masukkan Captcha</label>
				<p class="text-center mt-2"><img src="captcha.php?rand=<?= rand(); ?>" id='captcha_image'></p>
				<p class="text-center"><a href='javascript: refreshCaptcha();' class="text-primary text-decoration-none fw-bold"><i class="bi bi-arrow-clockwise"></i> Refresh Captcha</a></p>
			</div>
			<?php
			if (isset($message)) {
				echo "<script>showAlert('" . $message[0] . "', '" . $message[1] . "', '" . $message[2] . "');</script>";
			}
			?>
			<div class="my-3">
				<button type="submit" name="masuk" id="masuk" class="btn btn-primary w-100 py-2">Masuk</button>
				<p class="mt-5 mb-3 text-secondary text-center">Departemen IT SKKK Surakarta <i class="bi bi-c-square"></i> <?= TAHUN ?></p>
			</div>
		</form>
	</main>
</body>

</html>