<?php
/**
* @file
* @package xupdate
* @version $Id$
**/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XUPDATE_TRUST_PATH . '/class/AbstractStoreAction.class.php';

class Xupdate_Admin_PreloadStoreAction extends Xupdate_AbstractStoreAction
{
	public function __construct()
	{
		$this->contents = 'preload';
		$this->action = 'PreloadStore';
		$this->currentMenu = _MI_XUPDATE_ADMENU_PRELOAD;
		parent::__construct();
	}
} // end class
?>