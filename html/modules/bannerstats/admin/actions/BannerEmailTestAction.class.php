<?php
/**
 * Bannerstats - Module for XCL
 * BannerEmailTestAction.class.php
 *
 * Action to test email notifications using the XCube_MailBuilder system.
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

require_once __DIR__ . '/../class/Action.class.php';
require_once __DIR__ . '/../forms/BannerEmailTestForm.class.php';

// Core Mail Builder
if (file_exists(XOOPS_ROOT_PATH . '/core/XCube_MailBuilder.class.php')) {
    require_once XOOPS_ROOT_PATH . '/core/XCube_MailBuilder.class.php';
} else {
    error_log("Bannerstats_BannerEmailTestAction: Core XCube_MailBuilder.class.php not found. Test notification will fail.");
}

// Bannerstats module's specific mail builders
require_once XOOPS_MODULE_PATH . '/bannerstats/mail/AdminAlertMail.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/mail/ClientAlertMail.class.php';

// Required classes
require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClient.class.php';

// For XCube_DelegateUtils if not autoloaded
if (!class_exists('XCube_DelegateUtils') && file_exists(XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php')) {
    require_once XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php';
}

class Bannerstats_BannerEmailTestAction extends Bannerstats_Action
{
    /**
     * @var XCube_Root
     */
    protected $mRoot = null;

    /**
     * @var BannerEmailTestForm
     * Changed from protected to public to match parent class
     */
    public $mActionForm = null;

    /**
     * @var XoopsModule
     */
    protected $mModuleObject = null;

    /**
     * @var array
     */
    protected $mActiveBanners = [];

    /**
     * @var array
     */
    protected $mEmailTypes = [];

    public function __construct($adminFlag = true)
    {
        parent::__construct($adminFlag);
        $this->mRoot = XCube_Root::getSingleton();
    }

    public function hasPermission(&$controller, &$xoopsUser)
    {
        return (is_object($xoopsUser) && $xoopsUser->isAdmin());
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        parent::prepare($controller, $xoopsUser, $moduleConfig);

        if (!(is_object($this->mRoot->mContext->mXoopsUser) && $this->mRoot->mContext->mXoopsUser->isAdmin())) {
            $this->mRoot->mController->executeForward(XOOPS_URL . '/');
            return false;
        }

        $this->mActionForm = new BannerEmailTestForm();
        $this->mActionForm->prepare();

        // Get module object
        $module_handler = xoops_gethandler('module');
        if (!is_object($module_handler)) {
            error_log("Bannerstats_BannerEmailTestAction: FAILED to get module_handler.");
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', 'Error: Module handler not available.');
            $this->mRoot->mController->executeForward(XOOPS_URL . '/admin.php');
            return false;
        }
        $this->mModuleObject = $module_handler->getByDirname('bannerstats');

        if (!is_object($this->mModuleObject)) {
            error_log("Bannerstats_BannerEmailTestAction: Failed to load bannerstats module object.");
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', 'Error: Bannerstats module object not found.');
            $this->mRoot->mController->executeForward(XOOPS_URL . '/admin.php');
            return false;
        }

        // Load active banners
        $this->_loadActiveBanners();

        // Define email types
        $this->mEmailTypes = [
            'admin_alert' => _AD_BANNERSTATS_EMAIL_TYPE_ADMIN_ALERT ?? 'Admin Alert',
            'client_alert' => _AD_BANNERSTATS_EMAIL_TYPE_CLIENT_ALERT ?? 'Client Alert',
            'both' => _AD_BANNERSTATS_EMAIL_TYPE_BOTH ?? 'Both Admin and Client'
        ];

        return true;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return BANNERSTATS_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            $this->mActionForm->fetch();
            $this->mActionForm->validate();

            if ($this->mActionForm->hasError()) {
                return BANNERSTATS_FRAME_VIEW_INPUT;
            }

            return $this->_executeTestEmail($controller, $xoopsUser);
        }

        return BANNERSTATS_FRAME_VIEW_INPUT;
    }

    /**
     * Load active banners for the dropdown
     */
    protected function _loadActiveBanners()
    {
        $banner_handler = xoops_getmodulehandler('banner', 'bannerstats');
        if ($banner_handler) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('active', 1));
            $criteria->setSort('name');
            $criteria->setOrder('ASC');
            $this->mActiveBanners = $banner_handler->getObjects($criteria);
        }
    }

    /**
     * Execute the test email sending
     */
    protected function _executeTestEmail(&$controller, &$xoopsUser)
    {
        $bid = $this->mActionForm->get('bid');
        $emailType = $this->mActionForm->get('email_type');

        if (empty($bid) || empty($emailType)) {
            $this->mActionForm->addErrorMessage('Banner ID and Email Type are required.');
            return BANNERSTATS_FRAME_VIEW_INPUT;
        }

        // Load the banner
        $banner_handler = xoops_getmodulehandler('banner', 'bannerstats');
        $banner = $banner_handler->get($bid);
        
        if (!$banner) {
            $this->mActionForm->addErrorMessage('Selected banner not found.');
            return BANNERSTATS_FRAME_VIEW_INPUT;
        }

        // Get module config
        $moduleConfig = $this->mRoot->mContext->mModuleConfig;
        $xoopsConfig = $this->mRoot->mContext->mXoopsConfig ?? $GLOBALS['xoopsConfig'];

        $success = false;
        $errors = [];

        try {
            // Send admin email
            if ($emailType === 'admin_alert' || $emailType === 'both') {
                $success = $this->_sendAdminTestEmail($banner, $moduleConfig, $xoopsConfig) || $success;
            }

            // Send client email
            if ($emailType === 'client_alert' || $emailType === 'both') {
                $clientSuccess = $this->_sendClientTestEmail($banner, $moduleConfig, $xoopsConfig);
                $success = $clientSuccess || $success;
            }

            if ($success) {
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', 
                    'Test email(s) sent successfully. Check the configured email addresses.');
                return BANNERSTATS_FRAME_VIEW_SUCCESS;
            } else {
                $this->mActionForm->addErrorMessage('Failed to send test email(s). Check error logs for details.');
                return BANNERSTATS_FRAME_VIEW_INPUT;
            }

        } catch (Exception $e) {
            $this->mActionForm->addErrorMessage('An unexpected error occurred: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES));
            error_log("Bannerstats_BannerEmailTestAction: Exception - " . $e->getMessage());
            return BANNERSTATS_FRAME_VIEW_INPUT;
        }
    }

    /**
     * Send admin test email
     */
    protected function _sendAdminTestEmail($banner, $moduleConfig, $xoopsConfig)
    {
        try {
            $templateName = 'admin_test_notification.tpl';
            $subjectLangKey = '_AD_BANNERSTATS_TEST_EMAIL_SUBJECT';
            $subjectArgs = [$banner->get('name')];
            
            $bodyVarsProvider = function($object, $moduleConfig, $xoopsConfig) {
                return [
                    'BANNER_NAME' => $object->get('name'),
                    'BANNER_ID' => $object->get('bid'),
                    'BANNER_URL' => $object->get('url'),
                    'IMPRESSIONS_MADE' => $object->get('impmade'),
                    'IMPRESSIONS_TOTAL' => $object->get('imptotal'),
                    'CLICKS_MADE' => $object->get('clicks'),
                    'TEST_TYPE' => 'Admin Test Notification',
                    'SITE_NAME' => $xoopsConfig['sitename'],
                    'SITE_URL' => XOOPS_URL
                ];
            };

            $builder = new Bannerstats_AdminAlertMail($templateName, $subjectLangKey, $subjectArgs, $bodyVarsProvider);
            $director = new XCube_MailDirector($builder, $banner, $xoopsConfig, $moduleConfig);
            $director->constructMail();

            $mailer = $builder->getResult();
            $result = $mailer->send();
            
            if (!$result) {
                $errors = $mailer->getErrors();
                error_log("Bannerstats: Admin test email failed - " . implode(', ', $errors));
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Bannerstats: Admin test email exception - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send client test email
     */
    protected function _sendClientTestEmail($banner, $moduleConfig, $xoopsConfig)
    {
        try {
            // Get client info
            $client_handler = xoops_getmodulehandler('bannerclient', 'bannerstats');
            $client = $client_handler->get($banner->get('cid'));
            
            if (!$client || empty($client->get('email'))) {
                error_log("Bannerstats: No client or client email found for banner ID " . $banner->get('bid'));
                return false;
            }

            $templateName = 'client_test_notification.tpl';
            $subjectLangKey = '_AD_BANNERSTATS_CLIENT_TEST_EMAIL_SUBJECT';
            $subjectArgs = [$banner->get('name')];
            
            $bodyVarsProvider = function($object, $moduleConfig, $xoopsConfig) use ($client) {
                return [
                    'CLIENT_NAME' => $client->get('name'),
                    'BANNER_NAME' => $object->get('name'),
                    'BANNER_ID' => $object->get('bid'),
                    'BANNER_URL' => $object->get('url'),
                    'IMPRESSIONS_MADE' => $object->get('impmade'),
                    'IMPRESSIONS_TOTAL' => $object->get('imptotal'),
                    'CLICKS_MADE' => $object->get('clicks'),
                    'TEST_TYPE' => 'Client Test Notification',
                    'SITE_NAME' => $xoopsConfig['sitename'],
                    'SITE_URL' => XOOPS_URL
                ];
            };

            $builder = new Bannerstats_ClientAlertMail(
                $client->get('email'),
                $client->get('name'),
                $templateName,
                $subjectLangKey,
                $subjectArgs,
                $bodyVarsProvider
            );
            
            $director = new XCube_MailDirector($builder, $banner, $xoopsConfig, $moduleConfig);
            $director->constructMail();

            $mailer = $builder->getResult();
            $result = $mailer->send();
            
            if (!$result) {
                $errors = $mailer->getErrors();
                error_log("Bannerstats: Client test email failed - " . implode(', ', $errors));
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Bannerstats: Client test email exception - " . $e->getMessage());
            return false;
        }
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewInput($controller, $xoopsUser, $render);
        $render->setTemplateName('bannerstats_admin_email_test.html');
        
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('activeBanners', $this->mActiveBanners);
        $render->setAttribute('emailTypes', $this->mEmailTypes);
        
        if (is_object($this->mModuleObject)) {
            $render->setAttribute('module', $this->mModuleObject);
        }
        
        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewSuccess($controller, $xoopsUser, $render);
        $this->mRoot->mController->executeForward('./index.php?action=BannerEmailTest');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render);
        $render->setTemplateName('bannerstats_admin_email_test.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('activeBanners', $this->mActiveBanners);
        $render->setAttribute('emailTypes', $this->mEmailTypes);
        
        if (is_object($this->mModuleObject)) {
            $render->setAttribute('module', $this->mModuleObject);
        }
        
        return true;
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewCancel($controller, $xoopsUser, $render);
        $this->mRoot->mController->executeForward('./index.php');
    }

    protected function _getPagetitle()
    {
        return defined('_AD_BANNERSTATS_EMAIL_TEST_TITLE') ? _AD_BANNERSTATS_EMAILTEST_TITLE : 'Test Banner Email Notification';
    }
}