<?php /* JcodeConvert() wrapper function v1.0.2 by TOMO 2004/06/23 */

require_once dirname(__FILE__).'/jcode.php';

function jcode_convert_encoding($str, $to, $from = '')
{

	$jc_to   = _check_encoding($to);
	$jc_from = _check_encoding($from);

	if ($jc_from == 0) {
		$jc_from = AutoDetect($str);
	}

	if ($jc_to == 4) {
		global $table_jis_utf8;
		include_once dirname(__FILE__).'/code_table.jis2ucs';
	}
	if ($jc_from == 4) {
		global $table_utf8_jis;
		include_once dirname(__FILE__).'/code_table.ucs2jis';
	}
	return JcodeConvert($str, $jc_from, $jc_to);
}

function _check_encoding($str_encoding)
{
	switch (strtolower($str_encoding)) {
		case 'e':
		case 'euc':
		case 'euc-jp':
			$jc_encoding = 1;
			break;
		case 's':
		case 'sjis':
		case 'shift_jis':
			$jc_encoding = 2;
			break;
		case 'j':
		case 'jis':
		case 'iso-2022-jp':
			$jc_encoding = 3;
			break;
		case 'u':
		case 'utf8':
		case 'utf-8':
			$jc_encoding = 4;
			break;
		default:
			$jc_encoding = 0;
			break;
	}
	return $jc_encoding;
}

?>
