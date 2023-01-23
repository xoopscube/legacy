<?php
/**
 * Preload  : AdminDashboard
 * Version  : 2.3.2
 * Package  : XCL
 * Module   : Legacy
 * Author   : Nuno Luciano aka Gigamaster
 * Credits  : Original AdminSystemCheckPlus Preload
 *            by Wanikoo ( https://www.wanisys.net/ )
*/
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
/**
 * AdminDashboard is used to manage Control Panel blocks,
 * sort of raw option having logical equivalence to block management.
 * Usage :
 * 0 (disable), 1 (enable) dashboard template and block render.
 * Copy the Smarty code into admin_theme.html to render the block
 * @TODO dashboard UI options >> module preferences, admin preferences storage
 */


/**
 * Render Dashboard Template
 * Admin / Template / dashboard.html
 * @return bool Disable (0) or enable (1)
 */
if (!defined('XC_ADMINDASHBOARD_INFO')) {
    define('XC_ADMINDASHBOARD_INFO', 1);
}

/**
 * Render Admin Blocks
 * Set blocks visible in Control Panel.
 * Copy the Smarty coe to admin_theme.html e.g.:
 * <{$xoops_lblocks.action_search.title}>
 * <{$xoops_lblocks.action_search.content}>
 * @return bool Disable (0) or enable (1)
 */

// Smarty : <{$xoops_lblocks.action_search.content}>
if (!defined('XC_ADMIN_BLOCK_ACTIONSEARCH')) {
    define('XC_ADMIN_BLOCK_ACTIONSEARCH', 0);
}
// Smarty : <{$xoops_lblocks.block_comments.content}>
if (!defined('XC_ADMIN_BLOCK_COMMENTS')) {
    define('XC_ADMIN_BLOCK_COMMENTS', 0);
}
// Smarty : <{$xoops_lblocks.block_loginfo.content}>
if(!defined('XC_ADMIN_BLOCK_LOGINFO')) {
    define('XC_ADMIN_BLOCK_LOGINFO', 0);
}
// Smarty : <{$xoops_lblocks.block_menu.content}>
if (!defined('XC_ADMIN_BLOCK_MENU')) {
    define('XC_ADMIN_BLOCK_MENU', 0);
}
// Smarty : <{$xoops_lblocks.block_online.content}>
if (!defined('XC_ADMIN_BLOCK_ONLINE')) {
    define('XC_ADMIN_BLOCK_ONLINE', 1);
}
// Smarty : <{$xoops_lblocks.block_online.content}>
if (!defined('XC_ADMIN_BLOCK_OVERVIEW')) {
    define('XC_ADMIN_BLOCK_OVERVIEW', 1);
}
// Smarty : <{$xoops_lblocks.block_php.content}>
if (!defined('XC_ADMIN_BLOCK_PHP')) {
    define('XC_ADMIN_BLOCK_PHP', 1);
}
// Smarty : <{$xoops_lblocks.block_server.content}>
if (!defined('XC_ADMIN_BLOCK_SERVER')) {
    define('XC_ADMIN_BLOCK_SERVER', 0);
}
// Smarty : <{$xoops_lblocks.block_system.content}>
if (!defined('XC_ADMIN_BLOCK_SYSTEM')) {
    define('XC_ADMIN_BLOCK_SYSTEM', 0);
}
// Smarty : <{$xoops_lblocks.block_waiting.content}>
if (!defined('XC_ADMIN_BLOCK_WAITING')) {
    define('XC_ADMIN_BLOCK_WAITING', 0);
}
/**  TODO - Display on sidemenu the admin block "admin-theme"
if (!defined('XC_ADMINBLOCK_ADMINTHEME')) {
    define('XC_ADMINBLOCK_ADMINTHEME', 0);
}
*/
/**
 * Environment Raw Information
 *
 * PHP info wrapped in custom table
*/
if (!defined('XC_ADMINDASHBOARD_PHPINFO')) {
    define('XC_ADMINDASHBOARD_PHPINFO', 0);
}


