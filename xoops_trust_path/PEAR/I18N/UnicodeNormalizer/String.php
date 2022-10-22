<?php
/**
 * Unicode Normalizer
 *
 * PHP version 5
 *
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * + Redistributions of source code must retain the above copyright notice,
 * this list of conditions and the following disclaimer.
 * + Redistributions in binary form must reproduce the above copyright notice,
 * this list of conditions and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
 * + The names of its contributors may not be used to endorse or
 * promote products derived from this software without specific prior written permission.
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Internationalization
 * @package   I18N_UnicodeNormalizer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   SVN: $Id: String.php 39 2007-07-25 12:33:15Z mcorne $
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 */

/**
 * Manipulation of Unicode and UTF-8 strings
 *
 * Converts characters or strings from/to Unicode to/from UTF-8.
 * Splits UTF-8 strings. Extracts a UTF-8 string character.
 *
 * @category  Internationalization
 * @package   I18N_UnicodeNormalizer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 * @link      http://en.wikipedia.org/wiki/Utf8
 */
class I18N_UnicodeNormalizer_String
{
    /**
     * Converts a UTF-8 character into a Unicode code point
     *
     * @param  mixed  $char    the UTF-8 character as a multibyte binary string
     *                         or as an integer
     * @param  string $invalid the substitution ASCII character if the UTF-8
     *                         character is invalid, the default is "?"
     * @return string the Unicode code point in UCN format, e.g. \u00A0,
     *                invalid characters are replaced by the substitution
     *                ASCII character
     * @access public
     */
    public function char2unicode($char, $invalid = '?')
    {
        if (is_string($char)) {
            // the UTF-8 character is packed in a binary string
            switch (strlen($char)) {
                // determines the UTF-8 character byte count
                // unpacks the UTF-8 character into an integer
                case 1: // a 1-byte UTF-8 character,
                    $char = unpack('Cval', $char); //
                    $char = current($char);
                    break;

                case 2: // a 2-byte UTF-8 character
                    $char = unpack('nval', $char);
                    $char = current($char);
                    break;

                case 3: // a 3-byte UTF-8 character
                    $char = unpack('Cval1/nval2', $char);
                    $char = current($char) << 16 | next($char);
                    break;

                case 4: // a 4-byte or more UTF-8 character
                    $char = unpack('Nval', $char);
                    $char = current($char);
                    break;

                default: // error: an empty string or more than 4 bytes
                    return $invalid;
            }
        }

        if ($char >= 0) {
            // the UFT-8 value is positive (as expected)
            if (($char &0xFFFFFF80) == 0) {
                // ASCII character: 0zzz.zzzz  => 0000.0000 0000.0000 0zzz.zzzz
                $code = $char;
            } else if (($char &0xFFFFE0C0) == 0x0000C080) {
                // 110y.yyyy 10zz.zzzz => 0000.0000 0000.0yyy yyzz.zzzz
                $code = $char &0x3F | ($char &0x1F00) >> 2;
            } else if (($char &0xFFF0C0C0) == 0x00E08080) {
                // 1110.xxxx 10yy.yyyy 10zz.zzzz => 0000.0000 xxxx.yyyy yyzz.zzzz
                $code = $char &0x3F | ($char &0x3F00) >> 2 | ($char &0x0F0000) >> 4;
            } else if (($char &0xF8C0C0C0) == 0xF0808080) {
                // 1111.0www 10xx.xxxx 10yy.yyyy 10zz.zzzz => 000w.wwxx xxxx.yyyy yyzz.zzzz
                $code = $char &0x3F | ($char &0x3F00) >> 2 | ($char &0x3F0000) >> 4 |
                ($char &0x0F0000) >> 4 | ($char &0x07000000) >> 6;
            } else {
                // an invalid UTF-8 character
                return $invalid;
            }
        } else {
            // the UTF-8 value is seen as negative instead of unsigned integer,
            // note: leftmost bit must be discarded to enable bitwise operations!
            if (($char &0x78C0C0C0) == 0x70808080) {
                // 1111.0www 10xx.xxxx 10yy.yyyy 10zz.zzzz => 000w.wwxx xxxx.yyyy yyzz.zzzz
                $code = $char &0x3F | ($char &0x3F00) >> 2 | ($char &0x3F0000) >> 4 |
                ($char &0x0F0000) >> 4 | ($char &0x07000000) >> 6;
            } else { // an invalid UTF-8 value
                return $invalid;
            }
        }
        // converts the code point to a UCN formatted hexadecimal string, e.g. \u00A0
        return $this->dec2ucn($code);
    }

    /**
     * Converts an integer to an hexadecimal string in UCN format, e.g. \u00A0 or \U0012abcd
     *
     * @param  integer $int the integer
     * @return string  the UCN string
     * @access public
     */
    public function dec2ucn($int)
    {
        // converts integer to UCN, e.g. \u00A0 or \U0012abcd
        $format = $int <= 0xFFFF? '\u%04X' : '\U%08X';

        return sprintf($format, $int);
    }

