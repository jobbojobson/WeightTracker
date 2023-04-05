<?php
header('Content-Type:application/json; charset=UTF-8');

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
		
		require(__DIR__.'/../include/time/Day.php');
		require(__DIR__.'/../include/time/Period.php');
		
		$fromDate = trim($_GET['fromDate']);
		$toDate = trim($_GET['toDate']);
		
		try {
			$fromDate = new Day($fromDate);
			$toDate = new Day($toDate);
			$period = new Period($fromDate, $toDate);
		} catch(Exception $e) {
			array_push($errors, $e->getMessage());
		}
		
		if(sizeof($errors) > 0){
			echo json_encode([ 'errors' => $errors ]);
			exit();
		}
		
		require(__DIR__.'/../include/db/WeightDatabase.php');
		
		try {
			
			$data = (new WeightDatabase())->getData( $fromDate->__toString(), $toDate->__toString() );
			
			if(isset($_GET['export']) && sizeof($data) > 0){
				createCSV( $data );
			} else {
				#var_dump($data);
				
				array_walk_recursive($data, function(&$value, $key){
					$value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
				});
				
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
	
	# test that the posted data is actually a weight array from our front end
	
	require(__DIR__.'/../include/db/WeightDatabase.php');
	
	try {
		
		(new WeightDatabase())->setData($data);
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