<?php
// html/modules/bannerstats/actions/RequestSupportAction.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    die('XOOPS root path not defined');
}

// Ensure BannerStatsManager is available
require_once dirname(__DIR__) . '/class/BannerStatsManager.class.php';
// Ensure BannerClientSession is available
require_once dirname(__DIR__) . '/class/BannerClientSession.class.php';
// Ensure BannerClientToken is available
require_once dirname(__DIR__) . '/class/BannerClientToken.class.php';


class Bannerstats_RequestSupportAction
{
    private $moduleDirname;
    private $xoopsTpl;
    private $xoopsConfig; // To get admin email

    public function __construct()
    {
        global $xoopsTpl, $xoopsConfig;
        $this->xoopsTpl = $xoopsTpl;
        $this->xoopsConfig = $xoopsConfig;
        $this->moduleDirname = basename(dirname(dirname(__FILE__))); // 'bannerstats'

        if (is_object($this->xoopsTpl)) {
            $this->xoopsTpl->assign('module_dirname', $this->moduleDirname);
            $this->xoopsTpl->assign('action_url', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=RequestSupport");
            $this->xoopsTpl->assign('stats_url', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Stats");
        }
    }

    public function getPageTitle()
    {
        return defined('_MD_BANNERSTATS_REQUEST_SUPPORT_TITLE') ? _MD_BANNERSTATS_REQUEST_SUPPORT_TITLE : "Banner Support Request";
    }

    public function getDefaultView()
    {
        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            exit();
        }

        $clientName = BannerClientSession::getClientName();
        $clientEmail = BannerClientSession::getClientEmail();

        $submitted_data = $_SESSION['bannerstats_support_form_data'] ?? [];
        unset($_SESSION['bannerstats_support_form_data']);

        $form_data = [
            'client_name'  => $submitted_data['client_name'] ?? ($clientName ?: ''),
            'client_email' => $submitted_data['client_email'] ?? ($clientEmail ?: ''),
            'request_type' => $submitted_data['request_type'] ?? '',
            'banner_id'    => $submitted_data['banner_id'] ?? '',
            'subject'      => $submitted_data['subject'] ?? '',
            'message'      => $submitted_data['message'] ?? '',
        ];
        $this->xoopsTpl->assign('form_data', $form_data);

        $clientId = BannerClientSession::getClientId();
        if ($clientId === null) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Client ID not found in session.');
            $template_vars = $this->xoopsTpl->get_template_vars();
            if (!isset($template_vars['bannerstats_message'])) {
                 $this->xoopsTpl->assign('bannerstats_message', '');
            }
            // It's better to render an error template than just return null or an empty string
            // if $xoopsTpl is expected to be used.
            // Assuming you have a bannerstats_error.html template:
            return 'bannerstats_error.html'; // Or handle error appropriately
        }

        $statsManager = new BannerStatsManager();
        $clientBanners = $statsManager->getActiveBanners($clientId);

        $bannerOptions = [];
        $bannerOptions[] = ['id' => '', 'name' => defined('_MD_BANNERSTATS_SELECT_BANNER') ? _MD_BANNERSTATS_SELECT_BANNER : '-- Select Banner --'];

        if (!empty($clientBanners)) {
            foreach ($clientBanners as $banner) {
                 $isHtmlCurrentBanner = !empty($banner['htmlbanner']);
                 $banner_name_display = '';

                 if ($isHtmlCurrentBanner) {
                     $banner_name_display = "HTML Banner";
                     if (!empty($banner['htmlcode'])) {
                         $snippet = strip_tags($banner['htmlcode']);
                         $snippet = trim(preg_replace('/\s+/', ' ', $snippet));
                         if (strlen($snippet) > 30) {
                             $snippet = substr($snippet, 0, 30) . '...';
                         }
                         if (!empty($snippet)) {
                             $banner_name_display .= " - \"" . htmlspecialchars($snippet, ENT_QUOTES) . "\"";
                         }
                     }
                 } elseif (!empty($banner['imageurl'])) {
                     $banner_name_display = "Image: " . htmlspecialchars(basename($banner['imageurl']), ENT_QUOTES);
                 } else {
                     $banner_name_display = "Banner";
                 }
                 $banner_name_display .= " (ID: " . $banner['bid'] . ")";

                 $bannerOptions[] = [
                    'id' => $banner['bid'],
                    'name' => $banner_name_display,
                 ];
            }
        }
        $this->xoopsTpl->assign('banner_options', $bannerOptions);

        $requestTypes = [
            'new_banner' => defined('_MD_BANNERSTATS_REQ_NEW_BANNER') ? _MD_BANNERSTATS_REQ_NEW_BANNER : 'Request New Banner Setup',
            'update_code' => defined('_MD_BANNERSTATS_REQ_UPDATE_CODE') ? _MD_BANNERSTATS_REQ_UPDATE_CODE : 'Update Ad Code for Existing Banner',
            'problem' => defined('_MD_BANNERSTATS_REQ_PROBLEM') ? _MD_BANNERSTATS_REQ_PROBLEM : 'Report Problem with Existing Banner',
            'question' => defined('_MD_BANNERSTATS_REQ_QUESTION') ? _MD_BANNERSTATS_REQ_QUESTION : 'General Question about Banners',
            'other' => defined('_MD_BANNERSTATS_REQ_OTHER') ? _MD_BANNERSTATS_REQ_OTHER : 'Other',
        ];
        $this->xoopsTpl->assign('request_types', $requestTypes);

        if (class_exists('BannerClientToken')) {
            $token = BannerClientToken::create('support_request_form');
            $this->xoopsTpl->assign('csrf_token', $token);
        } else {
            error_log("Bannerstats Error: BannerClientToken class not found.");
        }
        
        $template_vars = $this->xoopsTpl->get_template_vars();
        if (!isset($template_vars['bannerstats_error_message'])) {
             $this->xoopsTpl->assign('bannerstats_error_message', '');
        }
        if (!isset($template_vars['bannerstats_message'])) {
             $this->xoopsTpl->assign('bannerstats_message', '');
        }

        return 'bannerstats_support.html';
    }

