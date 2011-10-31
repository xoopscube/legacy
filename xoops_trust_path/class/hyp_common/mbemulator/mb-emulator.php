<?php
/* mbstring emulator Class HypMBString
 * by nao-pon at hypweb.net
 * based on "mbstring emulator for Japanese by Andy"
 * 
 * license based on GPL(GNU General Public License)
 *
 * $Id: mb-emulator.php,v 1.13 2009/03/01 23:42:25 nao-pon Exp $
 */

if (! function_exists('XC_CLASS_EXISTS')) {
	require dirname(dirname(__FILE__)) . '/XC_CLASS_EXISTS.inc.php';
}

if (! XC_CLASS_EXISTS('HypMBString'))
{

if (!defined('MB_CASE_UPPER')) define('MB_CASE_UPPER', 0);
if (!defined('MB_CASE_LOWER')) define('MB_CASE_LOWER', 1);
if (!defined('MB_CASE_TITLE')) define('MB_CASE_TITLE', 2);

if (! function_exists('mb_detect_order')) {
function mb_detect_order($encoding_list = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_detect_order($encoding_list);
}
}

if (! function_exists('mb_language')) {
function mb_language($language='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_language($language);
}
}

if (! function_exists('mb_internal_encoding')) {
function mb_internal_encoding($encoding = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_internal_encoding($encoding);
}
}

if (! function_exists('mb_get_info')) {
function mb_get_info($type = 'all') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_get_info($type);
}
}

if (! function_exists('mb_substitute_character')) {
function mb_substitute_character($subchar='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_substitute_character($subchar);
}
}

if (! function_exists('mb_convert_encoding')) {
function mb_convert_encoding($str, $to_encoding, $from_encoding = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_convert_encoding($str, $to_encoding, $from_encoding);
}
}

if (! function_exists('mb_convert_kana')) {
function mb_convert_kana($str, $option='KV', $encoding = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_convert_kana($str, $option, $encoding);
}
}

if (! function_exists('mb_send_mail')) {
function mb_send_mail($to, $subject, $message , $additional_headers='', $additional_parameter='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_send_mail($to, $subject, $message , $additional_headers, $additional_parameter);
}
}

if (! function_exists('mb_detect_encoding')) {
function mb_detect_encoding($str , $encoding_list = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_detect_encoding($str , $encoding_list);
}
}

if (! function_exists('mb_strlen')) {
function mb_strlen($str , $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strlen($str , $encoding);
}
}

if (! function_exists('mb_strwidth')) {
function mb_strwidth($str, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strwidth($str, $encoding);
}
}

if (! function_exists('mb_strimwidth')) {
function mb_strimwidth($str, $start, $width, $trimmarker , $encoding = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strimwidth($str, $start, $width, $trimmarker , $encoding);
}
}

if (! function_exists('mb_substr')) {
function mb_substr($str, $start, $length='notnumber', $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_substr($str, $start, $length, $encoding);
}
}

if (! function_exists('mb_strcut')) {
function mb_strcut($str, $start , $length=0 , $encoding = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strcut($str, $start , $length, $encoding);
}
}

if (! function_exists('mb_strrpos')) {
function mb_strrpos($haystack, $needle , $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strrpos($haystack, $needle , $encoding);
}
}

if (! function_exists('mb_strpos')) {
function mb_strpos($haystack, $needle , $offset=0, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strpos($haystack, $needle, $offset, $encoding);
}
}

if (! function_exists('mb_substr_count')) {
function mb_substr_count($haystack, $needle, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_substr_count($haystack, $needle, $encoding);
}
}

if (! function_exists('mb_convert_variables')) {
function mb_convert_variables($to_encoding, $from_encoding, &$var, $s1='',$s2='',$s3='',$s4='',$s5='',$s6='',$s7='',$s8='',$s9='',$s10='') {
	$mb =& HypMBString::getInstance();
	if ( $s1 || $s2 || $s3 || $s4 || $s5 || $s6 || $s7 || $s8 || $s9 || $s10) {
		$arr = array(&$var, &$s1, &$s2, &$s3, &$s4, &$s5, &$s6, &$s7, &$s8, &$s9, &$s10);
 		return $mb->mb_convert_variables($to_encoding, $from_encoding, $arr);
	} else {
		return $mb->mb_convert_variables($to_encoding, $from_encoding, $var);
	}
}
}

if (! function_exists('mb_preferred_mime_name')) {
function mb_preferred_mime_name($encoding) {
	$mb =& HypMBString::getInstance();
	return $mb->mb_preferred_mime_name($encoding);
}
}

if (! function_exists('mb_decode_mimeheader')) {
function mb_decode_mimeheader($str) {
	$mb =& HypMBString::getInstance();
	return $mb->mb_decode_mimeheader($str);
}
}

if (! function_exists('mb_encode_mimeheader')) {
function mb_encode_mimeheader($str, $encoding = "ISO-2022-JP", $transfer_encoding = "B", $linefeed = "\r\n") {
	$mb =& HypMBString::getInstance();
	return $mb->mb_encode_mimeheader($str, $encoding, $transfer_encoding, $linefeed);
}
}

if (! function_exists('mb_http_input')) {
function mb_http_input($type = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_http_input($type);
}
}

if (! function_exists('mb_http_output')) {
function mb_http_output($encoding = '') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_http_output($encoding);
}
}

if (! function_exists('mb_output_handler')) {
function mb_output_handler($buffer, $status='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_output_handler($buffer, $status);
}
}

if (! function_exists('mb_encode_numericentity')) {
function mb_encode_numericentity($str, $convmap, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_encode_numericentity($str, $convmap, $encoding);
}
}

if (! function_exists('mb_decode_numericentity')) {
function mb_decode_numericentity($str, $convmap, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_decode_numericentity($str, $convmap, $encoding);
}
}

if (! function_exists('mb_strtoupper')) {
function mb_strtoupper($str, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strtoupper($str, $encoding);
}
}

if (! function_exists('mb_strtolower')) {
function mb_strtolower($str, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_strtolower($str, $encoding);
}
}

if (! function_exists('mb_convert_case')) {
function mb_convert_case($str, $case, $encoding='') {
	$mb =& HypMBString::getInstance();
	return $mb->mb_convert_case($str, $case, $encoding);
}
}

if (! function_exists('mb_regex_encoding')) {
function mb_regex_encoding($encoding='') {
	return mb_internal_encoding();
}
}


