<?php
/**
 * Standard cache - Module for XCL
 * CacheNotifyAction.class.php
 *
 * Action to test admin notifications using the XCube_MailBuilder system.
 * (Group subscription functionality has been removed).
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) & Gemini Code Assist
 * @copyright  2005-2024 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once __DIR__ . '/../class/Action.class.php';
require_once __DIR__ . '/../forms/CacheNotifyForm.class.php'; // Still needed for the CSRF token
require_once __DIR__ . '/../class/CacheManager.class.php';

// Core Mail Builder (contains XCube_MailDirector, XCube_AbstractMailBuilder, etc.)
require_once XOOPS_ROOT_PATH . '/core/XCube_MailBuilder.class.php';
// stdCache module's specific mail builder
require_once __DIR__ . '/../class/AdminNotificationMailBuilder.class.php';
// XCube_DelegateUtils if not autoloaded or included by the core
if (!class_exists('XCube_DelegateUtils') && file_exists(XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php')) {
    require_once XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php';
}

class stdCache_CacheNotifyAction extends stdCache_Action
{
    /**
     * @var XCube_Root
     */
    protected $mRoot = null;

    /**
     * @var CacheNotifyForm
     */
    protected $mActionForm = null;

    /**
     * @var stdCache_CacheManager
     */
    protected $mCacheManager = null;

    /**
     * @var XoopsModule
     */
    protected $mModuleObject = null; // Still useful for module context in mail builder

    public function __construct($adminFlag = false)
    {
        parent::__construct($adminFlag);
        $this->mRoot = XCube_Root::getSingleton();
    }

    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
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

        $this->mActionForm = new CacheNotifyForm();
        $this->mActionForm->prepare();
        
        try {
            $this->mCacheManager = new stdCache_CacheManager();
        } catch (Exception $e) {
            error_log("stdCache_CacheNotifyAction: Failed to initialize CacheManager - " . $e->getMessage());
            // Add error to form or redirect
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', 'Error: CacheManager could not be initialized.');
            $this->mRoot->mController->executeForward(XOOPS_URL . '/admin.php');
            return false;
        }

        $module_handler = xoops_gethandler('module');
        if (!is_object($module_handler)) {
             error_log("stdCache_CacheNotifyAction: FAILED to get module_handler.");
             XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', 'Error: Module handler not available.');
             $this->mRoot->mController->executeForward(XOOPS_URL . '/admin.php');
             return false;
        }
        $this->mModuleObject = $module_handler->getByDirname('stdCache');

        if (!is_object($this->mModuleObject)) {
            error_log("stdCache_CacheNotifyAction: Failed to load stdCache module object.");
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', 'Error: stdCache module object not found.');
            $this->mRoot->mController->executeForward(XOOPS_URL . '/admin.php');
            return false;
        }

        return true;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return STDCACHE_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $cmd = $this->mRoot->mContext->mRequest->getRequest('cmd');

        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            
            if ($cmd === 'updateTimestamp') {
                // debug step for 'updateTimestamp' to test the update logic
                $this->mActionForm->fetch(); 
                return $this->_executeUpdateTimestamp($controller, $xoopsUser);
            }
            
            // For all other POST commands (like 'testNotification')
            $this->mActionForm->fetch();
            $this->mActionForm->validate();

            if ($this->mActionForm->hasError()) {
                // An error message (e.g., token error) added by validate()
                return STDCACHE_FRAME_VIEW_INPUT; // Re-display form with errors
            }

            // If we reach here, the token was valid for other commands
            if ($cmd === 'testNotification') {
                return $this->_executeTestNotification($controller, $xoopsUser);
            } else {
                // Unknown POST command
                $this->mActionForm->addErrorMessage('Unknown action submitted.');
                return STDCACHE_FRAME_VIEW_INPUT;
            }

        } elseif ($cmd === 'testNotification') {
            // Handle GET request for testNotification (e.g., redirect or show error)
            $this->mRoot->mController->executeRedirect(XOOPS_URL . '/modules/stdCache/admin/index.php?action=CacheNotify', 1, 'Invalid request method for test notification.');
            return STDCACHE_FRAME_VIEW_NONE;
        }

        // Default view for GET requests or if no specific command
        return STDCACHE_FRAME_VIEW_INPUT;
    }

    /**
     * Handles the logic for updating the last_cache_alert_time
     * Adds messages directly to $this->mActionForm for display
     */
    protected function _executeUpdateTimestamp(&$controller, &$xoopsUser)
    {
        $this->mCacheManager->logOperation('Attempting manual update of last_cache_alert_time.', 'info');

        $manualTimestampValue = $this->mRoot->mContext->mRequest->getRequest('manual_timestamp');
        
        // Validate if it's a number, default to 0 if empty or invalid to reset
        if ($manualTimestampValue === '' || !is_numeric($manualTimestampValue)) {
            $newNotifyTime = 0;
            $this->mCacheManager->logOperation('Manual timestamp was empty or invalid, defaulting to 0.', 'info');
        } else {
            $newNotifyTime = (int)$manualTimestampValue;
        }

        if (!$this->mCacheManager) {
            // This should have been caught in prepare()
            $this->mActionForm->addErrorMessage('CacheManager not available. Cannot update timestamp.');
            $this->mCacheManager->logOperation('Update timestamp failed: CacheManager not available.', 'error');
            return STDCACHE_FRAME_VIEW_INPUT;
        }

        if ($this->mCacheManager->updateLastNotificationTime($newNotifyTime)) {
            // Use XCube_DelegateUtils for global admin messages
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', 
                sprintf('Last notification time successfully updated to: %s (%s)', 
                        $newNotifyTime, 
                        ($newNotifyTime > 0 ? date('Y-m-d H:i:s', $newNotifyTime) : 'Reset')
                )
            );
            $this->mCacheManager->logOperation(sprintf('Last notification time updated to %d.', $newNotifyTime), 'info');
        } else {
            // saveConfig in CacheManager should log the specific failure reason to STDCACHE_LOG
            $this->mActionForm->addErrorMessage('Failed to update the last notification time in the configuration. Check stdCache logs.');
            $this->mCacheManager->logOperation('Failed to update last_cache_alert_time via manual form.', 'error');
        }
        
        return STDCACHE_FRAME_VIEW_INPUT;
    }

    /**
     * Handles the logic for triggering a test notification using the new XCube_MailBuilder
     * Adds messages directly to $this->mActionForm for display
     */
    protected function _executeTestNotification(&$controller, &$xoopsUser)
    {
        if (!$this->mCacheManager) {
            $this->mActionForm->addErrorMessage('CacheManager not available for test notification. Critical error.');
            return STDCACHE_FRAME_VIEW_INPUT;
        }
        $this->mCacheManager->logOperation('Test notification button clicked (using XCube_MailBuilder).', 'info');

        $moduleConfigs = $this->mCacheManager->getConfigs();
        if (empty($moduleConfigs['cache_limit_alert_enable'])) {
            $this->mActionForm->addErrorMessage(
                defined('_AD_STDCACHE_TEST_ALERT_DISABLED') ? _AD_STDCACHE_TEST_ALERT_DISABLED : 'Notifications are disabled in module settings. Test cannot proceed.'
            );
            $this->mCacheManager->logOperation('Test notification aborted: Notifications disabled in preferences.', 'warning');
            return STDCACHE_FRAME_VIEW_INPUT; // Stay on page with error
        }

        $this->mCacheManager->logOperation('Proceeding to trigger test notification via XCube_MailBuilder.', 'info');
        $notificationLimit = (int)($moduleConfigs['cache_limit_alert_trigger'] ?? 40000000); // Default 40MB
        $simulatedCacheSize = $notificationLimit + 1000; // Simulate exceeding the limit

        $xoopsConfig = $this->mRoot->mContext->mXoopsConfig ?? $GLOBALS['xoopsConfig'];

        // Prepare the $object for the mail builder
        $mailObjectData = [
            'CACHE_SIZE_RAW'      => $simulatedCacheSize,
            'CACHE_LIMIT_RAW'     => $notificationLimit,
            'NOTIFICATION_TYPE'   => 'Test Cache Limit Notification (via XCube_MailBuilder)',
            'CACHE_SIZE_FORMATTED' => $this->mCacheManager ? $this->mCacheManager->formatSize($simulatedCacheSize) : 'N/A',
            'CACHE_LIMIT_FORMATTED'=> $this->mCacheManager ? $this->mCacheManager->formatSize($notificationLimit) : 'N/A',
        ];

        if (!class_exists('StdCache_AdminNotificationMailBuilder') || !class_exists('XCube_MailDirector')) {
            $this->mActionForm->addErrorMessage('Mail builder classes not found. Test cannot proceed.');
            $this->mCacheManager->logOperation('Test notification failed: Mail builder classes missing.', 'error');
            return STDCACHE_FRAME_VIEW_INPUT;
        }

        try {
            $builder = new StdCache_AdminNotificationMailBuilder();
            $director = new XCube_MailDirector($builder, $mailObjectData, $xoopsConfig, $moduleConfigs);
            $director->constructMail();

            $mailer = $builder->getResult();
            if ($mailer->send()) {
                // Use global message for success
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', defined('_AD_STDCACHE_TEST_MAIL_SUCCESS') ? _AD_STDCACHE_TEST_MAIL_SUCCESS : 'Test notification email sent successfully via XCube_MailBuilder. Check admin email.');
                $this->mCacheManager->logOperation('Test notification email sent successfully via XCube_MailBuilder.', 'info');

                return STDCACHE_FRAME_VIEW_SUCCESS;

            } else {
                $errorMessages = $mailer->getErrors();
                $this->mActionForm->addErrorMessage(
                    (defined('_AD_STDCACHE_TEST_MAIL_ERROR') ? _AD_STDCACHE_TEST_MAIL_ERROR : 'Failed to send test notification email via XCube_MailBuilder.') .
                    (!empty($errorMessages) ? ' Errors: ' . implode(', ', $errorMessages) : '')
                );
                $this->mCacheManager->logOperation('Failed to send test notification email via XCube_MailBuilder. Errors: ' . implode(', ', $errorMessages), 'error');

                return STDCACHE_FRAME_VIEW_INPUT;
            }
        } catch (Exception $e) {
            $this->mActionForm->addErrorMessage('An unexpected error occurred while sending the test email: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES));
            $this->mCacheManager->logOperation('Test notification exception: ' . $e->getMessage(), 'error');

            return STDCACHE_FRAME_VIEW_INPUT; // Stay on page with error 
        }
        
        // Always return to the input view to show messages
       // return STDCACHE_FRAME_VIEW_INPUT;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewInput($controller, $xoopsUser, $render);
        $render->setTemplateName('stdcache_admin_cache_notify.html');
        
        $render->setAttribute('actionForm', $this->mActionForm); 
        
        if (is_object($this->mModuleObject)) {
            $render->setAttribute('module', $this->mModuleObject);
        }

        // Fetch module configurations
        $moduleConfigs = [];
        if ($this->mCacheManager) {
            $moduleConfigs = $this->mCacheManager->getConfigs();
        }
        // Assign the notification enable status to the template
        // Use !empty() to treat 0, null, false, etc., as disabled
        $isNotificationEnabled = !empty($moduleConfigs['cache_limit_alert_enable']);
        $render->setAttribute('isNotificationEnabled', $isNotificationEnabled);

        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewSuccess($controller, $xoopsUser, $render);
        
        $this->mRoot->mController->executeForward('./index.php?action=CacheNotify');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render);
        $render->setTemplateName('stdcache_admin_cache_notify.html');
        $render->setAttribute('actionForm', $this->mActionForm); 
        if (is_object($this->mModuleObject)) {
            $render->setAttribute('module', $this->mModuleObject);
        }
        return true;
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewCancel($controller, $xoopsUser, $render);
        $this->mRoot->mController->executeForward('./index.php?action=CacheStats');
    }

    protected function _getPagetitle()
    {
        return defined('_AD_STDCACHE_TEST_TITLE') ? _AD_STDCACHE_TEST_TITLE : 'Test Admin Notification';
    }
}
