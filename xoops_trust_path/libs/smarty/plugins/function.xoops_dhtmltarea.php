<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_dhtmltarea.php,v 1.3 2010/02/22 15:12:36 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 * [ToDo]
 * 1) We may have to move this file to other module with following namespace or
 *	  package.
 * 2) Some users and developers want free elements at $params. For example,
 *	  $params['script']... This function have not impletented that yet. At
 *	  implementing, we will have to define the rule about sanitizing.
 * 3) Users can't set class element to this function, because XoopsForm is
 *	  used. For format xoops_xxxx functions, we may change XoopsForm class
 *	  group.
 * 
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 xoops_dhtmltarea
 * Version:  1.0
 * Date:	 Jun 6, 2004
 * Author:	 minahito
 * Purpose:  cycle through given values
 * Input:	 name = form 'name'.
 *			 value = preset value. Set raw value without htmlspecialchars().
 *			 id = form 'id'. If it's empty, ID is defined automatically by prefix & name.
 *			 cols = amount of cols. (default 50)
 *			 rows = amount of rows. (default 5)
 *			 editor = textarea editor type (default bbcode)
 * 
 * Examples: {xoops_dhtmltarea name=message cols=40 rows=6 value=$message}
 * -------------------------------------------------------------
 */

define ("XOOPS_DHTMLTAREA_DEFID_PREFIX", "legacy_xoopsform_");
define ("XOOPS_DHTMLTAREA_DEFAULT_COLS", "50");
define ("XOOPS_DHTMLTAREA_DEFAULT_ROWS", "5");

function smarty_function_xoops_dhtmltarea($params, &$smarty)
{
	if (!XC_CLASS_EXISTS('xoopsformelement')) {
		require_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
	}
	$form = null;

	$root =& XCube_Root::getSingleton();
	$textFilter =& $root->getTextFilter();
	if (isset($params['name'])) {
		//
		// Fetch major elements from $params.
		//
		$params['name'] = trim($params['name']);
		$params['class'] = isset($params['class']) ? trim($params['class']) : null;
		$params['cols'] = isset($params['cols']) ? intval($params['cols']) : XOOPS_DHTMLTAREA_DEFAULT_COLS;
		$params['rows'] = isset($params['rows']) ? intval($params['rows']) : XOOPS_DHTMLTAREA_DEFAULT_ROWS;
		$params['value'] = isset($params['value']) ? $textFilter->toEdit($params['value']) : null;
		$params['id'] = isset($params['id']) ? trim($params['id']) : XOOPS_DHTMLTAREA_DEFID_PREFIX . $params['name'];
	
		//
		// Build the object for output.
		//
		$html = "";
		switch($params['editor']){
		case 'html':
			XCube_DelegateUtils::call("Site.TextareaEditor.HTML.Show", new XCube_Ref($html), $params);
			break;
		
		case 'none':
			XCube_DelegateUtils::call("Site.TextareaEditor.None.Show", new XCube_Ref($html), $params);
			break;
		case 'bbcode':
		default:
			XCube_DelegateUtils::call("Site.TextareaEditor.BBCode.Show", new XCube_Ref($html), $params);
			break;
		}
		print $html;
	
		/*
		$form =new XoopsFormDhtmlTextArea($name, $name, $value, $rows, $cols);
		$form->setId($id);
		if ($class != null) {
			$form->setClass($class);
		}
		
		print $form->render();
		*/
	}
}

?>
