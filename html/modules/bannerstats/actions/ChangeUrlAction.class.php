<?php
// html/modules/bannerstats/actions/ChangeUrlAction.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    die('XOOPS root path not defined');
}

class Bannerstats_ChangeUrlAction
{
    private $moduleDirname;
    private $xoopsTpl;
    private $xoopsDB;
    private $bannerHandler; // XOOPS banner handler

    public function __construct()
    {
        global $xoopsTpl, $xoopsDB;
        $this->xoopsTpl = $xoopsTpl;
        $this->xoopsDB = $xoopsDB;
        $this->moduleDirname = basename(dirname(dirname(__FILE__))); // 'bannerstats'

        $this->bannerHandler = xoops_gethandler('banner');

        // Assign module directory name to template for constructing URLs if needed
        if (is_object($this->xoopsTpl)) {
            $this->xoopsTpl->assign('module_dirname', $this->moduleDirname);
            $this->xoopsTpl->assign('action_url', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl");
            $this->xoopsTpl->assign('stats_url', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Stats");
            $this->xoopsTpl->assign('change_url_base', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl");
        }
    }

    public function getPageTitle()
    {
        return "Change Banner URL";
    }

    /**
     * Displays the form to change banner URL or a list of banners.
     */
    public function getDefaultView()
    {
        if (!$this->bannerHandler) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'System error: Banner management system is unavailable. Please contact admin.');
            error_log('Bannerstats: ChangeUrlAction::getDefaultView - Banner handler not available.');
            return 'bannerstats_error.html';
        }

        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            exit();
        }

        $clientId = BannerClientSession::getClientId();
        if (!$clientId) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Client ID not found in session.');
            return 'bannerstats_error.html';
        }

        $bid_req = isset($_REQUEST['bid']) ? (int)$_REQUEST['bid'] : 0; // Use $_REQUEST to catch GET from redirect or direct link
        $banner_to_edit = null;
        $client_banners = [];

        $criteria = new CriteriaCompo(new Criteria('cid', $clientId));
        $banners_obj_array = $this->bannerHandler->getObjects($criteria, true); // Get as array bid => object

        if (empty($banners_obj_array)) {
            $this->xoopsTpl->assign('bannerstats_message', 'You do not have any banners to manage.');
            return 'bannerstats_message.html';
        }

        foreach ($banners_obj_array as $banner_id => $banner_obj) {
            $banner_name_display = $banner_obj->getVar('imageurl', 'n');
            if (empty($banner_name_display)) {
                 // Fallback if imageurl is empty, use bid.
                 // You might prefer a different field if available, e.g. a custom 'name' or 'description' field.
                $banner_name_display = "Banner ID: " . $banner_id;
            }

            $client_banners[] = [
                'id' => $banner_id,
                'name' => $banner_name_display,
                'clickurl' => $banner_obj->getVar('clickurl', 'n'),
            ];
        }
        
        if ($bid_req && isset($banners_obj_array[$bid_req])) {
             $banner_obj_edit = $banners_obj_array[$bid_req];
             $banner_name_edit_display = $banner_obj_edit->getVar('imageurl', 'n');
             if (empty($banner_name_edit_display)) {
                $banner_name_edit_display = "Banner ID: " . $bid_req;
             }
             $banner_to_edit = [
                'id' => $bid_req,
                'name' => $banner_name_edit_display,
                'clickurl' => $banner_obj_edit->getVar('clickurl', 'n'),
             ];

             // Check if the banner is HTML
             if ($banner_obj_edit->isHtml()) { // isHtml() is a method of XoopsBanner
                 $this->xoopsTpl->assign('is_html_banner', true);
                 $this->xoopsTpl->assign('html_banner_notice', _MD_BANNERSTATS_HTML_BANNER_NOTICE);
             } else {
                 $this->xoopsTpl->assign('is_html_banner', false);
             }
             $this->xoopsTpl->assign('banner_to_edit', $banner_to_edit);
        } else {
            $this->xoopsTpl->assign('client_banners', $client_banners);
        }

