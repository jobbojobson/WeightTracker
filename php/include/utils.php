<?php

/*
  Returns true if the given date string is YYYY-MM-DD. False otherwise.
*/
function validateDate($date) {
	if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts) == true) {
		$time = gmmktime(0, 0, 0, $parts[2], $parts[3], $parts[1]);

		$input_time = strtotime($date);
		if ($input_time === false) return false;

		return $input_time == $time;
	} else {
		return false;
	}
}

/*
  Returns true if midnight on fromDate is before midnight on toDate.
  All dates are represented as YYYY-MM-DD strings
*/
function validatePeriod( $fromDate, $toDate ){

	$dFrom = DateTime::createFromFormat( 'Y-m-d', $fromDate );
	$dTo = DateTime::createFromFormat( 'Y-m-d', $toDate );

	if ( $dFrom == false || $dTo == false){
		return false;
	} else {
		$dFrom->setTime(0,0,0,0);
		$dTo->setTime(0,0,0,0);
		
		return ( $dFrom->getTimestamp() <= $dTo->getTimestamp() );
	}
}

/*
  
*/
function sanitizeOutput( &$data ){
	
	array_walk_recursive($data, function(&$value, $key){
		$value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5);
	});
	
	#foreach($data as &$row){
		
		
		
	#}
	
}

?>