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

function check_prerequisites() {
	if (!function_exists('curl_version')) {
		print(json_encode(new Response(501, "curl is not available in this instance of PHP.", "")));
		return false;
	}
	
	return true;
}

function params_exist($check) {
	return isset($check['region'], $check['category'], $check['keywords']);
}

function build_cl_uri($check) {
	return "https://".$check['region'].".craigslist.org/search/".$check['category']."?query=".$check['keywords'];
}

// here is where the data formatting magic happens.
function build_data_set($dom, $curl) {
	if ($dom === FALSE) return false;
	
	$ret = array();
	
	// because PHP insists the DOM objects sometimes aren't objects?
	// possibly a bug in the HTML parser.
	error_reporting(E_ALL & ~E_NOTICE);
	
	// iterate over DOM
	foreach ($dom->find('.result-row') as $listing) {
		// the little text that appears under the listing's picture has what we need.
		$listing_text = $listing->find('.result-info', 0);
		
		// listing name/description/uri
		$name = $listing_text->find('a', 0)->plaintext;
		$itemlink = $listing_text->find('a', 0)->href;
		
		// listing date/time
		$datetime = $listing_text->find('time', 0)->datetime;
		
		// listing metadata (price/location)
		$meta = $listing_text->find('.result-meta', 0);
		$price = $meta->find('.result-price', 0)->plaintext;
		$location = $meta->find('.result-hood', 0)->plaintext;
		
		// clean up location/price
		$location = str_replace('(', '', $location);
		$location = str_replace(')', '', $location);
		$location = trim($location);
		$price = trim($price);
		
		// the data ID of the first link in the listing actually contains the images.craigslist.org
		// reference for what image to show.
		$image_uri = null;
		$data_str =  $listing->find('a', 0)->attr['data-ids'];
		$data_arr = explode(',', $data_str); // comma delimited for each image
		if (count($data_arr) > 0) { // if there was more than zero, there is an image to download.
			$extracted = explode(":", $data_arr[0])[1]; // whatever comes after the colon, that's the image token
			$image_uri = "https://images.craigslist.org/".$extracted."_300x300.jpg"; // 300x300 is the preview image
		}
		
		// encode the downloaded image to base64 (but only if the request succeeded).
		curl_setopt($curl, CURLOPT_URL, $image_uri);
		$curl_response_img = curl_exec($curl);
		$image_b64 = null;
		if ($curl_response_img !== false) {
			$image_b64 = base64_encode($curl_response_img);
		}
		
		// instance ClassifiedAd object.
		$ad = new ClassifiedAd($name, $price, $location, $datetime, $itemlink, $image_b64);
		array_push($ret, $ad);
	}
	
	return new ResponseBody($ret);
}

?>