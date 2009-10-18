<?php
 /**
  * @file
  * Context object class file
  *
  * $Id: common_funcs.class.php,v 1.0 10/16/2009 5:47 PM tom.pasley Exp $
  */
 
  /**
  * txtckr
  *
  * The base class for an OpenURL context object.
  * 
  * @package txtckr
  * @author Tom Pasley
  * @copyright 	open source: LGPL
  **/
  
  /** 
  * @notes	adapted from [http://q6.oclc.org/openurl/simple_openurl/]
  * _When_
  * ContextObject {ctx_}
  * There seems to be a general feeling on the OpenURL discussion list that the lack of a when-style Entity was an oversight.
  * OTOH, a handful of candidates have emerged in the discussion, but it's difficult to identify any one of them as deserving Entity-level status.

  * 				Identifier By-Value Metadata By-Reference Metadata Private Data
  * 					id             val                 ref              dat
  * ContextObject
  * ctx				ctx_id	

  * _Which_
  * Referer {rfr_}
  * Which database/discovery source was the user using when they invoked the request?

  * 				Identifier By-Value Metadata By-Reference Metadata Private Data
  * 					id             val                 ref              dat
  * Referrer
  * rfr     		rfr_id      rfr_val_fmt        rfr_ref_fmt			rfr_dat
  * 												rfr_ref

  * _Where_
  * Referring Entity {rfe_}
  * Where was the user when they invoked the request?
  * e.g. typically this might be taken from the HTTP 'Referer' header

  * 				Identifier By-Value Metadata By-Reference Metadata Private Data
  * 					id             val                 ref              dat
  * 												
  * ReferringEntity
  * rfe   			rfe_id      rfe_val_fmt        rfe_ref_fmt			rfe_dat
  * 												rfe_ref

  * _Who_
  * Requester {req_}
  * Who originated the request?
  * e.g. could contain information such as an authenticated user ID and authorized roles

  * 				Identifier By-Value Metadata By-Reference Metadata Private Data
  * 					id             val                 ref              dat
  * Requester
  * req				req_id      req_val_fmt        req_ref_fmt			req_dat
  * 												req_ref

  * _What_
  * Referent {rft_}
  * What is the subject of the request?
  * e.g. the Requester is interested in a record in the database

  * 				Identifier By-Value Metadata By-Reference Metadata Private Data
  * 					id             val                 ref              dat
  * Referent         
  * rft  			rft_id      rft_val_fmt        rft_ref_fmt			rft_dat
  * 												rft_ref

  * _Why_
  * ServiceType {svc_}
  * Why is the request being made?
  * e.g. the Requester wants to edit the Referent

  * 				Identifier By-Value Metadata By-Reference Metadata Private Data
  * 					id             val                 ref              dat
  * ServiceType
  * svc			     svc_id     svc_val_fmt         svc_ref_fmt  		svc_dat
  * 												svc_ref

  * _How_
  * Transport {res_}
  * How did the user express the request?
  * This indicates the URL/SOAP structure used to conveny the information above in a web service request.

  * 				Identifier By-Value Metadata By-Reference Metadata Private Data
  * 					id             val                 ref              dat
  * Resolver
  * res				res_id      res_val_fmt        res_ref_fmt			res_dat
  * 												res_ref
  */

require ('common_funcs.class.php');
require ('settings.class.php');
 
class contextobject extends common_functions{

	function __construct(){
		$this->co['logid'] 			= date("YmdHis").rand(00,59);
		$this->co['date'] 			= date("F j, Y, g:i a");
		$this->co['req_type'] 		= '';
		$this->co['ip'] 			= $_SERVER["REMOTE_ADDR"];
		$this->co['browser'] 		= $_SERVER["HTTP_USER_AGENT"];
		$this->request['http_get'] 	= $_SERVER['QUERY_STRING'];
		$this->request['http_post']	= file_get_contents('php://input');
		$this->errors['http_curl']	= '';
		$this->errors['settings']	= '';
		$this->ctx[0]				= 0; 
		$this->req[0]				= 0;
		$this->req['ids']			= array();
		# name attributes now embedded in contextobject
		$this->rfe[0]				= 0;
		$this->rfe['ids']			= array();
		$this->rfr[0]				= 0;
		$this->rfr['ids']			= array();
		$this->rft[0]				= 0;
		$this->rft['ids']			= array();
		$this->svc[0]				= 0;
		$this->settings 			= new settings;
		// $this->names				= '';
		// $this->log				= '';
	}



#### TXTCKA FUNCTIONS : START ####
	/**
	 * public static method
	 *
	 *	contextobject::set_property(params)
	 *
	 * @param	string	
	 * @param	string
	 * @param	string/integer
	 * @return	void
	 * @example	set_property($co, $key, $value) >> $this->$co[$key] = $value
	 * @note	
	 */
	function set_property($co, $key, $value){
	// $this->log .= "\nSetting $this->".$co.'['.$key.']='.$value;
	if (($value !== null) && (!isset($this->$co[$key]))){			// has this key already been set?
		$this->{$co}[$key] = $value;								// if not, then set it
		$this->{$co}[0] = ((count(array_keys($this->{$co})) - 2));	// add to the field count as we go
		}
	}

