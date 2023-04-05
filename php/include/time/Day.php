<?php 

class Day extends DateTimeImmutable {
	
	function __construct( $iso8601DateString ){
		parent::__construct( $iso8601DateString );
		$this->setTime(0, 0, 0, 0);
	}
	
	public function __toString(){
		return $this->format('Y-m-d');
	}
	
}
?>