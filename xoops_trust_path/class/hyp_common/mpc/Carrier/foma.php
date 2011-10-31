<?php
require_once 'common.php';

// {{{ class MPC_FOMA
/**
* FOMA絵文字変換ベースクラス
* 
* @author   ryster <ryster@php-develop.org>
* @license  http://www.opensource.org/licenses/mit-license.php The MIT License
* @link     http://php-develop.org/MobilePictogramConverter/
*/
class MPC_FOMA extends MPC_Common
{
    /**
    * 絵文字抽出正規表現
    * @var string
    */
    var $regex = array(
        'WEB' => '/&#(63\d{3});/ie',
        'IMG' => '/<img *src="{PATH}\/(63\d{3})\.gif" alt="[^"]*?" border="0" width="\d*?" height="\d*?" \/>/ie',
        'MODKTAI' => '/\(\(i:([0-9a-z]{4})\)\)/e',
    );
    
    /**
    * 文字列からi-mode絵文字を検出し、指定されたフォーマットに変換
    * 基本・拡張・隠し絵文字一部対応
    * 
    * @param string  $to     (MPC_TO_FOMA, MPC_TO_EZWEB, MPC_TO_SOFTBANK)
    * @param integer $option (MPC_TO_OPTION_RAW, MPC_TO_OPTION_WEB, MPC_TO_OPTION_IMG)
    * @return string
    */
    function Convert($to, $option = MPC_TO_OPTION_RAW, $toCharset = null)
    {
        $this->setTo($to);
        $this->setOption($option);
        $str         = $this->getString();
        $type        = $this->getStringType();
        $fromCharset = $this->getFromCharset();
        
        // RAWへ変換
        if ($type != MPC_FROM_OPTION_RAW) {
            if ($type === MPC_FROM_OPTION_MODKTAI) {
                $eval  = ($fromCharset !== MPC_FROM_CHARSET_SJIS) ? 'mb_convert_encoding(pack("H*", "$1"), $fromCharset, "SJIS-win")' : 'pack("H*", "$1")';
                $str   = preg_replace($this->getRegex($type), $eval, $str);
            } else {
                if ($type == MPC_FROM_OPTION_WEB) {
                    $eval = ($fromCharset === MPC_FROM_CHARSET_UTF8) ? 'mb_convert_encoding(pack("H*", @$1), "UTF-8", "unicode")' : 'mb_convert_encoding(pack("H*", @$1), "SJIS-win", "unicode")';
                    $str  = preg_replace('/&#x([a-z0-9]{4});/ie', $eval, $str);
                }
                $regex = str_replace('{PATH}', preg_quote(rtrim($this->getFOMAImagePath(), '/'), '/'), $this->getRegex($type));
                $eval  = ($fromCharset !== MPC_FROM_CHARSET_SJIS) ? 'mb_convert_encoding(pack("H*", dechex($1)), $fromCharset, "SJIS-win")' : 'pack("H*", dechex($1))';
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
                $decs = ($fromCharset == MPC_FROM_CHARSET_UTF8) ? unpack('C*', mb_convert_encoding(pack('C*', $result[0], $result[1], $result[2]), 'SJIS-win', 'UTF-8')) : $result;
                $dec  = hexdec($this->decs2hex($decs));
                $this->setPictogram($this->encoder($dec));
            } else {
                $this->setUnPictogram(pack('H*', $this->decs2hex($result)));
            }
        }
        $unpictograms = $this->getUnPictograms();
        $pictograms   = $this->getPictograms();
        $this->ReleaseUnPictograms();
        $this->ReleasePictograms();
        $buf = $unpictograms + $pictograms;
        if (is_array($buf)) {
            ksort($buf);
            return implode('', $buf);
        } else {
            return null;
        }
    }
    
    /**
    * 文字列からi-mode絵文字を除外する
    * 
    * @return string
    */
    function Except()
    {
        $str         = $this->getString();
        $type        = $this->getStringType();
        $fromCharset = $this->getFromCharset();
        
        // RAWへ変換
        if ($type != MPC_FROM_OPTION_RAW) {
            if ($type == MPC_FROM_OPTION_WEB) {
                $str = preg_replace('/&#x([a-z0-9]{4});/ie', '', $str);
            }
            $regex = str_replace('{PATH}', preg_quote(rtrim($this->getFOMAImagePath(), '/'), '/'), $this->getRegex($type));
            return preg_replace($regex, '', $str);
        }
        
        $this->setDS(unpack('C*', $str));
        $c = count($this->decstring);
        for ($this->i = 1;$this->i <= $c;$this->i++) {
            $result = $this->Inspection();
            if (is_null($result)) {
                continue;
            }
            
            // 絵文字変換処理
            if ($this->isPictogram($result) === false) {
                $this->setUnPictogram(pack('H*', $this->decs2hex($result)));
            }
        }
        // ここで文字コードの変換とかやる予定
        $buf = $this->getUnPictograms();
        $this->ReleaseUnPictograms();
        if (is_array($buf)) {
            return implode('', $buf);
        } else {
            return null;
        }
    }
    
    /**
    * 文字列にi-mode絵文字が何個含まれているかチェック
    *
    * @return integer
    */
    function Count()
    {
        $count       = 0;
        $str         = $this->getString();
        $type        = $this->getStringType();
        $fromCharset = $this->getFromCharset();
        
        // RAWへ変換
        if ($type != MPC_FROM_OPTION_RAW) {
            if ($type == MPC_FROM_OPTION_WEB) {
                $count = preg_match_all('/&#x([a-z0-9]{4});/ie', $str, $r);
            }
            $regex = str_replace('{PATH}', preg_quote(rtrim($this->getFOMAImagePath(), '/'), '/'), $this->getRegex($type));
            $count += preg_match_all($regex, $str, $r);
            return $count;
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
                $count++;
            }
        }
        
        return $count;
    }
    
    
    
    /**
    * バイナリがi-mode絵文字かどうか、チェック
    * 
    * @param array $chars
    * @return boolean
    */
    function isPictogram($chars)
    {
        if ($this->getFromCharset() == MPC_FROM_CHARSET_UTF8) {
            if (count($chars) != 3){
                return false;
            }
            list($char1, $char2, $char3) = $chars;
            if ($char1 == 0xEE &&
                    (
                        ($char2 == 0x98 && ($char3 >= 0xBE && $char3 <= 0xBF)) ||
                        ($char2 == 0x99 && ($char3 >= 0x80 && $char3 <= 0xBF)) ||
                        ($char2 == 0x9A && ($char3 >= 0x80 && $char3 <= 0xBA)) ||
                        ($char2 == 0x9B && ($char3 >= 0x8E && $char3 <= 0xBF)) ||
                        ($char2 == 0x9C && ($char3 >= 0x80 && $char3 <= 0xBF)) ||
                        ($char2 == 0x9D && ($char3 >= 0x80 && $char3 <= 0x97))
                    )
            ) {
                $boolean = true;
            } else {
                $boolean = false;
            }
        } else {
            if (count($chars) != 2) {
                return false;
            }
            list($char1, $char2) = $chars;
            if ((($char1 == 0xF8) && ($char2 >= 0x9F) && ($char2 <= 0xFC)) || (($char1 == 0xF9) && (($char2 >= 0x40 && $char2 <= 0x4F) || ($char2 >= 0x50 && $char2 <= 0x7E) || ($char2 >= 0x80 && $char2 <= 0xFC)))) {
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