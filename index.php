<?php 
ob_start();
$_BODYSCRIPTS = 'js/daily.js';
?>

<div class="frm-narrow">
	<h2 class="ctrl-daily big-label" data-date="<?php echo date("Y-m-d"); ?>" id="spnDate"></h2>

	<div class="ctrl-daily">
		<input id="inpDayValue" class="big-input" type="number" step=".1" />
	</div>
	<div class="ctrl-daily save-panel">
		<button id="btnSave" class="btn submit-form btn-big">Save</button></br>
		<span id="msgSuccess" class="message success fade-text"></span>
		<span id="msgError" class="message error"></span>
	</div>
</div>

<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/master.php');

?>