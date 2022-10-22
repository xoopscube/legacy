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
 * @version   SVN: $Id: Compiler.php 38 2007-07-23 11:42:30Z mcorne $
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 */

require_once 'I18N/UnicodeNormalizer.php';

/**
 * Description for require_once
 */
require_once 'I18N/UnicodeNormalizer/File.php';

/**
 * Unicode data compiler
 *
 * Compiles the data files to files with Unicode code points
 * converted to UTF-8 characters, e.g. utf8/CanonicalCombining.php.
 * Compiles the data files to files with the Unicode code points
 * converted to their UCN format, e.g. ucn/CanonicalCombining.php. The latter
 * is useful for testing purposes.
 *
 * The original Unicode.org data files are split into quick check files,
 * a character combining file, decomposition files, a composition exclusion
 * file, character composition files, test files. Each file contains a PHP array
 * with the characters as key.
 *
 * The Hangul data is generated with an algorithm.
 *
 * The compilation only needs to be run once.
 *
 * @category  Internationalization
 * @package   I18N_UnicodeNormalizer
 * @author    Michel Corne <mcorne@yahoo.com>
 * @copyright 2007 Michel Corne
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/I18N_UnicodeNormalizer
 * @link      http://www.unicode.org/Public/UNIDATA/
 * @see       I18N_UnicodeNormalizer::$compiled for the list of compiled files.
 */
class I18N_UnicodeNormalizer_Compiler
{
    /**
     * Code point format
     * <pre>
     * 'utf8': Code point conversion from Unicode to UTF-8 characters
     * 'ucn': Code point conversion from Unicode to their UCN format, e.g. \u000A
     * </pre>
     *
     * @var string
     */
    private $codeFormat;

    /**
     * The name list of compiled files
     *
     * @var    array  
     * @access private
     */
    private $compiled;

    /**
     * The Unicode.org data files
     *
     * @var array
     */
    private $data = array(// /
        'corrections' => 'NormalizationCorrections.txt',
        'derived_norm' => 'DerivedNormalizationProps.txt',
        'norm_test' => 'NormalizationTest-3.2.0.txt',
        'unicode_data' => 'UnicodeData.txt',
        );

    /**
     * The I18N_UnicodeNormalizer_File class instance
     *
     * @var    object 
     * @access private
     */
    private $file;

    /**
     * The force-compilation flag
     *
     * Forces files to be (re)compiled if true. Files are (re)compiled if not
     * done yet if false.
     *
     * @var    boolean
     * @access private
     */
    private $forceCompile;

    /**
     * Range limit below which a Unicode code point range is expanded
     *
     * @var    integer
     * @access private
     */
    private $rangeLimit;

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
     * Sets the Unicode code point format, the force-compilation option,
     * the code points range limit. Sets the paths of the data and compiled
     * file names
     *
     * @param  string  $dir            the data/compiled files base directory,
     *                                 the default is set by the normalizer
     * @param  string  $codeFormat     'utf8': for production, or 'ucn': for
     *                                 testing purposes, the default is 'utf8'
     * @param  boolean $forceCompile   compilation option: files are to be
     *                                 (re)compiled if true,
     *                                 files are recompiled as needed if false
     * @param  integer $limitedDataSet compiles a small subset of data
     *                                 for testing/coverage purposes if true,
     *                                 or use all data otherwise
     * @return void   
     * @access public 
     */
    public function __construct($dir = '', $codeFormat = '', $forceCompile = false, $limitedDataSet = false)
    {
        // sets the Unicode code point format, the force compilation flag, the range limit for tests
        $this->codeFormat = $codeFormat == 'ucn'? 'ucn' : 'utf8';
        $this->forceCompile = $forceCompile;
        $this->rangeLimit = $limitedDataSet? 2 : 12000;
        // gets the file names to compile, changes the file names to UCN if requested
        $this->compiled = I18N_UnicodeNormalizer::getFileNames($dir);
        $this->codeFormat == 'ucn' and $this->compiled = str_replace('utf8', 'ucn', $this->compiled);
        // builds the unicodeata file base paths, prepends the path to the file names
        $dir or $dir = I18N_UnicodeNormalizer::getDataDir();
        $dir .= '/unicodedata/';
        $data = substr_replace($this->data, $dir, 0, 0);
        $this->data = array_combine(array_keys($this->data), $data);

        $this->file = new I18N_UnicodeNormalizer_File();
        $this->string = new I18N_UnicodeNormalizer_String();
    }

