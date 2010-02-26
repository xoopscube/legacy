<?php
/**
 *
 * @package Legacy
 * @version $Id: BlockInstallListAction.class.php,v 1.3 2008/09/25 15:11:54 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/BlockInstallFilterForm.class.php";

class Legacy_BlockInstallListAction extends Legacy_AbstractListAction
{

	var $mpageArr = array(5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0);

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('newblocks');
		return $handler;
	}

	function &_getPageNavi()
	{
		$navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);

		$root =& XCube_Root::getSingleton();
		$perpage = $root->mContext->mRequest->getRequest($navi->mPrefix.'perpage');

		if (isset($perpage) && intval($perpage) == 0) { 	
		$navi->setPerpage(0);
		}
		return $navi;
	}

	function &_getFilterForm()
	{
		$filter =new Legacy_BlockInstallFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=BlockInstallList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("blockinstall_list.html");

		//
		// Lazy load
		//
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadModule();
		}
		
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$moduleHandler =& xoops_gethandler('module');
		$modules =& $moduleHandler->getObjects(new Criteria('isactive', 1));
		$render->setAttribute('modules', $modules);
		$render->setAttribute('filterForm', $this->mFilter);
		$render->setAttribute('pageArr', $this->mpageArr);		

		$block_handler =& $this->_getHandler();
		$block_total = $block_handler->getCount();
		$inactive_block_total = $block_handler->getCount(new Criteria('isactive', 0));
		$active_block_total = $block_total-$inactive_block_total;
		$render->setAttribute('BlockTotal', $block_total);
		$render->setAttribute('ActiveBlockTotal', $active_block_total);
		$render->setAttribute('InactiveBlockTotal', $inactive_block_total);

		$active_installed_criteria = new CriteriaCompo(new Criteria('visible', 1));
		$active_installed_criteria->add(new Criteria('isactive', 1));
		$active_installed_block_total = $block_handler->getCount($active_installed_criteria);
		$render->setAttribute('ActiveInstalledBlockTotal', $active_installed_block_total);
		$render->setAttribute('ActiveUninstalledBlockTotal', $active_block_total - $active_installed_block_total);

		$inactive_installed_criteria = new CriteriaCompo(new Criteria('visible', 1));
		$inactive_installed_criteria->add(new Criteria('isactive', 0));
		$inactive_installed_block_total = $block_handler->getCount($inactive_installed_criteria);
		$render->setAttribute('InactiveInstalledBlockTotal', $inactive_installed_block_total);
		$render->setAttribute('InactiveUninstalledBlockTotal', $inactive_block_total - $inactive_installed_block_total);

	}
}

?>
