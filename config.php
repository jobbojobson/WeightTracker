<?php
ob_start();
?>

<form id="frmConfig">
	<fieldset>
		<div class="form-control">
			<label for="selGender">Gender</label><br/>
			<select name="gender" id="selGender">
				<option value="M">Male</option>
				<option value="F">Female</option>
			</select>
		</div>
		<div class="form-control">
			<label for="inpDob">Date of Birth</label><br/>
			<input name="dob" id="inpDob" type="date" />
		</div>
		<div class="form-control">
			<label for="inpHeight">Height (cm)</label><br/>
			<input name="height" id="inpHeight" type="number" />
		</div>
		<div class="form-control">
			<label for="selAct">Activity Level</label><br/>
			<select name="activity" id="selAct">
				<option value="1.2">Little to no exercise</option>
				<option value="1.375">Light exercise</option>
				<option value="1.55">Moderate exercise</option>
				<option value="1.725">Heavy exercise</option>
				<option value="1.9">Very heavy exercise</option>
			</select>
		</div>
		<div class="form-control">
			<label for="inpDeficit">Calorie Deficit</label><br/>
			<input name="deficit" id="inpDeficit" type="number" />
		</div>
		<div class="form-control">
			<label for="inpGoal">Goal Weight (KG)</label><br/>
			<input name="goal" id="inpGoal" type="number" step=".1" />
		</div>	
		<div class="save-panel">
			<button id="btnSave" class="btn submit-form">Save</button>
			<span id="msgSuccess" class="message success fade-text"></span>
			<span id="msgError" class="message error"></span>
		</div>
	</fieldset>
</form>

<?php
$_PAGECONTENT = ob_get_contents();
ob_end_clean();
$_BODYSCRIPTS = 'js/config.js';
include('php/include/master.php');
?>
