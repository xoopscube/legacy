<?php
//nao-ponさんの本文表示ハック
//引数$lをバイト換算で与えていたものを文字の個数に変更
function search_make_context($text,$words,$l=128)
{
	if (!function_exists('mb_substr')) {
		function mb_substr($a, $b, $c, $enc = "") { return substr($a,$b,$c); }
	}
	if (!function_exists('mb_strlen')) {
		function mb_strlen($a) { return strlen($a); }
	}
	
	if (!is_array($words)) $words = array();
	
	$ret = "";
	$q_word = str_replace(" ", "|", preg_quote(join(' ', $words), "/"));
	
	if (preg_match("/$q_word/i", $text, $match))
	{
		$ret = ltrim(preg_replace('/\s+/', ' ', $text), " ");
		list($pre, $aft) = preg_split("/$q_word/i", $ret, 2);
		$m = intval($l/2);
		$ret = (mb_strlen($pre) > $m)? "... " : "";
		$ret .= mb_substr($pre, -1*$m, min(mb_strlen($pre), $m), _CHARSET) . $match[0];
		$m = $l - mb_strlen($ret);
		$ret .= mb_substr($aft, 0, $m, _CHARSET);
		if (mb_strlen($aft) > $m) $ret .= " ...";
	}
	
	if (!$ret)
		$ret = mb_substr($text, 0, $l, _CHARSET);
	
	return $ret;
}

function sort_by_date($p1, $p2) {
    return ($p2['time'] - $p1['time']);
}

function &context_search( $funcname, $queryarray, $andor = 'AND', $limit = 0, $offset = 0, $userid = 0){

	if( $funcname=="" ){
		return false;
	}
	$ret = $funcname($queryarray, $andor, $limit, $offset, $userid);
	return $ret;

}
?>