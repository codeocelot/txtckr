<?php
// External function which is called by basic_functions, and:
// - gets PubMed data relating to a PubMed ID
// - saves the PubMed data for later re-use if required
// - generates an error code which is translated here to a human-readable error
# using arrays directly
# /PubmedArticleSet/PubmedArticle/PubmedData/History/PubMedPubDate/Year
function parse_pubmed_xml($pmid){
if (!function_exists('fetchURL')){
require ('do_fetchURL.php');
}

if (!empty($pmid)){
// establish the variables we need
// $pmid_url = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?id=1202204&db=pubmed&mode=xml&tool=cfr-txtseekr&email=pasleyt@crop.cri.nz";
$pmidurl = "http://eutils.ncbi.nlm.nih.gov/entrez/eutils/efetch.fcgi?id=";
$pmidurl .= $pmid;
$pmidurl .= "&db=pubmed&mode=xml&tool=cfr-txtseekr&email=pasleyt@crop.cri.nz";

$xmlfilename = "./xml/pubmed/" . $pmid;
$xmlfilename .= ".xml";

$arr = array();

$fetch = fetchURL($pmidurl);
	$pubmedxml = $fetch['url_response'];
	$arr['PMurl_error_code'] = $fetch['url_error_code'];
	$arr['PMurl_error_text'] = $fetch['url_error_text'];
	$arr['PMurl_error_detail'] = "";
	unset($fetch);


if ((preg_match('/<!-- Error>XML not found for id:/', $pubmedxml)) && (!file_exists($xmlfilename))){ // PubMed error, no local cached copy
$tempfile = @fopen($xmlfilename, "w+");
@fwrite($tempfile, $pubmedxml);
$arr['PMurl_status'] = "Error. No results from PubMed query";
$arr['PMurl_error_detail'] = "Metadata not found in PubMed database for this PMID.";
$arr['PubMedFile'] = $xmlfilename;
return($arr);
	}
elseif ((preg_match('/<?xml version="1.0"?>/', $pubmedxml)) && (!file_exists($xmlfilename))){ // XML error, no local cached copy
$tempfile = @fopen($xmlfilename, "w+");
@fwrite($tempfile, $pubmedxml);
$arr = process_pubmed_xml($xmlfilename);
$arr['PMurl_status'] = "PubMed query successful";
$arr['PubMedFile'] = $xmlfilename;
return($arr);
	} 
elseif (($arr['PMurl_error_code'] != "200") && (!file_exists($xmlfilename))){ // HTTP error, no local cached copy
$arr['PMurl_status'] = "Error. No results from PubMed query";
$arr['PMurl_error_detail'] = "PubMed was unreachable - perhaps PubMed database is being maintained?";
return($arr);
	}
elseif (!file_exists($xmlfilename)){ // create a cached copy of PubMed XML data
$tempfile = @fopen($xmlfilename, "w+");
@fwrite($tempfile, $pubmedxml);
$arr = process_pubmed_xml($xmlfilename);
$arr['PMurl_status'] = "PubMed query successful";
$arr['PubMedFile'] = $xmlfilename;
return($arr);
	}
else { // no cached copy of PubMed XML data, and so far our connection has failed
	if (file_exists($arr['PubMedFile'])){
		$arr = process_pubmed_xml($xmlfilename);
		$arr['PMurl_status'] = "PubMed query failed";
		$arr['PMurl_error_detail'] = "Using previously cached PubMed Data";
		return($arr);
	} else {
		$arr['PMurl_status'] = "Error. No results from PubMed query";
		return($arr);
	}
}
}	
}
	
