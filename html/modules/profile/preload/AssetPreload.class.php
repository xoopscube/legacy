<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class Profile_AssetPreload extends XCube_ActionFilter
{
	/**
	 * @public
	 */
	function preBlockFilter()
	{
		if (!$this->mRoot->mContext->hasAttribute('module.profile.HasSetAssetManager')) {
			$delegate =new XCube_Delegate();
			$delegate->register('Module.profile.Event.GetAssetManager');
			$delegate->add(array(&$this, 'getManager'));
			$this->mRoot->mContext->setAttribute('module.profile.HasSetAssetManager', true);
		}
	}

	/**
	 * @private
	 */
	function getManager(&$obj)
	{
		require_once XOOPS_MODULE_PATH . "/profile/class/AssetManager.class.php";
		$obj = Profile_AssetManager::getSingleton();
	}
}

?>
