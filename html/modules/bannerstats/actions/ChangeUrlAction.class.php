<?php
/**
 * Bannerstats - Module for XCL
 * ChangeUrlAction.class.php
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

require_once dirname(__DIR__) . '/class/BannerClientSession.class.php';
require_once dirname(__DIR__) . '/class/BannerStatsManager.class.php';
require_once dirname(__DIR__) . '/class/BannerClientToken.class.php';

class Bannerstats_ChangeUrlAction
{
    private string $moduleDirname;
    private $xoopsTpl;
    private string $message = '';
    private bool $isError = false;
    private BannerStatsManager $statsManager;

    public function __construct()
    {
        global $xoopsTpl;
        $this->xoopsTpl = $xoopsTpl;
        $this->moduleDirname = basename(dirname(dirname(__FILE__))); // 'bannerstats'
        $this->statsManager = new BannerStatsManager();

        // Assign module directory name to template for constructing URLs if needed
        if (is_object($this->xoopsTpl)) {
            $this->xoopsTpl->assign('module_dirname', $this->moduleDirname);
            $this->xoopsTpl->assign('action_url', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl");
            $this->xoopsTpl->assign('stats_url', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Stats");
            $this->xoopsTpl->assign('change_url_base', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl");
        }
    }

    public function getPageTitle(): string
    {
        return defined('_MD_BANNERSTATS_CHANGE_URL_TITLE') ? _MD_BANNERSTATS_CHANGE_URL_TITLE : "Change Banner URL";
    }

    /**
     * Displays the form to change banner URL or a list of banners
     */
    public function getDefaultView(): string
    {
        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            exit();
        }

        $clientId = BannerClientSession::getClientId();
        if (!$clientId) {
            $this->message = 'Client ID not found in session.';
            $this->isError = true;
            $this->prepareMessageTemplate();
            return 'bannerstats_error.html';
        }

        $bid_req = isset($_REQUEST['bid']) ? (int)$_REQUEST['bid'] : 0; // Use $_REQUEST to catch GET from redirect or direct link
        $banner_to_edit = null;
        $client_banners = [];

        // Get active banners for this client
        $banners = $this->statsManager->getActiveBanners($clientId);

        if (empty($banners)) {
            $this->message = defined('_MD_BANNERSTATS_NO_BANNERS') ? _MD_BANNERSTATS_NO_BANNERS : 'You do not have any banners to manage.';
            $this->isError = false;
            $this->prepareMessageTemplate();
            return 'bannerstats_message.html';
        }

        foreach ($banners as $banner) {
            $banner_name_display = $banner['imageurl'] ?? '';
            if (empty($banner_name_display)) {
                // Fallback if imageurl is empty, use bid
                $banner_name_display = "Banner ID: " . $banner['bid'];
            }

            $client_banners[] = [
                'id' => $banner['bid'],
                'name' => $banner_name_display,
                'clickurl' => $banner['clickurl'] ?? '',
            ];
        }
        
        if ($bid_req && $this->findBannerById($banners, $bid_req, $banner_details)) {
            $banner_name_edit_display = $banner_details['imageurl'] ?? '';
            if (empty($banner_name_edit_display)) {
                $banner_name_edit_display = "Banner ID: " . $bid_req;
            }
            $banner_to_edit = [
                'id' => $bid_req,
                'name' => $banner_name_edit_display,
                'clickurl' => $banner_details['clickurl'] ?? '',
            ];

            // Check if the banner is in_array($bannerType, ['html', 'ad_tag', 'video'], true);
            $isHtmlBanner = ($banner_details['banner_type'] ?? '') === 'html';
            $this->xoopsTpl->assign('is_html_banner', $isHtmlBanner);
            if ($isHtmlBanner) {
                $this->xoopsTpl->assign('html_banner_notice', _MD_BANNERSTATS_ERR_BANNER_ADTAG_LINK);
            }
            $this->xoopsTpl->assign('banner_to_edit', $banner_to_edit);
        } else {
            $this->xoopsTpl->assign('client_banners', $client_banners);
        }

        // CSRF Token
        $token = BannerClientToken::create('change_url_form');
        $this->xoopsTpl->assign('csrf_token', $token);
        
        // Pass submitted new_url back to template if it was set (e.g. after validation error)
        if (isset($_SESSION['bannerstats_submitted_new_url'])) {
            $this->xoopsTpl->assign('submitted_new_url', htmlspecialchars($_SESSION['bannerstats_submitted_new_url'], ENT_QUOTES));
            unset($_SESSION['bannerstats_submitted_new_url']);
        }

        // Check for status message from redirect
        if (isset($_GET['status']) && $_GET['status'] === 'success') {
            $this->xoopsTpl->assign('bannerstats_message', defined('_MD_BANNERSTATS_URL_UPDATED') ? 
                _MD_BANNERSTATS_URL_UPDATED : 'Banner URL has been successfully updated.');
        }

        return 'bannerstats_change_url.html';
    }

    /**
     * Handles the form submission for changing the banner URL.
     */
    public function execute(): string
    {
        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            exit();
        }

        // CSRF Token Validation
        if (!BannerClientToken::validate($_POST['csrf_token'] ?? '', 'change_url_form')) {
            $this->message = defined('_MD_BANNERSTATS_INVALID_TOKEN') ? _MD_BANNERSTATS_INVALID_TOKEN : 'Invalid security token. Please try again.';
            $this->isError = true;
            error_log("ChangeUrlAction: CSRF token validation failed.");
            // To repopulate the form correctly, we might need to pass back the bid
            $_REQUEST['bid'] = isset($_POST['bid']) ? (int)$_POST['bid'] : 0;
            return $this->getDefaultView();
        }

        $clientId = BannerClientSession::getClientId();
        if (!$clientId) {
            $this->message = 'Client ID not found in session.';
            $this->isError = true;
            $this->prepareMessageTemplate();
            return 'bannerstats_error.html';
        }

        $bid = isset($_POST['bid']) ? (int)$_POST['bid'] : 0;
        $new_url = isset($_POST['new_url']) ? trim($_POST['new_url']) : '';

        if (!$bid) {
            $this->message = defined('_MD_BANNERSTATS_MISSING_BID') ? _MD_BANNERSTATS_MISSING_BID : 'Banner ID is missing.';
            $this->isError = true;
            $this->prepareMessageTemplate();
            return $this->getDefaultView();
        }
        
        // Store submitted URL in session in case of validation error to repopulate
        $_SESSION['bannerstats_submitted_new_url'] = $new_url;

        if (empty($new_url) || !filter_var($new_url, FILTER_VALIDATE_URL)) {
            $this->message = defined('_MD_BANNERSTATS_INVALID_URL') ? _MD_BANNERSTATS_INVALID_URL : 'Please enter a valid URL.';
            $this->isError = true;
            $this->prepareMessageTemplate();
            $_REQUEST['bid'] = $bid; // Ensure getDefaultView loads the correct banner
            return $this->getDefaultView();
        }

        // Use BannerStatsManager to update the URL
        if ($this->statsManager->updateBannerUrl($bid, $clientId, $new_url)) {
            unset($_SESSION['bannerstats_submitted_new_url']); // Clear submitted URL on success
            $redirectUrl = XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl&bid=" . $bid . "&status=success";
            header("Location: " . $redirectUrl);
            exit();
        } else {
            $this->message = defined('_MD_BANNERSTATS_URL_UPDATE_FAILED') ? 
                _MD_BANNERSTATS_URL_UPDATE_FAILED : 'Failed to update banner URL. Please try again.';
            $this->isError = true;
            error_log("ChangeUrlAction: Failed to update banner {$bid} for client {$clientId}.");
            $this->prepareMessageTemplate();
            $_REQUEST['bid'] = $bid; // To repopulate form
            return $this->getDefaultView();
        }
    }

    /**
     * Helper method to find a banner by ID in an array of banners
     * 
     * @param array $banners Array of banner data
     * @param int $bid Banner ID to find
     * @param array &$banner_details Reference to store the found banner details
     * @return bool True if banner found, false otherwise
     */
    private function findBannerById(array $banners, int $bid, &$banner_details): bool
    {
        foreach ($banners as $banner) {
            if ((int)$banner['bid'] === $bid) {
                $banner_details = $banner;
                return true;
            }
        }
        return false;
    }

    /**
     * Prepares the template variables for displaying messages
     */
    private function prepareMessageTemplate(): void
    {
        if (is_object($this->xoopsTpl)) {
            if ($this->isError) {
                $this->xoopsTpl->assign('bannerstats_error_message', $this->message);
            } else {
                $this->xoopsTpl->assign('bannerstats_message', $this->message);
            }
            $this->xoopsTpl->assign('bannerstats_continue_link', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Stats");
            $this->xoopsTpl->assign('bannerstats_continue_text', 'Return to Statistics');
        }
    }
}
