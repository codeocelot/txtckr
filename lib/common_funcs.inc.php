<?php
/**
 * These are predefined functions common to txtckr.
 *
 * @author		Tom Pasley
 * @date		08/09/2009
 * @last mod	15/09/2009
 * @package 	txtckr
 * @copyright 	open source
 */

###################### STRING FUNCTIONS : START ######################
 
//-- normalise : START
	// normalises a string, replacing spaces with underscores, making string lowercase
	// returns normalised value 	
	function normalise($value){
		$value = strtolower(preg_replace('/\s/', '_', $value));
		return ($value);
	}
//-- normalise : FINISH

//-- safe_normalise : START
	// safely normalises a string, replacing spaces, colons, backslashes, etc. with underscores, making string lowercase
	// returns normalised value 
 	function safe_normalise($value){
		$value = strtolower(preg_replace('/[\s\\\/;:\.]', '_', $value));
		return ($value);
	}
//-- safe_normalise : FINISH

//-- unencode : START
	// quick and dirty function for handling data sent in KEV format
	// returns cleaned value
	function unencode($value)
		$value = urldecode(rawurldecode($value));
		return ($value);
	}
//-- unencode : FINISH

//-- str_match : START
	// similar to pre_match, but for strings
	// returns boolean: true if found; false if not found
	function str_match($needle, $haystack){
		$result = true;
		$pos = strpos($haystack, $needle);
			if ($pos === FALSE) { // Note the use of === strpos may also return a non-Boolean value which evaluates to FALSE
				$result = false;
			}
		return ($result);
	}
//-- str_match : FINISH

###################### STRING FUNCTIONS : FINISH ######################

###################### NUMERIC FUNCTIONS : START ######################

//-- make_month_num : START
	// converts a PubMed/Other month format to two-digit numeric string
	// returns 01 if not a recognisable month
	function make_month_num($value){
	$num_month = "01";
	$arr = str_split($value, 3);
			switch (strtoupper($arr[0])){
				case "JAN":
					$num_month = "01";
					break;
				case "FEB":
					$num_month = "02";
					break;
				case "MAR":
					$num_month = "03";
					break;
				case "APR":
					$num_month = "04";
					break;
				case "MAY":
					$num_month = "05";
					break;
				case "JUN":
					$num_month = "06";
					break;
				case "JUL":
					$num_month = "07";
					break;
				case "AUG":
					$num_month = "08";
					break;
				case "SEP":
					$num_month = "09";
					break;
				case "OCT":
					$num_month = "10";
					break;
				case "NOV":
					$num_month = "11";
					break;
				case "DEC":
					$num_month = "12";
					break;
			}
	return ($num_month);		
	}	
//-- make_month_num : FINISH

//-- make_timestamp_openurl : START
	// takes a timestamp such as 200305201327
	// returns an OpenURL standard date like 2003-05-20
	function make_timestamp_openurl($timestamp){
	$date = NULL;
	if ((ctype_digit($timestamp)) |(is_numeric($timestamp)) ) {
	$arr = str_split($timestamp, 8);
	$datearr = str_split($arr[0], 2);
	$date = $datearr[0].$datearr[1]."-".$datearr[3]."-".$datearr[4];
	}
	return ($date);
	}
//-- make_timestamp_openurl : FINISH

###################### NUMERIC FUNCTIONS : FINISH ######################

###################### OBJECT FUNCTIONS : START ######################
	
//-- clone_object : START
	// clones the existing object 
	// creates the cloned object with $new_name
	function clone_object($new_name){
		$$new_name = clone $this;
	}
//-- clone_object : FINISH

###################### OBJECT FUNCTIONS : START ######################	


	
?>