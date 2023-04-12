<?php
ob_start();
?>


<div class="row">
	<form id="frmImage" class="col-md-4 col-lg-3">
		<div class="form-group mb-2">
			<label for="inpDate" class="form-label">Date of photo</label>
			<input name="date" id="inpDate" type="date" class="form-control" value="<?php echo date("Y-m-d"); ?>"/>
		</div>
		<div class="form-group mb-2">
			<label for="inpFile" class="form-label">Photo</label>
			<input name="image" id="inpFile" type="file" class="form-control"/>
		</div>
		<div class="save-panel">
			<button id="btnSave" class="btn btn-primary">Save</button>
			<?php require(__DIR__.'/php/include/ui/ctrl/msgPanel.html'); ?>
		</div>
	</form>



	<div class="col-md-8 col-lg-9">
		
		<div id="gallery"></div>
		
	</div>
</div>


<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();
$_BODYSCRIPTS='js/image.js';
include('php/include/ui/master.php');

?>