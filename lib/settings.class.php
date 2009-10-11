<?php
/**
 * This is the base class for txtckr settings.
 *
 * @author		Tom Pasley
 * @date		13/07/2009
 * @last mod	05/08/2009
 * @package 	txtckr
 * @copyright 	open source
 */

class settings {
 
	function define_services(){
		$this->services[00]		=	"fulltext";
		$this->services[01]		=	"holdings";
		$this->services[02]		=	"request";
		$this->services[03]		=	"citation";
		$this->services[04]		=	"abstract";
		$this->services[05]		=	"reference";
		$this->services[06]		=	"information";
		$this->services[07]		=	"indexing";
		$this->services[08]		=	"ranking";		
	}
		
		
	function define_contexttypes(){
	// Supported contextobjects - these are taken from ocoins generator
	// article: a document published in a journal.
		$this->types['key']['article'] = 'article';
		$this->types['reftype']['article'] = 'JOUR';
		$this->types['reqtype']['article'] = 'Journal Article';
		$this->types['sourcetype']['article'] = 'Journal';
		$this->types['notes']['article'] = '';
	// book: a publication that is complete in one part or a designated finite number of parts, often identified with an ISBN.
		$this->types['key']['book'] = 'book';
		$this->types['reftype']['book'] = 'BOOK';
		$this->types['reqtype']['book'] = 'Book';
		$this->types['sourcetype']['book'] = 'Book';
		$this->types['notes']['book'] = '';
	// bookitem: a defined section of a book, usually with a separate title or number.
		$this->types['key']['bookitem'] = 'bookitem';
		$this->types['reftype']['bookitem'] = 'CHAP';
		$this->types['reqtype']['bookitem'] = 'Book Section';
		$this->types['sourcetype']['bookitem'] = 'Book';
		$this->types['notes']['bookitem'] = '';
	// conference: a record of a conference that includes one or more conference papers and that is published as an issue of a journal or serial publication 
		$this->types['key']['conference'] = 'conference';
		$this->types['reftype']['conference'] = 'JFULL';
		$this->types['reqtype']['conference'] = 'Conference Item';
		$this->types['sourcetype']['conference'] = 'Conference';
		$this->types['notes']['conference'] = 'This was identified as a "collection of conference presentations published as an issue of a serial publication" in the OpenURL metadata.';
	// thesis or dissertation
		$this->types['key']['dissertation'] = 'dissertation';
		$this->types['reftype']['dissertation'] = 'THES';
		$this->types['reqtype']['dissertation'] = 'Dissertation';
		$this->types['sourcetype']['dissertation'] = 'Thesis/Dissertation';
		$this->types['notes']['dissertation'] = '';
	// document: general document type to be used when available data elements do not allow determination of a more specific document type, i.e. when one has only author and title but no publication information. 
		$this->types['key']['document'] = 'document';	
		$this->types['reftype']['document'] = 'GEN';
		$this->types['reqtype']['document'] = 'Unknown';
		$this->types['sourcetype']['document'] = 'Unknown';
		$this->types['notes']['document'] = 'This was identified as a "general document type" in the OpenURL metadata.';
	// issue: one instance of the serial publication
		$this->types['key']['issue'] = 'issue';
		$this->types['reftype']['issue'] = 'JFULL';
		$this->types['reqtype']['issue'] = 'Journal/Serial Issue';
		$this->types['sourcetype']['issue'] = 'Journal';
		$this->types['notes']['issue'] = 'This was identified as a "single issue of a serial publication" in the OpenURL metadata.';
	// journal: a serial publication issued in successive parts
		$this->types['key']['journal'] = 'journal';
		$this->types['reftype']['journal'] = 'JFULL';
		$this->types['reqtype']['journal'] = 'Journal/Serial Publication';
		$this->types['sourcetype']['journal'] = 'Journal';
		$this->types['notes']['journal'] = 'This was identified as a "serial publication" in the OpenURL metadata.';
	// patent
		$this->types['key']['patent'] = 'patent';
		$this->types['reftype']['patent'] = 'PAT';
		$this->types['reqtype']['patent'] = 'Patent';
		$this->types['sourcetype']['patent'] = 'Patent';
		$this->types['notes']['patent'] = '';
	// proceeding: a single conference presentation published in a journal or serial publication 
		$this->types['key']['proceeding'] = 'proceeding';
		$this->types['reftype']['proceeding'] = 'CONF';
		$this->types['reqtype']['proceeding'] = 'Conference Proceedings';
		$this->types['sourcetype']['proceeding'] = 'Conference';
		$this->types['notes']['proceeding'] = 'This was identified as a "single conference presentation in a serial publication" in the OpenURL metadata.';
	// preprint: an individual paper or report published in paper or electronically prior to its publication in a journal or serial.
		$this->types['key']['preprint'] = 'report';
		$this->types['reftype']['preprint'] = 'JOUR';
		$this->types['reqtype']['preprint'] = 'Journal Article Preprint';
		$this->types['sourcetype']['preprint'] = 'Journal';
		$this->types['notes']['preprint'] = 'This was identified as an "individual paper or report published in paper or electronically prior to its publication" in a journal or serial in the OpenURL metadata.';
	// report: report or technical report is a published document that is issued by an organization, agency or government body
		$this->types['key']['report']= 'report';
		$this->types['reftype']['report'] = 'RPRT';
		$this->types['reqtype']['report'] = 'Report';
		$this->types['sourcetype']['report'] = 'Report';
		$this->types['notes']['report'] = '';
	// unknown, but at least it's known that it's unknown!
		$this->types['key']['unknown'] = 'unknown';
		$this->types['reftype']['unknown'] = 'GEN';
		$this->types['reqtype']['unknown'] = 'Unknown';
		$this->types['sourcetype']['unknown'] = 'Unknown';
		$this->types['notes']['unknown'] = 'This was identified as an "unknown format" in the OpenURL metadata.';
	}

