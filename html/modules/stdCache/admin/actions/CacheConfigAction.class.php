<?php
/**
 * Standard cache - Module for XCL
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

require_once __DIR__ . '/../forms/CacheConfigForm.class.php';
require_once __DIR__ . '/../class/Action.class.php'; // Base action
require_once __DIR__ . '/../class/CacheManager.class.php';

// For XCube_DelegateUtils if not autoloaded or included by the core
if (!class_exists('XCube_DelegateUtils') && file_exists(XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php')) {
    require_once XOOPS_ROOT_PATH . '/core/XCube_DelegateUtils.class.php';
}


class stdCache_CacheConfigAction extends stdCache_Action
{

    /**
     * @var XCube_Root
     */
    protected $mRoot = null; // Declare the property

    /**
     * @var stdCache_CacheConfigForm
     */
    protected $mActionForm = null;

    /**
     * @var stdCache_CacheManager
     */
    protected $mCacheManager = null;

    /**
     * @var XoopsModule
     */
    protected $mModuleObject = null; // Add this property
    

    /**
     * Constructor
     */
    public function __construct($adminFlag = false) // Accept adminFlag if used by stdCache_Action
    {
        parent::__construct($adminFlag); // Call parent constructor
        $this->mRoot = XCube_Root::getSingleton(); // Initialize mRoot
    }

    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        // check is an object before calling isAdmin
        return (is_object($xoopsUser) && $xoopsUser->isAdmin());
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        parent::prepare($controller, $xoopsUser, $moduleConfig);

        // Use the objects passed as arguments
        if (!(is_object($xoopsUser) && $xoopsUser->isAdmin())) {
            $controller->executeForward(XOOPS_URL . '/'); // Use passed $controller
            return false; // Stop further execution
        }

        // direct instantiation here
        $this->mActionForm = new stdCache_CacheConfigForm();
        $this->mActionForm->prepare();

        try {
            $this->mCacheManager = new stdCache_CacheManager();
        } catch (Exception $e) {
            error_log("stdCache_CacheConfigAction: Failed to initialize CacheManager in prepare() - " . $e->getMessage());
            // Depending on how critical CacheManager is, rethrow or handle
            // for now rethrow for dev
            throw new RuntimeException('Failed to initialize CacheManager for CacheConfigAction: ' . $e->getMessage());
        }

        // Get and store the module object
        $module_handler = xoops_gethandler('module');
        if (is_object($module_handler)) {
            $this->mModuleObject = $module_handler->getByDirname('stdCache');
        }
        if (!is_object($this->mModuleObject)) {
            // Handle error: module object could not be loaded
            // This is a critical failure, perhaps redirect or log
            error_log("stdCache_CacheConfigAction: Failed to load stdCache module object.");
            // Optionally, prevent further execution if the module object is essential
            // $this->mRoot->mController->executeForward(XOOPS_URL . '/admin.php');
            // return false;
        }

        return true;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (!$this->mCacheManager) { // set in prepare()
            $this->mCacheManager = new stdCache_CacheManager();
        }
        $config = $this->mCacheManager->getConfigs(); // gets all configs for the module
        
        // Fallback defaults if not found in $config (database)
        $this->mActionForm->set('cache_limit_smarty', $config['cache_limit_smarty'] ?? 50000000); // 50MB
        $this->mActionForm->set('cache_limit_alert_trigger', $config['cache_limit_alert_trigger'] ?? 40000000); // 40MB
        $this->mActionForm->set('cache_limit_cleanup', $config['cache_limit_cleanup'] ?? 45000000); // 45MB
        $this->mActionForm->set('cache_limit_compiled', $config['cache_limit_compiled'] ?? 20000000); // 20MB
        $this->mActionForm->set('cache_limit_alert_enable', $config['cache_limit_alert_enable'] ?? 1); // Default to enabled
        
        return STDCACHE_FRAME_VIEW_INPUT;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        // Check for cancel button submission
        if (null !== $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel')) {
            return STDCACHE_FRAME_VIEW_CANCEL;
        }
        
        // Fetch the form values from the request
        $this->mActionForm->fetch();
        
        // Validate the form
        $this->mActionForm->validate(); // Call validate() it sets internal error flags
        
        if ($this->mActionForm->hasError()) { // Check using hasError()
            return STDCACHE_FRAME_VIEW_INPUT; // Re-display form with errors
        }
        
        if (!$this->mCacheManager) { 
            // This should ideally not happen if prepare() worked correctly
            $this->mCacheManager = new stdCache_CacheManager();
        }
        
        // Get values from the form
        $configValues = $this->mActionForm->getValues();
        
        // Call saveConfig and capture its result
        $saveSuccess = $this->mCacheManager->saveConfig($configValues);

        if ($saveSuccess) {
            if (class_exists('XCube_DelegateUtils')) {
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddMessage', _AD_STDCACHE_CONFIG_SAVED_SUCCESS);
            }
            return STDCACHE_FRAME_VIEW_SUCCESS;
        } else {
            if (class_exists('XCube_DelegateUtils')) {
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', _AD_STDCACHE_CONFIG_SAVED_FAIL);
            }
            return STDCACHE_FRAME_VIEW_ERROR;
        }
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max((float)$bytes, 0); // Ensure float and non-negative
        if ($bytes == 0) return '0 ' . $units[0];
        $pow = floor(log($bytes) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewSuccess($controller, $xoopsUser, $render);
        $controller->executeForward('./index.php?action=CacheStats');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render);
        $this->_setupViewInputErrorCommon($render); 
        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewInput($controller, $xoopsUser, $render);
        $this->_setupViewInputErrorCommon($render); 
        return true;
    }
    
