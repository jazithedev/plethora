<?php
/**
 * Main view of Plethora installation.
 *
 * @author	Krzysztof Trzos
 * @package	install
 * @since	1.0.0-alpha
 * @version	1.0.0-alpha
 */
?>

<?php /* @var $oBody \PlethoraInstall\InstallView */ ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="description" content="">
		<meta name="keywords" content="">
		<meta charset="utf-8">
		<meta name="author" content="Krzysztof Trzos">
		<meta name="robots" content="index,follow">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Plethora setup</title>
		<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		<style type="text/css">
			/* basics */
			h1 { text-align: center; }
			h2 { text-transform: uppercase; }
			.hidden_el { width: 1px; height: 1px; padding: 0; margin: -1px; border: 0 none; clip: rect(0px, 0px, 0px, 0px); overflow: hidden; position: absolute; }
			div#install_messages.success { color: green; }
			div#install_messages.error { color: red; }

			.form_actions { text-align: center; }

			/* wizard */
			.wizard { margin: 20px auto; background: #fff; }
			.wizard .nav-tabs { position: relative; margin: 40px auto; margin-bottom: 0; border-bottom-color: #e0e0e0; }

			.wizard > div.wizard-inner { position: relative; margin-bottom: 50px; }

			.connecting-line { height: 2px; background: #e0e0e0; position: absolute; width: 80%; margin: 0 auto; left: 0; right: 0; top: 50%; z-index: 1; }

			.wizard .nav-tabs > li.active > a,
			.wizard .nav-tabs > li.active > a:hover,
			.wizard .nav-tabs > li.active > a:focus {
				color: #555555; cursor: default; border: 0; border-bottom-color: transparent;
			}

			span.round-tab { width: 70px; height: 70px; line-height: 70px; display: inline-block; border-radius: 100px; background: #fff; border: 2px solid #e0e0e0; z-index: 2; position: absolute; left: 0; text-align: center; font-size: 25px; }
			span.round-tab i { color:#555555; }

			.wizard li.active span.round-tab { background: #fff; border: 2px solid #5bc0de; }
			.wizard li.active span.round-tab i { color: #5bc0de; }

			span.round-tab:hover { color: #333; border: 2px solid #333; }

			.wizard .nav-tabs > li { width: 16.6666666666%; }
			.wizard li:after { content: " "; position: absolute; left: 46%; opacity: 0; margin: 0 auto; bottom: 0px; border: 5px solid transparent; border-bottom-color: #5bc0de; transition: 0.1s ease-in-out; }
			.wizard li.active:after { content: " "; position: absolute; left: 46%; opacity: 1; margin: 0 auto; bottom: 0px; border: 10px solid transparent; border-bottom-color: #5bc0de; }
			.wizard .nav-tabs > li a { width: 70px; height: 70px; margin: 20px auto; border-radius: 100%; padding: 0; }
			.wizard .nav-tabs > li a:hover { background: transparent; }
			.wizard .tab-pane { position: relative; }
			.wizard h3 { margin-top: 0; }

			@media( max-width : 585px ) {
				.wizard { width: 90%; height: auto !important; }
				span.round-tab { font-size: 16px; width: 50px; height: 50px; line-height: 50px; }
				.wizard .nav-tabs > li a { width: 50px; height: 50px; line-height: 50px; }
				.wizard li.active:after { content: " "; position: absolute; left: 35%; }
			}

			.form-group input[type="checkbox"] { display: none; }
			.form-group input[type="checkbox"] + .btn-group > label span { width: 20px; }
			.form-group input[type="checkbox"] + .btn-group > label span:first-child { display: none; }
			.form-group input[type="checkbox"] + .btn-group > label span:last-child { display: inline-block; }
			.form-group input[type="checkbox"]:checked + .btn-group > label span:first-child { display: inline-block; }
			.form-group input[type="checkbox"]:checked + .btn-group > label span:last-child { display: none; }

		</style>
	</head>
	<body>
		<?= isset($oBody) ? $oBody->render() : NULL ?>
	</body>
</html>