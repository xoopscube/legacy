<?php
require_once 'common.php';

// {{{ class MPC_EZweb
/**
* EZweb絵文字変換ベースクラス
* 
* @author   ryster <ryster@php-develop.org>
* @license  http://www.opensource.org/licenses/mit-license.php The MIT License
* @link     http://php-develop.org/MobilePictogramConverter/
*/
class MPC_EZweb extends MPC_Common
{
    /**
    * 絵文字抽出正規表現
    * @var string
    */
    var $regex = array(
        'WEB' => '/<img\s+(icon|localsrc)="?([0-9]+)"?\s*>/ie',
        'IMG' => '/(<img src="{PATH}\/(\d{1,3})\.gif" alt="[^"]*?" border="0" \/>)/ie',
        'MODKTAI' => '/\(\(e:([0-9a-z]{4})\)\)/e',
    );
    
    /**
    * 文字列からEZweb絵文字を検出し、指定されたフォーマットに変換
    * 基本・拡張・隠し絵文字対応
    * 
    * @param  string  $to      (MPC_TO_FOMA, MPC_TO_EZWEB, MPC_TO_SOFTBANK)
    * @param  integer $option  (MPC_TO_OPTION_RAW, MPC_TO_OPTION_WEB, MPC_TO_OPTION_IMG)
    * @return string
    */
    function Convert($to, $option = MPC_TO_OPTION_RAW)
    {
        if (isset($toCharset)) {
            $this->setToCharset($toCharset);
        }
        $this->setTo($to);
        $this->setOption($option);
        $str         = $this->getString();
        $type        = $this->getStringType();
        $fromCharset = $this->getFromCharset();
        
        // RAWへ変換
        if ($type != MPC_FROM_OPTION_RAW) {
            if ($type === MPC_FROM_OPTION_MODKTAI) {
                $eval  = ($fromCharset == MPC_FROM_CHARSET_UTF8) ? 'mb_convert_encoding(pack("H*", dechex(hexdec("$1") - 1792)), "UTF-8", "unicode")' : 'pack("H*", "$1")';
                $str   = preg_replace($this->getRegex($type), $eval, $str);
            } else {
                if (empty($this->e2icon_table)) {
                    require 'map/e2icon_table.php';
                }
                $regex = str_replace('{PATH}', preg_quote(rtrim($this->getEZwebImagePath(), '/'), '/'), $this->getRegex($type));
                $eval  = ($fromCharset == MPC_FROM_CHARSET_UTF8) ? 'mb_convert_encoding(pack("H*", dechex(\$this->e2icon_table[$2] - 1792)), "UTF-8", "unicode")' : 'pack("H*", dechex(\$this->e2icon_table[$2]))';
                $str   = preg_replace($regex, $eval, $str);
            }
        }
        
        $this->setDS(unpack('C*', $str));
        $c = count($this->decstring);
        for ($this->i = 1;$this->i <= $c;$this->i++) {
            $result = $this->Inspection();
            if (is_null($result)) {
                continue;
            }
            
            // 絵文字変換処理
            if ($this->isPictogram($result)) {
                $hex = ($fromCharset == MPC_FROM_CHARSET_UTF8) ? strtoupper(dechex(hexdec(bin2hex(mb_convert_encoding(pack('H*', $this->decs2hex($result)), 'unicode', 'utf-8'))) + 1792)) : $this->decs2hex($result);
                if (empty($this->e2icon_table)) {
                    require 'map/e2icon_table.php';
                }
                list($iconno) = array_keys($this->e2icon_table, hexdec($hex));
                $this->setPictogram($this->encoder($iconno));
            } else {
                $this->setUnPictogram(pack('H*', $this->decs2hex($result)));
            }
        }
        // ここで文字コードの変換とかやる予定
        $buf = $this->getUnPictograms() + $this->getPictograms();
        $this->ReleaseUnPictograms();
        $this->ReleasePictograms();
        if (is_array($buf)) {
            ksort($buf);
            return implode('', $buf);
        } else {
            return null;
        }
    }
    
