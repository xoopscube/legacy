<?php
// compatibility for PHP < 5.2

// json support
//pear install Services_JSON-1.0.1
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
