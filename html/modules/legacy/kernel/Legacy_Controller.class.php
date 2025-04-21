<?php
/**
 *
 * @package     Legacy
 * @author      Nobuhiro YASUTOMI, PHP8
 * @version     $Id: Legacy_Controller.class.php,v 1.22 2008/11/14 09:45:23 mumincacao Exp $
 * @copyright   (c) 2005-2025 The XOOPSCube Project
 * @license     GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
if (!defined('XOOPS_TRUST_PATH')) {
    echo 'Since XOOPSCube Legacy version 2.2 a TRUST_PATH is required in mainfile.php';
    exit();
}

define('LEGACY_MODULE_VERSION', '2.5.0');

define('LEGACY_CONTROLLER_STATE_PUBLIC', 1);
define('LEGACY_CONTROLLER_STATE_ADMIN', 2);

define('LEGACY_XOOPS_MODULE_MANIFESTO_FILENAME', 'xoops_version.php');

require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_BlockProcedure.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Legacy_Utils.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Enum.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_Identity.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_RoleManager.class.php';

require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_CacheInformation.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_PublicControllerStrategy.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_TextFilter.class.php';
require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Legacy_Debugger.class.php';

/**
 * This class is a virtual controller that ensures compatibility with XOOPS 2.0.x.
 *
 * [NOTICE]
 * XOOPS 2.0.x can switch to public mode and control panel mode. This controller
 * emulates its process by using STATE. But, we may lose flexible setup with this
 * implementation. Now, we are investigating the influence.
 *
 * [TODO]
 * XCube_Controller keeps a process that set up instances of some legacy classes,
 * yet. We should move its process to this controller.
 */
class Legacy_Controller extends XCube_Controller
{
    public $_mAdminModeFlag = false;

    public $_mStrategy = null;

    public $mDialogMode = false;

    /**
     * @var XCube_Delegate
     */
    public $mCheckLogin = null;

    /**
     * @var XCube_Delegate
     */
    public $mLogout = null;

    /**
     * @var XCube_Delegate
     */
    public $mCreateLanguageManager = null;

    /**
     * @var XCube_Delegate
     */
    public $mSetBlockCachePolicy = null;

    /**
     * @var XoopsModule[]
     */
    public $mActiveModules = null;

    /**
     * @var XCube_Delegate
     */
    public $mSetModuleCachePolicy = null;

    /**
     * @var XCube_Delegate
     */
    public $mGetLanguageName = null;

    /**
     * @var XCube_Delegate
     */
    public $mSetupDebugger = null;

    /**
     * @var Legacy_AbstractDebugger
     */
    public $mDebugger;

    /**
     * @public
     * @var XCube_Delegate
     * [Secret Agreement] In execute_redirect(), notice the redirection which directs toward user.php.
     * @remark Only the special module should access this member property.
     */
    public $_mNotifyRedirectToUser = null;

    /**
     * @var XoopsLogger
     */
    public $mLogger = null;

    public function __construct()
    {
        parent::__construct();

        //
        // Setup member properties as member delegates.
        //
        $this->mSetupUser->register('Legacy_Controller.SetupUser');

        $this->mCheckLogin =new XCube_Delegate();
        $this->mCheckLogin->register('Site.CheckLogin');

        $this->mLogout =new XCube_Delegate();
        $this->mLogout->register('Site.Logout');

        $this->mCreateLanguageManager = new XCube_Delegate();
        $this->mCreateLanguageManager->register('Legacy_Controller.CreateLanguageManager');

        $this->mGetLanguageName = new XCube_Delegate();
        $this->mGetLanguageName->register('Legacy_Controller.GetLanguageName');

        $this->mSetBlockCachePolicy = new XCube_Delegate();
        $this->mSetModuleCachePolicy = new XCube_Delegate();

        $this->mSetupDebugger = new XCube_Delegate();
        $this->mSetupDebugger->add('Legacy_DebuggerManager::createInstance');

        $this->mSetupTextFilter->add('Legacy_TextFilter::getInstance', XCUBE_DELEGATE_PRIORITY_FINAL-1);

        $this->_mNotifyRedirectToUser = new XCube_Delegate();

    }

    public function prepare(&$root)
    {
        parent::prepare($root);

        //
        // Decide status. [TEST]
        //
        $this->_processHostAbstractLayer();

        $urlInfo = $this->_parseUrl();

        $adminStateFlag = false;
        if ((is_countable($urlInfo) ? count($urlInfo) : 0) >= 3) {
            if ('modules' === strtolower($urlInfo[0])) {
                if ('admin' === strtolower($urlInfo[2])) {
                    $adminStateFlag = true;
                } elseif ('legacy' === $urlInfo[1] && 'include' === $urlInfo[2]) {
                    $adminStateFlag = true;
                } elseif ('system' === $urlInfo[1] && strpos($urlInfo[2], 'admin.php') === 0) {
                    $adminStateFlag = true;
                }
            }
        } elseif (strpos($urlInfo[0], 'admin.php') === 0) {
            $adminStateFlag = true;
        }

        if ($adminStateFlag) {
            require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_AdminControllerStrategy.class.php';
            $this->_mStrategy = new Legacy_AdminControllerStrategy($this);
        } else {
            $this->_mStrategy = new Legacy_PublicControllerStrategy($this);
        }
    }

    /**
     * @access public
     */
    public function executeCommon()
    {
        //
        // Setup Filter chain and execute the process of these filters.
        //
        $this->_setupFilterChain();
        $this->_processFilter();

        if (!defined('XC_FORCE_DEBUG_PHP')) {
            error_reporting(0);
        }

        // This ^^;
        $this->_setupErrorHandler();

        //function date_default_timezone_set() is added on PHP5.1.0
        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set($this->_getLocalTimezone());
        }

        $this->_setupEnvironment();

        $this->_setupLogger();

        $this->_setupDB();

        $this->_setupLanguage();

        $this->_setupTextFilter();

        $this->_setupConfig();

        $this->_setupScript();

        $this->_setupDebugger();

        $this->_loadInterfaceFiles();

        $this->_processPreBlockFilter();    // What's !?

        $this->_setupSession();

        $this->_setupUser();

        $this->setupModuleContext();

        $this->_processModule();

