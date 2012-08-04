<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/admin/actions/ModuleStoreAction.class.php';

class Xupdate_Admin_PackageStoreAction extends Xupdate_Admin_ModuleStoreAction
{
	public function __construct()
	{
		parent::__construct();
	
		$this->contents = 'package';
		$this->action = 'PackageStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_PACKAGE;
	}
}