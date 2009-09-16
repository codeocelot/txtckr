<?php
/**
 * This is the base class for an txtckr object, which extends the basic contextobject to: 
 *		- look up relevant details from database
 * 		- stores those in an array for presenting as options
 *		- generates new objects for each of the rules referred to in the database
 *
 *
 * @author		Tom Pasley
 * @date		08/09/2009
 * @last mod	09/09/2009
 * @package 	txtckr
 * @copyright 	open source
 */

require('contextobject_class.php');

class txtckr extends contextobject{
	
 	function __construct(){ 					// default structure for a new rule, which will be saved into a database		
		parent::__construct();
		$this->errors['rules']		=	'';
		$this->errors['knowbase']	=	'';
		$this->knowbase['results']	= 	0;
		$this->knowbase['ids']		= 	0;
		$this->rules['count']		=	0;
	}

	require ('knowbase_class.php');				// database lookup for openurl targets
	require ('common_funcs.inc.php');			// reserved for txtckr processes
	require ('user_funcs.inc.php');				// this is one file that the end-user can use to create new functions, at their own risk.

	
	function get_rule($rule_id){ 				// get the rule id from the knowbase lookup, and load values from it into $this
		$rule_query = 'SELECT * FROM RULES WHERE RULE_ID = "'.$rule_id.'"';
		$properties = 
		foreach ($properties as $key  => $val){
			if(!empty($arr[$key])){
				$this->items[$key]	=	$val;
			}
		}
	}
	
	function get_required(){ 					// need to check if this works with preg_match and function
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
			return $services;					// if this is a new object to be stored in knowbase, then return possible services
		} else {
			return $this->servicetype;			// oops this is set - must be a valid rule
		}
	}

	function parse_template(){
		if (empty($this->template)){
			$this->errors[$this->template] .= '\nNo templates to include.'; 
			return($this->errors[$this->template]);
		} else {
		$this->path_to_template = $_SETTINGS['template_location'].$this->template.'.php';
			if (!file_exists($path_to_template)){
				$this->errors[$this->template] .= '\n'.$this->template.' was unable to be found at '.$this->path_to_template; 
				return($this->errors[$this->template]);
			} else {
				include ($path_to_template);
			}
		}
	}
	
}
?>