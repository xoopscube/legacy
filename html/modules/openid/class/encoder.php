<?php
/**
 * generic encoder
 * @author domifara(http://domifara.lolipop.jp/)
 * @version $Id$
 */
	class Openid_Encoder
	{
		function fromUtf8($string)
		{
			//when POST value unknown change
			if ( function_exists('mb_detect_encoding') ) {
				$in_charset = 'UTF-8';
				$out_charset = defined('_CHARSET') ? _CHARSET : mb_internal_encoding();
				$out_charset = strtoupper($out_charset);
				if ( $out_charset == 'GB2312'){
					$out_charset = 'EUC-CN';
				}
				$in_charset = mb_detect_encoding($string ,'ASCII,JIS,UTF-8,EUC-JP,SJIS,EUC-CN,BIG-5,EUC-KR');
				$local_string = ($in_charset == $out_charset) ? $string : mb_convert_encoding($string, $out_charset, $in_charset);
				return $local_string;
			}
			return $string;
		}

		function toUtf8($string)
		{
			//mb_string
			if ( function_exists('mb_convert_encoding') ) {
                $in_charset = defined('_CHARSET') ? _CHARSET : mb_internal_encoding();
                $in_charset = strtoupper($in_charset);
                if ('UTF-8' == $in_charset){
                    return $string;
                }
				if ( $in_charset == 'GB2312'){
					$in_charset = 'EUC-CN';
				}
				$converted_text = @mb_convert_encoding($string, 'UTF-8', $in_charset);
				return $converted_text;
			}
            if (defined('_CHARSET') && strtoupper(_CHARSET) == 'UTF-8') {
                return $string;
            }
            //xml_ parser
			if( function_exists('utf8_encode') ) {
				$converted_text = utf8_encode($string);
				return $converted_text;
			}
            if (!defined('_CHARSET')) {
                return $string;
            }
            $in_charset = strtoupper(_CHARSET);
			$out_charset = 'UTF-8';
			//chinese
			if ( substr($GLOBALS['xoopsConfig']['language'] , 1 , 7 ) == 'chinese') {
				if(function_exists("xoopschina_convert_encoding")) {
					$converted_text = xoopschina_convert_encoding($string, $in_charset, $out_charset);
					return $converted_text;
				}
				$xconv_handler = @xoops_getmodulehandler('xconv', 'xconv', true);
				if ( $in_charset == 'GB2312'){
					$in_charset = 'EUC-CN';
				}
				if($xconv_handler &&
					$converted_text = @$xconv_handler->convert_encoding($string, $out_charset, $in_charset)
				){
					return $converted_text;
				}
			}
			//iconv
			if( function_exists('iconv') ) {
				if ( $in_charset == 'GB2312'){
					$in_charset = 'EUC-CN';
				}
				$converted_text = @iconv($in_charset, $out_charset . "//TRANSLIT", $string);
				if( !empty($converted_text) && $converted_text != '')  {
					return $converted_text;
				}
			}
			return $string;
		}
	}//end of class DiscussEncoder
?>