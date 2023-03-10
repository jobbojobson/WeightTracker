<?php

/* Data Access Object for SQLite 3 */
class SQLiteWeightDAO implements WeightDAO {
	
	private $dbo;
	
	function __construct(){
		$this->dbo = new PDO('sqlite:'.__DIR__.'/../../db/weight.db');
		$this->dbo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->dbo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		$this->dbo->exec('PRAGMA foreign_keys = ON');
	}
	
	public function setData($rows){
		try {
			$this->dbo->beginTransaction();
			
			foreach($rows as &$row){
				
				$stmt = $this->dbo->prepare("SELECT DATE FROM T_WEIGHT WHERE DATE = CAST(strftime('%s', ?) as integer)");
				$stmt->execute([$row->date]);
				$existingRow = $stmt->fetch();
				$stmt->closeCursor();
				
				if(is_array($existingRow) && (sizeof($existingRow) > 0)){
					
					# Rows that exist already but have been POSTED again with blank kilos and notes are deleted
					if(strlen($row->kilograms) == 0 && strlen($row->note) == 0){
						
						$stmt = $this->dbo->prepare(
							"DELETE FROM T_WEIGHT WHERE DATE = CAST(strftime('%s', :date) as integer)");
							
						$this->setParamOrNull( $stmt, ':date', $row->date );
					} else {
						
						$stmt = $this->dbo->prepare(
							"UPDATE T_WEIGHT SET KILOGRAMS = :kilograms, NOTE = :note WHERE DATE = CAST(strftime('%s', :date) as integer)");
						
						$this->setParamOrNull( $stmt, ':kilograms', $row->kilograms );
						$this->setParamOrNull( $stmt, ':note', $row->note );
						$this->setParamOrNull( $stmt, ':date', $row->date );
					}
					
					$stmt->execute();
					
				} else {
					
					if(strlen($row->kilograms) > 0) {
						$stmt = $this->dbo->prepare(
							"INSERT INTO T_WEIGHT (KILOGRAMS, NOTE, DATE) VALUES(:kilograms, :note, CAST(strftime('%s', :date) as integer))");

						$this->setParamOrNull( $stmt, ':kilograms', $row->kilograms );
						$this->setParamOrNull( $stmt, ':note', $row->note );
						$this->setParamOrNull( $stmt, ':date', $row->date );
						
						$stmt->execute();
					}
					
				}
				
				
			} #next row
			
			$this->dbo->commit();
			return true;
			
		} catch(PDOException $e){
			$this->dbo->rollBack();
			throw $e;
		}
	}
	
	
	public function getData($fromDate, $toDate){
		
		$stmt = $this->dbo->prepare(
			"select 
				date(date, 'unixepoch') as date, 
				kilograms, 
				last_week_average, 
				pounds, 
				stone, 
				note 
			from v_weight 
			where date between CAST(strftime('%s', ?) as integer) and CAST(strftime('%s', ?) as integer) order by date asc");
			
		$stmt->execute([ $fromDate, $toDate ]);
		$output = [];
		while($row = $stmt->fetch()){
			array_push($output, $row);
		}
		$stmt->closeCursor();
		
		return $output;
	}
	
	public function getMaxWeight(){
		$stmt = $this->dbo->prepare('select max(kilograms) as kilograms from v_weight');
		$stmt->execute();
		$maxWeight = $stmt->fetch()['kilograms'];
		$stmt->closeCursor();
		return $maxWeight;
	}
	
	public function getCurrentWeight(){
		$stmt = $this->dbo->prepare('select last_week_average from v_weight where date = (select max(date) from v_weight)');
		$stmt->execute();
		$currentWeight = $stmt->fetch()['last_week_average'];
		$stmt->closeCursor();
		return $currentWeight;
	}
	
	public function getUser(){
		$stmt = $this->dbo->prepare("select gender, date(dob, 'unixepoch') as dob, height, activity_multiplier, deficit, goal_kg from t_user;");
		$stmt->execute();
		$user = $stmt->fetch();
		$stmt->closeCursor();
		return $user;
	}
	
	public function setUser($user){
		try {
			$this->dbo->beginTransaction();
			# I am assuming a row already exists, and it's just 1 row
			$stmt = $this->dbo->prepare("update t_user set gender = ?, dob = CAST(strftime('%s', ?) as integer), height = ?, activity_multiplier = ?, deficit = ?, goal_kg = ?");
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
		$sql = "select 
					date as unixtime,
					strftime('%Y-%m-%d', datetime(date, 'unixepoch')) as date,
					last_week_average
				from
					v_weight
				where date between ";
		
		
		if($fromDate == null){
			$sql .= "(select min(date) from v_weight) and ";
		} else {
			$sql .= "CAST(strftime('%s', :from) as integer) and ";
			$from = true;	
		}
		
		if($toDate == null){
			$sql .= "(select max(date) from v_weight) ";
		} else {
			$sql .= "CAST(strftime('%s', :to) as integer) ";
			$to = true;
		}
		
		$sql .= "order by unixtime";
		$stmt = $this->dbo->prepare($sql);
		
		if($from) $stmt->bindValue(':from', $fromDate);
		if($to) $stmt->bindValue(':to', $toDate);
		
		$stmt->execute();
		
		$output = [];
		while($row = $stmt->fetch()){
			array_push($output, [ $row['date'], $row['last_week_average'] ]);
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