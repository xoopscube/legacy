<?php
/*
 * Created on 2008/02/11 by nao-pon http://hypweb.net/
 * $Id: favicon.php,v 1.19 2011/11/22 09:07:53 nao-pon Exp $
 */

/**
 * favicon.php - Outputs the cached favicon with proper headers
 *
 * Copyright (c) 2007-2009 revulo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @author     revulo <revulon@gmail.com>
 * @licence    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    1.4
 * @link       http://www.revulo.com/PukiWiki/Plugin/Favicon.html
 */

ignore_user_abort(FALSE);
error_reporting(0);

define('FAVICON_TRUST_PATH' , dirname(__FILE__));
define('FAVICON_HYP_COMMON_PATH', dirname(FAVICON_TRUST_PATH));

if (is_file(FAVICON_HYP_COMMON_PATH . '/config/favicon.conf.php')) {
	include FAVICON_HYP_COMMON_PATH . '/config/favicon.conf.php';
} else {
	define('FAVICON_DEFAULT_IMAGE', FAVICON_TRUST_PATH . '/images/world_go.png');
	define('FAVICON_ERROR_IMAGE',   FAVICON_TRUST_PATH . '/images/link_break.png');
	define('FAVICON_CACHE_DIR',     FAVICON_TRUST_PATH . '/cache/');
	define('FAVICON_CACHE_TTL',     2592000);  // 60 * 60 * 24 * 30 [sec.] (1 month)
	define('FAVICON_SHORTEN_URLS', 'http://bit.ly http://tinyurl.com');
}
define('UNIX_TIME', (isset($_SERVER['REQUEST_TIME'])? $_SERVER['REQUEST_TIME'] : time()));

if (! defined('FAVICON_ADMIN_MODE')) define('FAVICON_ADMIN_MODE', FALSE);
if (! defined('HYP_X_SENDFILE_MODE')) define('HYP_X_SENDFILE_MODE', 0);

function get_favicon($url)
{
    if (! $url || ! is_url($url)) return false;
    if (UNIX_TIME <= get_timestamp($url) + FAVICON_CACHE_TTL) {
        $cache = get_url_filename($url);
        return file_get_contents($cache);
    } else {
        return update_cache($url);
    }
}

function get_timestamp($url)
{
    static $time;

    if (empty($time)) {
        $filename = get_url_filename($url);
        $time     = (int)filemtime($filename);
    }
    return $time;
}

function get_url_filename($url)
{
    static $filename;

    if (empty($filename)) {
        list($url) = explode('?', $url);
        if (preg_match('#^https?://[^/]+?/#i', $url)) {
        	$_url = preg_replace('#/[^/]*$#', '', $url);
        	if (defined('FAVICON_SHORTEN_URLS') && ! in_array($_url, explode(' ', FAVICON_SHORTEN_URLS))) {
        		$url = $_url;
        	}
        }
        $filename = FAVICON_CACHE_DIR . md5($url) . '.url';
    }
    return $filename;
}

function get_image_filename($icon)
{
    static $filename;

    if (empty($filename)) {
        if ($icon === 'DefaultIcon') {
        	$filename = FAVICON_DEFAULT_IMAGE;
        } else if ($icon === 'ErrorIcon') {
        	$filename = FAVICON_ERROR_IMAGE;
        } else {
            $filename = FAVICON_CACHE_DIR . $icon;
        }
    }
    return $filename;
}

function if_modified_since()
{
    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
        $str = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
        if (($pos = strpos($str, ';')) !== false) {
            $str = substr($str, 0, $pos);
        }
        if (strpos($str, ',') === false) {
            $str .= ' GMT';
        }
        $time = strtotime($str);
    }

    if (isset($time) && is_int($time)) {
        return $time;
    } else {
        return -1;
    }
}

