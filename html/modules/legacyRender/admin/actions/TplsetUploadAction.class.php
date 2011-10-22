<?php
/**
 * @package legacyRender
 * @version $Id: TplsetUploadAction.class.php,v 1.1 2007/05/15 02:34:17 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacyRender/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacyRender/admin/forms/TplsetUploadForm.class.php";

class LegacyRender_TplsetUploadAction extends LegacyRender_Action
{
	var $mActionForm = null;
	var $mErrorMessages = array();
	
	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =new LegacyRender_TplsetUploadForm();
		$this->mActionForm->prepare();
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		return LEGACYRENDER_FRAME_VIEW_INPUT;
	}
	
	function _addErrorMessage($msg)
	{
		$this->mErrorMessages[] = $msg;
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		if (xoops_getrequest('_form_control_cancel') != null) {
			return LEGACYRENDER_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}
		
		require_once XOOPS_ROOT_PATH . "/class/class.tar.php";
		$tar =new tar();

		$formFile = $this->mActionForm->get('upload');
		
		//
		// [Warn] access private member directly
		// TODO We should define the access method because we oftern 
		//      access private member of XCube_FormFile.
		//
		$tar->openTar($formFile->_mTmpFileName);

		if (!is_array($tar->files)) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		$tplsetName = null;
		foreach ($tar->files as $id => $info) {
			$infoArr = explode('/', str_replace("\\", '/', $info['name']));
			$tplsetName = $this->mActionForm->get('tplset_name');
			if ($tplsetName == null) {
				$tplsetName = trim($infoArr[0]);
			}
			
			if ($tplsetName != null) {
				break;
			}
		}
		
		//
		// Check tplset name.
		//
		if ($tplsetName == null || preg_match('/[' . preg_quote('\/:*?"<>|','/') . ']/', $tplsetName)) {
			$this->_addErrorMessage(_AD_LEGACYRENDER_ERROR_TPLSET_NAME_WRONG);
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		$handler =& xoops_getmodulehandler('tplset');
		if ($handler->getCount(new Criteria('tplset_name', $tplsetName)) != 0) {
			$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACYRENDER_ERROR_TPLSET_ALREADY_EXISTS, $tplsetName));
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		$tplset =& $handler->create();
		$tplset->set('tplset_name', $tplsetName);
		if (!$handler->insert($tplset)) {
			$this->_addErrorMessage(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_TPLSET);
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		$themeimages = array();
		if (!$this->_fetchTemplateFiles($tar, $tplset, $themeimages)) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		if (!$this->_fetchImageset($tar, $tplset, $themeimages)) {
			return LEGACYRENDER_FRAME_VIEW_ERROR;
		}
		
		return LEGACYRENDER_FRAME_VIEW_SUCCESS;
	}
	
	function _fetchTemplateFiles(&$tar, &$tplset, &$themeimages)
	{
		$handler =& xoops_getmodulehandler('tplfile');
		foreach ($tar->files as $id => $info) {
			$infoArr = explode('/', str_replace("\\", '/', $info['name']));
			if (isset($infoArr[3]) && trim($infoArr[3]) == 'blocks') {
				$default =& $handler->find('default', 'block', null, trim($infoArr[2]), trim($infoArr[4]));
			} elseif ((!isset($infoArr[4]) || trim($infoArr[4]) == '') && $infoArr[1] == 'templates') {
				$default =& $handler->find('default', 'module', null, trim($infoArr[2]), trim($infoArr[3]));
			} elseif (isset($infoArr[3]) && trim($infoArr[3]) == 'images') {
				$infoArr[2] = trim($infoArr[2]);
				if (preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $infoArr[2], $match)) {
					$themeimages[] = array('name' => $infoArr[2], 'content' => $info['file']);
				}
			}
			if (count($default) > 0) {
				$tplfile =& $default[0]->createClone($tplset->get('tplset_name'));
				$tplfile->Source->set('tpl_source', $info['file']);
				$tplfile->set('tpl_lastimported', time());
				
				if (!$handler->insert($tplfile)) {
					$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_TPLFILE, $tplfile->get('tpl_file')));
				}
				unset($default);
			}
			unset($info);
		}
		
		return true;
	}

	function _fetchImageset(&$tar, &$tplset, &$themeimages)
	{
		if (count($themeimages) == 0) {
			return true;
		}
		
		$handler =& xoops_gethandler('imageset');
		$imgset =& $handler->create();
		$imgset->set('imgset_name', $tplset->get('tplset_name'));
		$imgset->set('imgset_refid', 0);
		
		if (!$handler->insert($imgset)) {
			$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_IMAGESET, $tplset->get('tplset_name')));
			return false;
		}
		
		if (!$handler->linktplset($imgset->get('imgset_id'), $tplset->get('tplset_name'))) {
			$this->_addErrorMessage(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_LINKTPLSET);
			return false;
		}
		
		$handler =& xoops_gethandler('imagesetimg');
		for ($i = 0; $i < count($themeimages); $i++) {
			if (isset($themeimages[$i]['name']) && $themeimages[$i]['name'] != '') {
				$image =& $handler->create();
				$image->set('imgsetimg_file', $themeimages[$i]['name']);
				$image->set('imgsetimg_imgset', $imgset->get('imgset_id'));
				$image->set('imgsetimg_body', $themeimages[$i]['content'], true);
				if (!$handler->insert($image)) {
					$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACYRENDER_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $image->get('imgsetimg_file')));
				}
				unset($image);
			}
		}
		
		return true;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("tplset_upload.html");
		$render->setAttribute('actionForm', $this->mActionForm);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=TplsetList");
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		if (count($this->mErrorMessages) == 0) {
			$controller->executeRedirect("./index.php?action=TplsetList", 1, _AD_LEGACYRENDER_ERROR_DBUPDATE_FAILED);
		}
		else {
			$render->setTemplateName("tplset_upload_error.html");
			$render->setAttribute('errorMessages', $this->mErrorMessages);
		}
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=TplsetList");
	}
}

?>