    /**
     * Compiles the Unicode composition, decomposition and combining text data files into PHP
     *
     * @return void  
     * @access public
     */
    public function compileAll()
    {
        // compiles the quick check data
        $this->compileDerivedNorm('quick_check_nfc', 'NFC_QC');
        $this->compileDerivedNorm('quick_check_nfd', 'NFD_QC');
        $this->compileDerivedNorm('quick_check_nfkc', 'NFKC_QC');
        $this->compileDerivedNorm('quick_check_nfkd', 'NFKD_QC');
        // compiles the exclusions
        $this->compileDerivedNorm('exclusions', 'Full_Composition_Exclusion');
        // generates the Hangul decompositions and compositions
        $this->compileHangulDecomp();
        $this->compileCompositions('hangul_compos', 'hangul_decomp');
        // compiles the corrections
        $this->compileCorrections();
        // compiles the Unicode main data
        $this->compileUnicodeData();
        // generates the compositions
        $this->compileCompositions('compositions', 'canonical_decomp');
        // compiles the base tests, compiles the Hangul tests
        $this->compileTest('test_base', 'HANGUL', false);
        $this->compileTest('test_hangul', 'HANGUL', true);
    }

    /**
     * Generates the compositions
     *
     * @param  string  $composFileName the composition file name
     * @param  string  $decompFileName the decomposition file name
     * @return void   
     * @access private
     */
    private function compileCompositions($composFileName, $decompFileName)
    {
        if ($this->forceCompile or !file_exists($this->compiled[$composFileName])) {
            // the compositions are not or must be compiled
            // loads the canonical decomposition mappings, loads the exclusions
            $decompositions = require($this->compiled[$decompFileName]);
            $exclusions = require($this->compiled['exclusions']);
            // excludes some code points, flips the decompositions to create the compositions
            $decompositions = array_diff_key($decompositions, $exclusions);
            $compositions = array_flip($decompositions);
            // adds the Hangul compositions
            $composFileName != 'hangul_compos' and
            $compositions += require($this->compiled['hangul_compos']);
            // writes the compiled compositions
            $this->file->put($this->compiled[$composFileName], $compositions,
                __CLASS__ . '::' . __FUNCTION__, $this->compiled[$decompFileName]);
        }
    }

    /**
     * Compiles the corrections
     *
     * @return void   
     * @access private
     */
    private function compileCorrections()
    {
        if ($this->forceCompile or !file_exists($this->compiled['corrections'])) {
            // the corrections are not or must be compiled
            // parses the corrections, extracts the code point decomposition corrections
            $corrections = $this->parseFile($this->data['corrections']);
            array_walk($corrections, create_function('&$value', '$value = $value[2];'));
            // writes the compiled corrections
            $this->file->put($this->compiled['corrections'], $corrections,
                __CLASS__ . '::' . __FUNCTION__, $this->data['corrections']);
        }
    }

    /**
     * Compiles and expands the decomposition mappings including Hangul
     *
     * @param  array   $decomposition the decomposition mappings
     * @param  string  $fileName      the decomposition file name
     * @param  string  $fileNameX     the expanded decomposition file name
     * @return array   the expanded decomposition mappings
     * @access private
     */
    private function compileDecomp($decomposition, $fileName, $fileNameX)
    {
        // writes the compiled decomposition mappings
        $this->file->put($this->compiled[$fileName], $this->implode($decomposition),
            __CLASS__ . '::' . __FUNCTION__, $this->data['unicode_data']);

        foreach(array_keys($decomposition) as $code) {
            // expands the code point decomposition
            $this->expandDecomp($code, $decomposition);
        }
        // adds the hangul decompostions mappings
        $hangulDecomp = require($this->compiled['hangul_decomp']);
        // writes the compiled expanded decompositions mappings
        $this->file->put($this->compiled[$fileNameX], $this->implode($decomposition) + $hangulDecomp,
            __CLASS__ . '::' . __FUNCTION__, $this->compiled[$fileName]);

        return $decomposition;
    }

    /**
     * Compiles the derived normalization data to generates the quick-checks or the exclusions
     *
     * Extracts the data lines that include a specific keyword. Compiles that data.
     *
     * @param  string  $compiledName the name of the compiled file
     * @param  string  $keyword      the keyword within the data lines to extract,
     *                               e.g. 'NFC_QC', 'NFD_QC', 'NFKC_QC', 'NFKD_QC',
     *                               'Full_Composition_Exclusion'
     * @return void   
     * @access private
     */
    private function compileDerivedNorm($compiledName, $keyword)
    {
        if ($this->forceCompile or !file_exists($this->compiled[$compiledName])) {
            // the file is not or must be compiled
            // parses/filters the derived normalizations, sets the property value
            // to true if present, note: treating both quick check M[aybe] as N[o] as No
            $parsed = $this->parseFile($this->data['derived_norm'], $keyword);
            array_walk($parsed, create_function('&$value', '$value = true;'));
            // writes the compiled file
            $this->file->put($this->compiled[$compiledName], $parsed,
                __CLASS__ . '::' . __FUNCTION__, $this->data['derived_norm']);
        }
    }

