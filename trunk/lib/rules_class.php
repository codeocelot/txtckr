<?php
/**
 * This is the base class for a rules object, which generates links for a given service.
 *
 *
 * @author		Tom Pasley
 * @date		08/09/2009
 * @last mod	09/09/2009
 * @package 	txtckr
 * @copyright 	open source
 */

class rules_parser(){	
 	function __construct(){ 					// default structure for a new rule, which will be saved into a database
		$properties[00]		=	"date";
		$properties[01]		=	"rule_id";
		$properties[02]		=	"linktext";
		$properties[03]		=	"rule";
		$properties[04]		=	"reftype";
		$properties[05]		=	"sourcetype";
		$properties[06]		=	"required";
		$properties[07]		=	"servicetype";

		$this->date					=	= date("YmdHis").rand(00,59);
		$this->rule_id				=	''; 	// leave this for the database to populate
		$this->linktext				=	string();
		$this->rule					= 	string();
		$this->sourcetype			=	string();
		$this->reftype				=	array();
		$this->required				=	array();
		$this->servicetype			=	string();
		$this->errors				=	array();
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
		$i = 1; $i++;
		switch (true){
			case ((count($this->$$item) == 0) && ($validator === null)):
				$break[$i] = 'false';
				break;
			case (($validator !== null) && (!defined($this->$validator)) && (!defined($validator))):
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
					$_RULES[$item] .= '\n'.$item.' successfully completed initial test '.$validator.'.';
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
			return($this->errors); 				// abort - the rule didn't work.
		}
			$this->errors['rule'] .= .'\n'.$rule.' is being processed.';
			$_RULES = array();
			$
	}
}
?>