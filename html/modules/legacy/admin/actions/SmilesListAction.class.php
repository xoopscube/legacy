<?php
/**
 *
 * @package Legacy
 * @version $Id: SmilesListAction.class.php,v 1.3 2008/09/25 15:11:50 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/SmilesFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/SmilesListForm.class.php";

class Legacy_SmilesListAction extends Legacy_AbstractListAction
{
	var $mSmilesObjects = array();
	var $mActionForm = null;
	var $mpageArr = array(5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0);

	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =new Legacy_SmilesListForm();
		$this->mActionForm->prepare();
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('smiles');
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
		$filter =new Legacy_SmilesFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=SmilesList";
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("smiles_list.html");
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('pageArr', $this->mpageArr);
		$render->setAttribute('filterForm', $this->mFilter);

		$smiles_handler =& $this->_getHandler();
		$smiles_total = $smiles_handler->getCount();
		$display_smiles_total = $smiles_handler->getCount(new Criteria('display', 1));
		$render->setAttribute('SmilesTotal', $smiles_total);
		$render->setAttribute('displaySmilesTotal', $display_smiles_total);
		$render->setAttribute('notdisplaySmilesTotal', $smiles_total - $display_smiles_total);
	}

	function execute(&$controller, &$xoopsUser)
	{
		$form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
		if ($form_cancel != null) {
			return LEGACY_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();

		if ($this->mActionForm->hasError()) {
			return $this->_processConfirm($controller, $xoopsUser);
		}
		else {
			return $this->_processSave($controller, $xoopsUser);
		}
	}
	
	function _processConfirm(&$controller,&$xoopsUser)
	{
        		$codeArr = $this->mActionForm->get('code');
		$smilesHandler =& xoops_getmodulehandler('smiles');
		//
		// Do mapping.
		//
		foreach (array_keys($codeArr) as $sid) {
			$smiles =& $smilesHandler->get($sid);
			if (is_object($smiles)) {
			$this->mSmilesObjects[$sid] =& $smiles;
			}
			unset($smiles);
		}

		return LEGACY_FRAME_VIEW_INPUT;
	}

    function _processSave(&$controller, &$xoopsUser)
    {
        		$codeArr = $this->mActionForm->get('code');
        		$smilesHandler =& xoops_getmodulehandler('smiles');

        		foreach(array_keys($codeArr) as $sid) {
			$smiles =& $smilesHandler->get($sid);
			if (is_object($smiles)) {
            		$olddata['code'] = $smiles->get('code');
            		$olddata['emotion'] = $smiles->get('emotion');
            		$olddata['display'] = $smiles->get('display');
            		$newdata['code'] = $this->mActionForm->get('code', $sid);
            		$newdata['emotion'] = $this->mActionForm->get('emotion', $sid);
            		$newdata['display'] = $this->mActionForm->get('display', $sid);
            		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
                		$smiles->set('code', $this->mActionForm->get('code', $sid));
                		$smiles->set('emotion', $this->mActionForm->get('emotion', $sid));
                		$smiles->set('display', $this->mActionForm->get('display', $sid));
                		if (!$smilesHandler->insert($smiles)) {
				return LEGACY_FRAME_VIEW_ERROR;
                		}
            		}//count if
			}//object if
        		}//foreach

        		foreach(array_keys($codeArr) as $sid) {
		if($this->mActionForm->get('delete', $sid) == 1) {
			$smiles =& $smilesHandler->get($sid);
			if (is_object($smiles)) {
				if( !$smilesHandler->delete($smiles) ) {
				return LEGACY_FRAME_VIEW_ERROR;
				}
			}
		}
		}
		return LEGACY_FRAME_VIEW_SUCCESS;

    }

	/**
	 * To support a template writer, this send the list of mid that actionForm kept.
	 */
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("smiles_list_confirm.html");
		$render->setAttribute('smilesObjects', $this->mSmilesObjects);
		$render->setAttribute('actionForm', $this->mActionForm);
		
		//
		// To support a template writer, this send the list of mid that
		// actionForm kept.
		//
		$t_arr = $this->mActionForm->get('code');
		$render->setAttribute('sids', array_keys($t_arr));
	}


	function executeViewSuccess(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=SmilesList');
	}

	function executeViewError(&$controller, &$xoopsUser, &$renderer)
	{
		$controller->executeRedirect('./index.php?action=SmilesList', 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=SmilesList');
	}

}

?>
