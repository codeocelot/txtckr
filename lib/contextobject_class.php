<?php
/**
 * This is the base class for an OpenURL context object.
 *
 * @author		Tom Pasley
 * @date		13/07/2009
 * @last mod	05/08/2009
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
		$this->ctx[0]				= 0;
		$this->req[0]				= 0;
		$this->rfe[0]				= 0;	
		$this->rfr[0]				= 0;
		$this->rft[0]				= 0;
		$this->rft_ids[0]			= 0;
		$this->svc[0]				= 0;
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
					if (!isset($this->ctx[$key])){		// has this key already been set?
						$this->ctx[$key] = $value;		// if not, then set it
						$this->ctx[0]++;				// add to the field count as we go
					}
					break;
				case "rfe":
					if (!isset($this->rfe[$key])){
						$this->rfe[$key] = $value;
						$this->rfe[0]++;
					}
					break;
				case "rfr":
					if (!isset($this->rfr[$key])){
						$this->rfr[$key] = $value;
						$this->rfr[0]++;
					}
					break;
				case "req":
					if (!isset($this->req[$key])){
						$this->req[$key] = $value;
						$this->req[0]++;
					}
					break;
				case "rft":
					if (!isset($this->rft[$key])){
						$this->rft[$key] = $value;
						$this->rft[0]++;
					}
					break;
				default:
					break;
			}
		}
	}

	function set_creator($co, $creator_type, $value){
		switch($creator_type){
			case "au":
				break;
			case "aucorp":
				break;
			case "aufirst":
				break;
			case "auinit":
				break;
			case "aulast":
				break;
			case "inv":
				break;
			case "invfirst":
				break;
			case "invinit":
				break;
			case "invlast":
				break;	
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
				$newvalue = $this->checkISSN($value)
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
		$find[0] = '[-\s]';	$replace[0] = '';					//remove any hyphens or spaces
		$find[1] = 'isbns';	$replace[1] = '';					// tidy up any gunge
		$find[2] = 'isbn';	$replace[2] = '';					// tidy up any gunge
		$isbn = str_replace($find, $replace, $value);
		if (strlen($isbn) > 9){
			$this->set_property($co, 'isbn', $isbn); 				// it must be an okay length
		}
		if ($co == 'rft'){
			$this->rft['id_count']++;
		}
	}

	function set_issn($co, $issn_type, $issn){
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
		
		
		switch ($key) {
			case "advisor":
				$this->set_property($co, 'thesis_advisor', $value);
				$this->set_reftype($co, 'dissertation');
				break;
			case "applcc":
				$this->set_property($co, 'patent_application_country', $value);
				$this->set_reftype($co, 'patent');
				break;
			case "appldate":
				$this->set_property($co, 'patent_application_date', $value);
				$this->set_reftype($co, 'patent');
				break;
			case "applnumber":
				$this->set_property($co, 'patent_application_num', $value);
				$this->set_reftype($co, 'patent');
				break;
			case "applyear":
				$this->set_property($co, 'patent_application_year', $value);
				$this->set_reftype($co, 'patent');
				break;
			case "assignee":
				$this->set_property($co, 'patent_assignee', $value);
				$this->set_reftype($co, 'patent');
				break;				
			case "au":
				$this->set_creator($co, 'au', $value);
				break;
			case "aucorp":
				$this->set_creator($co, 'aucorp', $value);
				break;				
			case "aufirst":
				$this->set_creator($co, 'aufirst', $value);
				break;
			case "auinit":
				$this->set_creator($co, 'auinit', $value);
				break;
			case "aulast":
				$this->set_creator($co, 'aulast', $value);
				break;	
			case "btitle":
				$this->set_property($co, 'title', $value);
				$this->set_reftype($co, $co, 'book');
				break;
			case "coden":
				$this->set_identifier($co, 'coden', $value);
				break;
			case "contributor":
				$this->set_creator($co, 'contributor', $value);
				break;
			case "creator":
				$this->set_creator($co, 'creator', $value);
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
				$this->set_reftype($co, 'dissertation');
				break;
			case "eissn":
				$this->set_issn($co, 'eissn', $value);
				break;
			case "format":
				$this->set_reftype($co, $value);
				break;
			case "genre":
				$this->set_property($co, 'genre', $value);
				$this->set_reftype($co, $value);
				break;
			case "id":
				$newvalue = str_replace ('\s', '', $value);
				$this->set_identifier($co, $newvalue);
				break;
			case "inv":
				$this->set_creator($co, 'inv', $value);
				break;
			case "invfirst":
				$this->set_creator($co, 'invfirst', $value);
				break;
			case "invinit":
				$this->set_creator($co, 'invinit', $value);
				break;
			case "invlast":
				$this->set_creator($co, 'invlast', $value);
				break;
			case "isbn":
				$this->set_isbn($co, $value);
				break;
			case "issn":
				$this->set_issn($co, 'issn', $value);
				break;
			case "jtitle":
				$this->set_property($co, 'title', $value);
				$this->set_reftype($co, 'article');
				break;
			case "kind":
				$this->set_property($co, 'patent_kind', $value);
				$this->set_reftype($co, 'patent');
				break;		
			case "oclcnum":
				$this->set_oclcnum($co, $value);
				break;
			case "prioritydate":
				$this->set_property($co, 'patent_priority_date', $value);
				$this->set_reftype($co, 'patent');
				break;
			case "rfr_id":
				$newvalue = str_replace('sid:', '', $value);
				$this->set_referer($newvalue);
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
			case "val_fmt":
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
			case "url_ctx_val":
				$newvalue = str_replace('rft_id=info:', '', rawurldecode($value));
				$this->set_identifier($newvalue);
				break;
			default:
				$this->translate_openurl($co, $key, $value);
				break
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
	
	function translate_openurl($co, $key, $value){
	$openurl_keys=array // translate between OpenURL keys and English
		(	
		"artnum" => "article_number",
		"atitle" => "item_title",
		"cc" => "country_code",
		"co" => "country_name",
		"coverage" => "coverage",
		"description" => "description",
		"edition" => "edition",
		"epage" => "end_page",
		"inst" => "instution",
		"issue" => "issue",
		"pub" => "publisher",
		"pubdate" => "published",
		"publisher" => "publisher",
		"quarter" => "quarter",
		"series" => "series_title",
		"spage" => "start_page",
		"ssn" => "season",
		"stitle" => "abbreviated_title",
		"subject" => "subject",
		"type" => "type",
		"tpages" => "total_pages"
		);
		
		if (isset($openurl_keys[$key])){
			$newkey = $openurl_keys[$key];
		} else {
			$newkey = strtolower($key);
		}
		$this->set_property($co, $newkey, $value);
	}

}
?>