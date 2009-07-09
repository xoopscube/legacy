<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

if (!defined('XOOPS_ROOT_PATH')) exit();

class FileManager_Module extends Legacy_ModuleAdapter
{
	function FileManager_Module(&$xoopsModule)
	{
		parent::Legacy_ModuleAdapter($xoopsModule);
		$this->mGetAdminMenu =& new XCube_Delegate();
		$this->mGetAdminMenu->register('fileManager_Module.getAdminMenu');
	}

	function hasAdminIndex()
	{
		return true;
	}

	function getAdminIndex()
	{
		return XOOPS_MODULE_URL.'/'.$this->mXoopsModule->get('dirname').'/admin/index.php';
	}

	function getAdminMenu()
	{
		$menu = parent::getAdminMenu();
		$this->mGetAdminMenu->call(new XCube_Ref($menu));
		
		ksort($menu);
		
		return $menu;
	}

}
?>
