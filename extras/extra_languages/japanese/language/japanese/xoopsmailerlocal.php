<?php
// $Id: xoopsmailerlocal.php,v 1.4 2008/07/05 07:45:33 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: NobuNobu (Nobuki@Kowa.ORG)                                        //
// URL:  http://jp.xoops.org                                                 //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

if (!defined('XOOPS_ROOT_PATH')) exit();

class XoopsMailerLocal extends XoopsMailer {

    function XoopsMailerLocal(){
        $this->multimailer = new XoopsMultiMailerLocal();
        $this->reset();
        $this->charSet = 'iso-2022-jp';
        $this->encoding = '7bit';
				$this->multimailer->CharSet = $this->charSet;
				$this->multimailer->SetLanguage('ja', XOOPS_ROOT_PATH . '/class/mail/phpmailer/language/');
				$this->multimailer->Encoding = "7bit";
    }

    function encodeFromName($text){
        return $this->STRtoJIS($text,_CHARSET);
    }

    function encodeSubject($text){
        if ($this->multimailer->needs_encode) {
            return $this->STRtoJIS($text,_CHARSET);
        } else {
            return $text;
        }
    }

    function encodeBody(&$text){
        if ($this->multimailer->needs_encode) {
            $text = $this->STRtoJIS($text,_CHARSET);
        }
    }

    /*-------------------------------------
     PHP FORM MAIL 1.01 by TOMO
     URL : http://www.spencernetwork.org/
     E-Mail : groove@spencernetwork.org
    --------------------------------------*/
    function STRtoJIS($str, $from_charset){
        if (function_exists('mb_convert_encoding')) { //Use mb_string extension if exists.
            $str_JIS  = mb_convert_encoding(mb_convert_kana($str,"KV", $from_charset), "JIS", $from_charset);
        } else if ($from_charset=='EUC-JP') {
            $str_JIS = '';
            $mode = 0;
            $b = unpack("C*", $str);
            $n = count($b);
            for ($i = 1; $i <= $n; $i++) {
                if ($b[$i] == 0x8E) {
                    if ($mode != 2) {
                        $mode = 2;
                        $str_JIS .= pack("CCC", 0x1B, 0x28, 0x49);
                    }
                    $b[$i+1] -= 0x80;
                    $str_JIS .= pack("C", $b[$i+1]);
                    $i++;
                } elseif ($b[$i] > 0x8E) {
                    if ($mode != 1){
                        $mode = 1;
                        $str_JIS .= pack("CCC", 0x1B, 0x24, 0x42);
                    }
                    $b[$i] -= 0x80; $b[$i+1] -= 0x80;
                    $str_JIS .= pack("CC", $b[$i], $b[$i+1]);
                    $i++;
                } else {
                    if ($mode != 0) {
                        $mode = 0;
                        $str_JIS .= pack("CCC", 0x1B, 0x28, 0x42);
                    }
                    $str_JIS .= pack("C", $b[$i]);
                }
            }
            if ($mode != 0) $str_JIS .= pack("CCC", 0x1B, 0x28, 0x42);
        }
        return $str_JIS;
    }
}

class XoopsMultiMailerLocal extends XoopsMultiMailer {

    var $needs_encode;

    function XoopsMultiMailerLocal() {
        $this->XoopsMultiMailer();

        $this->needs_encode = true;
        if (function_exists('mb_convert_encoding')) {
            $mb_overload = ini_get('mbstring.func_overload');
            if (($this->Mailer == 'mail') && (intval($mb_overload) & 1)) { //check if mbstring extension overloads mail()
                $this->needs_encode = false;
                $this->mail_overload = true;
            }
        }
    }

    function AddrFormat($addr) {
        if(empty($addr[1])) {
            $formatted = $addr[0];
        } else {
            $formatted = $this->EncodeHeader($addr[1], 'text') . " <" . 
                         $addr[0] . ">";
        }
        return $formatted;
    }

    function EncodeHeader ($str, $position = 'text', $force=false) {
        if (version_compare(PHP_VERSION, '4.4.1')>0) {
            if (function_exists('mb_convert_encoding')) { //Use mb_string extension if exists.
                if ($this->needs_encode || $force) {
                    $enc = mb_internal_encoding();
                    mb_internal_encoding('ISO-2022-JP');
                    $encoded = mb_encode_mimeheader($str, 'ISO-2022-JP', 'B', "\r\n", 9); // offset strlen("Subject: ") as 9
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
                	$str_encoding = mb_detect_encoding($str, 'ASCII,'.$encode_charset );
                    if ($str_encoding == 'ASCII') { // Return original if string from only ASCII chars.
                        return $str;
                    } else if ($str_encoding != $encode_charset) { // Maybe this case may not occur.
                        $str = mb_convert_encoding($str, $encode_charset, $str_encoding);
                    }
                    $cut_start = 0;
                    $encoded ='';
                    $cut_length = floor((76-strlen('Subject: =?'.$encode_charset.'?B?'.'?='))/4)*3;
                    while($cut_start < strlen($str)) {
                        $partstr = mb_strcut ( $str, $cut_start, $cut_length, $encode_charset);
                        $partstr_length = strlen($partstr);
                        if (!$partstr_length) break;
                        if ($encode_charset == 'ISO-2022-JP') { 
                            //Should Adjust next cutting place for SO & SI char insertion.
                            if ((substr($partstr, 0, 3)===chr(27).'$B') 
                              && (substr($str, $cut_start, 3) !== chr(27).'$B')) {
                                $partstr_length -= 3;
                            }
                            if ((substr($partstr,-3)===chr(27).'(B') 
                              && (substr($str, $cut_start+$partstr_length-3, 3) !== chr(27).'(B')) {
                                $partstr_length -= 3;
                            }
                        }
                        if ($cut_start) $encoded .= "\r\n\t";
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
?>
