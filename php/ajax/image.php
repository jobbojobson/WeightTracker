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
			$data = (new WeightDatabase())->getImage(htmlspecialchars($date));
			header('Content-Type:'.$data['mime']);
			header('Content-Length:'.strlen($data['image']));
			echo $data['image'];
		}catch(Exception $e){
			header('Content-Type:application/json; charset=UTF-8');
			echo json_encode([ 'errors' => [ $e->getMessage() ]]);
		}
		
	}
}


function post(){
	
	header('Content-Type:application/json; charset=UTF-8');
	
	$date = null;
	$errors = [];
	
	if(isset($_POST['date'])){
		$date = trim($_POST['date']);
		
		require(__DIR__.'/../include/time/Day.php');
		
		try {
			new Day($date);
		} catch(Exception){
			echo json_encode([ 'errors' => [ 'Date is invalid' ] ]);
			exit();
		}
	} else {
		echo json_encode([ 'errors' => [ 'Date is not set' ] ]);
		exit();
	}
	
	
	if(isset($_POST['delete'])){
		
		require(__DIR__.'/../include/db/WeightDatabase.php');
		
		try {
			(new WeightDatabase())->deleteImage( $date );
			
			echo json_encode([ 'success' => true ]);
			
		} catch(Exception $e){
			echo json_encode(['errors' => [ 'Error deleting image' ]]);
		}
		
	} else if(isset($_FILES['image'])){
		
		$tmp = $_FILES['image']['tmp_name'];
		
		if(!isset($tmp) || strlen($tmp) < 1 || $_FILES['image']['error'] == 1){
			# remember that upload_max_filesize = 2M in php.ini by default
			echo json_encode([ 'errors' => [ 'Error uploading file' ] ]);
			exit();
		}
		
		$image_info = getimagesize($tmp);
		
		if( !  $image_info ){
			echo json_encode([ 'errors' => [ 'Supplied file is not an image file' ] ]);
			exit();
		}
		
		if(!in_array($image_info['mime'], ['image/jpeg', 'image/png', 'image/bmp', 'image/tiff', 'image/gif'])){
			echo json_encode([ 'errors' => [ 'Unsupported image type' ] ]);
			exit();
		}
		
		require(__DIR__.'/../include/db/WeightDatabase.php');
		
		try {
			(new WeightDatabase())->setImage( $date, file_get_contents($tmp), $image_info['mime'] );
			
			echo json_encode([ 'success' => true ]);
			
		} catch(Exception){
			echo json_encode(['errors' => [ 'Error saving image' ]]);
		}
		
	}
}


?>