<?php 
header('Content-Type:application/json; charset=UTF-8');

if( $_SERVER['REQUEST_METHOD'] == 'GET' ){

	require(__DIR__.'/../include/db.php');

	echo json_encode($dbo->getUser(), JSON_FORCE_OBJECT);
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	
	require(__DIR__.'/../include/utils.php');
	
	$errors = [];
	
	if( isset($_POST['gender']) && isset($_POST['dob']) && isset($_POST['height']) && isset($_POST['activity']) && isset($_POST['goal']) && isset($_POST['deficit'])) {
		
		$gender = trim($_POST['gender']);
		$dob = trim($_POST['dob']);
		$height = trim($_POST['height']);
		$activity = trim($_POST['activity']);
		$goal = trim($_POST['goal']);
		$deficit = trim($_POST['deficit']);
		
		if( ! in_array($gender, [ 'M', 'F' ])){
			array_push($errors, 'Gender not recognised');
		}
		
		if( ! validateDate($dob)){
			array_push($errors, 'Invalid date. Expecting YYYY-MM-DD');
		}
		
		if( ! is_numeric($height)){
			array_push($errors, 'Height is not numeric');
		}
		
		if( ! is_numeric($activity) || ! in_array($activity, [ 1.2, 1.375, 1.55, 1.725, 1.9 ] )){
			array_push($errors, 'Activity level not recognised');
		}
		
		if( ! is_numeric($deficit)){
			array_push($errors, "Deficit is not numeric");
		}
		
		if( ! is_numeric($goal)){
			array_push($errors, "Goal is not numeric");
		}
				
		if(sizeof($errors) > 0){
			echo json_encode([ 'errors' => $errors ]);
			exit();
		}
		
		require(__DIR__.'/../include/db.php');
		
		
		try {
			$dbo->setUser( [ 
				'gender' => $gender, 
				'dob' => $dob, 
				'height' => $height, 
				'activity_multiplier' => $activity, 
				'deficit' => $deficit, 
				'goal' => $goal ] );
			
			echo json_encode([ 'success' => true ]);
			
		} catch(PDOException $e){
			echo json_encode(['errors' => [ $e->getMessage() ]]);
		}
	}
}

?>