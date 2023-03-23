<?php 
ob_start();
$_BODYSCRIPTS = 'js/daily.js';
?>

<div>
	
	<h2 class="display-2" data-date="<?php echo date("Y-m-d"); ?>" id="spnDate"></h2>
	
	<div class="col-md-4 col-lg-2">
		<div class="form-group mb-3">
			<input id="inpDayValue" class="form-control" type="number" step=".1" placeholder="Kilograms"/>
		</div>
	</div>

	<button id="btnSave" type="button" class="btn btn-primary">Save</button>
	<?php require(__DIR__.'/php/include/ui/ctrl/msgPanel.html'); ?>

</div>

<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/ui/master.php');

?>