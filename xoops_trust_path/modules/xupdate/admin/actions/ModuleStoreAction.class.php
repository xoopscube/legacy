<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/class/AbstractStoreAction.class.php';

class Xupdate_Admin_ModuleStoreAction extends Xupdate_AbstractStoreAction
{
	public function __construct()
	{
		$this->contents = 'module';
		$this->action = 'ModuleStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_MODULE;
		parent::__construct();
	}
} // end class
?>