    public function execute()
    {
        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            exit();
        }

        if (!class_exists('BannerClientToken') || !BannerClientToken::validate($_POST['csrf_token'] ?? '', 'support_request_form')) {
            $this->xoopsTpl->assign('bannerstats_error_message', 'Invalid security token. Please try again.');
            $_SESSION['bannerstats_support_form_data'] = $_POST;
            return $this->getDefaultView();
        }

        $clientName = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
        $clientEmail = isset($_POST['client_email']) ? trim($_POST['client_email']) : '';
        $requestTypeKey = isset($_POST['request_type']) ? trim($_POST['request_type']) : '';
        $bannerId = isset($_POST['banner_id']) ? trim($_POST['banner_id']) : '';
        $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        $errors = [];
        if (empty($clientName)) {
            $errors[] = defined('_MD_BANNERSTATS_ERR_NAME_REQUIRED') ? _MD_BANNERSTATS_ERR_NAME_REQUIRED : 'Your name is required.';
        }
        if (empty($clientEmail) || !filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = defined('_MD_BANNERSTATS_ERR_EMAIL_REQUIRED') ? _MD_BANNERSTATS_ERR_EMAIL_REQUIRED : 'A valid email address is required.';
        }
        if (empty($requestTypeKey)) {
            $errors[] = defined('_MD_BANNERSTATS_ERR_REQ_TYPE_REQUIRED') ? _MD_BANNERSTATS_ERR_REQ_TYPE_REQUIRED : 'Please select a request type.';
        }
        if (empty($subject)) {
            $errors[] = defined('_MD_BANNERSTATS_ERR_SUBJECT_REQUIRED') ? _MD_BANNERSTATS_ERR_SUBJECT_REQUIRED : 'Subject is required.';
        }
        if (empty($message)) {
            $errors[] = defined('_MD_BANNERSTATS_ERR_MESSAGE_REQUIRED') ? _MD_BANNERSTATS_ERR_MESSAGE_REQUIRED : 'Message is required.';
        }

        if (in_array($requestTypeKey, ['update_code', 'problem'])) {
             if (empty($bannerId) || !is_numeric($bannerId) || (int)$bannerId <= 0) {
                 $errors[] = defined('_MD_BANNERSTATS_ERR_BANNER_ID_REQUIRED') ? _MD_BANNERSTATS_ERR_BANNER_ID_REQUIRED : 'Please select a valid Banner ID for this request type.';
             }
        }

        if (!empty($errors)) {
            $this->xoopsTpl->assign('bannerstats_error_message', implode('<br>', $errors));
            $_SESSION['bannerstats_support_form_data'] = $_POST;
            return $this->getDefaultView();
        }

