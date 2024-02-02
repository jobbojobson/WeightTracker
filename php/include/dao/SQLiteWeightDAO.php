<?php
include(__DIR__.'/DataAccessObject.php'); 

/* Data Access Object for SQLite 3 */
class SQLiteWeightDAO extends DataAccessObject {
	
	function __construct($dsn){
		parent::__construct($dsn, null, null);
		
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
					} else {
						throw new PDOException("Kilograms is required");
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
				note,
				image_exists
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
	
	public function setImage($date, $image, $mime){
		try {
			$this->dbo->beginTransaction();
			
			$stmt = $this->dbo->prepare("select date from t_image where date = CAST(strftime('%s', ?) as integer)");
			$stmt->execute([$date]);
			$existingRow = $stmt->fetch();
			$stmt->closeCursor();
			$stmt = null;
			
			if(is_array($existingRow) && (sizeof($existingRow) > 0)){
				$stmt = $this->dbo->prepare("update t_image set image = :image, mime = :mime where date = CAST(strftime('%s', :date) as integer)");
			} else {
				$stmt = $this->dbo->prepare("insert into t_image(date, image, mime) values(CAST(strftime('%s', :date) as integer), :image, :mime)");
			}
			
			$this->setParamOrNull( $stmt, ':date', $date);
			$this->setParamOrNull( $stmt, ':image', $image);
			$this->setParamOrNull( $stmt, ':mime', $mime);
						
			$stmt->execute();
			$this->dbo->commit();
			return true;
		} catch(Exception $e) {
			$this->dbo->rollback();
			if($e->getCode() == '23000'){ #integrity constraint violation
				throw new Exception('Error: Enter a weight value for this day before uploading an image');
			} else {
				throw $e;
			}
			throw $e;
		}
	}
	
	public function getImage($date){
		try {
			$stmt = $this->dbo->prepare("SELECT date, image, mime FROM t_image where date = CAST(strftime('%s', ?) as integer)");
			$stmt->execute([ $date ]);
			$image = $stmt->fetch();
			$stmt->closeCursor();
			return $image;
		} catch(Exception $e){
			throw $e;
		}
	}
	
	public function deleteImage($date){
		try {
			$this->dbo->beginTransaction();
			$stmt = $this->dbo->prepare("delete from t_image where date = CAST(strftime('%s', ?) as integer)");
			$stmt->execute([ $date ]);
			
			$this->dbo->commit();
		} catch(Exception $e){
			$this->dbo->rollback();
			throw $e;
		}
		
	}
}
?>