    /**
     * Creates the Hangul decomposition mappings
     *
     * @return void   
     * @access private
     * @link   http://www.unicode.org/unicode/reports/tr15/#Hangul
     */
    private function compileHangulDecomp()
    {
        if ($this->forceCompile or !file_exists($this->compiled['hangul_decomp'])) {
            // the hangul decomposition mappings are not or must be compiled
            $sbase = 0xAC00;
            $lbase = 0x1100;
            $vbase = 0x1161;
            $tbase = 0x11a7;
            $lcount = 19;
            $vcount = 21;
            $tcount = 28;
            $ncount = $vcount * $tcount;
            $scount = $lcount * $ncount;
            // limits the code points range
            $scount = min($scount, $this->rangeLimit);

            for($sindex = 0; $sindex < $scount; $sindex++) {
                // computes the L, V, and T syllable code point unicode values
                $l = $lbase + $sindex / $ncount;
                $v = $vbase + ($sindex % $ncount) / $tcount;
                $t = $tbase + $sindex % $tcount;
                // converts the L, V, T syllable to UTF-8 or UCN, concatenates L+V+T
                $hangulCode = $this->convertCode($l);
                $hangulCode .= $this->convertCode($v);
                $t != $tbase and $hangulCode .= $this->convertCode($t);
                $code = $sindex + $sbase;
                $decomposition[$this->convertCode($code)] = $hangulCode;
            }
            // writes the compiled hangul decomposition mappings
            $this->file->put($this->compiled['hangul_decomp'], $decomposition,
                __CLASS__ . '::' . __FUNCTION__);
        }
    }

    /**
     * Compiles the Unicode.org regression test file
     *
     * Extracts the tests lines that include (or not if specified) a specific
     * keyword. Compiles those lines.
     *
     * @param  string  $fileName       the compiled file name
     * @param  string  $keyword        the keyword within the test lines to extract,
     *                                 e.g. "HANGUL"
     * @param  boolean $includeKeyword extracts the lines with the keyword if true,
     *                                 or without if false
     * @return void   
     * @access private
     */
    private function compileTest($fileName, $keyword, $includeKeyword = true)
    {
        if ($this->forceCompile or !file_exists($this->compiled[$fileName])) {
            // the test is not or must be compiled
            // loads the corrections, parses the test data
            $corrections = require($this->compiled['corrections']);
            $parsed = $this->parseFile($this->data['norm_test'], $keyword, $includeKeyword, false);

            $normTest = array();
            foreach($parsed as $line => $test) {
                if (count($test) == 6) {
                    // a proper test, note: excluding the section titles,
                    // e.g. @Part 0 # Specific cases, ignore
                    // removes the empty trailing entry
                    unset($test[5]);
                    // extracts the first test entry: a single code point or a code list
                    $code = current($test);
                    // splits and converts the code point
                    $formattedCode = $this->splitCodes($code);

                    if (count($formattedCode) == 1) {
                        // the first test entry is a single code point, not a code list
                        // extracts the formatted code point
                        $formattedCode = current($formattedCode);
                        // the code point has a decomposition correction,
                        // resets the test entries to the code point
                        // + 4 times the same decomposition correction
                        isset($corrections[$formattedCode]) and
                        $test = array($code) + array_fill(1, 4, $corrections[$formattedCode]);
                    }
                    // extracts the test code point list
                    $test = array_map(array($this, 'splitCodes'), $test);
                    // concatenates the code points to create the UTF-8 string
                    $test = $this->implode($test);
                    // reindexes the test according to the data file column numbers
                    $normTest[$line] = array_combine(array(1, 2, 3, 4, 5), $test);
                }
            }
            // writes the compiled normalization test
            $this->file->put($this->compiled[$fileName], $normTest,
                __CLASS__ . '::' . __FUNCTION__, $this->data['norm_test']);
        }
    }