class Legacy_AdminDashboard extends XCube_ActionFilter
{
    public function preBlockFilter()
    {

        $root=&XCube_Root::getSingleton();

        // XCUBE_DELEGATE_PRIORITY_2
        // XCUBE_DELEGATE_PRIORITY_NORMAL+1
        $root->mDelegateManager->add("Legacypage.Admin.SystemCheck", "Legacy_AdminDashboard::AdminDashboardSystem", XCUBE_DELEGATE_PRIORITY_1);

        /**
         * Note
         * Strategy Setup for Admin Block of AdminRenderSystem
         * Ref. Legacy/kernel/Legacy_AdminControllerStrategy
        */
        if ($root->mController->_mStrategy && get_class($root->mController->_mStrategy) === 'Legacy_AdminControllerStrategy') {
            //$this->mController->_mStrategy->mSetupBlock->add( [$this, 'AdminSetupBlock'], XCUBE_DELEGATE_PRIORITY_NORMAL );
            $this->mController->_mStrategy->mSetupBlock->add( [$this, 'AdminSetupBlock'] );
        }

    }


    public static function AdminDashboardSystem()
    {

        $root =& XCube_Root::getSingleton();

        if(XC_ADMINDASHBOARD_INFO) {
            /*
            * Select the Layout Display Option
            * 0 - output is direct
            * 1 - output with template "legacy_dummy.html"
            * 2 - output with template "dashboard.html"
            */
            $uitype = 2;

            // 0 -Customize the dashboard of html/admin.php
            if ( $uitype == 0 ) {
                $uiadmin = '<b>Welcome to XOOPSCube Admin!</b><br>Have a nice time!';
                echo $uiadmin;
            }

            // 1 - Customize the dashboard of html/admin.php with template "legacy_dummy.html"
            elseif ( $uitype == 1 ) {
                $uiadminhtml = '<b>Welcome to XOOPSCube !</b>
                                <br>Have a nice and happy time!
                                <br><b>Output with template "legacy_dummy.html"</b>!';
                $attributes = [];
                $attributes['dummy_content'] = $uiadminhtml;
                $template = XOOPS_LEGACY_PATH. '/templates/legacy_dummy.html';
                self::display_message($attributes, $template, $return = false);
            }

            // 2 - Customize the dashboard with template "dashboard.html"
            elseif ( $uitype == 2 ) {

                if ( file_exists(XOOPS_LEGACY_PATH . '/admin/templates/dashboard.html') ) {

                    $title1  = 'XCL Documentation';
                    $content1= 'XCL provides out-of-the-box a search function to find admin features.'
                               . 'And documentation is available from menu Help for each module.';
                    /**
                     * Smarty Render
                     * Usage in template 'dashboard.html'' : <{$title}> and <{$content}>
                     */
                    $attributes = [];
                    $attributes['title'] = $title1;
                    $attributes['content'] = $content1;

                    $template = XOOPS_LEGACY_PATH. '/admin/templates/dashboard.html';

                    self::display_message($attributes, $template, $return = false);

                }
            }

        }


        /*
        * Admin Dashboard PHP Information
        */
        if (XC_ADMINDASHBOARD_PHPINFO) {

            ob_start();
            phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
            $phpinfo = ob_get_clean();

            preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
            $output = preg_replace('#<table#', '<table class="outer""', $output[1][0]);
            $output = preg_replace('#(\w),(\w)#', '\1, \2', $output);
            $output = preg_replace('#<h2([^>]*)>(.*)</h2>#m','<h3$1>$2</h3>', $output);
            $output = preg_replace('#<h1([^>]*)>(.*)</h1>#m','<h2$1>$2</h2>', $output);
            $output = preg_replace('#border="0" width="600"#', '', $output);
            $output = preg_replace('#<hr>#', '', $output);
            $output = preg_replace('#class="e"#', 'class="even"', $output);
            $output = preg_replace('#class="h"#', 'class="odd"', $output);
            $output = preg_replace('#class="v"#', 'class="even"', $output);
            $output = preg_replace('#class="p"#', 'class="odd"', $output);
            $output = str_replace(['<div class="center">', '</div>'], '', $output);

            $attributes = [];
            $attributes['dummy_content'] = $output;

            $template = XOOPS_LEGACY_PATH. '/templates/legacy_dummy.html';

            self::display_message($attributes, $template, $return = false);
        }

    }

