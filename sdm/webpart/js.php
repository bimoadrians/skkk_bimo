<script src="../assets/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js" defer></script>
<script>
let startY = 0;
let isPulling = false;

window.addEventListener("touchstart", function(e) {
	if (window.scrollY === 0) {
		startY = e.touches[0].clientY;
		isPulling = true;
	}
});

window.addEventListener("touchmove", function(e) {
	if (!isPulling) return;

	const currentY = e.touches[0].clientY;
	const diff = currentY - startY;

	// Jika user swipe ke bawah lebih dari 80px
	if (diff > 80) {
		isPulling = false;
		location.reload(); // Refresh halaman
	}
});

window.addEventListener("touchend", function() {
	isPulling = false;
});
</script>
