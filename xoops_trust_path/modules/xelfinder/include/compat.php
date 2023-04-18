<?php
// compatibility for PHP < 5.2

if(!function_exists('error_get_last')) {
	function error_get_last() {
		return ['type' => 0, 'message' => $GLOBALS[\PHP_ERRORMSG], 'file' => 'unknonw', 'line' => 0];
	}
}

// json support
if (! extension_loaded('json')) {
	require_once 'Services/JSON.php';
	if (!function_exists('json_decode')){
		function json_decode($content, $assoc=false) {
			$json = $assoc?new Services_JSON(SERVICES_JSON_LOOSE_TYPE):new Services_JSON;
			return $json->decode($content);
		}
	}
	if (!function_exists('json_encode')){
		function json_encode($content){
			$json = new Services_JSON;
			return $json->encode($content);
		}
	}
}

if (!function_exists('sys_get_temp_dir')) {
	function sys_get_temp_dir()
	{
		if (!empty($_ENV['TMP'])) {
			return realpath($_ENV['TMP']);
		}
	
		if (!empty($_ENV['TMPDIR'])) {
			return realpath( $_ENV['TMPDIR']);
		}
	
		if (!empty($_ENV['TEMP'])) {
			return realpath( $_ENV['TEMP']);
		}
	
		$tempfile = tempnam(uniqid(random_int(0, mt_getrandmax()),TRUE),'');
		if (file_exists($tempfile)) {
			unlink($tempfile);
			return realpath(dirname($tempfile));
		}
	}
}

/**
 * Replace str_getcsv()
 *
 * PHP versions 4 and 5
 *
 * @category  PHP
 * @package   PHP_Compat
 * @license   LGPL - http://www.gnu.org/licenses/lgpl.html
 * @copyright 2004-2009 Aidan Lister <aidan@php.net>, Arpad Ray <arpad@php.net>
 * @link      http://php.net/function.str_getcsv
 * @author    HM2K <hm2k@php.net>
 * @version   $CVS: 1.0 $
 * @since     5.3.0
 * @require   PHP 4.0.0 (fgetcsv)
 */
function php_compat_str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\') {
	$fh = tmpfile();
	fwrite($fh, $input);
	rewind($fh);
	$data = [];
	while (($row = php_compat_fgetcsv_wrap($fh, 1000, $delimiter, $enclosure, $escape)) !== FALSE) {
		$data[] = $row;
	}
	fclose($fh);
	return empty($data) ? false : $data;
}
/**
 * Wraps fgetcsv() for the correct PHP version
 *
 * @link http://php.net/function.fgetcsv
 */
function php_compat_fgetcsv_wrap($fh, $length, $delimiter = ',', $enclosure = '"', $escape = '\\') {
	// The escape parameter was added
	if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
		return fgetcsv($fh, $length, $delimiter, $enclosure, $escape);
	}
	// The enclosure parameter was added
	elseif (version_compare(PHP_VERSION, '4.3.0', '>=')) {
		return fgetcsv($fh, $length, $delimiter, $enclosure);
	} else {
		return fgetcsv($fh, $length, $delimiter);
	}
}
if (!function_exists('str_getcsv')) {
	/**
	 * Backwards compatbility for str_getcsv()
	 *
	 * @link http://php.net/function.fgetcsv
	 */
	function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\') {
		return php_compat_str_getcsv($input, $delimiter, $enclosure, $escape);
	}
}

if (! function_exists('array_replace_recursive')) {
	function _array_replace_recursive_recurse($array, $array1) {
		foreach ($array1 as $key => $value) {
			// create new key in $array, if it is empty or not an array
			if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
				$array[$key] = [];
			}
			// overwrite the value in the base array
			if (is_array($value)) {
				$value = _array_replace_recursive_recurse($array[$key], $value);
			}
			$array[$key] = $value;
		}
		return $array;
	}
	
	function array_replace_recursive($array, $array1) {
		// handle the arguments, merge one by one
		$args = func_get_args();
		$array = $args[0];
		if (!is_array($array)) {
			return $array;
		}
		for ($i = 1; $i < count($args); $i++) {
			if (is_array($args[$i])) {
				$array = _array_replace_recursive_recurse($array, $args[$i]);
			}
		}
		return $array;
	}
}

// sys_get_temp_dir()
if ( !function_exists('sys_get_temp_dir')) {
	function sys_get_temp_dir() {
		if (!empty($_ENV['TMP'])) { return realpath($_ENV['TMP']); }
		if (!empty($_ENV['TMPDIR'])) { return realpath( $_ENV['TMPDIR']); }
		if (!empty($_ENV['TEMP'])) { return realpath( $_ENV['TEMP']); }
		$tempfile=tempnam(__FILE__,'');
		if (file_exists($tempfile)) {
			unlink($tempfile);
			return realpath(dirname($tempfile));
		}
		return null;
	}
}