function process_pubmed_xml($xmlfilename){
$int = 1;
$arr['authors'] = "";
$arr['subjects'] = "[To 2 levels only]: ";

echo "<!-- Doing translation of PubMed XML for resolver! -->";
if (file_exists($xmlfilename)){
$RESULTSET = simplexml_load_file($xmlfilename);
foreach ($RESULTSET->PubmedArticle as $ITEM){

foreach ($ITEM->MedlineCitation as $MedlineCitation){
$arr['atitle'] = "" . $MedlineCitation->Article->ArticleTitle;
$arr['abstract'] = "" . $MedlineCitation->Article->Abstract->AbstractText;
$arr['title'] = "" . $MedlineCitation->Article->Journal->Title;
$arr['volume'] = "" . $MedlineCitation->Article->Journal->JournalIssue->Volume;
$arr['issue'] = "" . $MedlineCitation->Article->Journal->JournalIssue->Issue;
$arr['day'] =  "" . $MedlineCitation->Article->Journal->JournalIssue->PubDate->Day;
$arr['month'] = "" . $MedlineCitation->Article->Journal->JournalIssue->PubDate->Month;
$arr['year'] = "" . $MedlineCitation->Article->Journal->JournalIssue->PubDate->Year;
$arr['pages'] = "" . $MedlineCitation->Article->Pagination->MedlinePgn;
$arr['pmid'] = "" . $MedlineCitation->PMID;

foreach ($MedlineCitation->Article->Journal->ISSN as $issn){
	$issntype = "" . $issn['IssnType'];
	if ($issntype == "Print"){
	$arr['issn'] = "" . $issn;
	} else {
	$arr['eissn'] = "" . $issn;
	}
}

foreach ($MedlineCitation->MeshHeadingList->MeshHeading as $subject){
	if ($subject->QualifierName !=""){
	$arr['subjects'] .= "" . $subject->DescriptorName . " - " . $subject->QualifierName . "; ";
	} else {
	$arr['subjects'] .= "" . $subject->DescriptorName . "; ";
	}
}

foreach ($MedlineCitation->ChemicalList->Chemical as $chemical){
	if ($chemical->NameOfSubstance !=""){
	$arr['subjects'] .= "[Chemical: " . $chemical->NameOfSubstance . "]; ";
	} 
}



	foreach ($MedlineCitation->Article->AuthorList->Author as $author){
		$au = "author" . $int;
		$aulast = "aulast" . $int;
		$aufirst = "aufirst" . $int;
		$auinit = "auinit" . $int;
		$arr[$aulast] = "" . $author->LastName;
		if ($author->ForeName != ""){
		$arr[$aufirst] = "" . $author->ForeName;
		$arr[$au] = "" . $author->LastName . ", " . $author->ForeName;
		$arr['authors'] .=  "" . $author->LastName . ", " . $author->ForeName . "; ";
		} elseif ($author->FirstName != "") {
		$arr[$aufirst] = "" . $author->FirstName;
		$arr[$au] = "" . $author->LastName . ", " . $author->FirstName;
		$arr['authors'] .=  "" . $author->LastName . ", " . $author->FirstName . "; ";
		} elseif ($author->Initials != "") {
		$arr[$auinit] = "" . $author->Initials;
		$arr[$au] = "" . $author->LastName . ", " . $author->Initials;
		$arr['authors'] .=  "" . $author->LastName . ", " . $author->Initials . "; ";
		}
		$int++; 
	}

	}
}

foreach ($ITEM->PubmedData as $PubmedData){
	if (empty($arr['day'])){
	$arr['day'] = (int) "".$PubmedData->History->PubMedPubDate->Day;
	}

	if (empty($arr['month'])){
	$arr['month'] = (int) "".$PubmedData->History->PubMedPubDate->Month;
	}
	
	if (empty($arr['year'])){
	$arr['year'] = (int) "".$PubmedData->History->PubMedPubDate->Year;
	}
	
}

if (!ctype_digit($arr['month'])){
$arr['date'] = $arr['year']."-".makeNumMonth($arr['month'])."-01";
} else {
$arr['date'] = $arr['year']."-".$arr['month']."-".$arr['day'];
}


foreach ($RESULTSET->PubmedArticle->PubmedData->ArticleIdList->ArticleId as $articleid){
	$type = "" . $articleid['IdType'];
	$arr[$type] = "" . $articleid;
}


if (array_key_exists ('aulast1', $arr) ) {
$arr['aulast'] = $arr['aulast1'];
$arr['aufirst'] = $arr['aufirst1'];
}

if (array_key_exists ('doi', $arr) ) {
$arr['doiLink'] = "http://dx.doi.org/" . $arr['doi'];
}
if (array_key_exists ('oai', $arr) ) {
$arr['oaiLink'] = "http://search.yahoo.com/search;_ylt=?p=%22"  . $arr['oai'] . "%22&y=Search&fr=sfp";
}
if (array_key_exists ('pii', $arr) ) {
$arr['piiLink'] = "http://www.google.com/search?btnI&hl=en&q=&quot;"  . urlencode($arr['pii']) . "&quot;";
}
if (array_key_exists ('id', $arr) ) {
	if (preg_match('/http:\/\//', $value1)){
	$arr['idLink'] = $arr['id'];
	}
	elseif (preg_match('/www./', $value1)){
	$arr['idLink'] = $arr['id'];
	}
}

(@(!empty($arr['year'])) & (ctype_digit($arr['year'])) & ($arr['year'] != 0)) ? $arr['artyear'] = $arr['year'] : "";
$pagesarr = explode("-", $arr['pages']);
$arr['spage'] = $pagesarr['0'];
$arr['epage'] = $pagesarr['1'];
$arr['pmidLink'] = "http://www.ncbi.nlm.nih.gov/pubmed/" . $arr['pmid'];
}
$arr['PubMedFile'] = $xmlfilename;
return $arr;
}

?>