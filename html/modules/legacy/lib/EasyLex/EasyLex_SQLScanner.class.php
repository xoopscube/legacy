<?php

/**
 * @package EasyLexSQL
 * @version $Id: EasyLex_SQLScanner.class.php,v 1.3 2008/10/12 04:31:22 minahito Exp $
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license https://github.com/xoopscube/xcl/blob/master/BSD_license.txt
 *
 */

define('EASYLEX_SQL_UNKNOWN', 0);
define('EASYLEX_SQL_DIGIT', 1);
define('EASYLEX_SQL_LETTER', 2);
define('EASYLEX_SQL_STRING_LITERAL', 3);
define('EASYLEX_SQL_STRING_LITERAL_ESCAPE', 10);
define('EASYLEX_SQL_OPEN_PARENTHESIS', 4);
define('EASYLEX_SQL_CLOSE_PARENTHESIS', 5);
define('EASYLEX_SQL_SEPARATER', 6);
define('EASYLEX_SQL_SEMICOLON', 7);
define('EASYLEX_SQL_MARK', 8);
define('EASYLEX_SQL_COMMA', 9);

class EasyLex_SQLToken
{
    public $mType = EASYLEX_SQL_UNKNOWN;
    public $mValue = '';

    public function __construct($type, $value)
    {
        $this->mType = $type;
        $this->mValue = $value;
    }

    public function getOutputValue()
    {
        if (EASYLEX_SQL_SEPARATER == $this->mType) {
            return '';
        } else {
            return $this->mValue;
        }
    }

    public function getValue()
    {
        if (EASYLEX_SQL_SEPARATER == $this->mType) {
            return '';
        }

        if (EASYLEX_SQL_STRING_LITERAL == $this->mType) {
            return substr($this->mValue, 1, strlen($this->mValue) - 2);
        }

        return $this->mValue;
    }
}

/**
 * This is BSD easy lexcal scanner for SQL.
 *
 * @version 1.00
 */
class EasyLex_SQLScanner
{
    public $mTokens = [];
    public $mStatus = EASYLEX_SQL_UNKNOWN;

    /**
     * @var Array of char
     */
    public $mBuffer = [];

    public $mIndex = 0;

    public $mActiveToken = '';

    public $mActiveQuoteMark = null;

    public function setBuffer($buffer)
    {
        $this->mBuffer = [];
        for ($i = 0; $i < strlen($buffer); $i++) {
            $this->mBuffer[$i] = $buffer[$i];
        }

        $this->mIndex = 0;
    }

    public function parse()
    {
        while ($this->mIndex <= count($this->mBuffer)) {
            if ($this->mIndex == count($this->mBuffer)) {
                $ch = '';
                $type = EASYLEX_SQL_UNKNOWN;
            } else {
                $ch = $this->mBuffer[$this->mIndex];
                $type = $this->_getChrType($ch);
            }

            switch ($this->mStatus) {
                case EASYLEX_SQL_UNKNOWN:
                    $this->_parseUnknown($ch, $type);
                    break;

                case EASYLEX_SQL_DIGIT:
                    $this->_parseDigit($ch, $type);
                    break;

                case EASYLEX_SQL_LETTER:
                    $this->_parseLetter($ch, $type);
                    break;

                case EASYLEX_SQL_STRING_LITERAL:
                    $this->_parseStringLiteral($ch, $type);
                    break;

                case EASYLEX_SQL_STRING_LITERAL_ESCAPE:
                    $this->_parseStringLiteralEscape($ch, $type);
                    break;

                case EASYLEX_SQL_OPEN_PARENTHESIS:
                    $this->_parseOpenParenthesis($ch, $type);
                    break;

                case EASYLEX_SQL_CLOSE_PARENTHESIS:
                    $this->_parseCloseParenthesis($ch, $type);
                    break;

                case EASYLEX_SQL_SEPARATER:
                    $this->_parseSeparater($ch, $type);
                    break;

                case EASYLEX_SQL_MARK:
                    $this->_parseMark($ch, $type);
                    break;

                case EASYLEX_SQL_SEMICOLON:
                    $this->_parseSemicolon($ch, $type);
                    break;

                case EASYLEX_SQL_COMMA:
                    $this->_parseComma($ch, $type);
                    break;
            }
        }
    }

    /**
     * Load file and set buffer. If $preprocess is true, scan commetns and
     * remove these.
     *
     * @param  string $path file path
     * @param  bool   $preprocess
     * @return bool
     */
    public function loadFile($path, $preprocess = true)
    {
        if (!file_exists($path)) {
            return false;
        }

        $fp = fopen($path, 'rb');
        if (!$fp) {
            return false;
        }

        $t_buff = '';
        while ($str = fgets($fp)) {
            if ($preprocess) {
                $str = preg_replace("/^\s*\#.*/", '', $str);
            }
            $t_buff .= $str;
        }

        $this->setBuffer($t_buff);

        fclose($fp);
        return true;
    }