        $this->_processPostFilter();
    }

    /**
     * Subset of executeCommon() Method
     * It will be used when process starts with $xoopsOption['nocommon'] and
     * This process requires connecting XOOPS Database or LEGACY constant values
     * But it won't do any other initial settings
     *	(e.g. Session start, Permission handling)
     *
     * @access public
     * @param bool $connectdb set false if you don't want to connect XOOPS Database
     *
     */
    public function executeCommonSubset(bool $connectdb = true)
    {
        $this->_setupErrorHandler();
        $this->_setupEnvironment();
        if ($connectdb) {
            $this->_setupLogger();
            $this->_setupDB();
        }
    }

    public function _setupLogger()
    {
        require_once XOOPS_ROOT_PATH . '/class/logger.php';
        $this->mLogger =& XoopsLogger::instance();
        $this->mLogger->startTime();

        $GLOBALS['xoopsLogger'] =& $this->mLogger;
    }

    public function &getLogger()
    {
        return $this->mLogger;
    }

    public function _getLocalTimezone()
    {
        $iTime = time();
        $arr = localtime($iTime);
        $arr[5] += 1900;
        $arr[4]++;
        $iTztime = gmmktime($arr[2], $arr[1], $arr[0], $arr[4], $arr[3], $arr[5]);
        $offset = (float)(($iTztime - $iTime) / (60 * 60));
        $zonelist =
        [
            'Kwajalein' => -12.00,
            'Pacific/Midway' => -11.00,
            'Pacific/Honolulu' => -10.00,
            'America/Anchorage' => -9.00,
            'America/Los_Angeles' => -8.00,
            'America/Denver' => -7.00,
            'America/Tegucigalpa' => -6.00,
            'America/New_York' => -5.00,
            'America/Caracas' => -4.30,
            'America/Halifax' => -4.00,
            'America/St_Johns' => -3.30,
            'America/Argentina/Buenos_Aires' => -3.00,
            'America/Sao_Paulo' => -3.00,
            'Atlantic/South_Georgia' => -2.00,
            'Atlantic/Azores' => -1.00,
            'Europe/Dublin' => 0,
            'Europe/Belgrade' => 1.00,
            'Europe/Minsk' => 2.00,
            'Asia/Kuwait' => 3.00,
            'Asia/Tehran' => 3.30,
            'Asia/Muscat' => 4.00,
            'Asia/Yekaterinburg' => 5.00,
            'Asia/Kolkata' => 5.30,
            'Asia/Katmandu' => 5.45,
            'Asia/Dhaka' => 6.00,
            'Asia/Rangoon' => 6.30,
            'Asia/Krasnoyarsk' => 7.00,
            'Asia/Brunei' => 8.00,
            'Asia/Seoul' => 9.00,
            'Australia/Darwin' => 9.30,
            'Australia/Canberra' => 10.00,
            'Asia/Magadan' => 11.00,
            'Pacific/Fiji' => 12.00,
            'Pacific/Tongatapu' => 13.00
        ];
        $index = array_keys($zonelist, $offset);
        if (1 !== count($index)) {
            return false;
        }
        return $index[0];
    }

    public function _setupEnvironment()
    {
        parent::_setupEnvironment();

        require_once XOOPS_ROOT_PATH.'/include/version.php';

        require_once XOOPS_TRUST_PATH.'/settings/definition.inc.php';
        define('XOOPS_LEGACY_PATH', XOOPS_MODULE_PATH.'/'.XOOPS_LEGACY_PROC_NAME);

        require_once XOOPS_ROOT_PATH.'/include/functions.php';

        require_once XOOPS_ROOT_PATH.'/kernel/object.php';
        require_once XOOPS_ROOT_PATH.'/class/criteria.php';
        require_once XOOPS_ROOT_PATH.'/class/token.php';
        require_once XOOPS_ROOT_PATH.'/class/module.textsanitizer.php';

        require_once XOOPS_LEGACY_PATH.'/kernel/object.php';        // ToDo (here?)
        require_once XOOPS_LEGACY_PATH.'/kernel/handler.php';       // ToDo
        require_once XOOPS_ROOT_PATH.'/core/XCube_Utils.class.php'; // ToDo

        require_once XOOPS_ROOT_PATH.'/class/xoopssecurity.php';
        $GLOBALS['xoopsSecurity'] = new XoopsSecurity();
    }

    /**
     * @NOTICE
     * We only set up the filters that we have decided to register.
     * It's not flexible. It's not the fixed style.
     *
     * @MEMO
     * For testing, you can use the autoload plugin by writing a parameter in site_custom.ini.php
     *
     * site_custom.ini.php:
     *	[Legacy]
     *	AutoPreload = 1
     *
     */
    public function _setupFilterChain()
    {
        $this->_mStrategy->_setupFilterChain();
    }

    public function _setupBlock()
    {
        $this->_mStrategy->setupBlock();
    }

    /**
     * @MEMO Process of Block
     * Fetch objects from $this->mBlockChain
     * render the result of the object with html data,
     * and set the result to the member property.
     *
     * In this member function, the cache mechanism must be important.
     * If the object has its own cache settings, this function loads the data from the cache
     * instead of calling the business logic of the block.
     *
     * @access protected
     */
    public function _processBlock()
    {
        $i=0;

        /**
         * @MEMO Create render-target for blocks
         * We use reset() to re-cycle this object in the foreach loop.
         */
        $context =& $this->mRoot->mContext;

        foreach ($this->_mBlockChain as $blockProcedure) {
            //
            // This is a flag indicating whether the controller needs to call
            // the logic of the block.
            //
            $usedCacheFlag = false;

            $cacheInfo = null;

            if ($this->isEnableCacheFeature() && $blockProcedure->isEnableCache()) {
                //
                // Reset the block cache information structure, and initialize.
                //
                $cacheInfo =& $blockProcedure->createCacheInfo();

                $this->mSetBlockCachePolicy->call(new XCube_Ref($cacheInfo));
                $filepath = $cacheInfo->getCacheFilePath();

                //
                // If caching is enabled and the cache file exists, load and use it.
                // Note : version 2.3.0
                // @gigamaster added 'getTemplate' to link block in front theme
                if ($cacheInfo->isEnableCache() && $this->existActiveCacheFile($filepath, $blockProcedure->getCacheTime())) {
                    $content = $this->loadCache($filepath);
                    if ($blockProcedure->isDisplay() && !empty($content)) {
                        $context->mAttributes['legacy_BlockShowFlags'][$blockProcedure->getEntryIndex()] = true;
                        $context->mAttributes['legacy_BlockContents'][$blockProcedure->getEntryIndex()][] = [
                            'id' => $blockProcedure->getId(),
                            'name' => $blockProcedure->getName(),
                            'title'   => $blockProcedure->getTitle(),
                            'content' => $content,
                            'weight'  => $blockProcedure->getWeight(),
                            'template'  => $blockProcedure->getTemplate(),
                        ];
                    }

                    $usedCacheFlag = true;
                }
            }

            if (!$usedCacheFlag) {
                $blockProcedure->execute();

                $renderBuffer = null;
                if ($blockProcedure->isDisplay()) {
                    $renderBuffer =& $blockProcedure->getRenderTarget();
                    // Note : version 2.3.0
                    // @gigamaster added 'getTemplate' to link block in front theme
                    $context->mAttributes['legacy_BlockShowFlags'][$blockProcedure->getEntryIndex()] = true;
                    $context->mAttributes['legacy_BlockContents'][$blockProcedure->getEntryIndex()][] = [
                        'id'        => $blockProcedure->getId(),
                        'name'      => $blockProcedure->getName(),
                        'title'     => $blockProcedure->getTitle(),
                        'content'   => $renderBuffer->getResult(),
                        'weight'    => $blockProcedure->getWeight(),
                        'template'  => $blockProcedure->getTemplate(),
                    ];
                } else {
                    //
                    // Dummy save
                    //
                    $renderBuffer = new XCube_RenderTarget();
                }

                if ($this->isEnableCacheFeature() && $blockProcedure->isEnableCache() && is_object($cacheInfo) && $cacheInfo->isEnableCache()) {
                    $this->cacheRenderTarget($cacheInfo->getCacheFilePath(), $renderBuffer);
                }
            }

            unset($blockProcedure);
        }
        XCube_DelegateUtils::call('Legacy.SetupModuleContextSuccess', is_object($this->mRoot->mContext->mModule)? $this->mRoot->mContext->mModule->mXoopsModule : null);
    }


    public function _parseUrl()
    {
        $ret = [];
        $rootPathInfo = @parse_url(XOOPS_URL);
        $rootPath = ($rootPathInfo['path'] ?? '') . '/';
        $php_info = xoops_getenv('PATH_INFO');
        $requestPathInfo = @parse_url(!empty($php_info) ? substr(xoops_getenv('PHP_SELF'), 0, - strlen(xoops_getenv('PATH_INFO'))) : xoops_getenv('PHP_SELF'));

        if (false === $requestPathInfo) {
            die();
        }

        $requestPath = isset($requestPathInfo['path']) ? urldecode($requestPathInfo['path']) : '';
        $subPath=substr($requestPath, strlen($rootPath));
        $subPath = trim($subPath, '/');
        $subPath = preg_replace('@/{2,}@', '/', $subPath);
        //        $ret = explode('/', $subPath);
        //        return $ret;
        return explode('/', $subPath);
    }


    public function setupModuleContext($dirname = null)
    {
        if (null == $dirname) {
            //
            // Sets a module object.
            //
            $urlInfo = $this->_parseUrl();

            if ((is_countable($urlInfo) ? count($urlInfo) : 0) >= 2) {
                if ('modules' === strtolower($urlInfo[0])) {
                    $dirname = $urlInfo[1];
                }
            }
            if ('admin.php' === substr($urlInfo[0], 0, 9)) {
                $dirname = 'legacy';
            }
        }

        if (null == $dirname) {
            return;
        }

        if (!file_exists(XOOPS_ROOT_PATH . '/modules/' . $dirname . '/' . LEGACY_XOOPS_MODULE_MANIFESTO_FILENAME)) {
            return;
        }

        $this->_mStrategy->setupModuleContext($this->mRoot->mContext, $dirname);

        if (null !== $this->mRoot->mContext->mModule) {
            $this->mRoot->mContext->setAttribute('legacy_pagetitle', $this->mRoot->mContext->mModule->mXoopsModule->get('name'));
        }
    }


    public function _processModule()
    {
        if (null !== $this->mRoot->mContext->mModule) {
            $module =& $this->mRoot->mContext->mModule;
            if (!$module->isActive()) {
                /**
                 * Notify that the current user is accessing a non-activated module
                 * controller
                 */
                XCube_DelegateUtils::call('Legacy.Event.Exception.ModuleNotActive', $module);
                $this->executeForward(XOOPS_URL . '/');
                die();
            }

            if (!$this->_mStrategy->enableAccess()) {
                XCube_DelegateUtils::call('Legacy.Event.Exception.ModuleSecurity', $module);
                $this->executeRedirect(XOOPS_URL . '/user.php', 1, _NOPERM); // Depends on const message catalog.
                die();
            }

            $this->_mStrategy->setupModuleLanguage();
            $module->startup();

            $GLOBALS['xoopsModule'] =& $module->mXoopsModule;
            $GLOBALS['xoopsModuleConfig'] =& $module->mModuleConfig;
        }

        Legacy_Utils::raiseUserControlEvent();
    }

    // @todo change to refer settings ini file for HostAbstractLayer.
    public function _processHostAbstractLayer()
    {
        require_once XOOPS_ROOT_PATH.'/include/functions.php';

        $path_translated = xoops_getenv('PATH_TRANSLATED');
        $script_filename = xoops_getenv('SCRIPT_FILENAME');
        $request_uri = xoops_getenv('REQUEST_URI');
        if (!isset($path_translated) && isset($script_filename)) {
            //
            // @todo We have to confirm this.
            // There is this setting for CGI mode.
            //
            $_SERVER['PATH_TRANSLATED'] =& $_SERVER['SCRIPT_FILENAME'];
        } elseif (isset($path_translated) && !isset($script_filename)) {
            // There is this setting for IIS Win2K. Really?
            $_SERVER['SCRIPT_FILENAME'] =& $_SERVER['PATH_TRANSLATED'];
        }

        /**
         * IIS
         * @note IIS does not set REQUEST_URI. This system defines it. But...
         */
        if (empty($request_uri)) {
            $query_string = xoops_getenv('QUERY_STRING');
            if (!($_SERVER['REQUEST_URI'] = xoops_getenv('PHP_SELF'))) {
                $_SERVER['REQUEST_URI'] = xoops_getenv('SCRIPT_NAME');
            }
            if (isset($query_string)) {
                $_SERVER['REQUEST_URI'] .= '?' . $query_string;
            }

            // Guard for XSS string of PHP_SELF
            // @todo I must move this logic to preload plugin.
            if (preg_match('/[\<\>\"\'\(\)]/', xoops_getenv('REQUEST_URI'))) {
                die();
            }
        }

        /**
         * @note Backwards compatibility !
         * The old system depend on this setting.
         * We have to confirm it and modify!
        */
        $GLOBALS['xoopsRequestUri'] = xoops_getenv('REQUEST_URI');
    }

    public function _setupUser()
    {
        parent::_setupUser();

        // Set instance to global variable for compatibility with XOOPS2
        $GLOBALS['xoopsUser'] =& $this->mRoot->mContext->mXoopsUser;
        $GLOBALS['xoopsUserIsAdmin'] = is_object($this->mRoot->mContext->mXoopsUser) ? $this->mRoot->mContext->mXoopsUser->isAdmin(1) : false; // @todo Remove '1'?

        //
        // Set member handler to global variables for compatibility with XOOPS2
        //
        $GLOBALS['xoopsMemberHandler'] = xoops_gethandler('member');
        $GLOBALS['member_handler'] =& $GLOBALS['xoopsMemberHandler'];
    }

    public function _setupErrorHandler()
    {
    }

    /**
     * Create the instance of DataBase class, and set it to member property.
     * @access protected
     */
    public function _setupDB()
    {
        if (!defined('XOOPS_XMLRPC')) {
            define('XOOPS_DB_CHKREF', 1);
        } else {
            define('XOOPS_DB_CHKREF', 0);
        }

        require_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';

        if (true == $this->mRoot->getSiteConfig('Legacy', 'AllowDBProxy')) {
            if ('POST' !== xoops_getenv('REQUEST_METHOD') || !xoops_refcheck(XOOPS_DB_CHKREF)) {
                define('XOOPS_DB_PROXY', 1);
            }
        } elseif ('POST' !== xoops_getenv('REQUEST_METHOD')) {
            define('XOOPS_DB_PROXY', 1);
        }

        $this->mDB =& XoopsDatabaseFactory::getDatabaseConnection();

        $GLOBALS['xoopsDB']=&$this->mDB;
    }

    /**
     * Create an instance of Legacy_LanguageManager by the specified language,
     * and set it to member properties.
     *
     * @Notice
     * Now, this member function sets a string to the member property without
     * language manager.
     */
    public function _setupLanguage()
    {
        require_once XOOPS_LEGACY_PATH.'/kernel/Legacy_LanguageManager.class.php';

        $language = null;

        $this->mGetLanguageName->call(new XCube_Ref($language));

        if (null == $language) {
            $handler = xoops_gethandler('config');
            $criteria = new CriteriaCompo(new Criteria('conf_modid', 0));
            $criteria->add(new Criteria('conf_catid', XOOPS_CONF));
            $criteria->add(new Criteria('conf_name', 'language'));
            $configs =& $handler->getConfigs($criteria);

            if ((is_countable($configs) ? count($configs) : 0) > 0) {
                $language = $configs[0]->get('conf_value', 'none');
            }
        }

        $this->mRoot->mLanguageManager =& $this->_createLanguageManager($language);
        $this->mRoot->mLanguageManager->setLanguage($language);
        $this->mRoot->mLanguageManager->prepare();

        // If you use special page, load message catalog for it.
        if (isset($GLOBALS['xoopsOption']['pagetype'])) {
            $this->mRoot->mLanguageManager->loadPageTypeMessageCatalog($GLOBALS['xoopsOption']['pagetype']);
        }
    }

    /**
     * Factory for the language manager
     *
     * At first, this member function delegates to get an instance of LanguageManager.
     * If it can't get it, do the following process:
     *
     * @exemple
     * 1) Try creating an instance of 'Legacy_LanguageManager_' . ucfirst($language)
     * 2) If the class doesn't exist, try loading  'LanguageManager.class.php'
     *	  in the specified language.
     * 3) Re-try creating the instance.
     *
     * If it can't create any instances, create an instance of
     * Legacy_LanguageManager as default.
     *
     * @access protected
     * @param string $language
     * @return Legacy_LanguageManager
     */
    public function &_createLanguageManager($language)
    {
        require_once XOOPS_LEGACY_PATH . '/kernel/Legacy_LanguageManager.class.php';

        $languageManager = null;

        $this->mCreateLanguageManager->call(new XCube_Ref($languageManager), $language);

        if (!is_object($languageManager)) {
            $className = 'Legacy_LanguageManager_' . ucfirst(strtolower($language));

            //
            // If the class exists, create an instance. Else, load the file, and
            // try creating an instance again.
            //
            if (XC_CLASS_EXISTS($className)) {
                $languageManager = new $className();
            } else {
                $filePath = XOOPS_ROOT_PATH . '/language/' . $language . '/LanguageManager.class.php';
                if (file_exists($filePath)) {
                    require_once $filePath;
                }

                if (XC_CLASS_EXISTS($className)) {
                    $languageManager = new $className();
                } else {
                    //
                    // Default
                    //
                    $languageManager = new Legacy_LanguageManager();
                }
            }
        }

        return $languageManager;
    }

    public function _setupConfig()
    {
        $configHandler = xoops_gethandler('config');

        $this->mRoot->mContext->mXoopsConfig =& $configHandler->getConfigsByCat(XOOPS_CONF);

        $this->mRoot->mContext->mXoopsConfig['language'] = $this->mRoot->mLanguageManager->getLanguage();
        $GLOBALS['xoopsConfig'] =& $this->mRoot->mContext->mXoopsConfig; // Compatibility for X2
        $GLOBALS['config_handler'] =& $configHandler;
        $GLOBALS['module_handler'] = xoops_gethandler('module');

        if (0 == (is_countable($this->mRoot->mContext->mXoopsConfig) ? count($this->mRoot->mContext->mXoopsConfig) : 0)) {
            return;
        }

        // @disable-parser-inspector
        // Note: X-app themes must use Base System Render !
        $this->mRoot->mContext->setThemeName($this->mRoot->mContext->mXoopsConfig['theme_set']);
        $this->mRoot->mContext->setAttribute('legacy_sitename', $this->mRoot->mContext->mXoopsConfig['sitename']);
        $this->mRoot->mContext->setAttribute('legacy_pagetitle', $this->mRoot->mContext->mXoopsConfig['slogan']);
        $this->mRoot->mContext->setAttribute('legacy_slogan', $this->mRoot->mContext->mXoopsConfig['slogan']);
    }

    public function _setupScript()
    {
        require_once XOOPS_MODULE_PATH.'/legacy/class/Legacy_HeaderScript.class.php';
        $headerScript = new Legacy_HeaderScript();
        $this->mRoot->mContext->setAttribute('headerScript', $headerScript);
    }

    /**
     * Set debugger object to member property.
     * @return void
     */
    public function _setupDebugger()
    {
	// Commented out because suppressing errors here will not result in an error if the debugger setup fails.
	// If the debugger setup succeeds, error_reporting is set to the value corresponding to the debug mode.
        // error_reporting(0);

        $debug_mode = $this->mRoot->mContext->mXoopsConfig['debug_mode'];
        if (defined('XC_FORCE_DEBUG_PHP')) {
            $debug_mode = XOOPS_DEBUG_PHP;
        }

        $this->mSetupDebugger->call(new XCube_Ref($this->mDebugger), $debug_mode);
        $this->mDebugger && $this->mDebugger->prepare();

        $GLOBALS['xoopsDebugger']=&$this->mDebugger;
    }

    public function _processPreBlockFilter()
    {
        $this->_mStrategy->_processPreBlockFilter();
        parent::_processPreBlockFilter();
    }

    public function _processModulePreload($dirname)
    {
        //
        // Auto pre-loading for Module.
        //
        if ($this->mRoot->getSiteConfig('Legacy', 'AutoPreload') == 1) {
            if ($this->mActiveModules) {
                $moduleObjects = $this->mActiveModules;
            } else {
                $moduleHandler = xoops_gethandler('module');
                $criteria = new Criteria('isactive', 1);
                $this->mActiveModules =
            $moduleObjects =& $moduleHandler->getObjects($criteria);
            }
            foreach ($moduleObjects as $moduleObject) {
                $mod_dir = $moduleObject->getVar('dirname');
                $dir = XOOPS_ROOT_PATH . '/modules/' . $mod_dir . $dirname . '/';
                if (is_dir($dir)) {
                    $files = glob($dir.'*.class.php');
                    if ($files) {
                        foreach ($files as $file) {
                            require_once $file;
                            $className = ucfirst($mod_dir) . '_' . basename($file, '.class.php');

                            if (XC_CLASS_EXISTS($className) && !isset($this->_mLoadedFilterNames[$className])) {
                                $instance = new $className($this);
                                $this->addActionFilter($instance);
                                unset($instance);
                            }
                        }
                    }
                }
            }
        }
    }

    public function _setupSession()
    {
        parent::_setupSession();

        $root =& XCube_Root::getSingleton();
        $xoopsConfig = $root->mContext->mXoopsConfig;
        if ($xoopsConfig['use_mysession']) {
            $this->mRoot->mSession->setParam($xoopsConfig['session_name'], $xoopsConfig['session_expire']);
        }
        $this->mRoot->mSession->start();
    }

    protected function _loadInterfaceFiles()
    {
        $dir = XOOPS_MODULE_PATH.'/legacy/class/interface/';
        $interfaces = glob($dir.'*.php');
        foreach ($interfaces as $file) {
            require_once($file);
        }
    }

    public function executeHeader()
    {
        /** @Todo Now, It's done to run admin panel. */
        parent::executeHeader();

        /**
         * We changed a render-system class in a pure drawing system.
         * Therefore, a controller should not be required for compatibility.
         * @version 2.3.0 Gigamaster removed old version process in XCL2020/PHP7.
         */

        /** cache check */
        if (null !== $this->mRoot->mContext->mModule && $this->isEnableCacheFeature()) {
            $cacheInfo =& $this->mRoot->mContext->mModule->createCacheInfo();

            $this->mSetModuleCachePolicy->call($cacheInfo);

            if ($this->mRoot->mContext->mModule->isEnableCache()) {

                /** Checks whether the cache file exists. */
                $xoopsModule =& $this->mRoot->mContext->mXoopsModule;

                $cachetime = $this->mRoot->mContext->mXoopsConfig['module_cache'][$xoopsModule->get('mid')];
                $filepath = $cacheInfo->getCacheFilePath();

                /**
                 * Checks whether the active cache file exists. If it's OK, load
                 * cache and do display.
                 */
                if ($cacheInfo->isEnableCache() && $this->existActiveCacheFile($filepath, $cachetime)) {
                    $renderSystem =& $this->mRoot->getRenderSystem($this->mRoot->mContext->mModule->getRenderSystemName());
                    $renderTarget =& $renderSystem->createRenderTarget(XCUBE_RENDER_TARGET_TYPE_MAIN);
                    /**
                     * @version 2.3.0 Gigamaster
                     * //$renderTarget->setResult($this->loadCache($filepath));
                     */
                    $renderCache = $this->loadCache($filepath);
                    $renderTarget->setResult($renderCache);

                    $this->_executeViewTheme($renderTarget);

                    exit();
                }
            }
        }

        ob_start();
    }

    public function executeView()
    {
        if (null !== $this->mRoot->mContext->mModule) {
            $renderSystem =& $this->mRoot->getRenderSystem($this->mRoot->mContext->mModule->getRenderSystemName());
            $renderTarget =& $this->mRoot->mContext->mModule->getRenderTarget();

            /*
             * Managing standard output buffering for the main render target is the responsibility of a controller.
             * Of course, not all controllers are required to implement this.
             * The following lines ensure the compatibility and functionality of this controller.
             */

            // Wmm...
            if (is_object($renderTarget)) {
                if (null == $renderTarget->getTemplateName()) {
                    if (isset($GLOBALS['xoopsOption']['template_main'])) {
                        $renderTarget->setTemplateName($GLOBALS['xoopsOption']['template_main']);
                    }
                }

                $renderTarget->setAttribute('stdout_buffer', ob_get_contents());
            }

            ob_end_clean();

            if (is_object($renderTarget)) {
                $renderSystem->render($renderTarget);

                /**  Cache Control */
                $module = $this->mRoot->mContext->mModule;
                if ($this->isEnableCacheFeature() && $module->isEnableCache() && $module->mCacheInfo->isEnableCache()) {
                    $this->cacheRenderTarget($module->mCacheInfo->getCacheFilePath(), $renderTarget);
                }
            }
        }

        $this->_executeViewTheme($renderTarget);
    }

    /**
     * $resultRenderTarget object The render target of content's result.
     * @param $resultRenderTarget
     */
    public function _executeViewTheme(&$resultRenderTarget)
    {

        /** Get the render-system through theme object. */
        $theme =& $this->_mStrategy->getMainThemeObject();
        if (!is_object($theme)) {
            die('Could not found any compatible theme.');
        }

        $renderSystem =& $this->mRoot->getRenderSystem($theme->get('render_system'));
        $screenTarget = $renderSystem->getThemeRenderTarget($this->mDialogMode);

        if (is_object($resultRenderTarget)) {
            $screenTarget->setAttribute('xoops_contents', $resultRenderTarget->getResult());
        } else {
            $screenTarget->setAttribute('xoops_contents', ob_get_contents());
            ob_end_clean();
        }

        $screenTarget->setTemplateName($theme->get('dirname'));

        //
        // Rendering.
        //
        $renderSystem->render($screenTarget);

        //
        // Debug Process
        //
        $isAdmin=false;
        if (is_object($this->mRoot->mContext->mXoopsUser)) {
            if (null !== $this->mRoot->mContext->mModule && $this->mRoot->mContext->mModule->isActive()) {
                // @todo depend on Legacy Module Controller.
                $mid = $this->mRoot->mContext->mXoopsModule->getVar('mid');
            } else {
                $mid = 1;    ///< @todo Do not use literal directly!
            }

            $isAdmin = $this->mRoot->mContext->mXoopsUser->isAdmin($mid);
        }

        if ($isAdmin) {
            $this->mDebugger->displayLog();
        }
    }

    /**
     * @return XCube_DelegateManager
     */
    public function &_createDelegateManager()
    {
        $delegateManager =& parent::_createDelegateManager();

        $file = XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_EventFunctions.class.php';

        $delegateManager->add('Legacypage.Notifications.Access', 'Legacy_EventFunction::notifications', $file);
        $delegateManager->add('Legacyfunction.Notifications.Select', 'Legacy_EventFunction::notifications_select', $file);
        $delegateManager->add('Legacypage.Search.Access', 'Legacy_EventFunction::search', $file);
        $delegateManager->add('Legacypage.Imagemanager.Access', 'Legacy_EventFunction::imageManager', $file);
        $delegateManager->add('Legacypage.Backend.Access', 'Legacy_EventFunction::backend', $file);
        $delegateManager->add('Legacypage.Misc.Access', 'Legacy_EventFunction::misc', $file);
        $delegateManager->add('User_UserViewAction.GetUserPosts', 'Legacy_EventFunction::recountPost', $file);

        return $delegateManager;
    }

    /**
     * @return XCube_ServiceManager
     */
    public function &_createServiceManager()
    {
        $serviceManager =& parent::_createServiceManager();

        require_once XOOPS_ROOT_PATH . '/modules/legacy/service/LegacySearchService.class.php';
        $searchService = new Legacy_SearchService();
        $searchService->prepare();

        $serviceManager->addService('LegacySearch', $searchService);

        return $serviceManager;
    }

    /**
     * Check the login request through the delegates
     * and set XoopsObject to member property if the connection is successful.
     *
     * @access public
     */
    public function checkLogin()
    {
        if (!is_object($this->mRoot->mContext->mXoopsUser)) {
            $this->mCheckLogin->call(new XCube_Ref($this->mRoot->mContext->mXoopsUser));

            $this->mRoot->mLanguageManager->loadModuleMessageCatalog('legacy');

            if (is_object($this->mRoot->mContext->mXoopsUser)) {
                // If the current user doesn't belong to any groups, logout for XCL's security.
                $t_groups = $this->mRoot->mContext->mXoopsUser->getGroups();
                if (!is_array($t_groups)) {
                    // exception
                    $this->logout();
                    return;
                } elseif (0 == count($t_groups)) {
                    // exception, too
                    $this->logout();
                    return;
                }

                // RMV-NOTIFY
                // Perform some maintenance of notification records
                $notification_handler = xoops_gethandler('notification');
                $notification_handler->doLoginMaintenance($this->mRoot->mContext->mXoopsUser->get('uid'));

                XCube_DelegateUtils::call('Site.CheckLogin.Success', new XCube_Ref($this->mRoot->mContext->mXoopsUser));

                //
                // Fall back process for login success.
                //
                $url = XOOPS_URL;
                if (!empty($_POST['xoops_redirect']) && !strpos(xoops_getrequest('xoops_redirect'), 'register')) {
                    $parsed = parse_url(XOOPS_URL);
                    $url = isset($parsed['scheme']) ? $parsed['scheme'].'://' : 'https://';

                    if (isset($parsed['host'])) {
                        $url .= isset($parsed['port']) ? $parsed['host'] . ':' . $parsed['port'] . '/'.ltrim(trim(xoops_getrequest('xoops_redirect')), '/') : $parsed['host'] . '/'. ltrim(trim(xoops_getrequest('xoops_redirect')), '/');
                    } else {
                        $url .= xoops_getenv('HTTP_HOST') . '/'. ltrim(trim(xoops_getrequest('xoops_redirect')), '/');
                    }
                }

                $this->executeRedirect($url, 1, XCube_Utils::formatString(_MD_LEGACY_MESSAGE_LOGIN_SUCCESS, $this->mRoot->mContext->mXoopsUser->get('uname')));
            } else {
                XCube_DelegateUtils::call('Site.CheckLogin.Fail', new XCube_Ref($this->mRoot->mContext->mXoopsUser));

                //
                // Fall back process for login fail.
                //
                $this->executeRedirect(XOOPS_URL . '/user.php', 1, _MD_LEGACY_ERROR_INCORRECTLOGIN);
            }
        } else {
            $this->executeForward(XOOPS_URL . '/');
        }
    }

    /**
     * The current user logout.
     *
     * @access public
     */
    public function logout()
    {
        $successFlag = false;
        $xoopsUser =& $this->mRoot->mContext->mXoopsUser;


        if (is_object($xoopsUser)) {
            $this->mRoot->mLanguageManager->loadModuleMessageCatalog('legacy');

            $this->mLogout->call(new XCube_Ref($successFlag), $xoopsUser);
            if ($successFlag) {
                XCube_DelegateUtils::call('Site.Logout.Success', $xoopsUser);
                // @gigamaster join lang array, 'message' must be of the type string
                // $msg_array = [_MD_LEGACY_MESSAGE_LOGGEDOUT, _MD_LEGACY_MESSAGE_THANKYOUFORVISIT];
                // $msg_show = join('<br>', $msg_array);
                // $this->executeRedirect(XOOPS_URL . '/', 1, $msg_show);
                $this->executeRedirect(XOOPS_URL . '/', 1, [_MD_LEGACY_MESSAGE_LOGGEDOUT, _MD_LEGACY_MESSAGE_THANKYOUFORVISIT]);
            } else {
                XCube_DelegateUtils::call('Site.Logout.Fail', $xoopsUser);
            }
        } else {
            $this->executeForward(XOOPS_URL . '/');
        }
    }

    /**
     * @param $strategy
     * @see setStrategy()
     * @deprecated
     */
    public function switchStateCompulsory(&$strategy)
    {
        $this->setStrategy($strategy);
    }

    /**
     * ⚠ CAUTION ⚠
     * This method has a very specific task.
     * After executeCommon, this method changes the state and resets the property.
     * It depends on the steps of XCube_Controller.
     *
     * @param Legacy_AbstractControllerStrategy $strategy
     */
    public function setStrategy(&$strategy)
    {
        if ($strategy->mStatusFlag !== $this->_mStrategy->mStatusFlag) {
            $this->_mStrategy =& $strategy;

            //
            // The following line depends on XCube_Controller process of executeCommon.
            // Note that there is only one method implemented in the default distribution.
            //
            $this->setupModuleContext();
            $this->_processModule();
        }
    }

    /**
     * Set bool flag to dialog mode flag.
     * If you set true, executeView() will use Legacy_DialogRenderTarget class as render target.
     *
     * @param bool $flag
     */
    public function setDialogMode($flag)
    {
        $this->mDialogMode = $flag;
    }

    /**
     * Return dialog mode flag.
     * @return bool
     */
    public function getDialogMode()
    {
        return $this->mDialogMode;
    }

    /**
     *	Return current module object.
     *  But, it's decided by the rules of the state.
     *	Note that Preferences, Help and some other views return the specified module by dirname.
     *  This is useful for controlling a theme.
     *
     * @return XoopsModule
     */
    public function &getVirtualCurrentModule()
    {
        $ret =& $this->_mStrategy->getVirtualCurrentModule();
        return $ret;
    }

    /**
     * This member function works to redirect as well as redirect_header().
     * But, this member function handles raw values which hasn't been converted
     * by htmlspecialchars(). Therefore, if user calls this function with the
     * wrong value, some problems may be raised. If you can't understand the
     * difference, do not use this function but redirect_header().
     *
     * @param string      $url     redirect URL. Don't use user's variables or request.
     * @param int         $time    waiting time (sec)
     * @param string|null $message This string doesn't include tags.
     *
     * @todo Change this function to delegate.
     * @remark This method encodes $url and $message directly without its template, to share the template with old function.
     */
    public function executeRedirect($url, $time = 1, $message = null, $addRedirect = true)
    {
        global $xoopsConfig, $xoopsRequestUri;

        //
        // Check the following by way of caution.
        //
        if (preg_match('/(javascript|vbscript):/si', $url)) {
            $url = XOOPS_URL;
        }

        $displayMessage = '';
        if (is_array($message)) {
            foreach (array_keys($message) as $key) {
                $message[$key] = htmlspecialchars($message[$key], ENT_QUOTES);
            }
            $displayMessage = implode('<br>', $message);
        } else {
            $displayMessage = $message;
        }

        $url = htmlspecialchars($url, ENT_QUOTES);

        // @TODO test XOOPS2 Compatibility $addredirect = true

        // TODO uncomment to test
        // $addRedirect = $addredirect = true;

        if ($addRedirect && strpos($url, 'user.php') !== false) {
            $this->_mNotifyRedirectToUser->call(new XCube_Ref($url));
        }

        if (defined('SID') && (! isset($_COOKIE[session_name()]) || ($xoopsConfig['use_mysession'] && '' !== $xoopsConfig['session_name'] && !isset($_COOKIE[$xoopsConfig['session_name']])))) {
            if (strpos($url, (string) XOOPS_URL) === 0) {
                if (strpos($url, '?') === false) {
                    $connector = '?';
                } else {
                    $connector = '&amp;';
                }
                if (strpos($url, '#') !== false) {
                    $urlArray = explode('#', $url);
                    $url = $urlArray[0] . $connector . SID;
                    if (! empty($urlArray[1])) {
                        $url .= '#' . $urlArray[1];
                    }
                } else {
                    $url .= $connector . SID;
                }
            }
        }

       /* XCL 2.3.x
        * @gigamaster added theme_set, theme_url and theme_css (custom templates from theme)
        * also Render configs for X2 and D3 compatibility, refer to /class/template.php
        */
        $moduleHandler = xoops_gethandler('module');
        $legacyRender =& $moduleHandler->getByDirname('legacyRender');
        $configHandler = xoops_gethandler('config');
        $configs =& $configHandler->getConfigsByCat(0, $legacyRender->get('mid'));

        if (!defined('XOOPS_CPFUNC_LOADED')) {
            require_once XOOPS_ROOT_PATH.'/class/template.php';
            $xoopsTpl = new XoopsTpl();
            $xoopsTpl->assign(
                [
                    'xoops_sitename'   =>htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
                    'sitename'         =>htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES),
                    'theme_set'        =>htmlspecialchars($xoopsConfig['theme_set'], ENT_QUOTES),
                    'theme_url'        =>XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'],
                    'theme_css'        =>getcss(),
                    'langcode'         =>_LANGCODE,
                    'charset'          =>_CHARSET,
                    'time'             =>$time,
                    'url'              =>$url,
                    'message'          =>$displayMessage,
                    'logotype'         =>$configs['logotype'],
                    'favicon'          =>$configs['favicon'],
                    'lang_ifnotreload' =>sprintf(_IFNOTRELOAD, $url)
                ]
            );
            $GLOBALS['xoopsModuleUpdate'] = 1;

            $xoopsTpl->display('db:system_redirect.html');  // Legacy compatibility

        } else {

            header('Content-Type:text/html; charset='._CHARSET);
            echo '
            <!DOCTYPE html>
            <head>
            <title>'.htmlspecialchars($xoopsConfig['sitename']).'</title>
            <meta http-equiv="Content-Type" content="text/html; charset='._CHARSET.'">
            <{if !class_exists("AdelieDebug_Preload")}>
            <meta http-equiv="Refresh" content="'.$time.'; url='.$url.'">
            <{/if}>
            <link rel="stylesheet" href="<{$xoops_url}>/modules/legacy/admin/theme/style.css">
            <style>
                body {margin:2em;transition:background 500ms ease-in-out, color 200ms ease;}

                .wrapper-require {
                    align-content	: center;
                    display			: flex;
                    flex-direction	: column;
                    justify-content	: center;
                    min-height		: 100vh;
                }

                .wrapper-require > * {
                    margin			: 1em auto;
                }
                table {
                    width: 100%;
                    border-spacing: 1px;
                    border-collapse: separate;
                }
                td {
                    padding: 1em 2em;
                }
                .icon-package {
                margin-right		: 1em; /* Space icon -> text */
                vertical-align		: middle; /* Align icon and text */
                width				: 1.5em; /* Size icon to text */
                }

                </style>

            </head>
            <body>

                <div class="wrapper-redirect">
                    <h2>Redirect</h2>
                    <hr>
                    <h4>'.$displayMessage.'</h4>
                    <hr>
                    <p>'.sprintf(_IFNOTRELOAD, $url).'</p>
                    <hr>
                <{if class_exists("AdelieDebug_Preload")}>
                <p style="color:red;text-align: center">No automatic redirect during AdelieDebug execution.</p>
                <{/if}>

                </div>

            </body>
            </html>';
        }

        exit();
    }

    /**
     * Gets a value indicating whether the controller can use a cache mechanism.
     * @return bool
     */
    public function isEnableCacheFeature()
    {
        return $this->_mStrategy->isEnableCacheFeature();
    }

    /**
     * Gets a value indicating wheter a cache file keeps life time. If
     * $cachetime is 0 or the specific cache file doesn't exist, gets false.
     *
     * @param string $filepath	a file path of the specific cache file.
     * @param int	 $cachetime cache active duration. (Sec)
     * @return bool
     */
    public function existActiveCacheFile($filepath, $cachetime)
    {
        if (0 == $cachetime) {
            return false;
        }

        if (!file_exists($filepath)) {
            return false;
        }

        return ((time() - filemtime($filepath)) <= $cachetime);
    }

    /**
     * Save the content of $renderTarget to $filepath.
     * @param string $filepath a file path of the cache file.
     * @param        $renderTarget
     * @return bool success or failure.
     */
    public function cacheRenderTarget($filepath, &$renderTarget)
    {
        $fp = fopen($filepath, 'wb');
        if ($fp) {
            fwrite($fp, $renderTarget->getResult());
            fclose($fp);
            return true;
        }

        return false;
    }

    /**
     * Loads $filepath and gets the content of the file.
     * @param $filepath
     * @return string the content or null.
     */
    public function loadCache($filepath)
    {
        if (file_exists($filepath)) {
            return file_get_contents($filepath);
        }

        return null;
    }

    public function &_createContext()
    {
        require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_HttpContext.class.php';

        $context = new Legacy_HttpContext();
        $request = new XCube_HttpRequest();
        $context->setRequest($request);

        return $context;
    }

    /**
     * Gets URL of preference editor by this controller.
     * @public
     * @param XoopsModule $module
     * @return string absolute URL
     */
    public function getPreferenceEditUrl(&$module)
    {
        if (is_object($module)) {
            return XOOPS_MODULE_URL . '/legacy/admin/index.php?action=PreferenceEdit&confmod_id=' . $module->get('mid');
        }

        return null;
    }

    /**
     * Gets URL of help viewer by this controller.
     * @public
     * @param XoopsModule $module
     * @return string absolute URL
     */
    public function getHelpViewUrl(&$module)
    {
        if (is_object($module)) {
            return XOOPS_MODULE_URL . '/legacy/admin/index.php?action=Help&dirname=' . $module->get('dirname');
        }

        return null;
    }
}

