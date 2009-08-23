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

class name_collector(){
	function __construct(){
		$this->first_name[0]		= 0;
		$this->initials[0]			= 0;
		$this->last_name[0]			= 0;
		$this->pre_last_name[0]		= 0; // e.g. 'van der' 	as in Willem van der Weerden
		$this->post_last_name[0]	= 0; // e.g. 'III'		as in William Gates III
		$this->full_name[0]			= 0;
		$this->corp_name[0]			= 0;
		$this->name_registry[0]		= 0;
		$this->name_check[0]		= 0;
		$this->partials_list[0]		= 0;
		$i							= 1;
	}
	
	// not convinced either way if this should extend the named_entity class.
	// also need to allow for "van der ..." varieties of names and non-European names
	// this is going to be a very complex class to manufacture!

	function get_name_registry(){
		return array($this->name_registry);
	}
	
	function set_name($name_type, $value){
		$this->partials_list[$i++]	= $name_type; //register what came in which order, useful for name fragments.
		switch($name_type){
			case "aucorp": // corporate author full name
				$entity = 'aucorp'.$i;
				$this->name_registry[$i] = $entity;
				$$entity = new($named_entity);
				$$entity->full_name	 		= $value;
				$$entity->type				= 'corporate';
				// no need to set the subtype as the default is author
				$$entity->complete			= 'constructed';
				break;
			case "au": // author full names (full names first might be easier?)
				$this->temparr = $this->split_name($value);
				$entity = 'author'.$i;
				$this->name_registry[$i] 	= $entity;
				$$entity = new($named_entity);
				$$entity->last_name			= $temparr['last_name'];
				$$entity->first_name	 	= $temparr['first_name'];
				$$entity->initials			= rtrim($temparr['initials']);
				$$entity->full_name		 	= $temparr['full_name'];
				// no need to set the type as the default is personal
				// no need to set the subtype as the default is author
				$$entity->complete			= 'constructed';
				break;
			case "ed": // editor full names first
				$temparr = $this->split_name($value);
				$entity = 'editor'.$i;
				$this->name_registry[$i] = $entity;
				$$entity = new($named_entity);
				$$entity->last_name		 	= $temparr['last_name'];
				$$entity->first_name	 	= $temparr['first_name'];
				$$entity->initials	 		= rtrim($temparr['initials']);
				$$entity->full_name		 	= $temparr['full_name'];
				// no need to set the type as the default is personal
				$$entity->subtype			= 'editor';
				$$entity->complete			= 'constructed';
				break;
			case "inv": // inventor full names first
				$temparr = $this->split_name($value);
				$entity = 'inventor'.$i;
				$this->name_registry[$i] = $entity;
				$$entity = new($named_entity);
				$$entity->last_name	 		= $temparr['last_name'];
				$$entity->first_name	 	= $temparr['first_name'];
				$$entity->initials	 		= rtrim($temparr['initials']);
				$$entity->full_name		 	= $temparr['full_name'];
				// no need to set the type as the default is personal
				$$entity->subtype			= 'inventor';
				$$entity->complete			= 'constructed';
				break; // then it gets slightly more complicated
			case "aulast": // author surname (surnames next - should only be one of these per person!)
				$entity = 'author'.$i;
				$this->name_registry[$i]	= $entity;
				$this->name_check[$i]		= $entity; // this will need to be checked for completeness later.
				$$entity = new($named_entity); // create a named entity - can work out other names parts later.
				// no need to set the type as the default is personal
				// no need to set the subtype as the default is author
				$$entity->last_name[$i] 	= $value; // set the last name directly.
				$this->last_name[$i] 		= $value;
				$this->subtype[$i]			= 'author';
				break;
			case "edlast": // editor surname
				$entity = 'editor'.$i;
				$this->name_registry[$i] 	= $entity;
				$this->name_check[$i]		= $entity;
				$$entity = new($named_entity);
				// no need to set the type as the default is personal
				$$entity->subtype			= 'editor';
				$$entity->last_name[$i] 	= $value;
				$this->last_name[$i] 		= $value;
				$this->subtype[$i]			= 'editor';
				break;
			case "invlast": // inventor surname
				$entity = 'inventor'.$i;
				$this->name_registry[$i] 	= $entity;
				$this->name_check[$i]		= $entity;
				$$entity = new($named_entity);
				// no need to set the type as the default is personal
				$$entity->subtype			= 'inventor';
				$$entity->last_name[$i] 	= $value;
				$this->last_name[$i] 		= $value;
				$this->subtype[$i]			= 'inventor';
				break;
			case "aufirst": // author first name
				$this->first_name[$i] 		= $value;
				break;
			case "edfirst": // editor first name
				$this->first_name[$i] 	= $value;
				break;
			case "invfirst": // inventor first name
				$this->first_name[$i] 	= $value;
				break;
			case "auinit": // author initials
				$this->initials[$i]		= $value;
				break;
			case "edinit": // editor initials
				$this->initials[$i]		= $value;
				break;
			case "invinit": // inventor initials
				$this->initials[$i] 	= $value;
				break;
			default: // hmmm... "other"
				$entity = $name_type.$i;
				$$entity = new($named_entity);
				$this->$name_type[$i]	= $value;
				break;
		}
	}
		
