<?php

/**
 * Unicode Normalizer
 *
 * "...Unicode's normalization is the concept of character composition and decomposition.
 * Character composition is the process of combining simpler characters into
 * fewer precomposed characters, such as the n character and the combining ~ character
 * into the single n+~ character. Decomposition is the opposite process,
 * breaking precomposed characters back into their component pieces...
 * ...Normalization is important when comparing text strings for searching and
 * sorting (collation)..." [Wikipedia]
 *
 * Performs the 4 normalizations:
 * NFD:  Canonical Decomposition
 * NFC:  Canonical Decomposition, followed by Canonical Composition
 * NFKD: Compatibility Decomposition
 * NFKC: Compatibility Decomposition, followed by Canonical Composition
 * Complies with the official Unicode.org regression test.
 * Uses UTF8 binary strings natively but can normalize a string in any UTF format.
 * Fully tested with phpUnit. Code coverage test close to 100%.
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
 * @version   SVN: $Id: UnicodeNormalizer.php 39 2007-07-25 12:33:15Z mcorne $
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 */

require_once 'UnicodeNormalizer/String.php';

/**
 * Unicode Normalizer
 *
 * Performs the 4 normalizations: NFD, NFC, NFKD, NFKC.
 *
 * <pre>
 * Example 1: NFC-normalization of UTF-8 string 'foo'
 * $normalized = I18N_UnicodeNormalizer::toNFC('foo');
 * or
 * $normalizer = new I18N_UnicodeNormalizer();
 * $normalized = $normalizer->normalize('foo', 'NFC')
 *
 * Example 2: NFC-normalization of ISO-8859-1 string 'foo'
 * $normalized = I18N_UnicodeNormalizer::toNFC('foo', 'ISO-8859-1');
 * or
 * $normalizer = new I18N_UnicodeNormalizer();
 * $normalized = $normalizer->normalize('foo', 'NFC', 'ISO-8859-1')
 * </pre>
 *
 * @category  Internationalization
 * @package   I18N_UnicodeNormalizer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 * @link      http://www.unicode.org/unicode/reports/tr15 : Unicode Normalization Forms
 * @link      http://en.wikipedia.org/wiki/Unicode_normalization : Unicode Normalization Definition
 */
class I18N_UnicodeNormalizer
{
    /**
     * The canonical combining classes
     *
     * @var array
     */
    private static $combining = array();

    /**
     * The compiled file names
     *
     * @var array
     */
    private $compiled = array(// /
        'canonical_decomp' => 'CanonicalDecompositions.php',
        'canonical_decomp_x' => 'CanonicalDecompositionsX.php',
        'combining' => 'CanonicalCombining.php',
        'compat_decomp' => 'CompatibilityDecompositions.php',
        'compat_decomp_x' => 'CompatibilityDecompositionsX.php',
        'compositions' => 'Compositions.php',
        'corrections' => 'NormalizationCorrections.php',
        'exclusions' => 'CompositionExclusions.php',
        'hangul_compos' => 'HangulCompositions.php',
        'hangul_decomp' => 'HangulDecompositions.php',
        'quick_check_nfc' => 'QuickCheckNFC.php',
        'quick_check_nfd' => 'QuickCheckNFD.php',
        'quick_check_nfkc' => 'QuickCheckNFKC.php',
        'quick_check_nfkd' => 'QuickCheckNFKD.php',
        'test_base' => 'BaseTests.php',
        'test_hangul' => 'HangulTests.php',
        );

    /**
     * The character compositions
     *
     * @var    array
     * @access private
     * @static
     */
    private static $compositions = array();

    /**
     * The data/compiled files directory
     *
     * @var    string
     * @access private
     * @static
     */
    private static $dataDir = '';

    /**
     * The character decomposition mappings
     *
     * @var    array
     * @access private
     * @static
     */
    private static $decomp = array();

    /**
     * The decomposition types
     *
     * @var    array
     * @access private
     */
    private $decompTypes = array(// /
        'NFD' => 'canonical_decomp_x',
        'NFKD' => 'compat_decomp_x',
        'NFC' => 'canonical_decomp_x', // used by getCharInfo() only
        'NFKC' => 'compat_decomp_x', // used by getCharInfo() only
        );

