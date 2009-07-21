<?php
/**
 * This is the base class for an OpenURL context object.
 *
 * @author		Tom Pasley
 * @date		13/07/2009
 * @last mod	21/07/2009
 * @package 	txtckr
 * @copyright 	open source
 */
 
class contextobject{

	function __construct(){
		$this->co['logid'] 			= date("YmdHis").rand(00,59);
		$this->co['date'] 			= date("F j, Y, g:i a");
		$this->co['ip'] 			= $_SERVER["REMOTE_ADDR"];
		$this->co['browser'] 		= $_SERVER["HTTP_USER_AGENT"];
		$this->co['req_type'] 		= '';
		$this->ctx['field_count']	= 0;
		$this->req['field_count']	= 0;
		$this->rfe['field_count']	= 0;	
		$this->rfr['field_count']	= 0;
		$this->rft['field_count']	= 0;
		$this->svc['field_count']	= 0;
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

	function set_property($co, $key, $value){
	// echo '<b>'.$co.'</b>:'.$key.'='.$value.'<br/>';
	if ($value !== null){
			switch($co){
				case "ctx":
					if (!isset($this->ctx[$key])){	// has this key already been set?
					$this->ctx[$key] = $value;		// if not, then set it
					$this->ctx['field_count']++;	// add to the field count as we go
					}
					break;
				case "rfe":
					$this->rfe[$key] = $value;
					$this->rfe['field_count']++;
					break;
				case "rfr":
					$this->rfr[$key] = $value;
					$this->rfr['field_count']++;
					break;
				case "req":
					$this->req[$key] = $value;
					$this->req['field_count']++;
					break;
				case "rft":
					$this->rft[$key] = $value;
					$this->rft['field_count']++;
					return('set');
					break;
				default:
					break;
			}
		}
	}

	function set_date($co, $value){
		if ((strlen($value) == 4) && (($year > 1600) && ($year < 2100))) {
		$this->set_property($co, 'artyear', (int)$value);
		$this->set_property($co, 'dateisyear', 'false');
		} elseif ((ctype_digit($value)) |(is_numeric($value)) ) {
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
					if ((strlen($value) == 4) && (($year > 1600) && ($year < 2100))) {		// artyear is between 1800 and 2100... 
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
			case (preg_match('/^pmid[=:\/]/', $identifier)):			// PubMed ID (pmid)
				$this->set_pmid($co, $identifier);
				break; 
			case (preg_match('/^oclcnum[=:\/]|(OCoLC)/', $identifier)):				// OCLC number
				$this->set_oclcnum($co, $identifier);
				break;
			case (preg_match('/^oai[=:\/]/', $identifier)):				// OAI id.
				$this->set_oai($co, $identifier);
				break;
			case (preg_match('/^http:\/\/www.ncbi.nlm.nih.gov\/pubmed\/([0-9]+)/', $identifier)):
				$this->set_pmid($co, $identifier);
				break; 
			case (preg_match('/^http:\/\//', $identifier)):				// info link?
				$this->set_property($co, 'id', $identifier);
				$this->set_property($co, 'idLink', $identifier);
				break;
			case (preg_match('/^www./', $identifier)):				// web link?
				$this->set_property($co, 'id', $identifier);
				$idLink = 'http://'.$identifier;
				$this->set_property($co, 'idLink', $idLink);
				break;	
			default:
				$this->set_property($co, 'id', $identifier);
				break;
		}
	}
	
	function set_doi($co, $doi){
		$doi = str_replace('doi[=:/]', '', $doi);
		$this->set_property($co, 'doi', $doi);
		$trim = "'+', '.', '. ', ','";						// trim any gunge off the doi
		$doi = rtrim($doi, $trim);
		if ($doi !== false){
			$doiLink = 'http://dx.doi.org/'.$doi;
			$this->set_property($co, 'doiLink', $doiLink);		// human-friendly doi
		}
	}

	function set_handle($co, $handle){
		$handle = str_replace('hdl[=:/]', '', $handle);
		$this->set_property($co, 'handle', $handle);
		if ($handle !== false){
			$handleLink = 'http://hdl.handle.net/'.$handle;
			$this->set_property($co, 'handleLink', $handleLink);				// human-friendly url
		}
	}

	function set_oai($co, $oai){
		$oai = str_replace('oai[=:\/]', '', $oai);
		$this->set_property($co, 'oai', $oai);
		if ($oai !== false){
			$oaiLink = 'http://search.yahoo.com/search;_ylt=?p=%22'.$oai.'%22&y=Search&fr=sfp';
			$this->set_property($co, 'oaiLink', $oaiLink);				// OAI search link
		}	
	}
	
	function set_oclcnum($co, $oclcnum){	
		$oclcnum = str_replace('oclcnum[=:\/]', '', $oclcnum);
		$this->set_property($co, 'oclcnum', $oclcnum);
		if ($oclcnum !== false){
			$oclcnumLink = 'http://www.worldcat.org/oclc/'.$oclcnum;
			$this->set_property($co, 'oclcnumLink', $oclcnumLink);				// link to WorldCat
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
	}
	
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

	function set_reftype($co, $type){
		switch (strtolower($type)) {
			case ($type == "article"):
				$this->set_property($co, 'reftype', 'JOUR');		// article: a document published in a journal.
				$this->set_property($co, 'reqtype', 'Journal Article');
				$this->set_property($co, 'sourcetype', 'Journal');
				break;
			case ($type == "book"):
				$this->set_property($co, 'reftype', 'BOOK');	// book: a publication that is complete in one part or a designated finite number of parts, often identified with an ISBN.
				$this->set_property($co, 'reqtype', 'Book');
				$this->set_property($co, 'sourcetype', 'Book');
				break;
			case ($type == "bookitem"):
				$this->set_property($co, 'reftype', 'CHAP');	// bookitem: a defined section of a book, usually with a separate title or number.
				$this->set_property($co, 'reqtype', 'Book Chapter');
				$this->set_property($co, 'sourcetype', 'Book');
				break;
			case ($type == "conference"):		// conference: a record of a conference that includes one or more conference papers and that is published as an issue of a journal or serial publication 
				$this->set_property($co, 'reftype', 'JFULL');
				$this->set_property($co, 'reqtype', 'Conference Item');
				$this->set_property($co, 'sourcetype', 'Conference');
				$this->set_property($co, 'notes', 'This was identified as a "collection of conference presentations published as an issue of a serial publication" in the OpenURL metadata.');
				break;
			case ($type == "dissertation"):
				$this->set_property($co, 'reftype', 'THES');
				$this->set_property($co, 'reqtype', 'Dissertation');
				$this->set_property($co, 'sourcetype', 'Thesis/Dissertation');
				break;
			case ($type == "document"):		// document: general document type to be used when available data elements do not allow determination of a more specific document type, i.e. when one has only author and title but no publication information. 
				$this->set_property($co, 'reftype', 'GEN');
				$this->set_property($co, 'reqtype', 'Unknown');
				$this->set_property($co, 'sourcetype', 'Unknown');
				$this->set_property($co, 'notes', 'This was identified as a "general document type" in the OpenURL metadata.');
				break;
			case ($type == "issue"):
				$this->set_property($co, 'reftype', 'JFULL');	// issue: one instance of the serial publication
				$this->set_property($co, 'reqtype', 'Journal/Serial Issue');
				$this->set_property($co, 'sourcetype', 'Journal');
				$this->set_property($co, 'notes', 'This was identified as a "single issue of a serial publication" in the OpenURL metadata.');
				break;
			case ($type == "journal"):
				$this->set_property($co, 'reftype', 'JFULL');	// journal: a serial publication issued in successive parts
				$this->set_property($co, 'reqtype', 'Journal/Serial Publication');
				$this->set_property($co, 'sourcetype', 'Journal');
				$this->set_property($co, 'notes', 'This was identified as a "serial publication" in the OpenURL metadata.');
				break;
			case ($type == "patent"):
				$this->set_property($co, 'reftype', 'PAT');
				$this->set_property($co, 'reqtype', 'Patent');
				$this->set_property($co, 'sourcetype', 'Patent');	
				break;
			case ($type == "proceeding"):		// proceeding: a single conference presentation published in a journal or serial publication 
				$this->set_property($co, 'reftype', 'JOUR');
				$this->set_property($co, 'reqtype', 'Conference Proceedings');
				$this->set_property($co, 'sourcetype', 'Conference');
				$this->set_property($co, 'notes', 'This was identified as a "single conference presentation in a serial publication" in the OpenURL metadata.');
				break;
			case ($type == "preprint"):		// preprint: an individual paper or report published in paper or electronically prior to its publication in a journal or serial.
				$this->set_property($co, 'reftype', 'JOUR');
				$this->set_property($co, 'reqtype', 'Journal Article Preprint');
				$this->set_property($co, 'sourcetype', 'Journal');
				$this->set_property($co, 'notes', 'This was identified as an "individual paper or report published in paper or electronically prior to its publication" in a journal or serial in the OpenURL metadata.');
				break;
			case ($type == "report"):		// report: report or technical report is a published document that is issued by an organization, agency or government body
				$this->set_property($co, 'reftype', 'RPRT');
				$this->set_property($co, 'reqtype', 'Report');
				$this->set_property($co, 'sourcetype', 'Report');
				break;
			case ($type == "unknown"):
				$this->set_property($co, 'reftype', 'GEN');
				$this->set_property($co, 'reqtype', 'Unknown');
				$this->set_property($co, 'sourcetype', 'Unknown');
				$this->set_property($co, 'notes', 'This was identified as an "unknown format" in the OpenURL metadata.');
				break;
		}
	}
	
	function build($key, $value){
	//echo $key.'='.$value.'<br/>';
	$key = str_replace('.', '_', $key);
		switch(true){
			case (preg_match('/ctx_/', $key)):
				$co = 'ctx';
				$key = str_replace('ctx_', '', $key);
				$value = str_replace('info:', '', urldecode(rawurldecode($value)));
				break;
			case (preg_match('/rfe_/', $key)):
				$co = 'rfe';
				$key = str_replace('rfe_', '', $key);
				$value = str_replace('info:', '', urldecode(rawurldecode($value)));
				break;
			case (preg_match('/rfr_id/', $key)):
				$co = 'rfr';
				$value = str_replace('info:', '', urldecode(rawurldecode($value)));
				break;
			case (preg_match('/rfr_/', $key)):
				$co = 'rfr';
				$key = str_replace('rfr_', '', $key);
				$value = str_replace('info:', '', urldecode(rawurldecode($value)));
				break;
			case (preg_match('/req_/', $key)):
				$co = 'req';
				$key = str_replace('req_', '', $key);
				$value = str_replace('info:', '', urldecode(rawurldecode($value)));
				break;
			case (preg_match('/rft_/', $key)):
				$co = 'rft';
				$key = str_replace('rft_', '', $key);
				$value = str_replace('info:', '', urldecode(rawurldecode($value)));
				break;
			default:
				$co = 'rft';
				$value = urldecode(rawurldecode($value));
				break;
		}
		
		
		switch (true) {
			case ($key == "advisor"):
				$this->set_property($co, 'thesis_advisor', $value);
				break;
			case ($key == "applcc"):
				$this->set_property($co, 'patent_application_country', $value);
				break;
			case ($key == "appldate"):
				$this->set_property($co, 'patent_application_date', $value);
				break;
			case ($key == "applnumber"):
				$this->set_property($co, 'patent_application_num', $value);
				$this->set_reftype($co, 'patent');
				break;
			case ($key == "applyear"):
				$this->set_property($co, 'patent_application_year', $value);
				$this->set_reftype($co, 'patent');
				break;
			case ($key == "artnum"):
				$this->set_property($co, 'article_number', $value);
				break;
			case ($key == "assignee"):
				$this->set_property($co, 'patent_assignee', $value);
				$this->set_reftype($co, 'patent');
				break;				
			case ($key == "atitle"):
				$this->set_property($co, 'atitle', $value);
				break;
			case ($key == "au"):
				$this->set_creator($co, 'au', $value);
				break;
			case ($key == "aufirst"):
				$this->set_creator($co, 'aufirst', $value);
				break;
			case ($key == "auinit"):
				$this->set_creator($co, 'auinit', $value);
				break;
			case ($key == "aulast"):
				$this->set_creator($co, 'aulast', $value);
				break;	
			case ($key == "btitle"):
				$this->set_property($co, 'title', $value);
				$this->set_reftype($co, $co, 'book');
				break;
			case ($key == "cc"):
				$this->set_property($co, 'country_code', $value);
				break;
			case ($key == "co"):
				$this->set_property($co, 'country', $value);
				break;
			case ($key == "coden"):
				$this->set_identifier($co, 'coden', $value);
				break;
			case ($key == "contributor"):
				$this->set_property($co, 'contributor', $value);
				break;
			case ($key == "coverage"):
				$this->set_property($co, 'coverage', $value);
				break;
			case ($key == "creator"):
				$this->set_creator($co, 'creator', $value);
				break;
			case ($key == "ctx_ver"):
				$newvalue = strtoupper($value);
				if ($newvalue == "Z39.88-2004") {
				$this->set_property('ctx', 'openurl', '1.0');
				} else {
				$this->set_property('ctx', 'openurl', '0.1');
				}
				break;
			case ($key == "date"):
				$this->set_date($co, $value);
				break;
			case ($key == "degree"):
				$this->set_property($co, 'thesis_type', $value);
				$this->set_reftype($co, 'dissertation');
				break;
			case ($key == "description"):
				$this->set_property($co, 'description', $value);
				break;
			case ($key == "edition"):
				$this->set_property($co, 'edition', $newvalue);
				break;
			case ($key == "eissn"):
				$newvalue = $this->checkISSN($value)
				$this->set_property($co, 'dateisyear', $newvalue);
				break;
			case ($key == "epage"):
				$this->set_property($co, 'end_page', $value);
				break;
			case ($key == "format"):
				$this->set_reftype($co, $value);
				break;
			case ($key == "genre"):
				$this->set_property($co, 'genre', $value);
				$this->set_reftype($co, $value);
				break;
			case ($key == "id"):
				$newvalue = str_replace ('\s', '', $value);
				$this->set_identifier($co, $newvalue);
				break;
			case ($key == "inst"):
				$this->set_property($co, 'institution', $value);
				break;
			case ($key == "inv"):
				$this->set_creator($co, 'inv', $value);
				break;
			case ($key == "invfirst"):
				$this->set_creator($co, 'invfirst', $value);
				break;
			case ($key == "invinit"):
				$this->set_creator($co, 'invinit', $value);
				break;
			case ($key == "invlast"):
				$this->set_creator($co, 'invlast', $value);
				break;
			case ($key == "isbn"):
				$find[0] = '[-\s]';	$replace[0] = '';					//remove any hyphens or spaces
				$find[1] = 'isbns';	$replace[1] = '';					// tidy up any gunge
				$find[2] = 'isbn';	$replace[2] = '';					// tidy up any gunge
				$isbn = str_replace($find, $replace, $value);
				if (strlen($isbn) > 9){
				$this->set_property($co, 'isbn', $isbn); 					// it must be an okay length
				}
				break;
			case ($key == "issn"):
				$newvalue = $this->checkISSN($value)
				$this->set_property($co, 'issn', $newvalue);
				break;
			case ($key == "issue"):
				$this->set_property($co, 'issue', $value);
				break;
			case ($key == "jtitle"):
				$this->set_property($co, 'title', $value);
				$this->set_reftype($co, 'article');
				break;
			case ($key == "kind"):
				$this->set_property($co, 'patent_stage', $value);
				break;
			case ($key == "oclcnum"):
				$this->set_oclcnum($co, $value);
				break;
			case ($key == "prioritydate"):
				$this->set_property($co, 'patt_priority_date', $value);
				break;
			case ($key == "pub"):
				$this->set_property($co, 'publisher', $value);
				break;
			case ($key == "pubdate"):
				$this->set_property($co, 'published', $value);
				break;
			case ($key == "publisher"):
				$this->set_property($co, 'publisher', $value);
				break;
			case ($key == "quarter");
				$this->set_property($co, 'quarter', $value);
				break;
			case ($key == "rfr_id"):
				$this->set_referer(str_replace('sid', '', $value));
				break;
			case ($key == "series"):
				$this->set_property($co, 'series_title', $value);
				break;
			case ($key == "sici"):
				$this->set_sici($co, $value);
				break;
			case ($key == "sid"):
				$this->set_referer($value);
				break;
			case ($key == "ssn"):
				$this->set_property($co, 'season', $value);
				break;
			case ($key == "stitle"):
				$this->set_property($co, 'abbreviated_title', $value);
				break;
			case ($key == "subject"):
				$this->set_property($co, 'subject', $value);
				break;
			case ($key == "title"):
				$newvalue = trim($value, "\"");
				$this->set_property($co, 'title', $newvalue);
				break;
			case ($key == "tpages"):
				$this->set_property($co, 'total_pages', $value);
				break;
			case ($key == "type"):
				$this->set_property($co, 'type', $value);
				break;
			case ($key == "url_ver"):
				$newvalue = strtoupper($value);
				if ($newvalue == "Z39.88-2004") {
				$this->set_property($co, 'openurl', '1.0');
				} else {
				$this->set_property($co, 'openurl', '0.1');
				}
				break;
			case ($key == "val_fmt"):
				if (preg_match('/ofi\/fmt:xml:xsd/', $value)){
						$newvalue = str_replace('ofi/fmt:xml:xsd', '', $value);
						$this->set_reftype($co, $co, $newvalue);
						$this->set_property($co, 'metaformat', 'XML');
				} else{
						$newvalue = str_replace('ofi/fmt:kev:mtx:', '', $value);
						$this->set_reftype($co, $co, $newvalue);
						$this->set_property($co, 'metaformat', 'KEV');
				}
				break;
			case ($key == "url_ctx_val"):
				$newvalue = str_replace('rft_id=info:', '', rawurldecode($value));
				$this->set_identifier($newvalue);
				break;
		}

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

}
?>