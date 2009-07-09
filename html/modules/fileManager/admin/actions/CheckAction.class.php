<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH. '/fileManager/class/AbstractIndexAction.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/include/functions.php';

class FileManager_CheckAction extends FileManager_AbstractIndexAction
{
	var $confirmMssage   = null;

	function _checkFile($checkFile)
	{
		if (!file_exists($checkFile)) {
			return sprintf(_AD_FILEMANAGER_ERROR_FILE_EXISTS , $checkFile).'<br />';
		}
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		// flowplayer
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/flowplayer/');
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/flowplayer/flowplayer-3.1.1.swf');
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/flowplayer/flowplayer-3.1.1.min.js');

		// SWFUpload
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/' );
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/xupload.php' );
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/swfupload.swf');
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/swfupload.js');
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/css/xupload.css');
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/images/cancelbutton.gif');
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/images/XPButtonUploadText_61x22.png');
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/js/swfupload.swfobject.js' );
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/js/swfupload.queue.js' );
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/js/fileprogress.js' );
		$this->confirmMssage .= $this->_checkFile(XOOPS_ROOT_PATH . '/common/SWFUpload/js/handlers.js' );

		return CONTENTS_FRAME_VIEW_INDEX;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		$breadCrumbs[] = array('name' => _AD_FILEMANAGER_CHECK) ;
		$render->setTemplateName('fileManager_check.html');
		$render->setAttribute('module_info'   , getModuleInfo());
		$render->setAttribute('ip_address'    , getenv("REMOTE_ADDR"));
		$render->setAttribute('bread_crumbs'  , $breadCrumbs);
		$render->setAttribute('confirm_mssage', $this->confirmMssage);
	}
}
?>