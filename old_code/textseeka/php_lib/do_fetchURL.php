<?php
//------------------------------------------------------------fetchURL : START
// retrieves a webpage
// RETURNS:  array containing: 
// 			web page/HTML code as a string
// 			error as a code
// 			error as a text string

function fetchURL($url){
if (!defined(PROXY)){ 
require ('proxy_config.inc.php'); 
}
settype($fetch_response, "array");
settype($url_response, "string");

	$ch = curl_init();
	if (PROXY === "true"){
	$proxy = WEBPROXY;
		curl_setopt ($ch, CURLOPT_PROXY,$proxy);
	}
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt ($ch, CURLOPT_USERAGENT,'Mozilla/4.0');
	curl_setopt ($ch, CURLOPT_TIMEOUT, '15');
	curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
	curl_setopt ($ch, CURLOPT_URL,$url);

// $url_response will contain the response from the URL : $url
// $url_error_code will be something like 404
// $url_error_text will be something like "page not found"


$fetch_response['url_response'] = curl_exec($ch);
$fetch_response['url_error_code'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$fetch_response['url_error_text'] = ereg_replace  ("couldn't", "could not", curl_error($ch));

echo "<!--".$fetch_response['url_error_code']."-->";
echo "<!--".$fetch_response['url_error_text']."-->"; 

	curl_close($ch);
	
return ($fetch_response);
}
//------------------------------------------------------------fetchURL : FINISH
?>