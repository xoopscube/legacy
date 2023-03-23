<?php
/**
 * Snoopy - the PHP net client
 * @package    kernel
 * @subpackage util
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Monte Ohrt <monte@ohrt.com>
 * @copyright  (c) 1999-2014
 * @license    GPL 2.0
 */


class snoopy
{
    /**** Public variables ****/

    /* user definable vars */

    public $scheme = 'http'; // http or https
    public $host = 'www.php.net'; // host name we are connecting to
    public $port = 80; // port we are connecting to
    public $proxy_host = ''; // proxy host to use
    public $proxy_port = ''; // proxy port to use
    public $proxy_user = ''; // proxy user to use
    public $proxy_pass = ''; // proxy password to use

    public $agent = 'Snoopy v2.0.1'; // agent we masquerade as
    public $referer = ''; // referer info to pass
    public $cookies = []; // array of cookies to pass
    // $cookies["username"]="joe";
    public $rawheaders = []; // array of raw headers to send
    // $rawheaders["Content-type"]="text/html";

    public $maxredirs = 5; // http redirection depth maximum. 0 = disallow
    public $lastredirectaddr = ''; // contains address of last redirected address
    public $offsiteok = true; // allows redirection off-site
    public $maxframes = 0; // frame content depth maximum. 0 = disallow
    public $expandlinks = true; // expand links to fully qualified URLs.
    // this only applies to fetchlinks()
    // submitlinks(), and submittext()
    public $passcookies = true; // pass set cookies back through redirects
    // NOTE: this currently does not respect
    // dates, domains or paths.

    public $user = ''; // user for http authentication
    public $pass = ''; // password for http authentication

    // http accept types
    public $accept = 'image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, */*';

    public $results = ''; // where the content is put

    public $error = ''; // error messages sent here
    public $response_code = ''; // response code returned from server
    public $headers = []; // headers returned from server sent here
    public $maxlength = 500000; // max return data length (body)
    public $read_timeout = 0; // timeout on read operations, in seconds
    // supported only since PHP 4 Beta 4
    // set to 0 to disallow timeouts
    public $timed_out = false; // if a read operation timed out
    public $status = 0; // http request status

    public $temp_dir = '/tmp'; // temporary directory that the webserver
    // has permission to write to.
    // under Windows, this should be C:\temp

    public $curl_path = false;
    // deprecated, snoopy no longer uses curl for https requests,
    // but instead requires the openssl extension.

    // send Accept-encoding: gzip?
    public $use_gzip = true;

    // file or directory with CA certificates to verify remote host with
    public $cafile;
    public $capath;

    /**** Private variables ****/

    public $_maxlinelen = 4096; // max line length (headers)

    public $_httpmethod = 'GET'; // default http request method
    public $_httpversion = 'HTTP/1.0'; // default http request version
    public $_submit_method = 'POST'; // default submit method
    public $_submit_type = 'application/x-www-form-urlencoded'; // default submit type
    public $_mime_boundary = ''; // MIME boundary for multipart/form-data submit type
    public $_redirectaddr = false; // will be set if page fetched is a redirect
    public $_redirectdepth = 0; // increments on an http redirect
    public $_frameurls = []; // frame src urls
    public $_framedepth = 0; // increments on frame depth

    public $_isproxy = false; // set if using a proxy server
    public $_fp_timeout = 30; // timeout for socket connection

    /*======================================================================*\
        Function:	fetch
        Purpose:	fetch the contents of a web page
                    (and possibly other protocols in the
                    future like ftp, nntp, gopher, etc.)
        Input:		$URI	the location of the page to fetch
        Output:		$this->results	the output text from the fetch
    \*======================================================================*/

