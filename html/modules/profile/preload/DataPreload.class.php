<?php
/**
 * @file
 * @package xcat
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_DataPreload extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	function preBlockFilter()
	{
		$root =& XCube_Root::getSingleton();
	
		require_once XOOPS_MODULE_PATH . "/profile/service/ProfileService.class.php";
		$service =& new Profile_Service();
		$service->prepare();
	
		$this->mRoot->mServiceManager->addService('Profile_Service', $service);
	}
}

?>
