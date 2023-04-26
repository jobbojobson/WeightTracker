<?php
ob_start();
?>

<form id="frmConfig" class="col-md-4 col-lg-3">
	
	<div class="form-group mb-2">
		<label for="selGender" class="form-label">Gender</label>
		<select name="gender" id="selGender" class="form-select">
			<option value="M">Male</option>
			<option value="F">Female</option>
		</select>
	</div>
	<div class="form-group mb-2">
		<label for="inpDob" class="form-label">Date of Birth</label>
		<input name="dob" id="inpDob" type="date" class="form-control"/>
	</div>
	<div class="form-group mb-2">
		<label for="inpHeight" class="form-label">Height (cm)</label>
		<input name="height" id="inpHeight" type="number" class="form-control"/>
	</div>
	<div class="form-group mb-2">
		<label for="selAct" class="form-label">Activity Level</label>
		<select name="activity" id="selAct" class="form-select">
			<option value="1.2">Little to no exercise</option>
			<option value="1.375">Light exercise</option>
			<option value="1.55">Moderate exercise</option>
			<option value="1.725">Heavy exercise</option>
			<option value="1.9">Very heavy exercise</option>
		</select>
	</div>
	<div class="form-group mb-2">
		<label for="inpDeficit" class="form-label">Calorie Deficit</label>
		<input name="deficit" id="inpDeficit" type="number" class="form-control"/>
	</div>
	<div class="form-group mb-2">
		<label for="inpGoal" class="form-label">Goal Weight (KG)</label>
		<input name="goal" id="inpGoal" type="number" step=".1" class="form-control"/>
	</div>	
	<div class="save-panel">
		<?php require(__DIR__.'/php/include/ui/ctrl/saveButton.html'); ?>
		<?php require(__DIR__.'/php/include/ui/ctrl/msgPanel.html'); ?>
	</div>
	
</form>

<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();
$_BODYSCRIPTS = 'js/config.js';
include('php/include/ui/master.php');
?>
