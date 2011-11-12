<?php
// PukiWiki - Yet another WikiWikiWeb clone.
// $Id: mbstring.php,v 1.3 2007/09/02 14:53:58 nao-pon Exp $
// Copyright (C) 2003-2005 PukiWiki Developers Team
// License: GPL v2 or (at your option) any later version
//
// mbstring-extension alternate functions
// (will work with LANG == 'ja' and EUC-JP environment only)

/*
 * mbstring extension がサーバー側に存在しない時の代替関数
 *
 */

if (!defined('SOURCE_ENCODING')) define('SOURCE_ENCODING', (defined('_CHARSET')? _CHARSET : 'EUC-JP'));

// jcodeの所在
if (is_readable(dirname(__FILE__).'/jcode/jcode_wrapper.php') && !function_exists('jcode_convert_encoding')) {
	require_once(dirname(__FILE__).'/jcode/jcode_wrapper.php');
}

// jcodeが存在しない場合、マルチバイト文字や漢字コードを扱えない
if (! function_exists('jcode_convert_encoding')) {

//	die_message('Multibyte functions cannot be used. Please read "mbstring.php" for an additional installation procedure of "jcode".');

	function jstrlen($str)
	{
		return strlen($str);
	}

	function jsubstr($str, $start, $length)
	{
		return substr($str, $start, $length);
	}

	function AutoDetect($str)
	{
		return 0;
	}

	function jcode_convert_encoding($str, $to_encoding, $from_encoding)
	{
		return $str;
	}
}

// mb_convert_encoding -- 文字エンコーディングを変換する
if (! function_exists("mb_convert_encoding")) {
function mb_convert_encoding($str, $to_encoding, $from_encoding = '')
{
	// 拡張: 配列を受けられるように
	// mb_convert_variable対策
	if (is_array($str)) {
		foreach ($str as $key=>$value) {
			$str[$key] = mb_convert_encoding($value, $to_encoding, $from_encoding);
		}
		return $str;
	}
	return jcode_convert_encoding($str, $to_encoding, $from_encoding);
}
}

// mb_convert_variables -- 変数の文字コードを変換する
if (! function_exists("mb_convert_variables")) {
function mb_convert_variables($to_encoding, $from_encoding, &$vars)
{
	// 補助関数:配列を再帰的にjoinする
	if (! function_exists("mb_convert_variables_join_array")) {
	function mb_convert_variables_join_array($glue, $pieces)
	{
		$arr = array();
		foreach ($pieces as $piece) {
			$arr[] = is_array($piece) ? mb_convert_variables_join_array($glue, $piece) : $piece;
		}
		return join($glue, $arr);
	}
	}

	// 注: 可変長引数ではない。init.phpから呼ばれる1引数のパターンのみをサポート
	// 正直に実装するなら、可変引数をリファレンスで受ける方法が必要
	if (is_array($from_encoding) || $from_encoding == '' || $from_encoding == 'auto')
		$from_encoding = mb_detect_encoding(mb_convert_variables_join_array(' ', $vars));

	if ($from_encoding != 'ASCII' && $from_encoding != SOURCE_ENCODING)
		$vars = mb_convert_encoding($vars, $to_encoding, $from_encoding);

	return $from_encoding;
}
}

// mb_detect_encoding -- 文字エンコーディングを検出する
if (! function_exists("mb_detect_encoding")) {
function mb_detect_encoding($str, $encoding_list = '')
{
	static $codes = array(0=>'ASCII', 1=>'EUC-JP', 2=>'SJIS', 3=>'JIS', 4=>'UTF-8');

	// 注: $encoding_listは使用しない。
	$code = AutoDetect($str);
	if (! isset($codes[$code])) $code = 0; // oh ;(

	return $codes[$code];
}
}

// mb_detect_order --  文字エンコーディング検出順序の設定/取得
if (! function_exists("mb_detect_order")) {
function mb_detect_order($encoding_list = NULL)
{
	static $list = array();

	// 注: 他の関数に影響を及ぼさない。呼んでも無意味。
	if ($encoding_list === NULL) return $list;

	$list = is_array($encoding_list) ? $encoding_list : explode(',', $encoding_list);
	return TRUE;
}
}

// mb_encode_mimeheader -- MIMEヘッダの文字列をエンコードする
if (! function_exists("mb_encode_mimeheader")) {
function mb_encode_mimeheader($str, $charset = 'ISO-2022-JP', $transfer_encoding = 'B', $linefeed = "\r\n")
{
	// 注: $transfer_encodingに関わらずbase64エンコードを返す
	$str = mb_convert_encoding($str, $charset, 'auto');
	return '=?' . $charset . '?B?' . $str;
}
}

// mb_http_output -- HTTP出力文字エンコーディングの設定/取得
if (! function_exists("mb_http_output")) {
function mb_http_output($encoding = '')
{
	return SOURCE_ENCODING; // 注: 何もしない
}
}

// mb_internal_encoding --  内部文字エンコーディングの設定/取得
if (! function_exists("mb_internal_encoding")) {
function mb_internal_encoding($encoding = '')
{
	return SOURCE_ENCODING; // 注: 何もしない
}
}

// mb_language --  カレントの言語を設定/取得
if (! function_exists("mb_language")) {
function mb_language($language = NULL)
{
	static $mb_language = FALSE;

	if ($language === NULL) return $mb_language;
	$mb_language = $language;

	return TRUE; // 注: 常にTRUEを返す
}
}

// mb_strimwidth -- 指定した幅で文字列を丸める
if (! function_exists("mb_strimwidth")) {
function mb_strimwidth($str, $start, $width, $trimmarker = '', $encoding = '')
{
	if ($start == 0 && $width <= strlen($str)) return $str;

	// 注: EUC-JP専用, $encodingを使用しない
	$chars = unpack('C*', $str);
	$substr = '';

	while (! empty($chars) && $start > 0) {
		--$start;
		if (array_shift($chars) >= 0x80)
			array_shift($chars);
	}
	if ($b_trimmarker = (count($chars) > $width)) {
		$width -= strlen($trimmarker);
	}
	while (! empty($chars) && $width-- > 0) {
		$char = array_shift($chars);
		if ($char >= 0x80) {
			if ($width-- == 0) break;
			$substr .= chr($char);
			$char = array_shift($chars);
		}
		$substr .= chr($char);
	}
	if ($b_trimmarker) $substr .= $trimmarker;

	return $substr;
}
}

// mb_strlen -- 文字列の長さを得る
if (! function_exists("mb_strlen")) {
function mb_strlen($str, $encoding = '')
{
	// 注: EUC-JP専用, $encodingを使用しない
	return jstrlen($str);
}
}

// mb_substr -- 文字列の一部を得る
if (! function_exists("mb_substr")) {
function mb_substr($str, $start, $length = NULL, $encoding = '')
{
	// 注: EUC-JP専用, $encodingを使用しない
	return jsubstr($str, $start, ($length === NULL) ? jstrlen($str) : $length);
}
}
?>