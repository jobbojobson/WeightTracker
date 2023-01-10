<?php
ob_start();
$_HEADSCRIPTS = 'https://www.gstatic.com/charts/loader.js';
$_BODYSCRIPTS = 'js/chart.js';
?>

<div class="chart-controls">
	<label for="inpFromDate">From: </label>
	<input id="inpFromDate" type="date" value="<?php echo date("Y-m-d"); ?>"/>

	<label for="inpToDate">To: </label>
	<input id="inpToDate" type="date" value="<?php echo date("Y-m-d"); ?>"/>
	
	<button id="btnFetch" class="btn submit-form">Fetch</button>
</div>

<div id="lineChart" ></div>

<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/master.php');
?>