    /**
     * Compiles the combining file and the decomposition files
     *
     * @return void   
     * @access private
     */
    private function compileUnicodeData()
    {
        if ($this->forceCompile or !file_exists($this->compiled['combining']) or
                !file_exists($this->compiled['canonical_decomp']) or
                !file_exists($this->compiled['canonical_decomp_x']) or
                !file_exists($this->compiled['compat_decomp']) or
                !file_exists($this->compiled['compat_decomp_x'])) {
            // the combining and decompositions files are not or must be compiled
            // parses the unicode data, loads the corrections
            $unicodeData = $this->parseFile($this->data['unicode_data']);
            $corrections = require($this->compiled['corrections']);

            foreach($corrections as $code => $correctDecomp) {
                // checks there is a code point for this correction
                isset($unicodeData[$code]) or
                die('Error: missing code point with a correction in ' . $this->data['unicode_data']);
                $unicodeData[$code][5] = $correctDecomp;
            }
            // not expecting ranges for canonical class combining and decomposition mappings
            unset($unicodeData['code_ranges']);

            foreach($unicodeData as $code => $properties) {
                $details = array();
                // captures the canonical combining class, extracts the decompositions details
                $combiningClass = (int)$properties[3] and $combining[$code] = $combiningClass;
                $mapping = $properties[5];

                if ($mapping != '') {
                    if ($mapping{0} != '<') {
                        // a canonical decomposition,  extracts the canonical decomposition
                        $canonicalDecomp[$code] = $this->splitCodes($mapping);
                    } else {
                        // a compatibility decomposition, extracts the compatibility decomposition
                        $mapping = substr($mapping, strpos($mapping, '>') + 1) and
                        $compatDecomp[$code] = $this->splitCodes($mapping);
                    }
                }
            }
            // writes the compiled combining classes
            $this->file->put($this->compiled['combining'], $combining,
                __CLASS__ . '::' . __FUNCTION__, $this->data['unicode_data']);
            // compiles the canonical decomposition
            $canonicalDecomp = $this->compileDecomp($canonicalDecomp, 'canonical_decomp', 'canonical_decomp_x');
            // adds the expanded canonical decompositions to the compatibility decomposition mappings
            $compatDecomp += $canonicalDecomp;
            // compiles the compatibility decompositions mappings
            $this->compileDecomp($compatDecomp, 'compat_decomp', 'compat_decomp_x');
        }
    }

    /**
     * Converts the Unicode code point to a UTF-8 character or its UCN format
     *
     * @param  string $code the Unicode code point
     * @return string the UTF-8 character or the Unicode code point in its UCN format
     * @access public
     */
    public function convertCode($code)
    {
        // converts hexadecimal code to binary, e.g. 000A, or 0x000A, or \u000A to 10
        is_string($code) and $code = hexdec($code);
        // converts code to UCN hexadecimal string or packs code to UTF-8 binary string
        return $this->codeFormat == 'ucn'? $this->string->dec2ucn($code) : $this->string->unicode2char($code);
    }

    /**
     * Expands the decomposition mappings
     *
     * @param  string  $code           the Unicode code point
     * @param  array   &$decomposition the code point decomposition mappings
     * @return boolean the code point was expanded if true, or has no
     *                 decomposition if false
     * @access private
     */
    private function expandDecomp($code, &$decomposition)
    {
        static $self = __FUNCTION__; // a recursive function
        static $isExpanded;
        // the code point decomposition is being expanded,  error: entering infinite loop
        isset($isExpanded[$code]) and die('Error: recursion loop in ' . __CLASS__ . '::' . __FUNCTION__);

        if (!isset($decomposition[$code])) {
            // the code point has no decomposition
            return false;
        }
        // the code point has a decomposition
        // flags the code point decomposition as being expanded
        $isExpanded[$code] = true;
        $decomposed = array();
        foreach($decomposition[$code] as $mappedCode) {
            if ($this->$self($mappedCode, $decomposition)) {
                // the mapped code has a decomposition, adds the mapped code decomposition
                $decomposed = array_merge($decomposed, $decomposition[$mappedCode]);
            } else {
                // the mapped code has no decomposition, adds the mapped code itself
                $decomposed[] = $mappedCode;
            }
        }
        // updates the code point decomposition, the code point decomposition is now expanded
        $decomposition[$code] = $decomposed;
        unset($isExpanded[$code]);

        return true;
    }

    /**
     * Gets the name list of the compiled files
     *
     * @return array  the name list of compiled files
     * @access public
     */
    public function getFileNames()
    {
        return $this->compiled;
    }