    public function fetch($URI)
    {
        $URI_PARTS = parse_url($URI);
        if (!empty($URI_PARTS['user'])) {
            $this->user = $URI_PARTS['user'];
        }
        if (!empty($URI_PARTS['pass'])) {
            $this->pass = $URI_PARTS['pass'];
        }
        if (empty($URI_PARTS['query'])) {
            $URI_PARTS['query'] = '';
        }
        if (empty($URI_PARTS['path'])) {
            $URI_PARTS['path'] = '';
        }

        $fp = null;

        switch (strtolower($URI_PARTS['scheme'])) {
            case 'https':
                if (!extension_loaded('openssl')) {
                    trigger_error('openssl extension required for HTTPS', E_USER_ERROR);
                    exit;
                }
                $this->port = 443;
	            break;
	        case 'http':
                $this->scheme = strtolower($URI_PARTS['scheme']);
                $this->host = $URI_PARTS['host'];
                if (!empty($URI_PARTS['port'])) {
                    $this->port = $URI_PARTS['port'];
                }
                if ($this->_connect($fp)) {
                    if ($this->_isproxy) {
                        // using proxy, send entire URI
                        $this->_httprequest($URI, $fp, $URI, $this->_httpmethod);
                    } else {
                        $path = $URI_PARTS['path'] . ($URI_PARTS['query'] ? '?' . $URI_PARTS['query'] : '');
                        // no proxy, send only the path
                        $this->_httprequest($path, $fp, $URI, $this->_httpmethod);
                    }

                    $this->_disconnect($fp);

                    /* url was redirected, check if we've hit the max depth */
                    if ($this->_redirectaddr && $this->maxredirs > $this->_redirectdepth) {
                        // only follow redirect if it's on this site, or offsiteok is true
                        if (preg_match('|^https?://' . preg_quote($this->host) . '|i', $this->_redirectaddr) || $this->offsiteok) {
                            /* follow the redirect */
                            $this->_redirectdepth++;
                            $this->lastredirectaddr = $this->_redirectaddr;
                            $this->fetch($this->_redirectaddr);
                        }
                    }

                    if ($this->_framedepth < $this->maxframes && count($this->_frameurls) > 0) {
                        $frameurls = $this->_frameurls;
                        $this->_frameurls = [];


                            foreach ($frameurls as $frameurl) {

                            if ($this->_framedepth < $this->maxframes) {
                                $this->fetch($frameurl);
                                $this->_framedepth++;
                            } else {
                                break;
                            }
                        }
                    }
                } else {
                    return false;
                }
                return $this;
                break;
            default:
                // not a valid protocol
                $this->error = 'Invalid protocol "' . $URI_PARTS['scheme'] . '"\n';
                return false;
                break;
        }
        return $this;
    }

    /*======================================================================*\
        Function:	submit
        Purpose:	submit an http(s) form
        Input:		$URI	the location to post the data
                    $formvars	the formvars to use.
                        format: $formvars["var"] = "val";
                    $formfiles  an array of files to submit
                        format: $formfiles["var"] = "/dir/filename.ext";
        Output:		$this->results	the text output from the post
    \*======================================================================*/

