<?php

if( !function_exists('myStrlenText') ){

	function myStrlenText($str){

		// HTMLタグを削除
		$str = strip_tags($str);
		// 改行を削除
		$str = preg_replace("/(\015\012)|(\015)|(\012)/", "", $str);
		// 連続する半角スペースを半角スペース１としてカウント
		$str = preg_replace('!\s+!', " ", $str);
		// HTML特殊文字を半角1文字としてカウント
		$str = ereg_replace("&[a-zA-Z]{1,5};", " ", $str);
		// Unicode10進文字を半角1文字としてカウント
		$str = ereg_replace("&#[0-9]{1,5};", " ", $str);
		// PHPマルチバイト対応
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