<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_textarea.php,v 1.3 2008/09/25 15:12:35 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 * [ToDo]
 * [ToDo]
 * 1) We may have to move this file to other module with following namespace or
 *    package.
 * 2) Some users and developers want free elements at $params. For example,
 *    $params['script']... This function have not impletented that yet. At
 *    implementing, we will have to define the rule about sanitizing.
 * 
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_textarea
 * Version:  1.0
 * Date:     Nov 2, 2005
 * Author:   minahito
 * Purpose:  textarea tag with sanitize.
 * Input:    name = form 'name'.
 *           value = preset value. Set raw value without htmlspecialchars().
 *           class = form 'class'.
 *           id = form 'id'. If it's empty, ID is defined automatically by prefix & name.
 *           cols = amount of cols. (default 50)
 *           rows = amount of rows. (default 5)
 *           readonly = if it's true, textarea becomes readonly.
 * 
 * Examples: {xoops_textarea name=message cols=40 rows=6 value=$message}
 * -------------------------------------------------------------
 */

define ("XOOPS_TEXTAREA_DEFID_PREFIX", "legacy_xoopsform_");
define ("XOOPS_TEXTAREA_DEFAULT_COLS", "50");
define ("XOOPS_TEXTAREA_DEFAULT_ROWS", "5");

function smarty_function_xoops_textarea($params, &$smarty)
{
    $root =& XCube_Root::getSingleton();
    $textFilter =& $root->getTextFilter();
	if (isset($params['name'])) {
		//
		// Fetch major elements from $params.
		//
		$name = trim($params['name']);
		$class = isset($params['class']) ? trim($params['class']) : null;
        $style = isset($params['style']) ? trim($params['style']) : null;
		$cols = isset($params['cols']) ? intval($params['cols']) : XOOPS_TEXTAREA_DEFAULT_COLS;
		$rows = isset($params['rows']) ? intval($params['rows']) : XOOPS_TEXTAREA_DEFAULT_ROWS;
		$value = isset($params['value']) ? $textFilter->toEdit($params['value']) : null;
		$id = isset($params['id']) ? trim($params['id']) : XOOPS_TEXTAREA_DEFID_PREFIX . $name;
		$readonly = isset($params['readonly']) ? (bool)(trim($params['readonly'])) : false;

		//
		// Build string.
		//
		$string = "<textarea name=\"${name}\" cols=\"${cols}\" rows=\"${rows}\"";
		
		if ($class) {
			$string .= " class=\"${class}\"";
		}

        if ($style) {
            $string .= " style=\"${style}\"";
        }

		$string .= " id=\"$id\"";
		
		if($readonly) {
			$string .= " readonly=\"readonly\"";
		}

		$string .= ">" . $value . "</textarea>";

		//
		// Output.
		//
		print $string;
	}
}

?>
