<?php
header("Set-Cookie: SameSite=None; Secure");
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="ie=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		
		<title>Weight Tracker</title>
		
		<link rel="stylesheet" href="css/main.css" />
		<link rel="icon" href="img/weight-icon-original.svg" type="image/svg+xml"/>
		
		<script src="https://kit.fontawesome.com/a466a965c8.js" crossorigin="anonymous"></script>
		<script src="js/master.js"></script>
		
		<?php
			if(isset($_HEADSCRIPTS)){
				if(is_array($_HEADSCRIPTS)){
					foreach($_HEADSCRIPTS as $script){
						?><script src="<?php echo $script ?>"></script><?php
					}
				} else {
					?><script src="<?php echo $_HEADSCRIPTS ?>"></script><?php
				}
			}
		?>
	</head>
	<body>
	<header>
		<h1>Weight Tracker</h1>
		<nav>
			<a href="index.php"><i class="fa-solid fa-table fa-2xl"></i></a>
			<a href="chart.php"><i class="fa-solid fa-chart-line fa-2xl"></i></a>
			<a href="config.php"><i class="fa-solid fa-gear fa-2xl"></i></a>
		</nav>
	</header>
	
	<?php echo $_PAGECONTENT; ?>
	
	<footer>
	</footer>
	
	<?php
		if(isset($_BODYSCRIPTS)){
			if(is_array($_BODYSCRIPTS)){
				foreach($_BODYSCRIPTS as $script){
					?><script src="<?php echo $script ?>"></script><?php
				}
			} else {
				?><script src="<?php echo $_BODYSCRIPTS ?>"></script><?php
			}
		}
	?>
  </body>
</html>