	function check_name_registry(){ // need to ensure that count(last_names) == count(first_names) + count(initials);
		$registry = $this->get_name_registry();
	}
	
}

class named_entity(){

	function __construct(){
		$this->type				= 'personal';
		$this->subtype			= 'author';
		$this->old_name			= string();
		$this->first_name		= string();
		$this->initials			= string();
		$this->last_name		= string();
		$this->pre_last_name	= string();
		$this->post_last_name	= string();
		$this->full_name		= string();
		$this->complete			= 'incomplete';
		$this->temparr			= array();
		$i						= 1;

		// any names which could be either first names or prefixes (see below)
		// must start with "'/" and end with "$/'" as we're after complete matches only
		$pre_firsts[00] = '/^Van$/'; // Van = "Van West", "Van Morrison", or "Van Lustbader, Eric"?		
		
		// any names which are part of the last name, see examples below, with more complex last name prefixes first!
		$prefixes[00] = '/\b[Vv]an\s[Dd]en\b/'; // van den = Dutch
		$prefixes[01] = '/\b[Vv]an\s[Dd]er\b/'; // van der = Dutch
		$prefixes[02] = '/\b[Vv]an\s[Dd]e\b/'; // van de = Dutch
		$prefixes[03] = '/\b[Vv]an\b/'; // van = Dutch
		$prefixes[04] = '/\b[Vv]on\b/'; // von = German
		$prefixes[05] = '/\b[Dd]ela\b/'; // dela = French/Italian?
		$prefixes[06] = '/\b[Dd]e\sla\b/'; // de la = French
		$prefixes[07] = '/\b[Dd]e\b/'; // de = Dutch/French?
		$prefixes[08] = '/\b[Dd]es\b/'; // des = French
		$prefixes[09] = '/\b[Dd]i\b/'; // di = Italian
		$prefixes[10] = '/\b[Dd]u\b/'; // du = French
		$prefixes[11] = '/\b[Aa]f\b/'; // af = Swedish
		$prefixes[12] = '/\b[Bb]in\b/'; // bin = Arabic
		$prefixes[13] = '/\b[Bb]en\b/'; // ben = Hebrew
		$prefixes[14] = '/\b[Ii]bn\b/'; // ibn = Arabic
		$prefixes[15] = '/\b[Uu]yt\sden\b/'; // uyt den = Dutch
		$prefixes[16] = '/\b[Uu]yt\sder\b/'; // uyt der = Dutch
		$prefixes[17] = '/\b[Tt]en\b/'; // ten = Dutch
		$prefixes[18] = '/\b[Tt]er\b/'; // ter = Dutch
		$prefixes[19] = '/\b[Hh]et\b/'; // het = Dutch?
		$prefixes[20] = '/\b[Aa]b\b/'; // ab = Welsh
		$prefixes[21] = '/\b[Aa]p\b/'; // ap = Welsh
		$prefixes[22] = '/\b[Ss]t\.\b/'; // st. = English/French?
		
		// any common differentatiors for people with same name, e.g. Senior, Junior, Dr.
		$suffixes[00] = '/\b[Jj]r\.\b/'; // Jr. = American
		$suffixes[01] = '/\b[Ss]r\.\b/'; // Sr. = American
		$suffixes[02] = '/\b[IVX]+\b/i'; // IV, III = English/American
		$suffixes[03] = '/\b[Pp]h\.?d\b/'; // Doctor
		$suffixes[04] = '/\b[Mm]\.?d\b/'; // Masters
		$suffixes[05] = '/\b[Ee]sq\.\b/'; // Esquire

	}