    /**
     * The character normalization quick checks
     *
     * Characters listed in a given quick check array do not pass the the quick check,
     * and can possibly be normalized.
     * There are 4 quick check sub-arrays, one for each normalization type,
     * e.g. $quickCheck['NFC'] for the NFC normalisation.
     *
     * @var    array
     * @access private
     * @static
     */
    private static $quickCheck = array();

    /**
     * The quick check file nickname for each normalization
     *
     * @var    array
     * @access private
     * @see    self::$compiled
     */
    private $quickCheckTypes = array(// /
        'NFC' => 'quick_check_nfc',
        'NFD' => 'quick_check_nfd',
        'NFKC' => 'quick_check_nfkc',
        'NFKD' => 'quick_check_nfkd',
        );

    /**
     * The I18N_UnicodeNormalizer_String class instance
     *
     * @var    object
     * @access private
     */
    private $string;

    /**
     * The class constructor
     *
     * Sets the paths to the data/compiled files.
     *
     * @param  string $dir the data/compiled files base directory,
     *                     this is only to be used if it cannot be determined
     *                     automaticly, or by the package maintainers for testing purposes
     * @return void
     * @access public
     */
    public function __construct($dir = '')
    {
        // gets the data/compiled file base paths, prepends the path to the file names
        $dir or $dir = self::getDataDir();
        $dir .= '/utf8/';
        $compiled = substr_replace($this->compiled, $dir, 0, 0);
        $this->compiled = array_combine(array_keys($this->compiled), $compiled);

        $this->string = new I18N_UnicodeNormalizer_String();
    }

    /**
     * Gets some information for a set of characters
     *
     * Finds if the characters pass the quick check. Finds their combining classes,
     * their compositions and their decomposition mappings.
     * Mainly used for debugging/testing purposes.
     *
     * @param  mixed  $chars the UTF-8 characters to get the information for, either
     *                       as a string or an array of characters
     * @param  string $type  the type of normalization: 'NFC', 'NFD', 'NFKC' or 'NFKD'
     * @return array  the information, up to 4 sub-arrays, with the characters
     *                as keys and the corresponding quick check value, or
     *                combining class, or compositions, or decomposition mappingsm
     *                converted to the UCN format.
     * @access public
     */
    public function getCharInfo($chars, $type)
    {
        if (!$this->isValidType($type)) {
            // an invalid normalization type
            return null;
        }
        // loads the quick check file, the canonical combining classes,
        // the characters compositions,  and the decomposition mappings
        isset(self::$quickCheck[$type]) or
        self::$quickCheck[$type] = require($this->compiled[$this->quickCheckTypes[$type]]);
        self::$combining or self::$combining = require($this->compiled['combining']);
        self::$compositions or self::$compositions = require($this->compiled['compositions']);
        isset(self::$decomp[$type]) or
        self::$decomp[$type] = require($this->compiled[$this->decompTypes[$type]]);
        // splits the character string
        is_array($chars) or $chars = $this->string->split($chars);

        $combinations = array();
        foreach($chars as $idx => $char) {
            // creates 1, 2, and 3 character combinations
            $key = $char;
            $combinations[$key] = true;
            isset($chars[$idx + 1]) and $key .= $chars[$idx + 1] and $combinations[$key] = true and
            isset($chars[$idx + 2]) and $key .= $chars[$idx + 2] and $combinations[$key] = true;
        }

        $chars = array_flip($chars);

        $found = array();
        if ($intersect = array_intersect_key(self::$quickCheck[$type], $chars)) {
            // finds the characters present in the quick check data
            // converts the characters to the UCN format
            $keys = array_map(array($this->string, 'string2unicode'), array_keys($intersect));
            $values = array_values($intersect);
            $found['quick_check'] = array_combine($keys, $values);
        }

        if ($intersect = array_intersect_key(self::$combining, $chars)) {
            // finds the characters present in the combining data
            // converts the characters to the UCN format
            $keys = array_map(array($this->string, 'string2unicode'), array_keys($intersect));
            $values = array_values($intersect);
            $found['combining'] = array_combine($keys, $values);
        }

        if ($intersect = array_intersect_key(self::$decomp[$type], $chars)) {
            // finds the characters present in the decomposition data
            // converts the characters and the value to the UCN format
            $keys = array_map(array($this->string, 'string2unicode'), array_keys($intersect));
            $values = array_values($intersect);
            $values = array_map(array($this->string, 'string2unicode'), $values);
            $found['decompositions'] = array_combine($keys, $values);
        }

        if ($intersect = array_intersect_key(self::$compositions, $combinations)) {
            // finds the characters present in, for example, the quick check data
            // converts the characters and the value to the UCN format
            $keys = array_map(array($this->string, 'string2unicode'), array_keys($intersect));
            $values = array_values($intersect);
            $values = array_map(array($this->string, 'string2unicode'), $values);
            $found['compositions'] = array_combine($keys, $values);
        }

        return $found;
    }