Class HypMBString
{
	var $loaded;
	
	function HypMBString()
	{
		if (extension_loaded('iconv')) {
			$this->use_iconv = TRUE;
		} else {
			$this->use_iconv = FALSE;
			$this->load_table('convert');
		}
		
		$this->mbemu_internals['ini_file'] = parse_ini_file(dirname(__FILE__).'/mb-emulator.ini');

		$this->mbemu_internals['language'] = $this->mbemu_internals['ini_file']['language'];
		$this->mbemu_internals['internal_encoding'] = $this->mbemu_internals['ini_file']['internal_encoding'];
		$this->mbemu_internals['lang_array'] = array (
			'Japanese', 'ja','jp', 'English', 'en', 'uni'
			);

		$this->mbemu_internals['encoding'] = array (
			'AUTO'        => 0XFF,

			'ASCII'           => 0,
			'ANSI_X3.4-1968'  => 0,
			'ISO-IR-6'        => 0,
			'ANSI_X3.4-1986'  => 0,
			'ISO_646.IRV:1991'=> 0,
			'US-ASCII'        => 0,
			'ISO646-US'       => 0,
			'US'              => 0,
			'IBM367'          => 0,
			'CP367'           => 0,
			'CSASCII'         => 0,

			'EUC-JP'      => 1,
			'EUC'         => 1,
			'EUC_JP'      => 1,
			'EUCJP'       => 1,
			'X-EUC-JP'    => 1,
			'EUCJP-WIN'   => 1,
			'EUCJP-MS'    => 1,
			'EUCJP-OPEN'  => 1,

			'SHIFT_JIS'   => 2,
			'SJIS'        => 2,
			'SHIFT-JIS'   => 2,

			'ISO-2022-JP'   => 3,
			'JIS'           => 3,
			'ISO2022JPMS'   => 3,
			'ISO-2022-JP-MS'=> 3,

			'UTF-8'       => 4,
			'UTF8'        => 4,

			'UTF-16'      => 5,
			'UTF16'       => 5,

			'ISO-8859-1'  => 6,
			'ISO_8859-1'  => 6,
			'LATIN1'      => 6,

			'CP932'       => 7,
			'SJIS-WIN'    => 7,
			'SJIS-OPEN'   => 7,
			'WINDOWS-31J' => 7,
			'MS_KANJI'    => 7,

			'UTF-16BE'    => 8,
			'UTF16BE'     => 8,

			'BIG5'        => 10,
			'BIG-5'       => 10,
			'CN-BIG5'     => 10,
			'BIG-FIVE'    => 10,
			'BIGFIVE'     => 10,
			'CP950'       => 10,

			'EUC-CN'      => 11,
			'GB2312'      => 11,
			'CN-GB'       => 11,
			'EUC_CN'      => 11,
			'EUCCN'       => 11,
			'X-EUC-CN'    => 11,

			'EUC-KR'      => 12,
			'EUC_KR'      => 12,
			'EUCKR'       => 12,
			'X-EUC-KR'    => 12,

		);
		
		if (!($this->mb_detect_order($this->mbemu_internals['ini_file']['detect_order'])))
			$this->mbemu_internals['detect_order'] = array ("ASCII", "JIS", "UTF-8", "EUC-JP", "SJIS");

		$this->mbemu_internals['substitute_character'] = $this->mbemu_internals['ini_file']['substitute_character'];

		$this->mbemu_internals['regex'] = array(
			0 => "[\x01-\x7F]", // for ASCII
			1 => "[\xA1-\xFE]([\xA1-\xFE])|[\x01-\x7F]|\x8E([\xA0-\xDF])", // for EUC-JP
			2 => "[\x81-\x9F\xE0-\xFC]([\x40-\xFC])|[\x01-\x7F]|[\xA0-\xDF]", // for Shift_JIS
			3 => "(?:^|\x1B\(\x42)([\x01-\x1A,\x1C-\x7F]*)|(?:\x1B\\$\x42([\x01-\x1A,\x1C-\x7F]*))|(?:\x1B\(I([\x01-\x1A,\x1C-\x7F]*))", // for JIS
			4 => "[\x01-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF][\x80-\xBF]|[\xF0-\xF7][\x80-\xBF][\x80-\xBF][x\80-\xBF]", // for UTF-8
			5 => "..", // for UTF-16
			6 => ".", // for ISO-8859-1
			7 => "[\x81-\x9F\xE0-\xFC]([\x40-\xFC])|[\x01-\x7F]|[\xA0-\xDF]", // for SJIS_WIN
			8 => "..", // for UTF-16BE
			
			10 => "[\xA1-\xFE][\x40-\x7E\xA1-\xFE]|[\x01-\x7F]", // for Big5
			11 => "[\xA1-\xF7][\xA1-\xFE]|[\x01-\x7F]", // EUC-CN
			12 => "[\xA1-\xC8\xCA-\xFD][\xA1-\xFE]|[\x01-\x7F]", // EUC-KR
			);
		
		$loaded = array();
	}
	
	function & getInstance () {
		static $instance;
		if (!isset($instance)) {
			$instance = new HypMBString();
		}
		return $instance;
	}
	
	function load_table ($table) {
		if (isset($this->loaded[$table])) {
			return;
		}
		$this->loaded[$table] = TRUE;
		$method = '_load_table_' . $table;
		$this->$method();
	}

	function _load_table_convert() {
		include dirname(__FILE__).'/convert.table';
	}

	function _load_table_convert_kana() {
		include dirname(__FILE__).'/convert_kana.table';
	}

	function _load_table_lower() {
		include dirname(__FILE__).'/lower.table';
	}

	function _load_table_upper() {
		include dirname(__FILE__).'/upper.table';
	}

	function _load_table_sjistouni() {
		include dirname(__FILE__).'/sjistouni.table';
	}

	function _load_table_unitosjis() {
		include dirname(__FILE__).'/unitosjis.table';
	}

	function convert_variables_join_array($glue, $pieces)
	{
		$arr = array();
		if (!is_array($pieces)) {
			$pieces = array($pieces);
		}
		foreach ($pieces as $piece) {
			$arr[] = is_array($piece) ? $this->convert_variables_join_array($glue, $piece) : $piece;
		}
		return join($glue, $arr);
	}

	function regularize_encoding($encoding = '') {
		if ($encoding && is_string($encoding)) {
			$encoding = strtoupper($encoding);
			if (isset($this->mbemu_internals['encoding'][$encoding])) {
				$encoding = array_search($this->mbemu_internals['encoding'][$encoding], $this->mbemu_internals['encoding']);
				return $encoding;
			}
			if ($this->use_iconv) {
				return $encoding;
			}
		}
		return $this->mbemu_internals['internal_encoding'];
	}
	
	/* mbstring emulator for Japanese by Andy
	 * email : webmaster@matsubarafamily.com
	 *
	 * license based on GPL(GNU General Public License)
	 *
	 * Ver.0.84 (2006/1/20)
	 */

	function mb_detect_order($encoding_list = '')
	{
		if ($encoding_list) {
			if (is_string($encoding_list)) {
				$encoding_list = strtoupper($encoding_list);
				$encoding_list = split(', *', $encoding_list);
			}
			$_enc = array();
			foreach($encoding_list as $encode) {
				if (array_key_exists($encode, $this->mbemu_internals['encoding']) && $encode !== 'AUTO') {
					$_enc[] = $encode;
				}
			}
			if ($_enc) {
				$this->mbemu_internals['detect_order'] = $_enc;
				return TRUE;
			} else {
				return FALSE;
			}
		}
		return $this->mbemu_internals['detect_order'];
	}

	function mb_language($language='')
	{
		if ($language =='') {
			if ($this->mbemu_internals['language'] == '') return FALSE;
			else return $this->mbemu_internals['language'];
		} else {
		foreach ($this->mbemu_internals['lang_array'] as $element) {
			if (strtolower($element) == strtolower($language)) {
				$this->mbemu_internals['language'] = $element;
				return TRUE;
			}
		}
		return FALSE;
	  }
	}


	function mb_internal_encoding($encoding = '')
	{
		if ($encoding =='') {
			if ($this->use_iconv) {
				return iconv_get_encoding('internal_encoding');
			} else {
				return $this->mbemu_internals['internal_encoding'];
			}
		} else {
			$encoding = $this->regularize_encoding($encoding);
			if ($this->use_iconv) {
				$this->mbemu_internals['internal_encoding'] = (iconv_set_encoding('internal_encoding', $encoding))? $encoding : '';
			} else {
				$this->mbemu_internals['internal_encoding'] = $this->regularize_encoding($encoding);
			}
			return ($this->mbemu_internals['internal_encoding'])? TRUE : FALSE;
		}
	}

	function mb_get_info($type = 'all')
	{
		switch(strtolower($type)) {
			case 'all' :
				$a['internal_encoding'] = $this->mb_internal_encoding();
				$a['http_output'] = $this->mb_http_output();
				$a['http_input'] = 'pass';
				$a['func_overload'] = 'pass';
				return $a;
			case 'internal_encoding' :
				return $this->mb_internal_encoding();
			case 'http_output' :
				return $this->mb_http_output();
			case 'http_input' :
				return 'pass';
			case 'func_overloard' :
				return 'pass';
		}
	}

	function mb_substitute_character($subchar='')
	{
		if (!$subchar) return $this->mbemu_internals['substitute_character'];
		if (is_int($subchar)) {
			$this->mbemu_internals['substitute_character'] = $subchar;
		} else {
			$subchar = strtolower($subchar);
			switch ($subchar) {
				case 'none' :
				case 'long' :
					$this->mbemu_internals['substitute_character'] = $subchar;
			}
		}
	}


	function mb_convert_encoding( $str, $to_encoding, $from_encoding = '')
	{
		if (!$str) {
			return '';
		}
		
		if (is_array($str)) {
			foreach ($str as $key=>$value) {
				$str[$key] = $this->mb_convert_encoding($value, $to_encoding, $from_encoding);
			}
			return $str;
		}
		
		$to_encoding = strtoupper($to_encoding);
		$from_encoding = $this->mb_detect_encoding($str, $from_encoding);
		
		if ($this->use_iconv && $from_encoding !== 'AUTO') {
			$to_encoding = $this->regularize_encoding($to_encoding);
			$from_encoding = $this->regularize_encoding($from_encoding);
			return @ iconv($from_encoding, $to_encoding . '//TRANSLIT', $str);
		} else {
			switch (@ $this->mbemu_internals['encoding'][$from_encoding]) {
				case 1: //euc-jp
					switch(@$this->mbemu_internals['encoding'][$to_encoding]) {
						case 2: //sjis
							return $this->_euctosjis($str);
						case 3: //jis
							$str = $this->_euctosjis($str);
							return $this->_sjistojis($str);
						case 4: //utf8
							return $this->_euctoutf8($str);
						case 5: //utf16
						case 8: //utf16BE
							$str = $this->_euctoutf8($str);
							return $this->_utf8toutf16($str);
						case 6: //iso-8859-1
							return utf8_decode($this->_euctoutf8($str));
						default:
							return $str;
					}
				case 2: //sjis
					switch(@$this->mbemu_internals['encoding'][$to_encoding]) {
						case 1: //euc-jp
							return $this->_sjistoeuc($str);
						case 3: //jis
							return $this->_sjistojis($str);
						case 4: //utf8
							return $this->_sjistoutf8($str);
						case 5: //utf16
						case 8: //utf16BE
							$str = $this->_sjistoutf8($str);
							return $this->_utf8toutf16($str);
						case 6: //iso-8859-1
							return utf8_decode($this->_sjistoutf8($str));
						default:
							return $str;
					}
				case 3: //jis
					switch(@$this->mbemu_internals['encoding'][$to_encoding]) {
						case 1: //euc-jp
							$str = $this->_jistosjis($str);
							return $this->_sjistoeuc($str);
						case 2: //sjis
							return $this->_jistosjis($str);
						case 4: //utf8
							$str = $this->_jistosjis($str);
							return $this->_sjistoutf8($str);
						case 5: //utf16
						case 8: //utf16BE
							$str = $this->_jistosjis($str);
							$str = $this->_sjistoutf8($str);
							return $this->_utf8toutf16($str);
						case 6: //iso-8859-1
							$str = $this->_jistosjis($str);
							return utf8_decode($this->_sjistoutf8($str));
						default:
							return $str;
					}
				case 4: //utf8
					switch(@$this->mbemu_internals['encoding'][$to_encoding]) {
						case 1: //euc-jp
							return $this->_utf8toeuc($str);
						case 2: //sjis
							return $this->_utf8tosjis($str);
						case 3: //jis
							$str = $this->_utf8tosjis($str);
							return $this->_sjistojis($str);
						case 5: //utf16
						case 8: //utf16BE
							return $this->_utf8toutf16($str);
						case 6: //iso-8859-1
							return utf8_decode($str);
						default:
							return $str;
					}
				case 5: //utf16
				case 8: //utf16BE
					switch(@$this->mbemu_internals['encoding'][$to_encoding]) {
						case 1: //euc-jp
							$str = $this->_utf16toutf8($str);
							return $this->_utf8toeuc($str);
						case 2: //sjis
							$str = $this->_utf16toutf8($str);
							return $this->_utf8tosjis($str);
						case 3: //jis
							$str = $this->_utf16toutf8($str);
							$str = $this->_utf8tosjis($str);
							return $this->_sjistojis($str);
						case 4: //utf8
							return $str = $this->_utf16toutf8($str);;
						case 6: //iso-8859-1
							$str = $this->_utf16toutf8($str);
							return utf8_decode($str);
						default:
							return $str;
					}
				case 6: //iso-8859-1
					switch(@$this->mbemu_internals['encoding'][$to_encoding]) {
						case 1: //euc-jp
							$str = utf8_encode($str);
							return $this->_utf8toeuc($str);
						case 2: //sjis
							$str = utf8_encode($str);
							return $this->_utf8tosjis($str);
						case 3: //jis
							$str = utf8_encode($str);
							$str = $this->_utf8tosjis($str);
							return $this->_sjistojis($str);
						case 4: //utf8
							return $str = utf8_encode($str);;
						case 5: //utf16
						case 8: //utf16BE
							$str = utf8_encode($str);
							return $this->_utf8toutf16($str);
						default:
							return $str;
					}
				default:
					return $str;
			}
		}
	}



	function _sjistoeuc(&$str)
	{
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $allchars);  // 文字の配列に分解
		$str_EUC = '';
		for ($i = 0; $i < $max; ++$i) {
			$num = ord($allchars[0][$i]);  // 各文字の1バイト目を数値として取り出す
			if ($num2 = ord($allchars[1][$i])) { // 2バイト目がある場合
				$shift = $this->mbemu_internals['sjistoeuc_byte1_shift'][$num2];
				$str_EUC .= chr($this->mbemu_internals['sjistoeuc_byte1'][$num] + $shift)
						   .chr($this->mbemu_internals['sjistoeuc_byte2'][$shift][$num2]);
			} elseif ($num <= 0x7F) {//英数字
				$str_EUC .= chr($num);
			} else { //半角カナ
				$str_EUC .= chr(0x8E).chr($num);
			}
		}
		return $str_EUC;
	}


	function _euctosjis(&$str)
	{
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][1].'/', $str, $allchars);  // 文字の配列に分解
		$str_SJIS = '';
		for ($i = 0; $i < $max; ++$i) {
			$num = ord($allchars[0][$i]);  // 各文字の1バイト目を数値として取り出す
			if ($num2 = ord($allchars[1][$i])) { // 漢字の場合
				$str_SJIS .= chr($this->mbemu_internals['euctosjis_byte1'][$num]);
				if ($num & 1)
					$str_SJIS .= chr($this->mbemu_internals['euctosjis_byte2'][0][$num2]);
				else
					$str_SJIS .= chr($this->mbemu_internals['euctosjis_byte2'][1][$num2]);
			} elseif ($num3 = ord($allchars[2][$i])) {//半角カナ
				$str_SJIS .= chr($num3);
			} else { //英数字
				$str_SJIS .= chr($num);
			}
		}
		return $str_SJIS;
	}

	function _sjistojis(&$str)
	{
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $allchars);  // 文字の配列に分解
		$str_JIS = '';
		$mode = 0; // 英数
		for ($i = 0; $i < $max; ++$i) {
			$num = ord($allchars[0][$i]);  // 各文字の1バイト目を数値として取り出す
			if ($num2 = ord($allchars[1][$i])) { // 2バイト目がある場合
				if ($mode != 1) {
					$mode = 1;
					$str_JIS .= chr(0x1b).'$B';
				}
				$shift = $this->mbemu_internals['sjistoeuc_byte1_shift'][$num2];
				$str_JIS .= chr(($this->mbemu_internals['sjistoeuc_byte1'][$num] + $shift) & 0x7F)
						   .chr($this->mbemu_internals['sjistoeuc_byte2'][$shift][$num2] & 0x7F);
			} elseif ($num > 0x80) {//半角カナ
				if ($mode != 2) {
					$mode = 2;
					$str_JIS .= chr(0x1B).'(I';
				}
				$str_JIS .= chr($num & 0x7F);
			} else {//半角英数
				if ($mode != 0) {
					$mode = 0;
					$str_JIS .= chr(0x1B).'(B';
				}
				$str_JIS .= chr($num);
			}
		}
		if ($mode != 0) {
			$str_JIS .= chr(0x1B).'(B';
		}
		return $str_JIS;
	}

	function _sub_jtosj($match)
	{
		$num = ord($match[0]);
		$num2 = ord($match[1]);
		$s = chr($this->mbemu_internals['euctosjis_byte1'][$num | 0x80]);
		if ($num & 1) {
			$s .= chr($this->mbemu_internals['euctosjis_byte2'][0][$num2 | 0x80]);
		} else {
			$s .= chr($this->mbemu_internals['euctosjis_byte2'][1][$num2 | 0x80]);
		}
		return $s;
	}

	function _jistosjis(&$str)
	{
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][3].'/', $str, $allchunks, PREG_SET_ORDER);  // 文字種ごとの配列に分解
		$st = '';
		for ($i = 0; $i < $max; ++$i) {
			if (ord($allchunks[$i][1])) { //英数にマッチ
				$st .= $allchunks[$i][1];
			} elseif (ord(@$allchunks[$i][2])) { //漢字にマッチ
				$tmp = substr($allchunks[$i][0], 3, strlen($allchunks[$i][0]));
				$st .= preg_replace_callback("/.(.)/", array(& $this, '_sub_jtosj'), $tmp);
			} elseif (ord(@$allchunks[$i][3])) { //半角カナにマッチ
				$st .= preg_replace("/./e","chr(ord['$1'] | 0x80);",$allchunks[$i][3]);
			}
		}
		return $st;
	}


	function _ucs2utf8($uni)
	{
		if ($uni <= 0x7f)
			return chr($uni);
		elseif ($uni <= 0x7ff) {
			$y = ($uni >> 6) & 0x1f;
			$x = $uni & 0x3f;
			return chr(0xc0 | $y).chr(0x80 | $x);
		} else {
			$z = ($uni >> 12) & 0x0f;
			$y = ($uni >> 6) & 0x3f;
			$x = $uni & 0x3f;
			return chr(0xe0 | $z).chr(0x80 | $y).chr(0x80 | $x);
		}
	}

	function _sjistoutf8(&$str)
	{
		$this->load_table('sjistouni');
		$st = '';
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $allchars);  // 文字の配列に分解
		for ($i = 0; $i < $max; ++$i) {
			$num = ord($allchars[0][$i]);  // 各文字の1バイト目を数値として取り出す
			if ($num2 = ord($allchars[1][$i])) { // 2バイト目がある場合
				$ucs2 = $this->mbemu_internals['sjistoucs2'][($num << 8) | $num2];
				$st .= $this->_ucs2utf8($ucs2);
			} elseif ($num > 0x80) {//半角カナ
				$st .= $this->_ucs2utf8(0xfec0 + $num);
			} else {//半角英数
				$st .= chr($num);
			}
		}
		return $st;
	}

	function _utf8ucs2($st)
	{
		$num = ord($st);
		if (!($num & 0x80)) //1byte
			return $num;
		elseif (($num & 0xe0) == 0xc0) {//2bytes
			$num2 = ord(substr($st, 1,1));
			return (($num & 0x1f) << 6) | ($num2 & 0x3f);
		} else  { //3bytes
			$num2 = ord(substr($st, 1,1));
			$num3 = ord(substr($st, 2,1));
			return (($num & 0x0f) << 12) | (($num2 & 0x3f) << 6) | ($num3 & 0x3f);
		}
	}

	function _utf8tosjis(&$str)
	{
		$this->load_table('unitosjis');
		$st = '';
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $allchars);  // 文字の配列に分解
		for ($i = 0; $i < $max; ++$i) {
			$num = $this->_utf8ucs2($allchars[0][$i]); //ucs2の値を取り出す
			if ($num < 0x80)
				$st .= chr($num);
			elseif ((0xff61 <= $num) && ($num <= 0xff9f))
				$st .= chr($num - 0xfec0);
			else {
				$sjis = $this->mbemu_internals['ucs2tosjis'][$num];
				$st .= chr($sjis >> 8) . chr($sjis & 0xff);
			}
		}
		return $st;
	}

	function _euctoutf8(&$str)
	{
		$this->load_table('sjistouni');
		$st = '';
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][1].'/', $str, $allchars);  // 文字の配列に分解
		for ($i = 0; $i < $max; ++$i) {
			$num = ord($allchars[0][$i]);  // 各文字の1バイト目を数値として取り出す
			if ($num2 = ord($allchars[1][$i])) { // 2バイト目がある場合
				if ($num & 1)
					$sjis = ($this->mbemu_internals['euctosjis_byte1'][$num] << 8) | $this->mbemu_internals['euctosjis_byte2'][0][$num2];
				else
					$sjis = ($this->mbemu_internals['euctosjis_byte1'][$num] << 8) | $this->mbemu_internals['euctosjis_byte2'][1][$num2];
				$st .= $this->_ucs2utf8($this->mbemu_internals['sjistoucs2'][$sjis]);
			} elseif ($num3 = ord($allchars[2][$i])) {
				$st .= $this->_ucs2utf8(0xfec0 + $num3);
			} else {//半角英数
				$st .= chr($num);
			}
		}
		return $st;
	}

	function _utf8toeuc(&$str)
	{
		$this->load_table('unitosjis');
		$st = '';
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $allchars);  // 文字の配列に分解
		for ($i = 0; $i < $max; ++$i) {
			$num = $this->_utf8ucs2($allchars[0][$i]); //ucs2の値を取り出す
			if ($num < 0x80)
				$st .= chr($num);
			elseif ((0xff61 <= $num) && ($num <= 0xff9f)) //半角カナ
				$st .= chr(0x8e) . chr($num - 0xfec0);
			else {
				$sjis = $this->mbemu_internals['ucs2tosjis'][$num];
				$upper = $sjis >> 8;
				$lower = $sjis & 0xff;
				$shift = $this->mbemu_internals['sjistoeuc_byte1_shift'][$lower];
				$st .= chr($this->mbemu_internals['sjistoeuc_byte1'][$upper] + $shift)
					   .chr($this->mbemu_internals['sjistoeuc_byte2'][$shift][$lower]);
			}
		}
		return $st;
	}

	function _utf8toutf16(&$str)
	{
		$st = '';
		$max = preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $allchars);  // 文字の配列に分解
		for ($i = 0; $i < $max; ++$i) {
			$num = $this->_utf8ucs2($allchars[0][$i]); //ucs2の値を取り出す
			$st .= chr(($num >> 8) & 0xff).chr($num & 0xff);
		}
		return $st;
	}

	function _utf16toutf8(&$str)
	{
		$st = '';
		$ar = unpack("n*", $str);
		foreach($ar as $char) {
			$st .= $this->_ucs2utf8($char);
		}
		return $st;
	}

		
	function sub_zenhan_EUC(&$str, $match) {
		$match = $match . "|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e[\xa0-\xdf]";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($num = ord($chars[1][$i])) //全角にマッチングした場合
				$str .= chr(array_search($chars[1][$i], $this->mbemu_internals['alphanumeric_convert']));
			//	$str .= chr($num & 0x7F);
			else
				$str .= $chars[0][$i];
		}
	}

	function sub_hanzen_EUC(&$str, $match) {
		$match = $match . "|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e[\xa0-\xdf]";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($num = ord($chars[1][$i])) //半角にマッチングした場合
				$str .= $this->mbemu_internals['alphanumeric_convert'][$num];
			else
				$str .= $chars[0][$i];
		}
	}

	function alpha_zenhan_EUC(&$str) {
		$this->sub_zenhan_EUC($str, "(\xA3[\xC1-\xFA])");
	}

	function alpha_hanzen_EUC(&$str) {
		$this->sub_hanzen_EUC($str, "([\x41-\x5A,\x61-\x7A])");
	}


	function num_zenhan_EUC(&$str) {
		$this->sub_zenhan_EUC($str, "(\xA3[\xB0-\xB9])");
	}

	function num_hanzen_EUC(&$str) {
		$this->sub_hanzen_EUC($str, "([\x30-\x39])");
	}

	function alphanum_zenhan_EUC(&$str) {
		$this->sub_zenhan_EUC($str, "(\xa1[\xa4,\xa5,\xa7-\xaa,\xb0,\xb2,\xbf,\xc3,\xca,\xcb,\xce-\xd1,\xdc,\xdd,\xe1,\xe3,\xe4,\xf0,\xf3-\xf7]|\xA3[\xC1-\xFA]|\xA3[\xB0-\xB9])");
	}

	function alphanum_hanzen_EUC(&$str) {
		$this->sub_hanzen_EUC($str, "([\\\x21,\\\x23-\\\x26,\\\x28-\\\x5B,\\\x5D-\\\x7D])");
	}


	function space_zenhan_EUC(&$str) {
		$this->sub_zenhan_EUC($str, "(\xA1\xA1)");
	}

	function space_hanzen_EUC(&$str) {
		$this->sub_hanzen_EUC($str, "(\x20)");
	}

	function katakana_zenhan_EUC(&$str) {
		$match = "\xa5([\xa1-\xf4])|\xa1([\xa2,\xa3,\xa6,\xab,\xac,\xbc,\xd6,\xd7])|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e[\xa0-\xdf]";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($num = ord($chars[1][$i])) //カナにマッチングした場合
				$str .= chr(0x8e) . $this->mbemu_internals['kana_zenhan_convert'][$num];
			elseif ($num = ord($chars[2][$i])) //半角変換可能な特殊文字にマッチした場合
				$str .= chr(0x8e) . $this->mbemu_internals['special_zenhan_convert'][$num];
			else
				$str .= $chars[0][$i];
		}
	}

	function hiragana_zenhan_EUC(&$str) {
		$match = "\xa4([\xa1-\xf4])|\xa1([\xa2,\xa3,\xa6,\xab,\xac,\xbc,\xd6,\xd7])|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e[\xa0-\xdf]";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($num = ord($chars[1][$i])) //かなにマッチングした場合
				$str .= chr(0x8e) . $this->mbemu_internals['kana_zenhan_convert'][$num];
			elseif ($num = ord($chars[2][$i])) //半角変換可能な特殊文字にマッチした場合
				$str .= chr(0x8e) . $this->mbemu_internals['special_zenhan_convert'][$num];
			else
				$str .= $chars[0][$i];
		}
	}

	function katakana_hanzen1_EUC(&$str) {	//濁点の統合をする方
		$match = "\x8e((?:[\xb3,\xb6-\xc4,\xca-\xce]\x8e\xde)|(?:[\xca-\xce]\x8e\xdf))|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e([\xa1-\xdf])";
			//濁点や半濁点は一緒にマッチング
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($chars[1][$i]) //濁音，半濁音にマッチングした場合
				$str .= chr(0xa5).chr(array_search($chars[1][$i], $this->mbemu_internals['kana_zenhan_convert']));
			elseif ($chars[2][$i]) //その他の半角カナにマッチ
				if ($num = array_search($chars[2][$i], $this->mbemu_internals['kana_zenhan_convert']))
					$str .= chr(0xa5).chr($num);
				else
					$str .= chr(0xa1).chr(array_search($chars[2][$i], $this->mbemu_internals['special_zenhan_convert']));
			else
				$str .= $chars[0][$i];
		}
	}

	function hiragana_hanzen1_EUC(&$str) {	//濁点の統合をする方
		$match = "\x8e((?:[\xb6-\xc4,\xca-\xce]\x8e\xde)|(?:[\xca-\xce]\x8e\xdf))|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e([\xa1-\xdf])";
			//濁点や半濁点は一緒にマッチング
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($chars[1][$i]) //濁音，半濁音にマッチングした場合
				$str .= chr(0xa4).chr(array_search($chars[1][$i], $this->mbemu_internals['kana_zenhan_convert']));
			elseif ($chars[2][$i]) //その他の半角カナにマッチ
				if ($num = array_search($chars[2][$i], $this->mbemu_internals['kana_zenhan_convert']))
					$str .= chr(0xa4).chr($num);
				else
					$str .= chr(0xa1).chr(array_search($chars[2][$i], $this->mbemu_internals['special_zenhan_convert']));
			else
				$str .= $chars[0][$i];
		}
	}

	function katakana_hanzen2_EUC(&$str) {	//濁点の統合をしない方
		$match = "[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e([\xa1-\xdf])";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($chars[1][$i]) //半角カナにマッチ
				if ($num = array_search($chars[1][$i], $this->mbemu_internals['kana_zenhan_convert']))
					$str .= chr(0xa5).chr($num);
				else
					$str .= chr(0xa1).chr(array_search($chars[1][$i], $this->mbemu_internals['special_zenhan_convert']));
			else
				$str .= $chars[0][$i];
		}
	}

	function hiragana_hanzen2_EUC(&$str) {	//濁点の統合をしない方
		$match = "[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e([\xa1-\xdf])";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($chars[1][$i]) //半角カナにマッチ
				if ($num = array_search($chars[1][$i], $this->mbemu_internals['kana_zenhan_convert']))
					$str .= chr(0xa4).chr($num);
				else
					$str .= chr(0xa1).chr(array_search($chars[1][$i], $this->mbemu_internals['special_zenhan_convert']));
			else
				$str .= $chars[0][$i];
		}
	}

	function katakana_hiragana_EUC(&$str) {

		$match = "\xa5([\xa1-\xf3])|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e[\xa0-\xdf]";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($num = ord($chars[1][$i])) //カナにマッチングした場合
				$str .= chr(0xa4) . chr($num);
			else
				$str .= $chars[0][$i];
		}
	}

	function hiragana_katakana_EUC(&$str) {

		$match = "\xa4([\xa1-\xf4])|[\xa1-\xfe][\xa1-\xfe]|[\x01-\x7f]|\x8e[\xa0-\xdf]";
		$max = preg_match_all("/$match/", $str, $chars);
		$str = '';
		for ($i = 0; $i < $max; ++$i) {
			if ($num = ord($chars[1][$i])) //カナにマッチングした場合
				$str .= chr(0xa5) . chr($num);
			else
				$str .= $chars[0][$i];
		}
	}

	function mb_convert_kana( $str, $option='KV', $encoding = '')
	{
		$this->load_table('convert_kana');
		
		$encoding = $this->regularize_encoding($encoding);
		$str = $this->mb_convert_encoding($str, 'EUC-JP', $encoding);

		if (strstr($option, "r")) $this->alpha_zenhan_EUC($str);
		if (strstr($option, "R")) $this->alpha_hanzen_EUC($str);
		if (strstr($option, "n")) $this->num_zenhan_EUC($str);
		if (strstr($option, "N")) $this->num_hanzen_EUC($str);
		if (strstr($option, "a")) $this->alphanum_zenhan_EUC($str);
		if (strstr($option, "A")) $this->alphanum_hanzen_EUC($str);
		if (strstr($option, "s")) $this->space_zenhan_EUC($str);
		if (strstr($option, "S")) $this->space_hanzen_EUC($str);
		if (strstr($option, "k")) $this->katakana_zenhan_EUC($str);
		if (strstr($option, "K")) {
			if (strstr($option, "V"))
				$this->katakana_hanzen1_EUC($str);
			else
				$this->katakana_hanzen2_EUC($str);
		}
		if (strstr($option, "H")) {
			if (strstr($option, "V"))
				$this->hiragana_hanzen1_EUC($str);
			else
				$this->hiragana_hanzen2_EUC($str);
		}
		if (strstr($option, "h")) $this->hiragana_zenhan_EUC($str);
		if (strstr($option, "c")) $this->katakana_hiragana_EUC($str);
		if (strstr($option, "C")) $this->hiragana_katakana_EUC($str);

		$str = $this->mb_convert_encoding($str, $encoding, 'EUC-JP');
		return $str;
	}

	// if mb_language is uni this function send mail using UTF-8/Base64
	// if English or en this function send mail using ISO-8859-1/quoted printable
	// if Japanese this function send mail using ISO-2022-JP
	function mb_send_mail($to, $subject, $message , $additional_headers='', $additional_parameter='')
	{
		switch ($this->mb_language()) {
			case 'jp' :
			case 'ja' :
			case 'Japanese' :
				if (!$this->_check_encoding($subject, 3)) //if not JIS encoded
					$subject = $this->mb_encode_mimeheader($subject);
				else {
					$tmp = $this->mb_internal_encoding();
					$this->mb_internal_encoding('iso-2022-jp');
					$subject = $this->mb_encode_mimeheader($subject);
					$this->mb_internal_encoding($tmp);
				}
				if (!$this->_check_encoding($message, 3))
					$message = $this->mb_convert_encoding($message, "iso-2022-jp", $this->mb_internal_encoding());
				$additional_headers .= 
				"\r\nMime-Version: 1.0\r\nContent-Type: text/plain; charset=ISO-2022-JP\r\nContent-Transfer-Encoding: 7bit";
				mail($to, $subject, $message, $additional_headers, $additional_parameter);
				break;
			case 'en' :
			case 'English' :
				$subject = $this->mb_encode_mimeheader($subject, $this->mb_internal_encoding(), 'Q');
				$message = $this->_sub_encode_base64($message, $this->mb_internal_encoding(), 76 , "\r\n");
				$additional_headers .= 
				"\r\nMime-Version: 1.0\r\nContent-Type: text/plain; charset=".
				$this->mb_preferred_mime_name($this->mb_internal_encoding()).
				"\r\nContent-Transfer-Encoding: BASE64";
				mail($to, $subject, $message, $additional_headers, $additional_parameter); 
				break;
			case 'uni' :
				$subject = $this->mb_encode_mimeheader($subject, $this->mb_internal_encoding(), 'B');
				$message = $this->_sub_encode_base64 ($message, $this->mb_internal_encoding(), 76 , "\r\n");
				$additional_headers .= 
				"\r\nMime-Version: 1.0\r\nContent-Type: text/plain; charset=".
				$this->mb_preferred_mime_name($this->mb_internal_encoding()).
				"\r\nContent-Transfer-Encoding: BASE64";
				mail($to, $subject, $message, $additional_headers, $additional_parameter); 
				break;
		}
		
	}



	function _check_encoding($str, $encoding_number)
	{
		return (preg_match('/^('.$this->mbemu_internals['regex'][$encoding_number].')+$/', $str));
	}

	function mb_detect_encoding( $str , $encoding_list = '')
	{
		if ($encoding_list == '') {
				$encoding_list = $this->mb_detect_order();
		}
		if (!is_array($encoding_list)) {
			$encoding_list = strtoupper($encoding_list);
			if ($encoding_list == 'AUTO') {
				$encoding_list = $this->mb_detect_order();
			} else {
				$encoding_list = split(', *', $encoding_list);
			}
		}
		foreach($encoding_list as $encode) {
			$encode = $this->regularize_encoding($encode);
			if (!$encode) continue;
			if ($this->use_iconv) {
				$this->error = 0;
				set_error_handler(array(&$this, 'iconvErrorHandler'));
				iconv($encode, 'UTF-8', $str);
				restore_error_handler();
				if ($this->error) {
					continue;
				} else {
					return $encode;
				}
			} else { 
				if (isset($this->mbemu_internals['encoding'][$encode]) && $this->_check_encoding($str, $this->mbemu_internals['encoding'][$encode])) {
					return $encode;
				}
			}
		}
		return (count($encoding_list) === 1)? $encoding_list[0] : 'AUTO';
	}

	function mb_strlen ($str ,$encoding='')
	{
		$encoding = $this->regularize_encoding($encoding);
		if ($this->use_iconv) {
			return iconv_strlen($str ,$encoding);
		} else {
			switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
				case 1 : //euc-jp
				case 2 : //shift-jis
				case 4 : //utf-8
					return preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $str, $arr);
				case 5 : //utf-16
				case 8 : //utf16BE
					return strlen($str) >> 1;
				case 3 : //jis
					$str = $this->mb_convert_encoding($str, 'SJIS', 'JIS');
					return preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $arr);
				case 0 : //ascii
				case 6 : //iso8859-1
				default:
					return strlen($str);
			}
		}
	}

	function mb_strwidth( $str, $encoding='')
	{
		$encoding = $this->regularize_encoding($encoding);

		if ($this->use_iconv && $encoding !== 'ASCII' && $encoding !== 'ISO-8859-1') {
			$str = $this->mb_convert_encoding($str, 'UTF-8', $encoding);
			$encoding = 'UTF-8';
		}

		switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
			case 4 : //utf-8
				$max = $len = preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $arr);
				for ($i=0; $i < $max; ++$i) {
					$ucs2 = $this->_utf8ucs2($arr[0][$i]);
					if (((0x2000 <= $ucs2) && ($ucs2 <= 0xff60)) || (0xffa0 <= $ucs2))
						++$len;
				}
				return $len;
			case 1 : //euc-jp
			case 2 : //shift-jis
				$max = $len = preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $str, $arr);
				for ($i=0; $i < $max; ++$i)
					if ($arr[1][$i]) ++$len;
				return $len;
			case 5 : //utf-16
			case 8 : //utf16BE
				$max = $len = preg_match_all('/'.$this->mbemu_internals['regex'][5].'/', $str, $arr);
				for ($i=0; $i < $max; ++$i) {
					$ucs2 = (ord($arr[0][$i]) << 8) | ord(substr($arr[0][$i],1,1));
					if (((0x2000 <= $ucs2) && ($ucs2 <= 0xff60)) || (0xffa0 <= $ucs2))
						++$len;
				}
				return $len;
			case 3 : //jis
				$str = $this->mb_convert_encoding($str, 'SJIS', 'JIS');
				$max = $len = preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $arr);
				for ($i=0; $i < $max; ++$i)
					if ($arr[1][$i]) ++$len;
				return $len;
			case 0 : //ascii
			case 6 : //iso8859-1
			default:
				return strlen($str);
		}
	}

	function mb_strimwidth( $str, $start, $width, $trimmarker , $encoding = '')
	{
		$encoding = $this->regularize_encoding($encoding);

		$str = $this->mb_substr($str, $start, 'notnumber', $encoding);
		if (($len = $this->mb_strwidth($str,$encoding)) <= $width)
			return $str;
		$trimwidth = $this->mb_strwidth($trimmarker,$encoding);
		$width -= $trimwidth;
		if ($width <= 0) return $trimmarker;

		$iconv_conv = FALSE;
		if ($this->use_iconv && $encoding !== 'ASCII' && $encoding !== 'ISO-8859-1') {
			if ($encoding !== 'UTF-8') {
				$str = $this->mb_convert_encoding($str, 'UTF-8', $encoding);
				$iconv_conv = $encoding;
				$encoding = 'UTF-8';
			}
		}
		
		switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
			case 4 : //utf-8
				preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $arr);
				$i = 0;
				while(TRUE) {
					$ucs2 = $this->_utf8ucs2($arr[0][$i]);
					if (((0x2000 <= $ucs2) && ($ucs2 <= 0xff60)) || (0xffa0 <= $ucs2))
						$width -= 2;
					else
						--$width;
					if ($width<0) break;
					++$i;
				}
				$arr[0] = array_slice($arr[0], 0, $i);
				$ret = implode("", $arr[0]).$trimmarker;
				if ($iconv_conv) {
					$ret = $this->mb_convert_encoding($ret, $iconv_conv, 'UTF-8');
				}
				return $ret;
			case 5 : //utf-16
			case 8 : //utf16BE
				$arr = unpack("n*", $str);
				$i = 0;
				foreach($arr as $ucs2) {
					if (((0x2000 <= $ucs2) && ($ucs2 <= 0xff60)) || (0xffa0 <= $ucs2))
						$width -= 2;
					else
						--$width;
					if ($width<0) break;
					++$i;
				}
				$arr[0] = array_slice($arr[0], 0, $i);
				return implode("", $arr[0]).$trimmarker;
			case 1 : //euc-jp
			case 2 : //shift-jis
				preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $str, $arr);
				$i = 0;
				while(TRUE) {
					if ($arr[1][$i])
						$width -= 2;
					else
						--$width;
					if ($width<0) break;
					++$i;
				}
				$arr[0] = array_slice($arr[0], 0, $i);
				return implode("", $arr[0]).$trimmarker;
			case 3 : //jis
				$str = $this->mb_convert_encoding($str, 'SJIS', 'JIS');
				$trimmarker = $this->mb_convert_encoding($trimmarker, 'SJIS', 'JIS');
				preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $arr);
				$i = 0;
				while(TRUE) {
					if ($arr[1][$i])
						$width -= 2;
					else
						--$width;
					if ($width<0) break;
					++$i;
				}
				$arr[0] = array_slice($arr[0], 0, $i);
				return $this->mb_convert_encoding(implode("", $arr[0]).$trimmarker,'JIS','SJIS');
			case 0 : //ascii
			case 6 : //iso8859-1
			default:
				return substr($str, 0, $width).$trimmarker;
		}
	}


	function mb_substr ( $str, $start , $length='notnumber' , $encoding='')
	{
		$encoding = $this->regularize_encoding($encoding);
		if ($this->use_iconv) {
			if (!is_int($length)) {
				$length = iconv_strlen($str, $encoding);
			}
			return iconv_substr($str, $start, $length ,$encoding);
		} else {
			$arr = array();
			switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
				case 1 : //euc-jp
				case 2 : //shift-jis
				case 4 : //utf-8
				case 5 : //utf-16
				case 8 : //utf16BE
					preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $str, $arr);
					break;
				case 3 : //jis
					$str = $this->mb_convert_encoding($str, 'SJIS', 'JIS');
					preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $arr);
					break;
				case 0 : //ascii
				case 6 : //iso-8859-1
				default:
					if (is_int($length))
						return substr($str, $start , $length);
					else
						return substr($str, $start);
			}
			if (is_int($length))
				$arr[0] = array_slice($arr[0], $start, $length);
			else
				$arr[0] = array_slice($arr[0], $start);
			$str = implode("", $arr[0]);
			if ($this->mbemu_internals['encoding'][$encoding] == 3)
				$str = $this->mb_convert_encoding($str, 'JIS', 'SJIS');
			return $str;
		}
	}

	function _sub_strcut($arr, $start, $length) {
		$max = count($arr[0]);
		$s = ''; $counter = 0;
		for ($i = 0; $i < $max; ++$i) {
			$counter += strlen($arr[0][$i]);
			if ($counter > $start) {
				if ($length == 0) {
					for ($j = $i; $j < $max; ++$j)
						$s .= $arr[0][$j];
					return $s;
				}
				for ($j = $i, $len = 0; $j < $max; ++$j) {
					$len += strlen($arr[0][$j]);
					if ($len <= $length)
						$s .= $arr[0][$j];
				}
				return $s;
			}
		}
		return $s;
	}

	function mb_strcut ( $str, $start , $length=0, $encoding = '')
	{
		$encoding = $this->regularize_encoding($encoding);

		$iconv_conv = FALSE;
		if ($this->use_iconv && $encoding !== 'ASCII' && $encoding !== 'ISO-8859-1') {
			$str = $this->mb_convert_encoding($str, 'UTF-8', $encoding);
			if ($encoding !== 'UTF-8') {
				$iconv_conv = $encoding;
				$encoding = 'UTF-8';
			}
		}
		
		switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
			case 1 : //euc-jp
			case 2 : //shift-jis
			case 4 : //utf-8
			case 5 : //utf-16
			case 8 : //utf16BE
				preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $str, $arr);
				if ($iconv_conv) {
					$this->mb_convert_variables($iconv_conv, $encoding, $arr);
				}
				$ret = $this->_sub_strcut($arr, $start, $length);
				return $ret;
			case 3 : //jis
				$str = $this->mb_convert_encoding($str, 'SJIS', 'JIS');
				preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $str, $arr);
				$sub = $this->_sub_strcut($arr, $start, $length);
				return $this->mb_convert_encoding($sub, 'JIS', 'SJIS');
			case 0 : //ascii
			case 6 : //iso-8859-1
			default:
				if (intval($length) !== 0)
					return substr($str, $start , $length);
				else
					return substr($str, $start);
		}
	}

	function _sub_strrpos($ar_haystack, $ar_needle)
	{
		$max_h = count($ar_haystack) - 1;
		$max_n = count($ar_needle) - 1;
		for ($i = $max_h; $i >= $max_n; --$i) {
			if ($ar_haystack[$i] == $ar_needle[$max_n]) {
				$match = TRUE;
				for ($j = 1; $j <= $max_n; ++$j)
					if ($ar_haystack[$i-$j] != $ar_needle[$max_n-$j]) {
						$match = FALSE;
						break;
					}
				if ($match) return $i - $max_n;
			}
		}
		return FALSE;
	}

	function mb_strrpos ( $haystack, $needle , $encoding = '')
	{
		$encoding = $this->regularize_encoding($encoding);

		if ($this->use_iconv) {
			return iconv_strrpos($haystack, $needle , $encoding);
		} else {
			switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
				case 1 : //euc-jp
				case 2 : //shift-jis
				case 4 : //utf-8
				case 5 : //utf-16
				case 8 : //utf16BE
					preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $haystack, $ar_h);
					preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $needle, $ar_n);
					return $this->_sub_strrpos($ar_h[0], $ar_n[0]);
				case 3 : //jis
					$haystack = $this->mb_convert_encoding($haystack, 'SJIS', 'JIS');
					$needle = $this->mb_convert_encoding($needle, 'SJIS', 'JIS');
					preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $haystack, $ar_h);
					preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $needle, $ar_n);
					return $this->_sub_strrpos($ar_h[0], $ar_n[0]);
				case 0 : //ascii
				case 6 : //iso-8859-1
				default:
					return strrpos($haystack, $needle);
			}
		}
	}

	function _sub_strpos($ar_haystack, $ar_needle, $offset)
	{
		$max_n = count($ar_needle) - 1;
		$max_h = count($ar_haystack) - count($ar_needle);
		for ($i = $offset; $i <= $max_h; ++$i) {
			for ($j = 0; $j <= $max_n; ++$j) {
				$match = TRUE;
				if ($ar_haystack[$i+$j] != $ar_needle[$j]) {
					$match = FALSE;
					break;
				}
			}
			if ($match) return $i;
		}
		return FALSE;
	}

	function mb_strpos ( $haystack, $needle , $offset = 0, $encoding = '')
	{
		$encoding = $this->regularize_encoding($encoding);

		if ($this->use_iconv) {
			return iconv_strpos($haystack, $needle ,$offset, $encoding);
		} else {
			switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
				case 1 : //euc-jp
				case 2 : //shift-jis
				case 4 : //utf-8
				case 5 : //utf-16
				case 8 : //utf16BE
					preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $haystack, $ar_h);
					preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $needle, $ar_n);
					return $this->_sub_strpos($ar_h[0], $ar_n[0], $offset);
				case 3 : //jis
					$haystack = $this->mb_convert_encoding($haystack, 'SJIS', 'JIS');
					$needle = $this->mb_convert_encoding($needle, 'SJIS', 'JIS');
					preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $haystack, $ar_h);
					preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $needle, $ar_n);
					return $this->_sub_strpos($ar_h[0], $ar_n[0], $offset);
				case 0 : //ascii
				case 6 : //iso-8859-1
				default:
					return strpos($haystack, $needle , $offset);
			}
		}
	}

	function _sub_substr_count($ar_haystack, $ar_needle)
	{
		$matches = 0;
		$max_n = count($ar_needle) - 1;
		$max_h = count($ar_haystack) - count($ar_needle);
		for ($i = 0; $i <= $max_h; ++$i) {
			for ($j = 0; $j <= $max_n; ++$j) {
				$match = TRUE;
				if ($ar_haystack[$i+$j] != $ar_needle[$j]) {
					$match = FALSE;
					break;
				}
			}
			if ($match) ++$matches;
		}
		return $matches;
	}

	function mb_substr_count($haystack, $needle , $encoding = '')
	{
		$encoding = $this->regularize_encoding($encoding);

		if ($this->use_iconv && $encoding !== 'ASCII' && $encoding !== 'ISO-8859-1') {
			$haystack = $this->mb_convert_encoding($haystack, 'UTF-8', $encoding);
			$encoding = 'UTF-8';
		}

		switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
			case 1 : //euc-jp
			case 2 : //shift-jis
			case 4 : //utf-8
			case 5 : //utf-16
			case 8 : //utf16BE
				preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $haystack, $ar_h);
				preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $needle, $ar_n);
				return $this->_sub_substr_count($ar_h[0], $ar_n[0]);
			case 3 : //jis
				$haystack = $this->mb_convert_encoding($haystack, 'SJIS', 'JIS');
				$needle = $this->mb_convert_encoding($needle, 'SJIS', 'JIS');
				preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $haystack, $ar_h);
				preg_match_all('/'.$this->mbemu_internals['regex'][2].'/', $needle, $ar_n);
				return $this->_sub_substr_count($ar_h[0], $ar_n[0]);
			case 0 : //ascii
			case 6 : //iso-8859-1
			default:
				return substr_count($haystack, $needle);
		}
	}

	function mb_convert_variables($to_encoding, $from_encoding, &$arr)
	{
		if (!($encode = $this->mb_detect_encoding($this->convert_variables_join_array(' ', $arr), $from_encoding))) {
			return FALSE;
		}
		$arr = $this->mb_convert_encoding($arr, $to_encoding, $encode);
		return ($this->mbemu_internals['encoding'][$encode] === @$this->mbemu_internals['encoding'][$from_encoding])? $from_encoding : $encode;
	}

	function mb_preferred_mime_name ($encoding)
	{
		$encoding = strtoupper($encoding);
		
		switch ($this->mbemu_internals['encoding'][$encoding]) {
			case 0 : //ascii
				return 'US-ASCII';
			case 1 : //euc-jp
				return 'EUC-JP';
			case 2 : //shift-jis
				return 'Shift_JIS';
			case 3 : //jis
				return 'ISO-2022-JP';
			case 4 : //utf-8
				return 'UTF-8';
			case 5 : 
				return 'UTF-16';
			case 6 :
				return 'ISO-8859-1';
			case 8 : //utf16BE
				return 'UTF-16BE';
		}
	}

	function mb_decode_mimeheader($str)
	{
		$lines = preg_split("/(\r\n|\r|\n)( *)/", $str);
		$s = '';
		foreach ($lines as $line) {
			if ($line != "") {
				$line = preg_replace("/<[\w\-+\.]+\@[\w\-+\.]+>/","", $line); //メール・アドレス部を消す
				$matches = preg_split("/=\?([^?]+)\?(B|Q)\?([^?]+)\?=/", $line, -1, PREG_SPLIT_DELIM_CAPTURE);
				for ($i = 0; $i < count($matches)-1; $i+=4) {
					if (!preg_match("/^[ \t\r\n]*$/", $matches[$i]))
						$s .= $matches[$i];
					if ($matches[$i+2] == 'B')
						$s .= $this->mb_convert_encoding(base64_decode($matches[$i+3]), 
												$this->mb_internal_encoding(), $matches[$i+1]);
					else
						$s .= $this->mb_convert_encoding(quoted_printable_decode($matches[$i+3]), 
												$this->mb_internal_encoding(), $matches[$i+1]);
				}
				if (!preg_match("/^[ \t\r\n]*$/", $matches[$i]))
						$s .= $matches[$i];
			}
		}
		return $s;
	}

	function _sub_qponechar($str, &$len)
	{
		$all = unpack("C*", $str);
		$s = ''; $len = 0;
		foreach($all as $char) {
			if (((ord('A') <= $char) && ($char <= ord('Z'))) ||
				((ord('a') <= $char) && ($char <= ord('z')))) {
				$s .= chr($char);
				++$len;
			} else {
				$s .= '='.sprintf("%2X",$char);
				$len += 3;
			}
		}
		return $s;
	}

	function _sub_quoted_printable_encode($str, $encoding, $maxline, $linefeed)
	{
		$l = 0;
		switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
			case 0 : //ascii
			case 1 : //euc-jp
			case 2 : //shift-jis
			case 4 : //utf-8
				$max = preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $str, $allchars);
				break;
			case 3 : //jis
				$max = preg_match_all('/'.$this->mbemu_internals['regex'][3].'/', $str, $allchunks, PREG_SET_ORDER);  // 文字種ごとの配列に分解
				$st = ''; // quoted printable変換後の文字列
				$len = $maxline;  // その行に追加可能なバイト数
				$needterminate = FALSE; //最後にエスケープシーケンスが必要かどうか
				for ($i = 0; $i < $max; ++$i) {
					if (ord($allchunks[$i][1])) { //英数にマッチ
						if ($needterminate) {
							$st .= '=1B=28B';
							$len -= 7;
						}
						$tmparr = unpack("C*", $allchunks[$i][1]);
						foreach ($tmparr as $char) {
							$tmp = $this->_sub_qponechar(chr($char), $l);
							if ($len < $l) {
								$st .= $linefeed;
								$len = $maxline;
							}
							$st .= $tmp;
							$len -= $l;
						} 
						$needterminate = FALSE;
					} elseif (ord($allchunks[$i][2])) { //漢字にマッチ
						$maxchars = preg_match_all("/../",substr($allchunks[$i][0], 3),$allchars);
						$tmp = $this->_sub_qponechar($allchars[0][0], $l);
						if ($len < 14 + $l) {
							if ($needterminate)
								$st .= '=1B=28B';
							$st .= $linefeed;
							$len = $maxline;
						}
						$st .= '=1B=24B';
						$len -= 7;
						for ($j = 0; $j < $maxchars; ++$j) {
							$tmp = $this->_sub_qponechar($allchars[0][$j], $l);
							if ($len < $l + 7) {
								$st .= '=1B=28B'.$linefeed.'=1B=24B';
								$len = $maxline-7;
							}
							$st .= $tmp;
							$len -= $l;
						}
						$needterminate = TRUE;
						
					} elseif (ord($allchunks[$i][3])) { //半角カナにマッチ
						$max = preg_match_all("/./",$allchunks[$i][3],$allchars);
						$tmp = $this->_sub_qponechar($allchars[0][0], $l);
						if ($len < 14 + $l) {
							if ($needterminate)
								$st .= '=1B=28B';
							$st .= $linefeed;
							$len = $maxline;
						}
						$st .= '=1B=28I';
						$len -= 7;
						for ($j == 0; $j < $max; ++$j) {
							$tmp = $this->_sub_qponechar($allchars[0][$j], $l);
							if ($len < $l + 7) {
								$st .= '=1B=28B'.$linefeed.'=1B=28I';
								$len = $maxline-7;
							}
							$st .= $tmp;
							$len -= $l;
						}
						$needterminate = TRUE;
					}
				}
				if ($needterminate) $st .= '=1B=28B';
				$st .= $linefeed;
				return $st;
		}
		$st = ''; // quoted printable変換後の文字列
		$len = $maxline;  // その行に追加可能なバイト数
		for ($i = 0; $i < $max; ++$i) {
			$tmp = $this->_sub_qponechar($allchars[0][$i], $l);
			if ($l > $len) {
				$st .= $linefeed;
				$len = $maxline;
			}
			$st .= $tmp;
			$len -= $l;
		}
		$st .= $linefeed;
		return $st;
	}

	function _sub_encode_base64($str, $encoding, $maxline , $linefeed)
	{
		switch ($e = $this->mbemu_internals['encoding'][$encoding]) {
			case 0 : //ascii
			case 6 : //iso-8859-1
				return chunk_split( base64_encode($str) , $maxline, $linefeed);
			case 1 : //euc-jp
			case 2 : //shift-jis
			case 4 : //utf-8
			case 5 : //utf-16
			case 8 : //utf16BE
				$max = preg_match_all('/'.$this->mbemu_internals['regex'][$e].'/', $str, $allchars);
				break;
			case 3 : //jis
				$max = preg_match_all('/'.$this->mbemu_internals['regex'][3].'/', $str, $allchunks);  // 文字種ごとの配列に分解
				$st = ''; // BASE64変換後の文字列
				$maxbytes = floor($maxline * 3 / 4);  //1行に変換可能なバイト数
				$len = $maxbytes;  // その行に追加可能なバイト数
				$line = '';  //1行分の変換前の文字列
				$needterminate = FALSE; //最後にエスケープシーケンスが必要かどうか
				for ($i = 0; $i < $max; ++$i) {
					if (ord($allchunks[1][$i])) { //英数にマッチ
						if ($needterminate) {
							$line .= chr(0x1B).'(B';
							$len -= 3;
						}
						$tmpstr = $allchunks[1][$i];  //追加する文字列
						$l = strlen($tmpstr);  //追加する文字列の長さ
						while ($l > $len) {
							$line .= substr($tmpstr, 0, $len);
							$st .= base64_encode($line).$linefeed;
							$l -= $len;
							$tmpstr = substr($tmpstr, $len);
							$len = $maxbytes;
							$line = '';
						} 
						$line .= $tmpstr;
						$len -= $l;
						$needterminate = FALSE;
					} elseif (ord($allchunks[2][$i])) { //漢字にマッチ
						$tmpstr = substr($allchunks[0][$i], 3);
						if ($len < 8) { //文字を追加するのに最低8バイト必要なので
							if ($needterminate)
								$line .= chr(0x1B).'(B';
							$st .= base64_encode($line).$linefeed;
							$len = $maxbytes;
							$line = '';
						}
						$l = strlen($tmpstr);
						$line .= chr(0x1B).'$B';
						$len -= 3; 
						while ($l > $len-3) {
							$add = floor(($len-3) / 2) * 2;
							if ($add == 0) break;
							$line .= substr($tmpstr, 0, $add).chr(0x1B).'(B';
							$st .= base64_encode($line).$linefeed;
							$l -= $add;
							$tmpstr = substr($tmpstr, $add);
							$len = $maxbytes-3;
							$line = chr(0x1B).'$B';
						} 
						$line .= $tmpstr;
						$len -= $l;
						$needterminate = TRUE;
						
					} elseif (ord($allchunks[3][$i])) { //半角カナにマッチ
						$tmpstr = $allchunks[3][$i];
						if ($len < 7) { //文字を追加するのに最低7バイト必要なので
							if ($needterminate)
								$line .= chr(0x1B).'(B';
							$st .= base64_encode($line).$linefeed;
							$len = $maxbytes;
							$line = '';
						}
						$l = strlen($tmpstr);
						$line .= chr(0x1B).'(I';
						$len -= 3; 
						while ($l > $len-3) {
							$line .= substr($tmpstr, 0, $len-3).chr(0x1B).'(B';
							$st .= base64_encode($line).$linefeed;
							$l -= $len;
							$tmpstr = substr($tmpstr, $len-3);
							$len = $maxbytes-3;
							$line = chr(0x1B).'(I';
						} 
						$line .= $tmpstr;
						$len -= $l;
						$needterminate = TRUE;
					}
				}
				if ($needterminate) $line .= chr(0x1B).'(B';
				$st .= base64_encode($line).$linefeed;
				return $st;
		}
		$st = ''; // BASE64変換後の文字列
		$maxbytes = floor($maxline * 3 / 4);  //1行に変換可能なバイト数
		$len = $maxbytes;  // その行に追加可能なバイト数
		$line = '';  //1行分の変換前の文字列
		for ($i = 0; $i < $max; ++$i) {
			$l = strlen($allchars[0][$i]);
			if ($l > $len) {
				$st .= base64_encode($line).$linefeed;
				$len = $maxbytes;
				$line = '';
			}
			$line .= $allchars[0][$i];
			$len -= $l;
		}
		$st .= base64_encode($line).$linefeed;
		return $st;
	}

	function mb_encode_mimeheader( $str, $encoding = "ISO-2022-JP", $transfer_encoding = "B", $linefeed = "\r\n")
	{
		if ($transfer_encoding == "b") $transfer_encoding = "B";
		if ($transfer_encoding <> "B") $transfer_encoding = "Q";
		$encoding = strtoupper($encoding);
		
		$head = '=?' . $this->mb_preferred_mime_name ($encoding) . '?'.$transfer_encoding.'?';
		$str = $this->mb_convert_encoding($str, $encoding, $this->mb_internal_encoding());
		$length = 76 - strlen($head) - 4;
		if ($transfer_encoding == "B") {
	        $str = $this->_sub_encode_base64( $str , $encoding, $length, $linefeed);
		} else {
			$str = $this->_sub_quoted_printable_encode($str, $encoding, $length, $linefeed);
		}
		$ar = explode($linefeed, $str);
		$s = '';
		foreach ($ar as $element) {
			if ($element <> '')
				$s .= $head . $element . '?=' .$linefeed;
		}
		return $s;
	}

	function mb_http_input($type = '')
	{
		return FALSE;
	}

	function mb_http_output($encoding = '')
	{
		if ($encoding == '') return $this->mbemu_internals['ini_file']['http_output'];
		if (strtolower($encoding) == 'pass') {
			$this->mbemu_internals['ini_file']['http_output'] = 'pass';
			return TRUE;
		}
		$this->mbemu_internals['ini_file']['http_output'] = $this->mb_preferred_mime_name($encoding);
		return TRUE;
	}


	function mb_output_handler ( $buffer, $status='')
	{
		if ($this->mbemu_internals['ini_file']['http_output'] == 'pass')
			return $buffer;
		return $this->mb_convert_encoding($buffer, $this->mbemu_internals['ini_file']['http_output'], $this->mb_internal_encoding());
	}


	function mb_encode_numericentity($str, $convmap, $encoding="")
	{
		$encoding = $this->regularize_encoding($encoding);
		$str = $this->mb_convert_encoding($str, 'UTF-16BE', $encoding);
		$ar = unpack("n*", $str);
		$s = "";
		foreach($ar as $char) {
			$max = count($convmap);
			for ($i = 0; $i < $max; $i += 4) {
				if (($convmap[$i] <= $char) && ($char <= $convmap[$i+1])) {
					$char += $convmap[$i+2];
					$char &= $convmap[$i+3];
					$s .= sprintf("&#%u;", $char);
					break;
				}
			}
			if ($i >= $max) $s .= pack("n*", $char);
		}
		return $s;
	}

	function mb_decode_numericentity ($str, $convmap, $encoding="")
	{
		$encoding = $this->regularize_encoding($encoding);
		$ar = preg_split('/(&#[0-9]+;)/', $str, -1, PREG_SPLIT_DELIM_CAPTURE);
		$s = '';
		$max = count($convmap);
		foreach($ar as $chunk) {
			if (preg_match('/&#([0-9]+);/', $chunk, $match)) {
				for ($i = 0; $i < $max; $i += 4) {
					$num = $match[1] - $convmap[$i+2];
					if (($convmap[$i] <= $num) && ($num <= $convmap[$i+1])) {
						$ucs2 = pack('n*', $num);
						$s .= $this->mb_convert_encoding($ucs2, $encoding, 'UTF-16BE');
						break;
					}
				}
				if ($i >= $max) $s .= $chunk;
			} else {
				$s .= $chunk;
			}
		}
		return $s;
	}

	function mb_strtoupper($str, $encoding='')
	{
		$this->load_table('upper');

		$encoding = $this->regularize_encoding($encoding);
		$str = $this->mb_convert_encoding($str, 'UTF-8', $encoding);

		$max = preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $allchars);  // make array of chars
		$newst = '';
		for ($i = 0; $i < $max; ++$i) {
			$val = $this->_utf8ucs2($allchars[0][$i]); //get ucs2 value
			if ((0x61 <= $val) && ($val <= 0x7a)) {
				$val -= 0x20;
				$newst .= $this->_ucs2utf8($val);
			} elseif (isset($this->mbemu_internals['upperarray'][$val])) {
				$newst .= $this->_ucs2utf8($this->mbemu_internals['upperarray'][$val]);
			} else {
				$newst .= $allchars[0][$i];
			}
		}
		return $this->mb_convert_encoding($newst, $encoding, 'UTF-8');
	}

	function mb_strtolower($str, $encoding='')
	{
		$this->load_table('lower');

		$encoding = $this->regularize_encoding($encoding);
		$str = $this->mb_convert_encoding($str, 'UTF-8', $encoding);

		$max = preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $allchars);  // make array of chars
		$newst = '';
		for ($i = 0; $i < $max; ++$i) {
			$val = $this->_utf8ucs2($allchars[0][$i]); //get ucs2 value
			if ((0x41 <= $val) && ($val <= 0x5a)) {
				$val += 0x20;
				$newst .= $this->_ucs2utf8($val);
			} elseif (isset($this->mbemu_internals['lowerarray'][$val])) {
				$newst .= $this->_ucs2utf8($this->mbemu_internals['lowerarray'][$val]);
			} else {
				$newst .= $allchars[0][$i];
			}
		}
		return $this->mb_convert_encoding($newst, $encoding, 'UTF-8');
	}

	function mb_convert_case($str, $case, $encoding='')
	{
		switch($case) {
			case MB_CASE_UPPER :
				return $this->mb_strtoupper($str, $encoding);
			case MB_CASE_LOWER :
				return $this->mb_strtolower($str, $encoding);
			case MB_CASE_TITLE :
				$this->load_table('upper');
				$this->load_table('lower');
				$encoding = $this->regularize_encoding($encoding);
				$str = $this->mb_convert_encoding($str, 'UTF-8', $encoding);

				$max = preg_match_all('/'.$this->mbemu_internals['regex'][4].'/', $str, $allchars);  // make array of chars
				$newst = '';
				$isalpha = FALSE;
				for ($i = 0; $i < $max; ++$i) {
					$val = $this->_utf8ucs2($allchars[0][$i]); //get ucs2 value
					if ((0x41 <= $val) && ($val <= 0x5a)) {
						if ($isalpha) {
							$val += 0x20; // to lower;
						} else {
							$isalpha = TRUE;
						}
						$newst .= $this->_ucs2utf8($val);
					} elseif ((0x61 <= $val) && ($val <= 0x7a)){
						if (!$isalpha) {
							$val -= 0x20; // to upper
							$isalpha = TRUE;
						}
						$newst .= $this->_ucs2utf8($val);
					} elseif ($upper = @$this->mbemu_internals['upperarray'][$val]) { // this char is lower
						if ($isalpha) {
							$newst .= $this->_ucs2utf8($val);
						} else {
							$isalpha = TRUE;
							$newst .= $this->_ucs2utf8($upper);
						}
					} elseif ($lower = @$this->mbemu_internals['lowerarray'][$val]) { // this char is upper
						if ($isalpha) {
							$newst .= $this->_ucs2utf8($lower);
						} else {
							$isalpha = TRUE;
							$newst .= $this->_ucs2utf8($val);
						}
					} else {
						$isalpha = FALSE;
						$newst .= $allchars[0][$i];
					}
				}
				return $this->mb_convert_encoding($newst, $encoding, 'UTF-8');
		}
	}

	function iconvErrorHandler($errno, $errstr, $errfile, $errline)
	{
		if ($errno === 8) {
			$this->error = $errno;
		    return true;
		}
	    return false;
	}

	function _print_str($str) {
		$all = unpack("C*", $str);
		$s = '';
		foreach($all as $char) {
			$s .= sprintf(" %2X",$char);
		}
		print $s."\n";
	}

}

}
?>