	/**
	 * public static method
	 *
	 *	contextobject::set_hash(params)
	 *
	 * @param	string	
	 * @param	string
	 * @param	string/integer
	 * @param	integer
	 * @return	void
	 * @example	set_hash($co, $hash_name, $property, $value, $p=1) >> $this->$co[$entitytype][$p] = array($property => $value);
	 * @note	
	 */
	function set_hash($co, $hash_name, $property, $value, $p=1){
	if ((is_array($property)) && ($value === null)) {
		$array = $property;
		foreach($array as $property => $value){
			$this->set_hash($co, $hash_name, $property, $value, $p);
		}
	}
		switch (true){ // trying to set $this->rfe['author'][1]['last_name'] = 'Pasley');
		case (!isset($this->{$co}[$hash_name])):				// there is no $this->rfe['author']
			$this->{$co} 					= array($hash_name => array(0 => $p, $p => array($property => $value)))+array($this->{$co});
			break;
		case (!isset($this->{$co}[$hash_name][$p])):			// there is no $this->rfe['author'][1]
			$this->{$co}[$hash_name]		= array(0 => 1, $p => array($property => $value));
			$this->{$co}[$hash_name][0]		= count(array_keys($this->{$co}[$hash_name]));
			break;		
		case (isset($this->{$co}[$hash_name][$p][$property])):	// $this->rfe['author'][1]['last_name'] is already set
			$p = ($p + 1);
			$this->set_hash($co, $hash_name, $property, $value, $p);
			break;
		case (isset($this->{$co}[$hash_name][$p])):				// $this->rfe['author'][1] is already set, but $this->rfe['author'][1]['last_name'] isn't
			$this->{$co}[$hash_name][$p] 	= array_merge($this->{$co}[$hash_name][$p], array($property => $value));
			break;	
		default:												// $this->rfe['author'][1] is already set, but $this->rfe['author'][1]['last_name'] isn't
			$this->{$co}[$hash_name][$p] 	= array_merge($this->{$co}[$hash_name][$p], array($property => $value));
		}
		// $this->log .= "\nSetting $this->".$co.'['.$hash_name.']['.$p.']['.$property.']='.$value;
		// $this->names .= "\nSetting $this->".$co.'['.$hash_name.']['.$p.']['.$property.']='.$value;
	}
	
