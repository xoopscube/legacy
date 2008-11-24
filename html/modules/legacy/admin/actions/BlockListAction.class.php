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
		$this->mActionForm =& new Legacy_BlockListForm();
		$this->mActionForm->prepare();
	}
	
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('newblocks');
		return $handler;
	}

	function &_getFilterForm()
	{
		$filter =& new Legacy_BlockFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}
	
	function &_getPageNavi()
	{
		$navi =& parent::_getPageNavi();
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
