<?php
/**
 * This is the base class for an OpenURL context object.
 *
 * @author		Tom Pasley
 * @date		13/07/2009
 * @last mod	18/09/2009
 * @package 	txtckr
 * @copyright 	open source
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
		$this->ctx[0]				= 0; // these replace name_registry
		$this->req[0]				= 0;
		$this->rfe[0]				= 0;
		$this->rfr[0]				= 0;
		$this->rft[0]				= 0;
		$this->rft_ids[0]			= 0;
		$this->svc[0]				= 0;
		# name attributes now embedded in contextobject
		$this->name_registry[0]		= 0; // used to store the last number used in this array
		$this->name_partials[0]		= 0; // used to store the last number used in this array 
		$this->name_check[0]		= 0; // used to store the last number used in this array
		$this->first_name[0]		= 0;
		$this->initial_1[0]			= 0;
		$this->initial_m[0]			= 0;
		$this->initials[0]			= 0;
		$this->last_name[0]			= 0;
		$this->pre_last_name[0]		= 0; // e.g. 'van der' 	as in Willem van der Weerden
		$this->post_last_name[0]	= 0; // e.g. 'III'		as in William Gates III
		$this->full_name[0]			= 0;
		$this->corp_name[0]			= 0;
		$this->settings				= new settings;		
	}

/* adapted from [http://q6.oclc.org/openurl/simple_openurl/]
_When_
ContextObject {ctx_}
There seems to be a general feeling on the OpenURL discussion list that the lack of a when-style Entity was an oversight.
OTOH, a handful of candidates have emerged in the discussion, but it's difficult to identify any one of them as deserving Entity-level status.

				Identifier By-Value Metadata By-Reference Metadata Private Data
					id             val                 ref              dat
ContextObject
ctx				ctx_id	

_Which_
Referer {rfr_}
Which database/discovery source was the user using when they invoked the request?

				Identifier By-Value Metadata By-Reference Metadata Private Data
					id             val                 ref              dat
Referrer
rfr     		rfr_id      rfr_val_fmt        rfr_ref_fmt			rfr_dat
												rfr_ref

_Where_
Referring Entity {rfe_}
Where was the user when they invoked the request?
e.g. typically this might be taken from the HTTP 'Referer' header

				Identifier By-Value Metadata By-Reference Metadata Private Data
					id             val                 ref              dat
												
ReferringEntity
rfe   			rfe_id      rfe_val_fmt        rfe_ref_fmt			rfe_dat
												rfe_ref

_Who_
Requester {req_}
Who originated the request?
e.g. could contain information such as an authenticated user ID and authorized roles

				Identifier By-Value Metadata By-Reference Metadata Private Data
					id             val                 ref              dat
Requester
req				req_id      req_val_fmt        req_ref_fmt			req_dat
												req_ref

_What_
Referent {rft_}
What is the subject of the request?
e.g. the Requester is interested in a record in the database

				Identifier By-Value Metadata By-Reference Metadata Private Data
					id             val                 ref              dat
Referent         
rft  			rft_id      rft_val_fmt        rft_ref_fmt			rft_dat
												rft_ref

_Why_
ServiceType {svc_}
Why is the request being made?
e.g. the Requester wants to edit the Referent

				Identifier By-Value Metadata By-Reference Metadata Private Data
					id             val                 ref              dat
ServiceType
svc			     svc_id     svc_val_fmt         svc_ref_fmt  		svc_dat
												svc_ref

_How_
Transport {res_}
How did the user express the request?
This indicates the URL/SOAP structure used to conveny the information above in a web service request.

				Identifier By-Value Metadata By-Reference Metadata Private Data
					id             val                 ref              dat
Resolver
res				res_id      res_val_fmt        res_ref_fmt			res_dat
												res_ref
*/