	function undefine_contexttypes(){
		$this->types = array();
	}
	
	
	function define_name_parts(){
		$this->prefixes[00] = '/\bvan\sden\b/i'; // van den = Dutch
		$this->prefixes[01] = '/\bvan\sder\b/i'; // van der = Dutch
		$this->prefixes[02] = '/\bvan\sde\b/i'; // van de = Dutch
		$this->prefixes[03] = '/\bvan\b/i'; // van = Dutch
		$this->prefixes[04] = '/\bvon\b/i'; // von = German
		$this->prefixes[05] = '/\bdela\b/i'; // dela = French/Italian?
		$this->prefixes[06] = '/\bde\sla\b/i'; // de la = French
		$this->prefixes[07] = '/\bde\b/i'; // de = Dutch/French?
		$this->prefixes[08] = '/\bdes\b/i'; // des = French
		$this->prefixes[09] = '/\bdi\b/i'; // di = Italian
		$this->prefixes[10] = '/\bdu\b/i'; // du = French
		$this->prefixes[11] = '/\baf\b/i'; // af = Swedish
		$this->prefixes[12] = '/\bbin\b/i'; // bin = Arabic
		$this->prefixes[13] = '/\bben\b/i'; // ben = Hebrew
		$this->prefixes[14] = '/\bibn\b/i'; // ibn = Arabic
		$this->prefixes[15] = '/\buyt\sden\b/i'; // uyt den = Dutch
		$this->prefixes[16] = '/\buyt\sder\b/i'; // uyt der = Dutch
		$this->prefixes[17] = '/\bten\b/i'; // ten = Dutch
		$this->prefixes[18] = '/\bter\b/i'; // ter = Dutch
		$this->prefixes[19] = '/\bhet\b/i'; // het = Dutch?
		$this->prefixes[20] = '/\bab\b/i'; // ab = Welsh
		$this->prefixes[21] = '/\bap\b/i'; // ap = Welsh
		$this->prefixes[22] = '/\bst\.\b/i'; // st. = English/French?
		
		$this->others[00] = '/^Van$/D'; // Van = "West, Van", "Morrison, Van", or "Van Lustbader, Eric"?
		$this->others[01] = '/^Ben$/D'; // Ben = "Ben Carey, Donald" or "Carey, Ben"?
		
		$this->titles[00] = '/\bjr\.\b/i'; // Jr. = American
		$this->titles[01] = '/\bsr\.\b/i'; // Sr. = American
		$this->titles[02] = '/\b[ivx]+\b/i'; // IV, III = English/American
		$this->titles[03] = '/\bph\.?d\b/i'; // Doctor
		$this->titles[04] = '/\bm\.?d\b/i'; // Masters
		$this->titles[05] = '/\besq\.\b/i'; // Esquire
		$this->titles[06] = '/\besquire\b/i'; // Esquire
		$this->titles[07] = '/\bjudge\b/i'; // Esquire
	}