    public function submit($URI, $formvars = '', $formfiles = '')
    {
        unset($postdata);

        $postdata = $this->_prepare_post_body($formvars, $formfiles);

        $URI_PARTS = parse_url($URI);
        if (!empty($URI_PARTS['user'])) {
            $this->user = $URI_PARTS['user'];
        }
        if (!empty($URI_PARTS['pass'])) {
            $this->pass = $URI_PARTS['pass'];
        }
        if (empty($URI_PARTS['query'])) {
            $URI_PARTS['query'] = '';
        }
        if (empty($URI_PARTS['path'])) {
            $URI_PARTS['path'] = '';
        }

        switch (strtolower($URI_PARTS['scheme'])) {
            case 'https':
                if (!extension_loaded('openssl')) {
                    trigger_error('openssl extension required for HTTPS', E_USER_ERROR);
                    exit;
                }
                $this->port = 443;
            case 'http':
                $this->scheme = strtolower($URI_PARTS['scheme']);
                $this->host = $URI_PARTS['host'];
                if (!empty($URI_PARTS['port'])) {
                    $this->port = $URI_PARTS['port'];
                }
                if ($this->_connect($fp)) {
                    if ($this->_isproxy) {
                        // using proxy, send entire URI
                        $this->_httprequest($URI, $fp, $URI, $this->_submit_method, $this->_submit_type, $postdata);
                    } else {
                        $path = $URI_PARTS['path'] . ($URI_PARTS['query'] ? '?' . $URI_PARTS['query'] : '');
                        // no proxy, send only the path
                        $this->_httprequest($path, $fp, $URI, $this->_submit_method, $this->_submit_type, $postdata);
                    }

                    $this->_disconnect($fp);

                    /* url was redirected, check if we've hit the max depth */
                    if ($this->_redirectaddr && $this->maxredirs > $this->_redirectdepth) {
                        if (!preg_match('|^' . $URI_PARTS['scheme'] . '://|', $this->_redirectaddr)) {
                            $this->_redirectaddr = $this->_expandlinks($this->_redirectaddr, $URI_PARTS['scheme'] . '://' . $URI_PARTS['host']);
                        }

                        // only follow redirect if it's on this site, or offsiteok is true
                        if (preg_match('|^https?://' . preg_quote($this->host) . '|i', $this->_redirectaddr) || $this->offsiteok) {
                            /* follow the redirect */
                            $this->_redirectdepth++;
                            $this->lastredirectaddr = $this->_redirectaddr;
                            if (strpos($this->_redirectaddr, '?') > 0) {
                                $this->fetch($this->_redirectaddr);
                            } // the redirect has changed the request method from post to get
                            else {
                                $this->submit($this->_redirectaddr, $formvars, $formfiles);
                            }
                        }
                    }

                    if ($this->_framedepth < $this->maxframes && count($this->_frameurls) > 0) {
                        $frameurls = $this->_frameurls;
                        $this->_frameurls = [];

                        foreach ($frameurls as $frameurl) {
                            if ($this->_framedepth < $this->maxframes) {
                                $this->fetch($frameurl);
                                $this->_framedepth++;
                            } else {
                                break;
                            }
                        }
                    }
                } else {
                    return false;
                }
                return $this;
                break;
            default:
                // not a valid protocol
                $this->error = 'Invalid protocol "' . $URI_PARTS['scheme'] . '"\n';
                return false;
                break;
        }
        return $this;
    }

    /*======================================================================*\
        Function:	fetchlinks
        Purpose:	fetch the links from a web page
        Input:		$URI	where you are fetching from
        Output:		$this->results	an array of the URLs
    \*======================================================================*/

    public function fetchlinks($URI)
    {
        if (false !== $this->fetch($URI)) {
            if ($this->lastredirectaddr) {
                $URI = $this->lastredirectaddr;
            }
            if (is_array($this->results)) {
                foreach ($this->results as $x => $xValue) {
                    $this->results[$x] = $this->_striplinks($xValue);
                }
            } else {
                $this->results = $this->_striplinks($this->results);
            }

            if ($this->expandlinks) {
                $this->results = $this->_expandlinks($this->results, $URI);
            }
            return $this;
        }

        return false;
    }

    /*======================================================================*\
        Function:	fetchform
        Purpose:	fetch the form elements from a web page
        Input:		$URI	where you are fetching from
        Output:		$this->results	the resulting html form
    \*======================================================================*/

    public function fetchform($URI)
    {
        if (false !== $this->fetch($URI)) {
            if (is_array($this->results)) {
                foreach ($this->results as $x => $xValue) {
                    $this->results[$x] = $this->_stripform($xValue);
                }
            } else {
                $this->results = $this->_stripform($this->results);
            }

            return $this;
        }

        return false;
    }


    /*======================================================================*\
        Function:	fetchtext
        Purpose:	fetch the text from a web page, stripping the links
        Input:		$URI	where you are fetching from
        Output:		$this->results	the text from the web page
    \*======================================================================*/

    public function fetchtext($URI)
    {
        if (false !== $this->fetch($URI)) {
            if (is_array($this->results)) {
                foreach ($this->results as $x => $xValue) {
                    $this->results[$x] = $this->_striptext($xValue);
                }
            } else {
                $this->results = $this->_striptext($this->results);
            }
            return $this;
        }

        return false;
    }

    /*======================================================================*\
        Function:	submitlinks
        Purpose:	grab links from a form submission
        Input:		$URI	where you are submitting from
        Output:		$this->results	an array of the links from the post
    \*======================================================================*/

