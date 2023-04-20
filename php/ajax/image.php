<?php

switch($_SERVER['REQUEST_METHOD']){
	case 'GET': 
		get();
		break;
	case 'POST':
		post();
		break;
}


function get(){
	
	# get the row from the database
	# look at the mime type of the row
	# set the appropriate header
	# send back the image data
	
	if(isset($_GET['date'])){
		
		
		$date = trim($_GET['date']);
		
		require(__DIR__.'/../include/time/Day.php');
		
		try{
			new Day($date);
		}catch(Exception $e){
			exit();
		}
		
		require(__DIR__.'/../include/db/WeightDatabase.php');
		
		try {
			$data = (new WeightDatabase())->getImage($date);
			header('Content-Type:'.$data['mime']);
			header('Content-Length:'.strlen($data['image']));
			echo $data['image']; #
		}catch(Exception $e){
			echo json_encode([ 'errors' => [ $e->getMessage() ]]);
		}
		
	}
}


function post(){
	
	header('Content-Type:application/json; charset=UTF-8');
	
	if(isset($_FILES['image']) && isset($_POST['date'])){
		
		$errors = [];
		
		$date = trim($_POST['date']);
		$tmp = $_FILES['image']['tmp_name'];
		$image_info = getimagesize($tmp);
		
		if( !  $image_info ){
			array_push($errors, 'Supplied file is not an image file');
		}
		
		require(__DIR__.'/../include/time/Day.php');
		
		try {
			new Day($date);
		} catch(Exception $e){
			array_push($errors, 'Date is invalid');
		}
		
		if(sizeof($errors) > 0){
			echo json_encode([ 'errors' => $errors ]);
			exit();
		}
		
		require(__DIR__.'/../include/db/WeightDatabase.php');
		
		try {
			
			(new WeightDatabase())->setImage( $date, file_get_contents($tmp), $image_info['mime'] );
			echo json_encode([ 'success' => true ]);
			
		} catch(Exception $e){
			echo json_encode(['errors' => [ $e->getMessage() ]]);
		}
		
	}
	
}



?>