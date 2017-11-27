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

// define constants
define('USER_AGENT', "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36");
define('COOKIE_JAR', '/tmp/cookies/');
define('COOKIE_FILE', 'cl_cookie');

// require necessary files
require('./inc/shd.php');
require('./inc/response.php');
require('./inc/funcs.php');

// prerequisite checks
if (check_prerequisites() === FALSE) {
	die();
}

// set necessary initial codes
$response_code = 200;
$error_body = "OK";
$response_body = null;
$curl = curl_init();

// Ensure curl inits
if ($curl === FALSE) {
	// if curl fails to init, create a response.
	$response_code = 503;
	$error_body = "curl failed with error code " . curl_errno($curl) . ": " . curl_error($curl);
}

// set up curl properties
// I'm sorry for disabling SSL verification here, but I have two reasons:
// 1. The data sent/received here is not sensitive.
// 2. cURL doesn't have a keychain by default.  You have to manually add your CA.
// I recommend you do that and then change CURLOPT_SSL_VERIFYPEER to true.
curl_setopt($curl, CURLOPT_COOKIEJAR, COOKIE_JAR . COOKIE_FILE); // accept cookies.
curl_setopt($curl, CURLOPT_COOKIEFILE, COOKIE_JAR . COOKIE_FILE); // ditto.
curl_setopt($curl, CURLOPT_USERAGENT, USER_AGENT); // spoof user agent.
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // return details of the transfer to PHP.
curl_setopt($curl, CURLOPT_AUTOREFERER, true); // set referer headers.
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // follow redirects.
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // BAD -- disables ssl verify, but we aren't transmitting sensitive data so it's *kind of* okay.

// first, sanitize to ensure all keys are in $_GET
if (!params_exist($_GET)) {
	$response_code = 400;
	$error_body = "missing required parameters";
}

if ($curl !== FALSE) {
	// assemble a CL uri that can be used to field a search request
	// then get page.
	$uri = build_cl_uri($_GET);
	curl_setopt($curl, CURLOPT_URL, $uri);

	// execute CURL.
	$curl_response = curl_exec($curl);
	
	// if curl failed to execute, create a response.
	if (curl_errno($curl) !== 0) {
		$response_code = 503;
		$error_body = "curl failed with error code " . curl_errno($curl) . ": " . curl_error($curl);
	} else {
		// convert curl response to DOM object
		$dom = str_get_html($curl_response);
		$response_body = build_data_set($dom, $curl);
		
		// finally, check if the data set worked out.
		if ($dom === FALSE || $response_body === FALSE) {
			$response_code = 503;
			$error_body = "unable to parse html DOM";
		}
	}
	
	// close curl connection
	curl_close($curl);
} else if ($curl === FALSE) {
	// if curl failed to execute, create a response.
	$response_code = 503;
	$error_body = "curl failed with error code " . curl_errno($curl) . ": " . curl_error($curl);
}

// append URI to response body, so it's there
$response_body->request_uri = $uri;

// encapsulate response
$resp = new Response($response_code, $error_body, $response_body);
	
// output as json
print(json_encode($resp));
	
?>