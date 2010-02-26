<?php
/**
 *
 * @package Legacy
 * @version $Id: Module.class.php,v 1.3 2008/09/25 15:11:28 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_Module extends Legacy_ModuleAdapter
{
	function Legacy_Module(&$xoopsModule)
	{
		parent::Legacy_ModuleAdapter($xoopsModule);
		$this->mGetAdminMenu =new XCube_Delegate();
		$this->mGetAdminMenu->register('Legacy_Module.getAdminMenu');
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
