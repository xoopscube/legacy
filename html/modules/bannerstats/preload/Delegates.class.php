<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_Delegates extends XCube_ActionFilter
{
    public function preBlockFilter() // Or postFilter, when XCube_Root is fully ready
    {
        $delegateManager = $this->mRoot->getDelegateManager();
        if ($delegateManager) {
            // For html/banners.php
            $delegateManager->add(
                'Legacypage.Banners.Access',
                'Bannerstats_DelegateManager::handleBannersAccess',
                XOOPS_MODULE_PATH . '/bannerstats/kernel/DelegateManager.class.php'
            );

            // For xoops_getbanner() in include/functions.php
            $delegateManager->add(
                'Legacy.Function.GetBannerHtml',
                'Bannerstats_DelegateManager::provideBannerHtml',
                XOOPS_MODULE_PATH . '/bannerstats/kernel/DelegateManager.class.php'
            );
        } else {
            error_log("Bannerstats Preload: DelegateManager not available.");
        }
    }
}
