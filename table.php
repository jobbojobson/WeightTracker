<?php
ob_start();
$_BODYSCRIPTS = 'js/table.js';
?>


<div class="div-col">
	<div class="scrollControls">
		<i class="fa-solid fa-chevron-up fa-xl"></i>
		<i class="fa-solid fa-chevron-down fa-xl"></i>
		
		
		<div class="table-controls">
			
			<label for="inpFromDate">From: </label>
			<input id="inpFromDate" type="date" value="<?php echo date("Y-m-d"); ?>"/>
			
			<label for="inpToDate">To: </label>
			<input id="inpToDate" type="date" value="<?php echo date("Y-m-d"); ?>"/>
			
			<button id="btnFetch" class="btn submit-form">Fetch</button>
		</div>
	</div>
	<div id="scrAllTable">
		<table id="tblData" class="dataTable">
			<thead class="sticky-header">
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
	<div class="save-panel">
		<button id="btnSave" class="btn submit-form">Save</button>
		<span id="msgSuccess" class="message success fade-text"></span>
		<span id="msgError" class="message error"></span>
	</div>
</div>

<div id="summary" class="div-col">
	
</div>



<?php

$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/master.php');
?>