    public function submitlinks($URI, $formvars = '', $formfiles = '')
    {
        if (false !== $this->submit($URI, $formvars, $formfiles)) {
            if ($this->lastredirectaddr) {
                $URI = $this->lastredirectaddr;
            }
            if (is_array($this->results)) {
                foreach ($this->results as $x => $xValue) {
                    $this->results[$x] = $this->_striplinks($xValue);
                    if ($this->expandlinks) {
                        $this->results[$x] = $this->_expandlinks($xValue, $URI);
                    }
                }
            } else {
                $this->results = $this->_striplinks($this->results);
                if ($this->expandlinks) {
                    $this->results = $this->_expandlinks($this->results, $URI);
                }
            }
            return $this;
        }

        return false;
    }

    /*======================================================================*\
        Function:	submittext
        Purpose:	grab text from a form submission
        Input:		$URI	where you are submitting from
        Output:		$this->results	the text from the web page
    \*======================================================================*/

    public function submittext($URI, $formvars = '', $formfiles = '')
    {
        if (false !== $this->submit($URI, $formvars, $formfiles)) {
            if ($this->lastredirectaddr) {
                $URI = $this->lastredirectaddr;
            }
            if (is_array($this->results)) {
                foreach ($this->results as $x => $xValue) {
                    $this->results[$x] = $this->_striptext($xValue);
                    if ($this->expandlinks) {
                        $this->results[$x] = $this->_expandlinks($xValue, $URI);
                    }
                }
            } else {
                $this->results = $this->_striptext($this->results);
                if ($this->expandlinks) {
                    $this->results = $this->_expandlinks($this->results, $URI);
                }
            }
            return $this;
        }

        return false;
    }


    /*======================================================================*\
        Function:	set_submit_multipart
        Purpose:	Set the form submission content type to
                    multipart/form-data
    \*======================================================================*/
    public function set_submit_multipart()
    {
        $this->_submit_type = 'multipart/form-data';
        return $this;
    }


    /*======================================================================*\
        Function:	set_submit_normal
        Purpose:	Set the form submission content type to
                    application/x-www-form-urlencoded
    \*======================================================================*/
    public function set_submit_normal()
    {
        $this->_submit_type = 'application/x-www-form-urlencoded';
        return $this;
    }




    /*======================================================================*\
        Private functions
    \*======================================================================*/


    /*======================================================================*\
        Function:	_striplinks
        Purpose:	strip the hyperlinks from an html document
        Input:		$document	document to strip.
        Output:		$match		an array of the links
    \*======================================================================*/

