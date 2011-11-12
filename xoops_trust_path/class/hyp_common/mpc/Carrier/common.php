<?php
/* 変換後の絵文字体系 */
define('MPC_TO_OPTION_RAW' , 'RAW'); // バイナリコード
define('MPC_TO_OPTION_WEB' , 'WEB'); // Web入力コード
define('MPC_TO_OPTION_IMG' , 'IMG'); // 画像
define('MPC_TO_OPTION_MODKTAI' , 'MODKTAI'); // mod_ktai用コード

// {{{ class MPC_common
/**
* 絵文字変換ベースクラス
*
* @author   ryster <ryster@php-develop.org>
* @license  http://www.opensource.org/licenses/mit-license.php The MIT License
* @link     http://php-develop.org/MobilePictogramConverter/
*/
class MPC_Common
{
    /**
    * 変換する文字列
    * @var mixed
    */
    var $string;

    /**
    * 変換する文字列の文字コード (MPC_FROM_CHARSET_SJIS, MPC_FROM_CHARSET_UTF8)
    * @var string
    */
    var $from_charset;

    /**
    * 変換する文字列の絵文字タイプ (MPC_FROM_FOMA, MPC_FROM_EZWEB, MPC_FROM_SOFTBANK)
    * @var string
    */
    var $from;

    /**
    * 変換後絵文字タイプ (MPC_TO_FOMA, MPC_TO_EZWEB, MPC_TO_SOFTBANK)
    * @var string
    */
    var $to;

    /**
    * 変換オプション (MPC_TO_OPTION_RAW, MPC_TO_OPTION_WEB, MPC_TO_OPTION_IMG)
    * @var string
    */
    var $option;

    /**
    * 変換する文字列の絵文字タイプ (MPC_FROM_OPTION_RAW, MPC_FROM_OPTION_WEB, MPC_FROM_OPTION_IMG)
    * @var string
    */
    var $strtype;

    /**
    * i-mode絵文字画像格納パス
    * @var string
    */
    var $i_img_path = 'img/i/';
    var $i_img_size = array('16', '16');

    /**
    * EZweb絵文字画像格納パス
    * @var string
    */
    var $e_img_path = 'img/e/';

    /**
    * SoftBank絵文字画像格納パス
    * @var string
    */
    var $s_img_path = 'img/s/';

    /**
    * i-mode => EZweb変換マップ (map/i2e_table.php参照)
    * @var array
    */
    var $i2e_table = array();

    /**
    * i-mode => SoftBank変換マップ (map/i2s_table.php参照)
    * @var array
    */
    var $i2s_table = array();

    /**
    * SoftBank => i-mode変換マップ (map/s2i_table.php参照)
    * @var array
    */
    var $s2i_table = array();

    /**
    * SoftBank => EZweb変換マップ (map/s2e_table.php参照)
    * @var array
    */
    var $s2e_table = array();

    /**
    * EZweb => i-mode変換マップ (map/e2i_table.php参照)
    * @var array
    */
    var $e2i_table = array();

    /**
    * EZweb => SoftBank変換マップ (map/e2s_table.php参照)
    * @var array
    */
    var $e2s_table = array();

    /**
    * EZweb(icon番号) => EZweb(Shift_JIS Hex)変換マップ (map/e2icon_table.php参照)
    * @var array
    */
    var $e2icon_table = array();

    /**
    * [emj:\d] => i-mode変換マップ (map/emj2i_table.php参照)
    * @var array
    */
    var $emj2i_table = array();

    /**
    * [emj:\d] => SoftBank変換マップ (map/emj2s_table.php参照)
    * @var array
    */
    var $emj2s_table = array();

    /**
    * ((i:xxxx)) => [emj:xxx] 変換マップ
    * @var array
    */
    var $modKtai2i_icon = array();

    /**
    * ((e:xxxx)) => [emj:xxx:ez] 変換マップ
    * @var array
    */
    var $modKtai2e_icon = array();

    /**
    * ((s:xxxx)) => [emj:xxx:sb] 変換マップ
    * @var array
    */
    var $modKtai2s_icon = array();

    /**
    * 変換先の絵文字が存在しなかった場合の代替文字列
    * @var string
    */
    var $substitute = '〓';

    /**
    * 文字列（dec）格納変数
    * @var array
    */
    var $decstring = array();

    /**
    * インクリメント用
    * @var integer
    */
    var $i = 0;

    /**
    * 添字配列用
    * @var integer
    */
    var $n = 0;

    /**
    * 文字列格納変数
    * @var array
    */
    var $unPictograms = array();

    /**
    * 絵文字格納変数
    * @var array
    */
    var $Pictograms = array();