    /**
     * Determines the data/compiled files directory
     *
     * In case of a raw install coming for example from the SVN repository,
     * the data/compiled files directory is expected to be in the same directory
     * as this file. In case of a Pear install, the data/compiled files directory
     * is computed by PEAR_Config.
     *
     * @return string the data/compiled files base directory
     * @access public
     * @static
     */
    public static function getDataDir()
    {
        if (empty(self::$dataDir)) {
            // the data directory is unknown
            if (file_exists(dirname(__FILE__) . '/' . 'data')) {
                // assuming a raw install, e.g. coming from a SVN checkout
                self::$dataDir = dirname(__FILE__) . '/data';
            } else if ((@include_once "PEAR/Config.php")) {
                // there is a Pear install on the system, gets the data directory
                self::$dataDir = PEAR_Config::singleton()->get('data_dir');
                // adds the package name to the data directory
                self::$dataDir .= '/' . __CLASS__;
            }
            // else: the install is most likely corrupted, the process will
            // stop at the next require statement of a data/compiled file
        }

        return self::$dataDir;
    }

    /**
     * Gets the name list of the compiled files
     *
     * @param  string $dir the data/compiled files base directory,
     * @return array  the name list of compiled files
     * @access public
     * @static
     */
    public static function getFileNames($dir = '')
    {
        $normalizer = new I18N_UnicodeNormalizer($dir);

        return $normalizer->compiled;
    }

    /**
     * Checks if a character is a starter
     *
     * A starter is a character that passes the quick check and with a
     * combining class equal to 0.
     *
     * @param  string  $char the character
     * @param  string  $type the type of normalization: 'NFC', 'NFD', 'NFKC' or 'NFKD'
     * @return boolean true if a starter, false otherwise
     * @access public
     */
    public function isStarter($char, $type)
    {
        if (!$this->isValidType($type)) {
            // an invalid normalization type
            return null;
        }
        // loads the quick check file, and the the canonical combining classes
        isset(self::$quickCheck[$type]) or
        self::$quickCheck[$type] = require($this->compiled[$this->quickCheckTypes[$type]]);
        self::$combining or self::$combining = require($this->compiled['combining']);

        $isStarter = (!isset(self::$quickCheck[$type][$char]) and
            (!isset(self::$combining[$char]) or !self::$combining[$char]));

        return $isStarter;
    }

    /**
     * Checks if the normalization type is valid: NFC, NFD, NFKC or NKFD
     *
     * @param  string $type the normalization type, e.g. 'NFC'
     * @return array  true if valid, false otherwise
     * @access public
     */
    public function isValidType($type)
    {
        return isset($this->quickCheckTypes[$type]);
    }

