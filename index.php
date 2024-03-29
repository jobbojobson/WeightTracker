<?php 
ob_start();
$_BODYSCRIPTS = 'js/daily.js';
?>

<h2 class="display-2" data-date="<?php echo date("Y-m-d"); ?>" id="spnDate"></h2>

<div class="row">

	<div class="col-md-4 col-lg-3">

		<div class="form-group mb-3">
			<label for="inpDayValue" class="form-label">Weight</label>
			<input id="inpDayValue" class="form-control" type="number" step=".1" placeholder="Kilograms"/>
		</div>
		<div class="form-group mb-3">
			<label for="inpDayNote" class="form-label">Note</label>
			<input id="inpDayNote" class="form-control" type="text" />
		</div>

		<?php require(__DIR__.'/php/include/ui/ctrl/saveButton.html'); ?>
		<?php require(__DIR__.'/php/include/ui/ctrl/msgPanel.html'); ?>

	</div>

	<div id="summary" class="col-md-8 col-lg-9 mt-2">
		
	</div>
</div>

<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/ui/master.php');

?>
