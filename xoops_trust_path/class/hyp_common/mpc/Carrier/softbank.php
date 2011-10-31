<?php
require_once 'common.php';

// {{{ class MPC_SoftBank
/**
* SoftBankŠG•¶Žš•ÏŠ·ƒx[ƒXƒNƒ‰ƒX
* 
* @author   ryster <ryster@php-develop.org>
* @license  http://www.opensource.org/licenses/mit-license.php The MIT License
* @link     http://php-develop.org/MobilePictogramConverter/
*/
class MPC_SoftBank extends MPC_Common
{
  /**
  * ŠG•¶Žš’Šo³‹K•\Œ»
  * @var string
  */
    var $regex = array(
        'WEB' => '/[\x1B][\x24](([\x47][\x21-\x7A]+)|([\x45][\x21-\x7A]+)|([\x46][\x21-\x7A]+)|([\x4F][\x21-\x6D]+)|([\x50][\x21-\x6C]+)|([\x51][\x21-\x5E]+))[\x0F]?/',
        'IMG' => '/<img src="{PATH}\/((17|18|20)\d{3})\.gif" alt="[^"]*?" border="0" width="\d*?" height="\d*?" \/>/ie',
        'MODKTAI' => '/\(\(s:([0-9a-z]{4})\)\)/e',
    );
    
    /**
    * RAW => WEB •ÏŠ·ƒe[ƒuƒ‹
    * @var array
    */
    var $sr2sw_table = array(
        0x80 => 0x47, 0x81 => 0x47, // Page 1
        0x84 => 0x45, 0x85 => 0x45, // Page 2
        0x88 => 0x46, 0x89 => 0x46, // Page 3
        0x8C => 0x4F, 0x8D => 0x4F, // Page 4
        0x90 => 0x50, 0x91 => 0x50, // Page 5
        0x94 => 0x51                // Page 6
    );
    
    /**
    * •¶Žš—ñ‚©‚çSoftBankŠG•¶Žš‚ðŒŸo‚µAŽw’è‚³‚ê‚½ƒtƒH[ƒ}ƒbƒg‚É•ÏŠ·
    * 
    * @param string  $to     (MPC_TO_FOMA, MPC_TO_EZWEB, MPC_TO_SOFTBANK)
    * @param integer $option (MPC_TO_OPTION_RAW, MPC_TO_OPTION_WEB, MPC_TO_OPTION_IMG)
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
        
        // RAW‚Ö•ÏŠ·
        if ($type === MPC_FROM_OPTION_MODKTAI) {
            $str  = preg_replace($this->getRegex($type), 'pack("H*", "1B24"."$1"."0F")', $str);
        } else {
            if ($type != MPC_FROM_OPTION_RAW && $type != MPC_FROM_OPTION_WEB) {
                $regex = str_replace('{PATH}', preg_quote(rtrim($this->getSoftBankImagePath(), '/'), '/'), $this->getRegex($type));
                $str   = preg_replace($regex, 'pack("H*", "1B24".dechex($1)."0F")', $str);
            }
        }
        
        $this->setDS(unpack('C*', $str));
        $c = count($this->decstring);
        for ($this->i = 1;$this->i <= $c;$this->i++) {
            $result = $this->Inspection();
            if (is_null($result)) {
                continue;
            }
            
            // ŠG•¶Žš•ÏŠ·ˆ—
            if ($this->isPictogram($result)) {
                if ($fromCharset == MPC_FROM_CHARSET_SJIS) {
                    list($char1, $char2, $char3) = $this->sjis2utf8($result[0], $result[1]);
                } else {
                    list($char1, $char2, $char3) = $result;
                }
                
                $num  = ($char2 == 0x80 || $char2 == 0x84 || $char2 == 0x88 || $char2 == 0x8C || $char2 == 0x90 || $char2 == 0x94) ? ($char3 - 0x81) : ($char3 - 0x80 + 63);
                $dec1 = $this->sr2sw_table[$char2];
                $dec2 = $num + 0x21;
                $this->setPictogram($this->encoder(hexdec($this->decs2hex(array($dec1, $dec2)))));
            } else {
                $this->setUnPictogram(pack('H*', $this->decs2hex($result)));
            }
        }
        // ‚±‚±‚Å•¶ŽšƒR[ƒh‚Ì•ÏŠ·‚Æ‚©‚â‚é—\’è
        $buf = $this->getUnPictograms() + $this->getPictograms();
        $this->ReleaseUnPictograms();
        $this->ReleasePictograms();
        if (is_array($buf)) {
            ksort($buf);
            $buf = implode('', $buf);
            preg_match_all($this->getRegex('WEB'), $buf, $matches);
            if (count($matches) > 0) {
                $split = preg_split($this->getRegex('WEB'), $buf);
                $max  = count($split) - 1;
                $buf2 = '';
                foreach ($split as $key => $value) {
                    if ($max != $key) {
                        $emoji = '';
                        $decs = unpack('C*', $matches[1][$key]);
                        $num  = count($decs);
                        for ($i = 2;$i <= $num;$i++) {
                            $dec    = hexdec(dechex($decs[1]).dechex($decs[$i]));
                            $emoji .= $this->encoder($dec);
                        }
                        $buf2 .= $value.$emoji;
                    } else {
                        $buf2 .= $value;
                    }
                }
                return $buf2;
            }
        } else {
            return null;
        }
    }
    
    /**
    * •¶Žš—ñ‚©‚çSoftBankŠG•¶Žš‚ðœŠO‚·‚é
    * 
    * @return string
    */
    function Except()
    {
        $str  = $this->getString();
        $type = $this->getStringType();
        
        // RAW‚Ö•ÏŠ·
        if ($type != MPC_FROM_OPTION_RAW && $type != MPC_FROM_OPTION_WEB) {
            $regex = str_replace('{PATH}', preg_quote(rtrim($this->getSoftBankImagePath(), '/'), '/'), $this->getRegex($type));
            $str   = preg_replace($regex, 'pack("H*", "1B24".dechex($1)."0F")', $str);
        }
        
        $this->setDS(unpack('C*', $str));
        $c = count($this->decstring);
        for ($this->i = 1;$this->i <= $c;$this->i++) {
            $result = $this->Inspection();
            if (is_null($result)) {
                continue;
            }
            
            // ŠG•¶Žš•ÏŠ·ˆ—
            if ($this->isPictogram($result) === false) {
                $this->setUnPictogram(pack('H*', $this->decs2hex($result)));
            }
        }
        // ‚±‚±‚Å•¶ŽšƒR[ƒh‚Ì•ÏŠ·‚Æ‚©‚â‚é—\’è
        $buf = $this->getUnPictograms();
        $this->ReleaseUnPictograms();
        if (is_array($buf)) {
            $buf = implode('', $buf);
            return preg_replace($this->getRegex('WEB'), '', $buf);
        }
    }
    