    /**
     * Normalizes a string
     *
     * @param  string $string   the string to normalize
     * @param  string $type     the type of normalization: 'NFC', 'NFD', 'NFKC'
     *                          or 'NFKD', 'NFC' is the default
     * @param  string $encoding the string encoding, must be compliant with mb_list_encodings(),
     *                          e.g. 'UFT-16', 'UTF-8' is the defaut
     * @return mixed  the normalized string
     * @access public
     */
    public function normalize($string, $type = '', $encoding = '')
    {
        if (!$type) {
            // no type specified, defaults to canonical composition
            $type = 'NFC';
        } else if (!$this->isValidType($type)) {
            // not a valid type
            return false;
        }

        if (!$encoding) {
            // no encoding specified
            $encoding = 'UTF-8'; // defaults to UTF-8 encoding
        } else if (in_array($encoding, mb_list_encodings())) {
            // checks the encoding is valid, encodes the string to UTF-8
            $string = mb_convert_encoding($string, 'UTF-8', $encoding);
        } else {
            // unknown encoding
            return false;
        }

        if (!preg_match('~[^\x0-\x7F]~', $string)) {
            // an ASCII string (which is already normalized by definition)
            $normalized = $string;
        } else {
            // not an ASCII string
            // captures the function name for recursive calls
            static $self = __FUNCTION__;
            // loads the quick check file, loads the canonical combining classes
            isset(self::$quickCheck[$type]) or
            self::$quickCheck[$type] = require($this->compiled[$this->quickCheckTypes[$type]]);
            self::$combining or self::$combining = require($this->compiled['combining']);

            $toNormalize = '';
            $prevCombClass = 0;
            $starterPos = null;
            $normalized = '';
            $combiningClass = 0;
            $length = strlen($string);

            for($i = 0; $i < $length;) {
                // checks if the first character is ASCII, or gets the next character
                // note: getChar() could be called directly but this increases the performance by 10-20%
                ($char = $string{$i}) < "\x80" and ++$i or
                $char = $this->string->getChar($string, $i, $length);

                if (!isset(self::$quickCheck[$type][$char])) {
                    // the character passes quick check
                    // gets the character combining class
                    $combiningClass = isset(self::$combining[$char])? self::$combining[$char] : 0;

                    if ($combiningClass == 0) {
                        // the character is a starter
                        if ($toNormalize != '') {
                            // resets the normalized string to the first starter
                            $this->resetToStarter($normalized, $toNormalize, $starterPos);

                            if ($type == 'NFC' or $type == 'NFKC') {
                                // a composition normalization
                                // decompose-normalizes the substring, recomposes the substring to normalize
                                $toNormalize = $this->$self($toNormalize, $type == 'NFC'? 'NFD' : 'NFKD');
                                $normalized .= $this->recompose($toNormalize);
                            } else {
                                // a decomposition normalization, resorts the substring to normalize
                                $normalized .= $this->resortDecomp($toNormalize);
                            }
                            // resets the substring to normalize
                            $starterPos = null;
                            $toNormalize = '';
                        }
                        // captures the starter
                        $normalized .= $char;
                    } else if ($toNormalize != '') {
                        // there are already characters to normalize
                        // adds the character to the substring to normalize
                        $toNormalize .= $char;
                    } else if ($prevCombClass <= $combiningClass) {
                        // the previous combining class is lower
                        $starterPos === null and $normalized != '' and
                        // captures the starter/previous character position
                        $starterPos = (mb_strlen($normalized, 'UTF-8') - 1);
                        // adds the normalized character
                        $normalized .= $char;
                    } else {
                        // character is not a starter and is not normalized
                        $starterPos === null and $normalized != '' and
                        // captures starter/previous character position
                        $starterPos = (mb_strlen($normalized, 'UTF-8') - 1);
                        // adds the character to the substring to normalize
                        $toNormalize .= $char;
                    }
                    $prevCombClass = $combiningClass;
                } else {
                    // the character does not pass the quick check
                    if ($starterPos === null and $normalized != '') {
                        // captures the character position
                        $starterPos = (mb_strlen($normalized, 'UTF-8') - 1);
                    }

                    if ($type == 'NFD' or $type == 'NFKD') {
                        // a decomposition normalization
                        isset(self::$decomp[$type]) or isset($this->decompTypes[$type]) and
                        self::$decomp[$type] = require($this->compiled[$this->decompTypes[$type]]);
                        // loads the decomposition mappings, if the character has a decomposition
                        isset(self::$decomp[$type][$char]) and $char = self::$decomp[$type][$char];
                    }
                    // adds the character to the substring to normalize
                    $toNormalize .= $char;
                }
            }

            if ($toNormalize != '') {
                // resets the normalized string to the first starter
                $this->resetToStarter($normalized, $toNormalize, $starterPos);
                if ($type == 'NFC' or $type == 'NFKC') {
                    // a composition normalization
                    // decompose-normalizes the substring, recomposes the substring
                    $toNormalize = $this->$self($toNormalize, $type == 'NFC'? 'NFD' : 'NFKD');

                    $normalized .= $this->recompose($toNormalize);
                } else {
                    // decomposition normalization, resorts the substring to normalize
                    $normalized .= $this->resortDecomp($toNormalize);
                }
            }
        }
        // encodes the string
        $encoding == 'UTF-8' or $normalized = mb_convert_encoding($normalized, $encoding, 'UTF-8');

        return $normalized ;
    }

