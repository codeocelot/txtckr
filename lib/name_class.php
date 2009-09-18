<?php
/**
 * These are the base classes for dealing with named objects, such as people!
 *
 * @author		Tom Pasley
 * @date		03/08/2009
 * @last mod	12/08/2009
 * @package 	txtckr
 * @copyright 	open source
 */

class named_entity(){

	function __construct(){
		$this->type				= 'unknown';
		$this->subtype			= 'unknown';
		$this->name				= string(); // the name as supplied
		$this->first_name		= string();
		$this->second_name		= string();
		$this->third_name		= string();
		$this->initial_1		= string(); // first initial
		$this->initial_m		= string(); // middle initial only
		$this->initials			= string(); // all initials
		$this->last_name		= string(); // last name 
		$this->pre_last_name	= string(); // any names which are part of the last name
		$this->title-diff		= string(); // any "titles" or other differentatiors
		$this->full_name		= string(); // the name when fully parsed
		$this->complete			= 'incomplete';
		$this->temparr			= array();
		$i						= 1;
	}

	require ('common_funcs.inc.php');
	require ('settings.inc.php');
	
	function check_prefix(){ // need to check if this works with preg_match and function
	if (!defined($this->define_n-prefixes)){	
		// any names which are part of the last name, see examples below, with more complex last name prefixes first!
		$prefixes[00] = '/\bvan\sden\b/i'; // van den = Dutch
		$prefixes[01] = '/\bvan\sder\b/i'; // van der = Dutch
		$prefixes[02] = '/\bvan\sde\b/i'; // van de = Dutch
		$prefixes[03] = '/\bvan\b/i'; // van = Dutch
		$prefixes[04] = '/\bvon\b/i'; // von = German
		$prefixes[05] = '/\bdela\b/i'; // dela = French/Italian?
		$prefixes[06] = '/\bde\sla\b/i'; // de la = French
		$prefixes[07] = '/\bde\b/i'; // de = Dutch/French?
		$prefixes[08] = '/\bdes\b/i'; // des = French
		$prefixes[09] = '/\bdi\b/i'; // di = Italian
		$prefixes[10] = '/\bdu\b/i'; // du = French
		$prefixes[11] = '/\baf\b/i'; // af = Swedish
		$prefixes[12] = '/\bbin\b/i'; // bin = Arabic
		$prefixes[13] = '/\bben\b/i'; // ben = Hebrew
		$prefixes[14] = '/\bibn\b/i'; // ibn = Arabic
		$prefixes[15] = '/\buyt\sden\b/i'; // uyt den = Dutch
		$prefixes[16] = '/\buyt\sder\b/i'; // uyt der = Dutch
		$prefixes[17] = '/\bten\b/i'; // ten = Dutch
		$prefixes[18] = '/\bter\b/i'; // ter = Dutch
		$prefixes[19] = '/\bhet\b/i'; // het = Dutch?
		$prefixes[20] = '/\bab\b/i'; // ab = Welsh
		$prefixes[21] = '/\bap\b/i'; // ap = Welsh
		$prefixes[22] = '/\bst\.\b/i'; // st. = English/French?
	}
	$matches = array();
		foreach ($prefixes as $prefix => $poss_prefix){
			if (preg_match ($poss_prefix, $this->name, $matches){
				$this->set_prefix(trim($matches[1]));
			}
		}
	}

	function check_prefix_ne_other($order){
	if (!defined($this->define_n-pre_others)){
		// any names which could be either first names or prefixes (see below)
		// must start with "'/" and end with "$/D'" as we're after complete matches only
		$pre_others[00] = '/^Van$/D'; // Van = "West, Van", "Morrison, Van", or "Van Lustbader, Eric"?
		$pre_others[01] = '/^Ben$/D'; // Ben = "Ben Carey, Donald" or "Carey, Ben"?
	}
	$matches = array(); // make sure no first name or initials are set first!
		switch($order){
			case "first":
				if (($this->first_name === null) && ($this->initials === null)){
					foreach ($pre_others as $pre_other => $poss_pre_other){
						if (preg_match ($poss_pre_other, $this->pre_last_name, $matches){
							$this->first_name = ucfirst(trim($matches[1]));
							$initials = str_split($matches[1], 1);
							$this->initials = $initials[0].'. ';
							$this->pre_last_name = null;
						}
					}
				}
				break;
			case "second":
				if ($this->second_name === null){
					foreach ($pre_others as $pre_other => $poss_pre_other){
						if (preg_match ($poss_pre_other, $this->pre_last_name, $matches){
							$this->second_name = ucfirst(trim($matches[1]));
							$initials = str_split($matches[1], 1);
							$this->initials = $initials[0].'. ';
							$this->pre_last_name = null;
						}
					}
				}
				break;
			case "third":
				if ($this->third_name === null){
					foreach ($pre_others as $pre_other => $poss_pre_other){
						if (preg_match ($poss_pre_other, $this->pre_last_name, $matches){
							$this->third_name = ucfirst(trim($matches[1]));
							$initials = str_split($matches[1], 1);
							$this->initials = $initials[0].'. ';
							$this->pre_last_name = null;
						}
					}
				}
				break;
			default:
				break;
		}		
	}
	
	function check_suffix($value){
	if (!defined(define_n-suffixes)){
		// any "titles" or other differentatiors for people with same/common names, e.g. Senior, Junior, Dr.
		$suffixes[00] = '/\bjr\.\b/i'; // Jr. = American
		$suffixes[01] = '/\bsr\.\b/i'; // Sr. = American
		$suffixes[02] = '/\b[ivx]+\b/i'; // IV, III = English/American
		$suffixes[03] = '/\bph\.?d\b/i'; // Doctor
		$suffixes[04] = '/\bm\.?d\b/i'; // Masters
		$suffixes[05] = '/\besq\.\b/i'; // Esquire
		$suffixes[06] = '/\besquire\b/i'; // Esquire
		$suffixes[07] = '/\bjudge\b/i'; // Esquire
	}
		foreach ($suffixes as $suffix => $poss_suffix){
			if (preg_match ($poss_suffix, $this->name, $matches){
				$this->set_suffix(trim($matches[1]));
			}
		}
	}
	
	function get_name($name_type){
		$name_type = $this->normalise($name_type);
		if ($this->type == 'personal'){
			switch($name_type){
				case "fullname": 
					if ($this->full_name === null){
						$this->full_name = rtrim($this->get_name(last).', '$this->get_name(first);
						if ($this->second_name !== null){
							$this->full_name .= ' '.$this->second_name;
						}
					}
					return ($this->full_name);
					break;
				case "title-diff": //returns Dr.
					return($this->title-diff);
					break;
				case "firstname": // returns Tom
					$this->check_prefix_ne_other('first');
					return ($this->first_name);
					break;
				case "secondname": // returns Gilbert
					$this->check_prefix_ne_other('second');
					return ($this->second_name);
					break;
				case "thirdname": // returns Kennedy
					$this->check_prefix_ne_other('third');
					return ($this->third_name);
					break;
				case "initials": // returns T. G. K.
					return (rtrim($this->initials));
					break;
				case "initial_1": // returns T.
					return (rtrim($this->initial_1));
					break;
				case "initials_m": // returns G.
					return (rtrim($this->initial_m));
					break;
				case "lastname": // returns Pasley
					if ($this->pre_last_name !== null){
						$temp-name  =  $this->pre_last_name;
						return ($temp-name);
					} else {
						return ($this->last_name);
					}
					break;
				case "last+first": // returns Pasley, Tom
					if ($this->first_name !== null){
						$temp-name  = $this->get_name('last').', '.$this->get_name('first');
						$temp-name .= ($this->title-diff !== null) ? ' ['.$this->title-diff.']' : "";
						return ($temp-name);
					} else{
						$this->get_name('last+initial');
					}
					break;
				case "last+initial_1": // returns Pasley, T. [Dr.]
					if ($this->initials !== null){
						$temp-name .= $this->get_name('last').', '.$this->get_name('initial_1');
						$temp-name .= ($this->title-diff !== null) ? ' ['.$this->title-diff.']' : "";
						return ($temp-name);
						unset($temp-name);
					} else {
						return ($this->get_name('last'));
					}
					break;
				case "last+initials": // returns Pasley, T. G. P. [Dr.]
					if ($this->initials !== null){
						$temp-name = $this->get_name('last').', '.$this->get_name('initials');
						$temp-name .= ($this->title-diff !== null) ? ' ['.$this->title-diff.']' : "";
						return ($temp-name);
						unset($temp-name);
					} else {
						return ($this->get_name('last'));
					}
					break;
				default:
					if (isset($this->$name_type){
						return ($this->$name_type);
					} else {
						return (null);
					}
					break;
			}	
		} elseif ($this->type == 'corporate') {
					if (isset($this->$name_type){
						return ($this->$name_type);
					} else {
						return ($this->full_name);
					}
					break;		
		} else {
			if (isset($this->$name_type){
				return ($this->$name_type);
			}
		}
	}
	
	function set_name($name, $type){
		$type = $this->normalise($type);
		$this->set_type($type);
		if ((!isset($type)){
			$this->$type = $name;
		}
	}
	
	function set_prefix($prefix){
		if (!isset($this->pre_last_name)){
			$this->pre_last_name = $prefix;
			$value = ' '.$this->name;
			$this->name = trim(preg_replace($prefix, '', $value));
		} else {
			$this->pre_last_name .= $prefix;
			$value = ' '.$this->name;
			$this->name = trim(preg_replace($prefix, '', $value));
		}
	}
	
	function set_suffix($suffix){
		if (!isset($this->title-diff)){
			$this->title-diff = $suffix;
			$value = ' '.$this->name;
			$this->name = trim(preg_replace($suffix, '', $value));
		} else {
			$this->title-diff .= $suffix;
			$value = ' '.$this->name;
			$this->name = trim(preg_replace($suffix, '', $value))
		}
	}
	
	function set_type($name_type){
		$name_type = $this->normalise($name_type);
		switch(true){
			case ($name_type == 'aucorp'): // corporate author
				$this->type				= 'corporate';
				$this->subtype			= 'author';
				break;
			case (preg_match('/^au/', $name_type) : // author
				$this->type				= 'personal';
				$this->subtype			= 'author';
				break;
			case (preg_match('/^ed/', $name_type): // editor
				$this->type				= 'personal';
				$this->subtype			= 'editor';
				break;
			case (preg_match('/^inv/', $name_type) // inventor
				$this->type				= 'personal';
				$this->subtype			= 'inventor';
				break; // then it gets slightly more complicated
			default: // hmmm... "other"
				$this->type				= 'personal';
				$this->subtype			= $name_type;			
				break;
		}
	}
	
	function parse_name($name){
		if ($name === null)) {
			break;
		}
		$this->name = preg_replace('/[,]\s+/',' ',trim($name));
		if ($this->type	== 'corporate'){
			$this->full_name = $name;
			break;
		}
		$this->check_prefix();
		$this->check_suffix();
		$this->complete = 'constructed';
		switch (true){
			case (stristr(', ', $this->name)):
				$this->temparr = explode(',', $this->name);
				$this->last_name = array_shift($this->temparr); // shift the first value off the temparr - this should hopefully be the "last_name"
				$this->name = implode(' ', $this->temparr);
				$this->temparr = array(); // empty the temparr, and start again...
				$this->parse_name();
				break;
			case (stristr(' ', $this->name)):
				$this->temparr = explode(' ', $this->name);
				break;
		}
		
		if ((array_count_values($this->temparr)) > 1){ // make sure there's something worth processing
			if (!isset($name_arr['last_name'])){
				$this->last_name = array_pop($this->temparr); // pop the last value off the temparr - this should be the "last_name"
				$this->full_name = $this->last_name.', ';
			} 
			foreach ($this->temparr as $item => $name_seg) {
				if ($name_seg !== null) {
					$i = 1; $i++;
					$this->full_name .= ' ';
					if ($i == 1){
						$this->first_name  	 = $name_seg;
						$this->full_name	.= $name_seg;
						$initials = str_split($name_seg, 1);
						$this->initials 	 = $initials[0].'. ';
						$this->initial_1	 = $initials[0].'. ';
					} elseif($i == 2) {
						$this->second_name 	 = $name_seg;
						$this->full_name 	.= $name_seg;
						$initials = str_split($name_seg, 1);
						$this->initials 	.= $initials[0].'. ';
						$this->initial_m	 = $initials[0].'. ';
					} elseif($i == 3) {
						$this->third_name 	 = $name_seg;
						$this->full_name 	.= $name_seg;
						$initials = str_split($name_seg, 1);
						$this->initials 	.= $initials[0].'. ';
					} else {
						$this->full_name	.= $name_seg;
						$initials = str_split($name_seg, 1);
						$this->initials 	.= $initials[0].'. ';
					}
				}
			}
		}
	}

}
?>