<?php 
header('Content-Type:application/json; charset=UTF-8');

if( $_SERVER['REQUEST_METHOD'] == 'POST' ) exit();

$fromDate = null;
$toDate = null;

require(__DIR__.'/../include/time/Day.php');

if(isset($_GET['fromDate']) && !empty($_GET['fromDate'])) {
	
	try {
		$fromDate = new Day(trim($_GET['fromDate']));
	} catch(Exception $e) {
		exit();
	}
}

if(isset($_GET['toDate']) && !empty($_GET['toDate'])) {
	
	try {
		$toDate = new Day(trim($_GET['toDate']));
	} catch(Exception $e) {
		exit();
	}
}

require(__DIR__.'/../include/db/WeightDatabase.php');

try {
	
	echo json_encode([ 'success' => true, 'data' => (new WeightDatabase())->getGoogleChartData( $fromDate->__toString(), $toDate->__toString() ) ]);
	
} catch(PDOException $e){
	echo json_encode([ 'errors' => [ $e->getMessage() ] ]);
}

?>