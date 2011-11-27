<?php
//
// Created on 2006/10/16 by nao-pon http://hypweb.net/
// $Id: compat.php,v 1.14 2011/11/26 12:03:10 nao-pon Exp $
//

// const
if (! defined('FILE_APPEND')) define('FILE_APPEND', 8);
if (! defined('FILE_USE_INCLUDE_PATH')) define('FILE_USE_INCLUDE_PATH', 1);

//// mbstring ////
if (! extension_loaded('mbstring') && ! XC_CLASS_EXISTS('HypMBString')) {
	if (is_file(XOOPS_TRUST_PATH . '/class/hyp_common/mbemulator/mb-emulator.php')) {
		require_once(XOOPS_TRUST_PATH . '/class/hyp_common/mbemulator/mb-emulator.php');
	} else {
		require_once(dirname(__FILE__) . '/mbstring.php');
	}
}

//// Compat ////

// is_a --  Returns TRUE if the object is of this class or has this class as one of its parents
// (PHP 4 >= 4.2.0)
if (! function_exists('is_a')) {

	function is_a($class, $match)
	{
		if (empty($class)) return FALSE;

		$class = is_object($class) ? get_class($class) : $class;
		if (strtolower($class) == strtolower($match)) {
			return TRUE;
		} else {
			return is_a(get_parent_class($class), $match);	// Recurse
		}
	}
}

// array_fill -- Fill an array with values
// (PHP 4 >= 4.2.0)
if (! function_exists('array_fill')) {

	function array_fill($start_index, $num, $value)
	{
		$ret = array();
		while ($num-- > 0) $ret[$start_index++] = $value;
		return $ret;
	}
}

// md5_file -- Calculates the md5 hash of a given filename
// (PHP 4 >= 4.2.0)
if (! function_exists('md5_file')) {

	function md5_file($filename)
	{
		if (! is_file($filename)) return FALSE;

		$fd = fopen($filename, 'rb');
		if ($fd === FALSE ) return FALSE;
		$data = fread($fd, filesize($filename));
		fclose($fd);
		return md5($data);
	}
}

// sha1 -- Compute SHA-1 hash
// (PHP 4 >= 4.3.0, PHP5)
if (! function_exists('sha1')) {
	if (extension_loaded('mhash')) {
		function sha1($str) {
			return bin2hex(mhash(MHASH_SHA1, $str));
		}
	} else {
		include_once dirname(__FILE__) . '/compat_sha1.php';
	}
}

// file_get_contents -- Reads entire file into a string
// (PHP 4 >= 4.3.0, PHP 5)
if (! function_exists('file_get_contents')) {
function file_get_contents($filename, $incpath = false, $resource_context = null, $offset = -1, $maxlen = -1)
{
	return HypCommonFunc::file_get_contents($filename, $incpath, $resource_context, $offset, $maxlen);
}
}

/**
 * Replace file_put_contents()
 *
 * @category    PHP
 * @package     PHP_Compat
 * @license     LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright   2004-2007 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link        http://php.net/function.file_put_contents
 * @author      Aidan Lister <aidan@php.net>
 * @version     $Revision: 1.14 $
 * @internal    resource_context is not supported
 * @since       PHP 5
 * @require     PHP 4.0.0 (user_error)
 */