function output_image($icon, $time = 0)
{
    $filename = get_image_filename($icon);
    $mime = get_mimetype_from_name($icon);

    if (function_exists('mb_http_output')) {
        mb_http_output('pass');
    }

    if ($time) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time) . ' GMT');
        header('Cache-Control: public, max-age=' . FAVICON_CACHE_TTL);
        header('Expires: ' . gmdate( "D, d M Y H:i:s", UNIX_TIME + FAVICON_CACHE_TTL ) . ' GMT');
    }
    header('Etag: '. $time);
    header('Content-Length: ' . filesize($filename));
    header('Content-Type: ' . $mime);
    if ( HYP_X_SENDFILE_MODE >= 2 ) {
		header('X-Sendfile: ' . $filename);
    } else if ( HYP_X_SENDFILE_MODE === 1 ) {
    	header('X-LIGHTTPD-send-file: ' . $filename);
    } else {
    	@readfile($filename);
    }
    exit();
}


function update_cache($url)
{
    if (! is_writable(FAVICON_CACHE_DIR)) {
    	// cache dir was not writable
    	return false;
    }

    // Garbage Collection
    $garbage = FAVICON_CACHE_DIR . '.garbage.time';
    if (! is_file($garbage) || filemtime($garbage) + 86400 < UNIX_TIME) {
    	include_once dirname(dirname(__FILE__)) . '/hyp_common_func.php';
    	HypCommonFunc::touch($garbage);
    	clear_cache();
    }

	$url_org = $url;
    $html = http_get_contents($url, 4096);
    //$_url = $url;
    //check_group($url);
	//if ($url !== $_url) {
	//	$html = http_get_contents($url, 4096);
	//}
    if ($html === false) {        // connection failed or timed out
        $favicon = 'DefaultIcon';
    } else if ($html === null) {  // 404 status code or unsupported scheme
        $favicon = 'ErrorIcon';
    } else {
        $url  = parse_url($url);
        $base = $url['scheme'] . '://' . $url['host'] . (isset($url['port']) ? ':' . $url['port'] : '');
        $url  = $base . (isset($url['path']) ? $url['path'] : '/');
        if (preg_match('/<link ([^>]*)rel=[\'"]?(?:shortcut )?icon[\'"]?([^>]*)/si', $html, $matches)) {
            $link = implode(' ', $matches);

            if (preg_match('/href=[\'"]?(https?:\/\/)?([^\'" ]+)/si', $link, $matches)) {
                $favicon = $matches[2];
                if ($matches[1]) {
                    $favicon = $matches[1] . $favicon;
                } else if ($favicon[0] === '/') {
                    $favicon = $base . $favicon;
                } else if (substr($url, -1) === '/') {
                    $favicon = $url . $favicon;
                } else {
                    $favicon = dirname($url) . '/' . $favicon;
                }
                str_replace('/./', '/', $favicon);
                while(preg_match('#[^/]+/\.\./#', $favicon)) {
                	$favicon = preg_replace('#[^/]+/\.\./#', '', $favicon);
                }
            }
        }
        if (empty($favicon)) {
            $favicon = $base . '/favicon.ico';
        }

        $data = http_get_contents($favicon);
        if ($data === false) {                   // connection failed or timed out
            return false;
        } else if ($ext = get_extention($data)) {
            $favicon = md5($url) . $ext;
            $image = get_image_filename($favicon);
            if (file_put_contents($image, $data) === FALSE) {
            	$favicon = 'ErrorIcon';
            }
        } else {
        	// no favicon or unknown format
            $favicon = 'DefaultIcon';
        }
    }

    $filename = get_url_filename($url_org);
    if (file_put_contents($filename, $favicon)) {
    	return $favicon;
    } else {
    	return false;
    }
}

function http_get_contents(& $url, $size = 0)
{
    file_put_contents(get_url_filename($url), 'DefaultIcon');

	include_once dirname(dirname(__FILE__)) . '/hyp_common_func.php';

	$ht = new Hyp_HTTP_Request();
	$ht->init();
	$ht->url = $url;
	if ($size) $ht->getSize = $size;
	$ht->ua = 'Mozilla/5.0';
	$ht->connect_timeout = 2;
	$ht->read_timeout = 5;
	$ht->get();
	if ($size) $url = $ht->url;
	return ($ht->rc == 404 || $ht->rc == 410 || $ht->rc > 600 || $ht->rc < 100)? null : $ht->data;
}