    /**
     * Recomposes a string
     *
     * Recomposes character sequences into a unique characters.
     *
     * @param  string  $string the string to recompose
     * @return string  the recomposed string
     * @access private
     */
    private function recompose($string)
    {
        // loads the characters compositions
        self::$compositions or self::$compositions = require($this->compiled['compositions']);

        $noneStarters = '';
        $starter = '';
        $recomposed = '';
        $prevCombiningClass = 0;
        $isComposed = false;
        $length = strlen($string);

        for($i = 0; $i < $length;) {
            // checks if the first character is ASCII, or gets the next character
            // note: getChar() could be called directly but this increases the performance by 10-20%
            ($char = $string{$i}) < "\x80" and ++$i or
            $char = $this->string->getChar($string, $i, $length);

            if ($isComposed) {
                // the character is already recomposed into a hangul starter
                $isComposed = false;
            } else if ($combiningClass = isset(self::$combining[$char])? self::$combining[$char] : 0) {
                // the character is not a starter
                if (($prevCombiningClass < $combiningClass or $prevChar == $char) and
                        $starter and isset(self::$compositions[$starter . $char])) {
                    // the character is not blocked from starter, or
                    // a character is not blocked by the same preceeding character, and
                    // starter + character can be composed, composes starter + character
                    $prevChar = $starter = self::$compositions[$starter . $char];
                    $prevCombiningClass = 0;
                } else {
                    // the character cannot be composed with the starter, captures the character
                    $noneStarters .= $char;
                    $prevChar = $char;
                    $prevCombiningClass = $combiningClass;
                }
            } else if (!$noneStarters and $starter) {
                // the character is a starter following a starter
                $nextChar = $this->string->getChar($string, $i, $length, true);
                if ($nextChar != '' and
                    isset(self::$compositions[$starter . $char . $nextChar])) {
                    // there is another character to come, and
                    // starter + current + next characters can be composed into a hangul character
                    // composes the starter + characters
                    $prevChar = $starter = self::$compositions[$starter . $char . $nextChar];
                    $prevCombiningClass = 0;
                    $isComposed = true;
                } else if (isset(self::$compositions[$starter . $char])) {
                    // composes starter + character
                    $prevChar = $starter = self::$compositions[$starter . $char];
                    $prevCombiningClass = 0;
                } else {
                    // the character is a starter that cannot be composed
                    // adds the previous starter, adds none starter characters
                    $recomposed .= $starter;
                    $recomposed .= $noneStarters and $noneStarters = '';
                    // sets the new starter
                    $prevChar = $starter = $char;
                    $prevCombiningClass = $combiningClass;
                }
            } else {
                // the character is a starter that cannot be composed
                // adds the previous starter, adds none starter characters
                $recomposed .= $starter;
                $recomposed .= $noneStarters and $noneStarters = '';
                // sets the new starter
                $prevChar = $starter = $char;
                $prevCombiningClass = $combiningClass;
            }
        }
        // adds the last recomposed substring
        $recomposed .= $starter . $noneStarters;

        return $recomposed;
    }

    /**
     * Resets the normalized string to the last starter
     *
     * @param  string  &$normalized  the normalized (sub)string
     * @param  string  &$toNormalize the (sub)string to normalize
     * @param  integer $starterPos   the last starter position
     * @return void
     * @access private
     */
    private function resetToStarter(&$normalized, &$toNormalize, $starterPos)
    {
        if ($starterPos !== null) {
            // extracts the substring from last starter
            // $fromStarter = mb_substr($normalized, $starterPos, PHP_INT_MAX , 'UTF-8');
			// necessary fix because of PHP Bug #42101: mb_substr error if length = PHP_INT_MAX
			// using the 32-bit max integer value instead
            $fromStarter = mb_substr($normalized, $starterPos, 2147483647 , 'UTF-8');
            // adds the substring to the substring to normalize
            $toNormalize = $fromStarter . $toNormalize;
            // strips the normalized string from the last starter on
            $normalized = mb_substr($normalized, 0, $starterPos, 'UTF-8');
        }
    }

