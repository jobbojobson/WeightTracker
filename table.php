<?php
ob_start();
$_BODYSCRIPTS = 'js/table.js';
?>
	
<div class="table-controls row align-items-center g-3 mb-2">
	<div class="col-md-auto col-12">
		<span id="scrollControls" class="mr-2">
			<i class="bi bi-chevron-bar-up"></i>
			<i class="bi bi-chevron-bar-down"></i>
		</span>
	</div>
	
	<?php require(__DIR__.'/php/include/ui/ctrl/dateRangeFetch.php'); ?>
</div>

<div id="scrAllTable" class="table-responsive">
	<table id="tblData" class="table table-sm align-middle table-bordered mb-0">
		<thead class="sticky-header table-dark">
			<tr>
				<th scope="col" class="date-col">Date</th>
				<th scope="col" class="num-col-small">KG</th>
				<th scope="col" class="num-col-small">7 Day Average</th>
				<th scope="col" class="num-col-small">Pounds</th>
				<th scope="col" class="num-col-small">Stone</th>
				<th scope="col" class="text-col-wide">Note</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div class="save-panel form-row mt-2 mb-2">
	<button id="btnSave" class="btn btn-primary submit-form">Save</button>
	<?php require(__DIR__.'/php/include/ui/ctrl/msgPanel.php'); ?>
</div>

<div id="summary">
	
</div>

<?php

$_PAGECONTENT = ob_get_contents();
ob_end_clean();

include('php/include/ui/master.php');
?>