    /**
    * モバイルユーザーエージェント
    * @var array
    */
    var $mobile_user_agent = array(
       'DoCoMo'   => '/^DoCoMo\/\d\.\d[ \/]/',
       'SoftBank' => '/^(?:(?:SoftBank|Vodafone|J-PHONE)\/\d\.\d|MOT-)/',
       'EZweb'    => '/^(?:KDDI-[A-Z]+\d+[A-Z]? )?UP\.Browser\//',
    );

    /**
    * ユーザーエージェント
    * @var string
    */
	var $userAgent = NULL;

    function mail2ModKtai($str ,$mail, $charset) {
		$to = $this->mail_host = '';
		if (preg_match('/docomo\.ne\.jp$/i', $mail)) {
			$to = MPC_TO_FOMA;
			$this->mail_host = MPC_FROM_FOMA;
		} else if (preg_match('/ezweb\.ne\.jp$/i', $mail)) {
			$to = MPC_TO_EZWEB;
			$this->mail_host = MPC_FROM_EZWEB;
		} else if (preg_match('/(?:softbank|vodafone|disney)\.ne\.jp$/i', $mail)) {
			$to = MPC_TO_SOFTBANK;
			$this->mail_host = MPC_FROM_SOFTBANK;
		}
		if ($this->mail_host) {
			$charset = strtolower($charset);
			if ($charset === 'shift-jis') $charset = 'shift_jis';
			if ($charset === 'iso-2022-jp') {
				$_sub = mb_substitute_character();
				mb_substitute_character("long");
				$str = mb_convert_encoding($str, 'UTF-8', 'JIS');
				mb_substitute_character($_sub);

				$str = preg_replace_callback('/JIS\+[0-9A-F]{4}/i', array($this, 'jis2ktaimod'), $str);

				$str = mb_convert_encoding($str, 'JIS', 'UTF-8');
			} else if ($charset === 'shift_jis' || $charset === 'utf-8') {
				$from_encode = ($charset === 'shift_jis')? MPC_FROM_CHARSET_SJIS : MPC_FROM_CHARSET_UTF8;
				$mpc = MobilePictogramConverter::factory('', $this->mail_host, $from_encode, MPC_FROM_OPTION_RAW);
				$mpc->setString($str);
				$str = $mpc->Convert($to, MPC_TO_OPTION_MODKTAI);
				$mpc = null;
			}
		}
		return $str;
    }

    function jis2ktaimod ($match) {
		$str = strtolower(substr($match[0], 4));
		$_str = $str;
		switch ($this->mail_host) {
			case MPC_FROM_FOMA:
				if (empty($this->i_mail2modktai_table)) {
		        	require 'map/i_mail2modktai_table.php';
				}
				if (isset($this->i_mail2modktai_table[$str])) {
					return '((i:' . $this->i_mail2modktai_table[$str] . '))';
				} else {
					return $_str;
				}
			case MPC_FROM_SOFTBANK:
				if (empty($this->s_mail2modktai_table)) {
		        	require 'map/s_mail2modktai_table.php';
				}
				if (isset($this->s_mail2modktai_table[$str])) {
					return '((s:' . $this->s_mail2modktai_table[$str] . '))';
				} else {
					return $_str;
				}
			case MPC_FROM_EZWEB:
			    $first = substr($str, 0, 2 );
			    $second = substr( $str, 2, 2);

			    // 最初の2文字を変換
				$sjis1 = hexdec($first);
				$sjis1 = ($sjis1 - hexdec("21"))/2 + hexdec("81");
				if($sjis1 >= hexdec("9e")) {
		    		$sjis1 += hexdec("40");
		    	}

			    //最後の2文字を変換
			    $buf = hexdec($first) % 2;
			    $sjis2 = hexdec($second);
			    if ( $buf == 1 ) {
			    	$sjis2 += hexdec("1f");
			    } else {
			    	$sjis2 += hexdec("7d");
			    }
			    if ($sjis2 >= hexdec("7f")) {
			    	$sjis2++;
			    }

			    // 16進数に変換
			    $sjis1 = strtolower(dechex($sjis1));
			    $sjis2 = strtolower(dechex($sjis2));

			    // Eメール送出用SJISからKDDI絵文字用SJISに変換
			    if ( $sjis1 === 'eb' ) {
			        $sjis1 = 'f6';
			    } else if ($sjis1 === "ec" ) {
			        $sjis1 = "f7";
			    } else if ($sjis1 === "ed" ) {
			        $sjis1 = "f3";
			    } else if ($sjis1 === "ee" ) {
			        $sjis1 = "f4";
			    }

			    $buf = $sjis1 . $sjis2;

			    return '((e:' . $buf . '))';

		}
		return $_str;
    }

