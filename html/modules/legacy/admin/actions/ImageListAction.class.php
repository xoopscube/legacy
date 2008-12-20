<?php
/**
 *
 * @package Legacy
 * @version $Id: ImageListAction.class.php,v 1.4 2008/09/25 15:11:51 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractListAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/ImageFilterForm.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/ImageListForm.class.php";

class Legacy_ImageListAction extends Legacy_AbstractListAction
{
	var $mImageObjects = array();
	var $mCategory = null;
	var $mActionForm = null;
	var $mpageArr = array(5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 60, 70, 80, 90, 100, 0);

	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =& new Legacy_ImageListForm();
		$this->mActionForm->prepare();
	}

	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('image');
		return $handler;
	}

	function &_getPageNavi()
	{
		$navi =& new XCube_PageNavigator($this->_getBaseUrl(), XCUBE_PAGENAVI_START | XCUBE_PAGENAVI_PERPAGE);

		$root =& XCube_Root::getSingleton();
		$perpage = $root->mContext->mRequest->getRequest($navi->mPrefix.'perpage');
		if (isset($perpage) && intval($perpage) == 0) { 	
		$navi->setPerpage(0);
		}
		return $navi;
	}

	function &_getFilterForm()
	{
		$filter =& new Legacy_ImageFilterForm($this->_getPageNavi(), $this->_getHandler());
		return $filter;
	}

	function _getBaseUrl()
	{
		return "./index.php?action=ImageList";
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		$result = parent::getDefaultView($controller, $xoopsUser);
		if ($result == LEGACY_FRAME_VIEW_INDEX) {
			$cat_id = $controller->mRoot->mContext->mRequest->getRequest('imgcat_id');
			$handler =& xoops_getmodulehandler('imagecategory');
			$this->mCategory =& $handler->get($cat_id);
			
			if ($this->mCategory == null) {
			$controller->executeForward("./index.php?action=ImagecategoryList");
			}
		}
		
		return $result;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("image_list.html");
		
		foreach (array_keys($this->mObjects) as $key) {
			$this->mObjects[$key]->loadImagecategory();
		}
		
		$render->setAttribute("objects", $this->mObjects);
		$render->setAttribute("pageNavi", $this->mFilter->mNavi);
		
		$render->setAttribute("category", $this->mCategory);
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute('pageArr', $this->mpageArr);
		$render->setAttribute('filterForm', $this->mFilter);

		$image_handler =& $this->_getHandler();
		$imgcat_id = $controller->mRoot->mContext->mRequest->getRequest('imgcat_id');
		$cat_id = isset($imgcat_id) ? intval($imgcat_id) : 0;
		$total_criteria =& new CriteriaCompo(new Criteria('imgcat_id', $cat_id));
		$image_total = $image_handler->getCount($total_criteria);
		$total_criteria->add(new Criteria('image_display', 1));
		$display_image_total = $image_handler->getCount($total_criteria);
		$render->setAttribute('ImageTotal', $image_total);
		$render->setAttribute('displayImageTotal', $display_image_total);
		$render->setAttribute('notdisplayImageTotal', $image_total - $display_image_total);


	}

	function execute(&$controller, &$xoopsUser)
	{

		$imgcatid = $controller->mRoot->mContext->mRequest->getRequest('imgcatid');
		$cat_id = isset($imgcatid) ? intval($imgcatid) : 0;
		$handler =& xoops_getmodulehandler('imagecategory');
		$this->mCategory =& $handler->get($cat_id);
		if ($this->mCategory == null) {
		$controller->executeForward("./index.php?action=ImagecategoryList");
		}

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
        		$nicenameArr = $this->mActionForm->get('nicename');
		$imageHandler =& xoops_getmodulehandler('image');
		//
		// Do mapping.
		//
		foreach (array_keys($nicenameArr) as $imid) {
			$image =& $imageHandler->get($imid);
			if (is_object($image)) {
			$this->mImageObjects[$imid] =& $image;
			}
			unset($image);
		}
	

		return LEGACY_FRAME_VIEW_INPUT;
	}

    function _processSave(&$controller, &$xoopsUser)
    {
        		$nicenameArr = $this->mActionForm->get('nicename');
        		$imageHandler =& xoops_getmodulehandler('image');

        		foreach(array_keys($nicenameArr) as $imid) {
			$image =& $imageHandler->get($imid);
			if (is_object($image)) {
            		$olddata['nicename'] = $image->get('image_nicename');
            		$olddata['weight'] = $image->get('image_weight');
            		$olddata['display'] = $image->get('image_display');
            		$newdata['nicename'] = $this->mActionForm->get('nicename', $imid);
            		$newdata['weight'] = $this->mActionForm->get('weight', $imid);
            		$newdata['display'] = $this->mActionForm->get('display', $imid);
            		if (count(array_diff_assoc($olddata, $newdata)) > 0 ) {
                		$image->set('image_nicename', $this->mActionForm->get('nicename', $imid));
                		$image->set('image_weight', $this->mActionForm->get('weight', $imid));
                		$image->set('image_display', $this->mActionForm->get('display', $imid));
                		if (!$imageHandler->insert($image)) {
				return LEGACY_FRAME_VIEW_ERROR;
                		}
            		}//count if
			}//object if
        		}//foreach

        		foreach(array_keys($nicenameArr) as $imid) {
		if($this->mActionForm->get('delete', $imid) == 1) {
			$image =& $imageHandler->get($imid);
			if (is_object($image)) {
				if( !$imageHandler->delete($image) ) {
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
		$render->setTemplateName("image_list_confirm.html");
		$render->setAttribute('imageObjects', $this->mImageObjects);
		$render->setAttribute('actionForm', $this->mActionForm);
		$render->setAttribute("category", $this->mCategory);		
		//
		// To support a template writer, this send the list of mid that
		// actionForm kept.
		//
		$t_arr = $this->mActionForm->get('nicename');
		$render->setAttribute('imids', array_keys($t_arr));
	}


	function executeViewSuccess(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=ImageList&imgcat_id='.$this->mCategory->get('imgcat_id'));
	}

	function executeViewError(&$controller, &$xoopsUser, &$renderer)
	{
		$controller->executeRedirect('./index.php?action=ImageList&imgcat_id='.$this->mCategory->get('imgcat_id'), 1, _MD_LEGACY_ERROR_DBUPDATE_FAILED);
	}

	function executeViewCancel(&$controller,&$xoopsUser,&$renderer)
	{
		$controller->executeForward('./index.php?action=ImageList&imgcat_id='.$this->mCategory->get('imgcat_id'));
	}

}

?>
