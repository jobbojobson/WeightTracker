<?php
ob_start();
$_HEADSCRIPTS = 'https://www.gstatic.com/charts/loader.js';
$_BODYSCRIPTS = 'js/chart.js';
?>

<div class="chart-controls row row-cols-md-auto align-items-center g-3 mb-2">
	
	<?php require(__DIR__.'/php/include/ui/ctrl/dateRangeFetch.html'); ?>
</div>

<div id="lineChart" ></div>

<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/ui/master.php');
?>
