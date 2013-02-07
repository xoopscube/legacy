<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/class/AbstractStoreAction.class.php';

class Xupdate_Admin_ThemeStoreAction extends Xupdate_AbstractStoreAction
{
	public function __construct()
	{
		$this->contents = 'theme';
		$this->action = 'ThemeStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_THEME;
		parent::__construct();
	}
} // end class
?>