    /**
     * Gets the current character from a UTF-8 string
     *
     * Returns a substitution character if the first byte is invalid.
     * Expecting a valid UTF-8 string. Does not check if the bytes following
     * the first one are valid.
     *
     * @param  string  $string    the UTF-8 string
     * @param  integer &$pos      the current byte position within the UTF-8 string,
     *                            the position is updated to the next character on exit
     * @param  integer $length    the length of the UTF-8 string
     * @param  boolean $lookahead update the position to the next UTF-8 character
     *                            if true, leaves it unchanged if true
     * @param  string  $invalid   the ASCII character replacing an invalid byte, e.g. "?",
     *                            invalid bytes are silently ignored if null
     * @return string  the UTF-8 character, or false if there are
     *                 no more characters to get
     * @access public
     */
    public function getChar($string, &$pos, $length, $lookahead = false, $invalid = '?')
    {
        if ($pos >= $length) {
            // no more character to read
            return false;
        }
        // saves the current character position if lookahead
        $lookahead and $copy = $pos;
        // gets the first byte
        $char = $string{$pos++};

        if ($char < "\x80") {
            // a 1-byte character
        } else if ($char < "\xC0") {
            // error: invalid as a first byte
            $char = $invalid;
        } else if ($char < "\xE0") {
            // a 2-byte character
            $char .= $string{$pos++};
        } else if ($char < "\xF0") {
            // a 3-byte character
            $char .= substr($string, $pos, 2);
            $pos += 2;
        } else if ($char < "\xF8") {
            // a 4-byte character
            $char .= substr($string, $pos, 3);
            $pos += 3;
        } else {
            // error: out of range as a first byte
            $char = $invalid;
        }
        // restores the current character position if lookahead
        $lookahead and $pos = $copy;

        return $char;
    }

    /**
     * Splits a UTF-8 string into its characters
     *
     * Expecting a valid UTF-8 string.
     *
     * @param  string $string the UTF-8 string
     * @return array  the UTF-8 string characters
     * @access public
     */
    public function split($string)
    {
        $splitted = array();
        $multibyte = '';

        for($i = 0; $i < strlen($string); $i++) {
            $c = $string{$i};
            $n = ord($c);

            if (($n &0x80) == 0) {
                // a 1-byte UTF-8 character
                $multibyte and $splitted[] = $multibyte and $multibyte = '';
                $splitted[] = $c;
            } else if (($n &0xC0) == 0x80) {
                // a following byte
                $multibyte .= $c;
            } else {
                // the first byte of a muti-byte UTF-8 character
                $multibyte and $splitted[] = $multibyte;
                $multibyte = $c;
            }
        }

        $multibyte and $splitted[] = $multibyte;

        return $splitted;
    }

    /**
     * Converts a UTF-8 string to a Unicode string in UCN format
     *
     * Example: string2unicode('123') returns '\u0031\u0032\u0033'.
     * Expecting a valid UTF-8 string.
     *
     * @param  string  $string   the UTF-8 string
     * @param  boolean $toString returns a string if true, or an array of
     *                           characters if false, the default is true
     * @return mixed   the Unicode string in UCN format
     * @access public
     */
    public function string2unicode($string, $toString = true)
    {
        // splits the string into characters
        is_array($string) or $string = $this->split($string);
        // converts the characters to Unicode code points
        $string = array_map(array($this, 'char2unicode'), $string);
        // puts the code points into a string
        $toString and $string = implode('', $string);

        return $string;
    }

    /**
     * Converts a Unicode code point into a UTF-8 character
     *
     * @param  mixed  $code    the Unicode code point as an hexadecimal string,
     *                         e.g. 000A, or 0x000A, or \u000A,
     *                         or as an integer, e.g. or (int)10
     * @param  string $invalid the substitution ASCII character if the Unicode
     *                         is invalid, default is "?"
     * @return string the UTF-8 character, or the substitution ASCII character
     *                if the Unicode is invalid
     * @access public
     */
    public function unicode2char($code, $invalid = '?')
    {
        // converts the hexadecimal unicode to binary, e.g. 000A, or 0x000A, or \u000A to 10
        is_string($code) and $code = hexdec($code);

        if ($code < 0x80) {
            // ASCII character: 0000.0000 0000.0000 0zzz.zzzz  => 0zzz.zzzz
            $char = pack('C', $code);
        } else if ($code < 0x800) {
            // 0000.0000 0000.0yyy yyzz.zzzz => 110y.yyyy 10zz.zzzz
            $int = 0x0000C080 | 0x3F &$code | (0xC0 &$code) << 2 | (0x0700 &$code) << 2;
            $char = pack('n', $int);
        } else if ($code < 0x10000) {
            // 0000.0000 xxxx.yyyy yyzz.zzzz => 1110.xxxx 10yy.yyyy 10zz.zzzz
            $int = 0x00E08080 | 0x3F &$code | (0xC0 &$code) << 2 | (0x0F00 &$code) << 2 |
            (0xF000 &$code) << 4;
            $char = pack('Cn', ($int &0xFF0000) >> 16, $int &0xFFFF);
        } else if ($code < 0x110000) {
            // 000w.wwxx xxxx.yyyy yyzz.zzzz => 1111.0www 10xx.xxxx 10yy.yyyy 10zz.zzzz
            $int = 0xF0808080 | 0x3F &$code | (0xC0 &$code) << 2 | (0x0F00 &$code) << 2 |
            (0xF000 &$code) << 4 | (0x030000 &$code) << 4 | (0x1C0000 &$code) << 6;
            $char = pack('N', $int);
        } else {
            // error: the Unicode value out of range
            $char = $invalid;
        }

        return $char;
    }

    /**
     * Converts a Unicode string in the UCN format to a UTF-8 string
     *
     * Expecting a valid Unicode string.
     * Any character outside of the [0-9A-Fa-f] range are considered as separators.
     * Example: unicode2string('\u0031\u0032\u0033') returns '123'.
     * Example: unicode2string('31 32x33') returns '123'.
     *
     * @param  string $string the Unicode string in UCN format
     * @return string the UTF-8 string
     * @access public
     */
    public function unicode2string($string)
    {
        // splits the Unicode string into code points
        if ($codes = preg_split('~[^\da-f]~i', $string, -1, PREG_SPLIT_NO_EMPTY)) {
            // converts the code points to UTF-8 characters, puts them into a string
            $char = array_map(array($this, 'unicode2char'), $codes);
            $string = implode('', $char);
        }

        return $string;
    }
}

?>