    /**
    * •¶Žš—ñ‚ÉSoftBankŠG•¶Žš‚ª‰½ŒÂŠÜ‚Ü‚ê‚Ä‚¢‚é‚©ƒ`ƒFƒbƒN
    *
    * @return integer
    */
    function Count()
    {
        $count = 0;
        $str   = $this->getString();
        $type  = $this->getStringType();
        
        // RAW‚Ö•ÏŠ·
        if ($type != MPC_FROM_OPTION_RAW && $type != MPC_FROM_OPTION_WEB) {
            $regex = str_replace('{PATH}', preg_quote(rtrim($this->getSoftBankImagePath(), '/'), '/'), $this->getRegex($type));
            return preg_match_all($regex, 'pack("H*", "1B24".dechex($1)."0F")', $str);
        }
        
        $this->setDS(unpack('C*', $str));
        $c = count($this->decstring);
        for ($this->i = 1;$this->i <= $c;$this->i++) {
            $result = $this->Inspection();
            if (is_null($result)) {
                continue;
            }
            
            // ŠG•¶Žš•ÏŠ·ˆ—
            if ($this->isPictogram($result)) {
                $count++;
            }
        }
        // ‚±‚±‚Å•¶ŽšƒR[ƒh‚Ì•ÏŠ·‚Æ‚©‚â‚é—\’è
        $buf = $this->getUnPictograms() + $this->getPictograms();
        $this->ReleaseUnPictograms();
        $this->ReleasePictograms();
        if (is_array($buf)) {
            ksort($buf);
            $buf = implode('', $buf);
            $count += preg_match_all($this->getRegex('WEB'), $buf, $matches);
        }
        return $count;
    }
    
