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

require_once XUPDATE_TRUST_PATH . '/class/AbstractInstallAction.class.php';

/**
 * Xupdate_Admin_StoreAction
 *
 * @property mixed downloadUrlFormat
 */
class Xupdate_Admin_ModuleInstallAction extends Xupdate_AbstractInstallAction
{
	public function __construct()
	{
		parent::__construct();
		$this->contents = 'module';
		$this->action = 'ModuleStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_MODULE;
	}
}
?>
