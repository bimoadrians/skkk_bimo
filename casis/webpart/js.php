<script src="../assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(() => {
	$('#copy-button').click(() => {
		const nomorVA = $('#nomor-va').text();
		navigator.clipboard.writeText(nomorVA);
		const pesanKonfirmasi = $('<p class="pesan-konfirmasi text-light">Nomor VA telah disalin!</p>');
		pesanKonfirmasi.hide();
		$('#copy-button').after(pesanKonfirmasi);
		pesanKonfirmasi.slideDown();
		setTimeout(() => {
			pesanKonfirmasi.slideUp(() => {
				pesanKonfirmasi.remove();
			});
		}, 2000);
	});
});
</script>
