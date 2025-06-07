<?php
/**
 * stdCache Admin Notification Mail Builder
 * 
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// StdCache_CacheNotifyPreload load is CRUCIAL
require_once XOOPS_MODULE_PATH . '/stdCache/preload/CacheNotifyPreload.class.php';
require_once XOOPS_ROOT_PATH . '/core/XCube_MailBuilder.class.php';
require_once XOOPS_MODULE_PATH . '/stdCache/admin/class/CacheManager.class.php';

class StdCache_AdminNotificationMailBuilder extends XCube_AdminNotificationMailBuilder
{
    // Static properties to hold configuration values
    // initialized by initializeEffectiveConfig()
    private static bool $sIsConfigInitialized = false;

    public static int $sEffectiveIntervalSeconds;
    public static string $sEffectiveSubjectLangConst;
    public static string $sEffectiveSubjectDefault;
    public static string $sEffectiveMsgLimitReachedLangConst;
    public static string $sEffectiveMsgLimitReachedDefault;
    public static string $sEffectiveMsgGenericLangConst;
    public static string $sEffectiveMsgGenericDefault;
    public static string $sEffectiveTemplateNameModule;
    public static ?string $sEffectiveAdminGroupPrefKey; // Can be null
    public static string $sEffectiveExtraAdminEmails;

    // FALLBACK constants. These are used if Preload is not available
    // true compile-time constants
    private const FALLBACK_INTERVAL_SECONDS = 3600;
    private const FALLBACK_SUBJECT_LANG_CONST = '_AD_STDCACHE_MAIL_SUBJECT_CACHE_LIMIT_FALLBACK';
    private const FALLBACK_SUBJECT_DEFAULT = '[%s] stdCache Notification (Fallback)';
    private const FALLBACK_MSG_LIMIT_REACHED_LANG_CONST = '_AD_STDCACHE_MAIL_MSG_LIMIT_REACHED_FALLBACK';
    private const FALLBACK_MSG_LIMIT_REACHED_DEFAULT = "Cache limit reached: %s of %s (Fallback).";
    private const FALLBACK_MSG_GENERIC_LANG_CONST = '_AD_STDCACHE_MAIL_MSG_GENERIC_FALLBACK';
    private const FALLBACK_MSG_GENERIC_DEFAULT = "Generic stdCache notice (Fallback).";
    private const FALLBACK_TEMPLATE_NAME_MODULE = 'admin_notification.tpl';
    private const FALLBACK_ADMIN_GROUP_PREF_KEY = null;
    private const FALLBACK_EXTRA_ADMIN_EMAILS = '';


    /**
     * Initializes the static configuration properties.
     * This method should be called once. It checks for StdCache_CacheNotifyPreload
     * and its constants, falling back to local defaults if necessary
     */
    private static function initializeEffectiveConfig(): void
    {
        if (self::$sIsConfigInitialized) {
            return;
        }

        $preloadClassAvailable = class_exists('StdCache_CacheNotifyPreload', false);

        // Interval
        self::$sEffectiveIntervalSeconds = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::SESSION_CHECK_INTERVAL'))
            ? StdCache_CacheNotifyPreload::SESSION_CHECK_INTERVAL
            : self::FALLBACK_INTERVAL_SECONDS;

        // Subject Language Constant
        self::$sEffectiveSubjectLangConst = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_SUBJECT_LANG_CONST'))
            ? StdCache_CacheNotifyPreload::MAIL_SUBJECT_LANG_CONST
            : self::FALLBACK_SUBJECT_LANG_CONST;

        // Subject Default String
        self::$sEffectiveSubjectDefault = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_SUBJECT_DEFAULT'))
            ? StdCache_CacheNotifyPreload::MAIL_SUBJECT_DEFAULT
            : self::FALLBACK_SUBJECT_DEFAULT;

        // Message Limit Reached Language Constant
        self::$sEffectiveMsgLimitReachedLangConst = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_MSG_LIMIT_REACHED_LANG_CONST'))
            ? StdCache_CacheNotifyPreload::MAIL_MSG_LIMIT_REACHED_LANG_CONST
            : self::FALLBACK_MSG_LIMIT_REACHED_LANG_CONST;

        // Message Limit Reached Default String
        self::$sEffectiveMsgLimitReachedDefault = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_MSG_LIMIT_REACHED_DEFAULT'))
            ? StdCache_CacheNotifyPreload::MAIL_MSG_LIMIT_REACHED_DEFAULT
            : self::FALLBACK_MSG_LIMIT_REACHED_DEFAULT;

        // Message Generic Language Constant
        self::$sEffectiveMsgGenericLangConst = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_MSG_GENERIC_LANG_CONST'))
            ? StdCache_CacheNotifyPreload::MAIL_MSG_GENERIC_LANG_CONST
            : self::FALLBACK_MSG_GENERIC_LANG_CONST;

        // Message Generic Default String
        self::$sEffectiveMsgGenericDefault = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_MSG_GENERIC_DEFAULT'))
            ? StdCache_CacheNotifyPreload::MAIL_MSG_GENERIC_DEFAULT
            : self::FALLBACK_MSG_GENERIC_DEFAULT;

        // Template Name
        self::$sEffectiveTemplateNameModule = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_TEMPLATE_NAME_MODULE'))
            ? StdCache_CacheNotifyPreload::MAIL_TEMPLATE_NAME_MODULE
            : self::FALLBACK_TEMPLATE_NAME_MODULE;

        // Admin Group Preference Key
        self::$sEffectiveAdminGroupPrefKey = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_ADMIN_GROUP_PREF_KEY'))
            ? StdCache_CacheNotifyPreload::MAIL_ADMIN_GROUP_PREF_KEY
            : self::FALLBACK_ADMIN_GROUP_PREF_KEY;

        // Extra Admin Emails
        self::$sEffectiveExtraAdminEmails = ($preloadClassAvailable && defined('StdCache_CacheNotifyPreload::MAIL_EXTRA_ADMIN_EMAILS'))
            ? StdCache_CacheNotifyPreload::MAIL_EXTRA_ADMIN_EMAILS
            : self::FALLBACK_EXTRA_ADMIN_EMAILS;

        self::$sIsConfigInitialized = true;
    }

    /**
     * @var stdCache_CacheManager|null
     */
    protected ?stdCache_CacheManager $mCacheManagerInstance = null;

    public function __construct()
    {
        parent::__construct('stdCache'); // Pass module dirname to parent
        self::initializeEffectiveConfig(); // static config initialized

        if (class_exists('stdCache_CacheManager')) {
            try {
                $this->mCacheManagerInstance = new stdCache_CacheManager();
            } catch (Exception $e) {
                error_log('STDCACHE_LOG (critical) AdminMailBuilder: Failed to initialize CacheManager - ' . $e->getMessage());
                $this->mCacheManagerInstance = null;
            }
        }
    }

    public function setSubject($object, $xoopsConfig): void
    {
        $subjectLangConstKey = self::$sEffectiveSubjectLangConst;
        $subjectDefaultString = self::$sEffectiveSubjectDefault;

        $actualSubjectString = (defined($subjectLangConstKey) && constant($subjectLangConstKey) !== $subjectLangConstKey)
                               ? constant($subjectLangConstKey)
                               : $subjectDefaultString;
        $this->mMailer->setSubject(sprintf($actualSubjectString, $xoopsConfig['sitename']));
    }

    public function setBody($object, $xoopsConfig): void
    {
        parent::setBody($object, $xoopsConfig);

        $cacheSizeFormatted = 'N/A';
        $cacheLimitFormatted = 'N/A';
        $notificationTypeString = 'General Admin Notice';

        if (is_array($object)) {
            // extract data from $object as before
            $cacheSizeFormatted = $object['CACHE_SIZE_FORMATTED'] ?? 'N/A';
            $cacheLimitFormatted = $object['CACHE_LIMIT_FORMATTED'] ?? 'N/A';
            $notificationTypeString = $object['NOTIFICATION_TYPE'] ?? $notificationTypeString;

            if ($cacheSizeFormatted === 'N/A' && isset($object['CACHE_SIZE_RAW']) && $this->mCacheManagerInstance) {
                $cacheSizeFormatted = $this->mCacheManagerInstance->formatSize($object['CACHE_SIZE_RAW']);
            }
            if ($cacheLimitFormatted === 'N/A' && isset($object['CACHE_LIMIT_RAW']) && $this->mCacheManagerInstance) {
                $cacheLimitFormatted = $this->mCacheManagerInstance->formatSize($object['CACHE_LIMIT_RAW']);
            }
        } else {
            // fallback logic as before
            error_log('STDCACHE_LOG (warning) AdminMailBuilder: setBody received an unexpected object type. Expected array, got: ' . gettype($object));
            if ($this->mCacheManagerInstance) {
                 $currentStats = $this->mCacheManagerInstance->getCacheStats();
                 $cacheSizeFormatted = $this->mCacheManagerInstance->formatSize($currentStats['cache_size'] ?? 0);
                 $limitForFormatting = $currentStats['cache_limit_alert_trigger'] ?? ($this->mCacheManagerInstance->getConfig('cache_limit_alert_trigger', 0));
                 $cacheLimitFormatted = $this->mCacheManagerInstance->formatSize($limitForFormatting);
                 $notificationTypeString = 'Cache Status Update (Fallback Data)';
            }
        }

        $this->mMailer->assign('CACHE_SIZE', $cacheSizeFormatted);
        $this->mMailer->assign('CACHE_LIMIT', $cacheLimitFormatted);
        $this->mMailer->assign('NOTIFICATION_TYPE', $notificationTypeString);
        $this->mMailer->assign('NOTIFICATION_TITLE', $notificationTypeString);

        $messageBody = '';
        if (strpos($notificationTypeString, 'Limit Exceeded') !== false || strpos($notificationTypeString, 'Test Cache Limit Notification') !== false) {
            $msgLimitReachedLangKey = self::$sEffectiveMsgLimitReachedLangConst;
            $msgLimitReachedDefault = self::$sEffectiveMsgLimitReachedDefault;
            $actualMessageString = (defined($msgLimitReachedLangKey) && constant($msgLimitReachedLangKey) !== $msgLimitReachedLangKey)
                                   ? constant($msgLimitReachedLangKey)
                                   : $msgLimitReachedDefault;
            $messageBody = sprintf(
                $actualMessageString,
                $cacheSizeFormatted,
                $cacheLimitFormatted
            );
        } else {
            $msgGenericLangKey = self::$sEffectiveMsgGenericLangConst;
            $msgGenericDefault = self::$sEffectiveMsgGenericDefault;
            $messageBody = (defined($msgGenericLangKey) && constant($msgGenericLangKey) !== $msgGenericLangKey)
                           ? constant($msgGenericLangKey)
                           : $msgGenericDefault;
        }
        $this->mMailer->assign('NOTIFICATION_MESSAGE', $messageBody);

        $adminLink = XOOPS_URL . '/modules/stdCache/admin/index.php?action=CacheStats';
        $this->mMailer->assign('ADMIN_MODULE_URL', $adminLink);
        $this->mMailer->assign('LINK_TO_MODULE_ADMIN', $adminLink);
        $this->mMailer->assign('ADMIN_URL', $adminLink);
    }

    public function setTemplateName(): void
    {
        $root = XCube_Root::getSingleton();
        $language = $root->mContext->getXoopsConfig('language');
        
        $moduleTemplateFilename = self::$sEffectiveTemplateNameModule;
        
        $moduleTemplatePath = XOOPS_MODULE_PATH . '/' . $this->mModuleName .
                              '/language/' . $language .
                              '/mail_template/' . $moduleTemplateFilename;

        if ($moduleTemplateFilename && file_exists($moduleTemplatePath)) {
            $this->mMailer->setTemplate($moduleTemplateFilename);
        } else {
            if ($moduleTemplateFilename && $moduleTemplateFilename !== self::FALLBACK_TEMPLATE_NAME_MODULE) {
                 error_log('STDCACHE_LOG (info) AdminMailBuilder: Module mail template "' . htmlspecialchars($moduleTemplateFilename, ENT_QUOTES) . '" not found. Falling back.');
            }
            parent::setTemplateName(); 
        }
    }

     public function setToUsers($object, $moduleConfig): void
    {
        $allToEmails = [];
        $memberHandler = null;

        $adminGroupPrefKey = self::$sEffectiveAdminGroupPrefKey;
        $extraAdminEmails = self::$sEffectiveExtraAdminEmails;

        if ($adminGroupPrefKey && !empty($moduleConfig[$adminGroupPrefKey])) {
            // logic as before, using $adminGroupPrefKey
            $customGroupId = (int)$moduleConfig[$adminGroupPrefKey];
            if ($customGroupId > 0) {
                $memberHandler = $memberHandler ?? xoops_gethandler('member');
                if (is_object($memberHandler)) {
                    $customAdminGroupObject = $memberHandler->getGroup($customGroupId);
                    if (is_object($customAdminGroupObject)) {
                        $usersInGroup = $memberHandler->getUsersByGroup($customGroupId);
                        if (is_array($usersInGroup)) {
                            foreach ($usersInGroup as $user) {
                                if (is_object($user) && $user->getVar('email')) {
                                    $allToEmails[] = $user->getVar('email');
                                }
                            }
                        }
                    } else {
                        error_log('STDCACHE_LOG (warning) AdminMailBuilder: Custom admin group ID ' . $customGroupId . ' (from pref "' . htmlspecialchars($adminGroupPrefKey, ENT_QUOTES) . '") not found.');
                    }
                } else {
                     error_log('STDCACHE_LOG (error) AdminMailBuilder: Failed to get member_handler for custom admin group.');
                }
            }
        }

        if ($extraAdminEmails) {
            // logic as before, using $extraAdminEmails
            $extraEmailsArray = array_map('trim', explode(',', $extraAdminEmails));
            foreach ($extraEmailsArray as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) { 
                    $allToEmails[] = $email;
                } else {
                    error_log('STDCACHE_LOG (warning) AdminMailBuilder: Invalid extra email format in EXTRA_ADMIN_EMAILS: ' . htmlspecialchars($email, ENT_QUOTES));
                }
            }
        }

        if (!empty($allToEmails)) {
            $uniqueEmails = array_unique($allToEmails);
            $this->mMailer->setToEmails($uniqueEmails);
        } else {
            parent::setToUsers($object, $moduleConfig);
        }
    }
}
