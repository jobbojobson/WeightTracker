<?php
ob_start();
$_BODYSCRIPTS = 'js/table.js';
?>


<div class="container">
	<div class="scrollControls row mb-3">
		<i class="bi bi-chevron-bar-up"></i>
		<i class="bi bi-chevron-bar-down"></i>
		
		
		<div class="table-controls">
			
			<label for="inpFromDate">From: </label>
			<input id="inpFromDate" type="date" value="<?php echo date("Y-m-d"); ?>"/>
			
			<label for="inpToDate">To: </label>
			<input id="inpToDate" type="date" value="<?php echo date("Y-m-d"); ?>"/>
			
			<button id="btnFetch" class="btn btn-primary submit-form">Fetch</button>
		</div>
	</div>
	<div id="scrAllTable" class="table-responsive">
		<table id="tblData" class="table table-sm align-middle table-bordered table-hover">
			<thead class="sticky-header table-dark">
				<tr>
					<th class="date-col">Date</th>
					<th class="num-col-small">KG</th>
					<th class="num-col-small">7 Day Average</th>
					<th class="num-col-small">Pounds</th>
					<th class="num-col-small">Stone</th>
					<th class="col-wide">Note</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
	</div>
	<div class="save-panel row">
		<button id="btnSave" class="btn btn-primary submit-form">Save</button>
		<span id="msgSuccess" class="message success fade-text"></span>
		<span id="msgError" class="message error"></span>
	</div>
	<!--bi bi-filetype-csv-->
</div>

<div id="summary" class="div-col">
	
</div>



<?php

$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/master.php');
?>