    /**
    * ƒoƒCƒiƒŠ‚ªSoftBankŠG•¶Žš‚©‚Ç‚¤‚©Aƒ`ƒFƒbƒN
    * 
    * @param  array $chars
    * @return boolean
    */
    function isPictogram($chars)
    {
        if ($this->getFromCharset() == 'UTF-8') {
            list($char1, $char2, $char3) = $chars;
            
            if (($char1 == 0xEE && 
                //ŠG•¶Žš1iŠî–{j
                (($char2 == 0x80 && ($char3 >= 0x81 && $char3 <= 0xBF)) ||
                 ($char2 == 0x81 && ($char3 >= 0x80 && $char3 <= 0x9A)) ||
                 //ŠG•¶Žš2iŠî–{j
                 ($char2 == 0x84 && ($char3 >= 0x81 && $char3 <= 0xBF)) ||
                 ($char2 == 0x85 && ($char3 >= 0x80 && $char3 <= 0x9A)) ||
                 //ŠG•¶Žš3iŠî–{j
                 ($char2 == 0x88 && ($char3 >= 0x81 && $char3 <= 0xBF)) ||
                 ($char2 == 0x89 && ($char3 >= 0x80 && $char3 <= 0x93)) ||
                 //ŠG•¶Žš4iŠg’£j
                 ($char2 == 0x8C && ($char3 >= 0x81 && $char3 <= 0xBF)) ||
                 ($char2 == 0x8D && ($char3 >= 0x80 && $char3 <= 0x8D)) ||
                 //ŠG•¶Žš5iŠg’£j
                 ($char2 == 0x90 && ($char3 >= 0x81 && $char3 <= 0xBF)) ||
                 ($char2 == 0x91 && ($char3 >= 0x80 && $char3 <= 0x8C)) ||
                 //ŠG•¶Žš6iŠg’£j
                 ($char2 == 0x94 && ($char3 >= 0x81 && $char3 <= 0xBE)))))
            {
                $boolean = true;
            } else {
                $boolean = false;
            }
        } else {
            list($char1, $char2) = $chars;
            if (
                //ŠG•¶Žš1(Šî–{)
                ($char1 == 0xF9 && (($char2 >= 0x41 && $char2 <= 0x7E) || ($char2 >= 0x80 && $char2 <= 0x9B))) ||
                //ŠG•¶Žš2iŠî–{j
                ($char1 == 0xF7 && (($char2 >= 0x41 && $char2 <= 0x7E) || ($char2 >= 0x80 && $char2 <= 0x9B))) ||
                //ŠG•¶Žš3(Šî–{)
                ($char1 == 0xF7 && ($char2 >= 0xA1 && $char2 <= 0xF3)) ||
                //ŠG•¶Žš4(Šg’£)
                ($char1 == 0xF9 && ($char2 >= 0xA1 && $char2 <= 0xED)) ||
                //ŠG•¶Žš5(Šg’£)
                ($char1 == 0xFB && (($char2 >= 0x41 && $char2 <= 0x7E) || ($char2 >= 0x80 && $char2 <= 0x8D))) ||
                //ŠG•¶Žš6(Šg’£)
                ($char1 == 0xFB && ($char2 >= 0xA1 && $char2 <= 0xD7))
            ) {
                $boolean = true;
            } else {
                $boolean = false;
            }
        }
        return $boolean;
    }
    
    /**
    * SJIS(dec)‚©‚çUTF-8(dec)‚Ö•ÏŠ·
    *
    * @access private
    * @param  integer $char1
    * @param  integer $char2
    * @return array
    */
    function sjis2utf8($char1, $char2)
    {
        if ($char1 == 0xF9 && ($char2 >= 0x41 && $char2 <= 0x7E)) {
            $diff = 6464;
        } elseif ($char1 == 0xF9 && ($char2 >= 0x80 && $char2 <= 0x9B)) {
            $diff = 6465;
        } elseif ($char1 == 0xF7 && ($char2 >= 0x41 && $char2 <= 0x7E)) {
            $diff = 5696;
        } elseif ($char1 == 0xF7 && ($char2 >= 0x80 && $char2 <= 0x9B)) {
            $diff = 5697;
        } elseif ($char1 == 0xF7 && ($char2 >= 0xA1 && $char2 <= 0xF3)) {
            $diff = 5536;
        } elseif ($char1 == 0xF9 && ($char2 >= 0xA1 && $char2 <= 0xED)) {
            $diff = 5792;
        } elseif ($char1 == 0xFB && ($char2 >= 0x41 && $char2 <= 0x7E)) {
            $diff = 5952;
        } elseif ($char1 == 0xFB && ($char2 >= 0x80 && $char2 <= 0x8D)) {
            $diff = 5953;
        } else {
            $diff = 5792;
        }
        
        $decs = unpack('C*', mb_convert_encoding(pack('H*', dechex((hexdec($this->decs2hex(array($char1, $char2))) - $diff))), 'UTF-8', 'unicode'));
        return array($decs[1], $decs[2], $decs[3]);
    }
}
// }}}
?>