/*     protected function _setupViewInputErrorCommon(&$render)
    {
        $render->setTemplateName('stdcache_admin_cache_config.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        
        $render->setAttribute('cache_limit_mb', $this->formatSize($this->mActionForm->get('cache_limit_smarty')));
        $render->setAttribute('cache_limit_alert_trigger_mb', $this->formatSize($this->mActionForm->get('cache_limit_alert_trigger')));
        $render->setAttribute('cache_limit_cleanup_mb', $this->formatSize($this->mActionForm->get('cache_limit_cleanup')));
        $render->setAttribute('cache_limit_compiled_mb', $this->formatSize($this->mActionForm->get('cache_limit_compiled')));
    } */
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewCancel($controller, $xoopsUser, $render);
        $controller->executeForward('./index.php?action=CacheStats');
    }

    protected function _getPagetitle() 
    {
        return defined('_AD_STDCACHE_CONFIG') ? _AD_STDCACHE_CONFIG : 'Cache Configuration';
    }

    protected function _setupViewInputErrorCommon(&$render)
    {
        $render->setTemplateName('stdcache_admin_cache_config.html');
        $render->setAttribute('actionForm', $this->mActionForm);

        // Format sizes for display
        $render->setAttribute('cache_limit_mb', $this->formatSize($this->mActionForm->get('cache_limit_smarty')));
        $render->setAttribute('cache_limit_alert_trigger_mb', $this->formatSize($this->mActionForm->get('cache_limit_alert_trigger')));
        $render->setAttribute('cache_limit_cleanup_mb', $this->formatSize($this->mActionForm->get('cache_limit_cleanup')));
        $render->setAttribute('cache_limit_compiled_mb', $this->formatSize($this->mActionForm->get('cache_limit_compiled')));

        // Pass the module object to the template
        if (is_object($this->mModuleObject)) {
            $render->setAttribute('module', $this->mModuleObject);
        } else {
            // Fallback or ensure $this->mModuleObject is always set
            // if template needs but it will fail if call getVar()
            // on null, best to be sure $this->mModuleObject is valid
        }
    }
}
