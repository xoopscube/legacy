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

require_once __DIR__ . '/../class/Action.class.php';
require_once __DIR__ . '/../class/CacheManager.class.php';
// Legacy_Utils is not directly used here, can be removed if not needed by parent or other methods
// require_once XOOPS_ROOT_PATH . '/modules/legacy/class/Legacy_Utils.class.php'; 

class stdCache_CacheStatsAction extends stdCache_Action
{
    protected $mObjectHandler = null;
    protected $mStats = [];
    
    protected function _getHandler()
    {
        if (!$this->mObjectHandler) {
            // Use try-catch during object instantiation
            try {
                $this->mObjectHandler = new stdCache_CacheManager();
            } catch (Exception $e) {
                // Log the error and rethrow or handle
                // For now, rethrowing to indicate a critical failure.
                // In a production environment, log this and show a user-friendly error.
                error_log("stdCache_CacheStatsAction: Failed to initialize CacheManager - " . $e->getMessage());
                throw new RuntimeException('Failed to initialize CacheManager: ' . $e->getMessage());
            }
        }
        return $this->mObjectHandler;
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $cacheManager = $this->_getHandler();
        $this->mStats = $cacheManager->getCacheStats();
        return STDCACHE_FRAME_VIEW_INDEX;
    }
    
    protected function _getPagetitle()
    {
        // Ensure language constant is defined
        return defined('_MI_STDCACHE_ADMENU_STATS') ? _MI_STDCACHE_ADMENU_STATS : 'Cache Statistics';
    }

    public function hasPermission(&$controller, &$xoopsUser, $moduleConfig)
    {
        // Ensure $xoopsUser is an object before calling isAdmin
        return (is_object($xoopsUser) && $xoopsUser->isAdmin());
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        // Parent prepare might do something important
        parent::prepare($controller, $xoopsUser, $moduleConfig);

        if (!(is_object($xoopsUser) && $xoopsUser->isAdmin())) {
            $controller->executeForward(XOOPS_URL . '/');
            return false; // Important to return false to stop further execution by ActionFrame
        }
        return true;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        // For GET requests, this action primarily displays stats.
        // POST requests could be for actions like "refresh stats" if implemented.
        return $this->getDefaultView($controller, $xoopsUser);
    }