    function euc2ktaimod($str) {
    	if ($this->from === MPC_FROM_SOFTBANK) {
			$ex = '\'((s:\' . join(\'))((s:\', explode(\' \', rtrim(chunk_split(strtolower(bin2hex(str_replace(\'\\"\', \'"\', \'$1\'))), 4, \' \')))) . \'))\'';
			$str = preg_replace('/[\x1B][\x24]((?:[G|E|F|O|P|Q][\x21-\x7E])+)[\x0F]?/e', $ex, $str);
    	} else {
    		$prefix = ($this->from === MPC_FROM_FOMA)? 'i' : 'e';
			$old = mb_substitute_character();
			mb_substitute_character('long');
			$str = mb_convert_encoding($str, 'EUC-JP', 'EUC-JP');
			mb_substitute_character($old);
			$ex = '\'(('.$prefix.':\'.strtolower(\'$1\').\'))\'';
			$str = preg_replace('/BAD\+([0-9A-F]{4})/ie', $ex, $str);
    	}
    	return $str;
    }

    /**
    * ユーザーエージェントからキャリアを自動判別し
    * mod_ktai コードから対応する絵文字に自動変換 by nao-pon
    * mod_ktai: http://labs.yumemi.co.jp/labs/mod/man_contents.html
    *
    * @return string
    */
    function autoConvertModKtai()
    {
        $useragent = (is_null($this->userAgent))? $_SERVER['HTTP_USER_AGENT'] : $this->userAgent;
        if (preg_match($this->getRegexp('DoCoMo'), $useragent)) {
            $to     = MPC_TO_FOMA;
            $option = MPC_TO_OPTION_RAW;
        } elseif (preg_match($this->getRegexp('SoftBank'), $useragent)) {
            $to     = MPC_TO_SOFTBANK;
            $option = MPC_TO_OPTION_WEB;
        } elseif (preg_match($this->getRegexp('EZweb'), $useragent)) {
            $to     = MPC_TO_EZWEB;
            $option = MPC_TO_OPTION_RAW;
        } else {
            $to     = 'COMMON';
            $option = MPC_TO_OPTION_IMG;
        }

        $this->setTo($to);
        $this->setOption($option);
        $str = $this->getString();

        $str = preg_replace_callback('/(<head.+?\/head>|<script.+?\/script>|<style.+?\/style>|<textarea.+?\/textarea>|<[^<>]+?>)|\(\(([eisv]):([0-9a-f]{4})\)\)|\[emj:(\d{1,4})(?::(im|sb|ez))?\]/isS', array(& $this, '_decodeModKtai'), $str);

        return $str;
    }

