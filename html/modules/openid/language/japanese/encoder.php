<?php
/**
 * Encoder for EUC-JP
 *
 * @version $Id$
 */
class Openid_Encoder
{
	function fromUtf8($from_string)
	{
		$from_enc = mb_detect_encoding($from_string ,'ASCII,JIS,UTF-8,EUC-JP,SJIS,EUC-CN,BIG-5,EUC-KR');
		$local_string = ('EUC-JP' == $from_enc) ? $from_string : mb_convert_encoding($from_string, 'EUC-JP', $from_enc);
		return $local_string;
	}
	
	function toUtf8($local_string)
	{
		$utf8_string = mb_convert_encoding($local_string, "utf-8", 'EUC-JP');
		return $utf8_string;
	}
}
?>