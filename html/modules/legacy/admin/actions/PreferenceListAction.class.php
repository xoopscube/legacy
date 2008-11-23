<?php
/**
 *
 * @package Legacy
 * @version $Id: PreferenceListAction.class.php,v 1.3 2008/09/25 15:11:50 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/PreferenceEditForm.class.php";

class Legacy_PreferenceListAction extends Legacy_Action
{
	var $mObjects = array();
	
	function prepare(&$controller, &$xoopsUser)
	{
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$handler =& xoops_gethandler('configcategory');
		$this->mObjects =& $handler->getObjects();
		
		return LEGACY_FRAME_VIEW_INDEX;
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		return $this->getDefaultView($controller, $xoopsUser);
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("preference_list.html");
		$render->setAttribute('objects', $this->mObjects);
	}
}

?>
