<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
	exit;
}

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';

/**
 * Xupdate_Admin_IndexAction
**/
class Xupdate_Admin_InstallCheckerAction extends Xupdate_AbstractAction
{
	/**
	 * getDefaultView
	 * 
	 * @param	void
	 * 
	 * @return	Enum
	**/
	public function getDefaultView()
	{
		if (! $this->mod_config['_FtpLoginCheck'] || ! $this->_removeInstallDir()) {
			$url = XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $this->mRoot->mContext->mModule->mXoopsModule->get('mid');
			redirect_header($url, 10, _MD_XUPDATE_MESSAGE_INSTALL_COMPLETE_WARNING);
		} else {
			$url = XOOPS_URL . '/index.php';
			header('Location: ' . $url);
		}
		exit();
	}
}

?>