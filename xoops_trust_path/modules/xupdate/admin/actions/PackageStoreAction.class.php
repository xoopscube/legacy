<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/class/AbstractStoreAction.class.php';

class Xupdate_Admin_PackageStoreAction extends Xupdate_AbstractStoreAction
{
	public function __construct()
	{
		$this->contents = 'package';
		$this->action = 'PackageStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_PACKAGE;
		$this->template = 'module';
		$this->handlerName = 'ModuleStore';
		parent::__construct();
	}
}