/**
 * @internal
 */
class Legacy_AbstractControllerStrategy
{
    /**
     * @var Legacy_Controller
     */
    public $mController = null;

    public $mStatusFlag;

    public array $_mLoadedFilterNames = [];

    public function __construct(&$controller)
    {
        $this->mController =& $controller;
    }

    public function _setupFilterChain()
    {
        $primaryPreloads = $this->mController->mRoot->getSiteConfig('Legacy.PrimaryPreloads');
        foreach ($primaryPreloads as $className => $classPath) {
            if (file_exists(XOOPS_ROOT_PATH . $classPath)) {
                require_once XOOPS_ROOT_PATH . $classPath;
                if (XC_CLASS_EXISTS($className) && !isset($this->_mLoadedFilterNames[$className])) {
                    $this->_mLoadedFilterNames[$className] = true;
                    $filter = new $className($this->mController);
                    $this->mController->addActionFilter($filter);
                    unset($filter);
                }
            }
        }

        //
        // Auto pre-loading.
        //
        if (1 == $this->mController->mRoot->getSiteConfig('Legacy', 'AutoPreload')) {
            $this->mController->_processPreload(XOOPS_ROOT_PATH . '/preload');
        }
    }

    /**
     * Create some instances for the module process.
     * Because Legacy_ModuleContext needs XoopsModule instance, this function
     * kills the process if XoopsModule instance can't be found. Plus, in the
     * case, raises 'Legacy.Event.Exception.XoopsModuleNotFound'.
     *
     * @param Legacy_HttpContext $context
     * @param string $dirname
     */
    public function setupModuleContext(&$context, $dirname)
    {
        $handler = xoops_gethandler('module');
        $module =& $handler->getByDirname($dirname);

        if (!is_object($module)) {
            XCube_DelegateUtils::call('Legacy.Event.Exception.XoopsModuleNotFound', $dirname);
            $this->mController->executeRedirect(XOOPS_URL . '/', 1, 'You can\'t access this URL.'); // TODO need message catalog.
            die();
        }

        $context->mModule =& Legacy_Utils::createModule($module);
        $context->mXoopsModule =& $context->mModule->getXoopsModule();
        $context->mModuleConfig = $context->mModule->getModuleConfig();

        //
        // Load Roles
        //
        $this->mController->mRoot->mRoleManager->loadRolesByMid($context->mXoopsModule->get('mid'));
    }

    public function setupBlock()
    {
    }

    public function _processPreBlockFilter()
    {
        $this->mController->_processModulePreload('/preload');
    }

    /**
     * @return XoopsModule
     * @see Legacy_Controller::getVirtualCurrentModule()
     */
    public function &getVirtualCurrentModule()
    {
        $ret = null;
        return $ret;
    }

    /**
     * Gets a value indicating whether the controller can use a cache mechanism.
     */
    public function isEnableCacheFeature()
    {
    }

    /**
     * Gets a value indicating whether the current user can access to the current module.
     * @return void
     */
    public function enableAccess()
    {
    }

    public function setupModuleLanguage()
    {
    }
}