    /**
    * mod_ktai コードをデコード (サブ関数) by nao-pon
    *
    * @param string $match
    * @return string
    */
    function _decodeModKtai($match)
    {
        if ($match[1]) {
            if ($this->to !== 'COMMON' && strtolower(substr($match[1], 0, 9)) === '<textarea') {
            	$carrier = '';
            	switch($this->to) {
            		case MPC_TO_FOMA:
            			$carrier = MPC_FROM_FOMA;
            			break;
            		case MPC_TO_EZWEB:
            			$carrier = MPC_FROM_EZWEB;
            			break;
            		case MPC_TO_SOFTBANK:
            			$carrier = MPC_FROM_SOFTBANK;
            			break;
            	}
            	if ($carrier) {
            		$mpc = MobilePictogramConverter::factory($match[1], $carrier, $this->from_charset, MPC_FROM_OPTION_MODKTAI);
            		return $mpc->autoConvert();
            	}
            }
            return $match[0];
        }

        if (isset($match[4])) {
	    	$emj_table = 'emj2i_table';
	    	$match[2] = 'i';
	    	if (! empty($match[5])) {
	    		switch (strtolower($match[5])) {
	    			case 'sb' :
	    				$emj_table = 'emj2s_table';
	    				$match[2] = 's';
	    				break;
	    			case 'ez' :
	    				$emj_table = 'e2icon_table';
	    				$match[2] = 'e';
	    				break;
	    			default :
	    				$emj_table = 'emj2i_table';
	    				$match[2] = 'i';
	    		}
	    	}
	    	if (empty($this->$emj_table)) {
				require 'map/'.$emj_table.'.php';
		    }
		    $_table = $this->$emj_table;
		    if (! isset($_table[$match[4]])) {
		    	return $match[0];
		    }
		    $match[3] = ($match[2] === 'e')? dechex($_table[$match[4]]) : $_table[$match[4]];
	    	/*
	    	if ($this->emj_to === 's') {
		    	if (empty($this->i2s_table)) {
					require 'map/i2s_table.php';
			    }
			    $match[2] = 's';
			    $match[3] = dechex($this->i2s_table[hexdec($match[3])]);
			} else if ($this->emj_to === 'e') {
		    	if (empty($this->i2e_table)) {
					require 'map/i2e_table.php';
			    }
	            if (empty($this->e2icon_table)) {
	                require 'map/e2icon_table.php';
	            }
			    $match[2] = 'e';
			    $match[3] = dechex($this->e2icon_table[$this->i2e_table[hexdec($match[3])]]);
			}
			*/
        }

        $mode = strtolower($match[2]);
        if ($mode === 'v') {
        	$mode = 's';
        }

        // ezweb convert to icon number
        $dec = HexDec($match[3]);
        if ($mode === 'e') {
            if (empty($this->e2icon_table)) {
                require 'map/e2icon_table.php';
            }
            $dec = intval(array_search($dec , $this->e2icon_table));
        }
        $_dec = $dec;

        //exists check
        switch($mode) {
            case 'i':
                $table = $mode . '2e_table';
                break;
            case 's':
                $table = $mode . '2e_table';
                break;
            case 'e':
                $table = $mode . '2s_table';
                break;
        }
        if (empty($this->$table)) {
            require 'map/'.$table.'.php';
        }
        $table_array =& $this->$table;
        if (! isset($table_array[$dec])) {
            return $match[0];
        }

        // set convert table
        $table = '';
        switch($this->to) {
            case MPC_TO_FOMA:
                if ($mode !== 'i') {
                    $table = $mode . '2i_table';
                }
                $decode_func = 'i_options_encode';
                break;
            case MPC_TO_SOFTBANK:
                if ($mode !== 's') {
                    $table = $mode . '2s_table';
                }
                $decode_func = 's_options_encode';
                break;
            case MPC_TO_EZWEB:
                if ($mode !== 'e') {
                    $table = $mode . '2e_table';
                }
                $decode_func = 'e_options_encode';
                break;
            default:
                $decode_func = $mode . '_options_encode';
        }

        // convert
        if ($table) {
            if (empty($this->$table)) {
                require 'map/'.$table.'.php';
            }
            $table_array =& $this->$table;
            $dec = (isset($table_array[$dec]))? $table_array[$dec] : FALSE;
        }

        // show image if nonexist
        $_option = '';
        if (! is_numeric($dec)) {
            $dec = $_dec;
            $_option = $this->getOption();
            $this->setOption(MPC_TO_OPTION_IMG);
            $decode_func = $mode . '_options_encode';
        }

        // decode
        $ret = $this->$decode_func($dec);

        if ($_option) $this->setOption($_option);

        return $ret;
    }

   /**
    * mod_ktai コードから対応する Text Pictgram Mobile コードへ変換 by nao-pon
    * mod_ktai: http://labs.yumemi.co.jp/labs/mod/man_contents.html
    * Text Pictgram Mobile: http://openpear.org/package/Text_Pictogram_Mobile
    *
    * @return string
    */
	function modKtai2textPictMobile()
	{
        $str = $this->getString();
        $str = preg_replace_callback('/(<head.+?\/head>|<script.+?\/script>|<style.+?\/style>|<textarea.+?\/textarea>|<[^<>]+?>)|\(\(([eisv]):([0-9a-f]{4})\)\)/isS', array(& $this, '_convertTextPictMobile'), $str);
		return $str;
	}

    /**
    * mod_ktai から対応する Text Pictgram Mobile へ変換 (サブ関数) by nao-pon
    *
    * @param string $match
    * @return string
    */
	function _convertTextPictMobile($match)
	{
        if ($match[1]) {
            return $match[0];
        }
        $mode = strtolower($match[2]);
        if ($mode === 'v') {
        	$mode = 's';
        }

        //exists check
        $table = 'modKtai2'. $mode . '_icon';

        if (empty($this->$table)) {
            $cache = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/cache/mpc_' . $table . '.dat';
            if (is_file($cache)) {
            	$this->$table = unserialize(file_get_contents($cache));
            } else {
            	if ($mode === 'i') {
            		if (empty($this->emj2i_table)) {
            			require 'map/emj2i_table.php';
            		}
            		$_table = $this->emj2i_table;
            		$_table = array_flip($_table);
            	} else if ($mode === 's') {
            		if (empty($this->emj2s_table)) {
            			require 'map/emj2s_table.php';
            		}
            		$_table = $this->emj2s_table;
            		$_table = array_flip($_table);
            	} else if ($mode === 'e') {
		            if (empty($this->e2icon_table)) {
		                require 'map/e2icon_table.php';
		            }
		            $_table = $this->e2icon_table;
            		$_table = array_map('dechex', $_table);
            		$_table = array_map('strtolower', $_table);
            		$_table = array_flip($_table);
            	}
            	$this->$table = $_table;
            	@file_put_contents($cache, serialize($_table));
            }
        }
        $table_array = $this->$table;
        $key = strtolower($match[3]);
        if (! isset($table_array[$key])) {
            return $match[0];
        } else {
        	if ($mode === 'i') {
        		return '[emj:'.$table_array[$key].']';
        	} else if ($mode === 'e') {
        		return '[emj:'.$table_array[$key].':ez]';
        	} else {
        		return '[emj:'.$table_array[$key].':sb]';
        	}
        }
	}

