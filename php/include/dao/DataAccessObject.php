<?php 

include(__DIR__.'/WeightDAO.php'); 

/*
	Extend this class and implement WeightDAO to create a custom DAO
*/
abstract class DataAccessObject implements WeightDAO {
	
	protected $dbo;
	
	function __construct($dsn, $username, $password){
		$this->dbo = new PDO($dsn, $username, $password);
	}
	
	/* utility function to set a parameter in the given statement. If the parameter is of length zero, it is set to null */
	protected function setParamOrNull( &$stmt, $sParam, $val, $type = PDO::PARAM_STR ){
		if(strlen($val) > 0){
			return $stmt->bindValue( $sParam, $val, $type );		
		} else {
			return $stmt->bindValue( $sParam, null ,PDO::PARAM_NULL );
		}
	}
}
?>