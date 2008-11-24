<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_input.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 * [ToDo]
 * 1) We may have to move this file to other module with following namespace or
 *    package.
 * 2) This function accepts all of <input> pattern. We may have to divide it.
 * 3) Some users and developers want free elements at $params. For example,
 *    $params['script']... This function have not impletented that yet. At
 *    implementing, we will have to define the rule about sanitizing.
 * 
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_input
 * Version:  1.0
 * Date:     Nov 2, 2005
 * Author:   minahito
 * Purpose:  input tag with sanitize.
 * Input:    name = name of form 'name'
 *           key = key of the name. If this parameter is array, set it.
 *           type = "text" or other
 *           value = preset value
 *           class = form 'class'.
 *           id = form 'id'. If it's empty, ID is defined automatically by prefix & name.
 *           size = size element.
 *           maxlength = maxlength element.
 *           default = If it equals 'value', add "checked" element in the case of "select" or "radio".
 *           disabled = disabled element.
 * 
 * Examples: {xoops_input name=text value=$message}
 *           {xoops_input name=checkbox value=1 default=$value }
 * -------------------------------------------------------------
 */

define ("XOOPS_INPUT_DEFID_PREFIX", "legacy_xoopsform_");

function smarty_function_xoops_input($params, &$smarty)
{
	if (isset($params['name'])) {
		//
		// Fetch major elements from $params.
		//
        $root =& XCube_Root::getSingleton();
        $textFilter =& $root->getTextFilter();
		$name = trim($params['name']);
		$key = isset($params['key']) ? trim($params['key']) : null;
		$type = isset($params['type']) ? strtolower(trim($params['type'])) : "text";
		$value = isset($params['value']) ? $textFilter->toEdit($params['value']) : null;
		$class = isset($params['class']) ? trim($params['class']) : null;
        $style = isset($params['style']) ? trim($params['style']) : null;
		$id = isset($params['id']) ? trim($params['id']) : XOOPS_INPUT_DEFID_PREFIX . $name;
		$size = isset($params['size']) ? intval($params['size']) : null;
		$maxlength = isset($params['maxlength']) ? intval($params['maxlength']) : null;
		$default = isset($params['default']) ? trim($params['default']) : null;
		
		$disabled = (isset($params['disabled']) && $params['disabled'] != false) ? true : false;

		//
		// Build string.
		//
		if ($key != null) {
			$string = "<input name=\"${name}[${key}]\"";
		}
		else {
			$string = "<input name=\"${name}\"";
		}
		
		if ($class) {
			$string .= " class=\"${class}\"";
		}

        if ($style) {
            $string .= " style=\"${style}\"";
        }

		if ($type == "checkbox" || $type == "radio") {
			$string .= " id=\"{$id}_{$value}\"";
		}else {
			$string .= " id=\"{$id}\"";
		}
		
		if ($type) {
			$string .= " type=\"${type}\"";
		}

		if ($size) {
			$string .= " size=\"${size}\"";
		}
		
		if($maxlength) {
			$string .= " maxlength=\"${maxlength}\"";
		}
		
		if($value !== null) {
			$string .= " value=\"${value}\"";
		}
		
		if (isset($params['default'])) {
			$default = trim($params['default']);
			if ($value == $default) {
				if ($type == "checkbox" || $type == "radio") {
					$string .= " checked=\"checked\"";
				}
			}
		}
		
		if ($disabled) {
			$string .= " disabled=\"disabled\"";
		}

		$string .= " />";

		//
		// Output.
		//
		print $string;
	}
}

?>
