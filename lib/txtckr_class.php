<?php
/**
 * This is the base class for an txtckr object, which looks up relevant details from database:
 * 		- stores those in an array for presenting as options
 *		- parses each of the rules referred to in the database
 *
 *
 * @author		Tom Pasley
 * @date		08/09/2009
 * @last mod	09/09/2009
 * @package 	txtckr
 * @copyright 	open source
 */

require('knowbase_class.php');
 
class txtckr {
	
 	function __construct($arr){
		$properties[00]		=	"date";
		$properties[01]		=	"linktext";
		$properties[02]		=	"rule";
		$properties[03]		=	"reftype";
		$properties[04]		=	"sourcetype";
		$properties[05]		=	"required";
		$properties[06]		=	"servicetype";
		
		$services[00]		=	"fulltext";
		$services[01]		=	"holdings";
		$services[02]		=	"request";
		$services[03]		=	"citation";
		$services[04]		=	"abstract";
		$services[05]		=	"reference";
		$services[06]		=	"information";
		$services[07]		=	"indexing";
		$services[08]		=	"ranking";
				
		$this->date			= 	date("YmdHis").rand(00,59);
		$this->linktext		=	string();
		$this->rule			= 	string();
		$this->reftype		=	array();
		$this->required		=	array();
		$this->servicetype	=	string();
		$this->code			=	string();
		
		foreach ($properties as $key  => $val){
			if(!empty($arr[$key])){
				$this->items[$key]		= 	$val;
			}
		} 
	}
	
	require ('common_funcs.inc.php');
	require ('user_funcs.inc.php');	

	function knowbase_lookup(){
	
	}
	
	function get_required(){ // need to check if this works with preg_match and function
		$tokens = array();
		preg_match ('/<[a-zA-Z0-9:]*>/', $this->rule, $tokens);
		foreach ($tokens as $num => $token){
			if (str_match(':', $token){
				while = explode(':', $token, 2)){
					if ($test === null){
						$this->required[$value] = $test;
					} else {
						$this->required[$value] = "isset";
					}
				}
			}
		}
	}

	function get_services(){
		if (empty($this->servicetype){
			return $services;
		} else {
			return $this->servicetype;
		}
	}

	function parse_rule(){
	if (empty($this->rule)){
		$this->errors['rule'] .= '\n'.$rule.' was found, but was unable to be parsed.';
		return null;
	}
	$this->get_required();
	$this->get_services();
	$_RULES = array();
	
	// $validators = array_keys($this->required);
	// $items = array_values($this->required);
	$count_validators = array_count_values($this->required);
	
		foreach ($this->required as $item => $validator){
		$item = $this->normalise($item);
		$validator = $this->normalise($validator);
		$_RULES[$item] = '';
		$path_to_validator = $_SETTINGS['validators_location'].'/'.$validator.'.php';
		$i = 1; $i++;
		switch (true){
			case ((count($this->$$item) == 0) && ($validator === null)):
				$break[$i] = 'false';
				break;
			case (($validator !== null) && (!defined($this->$validator)) && (!defined($validator)) && (!file_exists($path_to_validator))):
				$_RULES[$item] .= '\nValidation method '.$validator.' does not exist, or is not loaded.';
				$break[$i] = 'true';
				break;
			case (($validator !== null) && (!defined($this->$validator)) && (!file_exists($path_to_validator))):
				$_RULES[$item] .= '\nValidation method '.$validator.' does not exist, or is not loaded.';
				$break[$i] = 'true';
				break;
			case (($validator !== null) && (defined($this->$validator)):
				include($path_to_validator);
				if(!isset($this->$$item)){
					$_RULES[$item] .= '\n'.$item.' is not set, so can not be tested by '.$validator.'.';
					$break[$i] = 'true';
				} elseif ($this->$validator($item) == false){
					$_RULES[$item] .= '\n'.$item.' did not pass the test '.$validator.'.';
					$break[$i] = 'true';
				} else {
					$_RULES[$item] .= '\n'.$item.' successfully completed initial test '.$validator.'.';
				}
				break;			
			case (($validator !== null) && (defined($validator)):
				$_RULES[$item] .= '\nValidation method '.$validator.' exists.';
				if(count($this->$$item) == 0){
					$_RULES[$item] .= '\n'.$item.' is not set, so can not be tested by '.$validator.'.';
					$break[$i] = 'true';
				} elseif ($validate($item) == null){
					$_RULES[$item] .= '\n'.$item.' did not pass the test '.$validator.'.';
					$break[$i] = 'true';
				} else {
					$_RULES[$item] .= '\n'.$item.' successfully completed initial test'.$validator.'.';
					$break[$i] = 'false';
				}
				break;
			}
		}
		
		if (in_array("true", $break)) {
			$this->errors['rule'] .= .'\n'.$rule.' had errors - it can not be included';
			$_RULES .= array_count_values($break);
			$this->errors['rule'] .= $_RULES;
			$_RULES = array();
			return ($this->errors);
		} else {
			$this->errors['rule'] .= .'\n'.$rule.' is being processed.';
			$_RULES = array();
			return ($this->errors);
		}
		
	}
}
?>