###################### TXTCKA FUNCTIONS : START ######################	
	function set_property($co, $key, $value){
	// echo '<b>'.$co.'</b>:'.$key.'='.$value.'<br/>';
	if (($value !== null) && (!isset($this->ctx[$key]))){		// has this key already been set?
		$this->{$co}[$key] = $value;								// if not, then set it
		$this->{$co}[0] = count(array_keys($this->$co));			// add to the field count as we go
		}
	}

	function get_name_registry(){
		return array($this->name_registry);
	}
	
	/*
	Trying to:
	a) create a registry, which lists all name parts collected, in the order parsed

	b) either:
		 i) create a named entity, when provided with a full name
		ii) stored the name parts for later creation of a named entity as:
				$this->name_partials[0] 			= 3;
				$this->name_partials[1]				= 'rfe_aufirst'.'_'.1;
				$this->name_partials[2] 			= 'rfe_aulast'.'_'.1;
				
				$this->first_name[0]				= 3;
				$this->first_name['rfe_au'][1] 		= 'Tom';
				$this->first_name['rfe_au'][2] 		= 'Alison';
				
				$this->last_name[0]					= 2;
				$this->last_name['rfe_au'][1]		= 'Pasley';

	c) create a named entity for each 'last_name' collected, together with suitable 'first_name' or 'initials', etc.
	
	*/	
	
	function set_name($co, $name_type, $value){
			$entity_types['au'] 			= 'author';
			$entity_types['aufull'] 		= 'author';
			$entity_types['aucorp']			= 'author_corp';
			$entity_types['ed']				= 'editor';
			$entity_types['edfull']			= 'editor';
			$entity_types['inv'] 			= 'inventor';
			$entity_types['invfull'] 		= 'inventor';
			$entity_types['contributor'] 	= 'contributor';
			$entity_types['creator'] 		= 'creator';
		$r = count(array_keys($this->name_registry));		// use name_registry to store last used number
		$p = count(array_keys($this->name_partials));		// use name_partials to store last used number [0] is reserved for count
		$co_entity = $co.'_'.$name_type;					// register what came in which order, useful for name fragments.
		switch(true){
			case (isset($entity_types[$name_type])): 		// full names first!
				$entity_type = $entity_types[$name_type];
				$co_entity_r = $co.'_'.$entity_type.'_'.$r;	// using entity in this format as we can explode on '_' this later to get 3 values
				$this->name_registry[$r] = $co_entity_r;	// add the entity as a value to the registry
				$this->$co_entity = new named_entity;		// create a new object called (the value of entity)
				$this->$co_entity->set_type($name_type);	// set a couple of values based on what we were given
				$this->$co_entity->parse_name($value);		// parse the full name if we were given it
				break;										// stop processing, move on...
			// then it gets slightly more complicated
			// surnames next - should only be one of these per person!
			case (preg_match('/last$/', $name_type)):
				$entitytype = preg_replace('/last$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$entity_type 							= $co.'_'.$entity_types[$entitytype]; // e.g. rfe_author
				} else {								 // otherwise, we'll use what's left
					$entity_type 							= $co.'_'.$entitytype;	// e.g. rfe_collaborator
				}
				$this->name_check[$p]						= $entity_type;	// last names need to be checked
				$this->name_registry[$r] 					= $entity_type.'_'.$r ;
				$this->last_name[0]							= count(array_keys($this->last_name));
				$name_count			 						= $this->last_name[0];
				$this->last_name[$entitytype][$name_count]	= $value;
				break;
			case (preg_match('/first$/', $name_type)):
				$entitytype = preg_replace('/last$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$entity_type 								= $co.'_'.$entity_types[$entitytype]; // e.g. rfe_author
				} else {								 // otherwise, we'll use what's left
					$entity_type 								= $co.'_'.$entitytype;	// e.g. rfe_collaborator
				}
				$this->first_name[0]							= count(array_keys($this->first_name));
				$name_count										= $this->first_name[0];
				$this->first_name[$entitytype][$name_count]	= $value;	
				break;
			case (preg_match('/init$/', $name_type)):
				$entitytype = preg_replace('/last$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$entity_type 								= $co.'_'.$entity_types[$entitytype]; // e.g. rfe_author
				} else {								 // otherwise, we'll use what's left
					$entity_type 								= $co.'_'.$entitytype;	// e.g. rfe_collaborator
				}
				$this->initials[0]								= count(array_keys($this->initials));
				$name_count										= $this->initials[0];
				$this->initials[$entitytype][$name_count]		= $value;
				break;
			case (preg_match('/init1$/', $name_type)):
				$entitytype = preg_replace('/last$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$entity_type 								= $co.'_'.$entity_types[$entitytype]; // e.g. rfe_author
				} else {								 // otherwise, we'll use what's left
					$entity_type 								= $co.'_'.$entitytype;	// e.g. rfe_collaborator
				}
				$this->initial_1[0]								= count(array_keys($this->initial_1));
				$name_count										= $this->initial_1[0];
				$this->initial_1[$entitytype][$name_count]		= $value;
				break;
			case (preg_match('/initm$/', $name_type)):
				$entitytype = preg_replace('/last$/', '', $name_type);
				if (isset($entity_types[$entitytype])){ // should be author, editor, etc.
					$entity_type 								= $co.'_'.$entity_types[$entitytype]; // e.g. rfe_author
				} else {								 // otherwise, we'll use what's left
					$entity_type 								= $co.'_'.$entitytype;	// e.g. rfe_collaborator
				}
				$this->initial_m[0]								= count(array_keys($this->initial_m));
				$name_count										= $this->initial_m[0];
				$this->initial_m[$entitytype][$name_count]		= $value;
				break;
			default:
				$co_entity 										= $co.'_'.$name_type.'_'.$p;
				$$co_entity[$p]									= $value;
				if (!isset($this->$co_entity[0])){
					$this->$co_entity[0] 						= 0;
				}
				$this->$co_entity[0]							= count(array_keys($this->$co_entity));
				$name_count										= $this->$co_entity[0];
				$this->$co_entity[$entitytype][$name_count]	= $value;		
				break;
			}
			$this->name_partials[$p] = $co_entity.':'.$name_count;
	}
	
	
	function check_name_registry(){ // need to ensure that count(last_names) == count(first_names) + count(initials);
	$check_names = (array_keys(array_flatten(array_values($this->$co_entity))));
		$registry = $this->get_name_registry();
		foreach ($registry as $num => $name) {
			list($co_entity, $order) = explode($name, ':');
			if (is_array($this->$co['authors'])){ // add the sorted names here somehow
			$this->$co['authors'] = array_merge($entity, $this->$$co['authors']);		
			} else {
			$this->$co['authors'] = $$entity;
			}
		}
	}
	
	function get_names(){
		$registry = $this->get_name_registry();
		foreach ($registry as $num => $name) {
			list($co, $entity, $order) = explode($name, '_');
			$$entity[$order] = $name;
			if (is_array($this->$$co['authors'])){ // add the sorted names here somehow
			$this->$co['authors'] = array_merge($$entity, $this->$$co['authors']);		
			} else {
			$this->$co['authors'] = $$entity;
			}
		}	
	}

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
	
	function set_doi($co, $doi){
		$doi = str_replace('doi[=:/]', '', $doi);
		$this->set_property($co, 'doi', $doi);
		$trim = "'+', '.', '. ', ','";						// trim any gunge off the doi
		$doi = rtrim($doi, $trim);
		if ($doi !== null){
			$doiLink = 'http://dx.doi.org/'.$doi;
			$this->set_property($co, 'doiLink', $doiLink);		// human-friendly doi
		}
		if ($co == 'rft'){
			$this->rft['id_count']++;
		}
	}

	function set_handle($co, $handle){
		$handle = str_replace('hdl[=:/]', '', $handle);
		$this->set_property($co, 'handle', $handle);
		if ($handle !== null){
			$handleLink = 'http://hdl.handle.net/'.$handle;
			$this->set_property($co, 'handleLink', $handleLink);				// human-friendly url
		}
		if ($co == 'rft'){
			$this->rft['id_count']++;
		}
	}

	function set_isbn($co, $isbn){
		$find[0] = '[-\s]';	$replace[0] = '';					// remove any hyphens or spaces
		$find[1] = 'isbns';	$replace[1] = '';					// tidy up any gunge
		$find[2] = 'isbn';	$replace[2] = '';					// tidy up any gunge
		$isbn = str_replace($find, $replace, $value);
		if (strlen($isbn) > 9){
			$this->set_property($co, 'isbn', $isbn); 			// it must be an okay length
		}
		if ($co == 'rft'){
			$this->rft['id_count']++;
		}
	}

	function set_issn($co, $issn_type, $issn){
		if (preg_match('/\(/', $issn)){
			@list($issn,$junk) = explode('(', $issn, 2);		// tidy up any gunge
		}	
		$value = (string) trim ($issn); 						// trim any gunge so string length check is fine.
		$value = (string) trim ($value, '+'); 					// ditto
		
		if ((strlen($value) == 9) & (preg_match('/\d\d\d\d-\d\d\d[\dX]/i', $value))){
			$this->set_property($co, $issn_type, $value); 		// it must be an okay length, and have a hype in the middle
			if ($co == 'rft'){
				$this->rft['id_count']++;
			}
		} elseif ((strlen($value) == 8) & (preg_match('/\d\d\d\d\d\d\d[\dX]/i', $value))){
			$arr = str_split($value, 4);						// split into 2 segments of 4 characters
			$issn = $arr[0]."-".$arr[1];						// put a hyphen in the middle
			$this->set_property($co, $issn_type, $issn);		// voila - it's an issn!
			if ($co == 'rft'){
				$this->rft['id_count']++;
			}
		}
	}
	
	function set_oai($co, $oai){
		$oai = str_replace('oai[=:\/]', '', $oai);
		$this->set_property($co, 'oai', $oai);
		if ($oai !== null){
			$oaiLink = 'http://search.yahoo.com/search;_ylt=?p=%22'.$oai.'%22&y=Search&fr=sfp';
			$this->set_property($co, 'oaiLink', $oaiLink);						// OAI search link
		}
		if ($co == 'rft'){
			$this->rft['id_count']++;
		}		
	}
	
	function set_oclcnum($co, $oclcnum){	
		$oclcnum = str_replace('oclcnum[=:\/]', '', $oclcnum);
		$this->set_property($co, 'oclcnum', $oclcnum);
		if ($oclcnum !== null){
			$oclcnumLink = 'http://www.worldcat.org/oclc/'.$oclcnum;
			$this->set_property($co, 'oclcnumLink', $oclcnumLink);				// link to WorldCat
		}
		if ($co == 'rft'){
			$this->rft['id_count']++;
		}
	}

	function set_pmid($co, $pmid){
		$find[0] = 'pmid[=:\/]'; 							$replace[0]= '';
		$find[1] = 'http://www.ncbi.nlm.nih.gov/pubmed/'; 	$replace[1] = '';
		$pmid = str_replace($find, $replace, $identifier);
		$this->set_property($co, 'pmid', $pmid);
		if ($pmid !== false){
			$pmidLink = 'http://www.ncbi.nlm.nih.gov/pubmed/'.$pmid;
			$this->set_property($co, 'pmidLink', $pmidLink);				// link to PubMed record	
		}
		if ($co == 'rft'){
			$this->rft['id_count']++;
		}
	}

