<?php 
header('Content-Type:application/json; charset=UTF-8');


if( $_SERVER['REQUEST_METHOD'] == 'GET' ){

	require(__DIR__.'/../include/db/WeightDatabase.php');
	
	$data = (new WeightDatabase())->getUser();
	#var_dump($data);
	array_walk_recursive($data, function(&$value, $key){
		$value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
	});
	
	echo json_encode( $data, JSON_FORCE_OBJECT);
}

if( $_SERVER['REQUEST_METHOD'] == 'POST' ){
	
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
		
		require(__DIR__.'/../include/time/Day.php');
		
		try {
			new Day($dob);
		} catch(Exception $e) {
			array_push($errors, 'Invalid date of birth');
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
		
		require(__DIR__.'/../include/db/WeightDatabase.php');
		
		
		try {
			(new WeightDatabase())->setUser( [ 
				'gender' => $gender, 
				'dob' => $dob, 
				'height' => $height, 
				'activity_multiplier' => $activity, 
				'deficit' => $deficit, 
				'goal' => $goal ] );
			
			echo json_encode([ 'success' => true ]);
			
		} catch(PDOException){
			echo json_encode(['errors' => [ 'Error saving config' ]]);
		}
	}
}

?>