<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_optionsArray.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_optionsArray
 * Version:  0.1
 * Date:     Apr 6, 2006
 * Author:   minahito
 * Purpose:  Build option tags from array of object who have value and text in 
             own properties.
 * Input:    from = template variable that is array of object 
 *           label = property name to get the text.
 *           value = property name to get the value.
 *           default = selected value. variable (include Array).
 * 
 * -------------------------------------------------------------
 */

// This overlaps with "xoops_input".
//define ("XOOPS_INPUT_DEFID_PREFIX", "legacy_xoopsform_");

function smarty_function_xoops_optionsArray($params, &$smarty)
{
	//
	// We should check more.
	//
	$tags = '';
	$objectArr =& $params['from'];
	$default = isset($params['default']) ? $params['default'] : null;
	$id = isset($params['id']) ? XOOPS_INPUT_DEFID_PREFIX . $params['id'] . '_': null;
	static $textFilter;
	if (!isset($textFilter)) {
		$root =& XCube_Root::getSingleton();
		$textFilter = $root->getTextFilter();
	}
	$vname = $params['value'];
	$lname = $params['label'];
	$isarr = is_array($default);
	foreach ($objectArr as $object) {
	    $value = $object->get($vname);
		
		$selected = ($isarr?in_array($value, $default):$value==$default)?' selected="selected"':'';
		$value = $textFilter->toShow($value);
		$label = $textFilter->toShow($object->get($lname));
		$tags .= $id?"<option id=\"$id$value\" value=\"$value\"$selected>$label</option>\n":"<option value=\"$value\"$selected>$label</option>\n";
	}
	
	print $tags;
}

?>