    /**
    * ユーザーエージェントからキャリアを自動判別し
    * 対応する絵文字に自動変換
    *
    * @return string
    */
    function autoConvert($toCharset = null)
    {
        $useragent = (is_null($this->userAgent))? $_SERVER['HTTP_USER_AGENT'] : $this->userAgent;
        if (preg_match($this->getRegexp('DoCoMo'), $useragent)) {
            $to     = MPC_TO_FOMA;
            $option = MPC_TO_OPTION_RAW;
        } elseif (preg_match($this->getRegexp('SoftBank'), $useragent)) {
            $to     = MPC_TO_SOFTBANK;
            $option = MPC_TO_OPTION_WEB;
        } elseif (preg_match($this->getRegexp('EZweb'), $useragent)) {
            $to     = MPC_TO_EZWEB;
            $option = MPC_TO_OPTION_RAW;
        } else {
            $to     = str_replace('MPC_', '', strtoupper(get_class($this)));
            $option = MPC_TO_OPTION_IMG;
        }

        return $this->Convert($to, $option, $toCharset);
    }

    /**
    * 絵文字を指定した絵文字の指定したフォーマットへ変換
    *
    * @param integer $data
    * @return string
    */
    function encoder($data)
    {
        $buf  = '';
        $to   = $this->getTo();
        $c    = ($to == MPC_TO_EZWEB) ? 'e' : (($to == MPC_TO_SOFTBANK) ? 's' : 'i');
        $options_encode = $c.'_options_encode';

        $data = ($to == $this->getFrom()) ? $data : $this->MapSearch($data, $this->getTo());
        if (gettype($data) == 'integer') {
            $buf = $this->$options_encode($data);
        } else {
            $strings = explode('/', $data);
            if (is_array($strings) && count($strings) > 1) {
                foreach ($strings as $value) {
                    $buf .= $this->$options_encode($value);
                }
            } else {
                $buf = ($this->getFromCharset() == MPC_FROM_CHARSET_UTF8) ? mb_convert_encoding($data, 'UTF-8', 'SJIS-win') : $data;
            }
        }
        return $buf;
    }

    /**
    * 絵文字変換マップを検索
    *
    * @param  integer $key
    * @param  integer $to
    * @return string
    */
    function MapSearch($key, $to)
    {
        $from = $this->getFrom();
        $f = (($from == MPC_FROM_FOMA) ? 'i' : (($from == MPC_FROM_EZWEB) ? 'e' : 's'));
        $t = (($to   == MPC_TO_FOMA)   ? 'i' : (($to == MPC_TO_EZWEB)     ? 'e' : 's'));
        $map = $f.'2'.$t.'_table';
        if(empty($this->$map)) {
            require 'map/'.$map.'.php';
        }
        $mapping = $this->$map;
        return (empty($mapping[$key]) == false) ? $mapping[$key] : $this->getSubstitute();
    }

    /**
    * i-mode絵文字（10進数）を指定されたフォーマットへ変換
    *
    * @param  integer $dec
    * @return string
    */
    function i_options_encode($dec)
    {
        switch($this->getOption()) {
            case MPC_TO_OPTION_RAW:
                $buf = pack('H*', dechex($dec));
                if ($this->getFromCharset() == MPC_FROM_CHARSET_UTF8) {
                    $buf = mb_convert_encoding($buf, 'UTF-8', 'SJIS-win');
                }
                break;
            case MPC_TO_OPTION_WEB:
                $buf = ($dec >= 63921 && $dec <= 63996) ? '&#x'.strtoupper(bin2hex(mb_convert_encoding(pack('H*', dechex($dec)), 'unicode', 'SJIS-win'))).';' : '&#'.$dec.';';
                break;
            case MPC_TO_OPTION_IMG:
                $buf = '<img src="'.rtrim($this->i_img_path, '/').'/'.$dec.'.gif" alt="((i:'.dechex($dec).'))" border="0" width="'.$this->i_img_size[0].'" height="'.$this->i_img_size[0].'" />';
                break;
            case MPC_TO_OPTION_MODKTAI:
                $buf = '((i:'.dechex($dec).'))';
                break;
        }
        return $buf;
    }

