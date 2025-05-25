<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_Delegates extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add(
                'Legacypage.Banners.Access',
                'Bannerstats_DelegateManager::handleBannersAccess',
                XOOPS_MODULE_PATH . '/bannerstats/kernel/DelegateManager.class.php'
            );

        $this->mRoot->mDelegateManager->add(
                'Legacy.Function.GetBannerHtml',
                'Bannerstats_DelegateManager::provideBannerHtml',
                XOOPS_MODULE_PATH . '/bannerstats/kernel/DelegateManager.class.php'
            );
    }
}
