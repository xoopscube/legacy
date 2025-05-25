<?php
/**
 * Bannerstats - Module for XCL
 * Block functions for Bannerstats module.
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

/**
 * Shows the client-specific menu block.
 * Displays different links based on banner client authentication status.
 */
function b_bannerstats_client_menu_show($options)
{
    // Ensure session is started if not already (important for standalone block rendering)
    if (session_status() == PHP_SESSION_NONE) {
        @session_start();
    }

    // It's crucial that BannerClientSession.class.php can be found.
    // XOOPS_MODULE_PATH might not be defined if the block is rendered outside the module's direct context.
    // Using XOOPS_ROOT_PATH to build a reliable path.
    $sessionClassPath = XOOPS_ROOT_PATH . '/modules/bannerstats/class/BannerClientSession.class.php';
    if (file_exists($sessionClassPath)) {
        require_once $sessionClassPath;
    } else {
        // Log error and return empty block or error message if class can't be loaded
        error_log("Bannerstats Block Error: BannerClientSession.class.php not found at " . $sessionClassPath);
        return ['title' => 'Client Menu Error', 'content' => 'Session class missing.'];
    }

    $moduleDirname = 'bannerstats'; // Hardcode or retrieve more robustly if needed

    $block = [];
    // Language constants should be defined in bannerstats/language/english/blocks.php (and other languages)
    $block['title'] = defined('_MB_BANNERSTATS_CLIENT_MENU_TITLE') ? _MB_BANNERSTATS_CLIENT_MENU_TITLE : "Client Area";
    $menu_items = [];

    if (class_exists('BannerClientSession') && BannerClientSession::isAuthenticated()) {
        $menu_items[] = [
            'link' => XOOPS_URL . "/modules/{$moduleDirname}/index.php?action=Stats",
            'title' => defined('_MB_BANNERSTATS_VIEW_STATS') ? _MB_BANNERSTATS_VIEW_STATS : "View My Stats"
        ];
        // You can add more links here for authenticated clients
        // For example, if ChangeUrlAction is accessible to clients:
        // $menu_items[] = [
        //     'link' => XOOPS_URL . "/modules/{$moduleDirname}/index.php?action=ChangeUrl",
        //     'title' => defined('_MB_BANNERSTATS_CHANGE_URL') ? _MB_BANNERSTATS_CHANGE_URL : "Manage Banner URLs"
        // ];
        $menu_items[] = [
            'link' => XOOPS_URL . "/modules/{$moduleDirname}/index.php?action=RequestSupport",
            'title' => defined('_MB_BANNERSTATS_REQUEST_SUPPORT') ? _MB_BANNERSTATS_REQUEST_SUPPORT : "Request Support"
        ];
        $menu_items[] = [
            'link' => XOOPS_URL . "/modules/{$moduleDirname}/index.php?action=Logout",
            'title' => defined('_MB_BANNERSTATS_LOGOUT') ? _MB_BANNERSTATS_LOGOUT : "Logout"
        ];
        
        $clientLogin = BannerClientSession::getClientLogin();
        if ($clientLogin) {
             $block['welcome_message'] = sprintf(
                (defined('_MB_BANNERSTATS_WELCOME') ? _MB_BANNERSTATS_WELCOME : "Welcome, %s"),
                htmlspecialchars($clientLogin, ENT_QUOTES)
            );
        }

    } else {
        $menu_items[] = [
            'link' => XOOPS_URL . "/modules/{$moduleDirname}/index.php?action=Login",
            'title' => defined('_MB_BANNERSTATS_CLIENT_LOGIN') ? _MB_BANNERSTATS_CLIENT_LOGIN : "Client Login"
        ];
    }
    $block['menu_items'] = $menu_items;
    return $block;
}

// Placeholder for the banner display block function if it's in this file
// You might have this in a separate banner.php in the blocks directory
if (!function_exists('b_bannerstats_banner_show')) {
    function b_bannerstats_banner_show($options) {
        // $options[0] = cid, $options[1] = campaign_id (example)
        // This block would call Bannerstats_DelegateManager::getBannerHtmlForDisplay
        // or use the new <{banner}> Smarty function if the block template is Smarty.
        // For direct PHP block:
        $block = [];
        $delegateManagerPath = XOOPS_ROOT_PATH . '/modules/bannerstats/kernel/DelegateManager.class.php';
        if (file_exists($delegateManagerPath)) {
            require_once $delegateManagerPath;
            if (class_exists('Bannerstats_DelegateManager')) {
                $params = [];
                if (isset($options[0]) && (int)$options[0] > 0) {
                    $params['cid'] = (int)$options[0];
                }
                // Add campaign_id to params if $options[1] is set and used
                $block['content'] = Bannerstats_DelegateManager::getBannerHtmlForDisplay($params);
            } else {
                $block['content'] = '<!-- Bannerstats Error: DelegateManager class not found -->';
            }
        } else {
            $block['content'] = '<!-- Bannerstats Error: DelegateManager file not found -->';
        }
        return $block;
    }
}

if (!function_exists('b_bannerstats_banner_edit')) {
    function b_bannerstats_banner_edit($options) {
        // Form for editing block options (e.g., client ID, campaign ID)
        $form = "Client ID: <input type='text' name='options[0]' value='" . (int)($options[0] ?? 0) . "' /><br />";
        // $form .= "Campaign ID: <input type='text' name='options[1]' value='" . (int)($options[1] ?? 0) . "' />";
        return $form;
    }
}

