<?php

//%%%%%		TIME FORMAT SETTINGS   %%%%%
if(!defined('_DATESTRING')) define("_DATESTRING","Y/n/j G:i:s");
if(!defined('_MEDIUMDATESTRING')) define("_MEDIUMDATESTRING","Y/n/j G:i");
if(!defined('_SHORTDATESTRING')) define("_SHORTDATESTRING","Y/n/j");

//%%%%%		LANGUAGE SPECIFIC SETTINGS   %%%%%
if(!defined('_CHARSET')) define('_CHARSET', 'UTF-8');

if(!defined('_LANGCODE')) define('_LANGCODE', 'zh-tw');

// change 0 to 1 if this language is a multi-bytes language
if(!defined('XOOPS_USE_MULTIBYTES')) define("XOOPS_USE_MULTIBYTES", "1");

// If _MBSTRING_LANGUAGE is defined, the Legacy_LanguageManager class initializes mb functions.
// This mechanism exists for CJK --- Chinese, Japanese, Korean ---
if(!defined('_MBSTRING_LANGUAGE')) define("_MBSTRING_LANGUAGE", "Chinese");

?>
