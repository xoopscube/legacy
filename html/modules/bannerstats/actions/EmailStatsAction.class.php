<?php
/**
 * Bannerstats - Module for XCL
 * EmailStatsAction.class.php
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

class Bannerstats_EmailStatsAction
{
    private string $message = '';
    private bool $isError = false;

    public function getPageTitle(): string
    {
        return "Email Banner Statistics";
    }

    /**
     * Handles the request to email stats
     * 
     * @return string Template name
     */
    public function getDefaultView(): string
    {
        global $xoopsTpl, $xoopsConfig, $xoopsUser; // $xoopsUser might be needed for getMailer if not admin

        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/bannerstats/index.php?action=Login");
            exit();
        }

        $cid = BannerClientSession::getClientId();
        $bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;
        $submittedToken = isset($_GET['bstoken']) ? (string)$_GET['bstoken'] : '';

        if ($bid <= 0 || !BannerClientToken::validate($submittedToken, 'EmailStats_' . $bid)) {
            $this->message = _MD_BANNERSTATS_INVALID_TOKEN;
            $this->isError = true;
        } else {
            $statsManager = new BannerStatsManager();
            $clientEmail = $statsManager->getBannerClientEmail($cid);
            $bannerDetails = $statsManager->getBannerDetails($bid, $cid);

            if (empty($clientEmail)) {
                $this->message = _MD_BANNERSTATS_EMAIL_SENT_FAIL . " (Client email not found)";
                $this->isError = true;
            } elseif (!$bannerDetails) {
                $this->message = _MD_BANNERSTATS_BANNER_NOT_FOUND;
                $this->isError = true;
            } else {
                // Prepare email content
                $bannerIdentifier = '';
                if (!empty($bannerDetails['htmlbanner']) && !empty($bannerDetails['htmlcode'])) {
                    $bannerIdentifier = "[HTML Banner ID: " . $bannerDetails['bid'] . "]";
                } elseif (!empty($bannerDetails['imageurl'])) {
                    $bannerIdentifier = $bannerDetails['imageurl'];
                } else {
                    $bannerIdentifier = "Banner ID: " . $bannerDetails['bid'];
                }

                $clientLogin = BannerClientSession::getClientLogin() ?? 'Valued Client';

                // Language constants for email
                $subject = sprintf(
                    defined('_MB_BANNERSTATS_SUBJECT') ? _MB_BANNERSTATS_SUBJECT : "Banner Statistics for %s",
                    $bannerIdentifier
                );
                $mailBody = sprintf(
                    defined('_MB_BANNERSTATS_MAILMSG') ? _MB_BANNERSTATS_MAILMSG : "Dear %s,\n\nHere are the statistics for your banner '%s' on %s:\n\nImpressions Made: %d\nClicks Received: %d\n\nThank you.",
                    htmlspecialchars($clientLogin, ENT_QUOTES),
                    htmlspecialchars($bannerIdentifier, ENT_QUOTES),
                    htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
                    $bannerDetails['impmade'],
                    $bannerDetails['clicks']
                );

                $xoopsMailer = getMailer();
                $xoopsMailer->useMail();
                $xoopsMailer->setToEmails($clientEmail);
                $xoopsMailer->setFromEmail($xoopsConfig['adminmail']);
                $xoopsMailer->setFromName(htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
                $xoopsMailer->setSubject($subject);
                $xoopsMailer->setBody($mailBody);

                if ($xoopsMailer->send()) {
                    $this->message = _MD_BANNERSTATS_EMAIL_SENT_SUCCESS;
                    $this->isError = false;
                } else {
                    $this->message = _MD_BANNERSTATS_EMAIL_SENT_FAIL . "<!-- Mailer Error: " . $xoopsMailer->getErrors(true) . " -->";
                    $this->isError = true;
                    error_log("BannerStats Email Error: Failed to send stats to {$clientEmail} for banner {$bid}. Mailer errors: " . $xoopsMailer->getErrors(false));
                }
            }
        }

        if (is_object($xoopsTpl)) {
            $xoopsTpl->assign('bannerstats_message_content', $this->message);
            $xoopsTpl->assign('bannerstats_message_is_error', $this->isError);
            $xoopsTpl->assign('bannerstats_continue_link', XOOPS_URL . "/modules/bannerstats/index.php?action=Stats");
            $xoopsTpl->assign('bannerstats_continue_text', 'Return to Statistics');
        }

        return 'bannerstats_message.html';
    }
}