function get_mimetype($data)
{
    if (strncmp("\x00\x00\x01\x00", $data, 4) === 0) {
        // ICO
        return 'image/x-icon';
    } else if (strncmp("\x89PNG\x0d\x0a\x1a\x0a", $data, 8) === 0) {
        // PNG
        return 'image/png';
    } else if (strncmp('GIF87a', $data, 6) === 0 || strncmp('GIF89a', $data, 6) === 0) {
        // GIF
        return 'image/gif';
    } else if (strncmp("\xff\xd8", $data, 2) === 0) {
        // JPEG
        return 'image/jpeg';
    } else {
        return false;
    }
}

function get_extention($data)
{
    if (strncmp("\x00\x00\x01\x00", $data, 4) === 0) {
        // ICO
        return '.ico';
    } else if (strncmp("\x89PNG\x0d\x0a\x1a\x0a", $data, 8) === 0) {
        // PNG
        return '.png';
    } else if (strncmp('GIF87a', $data, 6) === 0 || strncmp('GIF89a', $data, 6) === 0) {
        // GIF
        return '.gif';
    } else if (strncmp("\xff\xd8", $data, 2) === 0) {
        // JPEG
        return '.jpg';
    }
    return false;
}

function get_mimetype_from_name($favicon)
{
    $ext = substr($favicon, -3);
    if ($ext === 'ico') {
        // ICO
        return 'image/x-icon';
    } else if ($ext === 'png') {
        // PNG
        return 'image/png';
    } else if ($ext === 'gif') {
        // GIF
        return 'image/gif';
    } else if ($ext === 'jpg') {
        // JPEG
        return 'image/jpeg';
    } else {
        return 'image/x-icon';
    }
}

function is_url(& $url)
{

	if ($url[0] === '/') {
		$p_url  = parse_url(XOOPS_URL);
        $base = $p_url['scheme'] . '://' . $p_url['host'] . (isset($p_url['port']) ? ':' . $p_url['port'] : '');
        $url = $base . '/' . ltrim(dirname(preg_replace('/(\?|#).*/', '', $url)), '/');
	} else if ($url[0] === '.') {
		$url = XOOPS_URL  . '/';
	} else {
		$p_url = parse_url($url);
		$url = $p_url['scheme'] . '://' . $p_url['host'] . (isset($p_url['port']) ? ':' . $p_url['port'] : '') . $p_url['path'];
	}

	$url = preg_replace('/index\.[^.]+$/i', '', $url);

	check_group($url);

	$url = preg_replace('/([" \x80-\xff]+)/e', 'rawurlencode("$1")', $url);
	return (preg_match('/(?:https?|ftp|news):\/\/[!~*\'();\/?:\@&=+\$,%#\w.-]+/', $url));
}

function check_group(& $url) {
	$hosts = get_hosts();
	if ($hosts) {
		$p_url = parse_url($url);
		$_parts = explode('.', $p_url['host']);
		while ($_parts) {
			$_host = join('.', $_parts);
			if (isset($hosts[$_host])) {
				if (defined('XOOPS_URL') && $url !== $hosts[$_host]) {
					//header('Etag: '. md5($url));
					//header('Cache-Control: public, max-age=' . FAVICON_CACHE_TTL );
					//header('Expires: ' . gmdate( "D, d M Y H:i:s", UNIX_TIME + FAVICON_CACHE_TTL ) . ' GMT');
					header('HTTP/1.1 301 Moved Permanently');
					header('Location:' . XOOPS_URL . '/class/hyp_common/favicon.php?url='.rawurlencode($hosts[$_host]));
					exit();
				}
				$url = $hosts[$_host];
				break;
			}
			array_shift($_parts);
		}
	}
}

