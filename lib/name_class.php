<?php
/**
 * These are the base classes for dealing with named objects.
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
				$temparr = $this->split_name($value);
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
	
	function check_prefix($value){
		switch (true){
			case (stristr(strtolower($value), ' van de ')): // Dutch
			case (stristr(strtolower($value), ' van den ')): // Dutch
			case (stristr(strtolower($value), ' van der ')): // Dutch
			case (stristr(strtolower($value), ' van ')): // Dutch
			case (stristr(strtolower($value), ' von ')): // German
			case (stristr(strtolower($value), ' dela ')): // French/Italian?
			case (stristr(strtolower($value), ' de la ')): // French
			case (stristr(strtolower($value), ' de ')): // Dutch/French?
			case (stristr(strtolower($value), ' des ')): // French
			case (stristr(strtolower($value), ' di ')): // Italian
			case (stristr(strtolower($value), ' du ')): // French
			case (stristr(strtolower($value), ' af ')): // Swedish
			case (stristr(strtolower($value), ' bin ')): // Arabic
			case (stristr(strtolower($value), ' ben ')): // Hebrew
			case (stristr(strtolower($value), ' ibn ')): // Arabic
			case (stristr(strtolower($value), ' uyt den ')): // Dutch
			case (stristr(strtolower($value), ' uyt der ')): // Dutch
			case (stristr(strtolower($value), ' ten ')): // Dutch
			case (stristr(strtolower($value), ' ter ')): // Dutch
			case (stristr(strtolower($value), ' het ')): // Dutch?
			case (stristr(strtolower($value), ' ab ')): // Welsh
			case (stristr(strtolower($value), ' ap ')): // Welsh
			case (stristr(strtolower($value), ' st. ')): // English/French?
	}
	
	function check_suffix($value){
		switch (true){
			case (stristr(strtolower($value), ' jr.')): // American
			case (stristr(strtolower($value), ' sr.')): // American
			case (preg_match(strtolower($value), '\s[ivx][ivx][ivx]')): // American
			// check the preg_match syntax for these:
			case (preg_match(strtolower($value), '\sph\.?d')) // Doctor
			case (preg_match(strtolower($value), '\sm\.?d')) // Masters
			case (preg_match(strtolower($value), '\sesq\.')) // Esquire
	}
	
	function split_name($value){
		$this->check_prefix($value);
		$this->check_suffix($value);
		$name_arr['old_name'] = $value;
		switch (true){
			case (preg_match(',', $name_arr['old_name'])):
				$temparr = explode(',', $name_arr['old_name']);
				$name_arr['last_name'] = $temparr[0];
				$value = implode(' ', $temparr);
				unset($temp_arr);
				$this->split_name($value);
				break;
			case (preg_match('\s', $name_arr['old_name'])):
				$this->temparr = explode(' ', $name_arr['old_name']);
				break;
		}
		
		if (isset($this->temparr)){
			$i = array_count_values($this->temparr);
			if (!isset($name_arr['last_name'])){
				$name_arr['last_name'] = $this->temparr[$i];
				$name_arr['full_name'] = $this->temparr[$i].',';
			} 
			foreach ($this->temparr as $item => $name_seg) {
				$i = 1; $i++;
				$name_arr['full_name'] .= ' ';
				if ($i == 1){
					$name_arr['first_name']  = $name_seg;
					$name_arr['full_name']	.= $name_seg;
					$initials = str_split($name_seg, 1);
					$name_arr['initials'] 	 = $initials[0].'. ';
				} elseif($i == 2) {
					$name_arr['second_name'] = $name_seg;
					$name_arr['full_name'] 	.= $name_seg;
					$initials = str_split($name_seg, 1);
					$name_arr['initials'] 	.= $initials[0].'. ';
				} else {
					$name_arr['full_name']	.= $name_seg;
					$initials = str_split($name_seg, 1);
					$name_arr['initials'] 	.= $initials[0].'. ';
				}
			}
		}
		return ($name_arr);
	}
	
	function check_name_registry(){ // need to ensure that count(last_names) == count(first_names) + count(initials);
		$registry = $this->get_name_registry();
	}
	
}

class named_entity(){

	function __construct(){
		$this->type			= 'personal';
		$this->subtype		= 'author';
		$this->first_name	= string();
		$this->initials		= string();
		$this->last_name	= string();
		$this->full_name	= string();
		$this->complete		= 'incomplete';
	}


	function get_name($name_type){
		if ($this->type == 'personal'){
			switch($name_type){
				case "full":
					return ($this->full_name);
					break;
				case "first":
					return ($this->first_name);
					break;
				case "initials":
					return ($this->initials);
					break;
				case "last":
					return ($this->last_name);
					break;
				case "last+first":
					if (isset($this->first_name)){
						$temp-name = $this->last_name.', '.$this->first_name;
						return ($temp-name);
						unset($temp-name);
					} else{
						$this->get_name('last+initial');
					}
					break;
				case "last+initial":
					if (isset($this->initials)){
						$temp-init = str_split($this->initials, 2);
						$temp-name = $this->last_name.', '.$temp-init;
						return ($temp-name);
						unset($temp-name);
					} else {
						return ($this->last_name);
					}
					break;
				case "last+initials":
					if (isset($this->initials)){
						$temp-name = $this->last_name.', '.$this->initials
						return ($temp-name);
						unset($temp-name);
					} else {
						return ($this->last_name);
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
	
	}
}
?>