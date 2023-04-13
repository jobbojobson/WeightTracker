<?php 

/* implement this interface to create Data Access Objects for other database technologies */
interface WeightDAO {
	/*
		Accepts an array of objects (rows) to save. The array is populated with objects with the properties:
		date string - ISO8601 format (YYYY-MM-DD)
		kilograms decimal
		note string
		
		returns true on successful save
		
		throws PDOException
	*/
	public function setData($rows);
	
	/*
		get all the data from $fromDate to $toDate
		fromDate string - ISO8601 format (YYYY-MM-DD)
		toDate string - ISO8601 format (YYYY-MM-DD)
		
		returns array of row objects. Each object has properties:
		date string - ISO8601 format (YYYY-MM-DD) 
		kilograms decimal
		last_week_average decimal
		pounds decimal
		stone string
		note string
		image_exists int - 1 if a photo exists for this date, 0 otherwise
		
		throws PDOException
	*/
	public function getData($fromDate, $toDate);
	
	/*
		Return the max(kilograms) 
		
		returns decimal
	*/
	public function getMaxWeight();
	
	/*
		Return the kilograms with the most recent date
		
		returns decimal
	*/
	public function getCurrentWeight();
	
	/*
		Get the user
		
		returns an associative array with the following properties:
		'gender' string
		'dob' string - ISO8601 format (YYYY-MM-DD)
		'height' decimal
		'activity_multiplier' decimal
		'deficit' integer 
		'goal' decimal 
	*/
	public function getUser();
	
	/*
		Update the user
		
		Takes an associative array with the following properties:
		'gender' string
		'dob' string - ISO8601 format (YYYY-MM-DD)
		'height' decimal
		'activity_multiplier' decimal
		'deficit' integer 
		'goal' decimal 
		
		returns true on successful save
		
		throws PDOException
	*/
	public function setUser($user);
	
	/*
		Get data in a Google Charts friendly format, from $fromDate to $toDate
		if $fromDate is null, this method should set it to the earliest date in the database
		if $toDate is null, this method should set it to the latest date in the database
		
		fromDate string - ISO8601 format (YYYY-MM-DD)
		toDate string - ISO8601 format (YYYY-MM-DD)
		
		returns array of arrays as expected by Google Charts:
		[ [ "2023-07-16", 90.5 ] ]
		
	*/
	public function getGoogleChartData($fromDate, $toDate);
	
	/*
		Set an image against the given date
		
		$date string - ISO8601 format (YYYY-MM-DD)
		$image binary - image data as it comes out of file_get_contents
		$mime string - the image's MIME type
		
		returns true on successful save
		
		throws PDOException
	*/
	public function setImage($date, $image, $mime);
	
	/*
		Get the image for the given date.
		Returns an associative array with the following properties:
		'date' string - ISO8601 format (YYYY-MM-DD)
		'image' binary - the image data
		'mime' string - the image data's mime type
		
		throws PDOException
	*/
	public function getImage($date);
}

?>