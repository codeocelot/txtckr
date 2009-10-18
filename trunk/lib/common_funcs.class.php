<?php
 /**
  * @file
  * Common functions class file
  *
  * $Id: common_funcs.class.php,v 1.0 10/16/2009 5:47 PM tom.pasley Exp $
  */
 
  /**
  * txtckr
  *
  * Provides predefined functions common to txtckr classes
  * 
  * @package txtckr
  * @author Tom Pasley
  * @copyright 	open source: LGPL
  **/


class common_functions {
#### STRING FUNCTIONS : START #####
	/**
	 * public static method
	 *
	 *	common_functions::normalise(param)
	 *
	 * @param	string
	 * @return	string lowercase, spaces replaced with underscores
	 * @example	"Something fishy" > "something_fishy"
	 * @note	
	 */ 
	public function normalise($value){
		$value = strtolower(preg_replace('/\s/', '_', $value));
		return ($value);
	}


	/**
	 * public static method
	 *
	 *	common_functions::safe_normalise(param)
	 *
	 * @param	string
	 * @return	string lowercase, spaces, colons, backslashes, etc. replaced with underscores
	 * @example	"http://Something\ fishy" > "http___something__fishy"
	 * @note	
	 */ 
 	function safe_normalise($value){
		$value = strtolower(preg_replace('/[\s\\\/;:\.]', '_', $value));
		return ($value);
	}


	/**
	 * public static method
	 *
	 *	common_functions::unencode(param)
	 *
	 * @param	string	url and/or rawurlencoded
	 * @return	string	string, stripped of any url encoding
	 * @example	"rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Apatent" > "rft_val_fmt=info:ofi/fmt:kev:mtx:patent"
	 * @note	for handling data sent in KEV format
	 */ 
	function unencode($value){
		$value = urldecode(rawurldecode($value));
		return ($value);
	}

	/**
	 * public static method
	 *
	 *	common_functions::str_match(params)
	 *
	 * @param	string	needle
	 * @param	string	haystack
	 * @return	boolean true if found; false if not found
	 * @example	str_match('hay', 'haystack') > true
	 * @note
	 */
	function str_match($needle, $haystack){
		$result = true;
		$pos = strpos($haystack, $needle);
			if ($pos === FALSE) { // Note the use of === strpos may also return a non-Boolean value which evaluates to FALSE
				$result = false;
			}
		return ($result);
	}
	

#### STRING FUNCTIONS : FINISH ####

#### NUMERIC FUNCTIONS : START ####
	/**
	 * public static method
	 *
	 *	common_functions::make_month_num(param)
	 *
	 * @param	string	'mar'
	 * @return	string	('01' if not a recognisable month)
	 * @example	make_month_num('mar') > '03'
	 * @note	converts a PubMed/Other month format to two-digit numeric string
	 */
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

	/**
	 * public static method
	 *
	 *	common_functions::make_timestamp_openurl(param)
	 *
	 * @param	string/integer	
	 * @return	string	returns an OpenURL standard date like 2003-05-20
	 * @example	make_timestamp_openur('200305201327') > '2003-05-20'
	 * @note	returns an OpenURL standard date
	 */
	function make_timestamp_openurl($timestamp){
	$date = NULL;
		if ((ctype_digit($timestamp)) |(is_numeric($timestamp)) ) {
			$arr = str_split($timestamp, 8);
			$datearr = str_split($arr[0], 2);
			$date = $datearr[0].$datearr[1]."-".$datearr[3]."-".$datearr[4];
		}
	return ($date);
	}

#### NUMERIC FUNCTIONS : FINISH ####

#### OBJECT FUNCTIONS : START ####

	/**
	 * public static method
	 *
	 *	common_functions::clone_object(param)
	 *
	 * @param	object	
	 * @return	string	returns an OpenURL standard date like 2003-05-20
	 * @example	$that = clone_object($this)
	 * @note	creates the cloned object with $new_name
	 */	
	function clone_object($new_name){
		$$new_name = clone $this;
	}


#### OBJECT FUNCTIONS : FINISH ####	

}
	
?>