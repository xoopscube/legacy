<?php
/**
 *
 * @package Legacy
 * @version $Id: modifier.xoops_html_purifier.php,v 1.0 2010/03/25 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
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
	$config->set('Core.Encoding', $encoding);
	if(in_array($doctype, $doctypeArr)){
		$config->set('HTML.Doctype', $doctype);
	}

	$purifier = new HTMLPurifier($config);
	return $purifier->purify($html);
}

?>