	function undefine_name_parts(){
		$this->prefixes			= array();
		$this->pre_others		= array();
		$this->titles			= array();
	}	
	
	function define_openurl_keys(){
		// translate between OpenURL keys and English lowercase names (spaces substituted for underscores)
		// some of these aren't translations, they're placeholders so they're not forgotten!
		$this->openurl['key']['advisor'] = 'thesis_advisor';
		$this->openurl['key']['applcc'] = 'patent_application_country';
		$this->openurl['key']['appldate'] = 'patent_application_date';
		$this->openurl['key']['applnumber'] = 'patent_application_num';
		$this->openurl['key']['applyear'] = 'patent_application_year';
		$this->openurl['key']['artnum'] = 'article_number';
		$this->openurl['key']['assignee'] = 'patent_assignee';
		$this->openurl['key']['atitle'] = 'item_title';
		$this->openurl['key']['au'] = 'author_fullname';
		$this->openurl['key']['aucorp'] = 'author_corporate';
		$this->openurl['key']['aufirst'] = 'author_firstname';
		$this->openurl['key']['aufull'] = 'author_fullname';
		$this->openurl['key']['auinit'] = 'author_initials';
		$this->openurl['key']['auinit1'] = 'author_initial_1';
		$this->openurl['key']['auinitm'] = 'author_initial_m';
		$this->openurl['key']['aulast'] = 'author_lastname';
		$this->openurl['key']['btitle'] = 'title';
		$this->openurl['key']['cc'] = 'country_code';
		$this->openurl['key']['co'] = 'country_name';
		$this->openurl['key']['coden'] = 'coden';
		$this->openurl['key']['contributor'] = 'contributor';
		$this->openurl['key']['coverage'] = 'coverage';
		$this->openurl['key']['creator'] = 'creator';
		$this->openurl['key']['degree'] = 'thesis_type';
		$this->openurl['key']['description'] = 'description';
		$this->openurl['key']['ed'] = 'editor_fullname';
		$this->openurl['key']['edfirst'] = 'editor_firstname';
		$this->openurl['key']['edfull'] = 'editor_fullname';
		$this->openurl['key']['edinit'] = 'editor_initials';
		$this->openurl['key']['edition'] = 'edition';
		$this->openurl['key']['edlast'] = 'editor_lastname';
		$this->openurl['key']['eissn'] = 'eissn';
		$this->openurl['key']['epage'] = 'end_page';
		$this->openurl['key']['genre'] = 'genre';
		$this->openurl['key']['inst'] = 'instution';
		$this->openurl['key']['inv'] = 'inventor_fullname';
		$this->openurl['key']['invfirst'] = 'inventor_firstname';
		$this->openurl['key']['invfull'] = 'inventor_fullname';
		$this->openurl['key']['invinit'] = 'inventor_initials';
		$this->openurl['key']['invlast'] = 'inventor_lastname';
		$this->openurl['key']['isbn'] = 'isbn';
		$this->openurl['key']['issn'] = 'issn';
		$this->openurl['key']['issue'] = 'issue';
		$this->openurl['key']['jtitle'] = 'title';
		$this->openurl['key']['kind'] = 'patent_kind';
		$this->openurl['key']['pages'] = 'pages';
		$this->openurl['key']['pub'] = 'publisher';
		$this->openurl['key']['pubdate'] = 'published';
		$this->openurl['key']['publisher'] = 'publisher';
		$this->openurl['key']['quarter'] = 'quarter';
		$this->openurl['key']['series'] = 'series_title';
		$this->openurl['key']['sid'] = 'referer_id';
		$this->openurl['key']['spage'] = 'start_page';
		$this->openurl['key']['ssn'] = 'season';
		$this->openurl['key']['stitle'] = 'abbreviated_title';
		$this->openurl['key']['subject'] = 'subject';
		$this->openurl['key']['title'] = 'title';
		$this->openurl['key']['tpages'] = 'total_pages';
		$this->openurl['key']['type'] = 'type';
	}
	