    /**
     * Resorts a decomposed string
     *
     * @param  string  $string the decomposed string
     * @return string  the resorted string
     * @access private
     * @todo   review the current limitation of up to 1000 characters to resort
     *         with the same combining class, this should probably be acceptable though!
     */
    private function resortDecomp($string)
    {
        $resorted = '';
        $order = array();
        $cnt = 0;
        $length = strlen($string);

        for($i = 0; $i < $length;) {
            // checks if the first character is ASCII, or gets the next character
            // note: getChar() could be called directly but this increases the performance by 10-20%
            ($char = $string{$i}) < "\x80" and ++$i or
            $char = $this->string->getChar($string, $i, $length);
            // gets the character combining class
            $combiningClass = isset(self::$combining[$char])? self::$combining[$char] : 0;

            if ($combiningClass) {
                // the character is not a starter
                // adds the character to resort, captures the character combining class
                // and concatenates a counter to differentiate characters with same combining classes
                $noneStarters[] = $char;
                $order[] = $combiningClass * 1000 + $cnt++;
            } else {
                // the character is a starter
                if ($order) {
                    // there are characters between starters
                    // sorts none starter characters on combination classes
                    array_multisort($order, SORT_ASC, SORT_NUMERIC , $noneStarters);
                    // adds none starter characters
                    $resorted .= implode('', $noneStarters);

                    $order = array();
                    $noneStarters = array();
                    $cnt = 0;
                }
                // adds the starter
                $resorted .= $char;
            }
        }

        if ($order) {
            // there are characters between starters
            // sorts none starter characters on combination classes
            array_multisort($order, SORT_ASC, SORT_NUMERIC , $noneStarters);
            // adds none starter characters
            $resorted .= implode('', $noneStarters);
        }

        return $resorted;
    }

    /**
     * NFC-normalizes a string
     *
     * @param  string $string   the string to normalize
     * @param  string $encoding the string encoding, must be compliant with mb_list_encodings(),
     *                          e.g. 'UFT-16', 'UTF-8' is the defaut
     * @return mixed  the normalized string
     * @access public
     * @static
     */
    public static function toNFC($string, $encoding = null)
    {
        $normalizer = new self;

        return $normalizer->normalize($string, 'NFC', $encoding);
    }

    /**
     * NFD-normalizes a string
     *
     * @param  string $string   the string to normalize
     * @param  string $encoding the string encoding, must be compliant with mb_list_encodings(),
     *                          e.g. 'UFT-16', 'UTF-8' is the defaut
     * @return mixed  the normalized string
     * @access public
     * @static
     */
    public static function toNFD($string, $encoding = null)
    {
        $normalizer = new self;

        return $normalizer->normalize($string, 'NFD', $encoding);
    }

    /**
     * NFKC-normalizes a string
     *
     * @param  string $string   the string to normalize
     * @param  string $encoding the string encoding, must be compliant with mb_list_encodings(),
     *                          e.g. 'UFT-16', 'UTF-8' is the defaut
     * @return mixed  the normalized string
     * @access public
     * @static
     */
    public static function toNFKC($string, $encoding = null)
    {
        $normalizer = new self;

        return $normalizer->normalize($string, 'NFKC', $encoding);
    }

    /**
     * NFKD-normalizes a string
     *
     * @param  string $string   the string to normalize
     * @param  string $encoding the string encoding, must be compliant with mb_list_encodings(),
     *                          e.g. 'UFT-16', 'UTF-8' is the defaut
     * @return mixed  the normalized string
     * @access public
     * @static
     */
    public static function toNFKD($string, $encoding = null)
    {
        $normalizer = new self;

        return $normalizer->normalize($string, 'NFKD', $encoding);
    }
}

?>