	/**
	 * public static method
	 *
	 *	contextobject::set_name(params)
	 *
	 * @param	string	
	 * @param	string
	 * @param	string/integer
	 * @return	void
	 * @example	set_name($co, $name_type, $value)
	 * @note	similar to set_property, except attempting to set each named_entity as a complex property (hierachial array/hash)
	 *   ["rft"]=> // context_segment 
	 *   array(11) {
	 * 	["inventor"]=> //named entity type
	 * 	array(2) {
	 * 	  [0]=>
	 * 	  int(1) // number of this entity types in this array
	 * 	  [1]=>  // then each entity type with it's properties
	 * 	  array(2) {
	 * 		["last_name"]=>
	 * 		string(10) "rftinvlast"
	 * 		["first_name"]=>
	 * 		string(11) "rftinvfirst"
	 */	
	function set_name($co, $name_type, $value){
			$entity_types['advisor'] 		= 'advisor';
			$entity_types['au'] 			= 'author';
			$entity_types['aufull'] 		= 'author';
			$entity_types['aucorp']			= 'author_corp';
			$entity_types['contributor'] 	= 'contributor';
			$entity_types['creator'] 		= 'creator';
			$entity_types['ed']				= 'editor';
			$entity_types['edfull']			= 'editor';
			$entity_types['inv'] 			= 'inventor';
			$entity_types['invfull'] 		= 'inventor';
		switch(true){
			case (isset($entity_types[$name_type])): 		// full names first!
				$hash_name = $entity_types[$name_type];
				$co_entity_r = $co.'_'.$hash_name.'_'.$r;	// using entity in this format as we can explode on '_' this later to get 3 values
				//$this->name_registry[$r] = $co_entity_r;	// add the entity as a value to the registry
				$this->$co_entity = new named_entity;		// create a new object called (the value of entity)
				$this->$co_entity->set_type($name_type);	// set a couple of values based on what we were given
				$this->$co_entity->parse_name($value);		// parse the full name if we were given it
				break;										// stop processing, move on...
			// then it gets slightly more complicated
			// surnames next - should only be one of these per person!
			case (preg_match('/last$/', $name_type)):
				$entitytype = preg_replace('/last$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$hash_name 							= $entity_types[$entitytype]; // e.g. author
				} else {								 // otherwise, we'll use what's left
					$hash_name 							= $entitytype;	// e.g. collaborator
				}
				$property										= 'name_0';
				$this->set_hash($co, $hash_name, $property, $value, 1);
				break;
			case (preg_match('/first$/', $name_type)):
				$entitytype = preg_replace('/first$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$hash_name 								= $entity_types[$entitytype]; // e.g. author
				} else {								 // otherwise, we'll use what's left
					$hash_name 								= $entitytype;	// e.g. collaborator
				}
				$property										= 'name_1';
				$this->set_hash($co, $hash_name, $property, $value, 1);
				break;
			case (preg_match('/init$/', $name_type)):
				$entitytype = preg_replace('/init$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$hash_name 								= $entity_types[$entitytype]; // e.g. author
				} else {								 // otherwise, we'll use what's left
					$hash_name 								= $entitytype;	// e.g. collaborator
				}
				$property										= 'initial_all';
				$this->set_hash($co, $hash_name, $property, $value, 1);
				break;
			case (preg_match('/init1$/', $name_type)):
				$entitytype = preg_replace('/init1$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$hash_name 								= $entity_types[$entitytype]; // e.g. author
				} else {								 // otherwise, we'll use what's left
					$hash_name 								= $entitytype;	// e.g. collaborator
				}
				$property										= 'initial_1';
				$this->set_hash($co, $hash_name, $property, $value, 1);
				break;
			case (preg_match('/initm$/', $name_type)):
				$entitytype = preg_replace('/initm$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$hash_name 								= $entity_types[$entitytype]; // e.g. author
				} else {								 // otherwise, we'll use what's left
					$hash_name 								= $entitytype;	// e.g. collaborator
				}
				$property										= 'initial_2';
				$this->set_hash($co, $hash_name, $property, $value, 1);
				break;
			default:
				$property										= $name_type;
				$this->set_hash($co, $hash_name, $property, $value, 1);
				break;
			}
	}
	
	/**
	 * public static method
	 *
	 *	contextobject::get_last_entity_type(params)
	 *
	 * @param	string	
	 * @param	string
	 * @return	integer
	 * @example	get_last_entity_type($co, $entity_type) >> '1';
	 * @note	
	 */	
	function get_last_entity_type($co, $entity_type){ // need to ensure that count(last_names) == count(first_names) + count(initials);
	$num = 1;
		if (isset($this->{$co}[$entity_type][0])){
			$num = (int($this->{$co}[$entity_type][0]));
			$num = ($num + 1);
		} 
	return ($num);
	}
	

