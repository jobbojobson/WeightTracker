<?php 

class Period extends DatePeriod {
	
	private $fromDay;
	private $toDay;
	
	function __construct( $fromDay, $toDay ){
		$this->fromDay = $fromDay;
		$this->toDay = $toDay;
		
		if($this->fromDay->getTimestamp() > $this->toDay->getTimestamp()) 
			throw new Exception('From date must be earlier than to date');
	}
	
	public function __toString(){
		return $this->fromDay->format('Y-m-d').' - '.$this->toDay->format('Y-m-d');
	}
}

?>