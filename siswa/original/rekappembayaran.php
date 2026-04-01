<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';

$mode = $_REQUEST["mode"];

if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
	header("Location: ../signin.php?mode=" . $mode_akses);
	exit;
} else {
	$titlePage = 'Dashboard - ' . APPS_NAME;
	
	$username = $_SESSION["username"];
	$goUserid = $_SESSION["goUserid"];
	$goStatus = $_SESSION["goStatus"];
	$goPassword = $_SESSION["goPassword"];
	$folder = $_SESSION["folder"];
	$response = $_SESSION["response"];
	
	//BEGIN EKSTRAK RESPONSE
	$id_key = $response[0]->id_key;
	$nama = $response[0]->nama;
	$telpon = $response[0]->telpon;
	$hp1 = $response[0]->hp;
	$hp2 = $response[0]->info1;
	$hp3 = $response[0]->info2;
	$namatingkat = $response[0]->namatingkat;
	$namakelas = $response[0]->namakelas;
	$alamattinggal = $response[0]->alamattinggal;
	$keterangansiswa = $response[0]->keterangan;
	//END EKSTRAK RESPONSE
	
	//BEGIN CARI BANK & VA
	$query = "SELECT b.bank, v.virtualaccount FROM jbsakad.va v LEFT JOIN jbsakad.siswa s ON s.nis=v.nis LEFT JOIN jbsakad.bank b ON b.replid=v.bank WHERE s.nis='" . $id_key . "' and v.statusaktif=1 and jenis=1";
	$response = JalankanSQL("view", $query, 1);
	$bank = $response[0]->bank;
	$virtualaccount = $response[0]->virtualaccount;
	//END CARI BANK & VA	
	
	//BEGIN CARI REKAP PEMBAYARAN
	$query = "SELECT * FROM konversiva WHERE nis='" . $id_key . "' ORDER BY replid DESC";
	$response = JalankanSQL("view", $query, 300);
	//END CARI REKAP PEMBAYARAN
}

if (isset($_GET['logout'])) {
	unset($user_id);
	session_destroy();
	header("location: ../signin.php?mode=" . $mode_akses);
};

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<?php include('webpart/head.php') ?>
	<?php include('webpart/js.php') ?>
</head>

<body>
	<main>
		<?php include('webpart/topbar.php') ?>
		<div class="container-fluid">
			<div class="row pt-5 py-1 my-4 text-start">
				<div class="col-9">
					<h5 class="text-primary"><!-- BISA DIISI --></h5>
				</div>
			</div>
			
			<div class="card p-3 my-3 bg-image rounded-4 shadow" style="background-image: url('../assets/imgs/card.png'); ">
				<div class="card-body flex-column text-white">
					<h6 class="card-title">N I S : <?=$id_key . " / " . $nopendaftaran?></h6>
					<div class="card-text">
						<p class="fs-3 text-warning"><?=$nama?></p>
						<p class="fs-6">Kelas : <?=$namakelas?></p>
						<p><small>VA <?=$bank?> : <?=$kode_va_siswa . $virtualaccount?></small></p>
					</div>
				</div>
			</div>			

			<div class="card p-3 rounded-4 shadow-lg">
				<div class="card-body flex-column align-items-start">
					<h5 class="card-title text-primary">Informasi Rekap Pembayaran</h5><br />
					<div class="card-text">
						<div class="col-auto">
							<?php if ($response) { ?>
									<table class="table">
									  <thead>
										<tr>
										  <th scope="col" width="2">No</th>
										  <th scope="col" width="20">Informasi</th>
										</tr>
									  </thead>
									  <tbody>							
									<?php foreach($response as $mydata) {
										$i = $i+1;
										$tanggaltransaksi = date("d-m-Y", strtotime($mydata->tanggaltransaksi));
										$keteranganalokasi = str_replace("<+>", "<br />", $mydata->keteranganalokasi);
										$keteranganalokasi = preg_replace_callback("/@(\d+)@/", function($matches) {return ShortNameOfMonth((int)$matches[1] + 0); }, $keteranganalokasi);
										if (($mydata->nominal)-($mydata->info3) == 0) {
											$jenis = "primary";
											$warna = "<font color=''>";
										} else {
											$jenis = "danger";
											$warna = "<font color='red'>";
										}
									?>
									<tr>
										  <th scope="row"><?=$i?></th>
										  <td><sup><b>- Tanggal :</b> <?=$tanggaltransaksi?><br /><b>- Transfer VA :</b> <?=FormatRupiah(($mydata->nominal))?><br /><b>- Deposit :</b> <?=$warna . FormatRupiah(($mydata->nominal)-($mydata->info3))?><br /><br /><b>Alokasi</b><br /><?=str_replace("<+>", "<br />", $keteranganalokasi)?></sup></td>
									</tr>
								  <? } ?>
								  </tbody>
								</table>
								<?php } else {
									echo "Tidak ditemukan data pembayaran";
								}
							?>
						</div>
						<br />
						<div class="col-auto">
							<a href="dashboard.php?mode=<?=$mode_akses?>" class="btn btn-primary"><i class="bi bi-eye"></i> Kembali</a>
						</div>						
					</div>
				</div>
			</div>
			<div class="row pt-5 py-1 my-4 text-start">
				<div class="col-9">
					<h5 class="text-primary"><!-- BISA DIISI --></h5>
				</div>
			</div>			
		</div>
	</main>
	<?php include('webpart/navbar_bottom.php') ?>
</body>

</html>