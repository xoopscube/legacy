<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_html_purifier.php,v 1.0 2010/03/25 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 modifier
 * Name:	 xoops_html_purifier
 * Purpose:  Purify dirty html code(XSS contains).
 * Input:	 html : html text
 *		 	 encoding : 
 *		 	 doctype : HTML 4.01 Strict
 *					   HTML 4.01 Transitional
 *					   XHTML 1.0 Strict
 *					   XHTML 1.0 Transitional
 *					   XHTML 1.1
 * -------------------------------------------------------------
 */
function smarty_modifier_xoops_html_purifier($html, $ecoding=null, $doctype=null)
{
	require_once XOOPS_LIBRARY_PATH.'/htmlpurifier/library/HTMLPurifier.auto.php';
	$encoding = $encoding ? $encoding : _CHARSET; 
	$doctypeArr = array("HTML 4.01 Strict","HTML 4.01 Transitional","XHTML 1.0 Strict","XHTML 1.0 Transitional","XHTML 1.1");

	$config = HTMLPurifier_Config::createDefault();
	if(in_array($doctype, $doctypeArr)){
		$config->set('HTML.Doctype', $doctype);
	}

	if ($_conv = ($encoding !== 'UTF-8' && function_exists('mb_convert_encoding'))) {
		$_substitute = mb_substitute_character();
		mb_substitute_character('none');
		$html = mb_convert_encoding($html, 'UTF-8', $encoding);
		$config->set('Core.Encoding', 'UTF-8');
	} else {
		$config->set('Core.Encoding', $encoding);
	}

	$purifier = new HTMLPurifier($config);
	$html = $purifier->purify($html);

	if ($_conv) {
		$html = mb_convert_encoding($html, $encoding, 'UTF-8');
		mb_substitute_character($_substitute);
	}

	return $html;
}

?>
