<?php
//nao-ponさんの本文表示ハック
//引数$lをバイト換算で与えていたものを文字の個数に変更
function search_make_context($text, $words=array(), $l=255, $parts=3, $delimiter='...', $caseInsensitive = TRUE, $whitespaceCompress = TRUE, $encode = null)
{
	if (! $encode) {
		if (defined(_CHARSET)) {
			$encode = _CHARSET;
		} else {
			$encode = mb_internal_encoding();
		}
	}

	static $strcut = '';
	if (!$strcut) $strcut = (function_exists('mb_substr'))? 'mb_substr' : 'substr';

	static $strlen = '';
	if (!$strlen) $strlen = (function_exists('mb_strlen'))? 'mb_strlen' : 'strlen';

	$limit = $parts + 1;
	$ret = str_replace(array('&lt;','&gt;','&quot;','&#039;','&amp;'),array('<','>','"',"'",'&'), $text);

	// short text
	if ($strlen($ret) <= $l) return htmlspecialchars($ret);

	if (is_array($words)) {
		$words = join(' ', $words);
	}
	$words = preg_replace('/\s+/', ' ', $words);
	if ($words) {
		$q_word = str_replace(' ', '|', preg_quote($words, '/'));
	} else {
		$q_word = '?!';
	}

	$match = array();
	$reg = '/(' . $q_word . ')/S';
	if ($encode === 'UTF-8') $reg .= 'u';
	if ($caseInsensitive) {
		$reg .= 'i';
	}
	if (preg_match($reg, $text, $match)) {
		if ($whitespaceCompress) {
			$ret = ltrim(preg_replace('/\s+/', ' ', $text));
		}
		$arr = preg_split($reg, $ret, $limit, PREG_SPLIT_DELIM_CAPTURE);
		$count = count($arr);

		$ret = '';
		if ($count === 1) {
			$ret = $arr[0];
		} else {
			$m = intval($l / max((($count - 1) / 2), 2));
			$add = 0;
			for($i = 0; $i < $count; $i = $i + 2) {
				$mc = $m;
				if ($add) {
					$mc += $add;
				}
				$key = $i + 1;
				if (isset($arr[$key])) {
					$mc = $mc - $strlen($arr[$key], $encode);
				}
				if (isset($arr[$i-1]) && isset($arr[$key])) {
					$type = 'middle';
				} else {
					if (isset($arr[$key])) {
						$type = 'first';
						if ($count > 3) $mc = $mc / 2;
					} else if (isset($arr[$i-1])) {
						$type = 'last';
					}
				}
				$len = $strlen($arr[$i], $encode);
				if ($len > $mc && $type !== 'last') {
					if ($type === 'middle') {
						// キーワードとキーワードで挟まれた部分
						$mc = $mc - $strlen($delimiter, $encode);
						$ret .= $strcut($arr[$i], 0, $mc / 2, $encode);
						$ret .= $delimiter;
						$ret .= $strcut($arr[$i], max($len - $mc / 2 + 1, 0), $mc / 2, $encode);
					} else {
						// 最初の部分
						$mc = $mc - $strlen($delimiter, $encode);
						$ret .= $delimiter;
						$ret .= $strcut($arr[$i], max($len - $mc + 1 , 0), $mc, $encode);
					}
					$add = 0;
				} else {
					$add += $mc - $len;
					$ret .= $arr[$i];
				}
				if (isset($arr[$key])) {
					$ret .= $arr[$key];
				}
			}
		}
	}

	if ($strlen($ret, $encode) > $l) {
		$l = $l - $strlen($delimiter, $encode);
		$ret = $strcut($ret, 0, $l, $encode);
		$ret = preg_replace('/&#?[A-Za-z0-9]{0,6}$/', '', $ret);
		$ret .= $delimiter;
	}

	$ret = htmlspecialchars($ret);
	$ret = preg_replace('/&amp;(#?[A-Za-z0-9]{2,6}?;)/', '&$1', $ret);

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