    /**
    * 文字列からEZweb絵文字を除外する
    * 
    * @return string
    */
    function Except()
    {
        $str     = $this->getString();
        $type    = $this->getStringType();
        $charset = $this->getFromCharset();
        
        if ($type != MPC_FROM_OPTION_RAW) {
            if (empty($this->e2icon_table)) {
                require 'map/e2icon_table.php';
            }
            $regex = str_replace('{PATH}', preg_quote(rtrim($this->getEZwebImagePath(), '/'), '/'), $this->getRegex($type));
            return preg_replace($regex, '', $str);
        }
        
        $this->setDS(unpack('C*', $str));
        $c = count($this->decstring);
        for ($this->i = 1;$this->i <= $c;$this->i++) {
            $result = $this->Inspection();
            if (is_null($result)) {
                continue;
            }
            if ($this->isPictogram($result) == false) {
                $this->setUnPictogram(pack('H*', $this->decs2hex($result)));
            }
        }
        $buf = $this->getUnPictograms();
        $this->ReleaseUnPictograms();
        if (is_array($buf)) {
            return implode('', $buf);
        } else {
            return null;
        }
    }
    
    /**
    * 文字列にEZweb絵文字が何個含まれているかチェック
    *
    * @return integer
    */
    function Count()
    {
        $count   = 0;
        $str     = $this->getString();
        $type    = $this->getStringType();
        $charset = $this->getFromCharset();
        
        if ($type != MPC_FROM_OPTION_RAW) {
            if (empty($this->e2icon_table)) {
                require 'map/e2icon_table.php';
            }
            $regex = str_replace('{PATH}', preg_quote(rtrim($this->getEZwebImagePath(), '/'), '/'), $this->getRegex($type));
            return preg_replace($regex, '', $str);
        }
        
        $this->setDS(unpack('C*', $str));
        $c = count($this->decstring);
        for ($this->i = 1;$this->i <= $c;$this->i++) {
            $result = $this->Inspection();
            if (is_null($result)) {
                continue;
            }
            if ($this->isPictogram($result) == true) {
                $count++;
            }
        }
        $this->ReleaseUnPictograms();
        return $count;
    }
    
    /**
    * バイナリがEZweb絵文字かどうか、チェック
    * 
    * @param array $chars
    * @return boolean
    */
    function isPictogram($chars)
    {
        if ($this->getFromCharset() == MPC_FROM_CHARSET_UTF8) {
            list($char1, $char2, $char3) = $chars;
            if (
                ($char1 == 0xEE &&
                    (
                        ($char2 == 0xB1 && ($char3 >= 0x80 && $char3 <= 0xBE)) ||
                        ($char2 == 0xB2 && ($char3 >= 0x80 && $char3 <= 0xBF)) ||
                        ($char2 == 0xB3 && ($char3 >= 0x80 && $char3 <= 0xBC)) ||
                        ($char2 == 0xB5 && ($char3 >= 0x80 && $char3 <= 0xBE)) ||
                        ($char2 == 0xB6 && ($char3 >= 0x80 && $char3 <= 0x8D)) ||
                        ($char2 == 0xBD && ($char3 >= 0x80 && $char3 <= 0xBE)) ||
                        ($char2 == 0xBE && ($char3 >= 0x80 && $char3 <= 0xBF)) ||
                        ($char2 == 0xBF && ($char3 >= 0x80 && $char3 <= 0xBC))
                     )
                ) ||
                ($char1 == 0xEF &&
                    (
                        ($char2 == 0x81 && ($char3 >= 0x80 && $char3 <= 0xBE)) ||
                        ($char2 == 0x82 && ($char3 >= 0x80 && $char3 <= 0xBF)) ||
                        ($char2 == 0x83 && ($char3 >= 0x80 && $char3 <= 0xBC))
                    )
                )
            ) {
                $boolean = true;
            } else {
                $boolean = false;
            }
        } else {
            list($char1, $char2) = $chars;
            if ((($char1 == 0xF3 || $char1 == 0xF6 || $char1 == 0xF7) && (($char2 >= 0x40 && $char2 <= 0x7E) || ($char2 >= 0x80 && $char2 <= 0xFC))) || ($char1 == 0xF4 && (($char2 >= 0x40 && $char2 <= 0x7E) || ($char2 >= 0x80 && $char2 <= 0x8D)))) {
                $boolean = true;
            } else {
                $boolean = false;
            }
        }
        return $boolean;
    }
}
// }}}
?>