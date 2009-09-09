<?php
/**
 * These are predefined functions common to txtckr.
 *
 * @author		Tom Pasley
 * @date		08/09/2009
 * @last mod	08/09/2009
 * @package 	txtckr
 * @copyright 	open source
 */

 	function safe_normalise($value){
		$value = strtolower(preg_replace('/[\s\\\/;:\.]', '_', $value));
		return ($value);
	}
	
	function normalise($value){
		$value = strtolower(preg_replace('/\s/', '_', $value));
		return ($value);
	}

	function clone_this($new_name){
		$$new_name = clone $this;
	}
		
	function unencode($value)
		$value = urldecode(rawurldecode($value));
		return ($value);
	}
	
	function str_match($needle, $haystack){
		$result = true;
		$pos = strpos($haystack, $needle);
			if ($pos === FALSE) { // Note the use of === strpos may also return a non-Boolean value which evaluates to FALSE
				$result = false;
			}
		return ($result);
	}
	
?>