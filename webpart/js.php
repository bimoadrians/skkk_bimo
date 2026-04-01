<script src="../assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
<script>
	function showAlert(judul, isi, icon) {
		swal.fire({
			title: judul,
			text: isi,
			icon: icon,
			buttons: ["OK", "Cancel"],
		});
	}
	
	function showAlertAutoClose (judul, isi) {
		let Welcome
		Swal.fire({
			title: judul,
			text: isi,
			html: 'Notifikasi ditutup dalam <b></b> detik',
			timer: 3000,
			timerProgressBar: true,
			didOpen: () => {
				Swal.showLoading();
				const timer = Swal.getPopup().querySelector("b");
				Welcome = setInterval(() => {
					timer.textContent = (Swal.getTimerLeft() / 1000).toFixed(0)
				}, 100);
			},
			willClose: () => {
				clearInterval(Welcome);
			}
		}).then((result) => {
			if (result.dismiss === Swal.DismissReason.timer) {
				console.log("I was closed by the timer");
			}
		});	
	}
</script>