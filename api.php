<?php
include('autoload.inc.php');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

function doNotCacheThis() {
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}
function parseGETArray($s) {
	$s = str_replace('%20','', $s);
	$s = str_replace(' ','', $s);
	if(substr($s,0,1) != '[') {
		$s = "[$s";
	}
	if(substr($s,-1,1) != ']') {
		$s = "$s]";
	}
	$s = str_replace('[','["', $s);
	$s = str_replace(']','"]', $s);
	$s = str_replace(',','","', $s);
	return json_decode($s);
}

// TODO move this to ChuckAPI and output as debugging info is ?debug is given
$timer = new Timer();
if(isset($_GET['callback'])) {
	header('Content-Type: text/javascript');
	$api = ChuckScriptCommunicationAPI::getInstance();
	$api->setCallback($_GET['callback']);
} else {
	header('Content-Type: application/json');
	$api = ChuckAPI::getInstance();
}

if(isset($_GET['escape'])) {
  $escape = $_GET['escape'];
  if($escape == "javascript") {
    $api->setEscaper(new JavaScriptEscaper());
  }
}

if(!isset($_SERVER['PATH_INFO'])) {
	exit();
}
$path = $_SERVER['PATH_INFO'];

// process the request
if($path == '/categories') {
	$api->echoCategories();
} else {
	$firstName = 'Chuck';
	if(isset($_GET['firstName'])) {
		$firstName = $_GET['firstName'];
	}
	$lastName = 'Norris';
	if(isset($_GET['lastName'])) {
		$lastName = $_GET['lastName'];
	}
	if(preg_match('/^\/jokes\/random\/([0-9]+)/',$path, $matches)) { // think of the possible trailing '/' !!!
		doNotCacheThis();
		// return multiple quotes
		$number = $matches[1];
		if(isset($_GET['limitTo'])) {
			$limitTo = parseGETArray($_GET['limitTo']);
			$api->echoRandomQuotesBelongingTo($firstName, $lastName, $number, $limitTo);
		} elseif(isset($_GET['exclude'])) {
			$exclude = parseGETArray($_GET['exclude']);
			$api->echoRandomQuotesExcluding($firstName, $lastName, $number, $exclude);
		} else {
			$api->echoRandomQuotes($firstName, $lastName, $number);
		}	
	} elseif(preg_match('/^\/jokes\/random/',$path)) { // think of the possible trailing '/' !!!
		doNotCacheThis();
		// return a single quote
		if(isset($_GET['limitTo'])) {
			$limitTo = parseGETArray($_GET['limitTo']);
			$api->echoRandomQuoteBelongingTo($firstName, $lastName, $limitTo);
		} elseif(isset($_GET['exclude'])) {
			$exclude = parseGETArray($_GET['exclude']);
			$api->echoRandomQuoteExcluding($firstName, $lastName, $exclude);
		} else {
			$api->echoRandomQuote($firstName, $lastName);
		}
	} elseif(preg_match('/^\/jokes\/([0-9]+)/',$path, $matches)) { // think of the possible trailing '/' !!!
		$api->echoQuoteById($firstName, $lastName, $matches[1]);
	} elseif(preg_match('/^\/jokes\/count/',$path, $matches)) { // think of the possible trailing '/' !!!
		$api->echoQuoteCount();
	} elseif(preg_match('/^\/jokes/',$path)) { // think of the possible trailing '/' !!!
		if(isset($_GET['limitTo'])) {
			$limitTo = parseGETArray($_GET['limitTo']);
			$api->echoAllQuotesBelongingTo($firstName, $lastName, $limitTo);
		} elseif(isset($_GET['exclude'])) {
			$exclude = parseGETArray($_GET['exclude']);
			$api->echoAllQuotesExcluding($firstName, $lastName, $exclude);
		} else {
			$api->echoAllQuotes($firstName, $lastName);
		}
	}
}

// do not log the request
