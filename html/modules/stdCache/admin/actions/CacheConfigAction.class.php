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

class stdCache_CacheConfigAction extends stdCache_Action
{

    /**
     * @var XCube_Root
     */
    protected $mRoot = null;

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
    protected $mModuleObject = null;
    

    /**
     * Constructor
     */
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

        if (!(is_object($xoopsUser) && $xoopsUser->isAdmin())) {
            $controller->executeForward(XOOPS_URL . '/');
            return false;
        }

        // direct instantiation
        $this->mActionForm = new stdCache_CacheConfigForm();
        $this->mActionForm->prepare();

        try {
            $this->mCacheManager = new stdCache_CacheManager();
        } catch (Exception $e) {
            error_log("stdCache_CacheConfigAction: Failed to initialize CacheManager in prepare() - " . $e->getMessage());
            throw new RuntimeException('Failed to initialize CacheManager for CacheConfigAction: ' . $e->getMessage());
        }

        // Get and store the module object
        $module_handler = xoops_gethandler('module');
        if (is_object($module_handler)) {
            $this->mModuleObject = $module_handler->getByDirname('stdCache');
        }
        if (!is_object($this->mModuleObject)) {
            // Handle error: critical failure
            error_log("stdCache_CacheConfigAction: Failed to load stdCache module object.");
        }

        return true;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (!$this->mCacheManager) {
            $this->mCacheManager = new stdCache_CacheManager();
        }
        $config = $this->mCacheManager->getConfigs();
        
        // Fallback defaults
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
        $this->mActionForm->validate();
        
        if ($this->mActionForm->hasError()) { // Check using hasError()
            return STDCACHE_FRAME_VIEW_INPUT; // Re-display form with errors
        }
        
        if (!$this->mCacheManager) { 
            $this->mCacheManager = new stdCache_CacheManager();
        }
        
        $configValues = $this->mActionForm->getValues();
        
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
        $bytes = max((float)$bytes, 0); // float, and non-negative
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
            // Fallback or on null, best to be sure $this->mModuleObject is valid
        }
    }
}