    private function formatSize($bytes)
    {
        // This method is also in CacheManager. 
        // Consider moving to a utility class or using CacheManager's instance.
        // For now, keeping it here as it's used by this action's view rendering.
        // If $bytes is already formatted (e.g. from CacheManager), this might not be needed here.
        // Assuming $this->mStats contains raw byte values for sizes.

        if (!is_numeric($bytes)) {
             // If it's an array or non-numeric, try to get a numeric value from it
            if (is_array($bytes) && isset($bytes['size']) && is_numeric($bytes['size'])) {
                $bytes = $bytes['size'];
            } else {
                return '0 B'; // Default for unknown or invalid input
            }
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max((float)$bytes, 0); // Ensure float and non-negative
        if ($bytes == 0) {
            return '0 ' . $units[0];
        }
        $pow = floor(log($bytes) / log(1024)); // Use floor(log($bytes, 1024))
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewIndex($controller, $xoopsUser, $render); // Call parent for common setup

        $render->setTemplateName('stdcache_admin_cache_stats.html');
        
        // Set standard attributes needed by XCube admin templates
        $render->setAttribute('actionForm', null); // No form on stats page itself
        $render->setAttribute('xoops_token', $this->_getToken()); // If any action needs a token
        
        // Calculations for Progress Bars
        $smarty_cache_current_bytes = (float)($this->mStats['cache_size'] ?? 0);
        $smarty_cache_limit_bytes = (float)($this->mStats['cache_limit'] ?? 0);

        $cache_percentage_raw = 0; // For the bar width
        if ($smarty_cache_limit_bytes > 0) {
            $cache_percentage_raw = ($smarty_cache_current_bytes / $smarty_cache_limit_bytes) * 100;
        }
        // Ensure percentage for bar width is not over 100 and not negative
        $cache_percentage_bar = max(0, min(100, $cache_percentage_raw));

        // For the display text inside the bar
        $cache_percentage_text = round($cache_percentage_raw, 0); // Round to whole number
        if ($cache_percentage_raw > 0 && $cache_percentage_text == 0) {
            $cache_percentage_text = 1; // Show at least 1% if there's any usage
        }
        // Ensure display text is also capped at 100 if raw value was > 100
        $cache_percentage_text = min(100, $cache_percentage_text);


        $compiled_templates_current_bytes = (float)($this->mStats['compiled_size'] ?? 0);
        $compiled_templates_limit_bytes = (float)($this->mStats['compiled_limit'] ?? 0);

        $compiled_percentage_raw = 0; // For the bar width
        if ($compiled_templates_limit_bytes > 0) {
            $compiled_percentage_raw = ($compiled_templates_current_bytes / $compiled_templates_limit_bytes) * 100;
        }
        $compiled_percentage_bar = max(0, min(100, $compiled_percentage_raw));

        // For the display text inside the bar
        $compiled_percentage_text = round($compiled_percentage_raw, 0);
        if ($compiled_percentage_raw > 0 && $compiled_percentage_text == 0) {
            $compiled_percentage_text = 1;
        }
        $compiled_percentage_text = min(100, $compiled_percentage_text);


        // Set the stats data for the template
        $render->setAttribute('compiled_count', $this->mStats['compiled_file_count'] ?? 0); 
        $render->setAttribute('compiled_size', $this->formatSize($this->mStats['compiled_size'] ?? 0));
        $render->setAttribute('compiled_subdirs', $this->mStats['compiled_subdirs'] ?? 0);
        // Pass both percentages for compiled templates
        $render->setAttribute('compiled_percentage_bar', round($compiled_percentage_bar, 2));
        $render->setAttribute('compiled_percentage_text', $compiled_percentage_text);

        // Smarty Cache / "Cache Files" section
        $render->setAttribute('cache_count', $this->mStats['smarty_cache_file_count'] ?? 0);
        $render->setAttribute('cache_subdirs', $this->mStats['smarty_cache_subdirs'] ?? 0);
        $render->setAttribute('cache_size', $this->formatSize($this->mStats['cache_size'] ?? 0));
        // Pass both percentages for Smarty cache
        $render->setAttribute('cache_percentage_bar', round($cache_percentage_bar, 2));
        $render->setAttribute('cache_percentage_text', $cache_percentage_text);
        
        // Add Log stats
        $render->setAttribute('logs_count', $this->mStats['logs_file_count'] ?? 0); 
        $render->setAttribute('log_size', $this->formatSize($this->mStats['logs_size'] ?? 0));
        $render->setAttribute('logs_subdirs', $this->mStats['logs_subdirs'] ?? 0);
        
        $render->setAttribute('upload_count', $this->mStats['upload_file_count'] ?? 0); 
        $render->setAttribute('upload_subdirs', $this->mStats['upload_subdirs'] ?? 0);
        $render->setAttribute('upload_size', $this->formatSize($this->mStats['upload_size'] ?? 0));

        $render->setAttribute('total_size', $this->formatSize($this->mStats['total_size'] ?? 0));
        
        $render->setAttribute('cache_limit', $this->formatSize($this->mStats['cache_limit'] ?? 0)); 
        $render->setAttribute('compiled_limit_config', $this->formatSize($this->mStats['compiled_limit'] ?? 0));
        
        if (is_object($controller->mRoot) && is_object($controller->mRoot->mContext) && is_object($controller->mRoot->mContext->mXoopsModule)) {
            $render->setAttribute('module', $controller->mRoot->mContext->mXoopsModule);
        }
        
        return true;
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        // Action redirect to the stats page to show fresh data.
        $controller->executeForward('./index.php?action=CacheStats');
        // No direct rendering needed as executeForward handles it.
        // ActionFrame will not call render target if controller->executeForward is used.
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        parent::executeViewError($controller, $xoopsUser, $render); // Call parent for common setup

        $render->setTemplateName('stdcache_admin_cache_stats.html'); // Or a dedicated error template
        
        // NOTE: Set empty/default values for template to prevent undefined variables
        // or run formatSize() on non-numeric data, use human-readable default value '0 B'
        $render->setAttribute('compiled_count', 0);
        $render->setAttribute('compiled_size', '0 B'); // default
        $render->setAttribute('compiled_subdirs', 0);
        $render->setAttribute('cache_count', 0); 
        $render->setAttribute('cache_subdirs', 0);
        $render->setAttribute('cache_size', '0 B'); // default
        $render->setAttribute('logs_count', 0);
        $render->setAttribute('logs_subdirs', 0);
        $render->setAttribute('log_size', '0 B'); // default
        $render->setAttribute('upload_count', 0);
        $render->setAttribute('upload_subdirs', 0);
        $render->setAttribute('upload_size', '0 B'); // default
        $render->setAttribute('total_size', '0 B'); // default
        $render->setAttribute('cache_limit', '0 B'); // default
        $render->setAttribute('compiled_limit_config', '0 B');

        if (is_object($controller->mRoot) && is_object($controller->mRoot->mContext) && is_object($controller->mRoot->mContext->mXoopsModule)) {
            $render->setAttribute('module', $controller->mRoot->mContext->mXoopsModule);
        }
        
        return true;
    }

    protected function _getToken()
    {
        // A more secure token should be generated and validated properly
        // For admin pages, even for non-critical ops, consider ActionForm security.
        // For now, it's for basic identification and placeholder.
        $user = XCube_Root::getSingleton()->mContext->mXoopsUser;
        if (is_object($user)) {
            return md5($user->get('uname') . XOOPS_SALT);
        }
        return md5('guest' . XOOPS_SALT); // Fallback
    }
}
