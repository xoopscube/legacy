<?php
/**
 * @package    profile
 * @version    2.3.1
 * @author     Nuno Luciano (aka gigamaster), 2020, XCL PHP7
 * @author     Kilica
 * @copyright  2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH.'/profile/class/FieldType.class.php';

class Profile_AssetPreload extends XCube_ActionFilter
{
    /**
     * @public
     */
    public function preBlockFilter()
    {
        if (!$this->mRoot->mContext->hasAttribute('module.profile.HasSetAssetManager')) {
            $delegate =new XCube_Delegate();
            $delegate->register('Module.profile.Event.GetAssetManager');
            $delegate->add([&$this, 'getManager']);
            $this->mRoot->mContext->setAttribute('module.profile.HasSetAssetManager', true);
        }
        $file = XOOPS_MODULE_PATH.'/profile/class/DelegateFunctions.class.php';
        $this->mRoot->mDelegateManager->add('Legacy_Profile.SaveProfile', 'Profile_Delegate::saveProfile', $file);
        $this->mRoot->mDelegateManager->add('Legacy_Profile.GetDefinition', 'Profile_Delegate::getDefinition', $file);
        $this->mRoot->mDelegateManager->add('Legacy_Profile.GetProfile', 'Profile_Delegate::getProfile', $file);
        $this->mRoot->mDelegateManager->add('Legacy_Profile.SetupActionForm', 'Profile_Delegate::setupActionForm', $file);
        $this->mRoot->mDelegateManager->add('Legacy_Profile.LoadActionForm', 'Profile_Delegate::loadActionForm', $file);
        $this->mRoot->mDelegateManager->add('Legacy.Event.UserDelete', 'Profile_AssetPreload::deleteProfile');
        $this->mRoot->mDelegateManager->add('Legacy.Admin.Event.UserDelete', 'Profile_AssetPreload::deleteProfile');
    }

    /**
     * @private
     * @param $obj
     */
    public function getManager(&$obj)
    {
        require_once XOOPS_MODULE_PATH . '/profile/class/AssetManager.class.php';
        $obj = Profile_AssetManager::getSingleton();
    }

    /**
     * @private
     * @param $user
     */
    public function deleteProfile(&$user)
    {
        $handler = Legacy_Utils::getModuleHandler('data', 'profile');
        $handler->deleteAll(new Criteria('uid', $user->get('uid')), true);
    }
}
