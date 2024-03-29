<?php
// $Id: xoopsmailerlocal.php,v 1.2 2008/09/21 06:36:10 minahito Exp $
// Author: NobuNobu (Nobuki@Kowa.ORG)
// URL:  https://jp.xoops.org
// Project: The XOOPS Project

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsMailerLocal extends XoopsMailer
{

    public function __construct()
    {
        $this->multimailer = new XoopsMultiMailerLocal();
        $this->reset();
        $this->charSet = 'iso-2022-jp';
        $this->encoding = '7bit';
        $this->multimailer->CharSet = $this->charSet;
        $this->multimailer->SetLanguage('ja', XOOPS_ROOT_PATH . '/class/mail/phpmailer/language/');
        $this->multimailer->Encoding = '7bit';
    }

    public function encodeFromName($text)
    {
        return $this->STRtoJIS($text, _CHARSET);
    }

    public function encodeSubject($text)
    {
        if ($this->multimailer->needs_encode) {
            return $this->STRtoJIS($text, _CHARSET);
        } else {
            return $text;
        }
    }

    public function encodeBody(&$text)
    {
        if ($this->multimailer->needs_encode) {
            $text = $this->STRtoJIS($text, _CHARSET);
        }
    }

    /*-------------------------------------
     PHP FORM MAIL 1.01 by TOMO
     URL : https://www.spencernetwork.org/
     E-Mail : groove@spencernetwork.org
    --------------------------------------*/
    public function STRtoJIS($str, $from_charset)
    {
        if (function_exists('mb_convert_encoding')) { //Use mb_string extension if exists.
            $str_JIS  = mb_convert_encoding(mb_convert_kana($str, 'KV', $from_charset), 'JIS', $from_charset);
        } elseif ('EUC-JP' == $from_charset) {
            $str_JIS = '';
            $mode = 0;
            $b = unpack('C*', $str);
            $n = count($b);
            for ($i = 1; $i <= $n; $i++) {
                if (0x8E == $b[$i]) {
                    if (2 != $mode) {
                        $mode = 2;
                        $str_JIS .= pack('CCC', 0x1B, 0x28, 0x49);
                    }
                    $b[$i+1] -= 0x80;
                    $str_JIS .= pack('C', $b[$i + 1]);
                    $i++;
                } elseif ($b[$i] > 0x8E) {
                    if (1 != $mode) {
                        $mode = 1;
                        $str_JIS .= pack('CCC', 0x1B, 0x24, 0x42);
                    }
                    $b[$i] -= 0x80;
                    $b[$i+1] -= 0x80;
                    $str_JIS .= pack('CC', $b[$i], $b[$i + 1]);
                    $i++;
                } else {
                    if (0 != $mode) {
                        $mode = 0;
                        $str_JIS .= pack('CCC', 0x1B, 0x28, 0x42);
                    }
                    $str_JIS .= pack('C', $b[$i]);
                }
            }
            if (0 != $mode) {
                $str_JIS .= pack('CCC', 0x1B, 0x28, 0x42);
            }
        }
        return $str_JIS;
    }
}

class XoopsMultiMailerLocal extends XoopsMultiMailer
{

    public $needs_encode;

    public function __construct()
    {
        parent::__construct();

        $this->needs_encode = true;
        if (function_exists('mb_convert_encoding')) {
            $mb_overload = ini_get('mbstring.func_overload');
            if (('mail' == $this->Mailer) && ((int)$mb_overload & 1)) { //check if mbstring extension overloads mail()
                $this->needs_encode = false;
            }
        }
    }

    public function addrFormat($addr)
    {
        if (empty($addr[1])) {
            $formatted = $this->secureHeader($addr[0]);
        } else {
            $formatted = $this->EncodeHeader($this->secureHeader($addr[1]), 'text') . ' <' . $this->secureHeader(
                $addr[0]
            ) . '>';
        }
        return $formatted;
    }

    public function encodeHeader($str, $position = 'text', $force=false)
    {
        if (version_compare(PHP_VERSION, '4.4.1')>0) {
            if (function_exists('mb_convert_encoding')) { //Use mb_string extension if exists.
                if ($this->needs_encode || $force) {
                    $enc = mb_internal_encoding();
                    mb_internal_encoding('ISO-2022-JP');
                    $eol = 'mail' == $this->Mailer ?(defined('XCUBE_MAILERLOCAL_MAIL_LE')?XCUBE_MAILERLOCAL_MAIL_LE:"\r\n"):"\n";    // XXX: this for bugs in PHP mail() subject with linefeed handling
                    $encoded = mb_encode_mimeheader($str, 'ISO-2022-JP', 'B', $eol, 9); // offset strlen("Subject: ") as 9
                    mb_internal_encoding($enc);
                } else {
                    $encoded = $str;
                }
            } else {
                $encoded = parent::EncodeHeader($str, $position);
            }
            return $encoded;
        } else {
            //Following Logic are made for recovering PHP4.4.0 and 4.4.1 mb_encode_mimeheader() bug.
            //TODO: If mb_encode_mimeheader() bug is fixed. Replace this to simple logic.
            $encode_charset = strtoupper($this->CharSet);
            if (function_exists('mb_convert_encoding')) { //Using mb_string extension if exists.
                if ($this->needs_encode || $force) {
                    $str_encoding = mb_detect_encoding($str, 'ASCII,'.$encode_charset);
                    if ('ASCII' == $str_encoding) { // Return original if string from only ASCII chars.
                        return $str;
                    } elseif ($str_encoding != $encode_charset) { // Maybe this case may not occur.
                        $str = mb_convert_encoding($str, $encode_charset, $str_encoding);
                    }
                    $cut_start = 0;
                    $encoded ='';
                    $cut_length = floor((76-strlen('Subject: =?'.$encode_charset.'?B?'.'?='))/4)*3;
                    while ($cut_start < strlen($str)) {
                        $partstr = mb_strcut($str, $cut_start, $cut_length, $encode_charset);
                        $partstr_length = strlen($partstr);
                        if (!$partstr_length) {
                            break;
                        }
                        if ('ISO-2022-JP' == $encode_charset) {
                            //Should Adjust next cutting place for SO & SI char insertion.
                            if ((substr($partstr, 0, 3)===chr(27).'$B')
                              && (substr($str, $cut_start, 3) !== chr(27).'$B')) {
                                $partstr_length -= 3;
                            }
                            if ((substr($partstr, -3)===chr(27).'(B')
                              && (substr($str, $cut_start+$partstr_length-3, 3) !== chr(27).'(B')) {
                                $partstr_length -= 3;
                            }
                        }
                        if ($cut_start) {
                            $encoded .= "\r\n\t";
                        }
                        $encoded .= '=?' . $encode_charset . '?B?' . base64_encode($partstr) . '?=';
                        $cut_start += $partstr_length;
                    }
                } else {
                    $encoded = $str;
                }
            } else {
                $encoded = parent::EncodeHeader($str, $position);
            }
            return $encoded;
        }
    }
}
