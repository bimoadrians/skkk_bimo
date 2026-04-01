	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<title><?= $titlePage ?></title>
	<link rel="apple-touch-icon" sizes="180x180" href="assets/imgs/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/imgs/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="assets/imgs/favicons/favicon-16x16.png">
	<link rel="mask-icon" href="assets/imgs/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="manifest" href="assets/imgs/favicons/site.webmanifest">
	<!-- ========== Style ========== -->
	<link href="assets/css/main.css" rel="stylesheet">
	<link href="assets/node_modules/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
	<!-- ========== Style ========== -->

	<!-- ========== JQUERY ========== -->
	<script src="assets/node_modules/jquery/dist/jquery.min.js" defer></script>
	<!-- ========== JQUERY ========== -->

	<!-- ========== FONT ========== -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
	<!-- ========== FONT ========== -->

	<!-- ========== sweetalert ========== -->
	<link rel="stylesheet" href="assets/node_modules/sweetalert2/dist/sweetalert2.min.css">
	<!-- ========== sweetalert ========== -->

	<style>
		body {
			font-family: "Roboto", sans-serif;
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
	</style>

	<script>
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