    public function _getChrType($ch)
    {
        if (preg_match("/\s/", $ch)) {
            return EASYLEX_SQL_SEPARATER;
        }

        if ('(' == $ch) {
            return EASYLEX_SQL_OPEN_PARENTHESIS;
        }

        if (')' == $ch) {
            return EASYLEX_SQL_CLOSE_PARENTHESIS;
        }

        if (';' == $ch) {
            return EASYLEX_SQL_SEMICOLON;
        }

        if (',' == $ch) {
            return EASYLEX_SQL_COMMA;
        }

        if (preg_match('/[0-9]/', $ch)) {
            return EASYLEX_SQL_DIGIT;
        }

        if (preg_match("/[!=<>%\*]/", $ch)) {
            return EASYLEX_SQL_MARK;
        }

        return EASYLEX_SQL_LETTER;
    }

    public function _parseUnknown($ch, $type)
    {
        $this->mStatus = $type;
        $this->mActiveToken .= $ch;
        $this->mIndex++;

        if ("'" == $ch || '"' == $ch || '`' == $ch) {
            $this->mStatus = EASYLEX_SQL_STRING_LITERAL;
            $this->mActiveQuoteMark = $ch;
        }
    }

    public function _parseDigit($ch, $type)
    {
        if (EASYLEX_SQL_DIGIT == $type) {
            $this->mActiveToken .= $ch;
            $this->mIndex++;
        } elseif (EASYLEX_SQL_LETTER == $type) {
            $this->mStatus = EASYLEX_SQL_LETTER;
            $this->mActiveToken .= $ch;
            $this->mIndex++;
        } else {
            $this->_createToken();
        }
    }

    public function _parseLetter($ch, $type)
    {
        if (EASYLEX_SQL_LETTER == $type || EASYLEX_SQL_DIGIT == $type) {
            $this->mActiveToken .= $ch;
            $this->mIndex++;
        } else {
            $this->_createToken();
        }
    }

    public function _parseStringLiteral($ch, $type)
    {
        $this->mActiveToken .= $ch;
        $this->mIndex++;

        if ("\\" == $ch) {
            $this->mStatus = EASYLEX_SQL_STRING_LITERAL_ESCAPE;
        } elseif ($ch == $this->mActiveQuoteMark) {
            $this->_createToken();
        }
    }

    public function _parseStringLiteralEscape($ch, $type)
    {
        $this->mStatus = EASYLEX_SQL_STRING_LITERAL;
    }

    public function _parseOpenParenthesis($ch, $type)
    {
        $this->_createToken();
    }

    public function _parseCloseParenthesis($ch, $type)
    {
        $this->_createToken();
    }

    public function _parseSeparater($ch, $type)
    {
        if (EASYLEX_SQL_SEPARATER == $type) {
            $this->mActiveToken .= $ch;
            $this->mIndex++;
        } else {
            // $this->_createToken();
            $this->mStatus = EASYLEX_SQL_UNKNOWN;
            $this->mActiveToken = '';
        }
    }

    public function _parseSemicolon($ch, $type)
    {
        $this->_createToken();
    }

    public function _parseMark($ch, $type)
    {
        if (EASYLEX_SQL_MARK == $type) {
            $this->mActiveToken .= $ch;
            $this->mIndex++;
        } else {
            $this->_createToken();
        }
    }

    public function _parseComma($ch, $type)
    {
        $this->_createToken();
    }

    public function _createToken($type = null, $value = null)
    {
        if (null === $type) {
            $type = $this->mStatus;
        }

        if (null === $value) {
            $value = $this->mActiveToken;
        }

        $token = new EasyLex_SQLToken($type, $value);
        $this->mTokens[] = &$token;

        $this->mStatus = EASYLEX_SQL_UNKNOWN;
        $this->mActiveToken = '';

        return $token;
    }

    /**
     * Return Array of operations.
     *
     * @return Array $ret[Index] = Array of tokens.
     */
    public function &getOperations()
    {
        $ret = [];
        $t_tokens = [];
        $depth = 0;

        foreach (array_keys($this->mTokens) as $key) {
            if (EASYLEX_SQL_OPEN_PARENTHESIS == $this->mTokens[$key]->mType) {
                $depth++;
            } elseif (EASYLEX_SQL_CLOSE_PARENTHESIS == $this->mTokens[$key]->mType) {
                $depth--;
            }

            $t_tokens[] = &$this->mTokens[$key];

            if (EASYLEX_SQL_SEMICOLON == $this->mTokens[$key]->mType && 0 == $depth) {
                $ret[] = &$t_tokens;
                unset($t_tokens);
                $t_tokens = [];
            }
        }

        if (count($t_tokens) > 0) {
            $ret[] = &$t_tokens;
            unset($t_tokens);
        }

        return $ret;
    }

    public function getSQL()
    {
        $sqls = [];
        $lines = &$this->getOperations();

        foreach ($lines as $line) {
            $t_arr = [];
            foreach ($line as $token) {
                $t_arr[] = $token->getOutputValue();
            }
            $sqls[] = implode(' ', $t_arr);
        }

        return $sqls;
    }
}
