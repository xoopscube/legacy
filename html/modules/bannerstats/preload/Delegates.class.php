<?php
// html/modules/bannerstats/preload/Delegates.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_Delegates extends XCube_ActionFilter
{
    public function preBlockFilter() // Or postFilter, depending on when XCube_Root is fully ready
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
?>
