<?php

if( !function_exists('myStrlenText') ){

	function myStrlenText($str){

		// Remove HTML tags
		$str = strip_tags($str);
		// Remove the line breaks
		$str = preg_replace("/(\015\012)|(\015)|(\012)/", "", $str);
		// contiguous space Counted as one half a space
		$str = preg_replace('!\s+!', " ", $str);
		// HTML special characters counts to as Single-byte character
		$str = preg_replace("/&[a-zA-Z]{1,6};/", " ", $str);
		// Hexadecimal characters Unicode10 counts to as Single-byte character
		$str = preg_replace("/&#[0-9]{1,5};/", " ", $str);
		// PHP support multi-byte
		if( function_exists('mb_strlen') ){
			$result = mb_strlen($str);
		}else{
			$result = strlen($str);
		}

		return $result;
	}

	function makeTopicImgURL($topic_path, $imgurl)
	{
		if ($imgurl != '' && file_exists($topic_path.$imgurl)) {
			return str_replace(XOOPS_ROOT_PATH,XOOPS_URL,$topic_path).$imgurl;
		}
		return false;
	}

	function topicImgAlign($int)
	{
		switch($int){
			case 1 : return "right";
			case 2 : return "left";
		}

		return fasle;
	}

}

?>