    /**
    * EZweb絵文字（icon番号）を指定されたフォーマットへ変換
    *
    * @param  integer $iconno
    * @return string
    */
    function e_options_encode($iconno)
    {
        switch($this->getOption()) {
            case MPC_TO_OPTION_RAW:
                if (empty($this->e2icon_table)) {
                    require 'map/e2icon_table.php';
                }
                $hex = dechex($this->e2icon_table[$iconno]);
                $buf = ($this->getFromCharset() == MPC_FROM_CHARSET_UTF8) ? mb_convert_encoding(pack('H*', dechex(hexdec($hex) - 1792)), 'UTF-8', 'unicode') : pack('H*', $hex);
                break;
            case MPC_TO_OPTION_WEB:
                $buf = '<img localsrc="'.$iconno.'" alt="" />';
                break;
            case MPC_TO_OPTION_IMG:
                $width = ($iconno == 174) ? 7 : (($iconno == 175) ? 4 : 15);
                $buf = '<img src="'.rtrim($this->e_img_path, '/').'/'.$iconno.'.gif" alt="((e:'.dechex($this->e2icon_table[$iconno]).'))" border="0" width="'.$width.'" height="15" />';
                break;
            case MPC_TO_OPTION_MODKTAI:
                if (empty($this->e2icon_table)) {
                    require 'map/e2icon_table.php';
                }
                $buf = '((e:'.dechex($this->e2icon_table[$iconno]).'))';
                break;
        }
        return $buf;
    }

    /**
    * SoftBank絵文字（10進数）を指定されたフォーマットへ変換
    *
    * @param  integer $dec
    * @return string
    */
    function s_options_encode($dec)
    {
        switch($this->getOption()) {
            case MPC_TO_OPTION_RAW:
                list($hex1, $hex2) = sscanf(dechex($dec), '%02s%02s');
                $dec1  = hexdec($hex1);
                $dec2  = hexdec($hex2);
                $num   = ($dec1 == 0x51) ? 0x60 : (($dec2 <= 0x5F) ? 0x60 : 0x20);
                $char2 = ($dec1 == 0x47) ? 0x80 : (($dec1 == 0x45) ? 0x84 : (($dec1 == 0x46) ? 0x88 : (($dec1 == 0x4F) ? 0x8C : (($dec1 == 0x50) ? 0x90 : 0x94))));
                if ($dec2 > 0x5F) {
                    $char2++;
                }
                $char3 = $dec2 + $num;
                $buf   = pack('C*', 0xEE, $char2, $char3);

                if ($this->getFromCharset() === 'SJIS') {
                    if (($char2 == 0x80 && ($char3 >= 0x81 && $char3 <= 0xBF)) || ($char2 == 0x81 && ($char3 >= 0x80 && $char3 <= 0x9A))) {
                        $diff = (($char2 == 0x80 && $char3 == 0xBF) || ($char2 == 0x81 && ($char3 >= 0x80 && $char3 <= 0x9A))) ? 6465 : 6464;
                    } elseif (($char2 == 0x84 && ($char3 >= 0x81 && $char3 <= 0xBF)) || ($char2 == 0x85 && ($char3 >= 0x80 && $char3 <= 0x9A))) {
                        $diff = (($char2 == 0x84 && $char3 == 0xBF) || ($char2 == 0x85 && ($char3 >= 0x80 && $char3 <= 0x9A))) ? 5697 : 5696;
                    } elseif (($char2 == 0x88 && ($char3 >= 0x81 && $char3 <= 0xBF)) || ($char2 == 0x89 && ($char3 >= 0x80 && $char3 <= 0x93))) {
                        $diff = 5536;
                    } elseif (($char2 == 0x8C && ($char3 >= 0x81 && $char3 <= 0xBF)) || ($char2 == 0x8D && ($char3 >= 0x80 && $char3 <= 0x8D))) {
                        $diff = 5792;
                    } elseif (($char2 == 0x90 && ($char3 >= 0x81 && $char3 <= 0xBF)) || ($char2 == 0x91 && ($char3 >= 0x80 && $char3 <= 0x8C))) {
                        $diff = (($char2 == 0x90 && $char3 == 0xBF) || ($char2 == 0x91 && ($char3 >= 0x80 && $char3 <= 0x8C))) ? 5953 : 5952;
                    } else {
                        $diff = 5792;
                    }

                    $buf = pack('H*', dechex(hexdec(bin2hex(mb_convert_encoding($buf, 'unicode', 'UTF-8'))) + $diff));
                }
                break;
            case MPC_TO_OPTION_WEB:
                $buf = pack('H*', '1B24'.dechex($dec).'0F');
                break;
            case MPC_TO_OPTION_IMG:
                $width = ($dec >= 20828 && $dec <= 20830) ? 18 : 15;
                $buf = '<img src="'.rtrim($this->s_img_path, '/').'/'.$dec.'.gif" alt="((s:'.dechex($dec).'))" border="0" width="'.$width.'" height="15" />';
                break;
            case MPC_TO_OPTION_MODKTAI:
                $buf = '((s:'.dechex($dec).'))';
                break;
        }
        return $buf;
    }