	/**
	 * public static method
	 *
	 *	contextobject::parse_name(params)
	 *
	 * @param	string	
	 * @param	string
	 * @param	string	 
	 * @return	void
	 * @example	parse_name($co, $entity_type, $name) >> '1';
	 * @note	
	 */		
	function parse_name($co, $entity_type, $name){
	if (!isset($this->settings->prefixes[00])){
			$this->settings->define_name_parts();
	}
	$num = $this->get_last_entity_type($co, $entity_type);
		if ($name === null) {
			break;
		}
		$name = preg_replace('/[,]\s+/',' ',trim($name));
		if ($entity_type == 'author_corp'){
			$this->set_hash($co, $entity_type, 'full_name', $name, $num);
			$this->set_hash($co, $entity_type, 'complete', 'constructed', $num);
			break;
		}
		$matches = array();
			foreach ($this->prefixes as $num => $prefix){
				if (preg_match ($prefix, $name, $matches)){
					$this->set_hash($co, $entity_type, 'prefix', $matches[1], $num);
				}
		}
		$matches = array();
			foreach ($this->titles as $num => $title){
				if (preg_match ($title, $name, $matches)){
					$this->set_hash($co, $entity_type, 'title', $matches[1], $num);
				}
			}
		$this->set_hash($co, $entity_type, 'complete', 'constructed', $num);
		if (stristr(', ', $name)){
				$this->temparr = explode(',', $name);
				$last_name = array_shift($this->temparr); // shift the first value off the temparr - this should hopefully be the "last_name"
				$this->set_hash($co, $entity_type, 'last_name', $last_name, $num);
				$name = implode(' ', $this->temparr);
				$this->temparr = array(); // empty the temparr, and start again...
		}
		$this->temparr = explode(' ', $name);
		
		if ((array_count_values($this->temparr)) > 1){ // make sure there's something worth processing
			if (!isset($last_name)){
				$last_name = array_pop($this->temparr); // pop the last value off the temparr - this should be the "last_name"
				$this->set_hash($co, $entity_type, 'last_name', $last_name, $num);
			} 
			$full_name = $last_name.',';
			foreach ($this->temparr as $item => $name_segment) {
				if ($name_segment !== null) {
					$name_segment = str_replace('.', '', $name_segment);
					$nseg_strlen = strlen($name_segment);
					$error = '';
					$i = 1; $i++;
					if (($nseg_strlen > 1) && (isset($this->name_initials[$i])) && (isset($this->name_segments[$i]))){ 
					// it's a name segment, and not just an initial
						$initials = str_split(strtoupper($name_segment), 1);	// create the initial
						$initial	 = $initials[0].'. ';						// set the 1st initials
						$this->set_hash($co, $entity_type, $this->name_initials[$i], $initial, $num);
						$this->set_hash($co, $entity_type, $this->name_segments[$i], $name_segment, $num);
					} elseif (($nseg_strlen = 1) && (isset($this->name_initials[$i]))){
						$initial	 = strtoupper($name_segment).'. ';
						$this->set_hash($co, $entity_type, $this->name_initials[$i], $initial, $num);
					} else {	// error!
						if (!isset($this->name_initials[$i]){
						$error .= '$this->name_initials['$i.'] is not set for '.$name;
						}
						if (!isset($this->name_segments[$i]){
						$error .= '$this->name_segments['$i.'] is not set for '.$name;
						}
						$this->set_hash('errors', 'settings', 'name_parser', $error, 1)
					}
				}
			}
		}
	}
	

	/**
	 * public static method
	 *
	 *	contextobject::set_date(params)
	 *
	 * @param	string	
	 * @param	string	 
	 * @return	void
	 * @example	set_date($co, $value) sets individual properties from one value;
	 * @note	
	 */	
	function set_date($co, $value){
		if ((strlen($value) == 4) && (($year > 1600) && ($year < 2100))){
		$this->set_property($co, 'artyear', (int)$value);
		$this->set_property($co, 'dateisyear', 'false');
		} elseif ((ctype_digit($value)) |(is_numeric($value))){
		$arr = str_split($value, 8);
		$datearr = str_split($arr[0], 2);
		$year 	= @(int)($datearr[0].$datearr[1]);
		$month 	= @(int)($datearr[3]);
		$day	= @(int)($datearr[4]);
		$this->set_property($co, 'year', $year);
		$this->set_property($co, 'month', $month);
		$this->set_property($co, 'day', $day);
		} else {
			if (preg_match('/\//', $value)){
				$newvalue = preg_replace('/\//', '-', $value); 
				$this->set_property($co, 'date', $newvalue);
				$datearr = explode("-", $value);
				foreach ($datearr as $poss => $year) {
				$year = (int) $year;
					if ((strlen($value) == 4) && (($year > 1600) && ($year < 2100))){		// artyear is between 1800 and 2100... 
						$this->set_property($co, 'dateisyear', 'true');
						$this->set_property($co, 'year', $year);
						break;
					} 
				}
			}
		}
	}
	

	/**
	 * public static method
	 *
	 *	contextobject::set_identifier(params)
	 *
	 * @param	string	
	 * @param	string	 
	 * @return	void
	 * @example	set_identifier($co, $identifier) sets individual properties from one value;
	 * @note	
	 */	
	function set_identifier($co, $identifier){
		switch (true) {
			case (preg_match('/^doi[=:\/]/', $identifier)):				// Digital Object Identifier (doi)
				$this->set_doi($co, $identifier);
				break; 
			case (preg_match('/^hdl[=:\/]/', $identifier)):				// Handle (hdl)
				$this->set_handle($co, $identifier);
				break;
			case (preg_match('/^oclcnum[=:\/]|(OCoLC)/', $identifier)):	// OCLC number
				$this->set_oclcnum($co, $identifier);
				break;
			case (preg_match('/^issn[=:\/]/', $identifier)):				// OAI id.
				$newvalue = $this->check_issn($value);
				$this->set_property($co, 'issn', $newvalue);
				break;
			case (preg_match('/^oai[=:\/]/', $identifier)):				// OAI id.
				$this->set_oai($co, $identifier);
				break;
			case (preg_match('/^pmid[=:\/]/', $identifier)):			// PubMed ID (pmid)
				$this->set_pmid($co, $identifier);
				break;
			case (preg_match('/^http:\/\/www.ncbi.nlm.nih.gov\/pubmed\/([0-9]+)/', $identifier)):
				$this->set_pmid($co, $identifier);
				break; 
			case (preg_match('/^http:\/\//', $identifier)):				// http link?
				$this->set_property($co, 'id', $identifier);
				$this->set_property($co, 'idLink', $identifier);
				break;
			case (preg_match('/^www./', $identifier)):					// web link?
				$this->set_property($co, 'id', $identifier);
				$idLink = 'http://'.$identifier;
				$this->set_property($co, 'idLink', $idLink);
				break;	
			default:
				$this->set_property($co, 'id', $identifier);
				break;
		}
	}

# IDENTIFIER HANDLERS -- START
	/**
	 * public static method
	 *
	 *	contextobject::set_doi(params)
	 *
	 * @param	string	
	 * @param	string	 
	 * @return	void
	 * @example	set_doi($co, $doi) sets individual properties from one value;
	 * @note	
	 */		
	function set_doi($co, $doi){
		$doi = preg_replace('doi[=:/]', '', $doi);
		$this->set_property($co, 'doi', $doi);
		$trim = "'+', '.', '. ', ','";						// trim any gunge off the doi
		$doi = rtrim($doi, $trim);
		if ($doi !== null){
			$doiLink = 'http://dx.doi.org/'.$doi;
			$this->set_property($co, 'doiLink', $doiLink);		// human-friendly doi
		}
	}


	/**
	 * public static method
	 *
	 *	contextobject::set_handle(params)
	 *
	 * @param	string	
	 * @param	string	 
	 * @return	void
	 * @example	set_handle($co, $handle) sets individual properties from one value;
	 * @note	
	 */		
	function set_handle($co, $handle){
		$handle = preg_replace('hdl[=:/]', '', $handle);
		$this->set_property($co, 'handle', $handle);
		if ($handle !== null){
			$handleLink = 'http://hdl.handle.net/'.$handle;
			$this->set_property($co, 'handleLink', $handleLink);				// human-friendly url
		}
	}

	/**
	 * public static method
	 *
	 *	contextobject::set_isbn(params)
	 *
	 * @param	string	
	 * @param	string	 
	 * @return	void
	 * @example	set_isbn($co, $isbn) sets individual properties from one value;
	 * @note	
	 */	
	function set_isbn($co, $isbn){
		$find[0] = '[-\s]';	$replace[0] = '';					// remove any hyphens or spaces
		$find[1] = 'isbns';	$replace[1] = '';					// tidy up any gunge
		$find[2] = 'isbn';	$replace[2] = '';					// tidy up any gunge
		$isbn = preg_replace($find, $replace, $value);
		if (strlen($isbn) > 9){
			$this->set_property($co, 'isbn', $isbn); 			// it must be an okay length
		}
	}

	/**
	 * public static method
	 *
	 *	contextobject::set_issn(params)
	 *
	 * @param	string	
	 * @param	string	 
	 * @param	string	 
	 * @return	void
	 * @example	set_issn($co, $issn_type, $issn) sets individual properties from one value;
	 * @note	
	 */		
	function set_issn($co, $issn, $issn_type='print'){
		if (preg_match('/\(/', $issn)){
			@list($issn,$junk) = explode('(', $issn, 2);		// tidy up any gunge
		}	
		$value = (string) trim ($issn); 						// trim any gunge so string length check is fine.
		$value = (string) trim ($value, '+'); 					// ditto
		
		if ((strlen($value) == 9) & (preg_match('/\d\d\d\d-\d\d\d[\dX]/i', $value))){
			$this->set_property($co, $issn_type, $value); 		// it must be an okay length, and have a hype in the middle
		} elseif ((strlen($value) == 8) & (preg_match('/\d\d\d\d\d\d\d[\dX]/i', $value))){
			$arr = str_split($value, 4);						// split into 2 segments of 4 characters
			$issn = $arr[0]."-".$arr[1];						// put a hyphen in the middle
			$this->set_property($co, $issn_type, $issn);		// voila - it's an issn!
		}
	}

	/**
	 * public static method
	 *
	 *	contextobject::set_oai(params)
	 *
	 * @param	string	
	 * @param	string	  
	 * @return	void
	 * @example	set_oai($co, $oai) sets individual properties from one value;
	 * @note	
	 */	
	function set_oai($co, $oai){
		$oai = preg_replace('oai[=:\/]', '', $oai);
		$this->set_property($co, 'oai', $oai);
		if ($oai !== null){
			$oaiLink = 'http://search.yahoo.com/search;_ylt=?p=%22'.$oai.'%22&y=Search&fr=sfp';
			$this->set_property($co, 'oaiLink', $oaiLink);						// OAI search link
		}
	}

	/**
	 * public static method
	 *
	 *	contextobject::set_oclcnum(params)
	 *
	 * @param	string	
	 * @param	string	  
	 * @return	void
	 * @example	set_oclcnum($co, $oclcnum) sets individual properties from one value;
	 * @note	
	 */		
	function set_oclcnum($co, $oclcnum){	
		$oclcnum = preg_replace('oclcnum[=:\/]', '', $oclcnum);
		$this->set_property($co, 'oclcnum', $oclcnum);
		if ($oclcnum !== null){
			$oclcnumLink = 'http://www.worldcat.org/oclc/'.$oclcnum;
			$this->set_property($co, 'oclcnumLink', $oclcnumLink);				// link to WorldCat
		}
	}

	/**
	 * public static method
	 *
	 *	contextobject::set_pmid(params)
	 *
	 * @param	string	
	 * @param	string	  
	 * @return	void
	 * @example	set_pmid($co, $pmid) sets individual properties from one value;
	 * @note	
	 */		
	function set_pmid($co, $pmid){
		$find[0] = 'pmid[=:\/]'; 							$replace[0]= '';
		$find[1] = 'http://www.ncbi.nlm.nih.gov/pubmed/'; 	$replace[1] = '';
		$pmid = preg_replace($find, $replace, $identifier);
		$this->set_property($co, 'pmid', $pmid);
		if ($pmid !== false){
			$pmidLink = 'http://www.ncbi.nlm.nih.gov/pubmed/'.$pmid;
			$this->set_property($co, 'pmidLink', $pmidLink);				// link to PubMed record	
		}
	}

# IDENTIFIER HANDLERS -- FINISH
	/**
	 * public static method
	 *
	 *	contextobject::set_referer(param)
	 *
	 * @param	string	  
	 * @return	void
	 * @example	set_referer($value) sets individual properties from one value;
	 * @note	
	 */		
	function set_referer($value){
		//$details['table'] = 'referers';
		//$details[0] = 'referer_name';
		//$details[1] = 'referer_type';
		//$results = query_db($details);
		//$results = array();
		@list($referer_name, $referer) = explode(':', $value);
		$this->set_property('rfr', 'referer_name', $referer_name);
		$this->set_property('rfr', 'referer_type', $referer);
	}

	
	/**
	 * public static method
	 *
	 *	contextobject::set_contexttype(param)
	 *
	 * @param	string	  
	 * @param	string
	 * @return	void
	 * @example	set_contexttype($co, $type) sets individual properties from one value;
	 * @note	
	 */	
	function set_contexttype($co, $type){
		$this->settings->define_contexttypes();
		switch (true) {
			case ($this->settings->types['key'][$type] != ''): // not sure about this - not tested, but more configurable!
				$this->set_property($co, 'reftype', $this->settings->types['reftype'][$type]);
				$this->set_property($co, 'reqtype', $this->settings->types['reqtype'][$type]);
				$this->set_property($co, 'sourcetype', $this->settings->types['sourcetype'][$type]);
				$this->set_property($co, 'notes', $this->settings->types['notes'][$type]);
				break;
			default:
				$this->set_property($co, 'reftype', 'GEN');
				$this->set_property($co, 'reqtype', 'Unknown');
				$this->set_property($co, 'sourcetype', 'Unknown');
				$this->set_property($co, 'notes', 'This was not identified as an known format in the OpenURL metadata. it was specified as '.$type);
				break;
		}
	}
	

	/**
	 * public static method
	 *
	 *	contextobject::build(param)
	 *
	 * @param	string	  
	 * @param	string
	 * @return	void
	 * @example	build($key, $value) sets individual properties from one value;
	 * @note	
	 */
	function build($key, $value){
	//echo $key.'='.$value.'<br/>';
	$key = $this->unencode($key); 		// remove any spaces - there shouldn't be any in the key names!
	$key = $this->normalise($key);		// then standardise the rest of the key name.
	$key = str_replace('.', '_', $key); // the change any periods "." to underscores "_"
	$value = $this->unencode($value);	// unencode any value, by urldecoding any rawurlencoded strings
	$value = str_replace('info:', '', $value);
		switch(true){
			case (preg_match('/^ctx/', $key)):
				$co = 'ctx';
				$key = str_replace('ctx_', '', $key);
				break;
			case (preg_match('/^rfe/', $key)):
				$co = 'rfe';
				$key = str_replace('rfe_', '', $key);
				break;
			case (preg_match('/^rfr_id$/', $key)):
				$co = 'rfr';
				$find[1] = '/sid:/i';	$replace[1] = '';
				$find[2] = '/sid\//i';	$replace[2] = '';
				$value = preg_replace($find, $replace, $value);
				break;
			case (preg_match('/^rfr/', $key)):
				$co = 'rfr';
				$key = str_replace('rfr_', '', $key);
				break;
			case (preg_match('/^req/', $key)):
				$co = 'req';
				$key = str_replace('req_', '', $key);
				break;
			case (preg_match('/^rft/', $key)):
				$co = 'rft';
				$key = str_replace('rft_', '', $key);
				break;
			default:
				$co = 'rft';
				break;
		}
		
		
		switch ($key) {
			case "advisor": // not sure if this should be parsed as a name, or left as is!
				$this->set_property($co, 'thesis_advisor', $value);
				$this->set_contexttype($co, 'dissertation');
				break;
			case "applcc":
				$this->set_property($co, 'patent_application_country', $value);
				$this->set_contexttype($co, 'patent');
				break;
			case "appldate":
				$this->set_property($co, 'patent_application_date', $value);
				$this->set_contexttype($co, 'patent');
				break;
			case "applnumber":
				$this->set_property($co, 'patent_application_num', $value);
				$this->set_contexttype($co, 'patent');
				break;
			case "applyear":
				$this->set_property($co, 'patent_application_year', $value);
				$this->set_contexttype($co, 'patent');
				break;
			case "assignee":
				$this->set_property($co, 'patent_assignee', $value);
				$this->set_contexttype($co, 'patent');
				break;				
			case "au":
				$this->set_name($co, 'au', $value);
				break;
			case "aufull":
				$this->set_name($co, 'au', $value);
				break;
			case "aucorp":
				$this->set_name($co, 'aucorp', $value);
				break;				
			case "aufirst":
				$this->set_name($co, 'aufirst', $value);
				break;
			case "auinit":
				$this->set_name($co, 'auinit', $value);
				break;
			case "auinit1":
				$this->set_name($co, 'auinit1', $value);
				break;
			case "auinitm":
				$this->set_name($co, 'auinitm', $value);
				break;
			case "aulast":
				$this->set_name($co, 'aulast', $value);
				break;	
			case "btitle":
				$this->set_property($co, 'title', $value);
				$this->set_contexttype($co, 'book');
				break;
			case "coden":
				$this->set_identifier($co, 'coden', $value);
				break;
			case "contributor":
				$this->set_name($co, 'contributor', $value);
				break;
			case "creator":
				$this->set_name($co, 'creator', $value);
				break;
			case "ctx_ver":
				$newvalue = strtoupper($value);
				if ($newvalue == "Z39.88-2004") {
				$this->set_property('ctx', 'openurl', '1.0');
				} else {
				$this->set_property('ctx', 'openurl', '0.1');
				}
				break;
			case "date":
				$this->set_date($co, $value);
				break;
			case "degree":
				$this->set_property($co, 'thesis_type', $value);
				$this->set_contexttype($co, 'dissertation');
				break;
			case "ed":
				$this->set_name($co, 'ed', $value);
				break;
			case "edfirst":
				$this->set_name($co, 'edfirst', $value);
				break;
			case "edfull":
				$this->set_name($co, 'ed', $value);
				break;				
			case "edinit":
				$this->set_name($co, 'edinit', $value);
				break;
			case "edlast":
				$this->set_name($co, 'edlast', $value);
				break;				
			case "eissn":
				$this->set_issn($co, 'eissn', $value);
				break;
			case "format":
				$this->set_contexttype($co, $value);
				break;
			case "genre":
				$this->set_property($co, 'genre', $value);
				$this->set_contexttype($co, $value);
				break;
			case "id":
				$newvalue = str_replace ('\s', '', $value);
				$this->set_identifier($co, $newvalue);
				break;
			case "inv":
				$this->set_name($co, 'inv', $value);
				break;
			case "invfirst":
				$this->set_name($co, 'invfirst', $value);
				break;
			case "invinit":
				$this->set_name($co, 'invinit', $value);
				break;
			case "invlast":
				$this->set_name($co, 'invlast', $value);
				break;
			case "isbn":
				$this->set_isbn($co, $value);
				break;
			case "issn":
				$this->set_issn($co, 'issn', $value);
				break;
			case "jtitle":
				$this->set_property($co, 'title', $value);
				$this->set_contexttype($co, 'article');
				break;
			case "kind":
				$this->set_property($co, 'patent_kind', $value);
				$this->set_contexttype($co, 'patent');
				break;		
			case "oclcnum":
				$this->set_oclcnum($co, $value);
				break;	
			case "prioritydate":
				$this->set_property($co, 'patent_priority_date', $value);
				$this->set_contexttype($co, 'patent');
				break;
			case "rfr_id":
				$newvalue = preg_replace('/sid[:][\/]/i', '', $value);
				$this->set_referer($newvalue);
				break;
			case "rft_val_fmt":
				$value = str_replace('info:', '', $value);
				if (preg_match('/ofi\/fmt:xml:xsd/', $value)){
						$type = str_replace('ofi/fmt:xml:xsd', '', $value);
						$this->set_contexttype($co, $type);
						$this->set_property($co, 'format', $type);
						$this->set_property($co, 'metaformat', 'XML');
				} else{
						$type = str_replace('ofi/fmt:kev:mtx:', '', $value);
						$this->set_contexttype($co, $type);
						$this->set_property($co, 'format', $type);
						$this->set_property($co, 'metaformat', 'KEV');
				}
				break;
			case "sici":
				$this->set_sici($co, $value);
				break;
			case "sid":
				$this->set_referer($value);
				break;
			case "title":
				$newvalue = trim($value, "\"");
				$this->set_property($co, 'title', $newvalue);
				break;
			case "url_ver":
				$newvalue = strtoupper($value);
				if ($newvalue == "Z39.88-2004") {
				$this->set_property($co, 'openurl', '1.0');
				} else {
				$this->set_property($co, 'openurl', '0.1');
				}
				break;
			case "url_ctx_val":
				$newvalue = str_replace('info:', '', rawurldecode($value));
				$this->set_identifier($newvalue);
				break;
			default:
				$this->translate_openurl($co, $key, $value);
				break;
		}
	}

	
	/**
	 * public static method
	 *
	 *	contextobject::build()
	 *
	 * @return	void
	 * @example	build_from_querystring sets individual properties from one value;
	 * @note	
	 */	
	function build_from_querystring() {
		$this->build_from_string($this->request['http_get']);
	}

	
	/**
	 * public static method
	 *
	 *	contextobject::build_from_string(param)
	 *
	 * @param	string	 
	 * @return	void
	 * @example	build_from_string sets individual properties from one value;
	 * @note	
	 */		
	function build_from_string($string) {
		$pairs = explode('&', $string);								// split on & into KV pairs
		echo $pairs;
		# loop through each pair
		foreach ($pairs as $values) {
			# split into key and value
			list($key,$value) = explode('=', $values, 2);
			// $key = (str_replace('[%20][\s]', '', $key));
			$this->build($key, $value);
		}
		$this->settings->undefine_contexttypes(); // free up some memory
	}


	/**
	 * public static method
	 *
	 *	contextobject::check_rft_dates()
	 *	 
	 * @return	void
	 * @note	checks on the dates hopefully set as part of the incoming request
	 */		
	function check_rft_dates(){ 
		if (isset ($this->rft['year'])){
			$date = $this->rft['year'];
			if (isset($this->rft['month'])){
				$date .= $this->rft['month'];
			} else {
				$date .= '01';
			}
			if (isset($this->rft['day'])){
				$date .= $this->rft['day'];
			} else {
				$date .= '01';
			}
			if (!isset($this->rft['date'])){
				$this->set_property($co, 'date', $date);
			}
		}

		if ((!isset($this->rft['artyear'])) | (!isset($this->rft['dateisyear'])) | (isset($this->rft['artyear']))){
			$this->set_property($co, 'artyear', date("Y"));
			$this->set_property($co, 'dateisyear', 'false');
		}
	}

	/**
	 * public static method
	 *
	 *	contextobject::translate_openurl(params)
	 *
	 * @param	string	 
	 * @param	string
	 * @param	string	
	 * @return	void
	 * @example	'aucorp' >> 'author_corporate'
	 * @note	translate_openurl sets individual properties for unparsed key
	 */		
	function translate_openurl($co, $key, $value){
		$this->settings->define_openurl_keys();		
		if (isset($openurl['key'][$key])){
			$newkey = $openurl['key'][$key];
		} else {
			$newkey = $this->normalise($key);
		}
		$this->set_property($co, $newkey, $value);
		$this->settings->undefine_openurl_keys(); // free up some resources
	}
###################### TXTCKA FUNCTIONS : FINISH ######################
}
?>