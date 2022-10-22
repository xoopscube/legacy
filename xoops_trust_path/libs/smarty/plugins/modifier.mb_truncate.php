<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty mb_truncate modifier plugin
 *
 * Type:	 modifier<br>
 * Name:	 mb_truncate<br>
 * Purpose:  Truncate a string to a certain length if necessary,
 *			 optionally splitting in the middle of a word, and
 *			 appending the $etc string or inserting $etc into the middle.
 * @link https://smarty.php.net/manual/en/language.modifier.truncate.php
 *			truncate (Smarty online manual)
 * @param string
 * @param integer
 * @param string
 * @param boolean
 * @param boolean
 * @return string
 */
function smarty_modifier_mb_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false)
{
	if ($length == 0){
		return '';
	}
	$encode = defined('_CHARSET')? _CHARSET : 'UTF-8';
	
	// decode
	$string = preg_replace(array("/&gt;/i", "/&lt;/i", "/&quot;/i", "/&#039;/i"), array(">", "<", "\"", "'"), $string);
	
	if (mb_strlen($string, $encode) > $length) {
		$length -= mb_strlen($etc, $encode);
		if (!$break_words && !$middle) {
			$string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1, $encode));
		}
		if(!$middle) {
			$string = mb_substr($string, 0, $length, $encode).$etc;
		} else {
			$string = mb_substr($string, 0, $length/2, $encode) . $etc . mb_substr($string, -$length/2, $encode);
		}
	}
	return htmlspecialchars($string, ENT_QUOTES);	// encode
}

/* vim: set expandtab: */

?>