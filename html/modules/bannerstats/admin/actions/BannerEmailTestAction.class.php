<?php
/**
 * Bannerstats - Module for XCL
 * BannerEmailTestAction.class.php
 *
 * Action to test email notifications using the XCube_MailBuilder system
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

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/Action.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/kernel/DelegateManager.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClient.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerEmailTestForm.class.php';

class Bannerstats_BannerEmailTestAction extends Bannerstats_Action
{
    /** @var Bannerstats_BannerEmailTestForm */
    public $mActionForm = null;
    protected string $mPageTitle = ''; // store page title
    protected array $mSuccessMessages = [];
    protected array $mErrorMessages = [];
    protected array $mWarningMessages = [];

    /**
     * Prepare action execution
     * @param XCube_Controller
     * @param XoopsUser
     * @param array $moduleConfig
     */
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        parent::prepare($controller, $xoopsUser, $moduleConfig);
        $this->mPageTitle = _AD_BANNERSTATS_EMAILTEST_TITLE;
        
        // Initialize action form
        $this->mActionForm = new Bannerstats_BannerEmailTestForm();
        $this->mActionForm->prepare();
    }


    public function getTokenName(): string
    {
        return 'module.bannerstats.BannerEmailTestForm.TOKEN';
    }
    /**
     * Check if the current user has permission for this action
     * @param XCube_Controller
     * @param XoopsUser
     * @return bool
     */
    public function hasPermission(&$controller, &$xoopsUser)
    {
        // Only admins can access this admin utility
        return (is_object($xoopsUser) && $xoopsUser->isAdmin($controller->mRoot->mContext->mXoopsModule->get('mid')));
    }

    /**
     * Gets the default view status for GET requests
     * This method sets up data for the form.
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @return int BANNERSTATS_FRAME_VIEW_INPUT
     */
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        // Set default values if needed
        $this->mActionForm->set('bid', 0);
        $this->mActionForm->set('email_type', 'low_client');
        
        return BANNERSTATS_FRAME_VIEW_INPUT;
    }

    /**
     * Executes the main logic for POST requests (sending the email)
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @return int BANNERSTATS_FRAME_VIEW_SUCCESS or BANNERSTATS_FRAME_VIEW_ERROR
     */
    public function execute(&$controller, &$xoopsUser)
    {
        // populate action form with HTTP request
        $this->mActionForm->fetch();

        // Debug log form properties after fetch
        //error_log("BannerEmailTestAction::execute - Form properties after fetch: email_type=" . var_export($this->mActionForm->get('email_type'), true) . ", bid=" . var_export($this->mActionForm->get('bid'), true));

        if (!$this->mActionForm->validate($this->mActionForm->getTokenName())) {
            // If this returns false, it means:
            // a) field failed validation (e.g., 'bid' not an int, 'email_type', etc.)
            // OR
            // b) token check failed.
            // form object ($this->mActionForm) with specific error messages
            $this->mErrorMessages = array_merge($this->mErrorMessages, $this->mActionForm->getErrorMessages());
            // Log errors for debugging
            foreach($this->mActionForm->getErrorMessages() as $errMsg) {
                error_log("Validation/Token Error: " . $errMsg);
            }
            return BANNERSTATS_FRAME_VIEW_INPUT;
        }

        // data and token validation have passed, get your validated data:
        $bid = (int)$this->mActionForm->get('bid');
        $emailType = (string)$this->mActionForm->get('email_type');

        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        /** @var Bannerstats_BannerObject $banner */
        $banner = $bannerHandler->get($bid);

        if (!$banner instanceof Bannerstats_BannerObject) {
            $this->mErrorMessages[] = sprintf(_AD_BANNERSTATS_EMAILTEST_BANNER_NOTFOUND, $bid);
            return BANNERSTATS_FRAME_VIEW_INPUT;
        }

        $banner->loadClient();
        $client = $banner->mClient;
        $clientName = ($client instanceof Bannerstats_BannerclientObject && $client->get('name')) ? $client->getShow('name') : Bannerstats_BannerHandler::DEFAULT_CLIENT_NAME;
        $clientEmail = ($client instanceof Bannerstats_BannerclientObject && $client->get('email')) ? $client->get('email') : null;

        $root = XCube_Root::getSingleton();
        $configHandler = xoops_gethandler('config');
        $moduleConfig = $configHandler->getConfigsByDirname('bannerstats');
        // module config key is 'admin_alert_email' as used in AdminAlertMail.class.php
        $adminEmail = $moduleConfig['admin_alert_email'] ?? $root->mContext->getXoopsConfig('adminmail');
        $bannerName = $banner->getShow('name');

        $recipientEmail = null;
        $emailTypeName = ''; // Used for success/failure messages

        // UI feedback switch $emailTypeName and $recipientEmail for success/error message
        // email content is set by BannerHandler::sendTestNotification method and mailer classes
        switch ($emailType) {
            case 'low_client':
                if (!$clientEmail) {
                    $this->mWarningMessages[] = sprintf(_AD_BANNERSTATS_EMAILTEST_CLIENT_NOTFOUND, $bid) . " Cannot send client email.";
                    return BANNERSTATS_FRAME_VIEW_INPUT;
                }
                $recipientEmail = $clientEmail;
                $emailTypeName = _AD_BANNERSTATS_EMAILTEST_TYPE_LOW_CLIENT;
                break;
            case 'low_admin':
                $recipientEmail = $adminEmail;
                $emailTypeName = _AD_BANNERSTATS_EMAILTEST_TYPE_LOW_ADMIN;
                break;
            case 'finished_client':
                if (!$clientEmail) {
                    $this->mWarningMessages[] = sprintf(_AD_BANNERSTATS_EMAILTEST_CLIENT_NOTFOUND, $bid) . " Cannot send client email.";
                    return BANNERSTATS_FRAME_VIEW_INPUT;
                }
                $recipientEmail = $clientEmail;
                $emailTypeName = _AD_BANNERSTATS_EMAILTEST_TYPE_FINISHED_CLIENT;
                break;
            case 'finished_admin':
                $recipientEmail = $adminEmail;
                $emailTypeName = _AD_BANNERSTATS_EMAILTEST_TYPE_FINISHED_ADMIN;
                break;
            default:
                $this->mErrorMessages[] = 'Invalid email type selected.';
                return BANNERSTATS_FRAME_VIEW_INPUT;
        }

        // Delegate the actual email sending to the handler
        if ($bannerHandler->sendTestNotification($bid, $emailType)) {
            $this->mSuccessMessages[] = sprintf(_AD_BANNERSTATS_EMAILTEST_MSG_SUCCESS, $emailTypeName, htmlspecialchars((string)$recipientEmail));
            return BANNERSTATS_FRAME_VIEW_SUCCESS;
        } else {
            $this->mErrorMessages[] = sprintf(_AD_BANNERSTATS_EMAILTEST_MSG_FAIL, $emailTypeName);
            // get more specific errors from the handler
            // if (method_exists($bannerHandler, 'getErrors') && !empty($bannerHandler->getErrors())) {
            //    $this->mErrorMessages = array_merge($this->mErrorMessages, $bannerHandler->getErrors());
            // }
            return BANNERSTATS_FRAME_VIEW_ERROR;
        }
    }

    /**
     * Renders the input form.
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('bannerstats_admin_email_test.html');
        $render->setAttribute('xoops_pagetitle', $this->mPageTitle);
        $render->setAttribute('pageTitle', $this->mPageTitle);
        $render->setAttribute('pageDescription', _AD_BANNERSTATS_EMAILTEST_DESC);

        // Set the action form for the template
        $render->setAttribute('actionForm', $this->mActionForm);

        // Get all active banners for selection
        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        $criteria = new CriteriaCompo(new Criteria('status', 1));
        $criteria->setSort('name');
        $banners = $bannerHandler->getObjects($criteria);
        $render->setAttribute('activeBanners', $banners);

        $emailTypes = [
            'low_client' => _AD_BANNERSTATS_EMAILTEST_TYPE_LOW_CLIENT,
            'low_admin' => _AD_BANNERSTATS_EMAILTEST_TYPE_LOW_ADMIN,
            'finished_client' => _AD_BANNERSTATS_EMAILTEST_TYPE_FINISHED_CLIENT,
            'finished_admin' => _AD_BANNERSTATS_EMAILTEST_TYPE_FINISHED_ADMIN,
        ];
        $render->setAttribute('emailTypes', $emailTypes);

        $render->setAttribute('bannerstats_success_messages', $this->mSuccessMessages);
        $render->setAttribute('bannerstats_error_messages', $this->mErrorMessages);
        $render->setAttribute('bannerstats_warning_messages', $this->mWarningMessages);
    }

    /**
     * Handles view after successful email sending
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        if (class_exists('XCube_DelegateUtils')) {
            foreach ($this->mSuccessMessages as $msg) {
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddSuccessMessage', $msg);
            }
        }
        //$controller->executeForward('./index.php?action=BannerEmailTest'); TODO MSG
        $controller->executeRedirect('./index.php?action=BannerEmailTest', 1, _AD_BANNERSTATS_EMAILTEST_TITLE);
    }

    /**
     * Handles view after failed email sending or other errors
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        if (!empty($this->mErrorMessages) || !empty($this->mWarningMessages)) {
            $this->executeViewInput($controller, $xoopsUser, $render); 
        } else {
            if (class_exists('XCube_DelegateUtils')) {
                 XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', _AD_BANNERSTATS_ERROR_ACTION_FAILED);
            }
            $controller->executeRedirect('./index.php?action=BannerEmailTest', 1);
        }
    }
    
    public function getSuccessMessages(): array { 
        return $this->mSuccessMessages; 
    }
    
    public function getErrorMessages(): array { 
        return $this->mErrorMessages; 
    }
    
    public function getWarningMessages(): array { 
        return $this->mWarningMessages; 
    }
}
