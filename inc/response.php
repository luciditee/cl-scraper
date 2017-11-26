<?php

/* 
 * Craigslist Listing Scraper
 * by Will Preston - 2017
 *
 * A simple frontend that grabs CL listings en masse and returns them in a 
 * convenient JSON format.
 * 
 * Requires cURL support to be compiled into PHP.
 *
 * Utilizes Simple HTML DOM by S.C. Chen, John Schlick, and Rus Carroll,
 * which is available under the MIT License.
 *
 * Released into the public domain.
 * 
 */

// Simple encapsulating class for a response.
class Response {
	
	var $body = null;
	var $error_body = "OK";
	var $http_code = 200;

	function __construct($http_code=200, $error_body="ok", $body = array()) {
		 $this->body = $body;
		 $this->error_body = $error_body;
		 $this->http_code = $http_code;
		 
		 // Set the numeric code.
		 http_response_code(is_numeric($http_code) ? $http_code : 200);
	}
}

// Simple encapsulating class for a response body.
class ResponseBody {
	var $ads = array();
	var $request_uri = "N/A";
	
	function __construct($ads=array()) {
		$this->ads = $ads;
	}
}

// Encapsulation of a Classified Ad.
class ClassifiedAd {
	var $itemName = "Item";
	var $askingPrice = "Price";
	var $location = "Location";
	var $datetime = "date/goes/here";
	var $itemlink = "uri-goes-here";
	
	function __construct($item, $price, $location, $datetime, $link) {
		$this->itemName = $item;
		$this->askingPrice = $price;
		$this->location = $location;
		$this->datetime = $datetime;
		$this->itemlink = $link;
	}
}

?>