        // CSRF Token
        // Ensure BannerClientToken class is loaded
        if (class_exists('BannerClientToken')) {
            $token = BannerClientToken::create('change_url_form');
            $this->xoopsTpl->assign('csrf_token', $token);
        } else {
            // Handle missing BannerClientToken class - perhaps log an error or disable CSRF for now (not recommended)
            error_log("Bannerstats Error: BannerClientToken class not found. CSRF protection is non-functional for ChangeUrlAction.");
            $this->xoopsTpl->assign('bannerstats_error_message', 'Security token system is unavailable. Please contact admin.');
            // Potentially return an error template or disable form submission
        }
        
        // Pass submitted new_url back to template if it was set (e.g. after validation error)
        if (isset($_SESSION['bannerstats_submitted_new_url'])) {
            $this->xoopsTpl->assign('submitted_new_url', htmlspecialchars($_SESSION['bannerstats_submitted_new_url'], ENT_QUOTES));
            unset($_SESSION['bannerstats_submitted_new_url']);
        }


        return 'bannerstats_change_url_form.html';
    }

    /**
     * Handles the form submission for changing the banner URL.
     */
    public function execute()
    {
        if (!$this->bannerHandler) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'System error: Banner management system is unavailable. Please contact admin.');
            error_log('Bannerstats: ChangeUrlAction::execute - Banner handler not available.');
            return 'bannerstats_error.html';
        }

        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            exit();
        }

        // CSRF Token Validation
        if (!class_exists('BannerClientToken') || !BannerClientToken::validateToken($_POST['csrf_token'] ?? '', 'change_url_form')) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Invalid security token. Please try again.');
            error_log("ChangeUrlAction: CSRF token validation failed.");
            // To repopulate the form correctly, we might need to pass back the bid
            $_REQUEST['bid'] = isset($_POST['bid']) ? (int)$_POST['bid'] : 0;
            return $this->getDefaultView();
        }

        $clientId = BannerClientSession::getClientId();
        if (!$clientId) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Client ID not found in session.');
            return 'bannerstats_error.html';
        }

        $bid = isset($_POST['bid']) ? (int)$_POST['bid'] : 0;
        $new_url = isset($_POST['new_url']) ? trim($_POST['new_url']) : '';

        if (!$bid) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Banner ID is missing.');
            return $this->getDefaultView();
        }
        
        // Store submitted URL in session in case of validation error to repopulate
        $_SESSION['bannerstats_submitted_new_url'] = $new_url;


        if (empty($new_url) || !filter_var($new_url, FILTER_VALIDATE_URL)) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Please enter a valid URL.');
            $_REQUEST['bid'] = $bid; // Ensure getDefaultView loads the correct banner
            return $this->getDefaultView();
        }

        $banner = $this->bannerHandler->get($bid);

        if (!$banner || $banner->getVar('cid') != $clientId) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Banner not found or you do not have permission to edit it.');
            error_log("ChangeUrlAction: Attempt to edit banner {$bid} by client {$clientId} failed ownership check.");
            return 'bannerstats_error.html';
        }

        $banner->setVar('clickurl', $new_url);
        if ($this->bannerHandler->insert($banner)) {
            unset($_SESSION['bannerstats_submitted_new_url']); // Clear submitted URL on success
            $redirectUrl = XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl&bid=" . $bid . "&status=success";
            header("Location: " . $redirectUrl);
            exit();
        } else {
            $errors = $this->bannerHandler->getErrors();
            $this->xoopsTpl->assign('bannerstats_error_message', 'Failed to update banner URL. Please try again. ' . implode(', ', $errors));
            error_log("ChangeUrlAction: Failed to update banner {$bid}. DB Error: " . implode(', ', $errors));
            $_REQUEST['bid'] = $bid; // To repopulate form
            return $this->getDefaultView();
        }
    }
}
?>
