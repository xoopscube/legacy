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
class Xupdate_Admin_IndexAction extends Xupdate_AbstractAction
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
		return XUPDATE_FRAME_VIEW_SUCCESS;
	}

	/**
	 * executeViewSuccess
	 * 
	 * @param	XCube_RenderTarget	&$render
	 * 
	 * @return	void
	**/
	public function executeViewSuccess(&$render)
	{
		$render->setTemplateName('admin.html');
		$render->setAttribute('adminMenu', $this->mModule->getAdminMenu());
	}
}

?>