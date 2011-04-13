<?php
# using arrays directly
function do_crossref_query($CRurl){
if (!function_exists('fetchURL')){
include ('do_fetchURL.php');
}

$arr = array();

$fetch = fetchURL($CRurl);
	$CRunixref = $fetch['url_response'];
	$arr['CRurl_error_code'] = $fetch['url_error_code'];
	$arr['CRurl_error_text'] = $fetch['url_error_text'];
	unset($fetch);
	
$arr['CrossRefFile'] = NULL;
if (preg_match('/<query status="unresolved" fl_count="0">/', $CRunixref)){
$arr['CRurl_status'] = "Null. No results from CrossRef query";
echo "<!-- Null. No results from CrossRef query -->\n";
$arr['error'] = "CrossRef query failed - unable to retrieve article details to determine access!";
$arr['error_detail'] = "CrossRef said: unresolved";
return($arr);
	}
elseif (preg_match('/DOI not found in CrossRef/', $CRunixref)){
$arr['CRurl_status'] = "Null. No results from CrossRef query";
echo "<!-- Null. No results from CrossRef query -->\n";
$arr['error'] = "CrossRef query failed - unable to retrieve article details to determine access!";
$arr['error_detail'] = "CrossRef said: DOI not found - perhaps the publisher is not a member of CrossRef?";
return($arr);
	}	
elseif ($arr['CRurl_error_code'] != "200"){
$arr['CRurl_status'] = "Error. No results from CrossRef query";
echo "<!-- Error. No results from CrossRef query -->\n";
$arr['error'] = "CrossRef query failed - unable to retrieve article details to determine access!";
$arr['error_detail'] = "CrossRef was unreachable!";
return($arr);
	}
else {
$arr['CRurl_status'] = "CrossRef query successful";
}
// set up the empty array key for Notes (used for generating the RIS formatted output)
$arr['N1'] = "";
// set up a couple of values which will increment later.
$i="1";
$ii="1";

// echo $cr_xml;
if (preg_match('/book/', $CRunixref)){
echo "<!-- CrossRef data: book -->\n";
$crB_xml = simplexml_load_string($CRunixref);
foreach ($crB_xml->doi_record->crossref->book as $book){
# PARSE BOOK METADATA : START
	foreach ($book->book_metadata as $book_metadata){
	$arr['reftype'] = "BOOK";
	foreach ($book_metadata->contributors->person_name as $person_name){
	$autype = $person_name['contributor_role'];
	if ($person_name['sequence'] == "first"){
	switch ($autype){
		case "editor":
			$arr['edlast'] = "" . $person_name->surname;
			$arr['edfirst'] = "" . $person_name->given_name;
			break;
		case "author":
			$arr['aulast'] = "" . $person_name->surname;
			$arr['aufirst'] = "" . $person_name->given_name;
			break;
		}
	} else {
	switch ($autype){
		case "editor":
		$edlast = "edlast" . $i;
		$edfirst = "edfirst" . $i;
			$arr[$edlast] = "" . $person_name->surname;
			$arr[$edfirst] = "" . $person_name->given_name;
			break;
		case "author":
		$aulast = "aulast" . $i;
		$aufirst = "aufirst" . $i;
			$arr[$aulast] = "" . $person_name->surname;
			$arr[$aufirst] = "" . $person_name->given_name;
			break;
		}
	$i++;
	 }
	}
	$arr['series'] = "" . $book_metadata->series_metadata->titles->title;
	$arr['title'] = "" . $book_metadata->titles->title;
	$arr['volume'] = "" . $book_metadata->volume;
	$arr['year'] = "" . $book_metadata->publication_date->year;
	$arr['artyear'] = "" . $book_metadata->publication_date->year;
	$arr['dateisyear'] = "true";
	$arr['publisher'] = "" . $book_metadata->publisher->publisher_name;
	$arr['city'] = "" . $book_metadata->publisher->publisher_place;
	$arr['PB'] = "" . $book_metadata->publisher->publisher_name;
	$arr['CY'] = "" . $book_metadata->publisher->publisher_place;
	$arr['isbn'] = "" . preg_replace  ('/[-\s]/', '', $book_metadata->isbn);
	$arr['SN'] = "" . preg_replace  ('/[-\s]/', '', $book_metadata->isbn);
	$arr['N1'] .= "ISBN (" . $book_metadata->isbn['media_type'] ."): " . $book_metadata->isbn ."\n";
	$arr['CRdoi'] = "" . $book_metadata->doi_data->doi;
	$arr['N1'] .= "http://dx.doi.org/" . $book_metadata->doi_data->doi ." [Book DOI Link]\n";
	$arr['N1'] .= "" . $book_metadata->doi_data->resource ." [Book URL]\n";
	$arr['N1'] .= "Language: " . mb_convert_case($book_metadata['language'], MB_CASE_UPPER, "UTF-8") ." (EN = English)\n";
	}
# PARSE BOOK METADATA : FINISH
# PARSE CHAPTER/CONTENT METADATA : START
if (preg_match('/content_item/', $CRunixref)){
	foreach ($book->content_item as $content_item){
	$arr['reftype'] = "CHAP";
	foreach ($content_item->contributors->person_name as $person_name){
	$autype = $person_name['contributor_role'];
	if ($person_name['sequence'] == "first"){
	switch ($autype){
		case "editor":
			$arr['edlast'] = "" . $person_name->surname;
			$arr['edfirst'] = "" . $person_name->given_name;
			break;
		case "author":
			$arr['aulast'] = "" . $person_name->surname;
			$arr['aufirst'] = "" . $person_name->given_name;
			break;
		}
	} else {
	switch ($autype){
		case "editor":
		$edlast = "edlast" . $i;
		$edfirst = "edfirst" . $i;
			$arr[$edlast] = "" . $person_name->surname;
			$arr[$edfirst] = "" . $person_name->given_name;
			break;
		case "author":
		$aulast = "aulast" . $i;
		$aufirst = "aufirst" . $i;
			$arr[$aulast] = "" . $person_name->surname;
			$arr[$aufirst] = "" . $person_name->given_name;
			break;
		}
	$i++;
	 }
	}
	$arr['atitle'] = "" . $content_item->titles->title;
	$arr['N1'] .= "" . $content_item->component_number ."\n";
	$arr['spage'] = "" . $content_item->pages->first_page;
	$arr['epage'] = "" . $content_item->pages->last_page;
	$arr['CRdoi'] = "" . $content_item->doi_data->doi;
	$arr['id'] = "doi:" . $content_item->doi_data->doi;
	$arr['idLink'] = "" . $content_item->doi_data->resource;
	}
# PARSE CHAPTER/CONTENT METADATA :  FINISH
  }
 }
}

if (preg_match('/journal_metadata/', $CRunixref)){
echo "<!-- CrossRef data: journal -->\n";
$crJ_xml = simplexml_load_string($CRunixref);
foreach ($crJ_xml->doi_record->crossref->journal as $journal){
	$arr['reftype'] = "JOUR";
# PARSE JOURNAL METADATA : START
	foreach ($journal->journal_metadata as $journal_metadata){
	$arr['title'] = "" . $journal_metadata->full_title;
	$arr['abbrev_title'] = "" . $journal_metadata->abbrev_title;
	$arr['issn'] = "" . $journal_metadata->issn;
	$arr['N1'] .= "ISSN (" . $journal_metadata->issn['media_type'] ."): " . $journal_metadata->issn ."\n";
	$arr['coden'] = "" . $journal_metadata->coden;
	}
# PARSE  JOURNAL METADATA : FINISH
# PARSE ISSUE METADATA : START
	foreach ($journal->journal_issue as $journal_issue){
	$arr['artyear'] = "" . $journal_issue->publication_date->year;
	$arr['dateisyear'] = "true";
	$arr['year'] = "" . $journal_issue->publication_date->year;
	$arr['month'] = "" . $journal_issue->publication_date->month;
	$arr['day'] = "" . $journal_issue->publication_date->day;
	$arr['volume'] = "" . $journal_issue->journal_volume->volume;
	$arr['issue'] = "" . $journal_issue->issue;
	}
# PARSE  ISSUE METADATA : FINISH
if (preg_match('/journal_article/', $CRunixref)){
# PARSE ARTICLE METADATA : START
	foreach ($journal->journal_article as $journal_article){
	foreach ($journal_article->contributors->person_name as $person_name){
	$autype = $person_name['contributor_role'];
	if ($person_name['sequence'] == "first"){
	switch ($autype){
		case "editor":
			$arr['edlast'] = "" . $person_name->surname;
			$arr['edfirst'] = "" . $person_name->given_name;
			break;
		case "author":
			$arr['aulast'] = "" . $person_name->surname;
			$arr['aufirst'] = "" . $person_name->given_name;
			break;
		}
	} else {
	switch ($autype){
		case "editor":
		$edlast = "edlast" . $i;
		$edfirst = "edfirst" . $i;
			$arr[$edlast] = "" . $person_name->surname;
			$arr[$edfirst] = "" . $person_name->given_name;
			break;
		case "author":
		$aulast = "aulast" . $i;
		$aufirst = "aufirst" . $i;
			$arr[$aulast] = "" . $person_name->surname;
			$arr[$aufirst] = "" . $person_name->given_name;
			break;
		}
	$i++;
	 }
	}
	$arr['atitle'] = "" . $journal_article->titles->title;
	$arr['N1'] .= "Article type: " . $journal_article['publication_type'] ."\n";
	$arr['spage'] = "" . $journal_article->pages->first_page;
	$arr['epage'] = "" . $journal_article->pages->last_page;
	$arr['CRdoi'] = "" . $journal_article->doi_data->doi;
	$arr['N1'] .= "Available online since: " . $journal_article->doi_data->timestamp ."\n";
	$arr['timestamp'] = $journal_article->doi_data->timestamp;
	if ($journal_article->publication_date['media_type'] == "online"){
	$arr['N1'] .= "Published online: " . $journal_article->publication_date->year . "-" . $journal_article->publication_date->month . "-" . $journal_article->publication_date->day . "\n";
	$arr['artyear'] = "" . $journal_article->publication_date->year;
	$arr['dateisyear'] = "true";
	}
	$arr['id'] = "doi:" . $journal_article->doi_data->doi;
	$arr['idLink'] = "" . $journal_article->doi_data->resource;
	}
# PARSE ARTICLE METADATA :  FINISH
  }
 }
}

if (isset($arr['CRdoi'])){
$unixrefname = "./xml/CrossRef/";
$unixrefname .= ereg_replace ("/", "-", $arr['CRdoi']);
$unixrefname .= ".xml";
// echo $unixrefname;
if (!file_exists($unixrefname)){
$tempfile = @fopen($unixrefname, "w+");
@fwrite($tempfile, $CRunixref);
}
	if (file_exists($unixrefname)){
	$arr['CrossRefFile'] = $unixrefname;
	}
}

if (isset($arr['artyear'])) {
$arr['artyear'] = (int) $arr['artyear'];
}
return($arr);
}


?>