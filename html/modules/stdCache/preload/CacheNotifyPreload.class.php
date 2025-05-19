<?php
/**
 * StdCache preload with session-based notification.
 * Executes its main logic directly from postFilter()
 * for direct update of last cache alert time after sending email.
 * This file is the central configuration point for notification settings.
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

class StdCache_CacheNotifyPreload extends XCube_ActionFilter
{
    // Preload Configuration Constants

    /**
     * Defines the interval (in seconds) for both session re-checks and notification cooldown.
     * Example: 60 for testing, 3600 (1 hour) or 86400 (24 hours) for production.
     */
    public const SESSION_CHECK_INTERVAL = 86400; // Preload interval constant

    // Mail Notification Settings

    /** Language constant for the email subject. */
    public const MAIL_SUBJECT_LANG_CONST = '_AD_STDCACHE_MAIL_SUBJECT_CACHE_LIMIT';
    /** Default email subject if language constant is not found. %s is sitename. */
    public const MAIL_SUBJECT_DEFAULT = '[%s] stdCache Admin Notification';

    /** Language constant for the 'limit reached' message body. */
    public const MAIL_MSG_LIMIT_REACHED_LANG_CONST = '_AD_STDCACHE_MAIL_MSG_LIMIT_REACHED';
    /** Default 'limit reached' message. %s are cache size and limit. */
    public const MAIL_MSG_LIMIT_REACHED_DEFAULT = "The cache usage is currently %s. This is approaching or has exceeded the configured notification limit of %s.";

    /** Language constant for a generic notification message. */
    public const MAIL_MSG_GENERIC_LANG_CONST = '_AD_STDCACHE_MAIL_MSG_GENERIC';
    /** Default generic notification message. */
    public const MAIL_MSG_GENERIC_DEFAULT = "Please review the system status for the stdCache module.";

    /** Module-specific mail template filename (e.g., 'cache_limit_notification.tpl'). */
    public const MAIL_TEMPLATE_NAME_MODULE = 'cache_limit_notification.tpl';

    /** Module configuration key for the admin group to notify. */
    public const MAIL_ADMIN_GROUP_PREF_KEY = 'admin_notification_group';

    /** Comma-separated list of additional email addresses to notify. */
    public const MAIL_EXTRA_ADMIN_EMAILS = '';

    // End Preload Configuration Constants


    public function __construct(&$controller)
    {
        parent::__construct($controller);
    }

    public function preBlockFilter()
    {
    }

    public function postFilter()
    {
        $this->checkAndNotifyIfNeeded();
    }

    public function checkAndNotifyIfNeeded()
    {
        // Time-based session check using the Preload interval
        $currentTimeForSessionCheck = time();
        if (isset($_SESSION['stdcache_preload_last_checked_time_v2.5']) &&
            (($currentTimeForSessionCheck - $_SESSION['stdcache_preload_last_checked_time_v2.5']) < self::SESSION_CHECK_INTERVAL)) {
            return; // Silently exit
        }
        $_SESSION['stdcache_preload_last_checked_time_v2.5'] = $currentTimeForSessionCheck;
        // END Time-based session check

        // Get module configurations
        $moduleHandler = xoops_gethandler('module');
        if (!is_object($moduleHandler)) { $this->logPreloadError('Failed to get module_handler.'); return; }
        $stdCacheModuleObject = $moduleHandler->getByDirname('stdCache');
        if (!is_object($stdCacheModuleObject)) { $this->logPreloadError('Failed to get stdCache module object.'); return; }
        $configHandler = xoops_gethandler('config');
        if (!is_object($configHandler)) { $this->logPreloadError('Failed to get config_handler.'); return; }
        
        $moduleConfig = $configHandler->getConfigsByCat(0, $stdCacheModuleObject->getVar('mid'));
        
        // Silently exit if notifications are disabled
        if (empty($moduleConfig['cache_limit_alert_enable'])) {
            return;
        }

        // Check notification cooldown interval
        $lastNotifyTime = (int)($moduleConfig['last_cache_alert_time'] ?? 0);
        $currentTime = time(); // Current time for notification logic
    
        $notificationCooldownInterval = self::SESSION_CHECK_INTERVAL; // Uses the Preload interval
        
        if (($currentTime - $lastNotifyTime) <= $notificationCooldownInterval) {
            return;
        }

        // CacheManager is required
        require_once __DIR__ . '/../admin/class/CacheManager.class.php';
        if (!class_exists('stdCache_CacheManager')) { $this->logPreloadError('stdCache_CacheManager class not found after attempt to include.'); return; }

        // AdminNotificationMailBuilder is required to mail
        require_once __DIR__ . '/../admin/class/AdminNotificationMailBuilder.class.php';
         if (!class_exists('StdCache_AdminNotificationMailBuilder')) { $this->logPreloadError('AdminNotificationMailBuilder class not found after attempt to include.'); return; }


        try {
            $cacheManager = new stdCache_CacheManager(); 
        } catch (Exception $e) {
            $this->logPreloadError('Failed to initialize CacheManager - ' . $e->getMessage()); return;
        }
        
        $currentStats = $cacheManager->getCacheStats();
        $smartyCacheNotificationLimit = (int)($moduleConfig['cache_limit_alert_trigger'] ?? PHP_INT_MAX);
        $compiledTemplatesLimit = (int)($moduleConfig['cache_limit_compiled'] ?? PHP_INT_MAX);
        $smartyCacheSize = (int)($currentStats['cache_size'] ?? 0);
        $compiledSize = (int)($currentStats['compiled_size'] ?? 0);

        $limitReached = false;
        $notificationType = ''; 
        $relevantSize = 0;
        $relevantLimit = 0;

        if ($smartyCacheSize > $smartyCacheNotificationLimit) {
            $limitReached = true; $notificationType = 'Smarty Cache Limit Exceeded';
            $relevantSize = $smartyCacheSize; $relevantLimit = $smartyCacheNotificationLimit;
        } elseif ($compiledSize > $compiledTemplatesLimit) {
            $limitReached = true; $notificationType = 'Compiled Templates Limit Exceeded';
            $relevantSize = $compiledSize; $relevantLimit = $compiledTemplatesLimit;
        }

        if ($limitReached) {
            $this->logPreloadInfo("A cache limit was reached ({$notificationType}). Proceeding to send notification.");
            $emailSent = $this->sendAutomatedNotification($moduleConfig, $notificationType, $relevantSize, $relevantLimit, $cacheManager);

            if ($emailSent) {
                $this->logPreloadInfo("Email sent. Attempting DIRECT update of last_cache_alert_time.");
                $updateSuccess = $cacheManager->updateLastNotificationTime($currentTime);
                if ($updateSuccess) {
                    $this->logPreloadInfo("DIRECT update of last_cache_alert_time to " . date('Y-m-d H:i:s', $currentTime) . " SUCCEEDED.");
                } else {
                    $this->logPreloadError("DIRECT update of last_cache_alert_time FAILED. Falling back to AJAX trigger.");
                    $this->_triggerUpdateLastNotificationTimeViaAjax($currentTime);
                }
            } else {
                $this->logPreloadError("Email sending failed (reported by sendAutomatedNotification). Not attempting timestamp update.");
            }
        }
    }

    protected function _triggerUpdateLastNotificationTimeViaAjax($timestamp)
    {
        $root = XCube_Root::getSingleton();
        if (!isset($GLOBALS['xoopsSecurity']) || !is_object($GLOBALS['xoopsSecurity'])) {
            $this->logPreloadError("CRITICAL - xoopsSecurity object not available for AJAX. Falling back to session deferral.");
            $_SESSION['stdcache_deferred_update_last_notify_time'] = $timestamp;
            return;
        }

        if (!isset($_SESSION)) {
            $this->logPreloadError("Preload AJAX Trigger: SESSION IS NOT SET before creating token!");
        }
        $token = $GLOBALS['xoopsSecurity']->createToken(); 

        $actionUrl = XOOPS_URL . "/modules/stdCache/admin/index.php?action=UpdateLastNotification";
        $escapedActionUrl = htmlspecialchars($actionUrl, ENT_QUOTES, 'UTF-8');
        $escapedToken = htmlspecialchars($token ?? '', ENT_QUOTES, 'UTF-8');
        $escapedTimestamp = (int)$timestamp;
        
        $jsCode = "
            document.addEventListener('DOMContentLoaded', function() {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{$escapedActionUrl}', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () { 
                    if (xhr.readyState === 4) { 
                        if (xhr.status === 200) {
                            try { var response = JSON.parse(xhr.responseText);
                                if (response.success) { console.log('stdCache Preload: AJAX last_cache_alert_time update SUCCESS.', response); }
                                else { console.error('stdCache Preload: AJAX update of last_cache_alert_time FAILED on server.', response.message, xhr.responseText); }
                            } catch (e) { console.error('stdCache Preload: Error parsing AJAX response for update.', e, xhr.responseText); }
                        } else { console.error('stdCache Preload: AJAX request for update failed with HTTP status ' + xhr.status, xhr.responseText); }
                    }
                };
                var params = 'timestamp={$escapedTimestamp}&token={$escapedToken}';
                xhr.send(params);
            });
        ";
        if (isset($root->mContext->mHeadScript) && is_object($root->mContext->mHeadScript) && method_exists($root->mContext->mHeadScript, 'addScript')) {
            $root->mContext->mHeadScript->addScript($jsCode);
            $this->logPreloadInfo("JavaScript for AJAX update added to mHeadScript (fallback).");
        } else {
            $this->logPreloadError("mHeadScript not available for AJAX. Falling back to session deferral.");
            $_SESSION['stdcache_deferred_update_last_notify_time'] = $timestamp;
        }
    }

    private function sendAutomatedNotification(array $moduleConfig, string $notificationTypeString, int $currentSize, int $limit, stdCache_CacheManager $cacheManager)
    {
        if (!class_exists('StdCache_AdminNotificationMailBuilder')) {
             $this->logPreloadError('Mail builder class StdCache_AdminNotificationMailBuilder not found. Aborting send.');
             return false;
        }
        if (!class_exists('XCube_MailDirector')) {
            $this->logPreloadError('Mail director class XCube_MailDirector not found. Aborting send.');
            return false;
        }

        $mailObjectData = [
            'CACHE_SIZE_RAW'       => $currentSize, 'CACHE_LIMIT_RAW'      => $limit,
            'NOTIFICATION_TYPE'    => $notificationTypeString . ' (Automated Alert)',
            'CACHE_SIZE_FORMATTED' => $cacheManager->formatSize($currentSize),
            'CACHE_LIMIT_FORMATTED'=> $cacheManager->formatSize($limit),
        ];
        $root = XCube_Root::getSingleton();
        $xoopsConfig = $root->mContext->getXoopsConfig();
        try {
            $builder = new StdCache_AdminNotificationMailBuilder(); // This uses constants from this Preload
            $director = new XCube_MailDirector($builder, $mailObjectData, $xoopsConfig, $moduleConfig);
            $director->constructMail();
            $mailer = $builder->getResult();
            if ($mailer->send()) {
                $this->logPreloadInfo('Automated cache limit notification sent successfully.');
                return true;
            } else {
                $this->logPreloadError('Failed to send email. Errors: ' . implode(', ', $mailer->getErrors()));
                return false;
            }
        } catch (Exception $e) {
            $this->logPreloadError('Exception during email send: ' . $e->getMessage());
            return false;
        }
    }

    private function logPreloadError($message) {
        error_log("STDCACHE_LOG (error) Preload: " . $message);
    }
    private function logPreloadInfo($message) {
        error_log("STDCACHE_LOG (info) Preload: " . $message);
    }
}
