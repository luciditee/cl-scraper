# EasyList

A simple, crude, public domain Craigslist data scraper that returns search results in a convenient in JSON format and behaves similar to an API with parameters specified via HTTP GET.

Requires PHP 5.3 or newer, and uses cURL.  Also utilizes SimpleHTMLDOM (http://simplehtmldom.sourceforge.net/), which is available under the MIT license.

*Please note that using bots to browse Craigslist is absolutely, positively, definitely against their terms of use, therefore I disown anyone seeking nefarious uses for this code.*

## Installation

Drop the files in any web-accessible directory on your server, then tweak the settings in `scrape.php` -- see below.

### scrape.php Settings
* `USER_AGENT` - Craigslist requires a user agent to browse their website.  I have specified my old Chrome/Windows 10 agent by default, you don't have to change this.
* `COOKIE_JAR` - A temporary, writable directory for cookies to go in, as Craigslist writes a cookie to identify unique users as well as to prevent casual bots.  **You will probably have to change this one.  TRAILING SLASH IS REQUIRED.**
* `COOKIE_FILE` - A filename to be placed within the specified `COOKIE_JAR` to represent the Craigslist cookie.  You don't have to change this one.

### Additional setup

By default, the script ignores SSL certificate information that Craigslist sends.  This is because cURL does not ship with a CA by default.

This is BAD PRACTICE, however, the code does this so that it works out of the box.  **It is highly recommended to change this.**

For details on how to fix your PHP configuration to add a CA, see [this helpful article](https://www.saotn.org/dont-turn-off-curlopt_ssl_verifypeer-fix-php-configuration/).

## Usage

Visit the domain and directory of the web server you uploaded it to, and invoke the API like so.

   http://your.domain/craigslist/scraper.php?region=yourcity&category=category&keywords=keywords+go+here

* `yourcity` is the craigslist most relevant to you, such as http://minneapolis.craigslist.org/ or http://grandrapids.craigslist.org/ .
* `category` is the internal search category for Craigslist listings where you will be searching.  For example, computer hardware is `cya`.  You can use the dropdown in the search menu on Craigslist and observe the category in the URL if you aren't sure what to put here.
* `keywords` is whatever search keywords you would search CL with, delimited+by+plus+signs.

## Contributing

Feel free to make pull requests.

If you commit changes, please understand that they are released to the public domain.  You will be given credit in this readme.
