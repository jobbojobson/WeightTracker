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
		
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
		<link rel="stylesheet" href="css/main-bs.css" />
		<link rel="icon" href="img/weight-icon-original.svg" type="image/svg+xml"/>
		
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
	<body class="container">
	
		<header>
			<nav class="navbar navbar-expand-md">
				<div class="container-fluid">
					<label class="navbar-brand" >Weight Tracker</label>
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>
					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav me-auto mb-2 mb-lg-0">
							<li class="nav-item">
								<a class="nav-link" href="index.php"><i class="bi bi-calendar-plus"></i> Home</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="table.php"><i class="bi bi-table"></i> Data</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="chart.php"><i class="bi bi-graph-down"></i> Chart</a>
							</li>
							</li class="nav-item">
								<a class="nav-link" href="config.php"><i class="bi bi-gear-fill"></i> Configuration</a>
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</header>
		
		<section class="container">
			<?php echo $_PAGECONTENT; ?>
		</section>
		
		<footer>
		</footer>
		
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
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