    public function _striplinks($document)
    {
        preg_match_all("'<\s*a\s.*?href\s*=\s*			# find <a href=
						([\"\'])?					# find single or double quote
						(?(1) (.*?)\\1 | ([^\s\>]+))		# if quote found, match up to next matching
													# quote, otherwise match up to next space
						'isx", $document, $links);


        // catenate the non-empty matches from the conditional subpattern

        foreach ($links[2] as $val) {
            if (!empty($val)) {
                $match[] = $val;
            }
        }

        foreach ($links[3] as $val) {
            if (!empty($val)) {
                $match[] = $val;
            }
        }

        // return the links
        return $match;
    }

    /*======================================================================*\
        Function:	_stripform
        Purpose:	strip the form elements from an html document
        Input:		$document	document to strip.
        Output:		$match		an array of the links
    \*======================================================================*/

    public function _stripform($document)
    {
        preg_match_all("'<\/?(FORM|INPUT|SELECT|TEXTAREA|(OPTION))[^<>]*>(?(2)(.*(?=<\/?(option|select)[^<>]*>[\r\n]*)|(?=[\r\n]*))|(?=[\r\n]*))'Usi", $document, $elements);

        // catenate the matches
        // return the links
        return implode("\r\n", $elements[0]);
    }


    /*======================================================================*\
        Function:	_striptext
        Purpose:	strip the text from an html document
        Input:		$document	document to strip.
        Output:		$text		the resulting text
    \*======================================================================*/

    public function _striptext($document)
    {

        // I didn't use preg eval (//e) since that is only available in PHP 4.0.
        // so, list your entities one by one here. I included some of the
        // more common ones.

        $search = [
            "'<script[^>]*?>.*?</script>'si", // strip out javascript
            "'<[\/\!]*?[^<>]*?>'si", // strip out html tags
            "'([\r\n])[\s]+'", // strip out white space
            "'&(quot|#34|#034|#x22);'i", // replace html entities
            "'&(amp|#38|#038|#x26);'i", // added hexadecimal values
            "'&(lt|#60|#060|#x3c);'i",
            "'&(gt|#62|#062|#x3e);'i",
            "'&(nbsp|#160|#xa0);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&(reg|#174);'i",
            "'&(deg|#176);'i",
            "'&(#39|#039|#x27);'",
            "'&(euro|#8364);'i", // europe
            "'&a(uml|UML);'", // german
            "'&o(uml|UML);'",
            "'&u(uml|UML);'",
            "'&A(uml|UML);'",
            "'&O(uml|UML);'",
            "'&U(uml|UML);'",
            "'&szlig;'i",
        ];
        $replace = [
            '',
            '',
            "\\1",
            '"',
            '&',
            '<',
            '>',
            ' ',
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            chr(174),
            chr(176),
            chr(39),
            chr(128),
            /*
             * use CHR code for UTF-8
             * Marijuana
             */
            chr(228),
            chr(246),
            chr(252),
            chr(196),
            chr(214),
            chr(220),
            chr(223),
        ];

        return preg_replace($search, $replace, $document);
    }

    /*======================================================================*\
        Function:	_expandlinks
        Purpose:	expand each link into a fully qualified URL
        Input:		$links			the links to qualify
                    $URI			the full URI to get the base from
        Output:		$expandedLinks	the expanded links
    \*======================================================================*/

    public function _expandlinks($links, $URI)
    {
        preg_match("/^[^\?]+/", $URI, $match);

        $match = preg_replace("|/[^\/\.]+\.[^\/\.]+$|", '', $match[0]);
        $match = preg_replace('|/$|', '', $match);
        $match_part = parse_url($match);
        $match_root =
            $match_part['scheme'] . '://' . $match_part['host'];

        $search = [
            '|^https://' . preg_quote($this->host) . '|i',
            "|^(\/)|i",
            '|^(?!https://)(?!mailto:)|i',
            "|/\./|",
            "|/[^\/]+/\.\./|"
        ];

        $replace = [
            '',
            $match_root . '/',
            $match . '/',
            '/',
            '/'
        ];

        return preg_replace($search, $replace, $links);
    }

    /*======================================================================*\
        Function:	_httprequest
        Purpose:	go get the http(s) data from the server
        Input:		$url		the url to fetch
                    $fp			the current open file pointer
                    $URI		the full URI
                    $body		body contents to send if any (POST)
        Output:
    \*======================================================================*/

    public function _httprequest($url, $fp, $URI, $http_method, $content_type = '', $body = '')
    {
        $cookie_headers = '';
        if ($this->passcookies && $this->_redirectaddr) {
            $this->setcookies();
        }

        $URI_PARTS = parse_url($URI);
        if (empty($url)) {
            $url = '/';
        }
        $headers = $http_method . ' ' . $url . ' ' . $this->_httpversion . "\r\n";
        if (!empty($this->host) && !isset($this->rawheaders['Host'])) {
            $headers .= 'Host: ' . $this->host;
            if (!empty($this->port) && '80' !== $this->port) {
                $headers .= ':' . $this->port;
            }
            $headers .= "\r\n";
        }
        if (!empty($this->agent)) {
            $headers .= 'User-Agent: ' . $this->agent . "\r\n";
        }
        if (!empty($this->accept)) {
            $headers .= 'Accept: ' . $this->accept . "\r\n";
        }
        if ($this->use_gzip) {
            // make sure PHP was built with --with-zlib
            // and we can handle gzipp'ed data
            if (function_exists('gzinflate')) {
                $headers .= "Accept-encoding: gzip\r\n";
            } else {
                trigger_error(
                    'use_gzip is on, but PHP was built without zlib support.' . '  Requesting file(s) without gzip encoding.',
                    E_USER_NOTICE);
            }
        }
        if (!empty($this->referer)) {
            $headers .= 'Referer: ' . $this->referer . "\r\n";
        }
        if (!empty($this->cookies)) {
            if (!is_array($this->cookies)) {
                $this->cookies = (array)$this->cookies;
            }

            reset($this->cookies);
            if (count($this->cookies) > 0) {
                $cookie_headers .= 'Cookie: ';
                foreach ($this->cookies as $cookieKey => $cookieVal) {
                    $cookie_headers .= $cookieKey . '=' . urlencode($cookieVal) . '; ';
                }
                $headers .= substr($cookie_headers, 0, -2) . "\r\n";
            }
        }
        if (!empty($this->rawheaders)) {
            if (!is_array($this->rawheaders)) {
                $this->rawheaders = (array)$this->rawheaders;
            }
            foreach ($this->rawheaders as $headerKey => $headerVal) {
                $headers .= $headerKey . ': ' . $headerVal . "\r\n";
            }
        }
        if (!empty($content_type)) {
            $headers .= "Content-type: $content_type";
            if ('multipart/form-data' === $content_type) {
                $headers .= '; boundary=' . $this->_mime_boundary;
            }
            $headers .= "\r\n";
        }
        if (!empty($body)) {
            $headers .= 'Content-length: ' . strlen($body) . "\r\n";
        }
        if (!empty($this->user) || !empty($this->pass)) {
            $headers .= 'Authorization: Basic ' . base64_encode($this->user . ':' . $this->pass) . "\r\n";
        }

        //add proxy auth headers
        if (!empty($this->proxy_user)) {
            $headers .= 'Proxy-Authorization: ' . 'Basic ' . base64_encode($this->proxy_user . ':' . $this->proxy_pass) . "\r\n";
        }


        $headers .= "\r\n";

        // set the read timeout if needed
        if ($this->read_timeout > 0) {
            stream_set_timeout($fp, $this->read_timeout);
        }
        $this->timed_out = false;

        fwrite($fp, $headers . $body, strlen($headers . $body));

        $this->_redirectaddr = false;
        unset($this->headers);

        // content was returned gzip encoded?
        $is_gzipped = false;

        while ($currentHeader = fgets($fp, $this->_maxlinelen)) {
            if ($this->read_timeout > 0 && $this->_check_timeout($fp)) {
                $this->status = -100;
                return false;
            }

            if ("\r\n" == $currentHeader) {
                break;
            }

            // if a header begins with Location: or URI:, set the redirect
            if (preg_match('/^(Location:|URI:)/i', $currentHeader)) {
                // get URL portion of the redirect
                preg_match('/^(Location:|URI:)[ ]+(.*)/i', rtrim($currentHeader), $matches);
                // look for :// in the Location header to see if hostname is included
                if (!preg_match("|\:\/\/|", $matches[2])) {
                    // no host in the path, so prepend
                    $this->_redirectaddr = $URI_PARTS['scheme'] . '://' . $this->host . ':' . $this->port;
                    // eliminate double slash
                    if (!preg_match('|^/|', $matches[2])) {
                        $this->_redirectaddr .= '/' . $matches[2];
                    } else {
                        $this->_redirectaddr .= $matches[2];
                    }
                } else {
                    $this->_redirectaddr = $matches[2];
                }
            }

            if (preg_match('|^HTTP/|', $currentHeader)) {
                if (preg_match("|^HTTP/[^\s]*\s(.*?)\s|", $currentHeader, $status)) {
                    $this->status = $status[1];
                }
                $this->response_code = $currentHeader;
            }

            //!Fix to test
            // The check for gzip-encoded content in responses should be case-insensitive, per RFC 2616.
            // Patch by Evan Anderson
            // - if (preg_match('/Content-Encoding: gzip/', $currentHeader)) {

            if (preg_match("/Content-Encoding: gzip/i", $currentHeader)) {
                $is_gzipped = true;
            }

            $this->headers[] = $currentHeader;
        }

        $results = '';
        do {
            $_data = fread($fp, $this->maxlength);
            if ($_data == '') {
                break;
            }
            $results .= $_data;
        } while (true);

        // gunzip
        if ($is_gzipped) {
            // per https://www.php.net/manual/en/function.gzencode.php
            $results = substr($results, 10);
            $results = gzinflate($results);
        }

        if ($this->read_timeout > 0 && $this->_check_timeout($fp)) {
            $this->status = -100;
            return false;
        }

        // check if there is a a redirect meta tag

        if (preg_match("'<meta[\s]*http-equiv[^>]*?content[\s]*=[\s]*[\"\']?\d+;[\s]*URL[\s]*=[\s]*([^\"\']*?)[\"\']?>'i", $results, $match)) {
            $this->_redirectaddr = $this->_expandlinks($match[1], $URI);
        }

        // have we hit our frame depth and is there frame src to fetch?
        if (($this->_framedepth < $this->maxframes) && preg_match_all("'<frame\s+.*src[\s]*=[\'\"]?([^\'\"\>]+)'i", $results, $match)) {
            $this->results[] = $results;
            foreach ($match[1] as $xValue) {
                $this->_frameurls[] = $this->_expandlinks($xValue, $URI_PARTS['scheme'] . '://' . $this->host);
            }
        } // have we already fetched framed content?
        elseif (is_array($this->results)) {
            $this->results[] = $results;
        }
        // no framed content
        else {
            $this->results = $results;
        }

        return $this;
    }

    /*======================================================================*\
        Function:	setcookies()
        Purpose:	set cookies for a redirection
    \*======================================================================*/

    public function setcookies()
    {
        foreach ($this->headers as $x => $xValue) {
            if (preg_match('/^set-cookie:[\s]+([^=]+)=([^;]+)/i', $this->headers[$x], $match)) {
                $this->cookies[$match[1]] = urldecode($match[2]);
            }
        }
        return $this;
    }


    /*======================================================================*\
        Function:	_check_timeout
        Purpose:	checks whether timeout has occurred
        Input:		$fp	file pointer
    \*======================================================================*/

    public function _check_timeout($fp)
    {
        if ($this->read_timeout > 0) {
            $fp_status = stream_get_meta_data($fp);
            if ($fp_status['timed_out']) {
                $this->timed_out = true;
                return true;
            }
        }
        return false;
    }

    /*======================================================================*\
        Function:	_connect
        Purpose:	make a socket connection
        Input:		$fp	file pointer
    \*======================================================================*/

    public function _connect(&$fp)
    {
        if (!empty($this->proxy_host) && !empty($this->proxy_port)) {
            $this->_isproxy = true;

            $host = $this->proxy_host;
            $port = $this->proxy_port;

            if ('https' == $this->scheme) {
                trigger_error('HTTPS connections over proxy are currently not supported', E_USER_ERROR);
                exit;
            }
        } else {
            $host = $this->host;
            $port = $this->port;
        }

        $this->status = 0;

        $context_opts = [];

        if ('https' == $this->scheme) {
            // if cafile or capath is specified, enable certificate
            // verification (including name checks)
            if (isset($this->cafile) || isset($this->capath)) {
                $context_opts['ssl'] = [
                    'verify_peer' => true,
                    'CN_match' => $this->host,
                    'disable_compression' => true,
                ];

                if (isset($this->cafile)) {
                    $context_opts['ssl']['cafile'] = $this->cafile;
                }
                if (isset($this->capath)) {
                    $context_opts['ssl']['capath'] = $this->capath;
                }
            }

            $host = 'ssl://' . $host;
        }

        $context = stream_context_create($context_opts);

        if (PHP_VERSION_ID > 50000) {
            if($this->scheme == 'http'){
                $port = 80;
                $host = "tcp://" . $host;
            }

//            if ('http' == $this->scheme) {
//                $host = 'tcp://' . $host;
//            }
            $fp = stream_socket_client(
                "$host:$port",
                $errno,
                $errmsg,
                $this->_fp_timeout,
                STREAM_CLIENT_CONNECT,
                $context);
        } else {
            //!Fix
            // TODO - Test
            $fp = fsockopen(
                $host,
                $port,
                $errno,
                $errstr,
                $this->_fp_timeout,
                $context);
        }

        if ($fp) {
            // socket connection succeeded
            return true;
        }

// socket connection failed
        $this->status = $errno;
        switch ($errno) {
            case -3:
                $this->error = 'socket creation failed (-3)';
                break;
            case -4:
                $this->error = 'dns lookup failure (-4)';
                break;
            case -5:
                $this->error = 'connection refused or timed out (-5)';
                break;
            default:
                $this->error = 'connection failed (' . $errno . ')';
        }
        return false;
    }

    /*======================================================================*\
        Function:	_disconnect
        Purpose:	disconnect a socket connection
        Input:		$fp	file pointer
    \*======================================================================*/

    public function _disconnect($fp)
    {
        return (fclose($fp));
    }


    /*======================================================================*\
        Function:	_prepare_post_body
        Purpose:	Prepare post body according to encoding type
        Input:		$formvars  - form variables
                    $formfiles - form upload files
        Output:		post body
    \*======================================================================*/

    public function _prepare_post_body($formvars, $formfiles)
    {
        $formvars  = (array)$formvars;
        $formfiles = (array)$formfiles;
        $postdata  = '';

        if (0 == count($formvars) && 0 == count($formfiles)) {
            return;
        }

        switch ($this->_submit_type) {
            case 'application/x-www-form-urlencoded':
                reset($formvars);
                foreach ($formvars as $key => $val) {
                    if (is_array($val) || is_object($val)) {
                        foreach ($val as $cur_key => $cur_val) {
                        $postdata .= urlencode($key) ."[$cur_key]=". urlencode($cur_val)."&";
                        //!Fix Todo - test this : $postdata .= urlencode($key) ."%5B$cur_key%5D=". urlencode($cur_val)."&";
                        }
                    } else {
                        $postdata .= urlencode($key) . '=' . urlencode($val) . '&';
                    }
                }
                $postdata = substr($postdata, 0, strlen($postdata) - 1);
                break;

            case 'multipart/form-data':
                $this->_mime_boundary = 'Snoopy' . md5(uniqid(microtime(), true));

                reset($formvars);
                foreach ($formvars as $key => $val) {
                    if (is_array($val) || is_object($val)) {
                        foreach ($val as $cur_val) {
                            $postdata .= '--' . $this->_mime_boundary . "\r\n";
                            $postdata .= "Content-Disposition: form-data; name=\"$key\[\]\"\r\n\r\n";
                            $postdata .= "$cur_val\r\n";
                        }
                    } else {
                        $postdata .= '--' . $this->_mime_boundary . "\r\n";
                        $postdata .= "Content-Disposition: form-data; name=\"$key\"\r\n\r\n";
                        $postdata .= "$val\r\n";
                    }
                }

                reset($formfiles);
                foreach ($formfiles as $field_name => $file_names) {
                    $file_names = (array)$file_names;
                    foreach ($file_names as $file_name) {
                        if (!is_readable($file_name)) {
                            continue;
                        }

                        $fp = fopen($file_name, 'r');
                        $file_content = fread($fp, filesize($file_name));
                        fclose($fp);
                        $base_name = basename($file_name);

                        $postdata .= '--' . $this->_mime_boundary . "\r\n";
                        $postdata .= "Content-Disposition: form-data; name=\"$field_name\"; filename=\"$base_name\"\r\n\r\n";
                        $postdata .= "$file_content\r\n";
                    }
                }
                $postdata .= '--' . $this->_mime_boundary . "--\r\n";
                break;
            // XOOPS2 Hack begin
            // Added on March 4, 2003 by onokazu@xoops.org
            case 'text/xml':
            default:
                $postdata = $formvars[0];
                break;
            // XOOPS2 Hack end
        }

        return $postdata;
    }

    /*======================================================================*\
    Function:	getResults
    Purpose:	return the results of a request
    Output:		string results
    \*======================================================================*/

    public function getResults()
    {
        return $this->results;
    }
}
