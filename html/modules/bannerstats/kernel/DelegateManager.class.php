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
        $moduleDirname = 'bannerstats'; // Consider making this dynamic if needed

        $request = $root->mContext->mRequest;
        $bid = $request->getRequest('bid') !== null ? (int)$request->getRequest('bid') : 0;
        $cid = $request->getRequest('cid') !== null ? (int)$request->getRequest('cid') : 0;

        if (session_status() == PHP_SESSION_NONE) {
            @session_start();
        }
        
        $sessionClassPath = XOOPS_MODULE_PATH . '/' . $moduleDirname . '/class/BannerClientSession.class.php';
        if (file_exists($sessionClassPath)) {
            require_once $sessionClassPath;
        } else {
            error_log("Bannerstats: BannerClientSession.class.php not found in handleBannersAccess delegate.");
            // It's generally better to let XOOPS handle error display or use a language constant
            echo "Bannerstats module session handler is missing. Please contact admin.";
            exit();
        }

        if ($bid > 0) {
            $clickUrl = self::_trackAndGetClickUrl($bid);
            if ($clickUrl) {
                $root->mController->executeRedirect($clickUrl, 0, '', true);
            } else {
                // Redirect to homepage if banner or click URL is invalid
                $root->mController->executeRedirect(XOOPS_URL . '/', 1, '', true);
            }
        } elseif (class_exists('BannerClientSession') && BannerClientSession::isAuthenticated()) {
            $redirect_url = XOOPS_URL . '/modules/' . $moduleDirname . '/index.php?action=Stats';
            $root->mController->executeRedirect($redirect_url, 0, '', true);
        } else {
            $redirect_url = XOOPS_URL . '/modules/' . $moduleDirname . '/index.php?action=Login';
            $root->mController->executeRedirect($redirect_url, 0, '', true);
        }
        exit();
    }

    /**
     * Tracks a click for a given banner ID based on its type
     * and returns the destination URL.
     *
     * @param int $bid Banner ID
     * @return string|false The destination click URL or false if banner not found/no URL.
     */
    protected static function _trackAndGetClickUrl($bid)
    {
        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        if (!$bannerHandler instanceof Bannerstats_BannerHandler) {
            error_log("Bannerstats_DelegateManager::_trackAndGetClickUrl - Failed to get Bannerstats_BannerHandler.");
            return false;
        }
        /** @var Bannerstats_BannerObject $banner */
        $banner = $bannerHandler->get($bid);

        if (is_object($banner) && $banner->get('status') == 1) { // Only track for active banners
            $trackClickForThisType = ($banner->get('banner_type') === 'image');

            if ($trackClickForThisType) {
                if ($bannerHandler->countClick($bid)) {
                    self::_logClick($banner); // Optional detailed logging
                }
            }
            return $banner->get('clickurl');
        }
        return false;
    }

    /**
     * Logs about a banner impression
     * @param Bannerstats_BannerObject $banner
     */
    protected static function _logImpression(Bannerstats_BannerObject $banner)
    {
        // Placeholder for future detailed impression logging (e.g., to a separate stats table).
    }

    /**
     * Logs about a banner click
     * @param Bannerstats_BannerObject $banner
     */
    protected static function _logClick(Bannerstats_BannerObject $banner)
    {
        // Placeholder for future detailed click logging.
    }

    /**
     * Public method called by the Smarty plugin <{banner ...}>.
     * Fetches and prepares HTML for a specific banner or a random one based on criteria.
     *
     * @param array $params Associative array, may contain 'bid', 'cid', or 'campaign_id'.
     * @return string HTML for the banner or an empty string/comment on failure.
     */
    public static function getBannerHtmlForDisplay(array $params = []): string
    {
        $bid = isset($params['bid']) ? (int)$params['bid'] : 0;
        // If 'cid' is not set in $params, pass null to handler, signifying "any client" for random selection.
        // If 'cid' is set (e.g., to 0 or >0), pass that specific integer.
        $cid = array_key_exists('cid', $params) ? (int)$params['cid'] : null;
        $campaign_id = isset($params['campaign_id']) ? (int)$params['campaign_id'] : 0;

        $bannerData = self::_fetchAndPrepareBannerDataForPlugin($bid, $cid, $campaign_id);

        $bannerHtml = '';
        if ($bannerData) {
            $bannerHtml = self::_buildBannerHtmlFromData($bannerData);
        } else {
            // Construct a more informative comment for debugging
            $criteriaParts = [];
            if ($bid > 0) $criteriaParts[] = "bid: {$bid}";
            if ($cid !== null) $criteriaParts[] = "cid: {$cid}";
            if ($campaign_id > 0) $criteriaParts[] = "campaign_id: {$campaign_id}";
            if (empty($criteriaParts)) $criteriaParts[] = "random global";
            
            $bannerHtml = "<!-- Bannerstats: No banner found for specified criteria (" . implode(", ", $criteriaParts) . ") -->";
            // error_log("Bannerstats_DelegateManager::getBannerHtmlForDisplay - No banner found for " . implode(", ", $criteriaParts));
        }
        return $bannerHtml;
    }

    /**
     * Fetches banner data either by specific BID or a random active one based on CID and Campaign ID.
     * Handles impression counting.
     *
     * @param int $bid Specific Banner ID (0 if not used).
     * @param int|null $cid Client ID. `null` means any client, `0` means client 0, `>0` means specific client.
     * @param int $campaign_id Campaign ID. If > 0, filters by this campaign.
     * @return array|null Banner data array or null if not found/not active.
     */
    private static function _fetchAndPrepareBannerDataForPlugin(int $bid = 0, ?int $cid = null, int $campaign_id = 0): ?array
    {
        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        if (!$bannerHandler instanceof Bannerstats_BannerHandler) {
            error_log("Bannerstats_DelegateManager: Failed to get Bannerstats_BannerHandler in _fetchAndPrepareBannerDataForPlugin.");
            return null;
        }

        // Call the unified handler method that understands bid, cid, and campaign_id
        /** @var Bannerstats_BannerObject|null $banner */
        $banner = $bannerHandler->getDisplayBanner($bid, $cid, $campaign_id);

        if (!$banner) {
            // error_log("Bannerstats_DelegateManager: No banner from getDisplayBanner for bid:{$bid}, cid:" . var_export($cid, true) . ", camp:{$campaign_id}");
            return null;
        }

        // Log impression for the selected banner
        $bannerHandler->countImpression($banner->getVar('bid'));
        self::_logImpression($banner); // Optional detailed logging

        // Prepare data array for HTML construction
        // Use 'get' and 'n' for "no filtering to prevent makeClickable
        $bannerData = [
            'bid' => $banner->getVar('bid'),
            'cid' => $banner->getVar('cid'),
            'name' => $banner->get('name', 'n'),
            'banner_type' => $banner->getVar('banner_type'),
            'imptotal' => $banner->getVar('imptotal'),
            'impmade' => $banner->getVar('impmade'),
            'clicks' => $banner->getVar('clicks'),
            'imageurl' => $banner->get('imageurl', 'n'),
            'clickurl' => $banner->get('clickurl', 'n'),
            'htmlcode' => $banner->get('htmlcode', 'n'),
            'width' => $banner->getVar('width'),
            'height' => $banner->getVar('height'),
        ];

        // The click URL should always point to banners.php for tracking
        $bannerData['clickurl_tracked'] = XOOPS_URL . '/banners.php?bid=' . $banner->getVar('bid');
        return $bannerData;
    }

    /**
     * Helper method to construct banner HTML from prepared data.
     *
     * @param array $bannerData Prepared banner data.
     * @return string HTML for the banner.
     */
    private static function _buildBannerHtmlFromData(array $bannerData): string
    {
        $bannerHtml = '';
        $bannerType = $bannerData['banner_type'] ?? 'unknown';

        switch ($bannerType) {
            case 'image':
                if (!empty($bannerData['imageurl'])) {
                    $imageUrl = $bannerData['imageurl'];
                    // Construct the correct image URL
                    if (strpos($imageUrl, '://') === false) { // If not an absolute URL
                        $imageUrl = XOOPS_URL . '/' . ltrim($imageUrl, '/');
                    }
                    $imgUrlEscaped = htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');
                    $altTextValue = $bannerData['name'] ?? ('Banner ID ' . ($bannerData['bid'] ?? ''));
                    $altTextEscaped = htmlspecialchars($altTextValue, ENT_QUOTES, 'UTF-8');
                    $trackedClickUrl = htmlspecialchars($bannerData['clickurl_tracked'] ?? '#', ENT_QUOTES, 'UTF-8');
                    
                    $styleAttrs = [];
                    if (isset($bannerData['width']) && $bannerData['width'] > 0) $styleAttrs[] = "width:" . intval($bannerData['width']) . "px;";
                    if (isset($bannerData['height']) && $bannerData['height'] > 0) $styleAttrs[] = "height:" . intval($bannerData['height']) . "px;";
                    $styleString = !empty($styleAttrs) ? " style='" . implode(" ", $styleAttrs) . "'" : "";
                    
                    // Use width/height attributes for semantic HTML and style for explicit sizing if needed
                    $widthAttr = isset($bannerData['width']) && $bannerData['width'] > 0 ? " width='" . intval($bannerData['width']) . "'" : "";
                    $heightAttr = isset($bannerData['height']) && $bannerData['height'] > 0 ? " height='" . intval($bannerData['height']) . "'" : "";


                    $bannerHtml = "<a href='{$trackedClickUrl}' target='_blank' rel='noopener noreferrer sponsored'>";
                    $bannerHtml .= "<img src='{$imgUrlEscaped}' alt='{$altTextEscaped}'{$widthAttr}{$heightAttr} border='0' loading='lazy'>";
                    $bannerHtml .= "</a>";
                } else {
                    $bannerHtml = "<!-- Bannerstats: Image banner (ID: " . ($bannerData['bid'] ?? 'N/A') . ") has no image URL. -->";
                }
                break;
            case 'html':
                $htmlContent = $bannerData['htmlcode'] ?? '';
                $bannerHtml = html_entity_decode($htmlContent, ENT_QUOTES);
                break;
            case 'ad_tag':
                $htmlContent = $bannerData['htmlcode'] ?? '';
                $bannerHtml = $htmlContent; // Output raw HTML
                break;
            case 'video':
             
                $htmlContent = $bannerData['htmlcode'] ?? '';
                $bannerHtml = $htmlContent; // Output raw HTML
                break;
            default:
                $bannerHtml = "<!-- Bannerstats: Unknown banner type '" . htmlspecialchars($bannerType, ENT_QUOTES, 'UTF-8') . "' -->";
                break;
        }
        return $bannerHtml;
    }

    /**
     * Original delegate for Legacy.Function.GetBannerHtml (handles xoops_getbanner())
     * Now uses the new helper methods.
     *
     * @param string &$bannerHtml By-reference HTML output for the banner
     * @param int    $cid         Client ID (0 for any client, as passed by xoops_getbanner)
     */
    public static function provideBannerHtml(&$bannerHtml, $cid = 0)
    {
        // For xoops_getbanner(), if $cid is 0, it means "any client" (pass null to handler).
        // If $cid > 0, it's a specific client.
        // A special consideration: if in admin (XOOPS_ADMIN_CP is defined), cid=0 might specifically mean system banners (cid=0 in DB).
        // Outside admin, cid=0 from xoops_getbanner means "any client", so we pass null to the handler.
        $cidForHandler = ($cid == 0 && !defined('XOOPS_ADMIN_CP')) ? null : $cid;

        // xoops_getbanner doesn't have a campaign_id parameter, so pass 0.
        $bannerData = self::_fetchAndPrepareBannerDataForPlugin(0, $cidForHandler, 0); 

        if ($bannerData) {
            $bannerHtml = self::_buildBannerHtmlFromData($bannerData);
        } else {
            $bannerHtml = "<!-- Bannerstats: No banner available -->";
            // error_log("Bannerstats_DelegateManager DEBUG: provideBannerHtml - _fetchAndPrepareBannerDataForPlugin returned NO data for effective CID: " . var_export($cidForHandler, true));
        }
    }

    /**
     * Sends a banner-related notification email.
     * This method is static and can be called from other classes.
     *
     * @param string $recipientEmail
     * @param string $recipientName
     * @param string $subject        The email subject
     * @param string $templateName template for the email body (e.g., 'banner_alert_client.tpl').
     * @param array  $templateVars
     * @return bool True on success, false on failure.
     */
    public static function _sendNotificationEmail(string $recipientEmail, string $recipientName, string $subject, string $templateName, array $templateVars = []): bool
    {
        if (empty($recipientEmail) || !filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
            error_log("Bannerstats Email Alert: Invalid or empty recipient email: " . $recipientEmail);
            return false;
        }

        $root = XCube_Root::getSingleton();

        //TODO XCUBE_MAILBUILDER
        /** @var XCube_Mailer $mailer */
        $mailer = $root->mServiceManager->getService('Mailer');
        if (!$mailer) {
            // Fallback if service is not available (less common for Mailer)
            $mailer = new XCube_MailDirector();
        }

        $xoopsConfig = $root->mContext->getXoopsConfig();

        // Set From address (usually site admin)
        $fromEmail = !empty($xoopsConfig['adminmail']) ? $xoopsConfig['adminmail'] : 'noreply@example.com';
        $fromName = !empty($xoopsConfig['sitename']) ? $xoopsConfig['sitename'] : 'Website Notification';
        
        $mailer->setFromEmail($fromEmail);
        $mailer->setFromName($fromName);

        $language = $xoopsConfig['language'] ?? 'english';
        $modulePath = XOOPS_MODULE_PATH . '/bannerstats';

        $templatePath = $modulePath . '/language/' . $language . '/mail_template/' . $templateName;

        if (!file_exists($templatePath)) {
            // Fallback to English if current language template doesn't exist
            $templatePath = $modulePath . '/language/english/mail_template/' . $templateName;
            if (!file_exists($templatePath)) {
                error_log("Bannerstats Email Alert: Template {$templateName} not found in English or {$language} path.");
                return false;
            }
        }
        
        $mailer->setSubject($subject);
        $mailer->setTemplate($templatePath); // Path to Smarty .tpl file

        // Assign common variables and specific template variables
        $mailer->assign('sitename', $xoopsConfig['sitename'] ?? 'Your Site');
        $mailer->assign('siteurl', XOOPS_URL . '/');
        $mailer->assign('recipient_name', $recipientName); // For personalization in the template

        foreach ($templateVars as $key => $value) {
            $mailer->assign($key, $value);
        }

        $mailer->setToEmails($recipientEmail);

        if ($mailer->send()) {
            error_log("Bannerstats Email Alert: Successfully sent '{$subject}' to {$recipientEmail}");
            return true;
        } else {
            error_log("Bannerstats Email Alert: Failed to send '{$subject}' to {$recipientEmail}. Error: " . $mailer->dumpErrors());
            return false;
        }
    }
}
