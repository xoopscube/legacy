<?php
/**
 *
 * @package Legacy
 * @version $Id: MiscSmiliesAction.class.php,v 1.3 2008/09/25 15:12:11 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/forms/SmilesFilterForm.class.php";

class Legacy_MiscSmiliesAction extends Legacy_AbstractListAction
{
	/**
	 * @var string
	 */
	var $mTargetName = null;

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('smiles', 'legacy');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =& new Legacy_SmilesFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./misc.php?type=Smilies";
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		$this->mTargetName = xoops_getrequest('target');
		return parent::getDefaultView($controller, $xoopsUser);
	}
	
	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		//
		// Because this action's template uses BASE message catalog, load it.
		//
		$root =& $controller->mRoot;
		$root->mLanguageManager->loadModuleMessageCatalog('legacy');
		
		$render->setTemplateName("legacy_misc_smilies.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$render->setAttribute("targetName", $this->mTargetName);
	}
}

?>
