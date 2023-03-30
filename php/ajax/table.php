<?php
header('Content-Type:application/json; charset=UTF-8');

require(__DIR__.'/../include/utils.php');


switch($_SERVER['REQUEST_METHOD']){
	case 'GET': 
		get();
		break;
	case 'POST':
		post();
		break;
}



function get(){
	
	
	if(isset($_GET['fromDate']) && !empty($_GET['fromDate']) && isset($_GET['toDate']) && !empty($_GET['toDate'])){
		
		$errors = [];
		
		$fromDate = trim($_GET['fromDate']);
		$toDate = trim($_GET['toDate']);
		
		if( ! validateDate($fromDate) ){
			array_push($errors, 'Invalid From Date');
		}
		
		if( ! validateDate($toDate) ){
			array_push($errors, 'Invalid To Date');
		}
		
		#test that from date is before to date
		if( ! validatePeriod($fromDate, $toDate) ){
			array_push($errors, 'From date must be before To date');
		}

		if(sizeof($errors) > 0){
			echo json_encode([ 'errors' => $errors ]);
			exit();
		}
		
		require(__DIR__.'/../include/db.php');
		
		try {
			
			$data = $dbo->getData( $fromDate, $toDate );
			
			if(isset($_GET['export']) && sizeof($data) > 0){
				createCSV( $data );
			} else {
				#var_dump($data);
				sanitizeOutput( $data );
				echo json_encode([ 'success' => true, 'data' => $data ]);
			}
		} catch(PDOException $e) {
			echo json_encode(['errors' => [ $e->getMessage() ]]);
		}
		
	}
	
}


function post(){
	
	$posted = file_get_contents('php://input');
	$data = json_decode( trim( $posted ) );
	
	if(is_null($data) || !is_array($data)){
		echo json_encode([ 'errors' => [ 'Invalid input' ]]);
		exit();
	}
	
	require(__DIR__.'/../include/db.php');
	
	try {
		
		$dbo->setData($data);
		echo json_encode([ 'success' => true ]);
		
	} catch(PDOException $e) {
		echo json_encode([ 'errors' => [ $e->getMessage() ] ]);
		
	}
	
}


function createCSV( $data ){
	
	ob_end_clean();
	$headers = array_keys($data[0]);
	$output = fopen( 'php://memory', 'w' );
	fputcsv($output, $headers);
	
	foreach($data as $row){
		fputcsv($output, array_values($row), ',');
	}
	
	fseek($output, 0);
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=weight_export.csv');
	fpassthru($output);
}

?>