    /**
    * 文字列の検査
    * 絵文字の可能性があるなら文字列（10進数）を格納した配列を返す
    *
    * return array
    */
    function Inspection()
    {
        $ds = $this->getCurrentDS();
        // 1byte
        if ($ds >= 0x00 && $ds <= 0x7F || ($this->getFromCharset() === MPC_FROM_CHARSET_SJIS && $ds >= 0xA0 && $ds <= 0xDF)) {
            $this->setUnPictogram(pack('C*', $ds));
        // 2byte <=
        } elseif ($this->getFromCharset() === MPC_FROM_CHARSET_SJIS && isset($this->decstring[$this->i + 1])) {
            $chars = array($ds, $this->decstring[$this->i + 1]);
            $this->i++;
        } else {
            if ($ds >= 0xE0 && $ds <= 0xEF && isset($this->decstring[$this->i + 1]) && isset($this->decstring[$this->i + 2])) {
                $chars = array($ds, $this->decstring[$this->i + 1], $this->decstring[$this->i + 2]);
                $this->i += 2;
            } else {
                $this->setUnPictogram(pack('C*', $ds));
            }
        }
        if (empty($chars) === false) {
            return $chars;
        } else {
            return null;
        }
    }

    /**
    * 現在の文字列（10進数を取得）
    *
    * @return string
    */
    function getCurrentDS()
    {
        if (isset($this->decstring[$this->i])) {
            return $this->decstring[$this->i];
        }
        return null;
    }

    /**
    * 文字列（10進数）を格納
    *
    * @param array
    */
    function setDS($decstrings)
    {
        if (is_array($decstrings)) {
            $this->decstring = $decstrings;
        }
    }

    /**
    * 文字列を配列に格納
    *
    * @param string $str
    */
    function setUnPictogram($str)
    {
        $this->unPictograms[$this->n] = $str;
        $this->n++;
    }

    /**
    * 文字列を取得
    *
    * @return array
    */
    function getUnPictograms()
    {
        return $this->unPictograms;
    }

    /**
    * 格納されている文字列を開放
    *
    * @return void
    */
    function ReleaseUnPictograms()
    {
        $this->unPictograms = array();
    }

    /**
    * 絵文字を配列に格納
    *
    * @param string $pictogram
    */
    function setPictogram($pictogram)
    {
        $this->Pictograms[$this->n] = $pictogram;
        $this->n++;
    }

    /**
    * 絵文字を取得
    *
    * @return array
    */
    function getPictograms()
    {
        return $this->Pictograms;
    }

    /**
    * 格納されている絵文字を開放
    *
    * @return void
    */
    function ReleasePictograms()
    {
        $this->Pictograms = array();
    }

    /**
    * メモリ開放
    *
    * @return void
    */
    function Clean()
    {
        $this->i2e_table = $this->i2s_table = $this->s2i_table = $this->s2e_table = $this->e2i_table = $this->e2s_table = $this->e2icon_table = array();
    }

    /**
    * 指定したキャリアのRegexを取得
    *
    * @param string $carrier
    * @return string
    */
    function getRegexp($carrier)
    {
        return $this->mobile_user_agent[$carrier];
    }


    /**
    * 絵文字画像格納ディレクトリの一括設定
    *
    * @param string $path
    */
    function setImagePath($path)
    {
        $path = rtrim($path, '/');
        $this->setFOMAImagePath($path.'/i/');
        $this->setEZwebImagePath($path.'/e/');
        $this->setSoftBankImagePath($path.'/s/');
    }

    /**
    * FOMA絵文字画像格納ディレクトリの設定
    *
    * @param string $path
    */
    function setFOMAImagePath($path)
    {
        $this->i_img_path = $path;
    }

