<?php
/**
 *
 * @package Legacy
 * @version $Id: function.legacy_button.php,v 1.3 2008/09/25 15:12:36 kilica Exp $
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
 * Name:     legacy_button
 * Version:  1.0
 * Date:     Oct 14, 2005
 * Author:   minahito
 * Purpose:  Display a button with the control
 * Input:    id = ID of form 'name'
 *           Text = The displayed text of the button
 *           class = The class name of the form
 * Examples: <{legacy_button id=commentpost Text='POST'}>
 * -------------------------------------------------------------
 */

function smarty_function_legacy_button($params, &$smarty)
{
	if (isset($params['id'])) {
		//
		// Fetch major elements from $params.
		//
		$id = trim($params['id']);
		$name = "Legacy.Event.User.${id}";
		$text = isset($params['Text']) ? htmlspecialchars(trim($params['Text']), ENT_QUOTES) : null;
		$class = isset($params['class']) ? htmlspecialchars(trim($params['class']), ENT_QUOTES) : null;

		//
		// Build string.
		//
		$string = "<input type='submit' id='${id}' name='${name}'";
		
		if ($text != null) {
			$string .= " value='${text}'";
		}
		
		if ($class != null) {
			$string .= " class='${class}'";
		}
		
		$string .= " />";

		//
		// Output.
		//
		print $string;
	}
}

?>
