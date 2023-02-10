<?php
/*
	Example data access object for MariaDB
*/
class MariaDBWeightDAO implements WeightDAO {
	
	private $dbo;
	
	function __construct(){
		$this->dbo = new PDO('mysql:host=localhost;dbname=WeightTracker', 'WeightTracker', 'WeightTracker');
		$this->dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->dbo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		/* 
			Decimals come back as strings. This is supposed to fix it but it doesn't.
			It's something to do with pdo_mysql vs pdo_mysqlnd that I don't understand.
			This class will just cast such strings to appropriate types 
		*/
		$this->dbo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	}
	
	public function setData($rows){
		
		try {
			$this->dbo->beginTransaction();
			
			foreach($rows as &$row){
				$stmt = $this->dbo->prepare("SELECT DATE FROM t_weight WHERE DATE = ?");
				$stmt->execute([$row->date]);
				$existingRow = $stmt->fetch();
				$stmt->closeCursor();
				
				if(is_array($existingRow) && (sizeof($existingRow) > 0)){
					
					# Rows that exist already but have been POSTED again with blank kilos and notes are deleted
					if(strlen($row->kilograms) == 0 && strlen($row->note) == 0){
						$stmt = $this->dbo->prepare(
							"DELETE FROM t_weight WHERE DATE = :date");
						
						$this->setParamOrNull( $stmt, ':date', $row->date );
					} else {
						
						$stmt = $this->dbo->prepare(
							"UPDATE t_weight SET KILOGRAMS = :kilograms, NOTE = :note WHERE DATE = :date");
						
						$this->setParamOrNull( $stmt, ':kilograms', $row->kilograms );
						$this->setParamOrNull( $stmt, ':note', $row->note );
						$this->setParamOrNull( $stmt, ':date', $row->date );
					}
					
				} else {
					
					$stmt = $this->dbo->prepare(
						"INSERT INTO t_weight (KILOGRAMS, NOTE, DATE) VALUES(:kilograms, :note, :date)");
					
					$this->setParamOrNull( $stmt, ':kilograms', $row->kilograms );
					$this->setParamOrNull( $stmt, ':note', $row->note );
					$this->setParamOrNull( $stmt, ':date', $row->date );
					
				}
				
				$stmt->execute();
			}
			
			$this->dbo->commit();
			return true;
			
		} catch(PDOException $e){
			$this->dbo->rollback();
			throw $e;
		}
	}
	
	public function getData($fromDate, $toDate){
		
		$stmt = $this->dbo->prepare(
			"select
				date,
				kilograms,
				last_week_average,
				pounds,
				stone,
				note
			from v_weight
			where date between ? and ? order by date asc");
		
		$stmt->execute( [ $fromDate, $toDate ] );
		$output = [];
		while($row = $stmt->fetch()){
			array_push($output, [ 
				'date' => $row['date'], 
				'kilograms' => (float)$row['kilograms'],
				'last_week_average' => (float)$row['last_week_average'],
				'pounds' => (float)$row['pounds'],
				'stone' => $row['stone'],
				'note' => $row['note']
			]);
		}
		$stmt->closeCursor();
		
		return $output;
	}
	
	public function getMaxWeight(){
		$stmt = $this->dbo->prepare('select max(kilograms) as kilograms from v_weight');
		$stmt->execute();
		$maxWeight = (float)$stmt->fetch()['kilograms'];
		$stmt->closeCursor();
		return $maxWeight;
	}
	
	public function getCurrentWeight(){
		$stmt = $this->dbo->prepare('select last_week_average from v_weight where date = (select max(date) from v_weight)');
		$stmt->execute();
		$currentWeight = (float)$stmt->fetch()['last_week_average'];
		$stmt->closeCursor();
		return $currentWeight;
	}
	
	public function getUser(){
		$stmt = $this->dbo->prepare("select gender, dob, height, activity_multiplier, deficit, goal_kg from t_user;");
		$stmt->execute();
		$user = $stmt->fetch();
		$stmt->closeCursor();
		return [
			'gender' => $user['gender'],
			'dob' => $user['dob'],
			'height' => (int)$user['height'],
			'activity_multiplier' => (float)$user['activity_multiplier'],
			'deficit' => (int)$user['deficit'],
			'goal_kg' => (float)$user['goal_kg']
		];
	}
	
	public function setUser($user){
		try {
			$this->dbo->beginTransaction();
			# I am assuming a row already exists, and it's just 1 row
			$stmt = $this->dbo->prepare("update t_user set gender = ?, dob = ?, height = ?, activity_multiplier = ?, deficit = ?, goal_kg = ?");
			$stmt->execute([ $user['gender'], $user['dob'], $user['height'], $user['activity_multiplier'], $user['deficit'], $user['goal'] ]);
			$this->dbo->commit();
			return true;
		} catch(PDOException $e){
			$this->dbo->rollback();
			throw $e;
		}
	}
	
	public function getGoogleChartData($fromDate, $toDate){
		
		$from = false;
		$to = false;
		$sql = "select unix_timestamp(date) as unixtime,
					date,
					last_week_average
				from
					v_weight
				where date between ";
				
		if($fromDate == null){
			$sql .= "(select min(date) from v_weight) and ";
		} else {
			$sql .= ":from and ";
			$from = true;
		}
		
		if($toDate == null){
			$sql .= "(select max(date) from v_weight) ";
		} else {
			$sql .= ":to ";
			$to = true;
		}
		
		$sql .= "order by unixtime";
		$stmt = $this->dbo->prepare($sql);
		
		if($from) $stmt->bindValue(':from', $fromDate);
		if($to) $stmt->bindValue(':to', $toDate);
		
		$stmt->execute();
		
		$output = [];
		while($row = $stmt->fetch()){
			array_push( $output, [
				$row['date'],
				(float)$row['last_week_average']
			]);
		}
		
		$stmt->closeCursor();
		return $output;
	}

	/* utility function to set a parameter in the given statement. If the parameter is of length zero, it is set to null */
	private function setParamOrNull( &$stmt, $sParam, $val, $type = PDO::PARAM_STR ){
		if(strlen($val) > 0){
			return $stmt->bindValue( $sParam, $val, $type );		
		} else {
			return $stmt->bindValue( $sParam, null ,PDO::PARAM_NULL );
		}
	}

}


?>