// file_put_contents
// (PHP 5)
if (! function_exists('file_put_contents')) {
function file_put_contents($filename, $content, $flags = null, $resource_context = null)
{
    // If $content is an array, convert it to a string
    if (is_array($content)) {
        $content = implode('', $content);
    }

    // If we don't have a string, throw an error
    if (!is_scalar($content)) {
        user_error('file_put_contents() The 2nd parameter should be either a string or an array',
            E_USER_WARNING);
        return false;
    }

    // Get the length of data to write
    $length = strlen($content);

    // Check what mode we are using
    $mode = ($flags & FILE_APPEND) ?
                'a' :
                'wb';

    // Check if we're using the include path
    $use_inc_path = ($flags & FILE_USE_INCLUDE_PATH) ?
                true :
                false;

    // Open the file for writing
    if (($fh = @fopen($filename, $mode, $use_inc_path)) === false) {
        user_error('file_put_contents() failed to open stream: Permission denied',
            E_USER_WARNING);
        return false;
    }

    // Attempt to get an exclusive lock
    $use_lock = ($flags & LOCK_EX) ? true : false ;
    if ($use_lock === true) {
        if (!flock($fh, LOCK_EX)) {
            return false;
        }
    }

    // Write to the file
    $bytes = 0;
    if (($bytes = @fwrite($fh, $content)) === false) {
        $errormsg = sprintf('file_put_contents() Failed to write %d bytes to %s',
                        $length,
                        $filename);
        user_error($errormsg, E_USER_WARNING);
        return false;
    }

    // Close the handle
    @fclose($fh);

    // Check all the data was written
    if ($bytes != $length) {
        $errormsg = sprintf('file_put_contents() Only %d of %d bytes written, possibly out of free disk space.',
                        $bytes,
                        $length);
        user_error($errormsg, E_USER_WARNING);
        return false;
    }

    // Return length
    return $bytes;
}
}

// array_change_key_case
// (PHP 4 >= 4.2.0, PHP 5)
if(!function_exists("array_change_key_case")){
	if ( !defined('CASE_LOWER') )define('CASE_LOWER', 0);
	if ( !defined('CASE_UPPER') )define('CASE_UPPER', 1);
    function array_change_key_case($input, $case=0){
        if(!is_array($input))return FALSE;
        $product = array();
        foreach($input as $key => $value){
            if($case){ //Upper
                $key2 = (  (is_string($key)) ? (strtoupper($key)) : ($key)  );
                $product[$key2] = $value;
            }
            else{ //Lower
                $key2 = (  (is_string($key)) ? (strtolower($key)) : ($key)  );
                $product[$key2] = $value;
            }
        }
        return $product;
    }/* endfunction array_change_key_case */
}/* endfunction exists array_change_key_case*/

// array_combine
// (PHP 5)
if (! function_exists('array_combine')) {
function array_combine($keys, $values)
{
	if (!is_array($keys)) {
		user_error('array_combine() expects parameter 1 to be array, ' .
			gettype($keys) . ' given', E_USER_WARNING);
		return;
	}

	if (!is_array($values)) {
		user_error('array_combine() expects parameter 2 to be array, ' .
			gettype($values) . ' given', E_USER_WARNING);
		return;
	}

	$key_count = count($keys);
	$value_count = count($values);
	if ($key_count !== $value_count) {
		user_error('array_combine() Both parameters should have equal number of elements', E_USER_WARNING);
		return false;
	}

	if ($key_count === 0 || $value_count === 0) {
		user_error('array_combine() Both parameters should have number of elements at least 0', E_USER_WARNING);
		return false;
	}

	$keys	 = array_values($keys);
	$values	 = array_values($values);

	$combined = array();
	for ($i = 0; $i < $key_count; $i++) {
		$combined[$keys[$i]] = $values[$i];
	}

	return $combined;
}
}

// htmlspecialchars_decode (PHP 5 >= 5.1.0)
if (! function_exists('htmlspecialchars_decode')) {
function htmlspecialchars_decode($string, $quote_style = null)
{
    // Sanity check
    if (!is_scalar($string)) {
        user_error('htmlspecialchars_decode() expects parameter 1 to be string, ' .
            gettype($string) . ' given', E_USER_WARNING);
        return;
    }

    if (!is_int($quote_style) && $quote_style !== null) {
        user_error('htmlspecialchars_decode() expects parameter 2 to be integer, ' .
            gettype($quote_style) . ' given', E_USER_WARNING);
        return;
    }

    // The function does not behave as documented
    // This matches the actual behaviour of the function
    if ($quote_style & ENT_COMPAT || $quote_style & ENT_QUOTES) {
        $from = array('&quot;', '&#039;', '&lt;', '&gt;', '&amp;');
        $to   = array('"', "'", '<', '>', '&');
    } else {
        $from = array('&lt;', '&gt;', '&amp;');
        $to   = array('<', '>', '&');
    }

    return str_replace($from, $to, $string);
}
}