<?php
/**
 * Standard cache - Module for XCL
 * Action to display cache statistics.
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

require_once __DIR__ . '/../class/Action.class.php';
require_once __DIR__ . '/../class/CacheManager.class.php';
require_once __DIR__ . '/../forms/CacheStatsForm.class.php';
class stdCache_CacheStatsAction extends stdCache_Action
{
    /**
     * @var XCube_Root
     */
    protected $mRoot = null;

    /**
     * @var array Holds the fetched cache stats
     */
    protected $mStats = [];

    /**
     * @var stdCache_CacheManager
     */
    protected $mCacheManager = null;

    /**
     * @var CacheStatsForm Used for token
     */
    protected $mActionForm = null;

    /**
     * @var XoopsModule|null
     */
    protected $mModuleObject = null;


    public function __construct()
    {
        parent::__construct(); 
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
            $controller->executeForward(XOOPS_URL . '/');
            return false; 
        }

        try {
            $this->mCacheManager = new stdCache_CacheManager();
        } catch (Exception $e) {

            $this->mCacheManager = null; 
            // Optionally, add an error message to be displayed
            if (class_exists('XCube_DelegateUtils')) {
                XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', 'Error: Cache statistics service is currently unavailable.');
            }

        }
        
        // Get XoopsModule object
        if (is_object($this->mRoot->mContext->mModule)) {
            $this->mModuleObject = $this->mRoot->mContext->mModule->getXoopsModule();
        }

        // Prepare the form for token if any planned action on this page
        $this->mActionForm = new CacheStatsForm();
        $this->mActionForm->prepare();

        return true;
    }

    /**
     * Default action for GET requests: display stats
     */
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        if ($this->mCacheManager) {
            $this->mStats = $this->mCacheManager->getCacheStats();
        } else {
            // If CacheManager failed to initialize, mStats will be empty
            $this->mStats = []; 
            // Error message should have been added in prepare()
        }
        return STDCACHE_FRAME_VIEW_INDEX; // Use VIEW_INDEX for the main display
    }

    /**
     * Handles POST requests. Currently, no specific POST actions are defined for this page
     * beyond what ActionFrame handles (like token validation if a form were submitted)
     */
    public function execute(&$controller, &$xoopsUser)
    {
        // This action is primarily for displaying stats (GET request)
        // If there were POST actions (e.g., a "Refresh Stats" button with a form),
        // any planned action should be handled here after token validation
        // For now, it defaults to showing the stats
        return $this->getDefaultView($controller, $xoopsUser);
    }

    protected function _getPagetitle()
    {
        return defined('_AD_STDCACHE_STATS_TITLE') ? _AD_STDCACHE_STATS_TITLE : 'Cache Statistics';
    }

    /**
     * Set common attributes for render
     * @param XCube_RenderTarget $render
     */
    protected function _setupViewCommon(&$render)
    {
        $render->setTemplateName('stdcache_admin_cache_stats.html');
        $render->setAttribute('actionForm', $this->mActionForm); // For token

        if (is_object($this->mModuleObject)) {
            $render->setAttribute('module', $this->mModuleObject);
        }
        
        // Ensure mCacheManager is available for formatting sizes
        if (!$this->mCacheManager && class_exists('stdCache_CacheManager')) {
            // Attempt to re-initialize if it failed in prepare but is needed now
            // This is a fallback; ideally, prepare() should handle it
            try {
                $this->mCacheManager = new stdCache_CacheManager();
            } catch (Exception $e) {
                // Log again if it fails here too
                error_log("STDCACHE_LOG (error) CacheStatsAction: Failed to re-initialize CacheManager in _setupViewCommon - " . $e->getMessage());
                $this->mCacheManager = null;
            }
        }
        
        // Fetch module configurations
        $moduleConfigs = [];
        if ($this->mCacheManager) {
            $moduleConfigs = $this->mCacheManager->getConfigs();
        }
        // Assign the notification enable status to the template
        // Use !empty() to treat 0, null, false, etc., as disabled
        $isNotificationEnabled = !empty($moduleConfigs['cache_limit_alert_enable']);
        

        // Helper function to format size, using CacheManager if available
        $formatSizeFunc = function($bytes) {
            if ($this->mCacheManager) {
                return $this->mCacheManager->formatSize($bytes);
            }
            // Basic fallback formatter if CacheManager is unavailable
            if (!is_numeric($bytes)) return '0 B';
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max((float)$bytes, 0);
            if ($bytes == 0) return '0 ' . $units[0];
            $pow = floor(log($bytes) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            return round($bytes, 2) . ' ' . $units[$pow];
        };

        // Calculate and set stats attributes for easier the template design
        $smarty_cache_current_bytes = (float)($this->mStats['cache_size'] ?? 0);
        $smarty_cache_limit_bytes = (float)($this->mStats['cache_limit_smarty'] ?? 0); // This is the general limit, not notification limit
        $cache_percentage_raw = ($smarty_cache_limit_bytes > 0) ? ($smarty_cache_current_bytes / $smarty_cache_limit_bytes) * 100 : 0;
        $cache_percentage_bar = max(0, min(100, $cache_percentage_raw));
        $cache_percentage_text = round($cache_percentage_raw, 0);
        if ($cache_percentage_raw > 0 && $cache_percentage_text == 0 && $smarty_cache_current_bytes > 0) $cache_percentage_text = 1; // Show 1% if > 0 but < 1%
        $cache_percentage_text = min(100, $cache_percentage_text);

        $compiled_templates_current_bytes = (float)($this->mStats['compiled_size'] ?? 0);
        $cache_limit_compiled_bytes = (float)($this->mStats['compiled_limit'] ?? 0);
        $compiled_percentage_raw = ($cache_limit_compiled_bytes > 0) ? ($compiled_templates_current_bytes / $cache_limit_compiled_bytes) * 100 : 0;
        $compiled_percentage_bar = max(0, min(100, $compiled_percentage_raw));
        $compiled_percentage_text = round($compiled_percentage_raw, 0);
        if ($compiled_percentage_raw > 0 && $compiled_percentage_text == 0 && $compiled_templates_current_bytes > 0) $compiled_percentage_text = 1;
        $compiled_percentage_text = min(100, $compiled_percentage_text);

        // Alert Email Notification
        $render->setAttribute('isNotificationEnabled', $isNotificationEnabled);

        $render->setAttribute('compiled_count', $this->mStats['compiled_file_count'] ?? 0);
        $render->setAttribute('compiled_size', $formatSizeFunc($this->mStats['compiled_size'] ?? 0));
        $render->setAttribute('compiled_subdirs', $this->mStats['compiled_subdirs'] ?? 0);
        $render->setAttribute('compiled_percentage_bar', round($compiled_percentage_bar, 2));
        $render->setAttribute('compiled_percentage_text', $compiled_percentage_text);
        $render->setAttribute('compiled_limit_config', $formatSizeFunc($this->mStats['compiled_limit'] ?? 0));


        $render->setAttribute('cache_count', $this->mStats['smarty_cache_file_count'] ?? 0);
        $render->setAttribute('cache_subdirs', $this->mStats['smarty_cache_subdirs'] ?? 0); 
        $render->setAttribute('cache_size', $formatSizeFunc($this->mStats['cache_size'] ?? 0));
        $render->setAttribute('cache_percentage_bar', round($cache_percentage_bar, 2));
        $render->setAttribute('cache_percentage_text', $cache_percentage_text);
        $render->setAttribute('cache_limit_smarty', $formatSizeFunc($this->mStats['cache_limit_smarty'] ?? 0));


        $render->setAttribute('logs_count', $this->mStats['logs_file_count'] ?? 0);
        $render->setAttribute('logs_subdirs', $this->mStats['logs_subdirs'] ?? 0);
        $render->setAttribute('log_size', $formatSizeFunc($this->mStats['logs_size'] ?? 0));

        $render->setAttribute('upload_count', $this->mStats['upload_file_count'] ?? 0);
        $render->setAttribute('upload_subdirs', $this->mStats['upload_subdirs'] ?? 0);
        $render->setAttribute('upload_size', $formatSizeFunc($this->mStats['upload_size'] ?? 0));

        $render->setAttribute('total_size', $formatSizeFunc($this->mStats['total_size'] ?? 0));
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewIndex($controller, $xoopsUser, $render); 
        $this->_setupViewCommon($render);
        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        // This action typically doesn't have a separate "success" view from a POST
        // If it did, it would redirect or show a success message
        // For now, redirecting to itself (which will re-render stats)
        $controller->executeForward('./index.php?action=CacheStats');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render); 
        $this->_setupViewCommon($render); // Display the stats page, errors will be shown by messages
        return true;
    }
}
