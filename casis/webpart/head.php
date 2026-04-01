	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<title><?= $titlePage ?></title>
	<link rel="apple-touch-icon" sizes="180x180" href="../assets/imgs/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="../assets/imgs/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="../assets/imgs/favicons/favicon-16x16.png">
	<link rel="mask-icon" href="../assets/imgs/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="manifest" href="../assets/imgs/favicons/site.webmanifest">
	<!-- ========== Style ========== -->
	<link href="../assets/css/main.css" rel="stylesheet">
	<link href="../assets/node_modules/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
	<!-- ========== Style ========== -->

	<!-- ========== JQUERY ========== -->
	<script src="../assets/node_modules/jquery/dist/jquery.min.js" defer></script>
	<!-- ========== JQUERY ========== -->

	<!-- ========== FONT ========== -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
	<!-- ========== FONT ========== -->

	<!-- ========== sweetalert ========== -->
	<script src="../assets/node_modules/sweetalert2/dist/sweetalert2.all.min.js" defer></script>
	<link rel="stylesheet" href="../assets/node_modules/sweetalert2/dist/sweetalert2.min.css">
	<!-- ========== sweetalert ========== -->

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />


	<style>
body {
	font-family: "Inter", sans-serif;
	font-optical-sizing: auto;
	font-weight: 400;
	font-style: normal;
	background-color: rgba(255, 255, 255, 1);
	padding-top: 56px;
}

.img-object-fit {
	width: 120px;
	height: 180px;
	object-fit: cover;
}

.icon-berlingkar {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	border-radius: 50%;
	width: 40px;
	height: 40px;
	font-size: 20px;
	background-color: rgb(28, 33, 80);
	color: #333;
}

.navbar-brand {
	font-weight: bold;
}

.navbar-toggler {
	color: #FFFFFF;
	border: none;
	font-size: 30px;
}

.navbar-nav {
	margin-right: 0;
}

.nav-link {
	color: #333;
	font-size: 16px;
}

.nav-link:hover {
	color: #000;
}

.bi-search {
	font-size: 20px;
}

.search-input {
	display: none;
	position: absolute;
	top: 50%;
	left: 50%;
	transform: translate(-50%, -50%);
	width: 200px;
	padding: 10px;
	border: 1px solid #ddd;
	border-radius: 4px;
	box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

.search-input.active {
	display: block;
}

.navbar {
	background-color: #013773;
	height: 56px;
	padding: 0 1rem;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.navbar .navbar-brand {
	color: #ffffff;
	font-size: 1.5rem;
	display: flex;
	align-items: center;
	height: 100%;
	margin: 0;
}

.navbar-toggler {
	border: none;
	padding: 0.25rem;
}

.navbar-toggler-icon {
	width: 1.6rem;
	height: 1.6rem;
	filter: invert(1);
	/* Putih */
}

.offcanvas-end {
	background-color: #021f3f;
	color: #ffffff;
	width: 250px;
}

.offcanvas-title {
	color: #ffffff;
}

.btn-close.btn-close-white {
	filter: invert(1);
}

.offcanvas-body .nav-link {
	color: #ffffff;
	font-size: 1rem;
	padding: 0.5rem 0;
}

.nav-link.text-danger {
	color: #DA304A !important;
}

.card-custom {
	border-radius: 12px;
	padding: 20px;
	transition: 0.3s;
	box-shadow: 0 6px 8px rgba(0, 0, 0, 0.05);
}

.card-primary {
	background-color: #bfd2e6;
	border: 1px solid rgba(1, 55, 115, 0.15);
	color: #013773;
}

.card-warning {
	background-color: #FFE9A1;
	border: 1px solid rgba(255, 202, 58, 0.3);
	color: #7a5a00;
}

.card-danger {
	background-color: #f2c5c6;
	border: 1px solid rgba(183, 21, 26, 0.2);
	color: #7a1012;
}

.card-info {
	background-color: #c7d6e1;
	border: 1px solid rgba(70, 117, 153, 0.25);
	color: #2e4a5c;
}

.card-success {
	background-color: #b7dcd7;
	border: 1px solid rgba(19, 111, 99, 0.25);
	color: #0f554c;
}

.card-light {
	background-color: #faf0ca;
	border: 1px solid rgba(250, 240, 202, 0.3);
	color: #5c5c5c;
}

.card-dark {
	background-color: #1e293b;
	border: 1px solid rgba(8, 32, 50, 0.5);
	color: #ffffff;
}

.card-secondary {
	background-color: #d4d9e0;
	border: 1px solid rgba(155, 164, 181, 0.2);
	color: #4a4f57;
}

.icon {
	font-size: 30px;
	float: right;
}

.progress {
	height: 5px;
	border-radius: 5px;
}

.container {
	margin-bottom: 80px;
}

.grid {
	display: grid;
	grid-template-columns: repeat(12, 1fr);
	grid-template-rows: auto;
}

.header-image {
	grid-area: 1 / 1 / span 12 / 2;
	background-size: cover;
}

.header-content {
	grid-area: 2 / 1 / span 12 / 3;
	background-color: rgba(0, 0, 0, 0.5);
	color: white;
	padding: 20px;
}

@media (min-width: 1024px) and (max-width: 1366px) {
	.card {
		background-size: 100% auto;
		margin-top: 50px;
	}
}

.card {
	animation: fadeInUp 0.8s ease;
}

@keyframes fadeInUp {
	from {
		transform: translateY(20px);
		opacity: 0;
	}

	to {
		transform: translateY(0);
		opacity: 1;
	}
}

.wave-footer {
	position: relative;
	margin-top: -20px;
}

.wave-footer svg {
	display: block;
	width: 100%;
	height: 50px;
}

	</style>

	<script>
const searchIcon = document.querySelector('.bi-search');
const searchInput = document.querySelector('.search-input');

searchIcon.addEventListener('click', () => {
	searchInput.classList.toggle('active');
});

$(document).ready(function() {
	setTimeout(function() {
		$(".alert").alert('close');
	}, 3000);
});

document.addEventListener('touchmove', function(event) {
	event.preventDefault();
});

document.addEventListener('touchstart', function(event) {
	startY = event.touches[0].screenY;
});

document.addEventListener('touchmove', function(event) {
	if (event.touches[0].screenY < startY) {
		// Swipe up
		location.reload();
	}
});
	</script>
