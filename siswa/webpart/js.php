<script src="../assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script src="https://unpkg.com/pulltorefreshjs@0.1.22/dist/index.js"></script>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		if (typeof PullToRefresh !== "undefined") {
			PullToRefresh.init({
				mainElement: 'body',
				onRefresh() {
					location.reload();
				}
			});
		} else {
			console.warn("PullToRefresh gagal dimuat, fallback manual aktif.");
			let startY = 0;
			let triggered = false;
			document.addEventListener('touchstart', e => {
				startY = e.touches[0].clientY;
				triggered = false;
			}, {
				passive: true
			});
			document.addEventListener('touchmove', e => {
				if (window.scrollY === 0 && !triggered) {
					const distance = e.touches[0].clientY - startY;
					if (distance > 100) {
						triggered = true;
						location.reload();
					}
				}
			}, {
				passive: true
			});
		}
	});


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