    /**
    * 設定されているFOMA絵文字画像格納ディレクトリを取得
    *
    * @return string
    */
    function getFOMAImagePath()
    {
        return $this->i_img_path;
    }

    /**
    * EZweb絵文字画像格納ディレクトリの設定
    *
    * @param string $path
    */
    function setEZwebImagePath($path)
    {
        $this->e_img_path = $path;
    }

    /**
    * 設定されているEZweb絵文字画像格納ディレクトリを取得
    *
    * @return string
    */
    function getEZwebImagePath()
    {
        return $this->e_img_path;
    }

    /**
    * SoftBank絵文字画像格納ディレクトリの設定
    *
    * @param string $path
    */
    function setSoftBankImagePath($path)
    {
        $this->s_img_path = $path;
    }

    /**
    * 設定されているSoftBank絵文字画像格納ディレクトリを取得
    *
    * @return string
    */
    function getSoftBankImagePath()
    {
        return $this->s_img_path;
    }

    /**
    * 変換する文字列の設定
    *
    * @param mixed $str
    */
    function setString($string, $convert = TRUE)
    {
        if ($convert) {
            $charset = ($this->getFromCharset() == MPC_FROM_CHARSET_SJIS) ? 'SJIS-win' : 'UTF-8';
            $this->string = mb_convert_encoding($string, $charset, $charset);
        } else {
            $this->string = $string;
        }
    }

    /**
    * 設定されている変換する文字列を取得
    *
    * @return mixed
    */
    function getString()
    {
        return $this->string;
    }

    /**
    * 変換前絵文字のキャリア設定 (MPC_FROM_FOMA, MPC_FROM_EZWEB, MPC_FROM_SOFTBANK)
    *
    * @param string $from
    */
    function setFrom($from)
    {
        $this->from = $from;
    }

    /**
    * 設定されている変換前絵文字キャリアを取得
    *
    * @return string
    */
    function getFrom()
    {
        return $this->from;
    }

    /**
    * 変換後絵文字のキャリア設定 (MPC_TO_FOMA, MPC_TO_EZWEB, MPC_TO_SOFTBANK)
    *
    * @param string $to
    */
    function setTo($to)
    {
        $this->to = $to;
    }

    /**
    * 設定されている変換後絵文字のキャリアを取得
    *
    * @return string
    */
    function getTo()
    {
        return $this->to;
    }

    /**
    * 変換オプション設定 (MPC_TO_OPTION_RAW, MPC_TO_OPTION_WEB, MPC_TO_OPTION_IMG)
    *
    * @param string $to
    */
    function setOption($option)
    {
        $this->option = $option;
    }

    /**
    * 設定されている変換オプションを取得
    *
    * @param string $to
    */
    function getOption()
    {
        return $this->option;
    }

    /**
    * 変換前絵文字のタイプ設定 (MPC_FROM_OPTION_RAW, MPC_FROM_OPTION_WEB, MPC_FROM_OPTION_IMG)
    *
    * @param string $strtype
    */
    function setStringType($strtype)
    {
        $this->strtype = $strtype;
    }

    /**
    * 設定されている変換前絵文字のタイプを取得
    *
    * @return string
    */
    function getStringType()
    {
        return $this->strtype;
    }

    /**
    * 変換する文字列の文字コードを設定 (MPC_FROM_CHARSET_SJIS, MPC_FROM_CHARSET_UTF8)
    *
    * @param string $charset
    */
    function setFromCharset($charset)
    {
        $this->from_charset = strtoupper($charset);
    }

    /**
    * 設定されている変換する文字列の文字コードを取得
    *
    * @return string
    */
    function getFromCharset()
    {
        return $this->from_charset;
    }

    /**
    * 指定した、正規表現を取得
    *
    * @param $type (WEB, IMG)
    * @return string
    */
    function getRegex($type) {
        if (isset($this->regex[$type])) {
            return $this->regex[$type];
        }
    }

    /**
    * 代替文字列設定
    *
    * @param mixed $str
    */
    function setSubstitute($str) {
        $this->substitute = $str;
    }

    /**
    * 設定されている代替文字列を取得
    *
    * @return string
    */
    function getSubstitute() {
        return $this->substitute;
    }

    /**
    * 10進数（配列）を16進数に変換
    * @param array $decs
    * @return string
    */
    function decs2hex($decs, $upper = true)
    {
        if (is_array($decs) === false) {
            return null;
        }
        $hex = '';
        foreach ($decs as $dec) {
            $hex .= dechex($dec);
        }
        return ($upper == true) ? strtoupper($hex) : $hex;
    }

}
// }}}
?>