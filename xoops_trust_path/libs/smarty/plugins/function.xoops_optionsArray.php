<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_optionsArray.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
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
	$tags = "";
	$objectArr =& $params['from'];
	$default = isset($params['default']) ? $params['default'] : null;
	$id = isset($params['id']) ? $params['id'] : null;
    $root =& XCube_Root::getSingleton();
    $textFilter =& $root->getTextFilter();
	foreach ($objectArr as $object) {
	    $value = $textFilter->toShow($object->get($params['value']));
	    $label = $textFilter->toShow($object->get($params['label']));
		
		$selected = "";
		if (is_array($default) && in_array($object->get($params['value']), $default)) {
			$selected = " selected=\"selected\"";
		}
		elseif (!is_array($default) && $object->get($params['value']) == $default) {
			$selected = " selected=\"selected\"";
		}
		
		if ($id) {
			$t_id = XOOPS_INPUT_DEFID_PREFIX . $id."_".$value;
			$tags .= "<option id=\"${t_id}\" value=\"${value}\"${selected}>${label}</option>\n";
		}
		else {
			$tags .= "<option value=\"${value}\"${selected}>${label}</option>\n";
		}
	}
	
	print $tags;
}

?>