# IDENTIFIER HANDLERS -- FINISH
	
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

	function set_contexttype($co, $type){ // not working!!!
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
	
	function build($key, $value){
	//echo $key.'='.$value.'<br/>';
	$key = $this->unencode($key); 		// remove any spaces - there shouldn't be any in the key names!
	$key = $this->normalise($key);		// then standardise the rest of the key name.
	$value = $this->unencode($value);	// unencode any value, by urldecoding any rawurlencoded strings
	$value = str_replace('info:', '', $value);
		switch(true){
			case (preg_match('/^ctx\./', $key)):
				$co = 'ctx';
				$key = str_replace('ctx_', '', $key);
				break;
			case (preg_match('/^rfe\./', $key)):
				$co = 'rfe';
				$key = str_replace('rfe_', '', $key);
				break;
			case (preg_match('/^rfr_id$/', $key)):
				$co = 'rfr';
				$find[1] = '/sid:/i';	$replace[1] = '';
				$find[2] = '/sid\//i';	$replace[2] = '';
				$value = preg_replace($find, $replace, $value);
				break;
			case (preg_match('/^rfr\./', $key)):
				$co = 'rfr';
				$key = str_replace('rfr.', '', $key);
				break;
			case (preg_match('/^req\./', $key)):
				$co = 'req';
				$key = str_replace('req.', '', $key);
				break;
			case (preg_match('/^rft\./', $key)):
				$co = 'rft';
				$key = str_replace('rft.', '', $key);
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
				$newvalue = str_replace('rft_id=info:', '', rawurldecode($value));
				$this->set_identifier($newvalue);
				break;
			default:
				$this->translate_openurl($co, $key, $value);
				break;
		}
	}

	function build_from_querystring() {
		$this->build_from_string($this->request['http_get']);
	}

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