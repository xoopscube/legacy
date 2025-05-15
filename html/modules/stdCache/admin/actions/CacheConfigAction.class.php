<?php
/**
 * Standard cache - Module for XCL
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
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
     * @var stdCache_CacheConfigForm
     */
    protected $mActionForm = null;

    /**
     * @var stdCache_CacheManager
     */
    protected $mCacheManager = null;
    
    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        // Ensure $xoopsUser is an object before calling isAdmin
        return (is_object($xoopsUser) && $xoopsUser->isAdmin());
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        parent::prepare($controller, $xoopsUser, $moduleConfig); // Call parent's prepare

        if (!(is_object($xoopsUser) && $xoopsUser->isAdmin())) {
            $controller->executeForward(XOOPS_URL . '/');
            return false; // Stop further execution
        }
        
        $this->mActionForm = new stdCache_CacheConfigForm();
        $this->mActionForm->prepare();

        $this->mCacheManager = new stdCache_CacheManager(); // Instantiate CacheManager here
        
        return true;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if (!$this->mCacheManager) { // Should have been set in prepare()
            $this->mCacheManager = new stdCache_CacheManager();
        }
        $config = $this->mCacheManager->getConfigs(); // This gets all configs for the module
        
        // Fallback defaults if not found in $config (database)
        $this->mActionForm->set('cache_limit', $config['cache_limit'] ?? 50000000); // 50MB
        $this->mActionForm->set('cache_notification_limit', $config['cache_notification_limit'] ?? 40000000); // 40MB
        $this->mActionForm->set('cache_cleanup_limit', $config['cache_cleanup_limit'] ?? 45000000); // 45MB
        $this->mActionForm->set('compiled_templates_limit', $config['compiled_templates_limit'] ?? 20000000); // 20MB
        $this->mActionForm->set('notification_enabled', $config['notification_enabled'] ?? 1); // Default to enabled
        
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
        $this->mActionForm->validate(); // Call validate() - it sets internal error flags
        
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
        parent::executeViewSuccess($controller, $xoopsUser, $render); // Call parent
        $controller->executeForward('./index.php?action=CacheStats');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render); // Call parent
        $this->_setupViewInputErrorCommon($render); 
        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewInput($controller, $xoopsUser, $render); // Call parent
        $this->_setupViewInputErrorCommon($render); 
        return true;
    }
    
    protected function _setupViewInputErrorCommon(&$render)
    {
        $render->setTemplateName('stdcache_admin_cache_config.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        
        $render->setAttribute('cache_limit_mb', $this->formatSize($this->mActionForm->get('cache_limit')));
        $render->setAttribute('cache_notification_limit_mb', $this->formatSize($this->mActionForm->get('cache_notification_limit')));
        $render->setAttribute('cache_cleanup_limit_mb', $this->formatSize($this->mActionForm->get('cache_cleanup_limit')));
        $render->setAttribute('compiled_templates_limit_mb', $this->formatSize($this->mActionForm->get('compiled_templates_limit')));
    }
    
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewCancel($controller, $xoopsUser, $render); // Call parent
        $controller->executeForward('./index.php?action=CacheStats');
    }

    protected function _getPagetitle() 
    {
        return defined('_AD_STDCACHE_CONFIG') ? _AD_STDCACHE_CONFIG : 'Cache Configuration';
    }
}
