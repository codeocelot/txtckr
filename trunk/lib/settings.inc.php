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
 
	function define_services(){
		$services[00]		=	"fulltext";
		$services[01]		=	"holdings";
		$services[02]		=	"request";
		$services[03]		=	"citation";
		$services[04]		=	"abstract";
		$services[05]		=	"reference";
		$services[06]		=	"information";
		$services[07]		=	"indexing";
		$services[08]		=	"ranking";		
	}
		
		
	function define_contexttypes(){
	// Supported contextobjects - these are taken from 
	
		$types['article'] = 'article';
		$article['reftype'] = 'JOUR';		// article: a document published in a journal.
		$article['reqtype'] = 'Journal Article';
		$article['sourcetype'] = 'Journal';
		$article['notes'] = '';

		$types['book'] = 'book';
		$book['reftype'] = 'BOOK';			// book: a publication that is complete in one part or a designated finite number of parts, often identified with an ISBN.
		$book['reqtype'] = 'Book';
		$book['sourcetype'] = 'Book';
		$book['notes'] = '';

		$types['bookitem'] = 'bookitem';
		$bookitem['reftype'] = 'CHAP';		// bookitem: a defined section of a book, usually with a separate title or number.
		$bookitem['reqtype'] = 'Book Section';
		$bookitem['sourcetype'] = 'Book';
		$bookitem['notes'] = '';

		$types['conference'] = 'conference';
		$conference['reftype'] = 'JFULL';	// conference: a record of a conference that includes one or more conference papers and that is published as an issue of a journal or serial publication 
		$conference['reqtype'] = 'Conference Item';
		$conference['sourcetype'] = 'Conference';
		$conference['notes'] = 'This was identified as a "collection of conference presentations published as an issue of a serial publication" in the OpenURL metadata.';

		$types['dissertation'] = 'dissertation';
		$dissertation['reftype'] = 'THES';	// thesis or dissertation
		$dissertation['reqtype'] = 'Dissertation';
		$dissertation['sourcetype'] = 'Thesis/Dissertation';
		$dissertation['notes'] = '';

		$types['document'] = 'document';	
		$document['reftype'] = 'GEN';		// document: general document type to be used when available data elements do not allow determination of a more specific document type, i.e. when one has only author and title but no publication information. 
		$document['reqtype'] = 'Unknown';
		$document['sourcetype'] = 'Unknown';
		$document['notes'] = 'This was identified as a "general document type" in the OpenURL metadata.';

		$types['issue'] = 'issue';
		$issue['reftype'] = 'JFULL';		// issue: one instance of the serial publication
		$issue['reqtype'] = 'Journal/Serial Issue';
		$issue['sourcetype'] = 'Journal';
		$issue['notes'] = 'This was identified as a "single issue of a serial publication" in the OpenURL metadata.';

		$types['journal'] = 'journal';
		$journal['reftype'] = 'JFULL';		// journal: a serial publication issued in successive parts
		$journal['reqtype'] = 'Journal/Serial Publication';
		$journal['sourcetype'] = 'Journal';
		$journal['notes'] = 'This was identified as a "serial publication" in the OpenURL metadata.';

		$types['patent'] = 'patent';
		$patent['reftype'] = 'PAT';			// patent
		$patent['reqtype'] = 'Patent';
		$patent['sourcetype'] = 'Patent';
		$proceeding['notes'] = '';

		$types['proceeding'] = 'proceeding';
		$proceeding['reftype'] = 'CONF';	// proceeding: a single conference presentation published in a journal or serial publication 
		$proceeding['reqtype'] = 'Conference Proceedings';
		$proceeding['sourcetype'] = 'Conference';
		$proceeding['notes'] = 'This was identified as a "single conference presentation in a serial publication" in the OpenURL metadata.';

		$types['preprint'] = 'report';
		$preprint['reftype'] = 'JOUR';		// preprint: an individual paper or report published in paper or electronically prior to its publication in a journal or serial.
		$preprint['reqtype'] = 'Journal Article Preprint';
		$preprint['sourcetype'] = 'Journal';
		$preprint['notes'] = 'This was identified as an "individual paper or report published in paper or electronically prior to its publication" in a journal or serial in the OpenURL metadata.';

		$types['report']= 'report';
		$report['reftype'] = 'RPRT';		// report: report or technical report is a published document that is issued by an organization, agency or government body
		$report['reqtype'] = 'Report';
		$report['sourcetype'] = 'Report';
		$report['notes'] = '';

		$types['unknown'] = 'unknown';
		$unknown['reftype'] = 'GEN';		// unknown, but at least it's know that it's unknown!
		$unknown['reqtype'] = 'Unknown';
		$unknown['sourcetype'] = 'Unknown';
		$unknown['notes'] = 'This was identified as an "unknown format" in the OpenURL metadata.';
	}
}	
?>