    // If you want to add any new block, please customize this function !
    // Refer to legacy/admin/blocks!
    public function AdminSetupBlock() {

        if ( XC_ADMIN_BLOCK_ACTIONSEARCH) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminActionSearch.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminActionSearch();
        }

        if (XC_ADMIN_BLOCK_COMMENTS) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockComments.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockComments();
        }

        if(XC_ADMIN_BLOCK_LOGINFO && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockLogInfo.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockLogInfo.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockLogInfo();
        }

        if (XC_ADMIN_BLOCK_MENU && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockMenu.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockMenu.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockMenu();
        }

        if (XC_ADMIN_BLOCK_ONLINE && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockOnline.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockOnline.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockOnline();
        }
        if (XC_ADMIN_BLOCK_OVERVIEW) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockOverview.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockOverview();
        }
        if (XC_ADMIN_BLOCK_PHP) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockPhp.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockPhp();
        }

        if (XC_ADMIN_BLOCK_SERVER) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockServer.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockServer();
        }

        if (XC_ADMIN_BLOCK_SYSTEM) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockSystem.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockSystem();
        }

        if (XC_ADMIN_BLOCK_WAITING) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminBlockWaiting.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminBlockWaiting();
        }

        //Block admin-theme changer
/*        if(XC_ADMINBLOCK_ADMINTHEME && file_exists(XOOPS_LEGACY_PATH . '/admin/blocks/AdminThemeSelect.class.php') && file_exists(XOOPS_LEGACY_PATH . '/admin/preload/AdminThemeSelectPreload.class.php')) {
            require_once XOOPS_LEGACY_PATH . '/admin/blocks/AdminThemeSelect.class.php';
            $this->mController->_mBlockChain[] = new Legacy_AdminThemeSelect();
        }*/

        // Add your custom block here!

    }


    /**
     * Admin Dashboard Display Message
     * @param array $attributes
     * @param string $template
     * @param bool $return
     * @return mixed
     */
    public static function display_message(array $attributes = [], string $template= '', bool $return)
    {
        $root =& XCube_Root::getSingleton();

        $renderSystem =& $root->getRenderSystem($root->mContext->mBaseRenderSystemName);

        //$renderSystem =& $root->getRenderSystemName($root->mContext->mBaseRenderSystemName);

        // Module's templates
        $renderTarget =& $renderSystem->createRenderTarget('main');

        // Module's templates blocks
        //$renderTarget =& $renderSystem->createRenderTarget(LEGACY_RENDER_TARGET_TYPE_BLOCK);

        // Module
        $renderTarget->setAttribute('legacy_module', 'legacy');

        $renderTarget->setTemplateName($template);

        foreach (array_keys($attributes) as $attribute) {
            $renderTarget->setAttribute($attribute, $attributes[$attribute]);
        }

        $renderSystem->render($renderTarget);
        if ($return == true) {
            return $renderTarget->getResult();
        }

        print $renderTarget->getResult();
    }

    /**
     * Admin Dashboard Get Template
     * @param $file
     * @param null $prefix
     * @return string
     */
    private static function getTemplate($file, $prefix = null)
    {
        $infoArr = Legacy_get_override_file($file, $prefix);
        if ($prefix) {
            $file = $prefix . $file;
        }

        if ($infoArr['theme'] !== null && $infoArr['dirname'] !== null) {
            //return XOOPS_THEME_PATH . '/' . $infoArr['theme'] . '/templates/' . $infoArr['dirname'] . '/' . $file;
    return XOOPS_THEME_PATH . '/' . $infoArr['theme'] . '/modules/' . $infoArr['dirname'] . '/' . $file;
        
        }

        if ($infoArr['theme'] !== null) {
            return XOOPS_THEME_PATH . '/' . $infoArr['theme'] . '/' . $file;
        }

//        if ($infoArr['dirname'] !== null) {
//            return XOOPS_MODULE_PATH . '/' . $infoArr['dirname'] . '/templates/blocks/' . $file;
//        }

        if ($infoArr['dirname'] !== null) {
            return XOOPS_MODULE_PATH . '/' . $infoArr['dirname'] . '/admin/templates/' . $file;
        }

        return LEGACY_ADMIN_RENDER_FALLBACK_PATH . '/' . $file;
    }
}


