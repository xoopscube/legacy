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

class Bannerstats_DelegateManager
{
    /**
     * Delegate for Legacypage.Banners.Access
     * Takes over when html/banners.php is accessed.
     */
    public static function handleBannersAccess()
    {
        $root = XCube_Root::getSingleton();
        $moduleDirname = 'bannerstats';

        // Ensure session is started for BannerClientSession
        if (session_status() == PHP_SESSION_NONE) {
            @session_start();
        }
        // It's good practice to ensure class is loaded if not using an autoloader fully
        $sessionClassPath = XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/BannerClientSession.class.php';
        if (file_exists($sessionClassPath)) {
            require_once $sessionClassPath;
        } else {
            // Log error, can't proceed with session check
            error_log("Bannerstats: BannerClientSession.class.php not found in handleBannersAccess delegate.");
            // Fallback or simple error message if redirect isn't safe
            echo "Bannerstats module session handler is missing. Please contact admin.";
            exit();
        }


        if (class_exists('BannerClientSession') && BannerClientSession::isAuthenticated()) {
            $redirect_url = XOOPS_URL . '/modules/' . $moduleDirname . '/index.php?action=Stats';
        } else {
            $redirect_url = XOOPS_URL . '/modules/' . $moduleDirname . '/index.php?action=Login';
        }

        $root->mController->executeRedirect($redirect_url, 0, ''); // 0 seconds delay
        exit(); // Crucial: Stop further execution of the original banners.php
    }

    /**
     * Delegate for Legacy.Function.GetBannerHtml
     * Provides the HTML for a banner to be displayed by themes/blocks using xoops_getbanner().
     *
     * @param string &$bannerHtml Reference to the string that will hold the banner HTML.
     */
    public static function provideBannerHtml(string &$bannerHtml)
    {
        // This is where the logic for selecting and rendering ONE banner would go.
        // It should be a simplified version of what modern_display_banner() was intended to do.
        // For this to be truly "simple" as per your request, this function needs
        // to be carefully designed.

        // Option A: Use a dedicated helper in bannerstats (Recommended for complexity)
        $helperPath = XOOPS_MODULE_PATH . '/bannerstats/class/BannerDisplayHelper.class.php'; // New helper
        if (file_exists($helperPath)) {
            require_once $helperPath;
            if (class_exists('Bannerstats_BannerDisplayHelper') && method_exists('Bannerstats_BannerDisplayHelper', 'getRandomBannerHtml')) {
                $bannerHtml = Bannerstats_BannerDisplayHelper::getRandomBannerHtml();
                return;
            }
        }

        // Option B: Minimalist implementation (if no helper yet)
        // This is a placeholder. A real implementation would query the DB for an active banner.
        // error_log("Bannerstats: provideBannerHtml delegate called. Implement actual banner fetching logic.");
        // $bannerHtml = "<!-- Banner from Bannerstats (Logic to be implemented) -->";

        // For now, let's assume the helper exists and does its job.
        // If the helper doesn't set $bannerHtml, it will remain empty, and xoops_getbanner will return its default comment.
    }
}
?>
