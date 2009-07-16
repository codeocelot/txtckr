<?php
/**
 * This is the base class for an OpenURL context object.
 *
 * @author		Tom Pasley
 * @date		13/07/2009
 * @last mod	16/07/2009
 * @package 	txtckr
 * @copyright 	open source
 */
 
class contextobject(){

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
		//var $$key;
		if ($value !== false){
		switch($co){
			case "ctx":
				$this->ctx[$key] = $value;
				break;
			case "rfe":
				$this->rfe[$key] = $value;
				break;
			case "rfr":
				$this->rfr[$key] = $value;
				break;
			case "req":
				$this->req[$key] = $value;
				break;
			case "rft":
				$this->rft[$key] = $value;
				break;
			default:
				break;
		}
		
		}
	}

	function set_date($co, $value){
		if ((strlen($value) == 4) && (($year > 1600) && ($year < 2100))) {
		set_property($co, 'artyear', (int)$value);
		set_property($co, 'dateisyear', 'false');
		} elseif ((ctype_digit($value)) |(is_numeric($value)) ) {
		$arr = str_split($value, 8);
		$datearr = str_split($arr[0], 2);
		$year 	= @(int)($datearr[0].$datearr[1]);
		$month 	= @(int)($datearr[3]);
		$day	= @(int)($datearr[4]);
		set_property($co, 'year', $year);
		set_property($co, 'month', $month);
		set_property($co, 'day', $day);
		} else {
			if (preg_match('/\//', $value)){
				$newvalue = preg_replace('/\//', '-', $value); 
				set_property($co, 'date', $newvalue);
				$datearr = explode("-", $value);
				foreach ($datearr as $poss => $year) {
				$year = (int) $year;
					if ((strlen($value) == 4) && (($year > 1600) && ($year < 2100))) {		// artyear is between 1800 and 2100... 
				set_property($co, 'dateisyear', 'true');
				set_property($co, 'year', $year);
				break;
					} 
				}
			}
		}
	}
	
	function set_identifier($co, $identifier){
		switch (true) {
			case (preg_match('/^doi[=:\/]/', $identifier)):					// Digital Object Identifier (doi)
				$doi = str_replace('doi[=:/]', '', $identifier);
				set_property($co, 'doi', $doi);
				$trim = "'+', '.', '. ', ','";								// trim any gunge off the doi
				$identifier = rtrim($identifier, $trim);
				if ($doi !== false){
					$doiLink = 'http://dx.doi.org/'.$doi;
					set_property($co, 'doiLink', $doiLink);						// human-friendly doi
				}
				break; 
			case (preg_match('/^hdl[=:\/]/', $identifier)):					// Handle (hdl)
				$handle = str_replace('hdl[=:/]', '', $identifier);
				set_property($co, 'handle', $identifier);
				if ($handle !== false){
					$handleLink = 'http://hdl.handle.net/'.$handle);
					set_property($co, 'handleLink', $handleLink);					// human-friendly url
				}
				break;
			case (preg_match('/^pmid[=:\/]/', $identifier)):				// PubMed ID (pmid)
				$pmid = str_replace('pmid[=:\/]', '', $identifier)
				set_property($co, 'pmid', $pmid);
				if ($pmid !== false){
					$pmidLink = 'http://www.ncbi.nlm.nih.gov/pubmed/'.$pmid;
					set_property($co, 'pmidLink', $pmidLink);						// link to PubMed record	
				}
				break; 
			case (preg_match('/^oclcnum[=:\/]|(OCoLC)/', $identifier)):					// OCLC number
				$oclcnum = str_replace('oclcnum[=:\/]', '', $identifier);
				set_property($co, 'oclcnum', $oclcnum);
				if ($oclcnum !== false){
					$oclcnumLink = 'http://www.worldcat.org/oclc/'.$oclcnum;
					set_property($co, 'oclcnumLink', $oclcnumLink);						// link to WorldCat
				}
				break;
			case (preg_match('/^oai[=:\/]/', $identifier)):					// OAI id.
				$oai = str_replace('oai[=:\/]', '', $identifier);
				set_property($co, 'oai', $oai);
				if ($oai !== false){
					$oaiLink = 'http://search.yahoo.com/search;_ylt=?p=%22'.$oai.'%22&y=Search&fr=sfp';
					set_property($co, 'oaiLink', $oaiLink);						// OAI search link
				}
				break;
			case (preg_match('/^http:\/\/www.ncbi.nlm.nih.gov\/pubmed\/([0-9]+)/', $identifier)):
				$pmid = str_replace('http://www.ncbi.nlm.nih.gov/pubmed/', '', $identifier)
				set_property($co, 'pmid', $pmid);
				if ($pmid !== false){
					$pmidLink = 'http://www.ncbi.nlm.nih.gov/pubmed/'.$pmid;
					set_property($co, 'pmidLink', $pmidLink);						// link to PubMed record	
				}
				break; 
			case (preg_match('/^http:\/\//', $identifier)):					// info link?
				set_property($co, 'id', $identifier);
				set_property($co, 'idLink', $identifier);
				break;
			case (preg_match('/^www./', $identifier)):						// web link?
				set_property($co, 'id', $identifier);
				$idLink = 'http://'.$identifier
				set_property($co, 'idLink', $idLink);
				break;	
			default:
				set_property($co, 'id', $identifier);
				break;
		}
	}
	
	function set_referer($value){
		$details['table'] = 'referers';
		$details[0] = 'referer_name';
		$details[1] = 'referer_type';
		$results = query_db($details);
		set_property($co, 'referer_name', $results['referer_name']);
		set_property($co, 'referer_type', $results['referer_type']);
	}
	
	function set_rft_reftype($type){
		switch (strtolower($type)) {
			case ($type == "article"):
				set_property('rft', 'reftype', 'JOUR');		// article: a document published in a journal.
				set_property('rft', 'reqtype', 'Journal Article');
				set_property('rft', 'sourcetype', 'Journal');
				break;
			case ($type == "book"):
				set_property('rft', 'reftype', 'BOOK');	// book: a publication that is complete in one part or a designated finite number of parts, often identified with an ISBN.
				set_property('rft', 'reqtype', 'Book');
				set_property('rft', 'sourcetype', 'Book');
				break;
			case ($type == "bookitem"):
				set_property('rft', 'reftype', 'CHAP');	// bookitem: a defined section of a book, usually with a separate title or number.
				set_property('rft', 'reqtype', 'Book Chapter');
				set_property('rft', 'sourcetype', 'Book');
				break;
			case ($type == "conference"):		// conference: a record of a conference that includes one or more conference papers and that is published as an issue of a journal or serial publication 
				set_property('rft', 'reftype', 'JFULL');
				set_property('rft', 'reqtype', 'Conference Item');
				set_property('rft', 'sourcetype', 'Conference');
				set_property('rft', 'notes', 'This was identified as a "collection of conference presentations published as an issue of a serial publication" in the OpenURL metadata.');
				break;
			case ($type == "dissertation"):
				set_property('rft', 'reftype', 'THES');
				set_property('rft', 'reqtype', 'Dissertation');
				set_property('rft', 'sourcetype', 'Thesis/Dissertation');
				break;
			case ($type == "document"):		// document: general document type to be used when available data elements do not allow determination of a more specific document type, i.e. when one has only author and title but no publication information. 
				set_property('rft', 'reftype', 'GEN');
				set_property('rft', 'reqtype', 'Unknown');
				set_property('rft', 'sourcetype', 'Unknown');
				set_property('rft', 'notes', 'This was identified as a "general document type" in the OpenURL metadata.');
				break;
			case ($type == "issue"):
				set_property('rft', 'reftype', 'JFULL');	// issue: one instance of the serial publication
				set_property('rft', 'reqtype', 'Journal/Serial Issue');
				set_property('rft', 'sourcetype', 'Journal');
				set_property('rft', 'notes', 'This was identified as a "single issue of a serial publication" in the OpenURL metadata.');
				break;
			case ($type == "journal"):
				set_property('rft', 'reftype', 'JFULL');	// journal: a serial publication issued in successive parts
				set_property('rft', 'reqtype', 'Journal/Serial Publication');
				set_property('rft', 'sourcetype', 'Journal');
				set_property('rft', 'notes', 'This was identified as a "serial publication" in the OpenURL metadata.');
				break;
			case ($type == "patent"):
				set_property('rft', 'reftype', 'PAT');
				set_property('rft', 'reqtype', 'Patent');
				set_property('rft', 'sourcetype', 'Patent');	
				break;
			case ($type == "proceeding"):		// proceeding: a single conference presentation published in a journal or serial publication 
				set_property('rft', 'reftype', 'JOUR');
				set_property('rft', 'reqtype', 'Conference Proceedings');
				set_property('rft', 'sourcetype', 'Conference');
				set_property('rft', 'notes', 'This was identified as a "single conference presentation in a serial publication" in the OpenURL metadata.');
				break;
			case ($type == "preprint"):		// preprint: an individual paper or report published in paper or electronically prior to its publication in a journal or serial.
				set_property('rft', 'reftype', 'JOUR');
				set_property('rft', 'reqtype', 'Journal Article Preprint');
				set_property('rft', 'sourcetype', 'Journal');
				set_property('rft', 'notes', 'This was identified as an "individual paper or report published in paper or electronically prior to its publication" in a journal or serial in the OpenURL metadata.');
				break;
			case ($type == "report"):		// report: report or technical report is a published document that is issued by an organization, agency or government body
				set_property('rft', 'reftype', 'RPRT');
				set_property('rft', 'reqtype', 'Report');
				set_property('rft', 'sourcetype', 'Report');
				break;
			case ($type == "unknown"):
				set_property('rft', 'reftype', 'GEN');
				set_property('rft', 'reqtype', 'Unknown');
				set_property('rft', 'sourcetype', 'Unknown');
				set_property('rft', 'notes', 'This was identified as an "unknown format" in the OpenURL metadata.');
				break;
		}
	}

	function build($key, $value){
		switch(true){
			case (preg_match('/ctx_/', $key)):
				$co = 'ctx';
				$key = preg_replace('/ctx_/', '', $key);
				$value = rawurldecode(preg_replace('/info:/', '', $value));
				break;
			case (preg_match('/rfe_/', $key)):
				$co = 'rfe';
				$key = preg_replace('/rfe_/', '', $key);
				$value = rawurldecode(preg_replace('/info:/', '', $value));
				break;
			case (preg_match('/rfr_/', $key)):
				$co = 'rfr';
				$key = preg_replace('/rfr_/', '', $key);
				$value = rawurldecode(preg_replace('/info:/', '', $value));
				break;
			case (preg_match('/req_/', $key)):
				$co = 'req';
				$key = preg_replace('/req_/', '', $key);
				$value = rawurldecode(preg_replace('/info:/', '', $value));
				break;
			case (preg_match('/rft_/', $key)):
				$co = 'rft';
				$key = preg_replace('/rft_/', '', $key);
				$value = rawurldecode(preg_replace('/info:/', '', $value));
				break;
			default:
				break;
		}
		
		
	switch (true) {
			case ($key == "advisor"):
				set_property($co, 'thesis_advisor', $value);
				break;
			case ($key == "applcc"):
				set_property($co, 'patent_application_country', $value);
				break;
			case ($key == "appldate"):
				set_property($co, 'patent_application_date', $value);
				break;
			case ($key == "applnumber"):
				set_property($co, 'patent_application_num', $value);
				set_reftype('patent');
				break;
			case ($key == "applyear"):
				set_property($co, 'patent_application_year', $value);
				set_reftype('patent');
				break;
			case ($key == "artnum"):
				set_property($co, 'article_number', $value);
				break;
			case ($key == "assignee"):
				set_property($co, 'patent_assignee', $value);
				set_reftype('patent');
				break;				
			case ($key == "atitle"):
				set_property($co, 'atitle', $value);
				break;
			case ($key == "au"):
				set_creator('rft', 'au', $value);
				break;
			case ($key == "aufirst"):
				set_creator('rft', 'aufirst', $value);
				break;
			case ($key == "auinit"):
				set_creator('rft', 'auinit', $value);
				break;
			case ($key == "aulast"):
				set_creator('rft', 'aulast', $value);
				break;	
			case ($key == "btitle"):
				set_property($co, 'title', $value);
				set_reftype('book');
				break;
			case ($key == "cc"):
				set_property($co, 'country_code', $value);
				break;
			case ($key == "co"):
				set_property($co, 'country', $value);
				break;
			case ($key == "coden"):
				set_identifier('rft', 'coden', $value);
				break;
			case ($key == "contributor"):
				set_property($co, 'contributor', $value);
				break;
			case ($key == "coverage"):
				set_property($co, 'coverage', $value);
				break;
			case ($key == "creator"):
				set_creator('rft', 'creator', $value);
				break;
			case ($key == "ctx_ver"):
				$newvalue = strtoupper($value);
				if ($newvalue == "Z39.88-2004") {
				set_property('ctx', 'openurl', '1.0');
				} else {
				set_property('ctx', 'openurl', '0.1');
				}
				break;
			case ($key == "date"):
				set_date($co, $value);
				break;
			case ($key == "degree"):
				set_property($co, 'thesis_type', $value);
				set_reftype('dissertation');
				break;
			case ($key == "description"):
				set_property($co, 'description', $value);
				set_reftype('description');
				break;
			case ($key == "edition"):
				set_property($co, 'edition', $newvalue);
				break;
			case ($key == "eissn"):
				$newvalue = checkISSN($value);
				set_property($co, 'dateisyear', $newvalue);
				break;
			case ($key == "genre"):
				set_property($co, 'genre', $value);
				set_reftype($value);
				break;
			case ($key == "id"):
				$newvalue = str_replace ('\s', '', $value);
				set_identifier($newvalue);
				break;
			case ($key == "inst"):
				set_property($co, 'thesis_inst', $value);
				break;
			case ($key == "isbn"):
				$find[0] = '[-\s]';	$replace[0] = '';					//remove any hyphens or spaces
				$find[1] = 'isbns';	$replace[1] = '';					// tidy up any gunge
				$find[2] = 'isbn';	$replace[2] = '';					// tidy up any gunge
				$isbn = str_replace($find, $replace, $value);
				if (strlen($isbn) > 9){
				set_property($co, 'isbn', $isbn); 					// it must be an okay length
				}
				break;
			case ($key == "issn"):
				set_property($co, 'issn', checkISSN($value));
				break;
			case ($key == "jtitle"):
				set_property($co, 'title', $value);
				set_reftype('article');
				break;
			case ($key == "kind"):
				set_property($co, 'patent_stage', $value);
				break;
			case ($key == "oclcnum"):
				set_property($co, 'oclcnum', $value);
				set_property($co, 'oclc_link', 'http://worldcat.org/oclc/'.$value);
				break;
			case ($key == "prioritydate"):
				set_property($co, 'patt_priority_date', $value);
				break;
			case ($key == "pub"):
				set_property($co, 'publisher', $value);
				break;
			case ($key == "pubdate"):
				set_property($co, 'published', $value);
				break;
			case ($key == "quarter");
				set_property($co, 'quarter', $value);
				break;				
			case ($key == "sid"):
				set_referer($value);
				break;
			case ($key == "ssn"):
				set_property($co, 'season', $value);
				break;
			case ($key == "stitle"):
				set_property($co, 'abbrev_title', $value);
				break;
			case ($key == "title"):
				$newvalue = trim($value, "\"");
				set_property($co, 'title', $newvalue);
				break;
			case ($key == "tpages"):
				set_property($co, 'num_pages', $value);
				break;
			case ($key == "url_ver"):
				$newvalue = strtoupper($value);
				if ($newvalue == "Z39.88-2004") {
				set_property($co, 'openurl', '1.0');
				} else {
				set_property($co, 'openurl', '0.1');
				}
				break;
			case ($key == "val_fmt"):
				if (preg_match('/ofi\/fmt:xml:xsd/', $value)){
						$newvalue = str_replace('ofi/fmt:xml:xsd', '', $value);
						set_reftype($newvalue);
						set_property($co, 'metaformat', 'XML');
				} else{
						$newvalue = str_replace('ofi/fmt:kev:mtx:', '', $value);
						set_reftype($newvalue);
						set_property($co, 'metaformat', 'KEV');
				}
				break;
			case ($key == "url_ctx_val"):
				$newvalue = str_replace('rft_id=info:', '', rawurldecode($value));
				set_identifier($newvalue);
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
		set_property($co, 'date', $date);
		}
		}

		if ((!isset($this->rft['artyear'])) | (!isset($this->rft['dateisyear'])) | (isset($this->rft['artyear']))){
		set_property($co, 'artyear', date("Y"));
		set_property($co, 'dateisyear', 'false');
		}
	}

}
?>