        $requestTypes = [
            'new_banner' => defined('_MD_BANNERSTATS_REQ_NEW_BANNER') ? _MD_BANNERSTATS_REQ_NEW_BANNER : 'Request New Banner Setup',
            'update_code' => defined('_MD_BANNERSTATS_REQ_UPDATE_CODE') ? _MD_BANNERSTATS_REQ_UPDATE_CODE : 'Update Ad Code for Existing Banner',
            'problem' => defined('_MD_BANNERSTATS_REQ_PROBLEM') ? _MD_BANNERSTATS_REQ_PROBLEM : 'Report Problem with Existing Banner',
            'question' => defined('_MD_BANNERSTATS_REQ_QUESTION') ? _MD_BANNERSTATS_REQ_QUESTION : 'General Question about Banners',
            'other' => defined('_MD_BANNERSTATS_REQ_OTHER') ? _MD_BANNERSTATS_REQ_OTHER : 'Other',
        ];
        $requestTypeDisplay = $requestTypes[$requestTypeKey] ?? 'N/A';

        // Prepare email
        $xoopsMailer = getMailer();
        
        // Ensure $xoopsMailer is an object before proceeding
        if (!is_object($xoopsMailer)) {
            $errorMessage = defined('_MD_BANNERSTATS_MAILER_ERROR') ? _MD_BANNERSTATS_MAILER_ERROR : 'Could not initialize the mailer service.';
            $this->xoopsTpl->assign('bannerstats_error_message', $errorMessage);
            error_log("Bannerstats: Failed to get mailer object from XCube_Root.");
            $_SESSION['bannerstats_support_form_data'] = $_POST;
            return $this->getDefaultView();
        }

        $language = $this->xoopsConfig['language'] ?? 'english'; // Use $this->xoopsConfig
        $mailTemplateDir = XOOPS_MODULE_PATH . '/' . $this->moduleDirname . '/language/' . $language . '/mail_template/';
        if (!is_dir($mailTemplateDir)) {
             $mailTemplateDir = XOOPS_MODULE_PATH . '/' . $this->moduleDirname . '/language/english/mail_template/';
        }
        $xoopsMailer->setTemplateDir($mailTemplateDir);
        $xoopsMailer->setTemplate('banner_support_request.tpl');
        
        $xoopsMailer->assign('CLIENT_NAME', $clientName);
        $xoopsMailer->assign('CLIENT_EMAIL', $clientEmail);
        $xoopsMailer->assign('REQUEST_TYPE', $requestTypeDisplay);
        $xoopsMailer->assign('BANNER_ID', $bannerId ? htmlspecialchars($bannerId, ENT_QUOTES) : 'N/A');
        $xoopsMailer->assign('SUBJECT_LINE', $subject);
        $xoopsMailer->assign('MESSAGE_BODY', nl2br(htmlspecialchars($message, ENT_QUOTES)));
        $xoopsMailer->assign('SITE_NAME', $this->xoopsConfig['sitename']); // Use $this->xoopsConfig
        $xoopsMailer->assign('SITE_URL', XOOPS_URL . '/');

        $emailSubject = '[' . $this->xoopsConfig['sitename'] . '] Banner Support Request: ' . $subject; // Use $this->xoopsConfig
        $xoopsMailer->setSubject($emailSubject);
        $xoopsMailer->setToEmails($this->xoopsConfig['adminmail']); // Use $this->xoopsConfig
        $xoopsMailer->setFromEmail($clientEmail);
        $xoopsMailer->setFromName(htmlspecialchars($clientName, ENT_QUOTES));

        if ($xoopsMailer->send()) {
            $redirectUrl = XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=RequestSupport&status=success";
            header("Location: " . $redirectUrl);
            exit();
        } else {
            $errorMessage = defined('_MD_BANNERSTATS_SUPPORT_SENT_ERROR') ? _MD_BANNERSTATS_SUPPORT_SENT_ERROR : 'There was an error sending your request. Please try again later.';
            $this->xoopsTpl->assign('bannerstats_error_message', $errorMessage . ' ' . $xoopsMailer->getErrors(true));
            error_log("Bannerstats: Failed to send support email. " . $xoopsMailer->getErrors(true));
            $_SESSION['bannerstats_support_form_data'] = $_POST;
            return $this->getDefaultView();
        }
    }
}