function get_hosts() {
	static $hosts = array();

	if ($hosts) return $hosts;

	$cache = FAVICON_CACHE_DIR . '.group.hosts';
	if (is_file($cache)) {
		 $mtime = filemtime($cache);
		 $checktime = filemtime(FAVICON_HYP_COMMON_PATH . '/dat/favicon_hostsgroup.dat');
		 if (is_file(FAVICON_HYP_COMMON_PATH . '/group.hosts')) {
		 	$checktime = max($checktime, filemtime(FAVICON_HYP_COMMON_PATH . '/group.hosts'));
		 }
		 if ($mtime > $checktime) {
		 	return unserialize(file_get_contents($cache));
		 }
	}

	$_hosts = file(FAVICON_HYP_COMMON_PATH . '/dat/favicon_hostsgroup.dat');
	if (is_file(FAVICON_HYP_COMMON_PATH . '/config/favicon_hostsgroup.dat')) {
		$_hosts = array_merge($_hosts, file(FAVICON_HYP_COMMON_PATH . '/config/favicon_hostsgroup.dat'));
	}
	if ($_hosts) {
		foreach($_hosts as $host) {
			list($from, $to) = explode(' ', $host);
			$hosts[trim($from)] = trim($to);
		}
	}
	file_put_contents($cache, serialize($hosts));
	return $hosts;
}

function redirect_icon($url)
{
	$p_url  = parse_url(XOOPS_URL);
    $base = $p_url['scheme'] . '://' . $p_url['host'] . (isset($p_url['port']) ? ':' . $p_url['port'] : '');
	$uri = preg_replace('/url=[^&]+/', 'icon=' . rawurlencode($url), $_SERVER['REQUEST_URI']);
	header('Cache-Control: public, max-age=' . FAVICON_CACHE_TTL );
	header('Expires: ' . gmdate( "D, d M Y H:i:s", UNIX_TIME + FAVICON_CACHE_TTL ) . ' GMT');
	header('Location: '.$base . $uri);
	exit();
}

function output_icon($icon) {

	$time = filemtime(get_image_filename($icon));

	if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && $time == $_SERVER['HTTP_IF_NONE_MATCH'])
	   || $time <= if_modified_since()) {
	    header('HTTP/1.1 304 Not Modified');
	    header('Etag: '. $time);
	    header('Cache-Control: public, max-age=' . FAVICON_CACHE_TTL );
	    header('Expires: ' . gmdate( "D, d M Y H:i:s", UNIX_TIME + FAVICON_CACHE_TTL ) . ' GMT');
	    exit;
	}

	output_image($icon, $time);
}

function clear_cache($mode = '') {
	if ($handle = opendir(FAVICON_CACHE_DIR)) {
		$all = FALSE;
		if ($mode === 'all') {
			$mode = '';
			$all = TRUE;
		}
		$chk = array();
		while (false !== ($file = readdir($handle))) {
			$target = FAVICON_CACHE_DIR . $file;
			if ($file !== '.' && $file !== '..') {
				if ($mode === 'check') {
					$ext = explode('.', $file);
					$ext = array_pop($ext);
					if (isset($chk[$ext])) {
						$chk[$ext]++;
					} else {
						$chk[$ext] = 1;
					}
					continue;
				}
				if (FAVICON_ADMIN_MODE && $mode && substr($file, strlen($mode) * -1) !== $mode) continue;
				if ((FAVICON_ADMIN_MODE && ($all || $mode)) || substr_count($file, '.') > 1 || filemtime($target) + FAVICON_CACHE_TTL < UNIX_TIME) {
					unlink($target);
				}
			}
		}
		if ($chk) {
			foreach ($chk as $key => $val) {
				echo $key . ': ' . $val . '<br />';
			}
		}
	}
}

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data)
    {
        $fp = fopen($filename, is_file($filename) ? 'r+b' : 'wb');
        if ($fp === false) {
            return false;
        }
        flock($fp, LOCK_EX);
        rewind($fp);
        $bytes = fwrite($fp, $data);
        fflush($fp);
        ftruncate($fp, ftell($fp));
        flock($fp, LOCK_UN);
        fclose($fp);
        return $bytes;
    }
}

if (isset($_GET['clear'])) {
	clear_cache($_GET['clear']);
	exit('ok');
}

$favicon = false;
if (isset($_GET['url'])) {
	$favicon = get_favicon(rawurldecode($_GET['url']));
}

if ($favicon === false) {
    output_image('ErrorIcon');
    exit();
}

output_icon($favicon);

exit();