	function undefine_openurl_keys(){
		$this->openurl['key'] = array();
	}

	function define_openurl_labels(){
		$this->openurl['label']['advisor'] = 'thesis_advisor';
		$this->openurl['label']['applcc'] = 'patent_application_country';
		$this->openurl['label']['appldate'] = 'patent_application_date';
		$this->openurl['label']['applnumber'] = 'patent_application_num';
		$this->openurl['label']['applyear'] = 'patent_application_year';
		$this->openurl['label']['artnum'] = 'article_number';
		$this->openurl['label']['assignee'] = 'patent_assignee';
		$this->openurl['label']['atitle'] = 'item_title';
		$this->openurl['label']['au'] = 'author_fullname';
		$this->openurl['label']['aucorp'] = 'author_corporate';
		$this->openurl['label']['aufirst'] = 'author_firstname';
		$this->openurl['label']['aufull'] = 'author_fullname';
		$this->openurl['label']['auinit'] = 'author_initials';
		$this->openurl['label']['auinit1'] = 'author_initial_1';
		$this->openurl['label']['auinitm'] = 'author_initial_m';
		$this->openurl['label']['aulast'] = 'author_lastname';
		$this->openurl['label']['btitle'] = 'title';
		$this->openurl['label']['cc'] = 'country_code';
		$this->openurl['label']['co'] = 'country_name';
		$this->openurl['label']['coden'] = 'coden';
		$this->openurl['label']['contributor'] = 'contributor';
		$this->openurl['label']['coverage'] = 'coverage';
		$this->openurl['label']['creator'] = 'creator';
		$this->openurl['label']['degree'] = 'thesis_type';
		$this->openurl['label']['description'] = 'description';
		$this->openurl['label']['ed'] = 'editor_fullname';
		$this->openurl['label']['edfirst'] = 'editor_firstname';
		$this->openurl['label']['edfull'] = 'editor_fullname';
		$this->openurl['label']['edinit'] = 'editor_initials';
		$this->openurl['label']['edition'] = 'edition';
		$this->openurl['label']['edlast'] = 'editor_lastname';
		$this->openurl['label']['eissn'] = 'eissn';
		$this->openurl['label']['epage'] = 'end_page';
		$this->openurl['label']['genre'] = 'genre';
		$this->openurl['label']['inst'] = 'instution';
		$this->openurl['label']['inv'] = 'inventor_fullname';
		$this->openurl['label']['invfirst'] = 'inventor_firstname';
		$this->openurl['label']['invfull'] = 'inventor_fullname';
		$this->openurl['label']['invinit'] = 'inventor_initials';
		$this->openurl['label']['invlast'] = 'inventor_lastname';
		$this->openurl['label']['isbn'] = 'isbn';
		$this->openurl['label']['issn'] = 'issn';
		$this->openurl['label']['issue'] = 'issue';
		$this->openurl['label']['jtitle'] = 'title';
		$this->openurl['label']['kind'] = 'patent_kind';
		$this->openurl['label']['pages'] = 'pages';
		$this->openurl['label']['pub'] = 'publisher';
		$this->openurl['label']['pubdate'] = 'published';
		$this->openurl['label']['publisher'] = 'publisher';
		$this->openurl['label']['quarter'] = 'quarter';
		$this->openurl['label']['series'] = 'series_title';
		$this->openurl['label']['sid'] = 'referer_id';
		$this->openurl['label']['spage'] = 'start_page';
		$this->openurl['label']['ssn'] = 'season';
		$this->openurl['label']['stitle'] = 'abbreviated_title';
		$this->openurl['label']['subject'] = 'subject';
		$this->openurl['label']['title'] = 'title';
		$this->openurl['label']['tpages'] = 'total_pages';
		$this->openurl['label']['type'] = 'type';
	}
	
	function undefine_openurl_labels(){
		$this->openurl['label'] = array();
	}	
}
?>