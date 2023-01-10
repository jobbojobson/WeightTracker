<?php 
header('Content-Type:application/json; charset=UTF-8');

require(__DIR__.'/../include/utils.php');

$fromDate = null;
$toDate = null;

if(isset($_GET['fromDate']) && !empty($_GET['fromDate'])) {
	
	$fromDate = trim($_GET['fromDate']);
	if( ! validateDate($fromDate)){
		exit();
	}
}

if(isset($_GET['toDate']) && !empty($_GET['toDate'])) {
	
	$toDate = trim($_GET['toDate']);
	if( ! validateDate($toDate)){
		exit();
	}
}

require(__DIR__.'/../include/db.php');

try {
	
	echo json_encode([ 'success' => true, 'data' => $dbo->getGoogleChartData( $fromDate, $toDate ) ]);
	
} catch(PDOException $e){
	echo json_encode([ 'errors' => [ $e->getMessage() ] ]);
}

?>