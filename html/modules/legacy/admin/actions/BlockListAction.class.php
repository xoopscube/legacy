<?php
/**
 *
 * @package Legacy
 * @version $Id: BlockListAction.class.php,v 1.3 2008/09/25 15:11:47 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/BlockFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/BlockListForm.class.php";

class Legacy_BlockListAction extends Legacy_AbstractListAction
{
	var $mActionForm = null;
	
	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =new Legacy_BlockListForm();
		$this->mActionForm->prepare();
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('newblocks');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =new Legacy_BlockFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}
	
	function &_getPageNavi()
	{
		$navi =new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);

		$root =& XCube_Root::getSingleton();
		$perpage = $root->mContext->mRequest->getRequest($navi->mPrefix.'perpage');

		if (isset($perpage) && intval($perpage) == 0) { 	
		$navi->setPerpage(0);
		
		return $navi;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=BlockList";
	}

	function execute(&$controller, &$xoopsUser)
	{
		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
		
		$weightArr = $this->mActionForm->get('weight');
		
		$handler =& xoops_getmodulehandler('newblocks');
		foreach (array_keys($weightArr) as $bid) {
			$block =& $handler->get($bid);
			if (is_object($block) && $block->get('isactive') == 1 && $block->get('visible') == 1) {
                $olddata['weight'] = $block->get('weight');
                $olddata['side'] = $block->get('side');
                $olddata['bcachetime'] = $block->get('bcachetime');
                $newdata['weight'] = $this->mActionForm->get('weight', $bid);
                $newdata['side'] = $this->mActionForm->get('side', $bid);
                $newdata['bcachetime'] = $this->mActionForm->get('bcachetime', $bid);
                if ( count(array_diff_assoc($olddata, $newdata)) > 0 ) {
    				$block->set('weight', $this->mActionForm->get('weight', $bid));
    				$block->set('side', $this->mActionForm->get('side', $bid));
    				$block->set('bcachetime', $this->mActionForm->get('bcachetime', $bid));
    				$block->set('last_modified', time());
    				
    				if (!$handler->insert($block)) {
    					return LEGACY_FRAME_VIEW_ERROR;
    				}
    			}
			}
		}
		
		return LEGACY_FRAME_VIEW_SUCCESS;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("block_list.html");
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadModule();
			$this->mObjects[$key]->loadColumn();
			$this->mObjects[$key]->loadCachetime();
		}
		
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		
		//
		// Load cache-time pattern objects and set.
		//
		$handler =& xoops_gethandler('cachetime');
		$cachetimeArr =& $handler->getObjects();
		$render->setAttribute('cachetimeArr', $cachetimeArr);
		
		$render->setAttribute('actionForm', $this->mActionForm);
		//
		$handler =& xoops_getmodulehandler('columnside');
		$columnSideArr =& $handler->getObjects();
		$render->setAttribute('columnSideArr', $columnSideArr);

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

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward('./index.php?action=BlockList');
	}
	
	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect("./index.php?action=BlockInstallList", 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}
}

?>
