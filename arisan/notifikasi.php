<?php
session_start();

include '../constant.php';
include '../config.php';
include '../core.php';
//include '../arisanconnection.php';

$mode = $_REQUEST["mode"];
if ($mode != $mode_akses) {
	header("Location: " . $skkksurakarta);
	die();
}
$userId				= strtolower($_REQUEST['user_id']);
$tableNotif		= 'apps';
$titleHeader	= 'Notifikasi';
$perintah			= 'view';
$limit				= 1;

$qCount		= "SELECT COUNT(id) as numid FROM $tableNotif.notifikasi WHERE user = '$userId' AND folder = '/arisan'";
$dataNum	= JalankanSQL($perintah, $qCount, $limit);
$numNotif	= $dataNum[0]->numid;

$q				= "SELECT * FROM $tableNotif.notifikasi WHERE user = '$userId' AND folder = '/arisan' ORDER BY id DESC";
$runNotif	= JalankanSQL($perintah, $q, 999);

if ($_REQUEST['op'] == 'mark') {
	$idNotif		= $_REQUEST['idnotif'];
	$qNotif			= "UPDATE $tableNotif.notifikasi SET isread = 1 WHERE id = $idNotif";
	$execNotif	= JalankanSQL('update', $qNotif, 1);
	if ($execNotif == TRUE) {
		header("location: notifikasi.php?mode=$mode_akses&user_id=$userId");
	}
}
?>

<!DOCTYPE html>
<html lang="en">

	<head>
		<?php include('webpart/head.php') ?>
	</head>

	<body>
		<?php include('webpart/sidebar.php') ?>
		<div class="container-fluid">
			<main>
				<div class="row py-1 my-4 text-start">
					<div class="col-12">
						<ul class="list-group list-group-flush">
							<?php if ($numNotif == 0): ?>
							<li class="list-group-item text-white text-center py-4 bg-primary rounded-3 shadow-sm">
								<i class="bi bi-bell-slash fs-3 mb-2 d-block"></i>
								Belum ada notifikasi
							</li>
							<?php else: ?>
							<?php foreach ($runNotif as $n):
								$textNotif = preg_replace('/\*([^\*]+)\*/', '<strong>$1</strong>', $n->berita);
								$textNotif = preg_replace('/~r/', '<br>', $textNotif);
								$isUnread = ($n->isread == 0);
							?>
							<li class="list-group-item p-0 my-3 border-0">
								<div class="d-flex shadow-sm rounded-3 overflow-hidden position-relative"
									style="background-color:rgb(216, 236, 255);">

									<!-- Strip indikator di kiri -->
									<?php if ($isUnread): ?>
									<div style="width: 6px; background-color: #013773;"></div>
									<?php else: ?>
									<div style="width: 6px; background-color: #dee2e6;"></div>
									<?php endif; ?>

									<div class="p-3 flex-grow-1">
										<div class="d-flex justify-content-between align-items-start flex-column flex-md-row gap-2">
											<div class="flex-grow-1">
												<div class="fw-semibold mb-1">
													<i class="bi bi-bell-fill me-2 text-primary"></i><?= $n->judul ?>
													<?php if ($isUnread): ?>
													<span class="ms-1 badge bg-primary-subtle text-primary rounded-pill small">Baru</span>
													<?php endif; ?>
												</div>
												<div class="text-muted small mb-1"><?= date('d M Y H:i', strtotime($n->ts)) ?></div>
												<div><?= $textNotif ?></div>
											</div>
											<?php if ($isUnread): ?>
											<button class="btn btn-sm btn-primary mt-2 mt-md-0" onclick="isRead('<?= $n->id ?>', '<?= $userId ?>')">
												<i class="bi bi-check2-circle me-1"></i> Tandai sudah dibaca
											</button>
											<?php endif; ?>
										</div>
									</div>
								</div>
							</li>
							<?php endforeach; ?>
							<?php endif; ?>
						</ul>
					</div>
				</div>
			</main>
		</div>
	</body>
	<script>
	function isRead(id, userid) {
		var mode_akses = 'aksesmobileapps';
		document.location.href = 'notifikasi.php?mode=' + mode_akses + '&op=mark&user_id=' + userid + '&idnotif=' + id;
	}
	</script>
	<?php include('webpart/js.php') ?>

</html>
