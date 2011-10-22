<?php

//%%%%%		TIME FORMAT SETTINGS   %%%%%
if(!defined('_DATESTRING')) define("_DATESTRING","Y/n/j G:i:s");
if(!defined('_MEDIUMDATESTRING')) define("_MEDIUMDATESTRING","Y/n/j G:i");
if(!defined('_SHORTDATESTRING')) define("_SHORTDATESTRING","Y/n/j");
define('_JSDATEPICKSTRING','yy-mm-dd');
define('_PHPDATEPICKSTRING','Y-m-d');


//%%%%%     REQUSTED DATA SETTINGS   %%%%%
if (!defined('_REQUESTED_DATA_NAME')) define('_REQUESTED_DATA_NAME', 'requested_data_name');
if (!defined('_REQUESTED_ACTION_NAME')) define('_REQUESTED_ACTION_NAME', 'requested_action_name');
if (!defined('_REQUESTED_DATA_ID')) define('_REQUESTED_DATA_ID', 'requested_data_id');


//%%%%%		LANGUAGE SPECIFIC SETTINGS	 %%%%%
if(!defined('_CHARSET')) define('_CHARSET', 'EUC-JP');

if(!defined('_LANGCODE')) define('_LANGCODE', 'ja');

// change 0 to 1 if this language is a multi-bytes language
if(!defined('XOOPS_USE_MULTIBYTES')) define("XOOPS_USE_MULTIBYTES", "1");

// If _MBSTRING_LANGUAGE is defined, the Legacy_LanguageManager class initializes mb functions.
// This mechanism exists for CJK --- Chinese, Japanese, Korean ---
if(!defined('_MBSTRING_LANGUAGE')) define("_MBSTRING_LANGUAGE", "japanese");

//
// Register the function about local.
//
if (class_exists('XCube_Root') && function_exists('mb_convert_encoding') && function_exists('mb_convert_kana')) {
	$root =& XCube_Root::getSingleton();
	$root->mDelegateManager->add('Legacy_Mailer.ConvertLocal', 'Legacy_JapaneseEucJP_convLocal');
}

@define('LEGACY_MAIL_LANG','ja');
@define('LEGACY_MAIL_CHAR','iso-2022-jp');
@define('LEGACY_MAIL_ENCO','7bit');

function Legacy_JapaneseEucJP_convLocal(&$text, $mime)
{
	if ($mime) {
		switch ($mime) {
			case '1':
				$text = mb_encode_mimeheader($text, LEGACY_MAIL_CHAR, 'B', "\n");
				break;
			case '2':
				$text = mb_encode_mimeheader($text, LEGACY_MAIL_CHAR, 'B', "");
				break;
		}
	}
	else {
		$text = mb_convert_encoding($text, 'JIS', _CHARSET);
	}
}

function xoops_language_trim($text)
{
	if (function_exists('mb_convert_kana')) {
		$text = mb_convert_kana($text, 's');
	}
	$text = trim($text);
	return $text;
}
?>
