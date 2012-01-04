<?php
// $Id: hyp_get_engine.php,v 1.19 2012/01/04 14:11:20 nao-pon Exp $
// HypGetQueryWord Class by nao-pon http://hypweb.net
////////////////////////////////////////////////

if( ! XC_CLASS_EXISTS( 'HypCommonFunc' ) ) {
	include dirname(__FILE__) . '/hyp_common_func.php';
}

if( ! XC_CLASS_EXISTS( 'HypGetQueryWord' ) )
{

class HypGetQueryWord
{
	function set_constants($qw='HYP_QUERY_WORD',$qw2='HYP_QUERY_WORD2',$en='HYP_SEARCH_ENGINE_NAME',$tmpdir='',$enc='EUC-JP')
	{
		$use_kakasi = ($qw2);
		$enc = strtoupper($enc);
		list($getengine_name,$getengine_query,$getengine_query2) = HypGetQueryWord::se_getengine($tmpdir,$enc,$use_kakasi);
		define($qw , $getengine_query);
		if ($use_kakasi) define($qw2, $getengine_query2);
		define($en , $getengine_name);
	}

	function se_getengine($tmpdir,$enc,$use_kakasi)
	{
		$_query = array_merge($_POST, $_GET);
		$_query = HypCommonFunc::stripslashes_gpc($_query);

		$query = (isset($_query['query']))? $_query['query'] : '';
		if ($query) {
			$query = preg_replace('/^("|\')(.+)\1$/', '$2', $query);
		}
		if (!$query) $query = (isset($_query['word']))? $_query['word'] : '';
		if (!$query) $query = (isset($_query['mes']))? $_query['mes'] : '';

		$query2 = $se_name = ''; //Default

		if (! $query)
		{
			$reffer='';
			if(isset($_SERVER['HTTP_REFERER'])) $reffer=$_SERVER['HTTP_REFERER'];

			if ($reffer)
			{
				$reffer = rtrim($reffer, '/');

				$se=file(dirname(__FILE__).'/dat/hyp_search_engines.dat');
				$found=0;

				foreach($se as $linea)
				{
					$linea=trim($linea);
					if($linea && $linea[0] !== '/')
					{
						//$reffer=strtolower($reffer);
						$tmp=explode('|',$linea);
						if(HypGetQueryWord::se_search($reffer,$tmp[1]))
						if(strpos($reffer,rtrim($tmp[2]))!==false)
						{
							$se_name=$tmp[0];
							$found=1;
							break;
						}
					}
				}

				if($found==1)
				{
					$vars=explode('?',$reffer);
					if(count($vars)>1)
					{
						$query=explode(rtrim($tmp[2]),$vars[1]);

						if(count($query)>1)
						{
							$query = explode('&',$query[1]);
							$query = $query[0];
						}
					}
				}
			}
		}

		if ($query) {
			//デコード関数 by nao-pon
			$encfrom = (isset($_GET['encode_hint']) && function_exists('mb_detect_encoding')) ? mb_detect_encoding ($_GET['encode_hint']) : "AUTO";
			$query = HypGetQueryWord::se_urldecode_euc($query,$enc,$encfrom);
			//Googleのキャッシュからの場合
			$query = preg_replace('/^cache\:[^ ]+ /','',$query);

			if (function_exists('mb_convert_kana')) $query = mb_convert_kana($query,'KVas',$enc);

			//$query = preg_replace("/( |\+|,|、|・)+/"," ",$query);

			$query2 = $query;
			if ($use_kakasi && $query2) {
				// 分かち書き
				include_once(dirname(__FILE__).'/hyp_kakasi.php');
				$kakasi = new Hyp_KAKASHI();
				$kakasi->encoding = $enc;
				if ($tmpdir && is_writable($tmpdir))
				{
					$kakasi->tmp_dir = $tmpdir;
				}
				$kakasi->get_wakatigaki($query2);
			}
		}

		return array($se_name,$query,$query2);
	}

	function se_search($string,$mask){
		static $in=array('.', '^', '$', '{', '}', '(', ')', '[', ']', '+', '*', '?', '/');
		static $out=array('\\.', '\\^', '\\$', '\\{', '\\}', '\\(', '\\)', '\\[', '\\]', '\\+', '.*', '.', '\\/');
		$mask='^'.str_replace($in,$out,$mask).'$';
		return (preg_match('/'.$mask.'/', $string));
	}

	// escuni2euc - convert "IE escaped Unicode" to "EUC-JP"
	//
	//			Programmed : Ishigaki Kazuhito ishigaki@factory.gr.jp

	// convert "single IE escaped unicode" to "UTF-8"
	// uni2utf8("%u65E5") returns "\xE5\xB1\x80"
	function uni2utf8($uniescape)
	{
		$c = "";

		$n = intval(substr($uniescape, -4), 16);
		if ($n < 0x7F) {// 0000-007F
			$c .= chr($n);
		} elseif ($n < 0x800) {// 0080-0800
			$c .= chr(0xC0 | ($n / 64));
			$c .= chr(0x80 | ($n % 64));
		} else {				// 0800-FFFF
			$c .= chr(0xE0 | (($n / 64) / 64));
			$c .= chr(0x80 | (($n / 64) % 64));
			$c .= chr(0x80 | ($n % 64));
		}
		return $c;
	}

	// Convert "IE escaped Unicode" to $enc
	// escuni2euc("%u65E5%u672C%u8A9E123") returns "日本語"
	function se_escuni2euc($escunistr, $enc)
	{
		$str = '';

		while(preg_match('/(.*)(%u[0-9A-F][0-9A-F][0-9A-F][0-9A-F])(.*)$/i', $escunistr, $fragment)) {
			$str = mb_convert_encoding(HypGetQueryWord::uni2utf8($fragment[2]).$fragment[3], $enc, 'UTF-8').$str;
			$escunistr = $fragment[1];
		}
		return $fragment[1].$str;
	}

	// 日本語対応のurldecode by nao-pon
	function se_urldecode_euc($str,$enc,$encfrom){
		if (function_exists('mb_convert_encoding') && $encfrom) {
			if (preg_match('/%u[0-9A-F][0-9A-F][0-9A-F][0-9A-F]/i',$str)){
				$query = HypGetQueryWord::se_escuni2euc(urldecode($str), $enc);//for IE unicode+urlencoding
			} else {
				$query = rawurldecode($str);
				$query = mb_convert_encoding($query,$enc,$encfrom);
			}
		} else {
			$query = rawurldecode($str);
		}
		return $query;
	}

	function word_highlight($body, $q_word, $enc = null, $msg = '', $extlink_class_name = 'ext', $word_max_len = 20) {
		if (is_null($enc)) {
			if (function_exists('mb_internal_encoding')) {
				$enc = mb_internal_encoding();
			} else if (defined('_CHARSET')) {
				$enc = _CHARSET;
			} else {
				$enc = 'EUC-JP';
			}
		}

		$enc = strtoupper($enc);

		// 外部リンクの場合 class="ext" を付加
		if ($extlink_class_name) {
			$_body = preg_replace_callback(
						'/(<script.*?<\/script>)|(<a[^>]+?href=(?:"|\')?(?!https?:\/\/'.$_SERVER['HTTP_HOST'].')https?:[^>]+?)>/isS' ,
						create_function('$arr', 'return $arr[1]? $arr[1] : ((strpos($arr[2], \'class=\') === FALSE)? "$arr[2] class=\"' . $extlink_class_name . '\">" : "$arr[0]");') ,
						$body
					);
			if ($_body) $body = $_body; // for RCRE error.
		}

		if (!$q_word || !$body) return $body;

		if (function_exists("xoops_gethandler")) {
			$config_handler =& xoops_gethandler('config');
			$xoopsConfigSearch =& $config_handler->getConfigsByCat(XOOPS_CONF_SEARCH);
		} else {
			$xoopsConfigSearch['keyword_min'] = 3;
		}

		//検索語ハイライト
		$search_word = '';
		//$words = array_flip(preg_split('/\s+/',$q_word,-1,PREG_SPLIT_NO_EMPTY));
		$words = array_flip(HypCommonFunc::phrase_split($q_word));
		$keys = array();
		$cnt = 0;
		if (function_exists('mb_strlen')) {
			$strlen = create_function('$str', 'return mb_strlen("$str");');
		} else {
			$strlen = create_function('$str', 'return strlen("$str");');
		}
		foreach ($words as $word=>$id) {
			$_len = $strlen($word);
			if ($_len < $xoopsConfigSearch['keyword_min']) continue;
			if ($_len > $word_max_len) continue;
			$keys[$word] = $strlen($word);
			$cnt++;
			if ($cnt > 10) break;
		}
		//arsort($keys,SORT_NUMERIC);
		$keys = HypGetQueryWord::get_search_words(array_keys($keys),false,$enc);
		$id = 0;
		$utf8 = ($enc === 'UTF-8')? 'u' : '';
		$php5_1 = (version_compare(PHP_VERSION, '5.1.0', '>='));
		foreach ($keys as $key=>$pattern) {
			$s_key = preg_replace('/&amp;#(\d+;)/', '&#$1', htmlspecialchars($key));
			$_count = 0;
			$pattern = ($s_key{0} == '&') ?
				('/(<head.*?<\/head>|<script.*?<\/script>|<style.*?<\/style>|<textarea.*?<\/textarea>|<strong class="word\d+">.*?<\/strong>|<[^>]*>)|('.$pattern.')/isS'.$utf8):
				('/(<head.*?<\/head>|<script.*?<\/script>|<style.*?<\/style>|<textarea.*?<\/textarea>|<strong class="word\d+">.*?<\/strong>|<[^>]*>|&(?:#[0-9]+|#x[0-9a-f]+|[0-9a-zA-Z]+);)|('.$pattern.')/isS'.$utf8);
			$GLOBALS['HypGetQueryWord_Highlighted'] = false;
			$_body = preg_replace_callback($pattern,
				create_function('$arr',
					'if ($arr[1]) {return $arr[1];} else {$GLOBALS[\'HypGetQueryWord_Highlighted\'] = true; return \'<strong class="word'.$id.'">\'.$arr[2].\'</strong>\';}'), $body);
			if ($GLOBALS['HypGetQueryWord_Highlighted']) {
				$body = $_body;
				$search_word .= ' <strong class="word'.$id.'">'.$s_key.'</strong>';
				$id++;
			}
		}
		if ($id) {
			$_count = 0;
			$body = str_replace('<!--HIGHLIGHT_SEARCH_WORD-->','<div class="highlight_search_words">'.$msg.': '.$search_word.'</div>',$body, $_count);
			if (! $_count) {
				$body = preg_replace('/<body[^>]*?>/', '$0<div class="highlight_search_words">'.str_replace('\\', '\\\\', $msg).': '.str_replace('\\', '\\\\', $search_word).'</div>', $body);
			}
		}
		return $body;
	}
	// 検索語を展開する
	function get_search_words($words, $special=false, $enc='EUC-JP')
	{
		$retval = array();

		//if (defined('XOOPS_USE_MULTIBYTES') && XOOPS_USE_MULTIBYTES && (!function_exists('mb_strlen') || !function_exists('mb_substr'))) return $retval;

		// Perlメモ - 正しくパターンマッチさせる
		// http://www.din.or.jp/~ohzaki/perl.htm#JP_Match
		$eucpre = $eucpost = '';
		$enc = strtoupper($enc);
		$is_utf8 = false;
		if ($enc === 'EUC-JP' || $enc === 'EUCJP-WIN')
		{
			$eucpre = '(?<!\x8F)';
			// # JIS X 0208 が 0文字以上続いて # ASCII, SS2, SS3 または終端
			$eucpost = '(?=(?:[\xA1-\xFE][\xA1-\xFE])*(?:[\x00-\x7F\x8E\x8F]|\z))';
		} else if ($enc === 'UTF-8') {
			$is_utf8 = true;
		}
		// $special : htmlspecialchars()を通すか
		$quote_func = create_function('$str',$special ?
			'return preg_quote($str,"/");' :
			'return preg_quote(htmlspecialchars($str),"/");'
		);
		// LANG=='ja'で、mb_convert_kanaが使える場合はmb_convert_kanaを使用
		$convert_kana_exists = function_exists('mb_convert_kana');
		$convert_kana = create_function('$str,$option,$enc',
			($convert_kana_exists) ?
				'return mb_convert_kana($str,$option,$enc);' : 'return $str;'
		);
		$mb_strlen = create_function('$str,$enc',
			(function_exists('mb_strlen')) ?
				'return mb_strlen($str,$enc);' : 'return strlen($str);'
		);
		$mb_substr = create_function('$str,$start,$len,$enc',
			(function_exists('mb_substr')) ?
				'return mb_substr($str,$start,$len,$enc);' : 'return substr($str,$start,$len);'
		);

		foreach ($words as $word)
		{
			// 英数字は半角,カタカナは全角,ひらがなはカタカナに
			$word_zk = $convert_kana($word,'aKCV',$enc);
			$chars = array();
			for ($pos = 0; $pos < $mb_strlen($word_zk,$enc);$pos++)
			{
				$char = $mb_substr($word_zk,$pos,1,$enc);
				$arr = array($quote_func($char));
				if (strlen($char) == 1) // 英数字
				{
					$arr[] = $quote_func($char); // 英文字
					if ($convert_kana_exists) {
						$arr[] = $quote_func($convert_kana(strtoupper($char),"A",$enc)); // 全角大文字
						$arr[] = $quote_func($convert_kana(strtolower($char),"A",$enc)); // 全角小文字
					}
				}
				else // マルチバイト文字
				{
					$arr[] = $quote_func($convert_kana($char,"c",$enc)); // ひらがな
					$arr[] = $quote_func($convert_kana($char,"k",$enc)); // 半角カタカナ
				}
				if ($is_utf8) {
					$chars[] = '['.join('',array_unique($arr)).']';
				} else {
					$chars[] = '(?:'.join('|',array_unique($arr)).')';
				}
			}
			$retval[$word] = $eucpre.join('',$chars).$eucpost;
		}
		return $retval;
	}
}
}

if (!function_exists('xoops_word_highlight'))
{
function xoops_word_highlight($body, $q_word, $enc = null)
{
	return HypGetQueryWord::word_highlight($body,$q_word,$enc);
}
}