    /**
     * Implodes an array's arrays
     *
     * @param  array  $array the array of arrays
     * @return array  an array of strings
     * @access public
     */
    public function implode($array)
    {
        static $callback = null;
        // create the function that implodes an array
        $callback or $callback = create_function('$values', 'return implode("", $values);');

        return array_map($callback, $array);
    }

    /**
     * Parses a Unicode text data file
     *
     * Can extract only the lines that have (or do not have if specified) a keyword.
     * Expands ranges of Unicode code points. Converts the Unicode code points
     * to UTF-8 characters or their UCN format.
     *
     * @param  string  $fileName       the file to parse
     * @param  string  $keyword        the keyword within the test lines to extract,
     *                                 e.g. "HANGUL"
     * @param  boolean $includeKeyword extracts the lines with the keyword if true,
     *                                 or without if false
     * @param  boolean $codeIndexing   entries are to be indexed with the code point
     *                                 as a key if true, or with the line number
     * @return array   the parsed data
     * @access private
     */
    private function parseFile($fileName, $keyword = null, $includeKeyword = true, $codeIndexing = true)
    {
        static $firstCode;
        static $firstProperties = null;

        $data = array();
        foreach(file($fileName) as $line => $string) {
            if ($keyword === null or (strpos($string, $keyword) !== false) === $includeKeyword) {
                // the string contains (or not) the keyword
                // removes the comments, extracts the properties, trims the properties
                list($string) = explode('#', $string);
                $properties = explode(';', trim($string));
                $properties = array_map('trim', $properties);

                if (($codeRange = current($properties)) != '') {
                    // not an empty/comment line, captures the code range or the single code
                    if (!$codeIndexing) {
                        // file line number indexing, captures the properties
                        $data[$line + 1] = $properties;
                    } else {
                        // entries to be indexed with the code point value
                        if (($name = next($properties)) !== false) {
                            // the second entry, typically a name, is not empty
                            if (strpos($name, ', First')) {
                                // the first code value of a range,
                                // e.g. <CJK Ideograph Extension A, First>
                                // captures the first code value
                                $firstCode = $codeRange;
                                $firstProperties = $properties;
                                continue;
                            } else if (strpos($name, ', Last')) {
                                // the last code value of a range,
                                // e.g. <CJK Ideograph Extension A, Last>
                                // note: the first and last code entries are expected
                                // to follow each other, see UnicodeData.txt
                                // sets the range with the previously stored
                                // first and last code values
                                $codeRange = $firstCode . '..' . $codeRange;
                            }
                        }
                        // extracts the possible code range, converts the first
                        // and last code values to binary
                        $range = explode('..', $codeRange);
                        $firstCode = hexdec(current($range));
                        $lastCode = next($range) and
                        $lastCode = hexdec($lastCode) or $lastCode = $firstCode;

                        if (($lastCode - $firstCode) <= $this->rangeLimit) {
                            // the range is to be expanded
                            // changes the array to string if there is only one property
                            count($properties) == 1 and $properties = $codeRange;
                            for($code = $firstCode; $code <= $lastCode; $code++) {
                                // scans the range
                                $formatted = $this->convertCode($code);
                                isset($data[$formatted]) and
                                die('Error: not expecting duplicate code point entries in ' . $fileName);
                                // indexes the properties with code
                                $data[$formatted] = $properties;
                            }
                        } else {
                            // a large range
                            if ($this->codeFormat == 'ucn') {
                                // the code format is hexadecimal
                                // changes the range first and last codes to hexadecimal
                                $firstCode = $this->convertCode($firstCode);
                                $lastCode = $this->convertCode($lastCode);
                            }
                            // else if: UTF-8, leave the range codes in decimal
                            // captures the range and properties
                            $rangeList[] = array($firstCode => $firstProperties, $lastCode => $properties);
                        }
                    }
                }
            }
        }
        // adds the ranges to the end
        isset($rangeList) and $data['code_ranges'] = $rangeList;

        return $data;
    }

    /**
     * Splits and converts a Unicode code points string to UTF-8 characters or their UCN format
     *
     * @param  string $codes the Unicode code points string,
     *                       e.g. a decomposition mapping: 0020 0308
     * @return array  the UTF-8 characters or their UCN format
     * @access public
     */
    public function splitCodes($codes)
    {
        // splits the string into code points
        $codes = preg_split('~[^\da-f]~i', $codes, -1, PREG_SPLIT_NO_EMPTY) and
        // converts the code points to UTF-8 characters or their UCN format
        $codes = array_map(array($this, 'convertCode'), $codes);

        return $codes;
    }
}

?>