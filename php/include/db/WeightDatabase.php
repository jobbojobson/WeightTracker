<?php
/*
	Have this class extend a DAO object. Those DAO objects should implement WeightDAO
	Write your own DAO to connect to your chosen backend
*/
include(__DIR__.'/../dao/MariaDBWeightDAO.php'); 

class WeightDatabase extends MariaDBWeightDAO {
	
	function __construct(){
		parent::__construct('mysql:host=localhost;dbname=WeightTracker', 'WeightTracker', 'WeightTracker');
		#parent::__construct('sqlite:'.__DIR__.'/../../../db/weight.db');
	}
}
?>