	function check_prefix(){ // need to check if this works with preg_match and function
	$matches = array();
		foreach ($prefixes as $prefix => $poss_prefix){
			if (preg_match ($poss_prefix, $this->old_name, $matches){
				$this->set_prefix(trim($matches[1]));
			}
		}
	}

	function check_prefix_ne_first(){
	$matches = array();
		if ($this->first_name === null){
			foreach ($pre_firsts as $pre_first => $poss_pre_first){
				if (preg_match ($poss_pre_first, $this->pre_last_name, $matches){
					$this->first_name = ucfirst(trim($matches[1]));
					$this->pre_last_name = null;
				}
			}
		}		
	}
	
	function check_suffix($value){	
		foreach ($suffixes as $suffix => $poss_suffix){
			if (preg_match ($poss_suffix, $this->old_name, $matches){
				$this->set_suffix(trim($matches[1]));
			}
		}
	}
	
	function get_name($name_type){
		if ($this->type == 'personal'){
			switch($name_type){
				case "full":
					return ($this->full_name);
					break;
				case "first":
					$this->check_prefix_ne_first();
					return ($this->first_name);
					break;
				case "initials":
					return ($this->initials);
					break;
				case "last":
					if ($this->pre_last_name !== null){
						$temp-name =  $this->pre_last_name.' '.$this->post_last_name;
						return ($temp-name);
					} else {
						return ($this->last_name);
					}
					break;
				case "last+first":
					if ($this->first_name !== null){
						$temp-name = $this->get_name('last').', '.$this->get_name('first');
						$temp-name .= ($this->post_last_name !== null) ? ', '.$this->post_last_name : "";
						return ($temp-name);
					} else{
						$this->get_name('last+initial');
					}
					break;
				case "last+initial":
					if ($this->initials !== null){
						$temp-init = str_split($this->get_name('initials'), 2);
						$temp-name = $this->get_name('last').', '.$temp-init;
						$temp-name .= ($this->post_last_name !== null) ? ', '.$this->post_last_name : "";
						return ($temp-name);
						unset($temp-name);
					} else {
						return ($this->get_name('last'));
					}
					break;
				case "last+initials":
					if ($this->initials !== null){
						$temp-name = $this->get_name('last').', '.$this->get_name('initials');
						$temp-name .= ($this->post_last_name !== null) ? ', '.$this->post_last_name : "";
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
		$type = strtolower(preg_replace('/\s/', '_', $type));
		$this->set_type($type);
/*		$this->old_name			= string();
		$this->first_name		= string();
		$this->initials			= string();
		$this->last_name		= string();
		$this->pre_last_name	= string();
		$this->post_last_name	= string();
		$this->full_name		= string(); */
		if (!isset($type)){
			$this->$type = $name;
		}
	}
	
	function set_prefix($prefix){
		if (!isset($this->pre_last_name)){
			$this->pre_last_name = $prefix;
			$value = ' '.$this->old_name;
			$this->old_name = trim(preg_replace($prefix, '', $value));
		} else {
			$this->pre_last_name .= $prefix;
			$value = ' '.$this->old_name;
			$this->old_name = trim(preg_replace($prefix, '', $value));
		}
	}
	
	function set_suffix($suffix){
		if (!isset($this->post_last_name)){
			$this->post_last_name = $suffix;
			$value = ' '.$this->old_name;
			$this->old_name = trim(preg_replace($suffix, '', $value));
		} else {
			$this->post_last_name .= $suffix;
			$value = ' '.$this->old_name;
			$this->old_name = trim(preg_replace($suffix, '', $value))
		}
	}
	
	function set_type($name_type){
		switch(true){
			case ($name_type == 'aucorp'): // corporate author
				$this->type				= 'corporate';
				// no need to set the subtype as the default is author
				break;
			case (preg_match('/^au/', $name_type) : // author
				// no need to set the type as the default is personal
				// no need to set the subtype as the default is author
				break;
			case (preg_match('/^ed/', $name_type): // editor
				// no need to set the type as the default is personal
				$this->subtype			= 'editor';
				break;
			case (preg_match('/^inv/', $name_type) // inventor
				// no need to set the type as the default is personal
				$this->subtype			= 'inventor';
				break; // then it gets slightly more complicated
			default: // hmmm... "other"
				// use the default type of personal?
				$this->subtype			= 'unknown';
				break;
		}
	}
	
	function split_name(){
		$this->check_prefix();
		$this->check_suffix();
		switch (true){
			case (preg_match(',\s', $this->old_name)):
				$this->temparr = explode(',', $this->old_name);
				$this->last_name = $this->temparr[0];
				$this->old_name = implode(' ', $this->temparr);
				unset($this->temparr);
				$this->split_name();
				break;
			case (preg_match('\s', $this->old_name)):
				$this->temparr = explode(' ', $this->old_name);
				break;
		}
		
		if (isset($this->temparr)){
			$i = (array_count_values($this->temparr) - 1); // arrays normally start with a 0, so adjust count of values accordingly
			if (!isset($name_arr['last_name'])){
				$this->last_name = $this->temparr[$i];
				$this->full_name = $this->temparr[$i].',';
			} 
			foreach ($this->temparr as $item => $name_seg) {
				$i = 1; $i++;
				$this->full_name .= ' ';
				if ($i == 1){
					$this->first_name  	 = $name_seg;
					$this->full_name	.= $name_seg;
					$initials = str_split($name_seg, 1);
					$this->initials 	  = $initials[0].'. ';
				} elseif($i == 2) {
					$this->second_name 	 = $name_seg;
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
?>