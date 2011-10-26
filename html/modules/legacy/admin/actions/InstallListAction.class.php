<?php
/**
 *
 * @package Legacy
 * @version $Id: InstallListAction.class.php,v 1.4 2008/09/25 15:11:45 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * @public
 * @internal
 * List up non-installation modules.
 */
class Legacy_InstallListAction extends Legacy_Action
{
	var $mModuleObjects = null;
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$handler =& xoops_getmodulehandler('non_installation_module');

		$this->mModuleObjects =& $handler->getObjects();
		
		return LEGACY_FRAME_VIEW_INDEX;
	}
	
	function executeViewIndex(&$controller, &$xoopsUser, &$renderer)
	{
		$renderer->setTemplateName("install_list.html");
		$renderer->setAttribute('moduleObjects', $this->mModuleObjects);
	}
}

?>
