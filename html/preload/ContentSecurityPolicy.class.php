<?php
/**
 * Content Security Policy implementation
 *
 * @package    XCL
 * @version    2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025
 */

class ContentSecurityPolicy extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        // Register with high priority to ensure it runs before output starts
        $this->mRoot->mDelegateManager->add('Legacy_RenderSystem.SetupXoopsTpl', array($this, 'addCSPHeaders'), XCUBE_DELEGATE_PRIORITY_FIRST);
        
        // Also hook into the header output specifically
        $this->mRoot->mDelegateManager->add('Legacy_RenderSystem.RenderTheme', array($this, 'ensureCSPHeaders'), XCUBE_DELEGATE_PRIORITY_FIRST);
        
        // Don't try to start a session - XCube already handles this
        // if (!isset($_SESSION)) {
        //     @session_start();
        // }
        
        // Create log directory if it doesn't exist
        $log_dir = XOOPS_CACHE_PATH . '/protector/logs';
        if (!is_dir($log_dir)) {
            @mkdir($log_dir, 0755, true);
        }
    }

    public function addCSPHeaders(&$xoopsTpl)
    {
        // Skip if headers already sent
        if (headers_sent()) {
            $this->logDebug('Headers already sent when addCSPHeaders was called');
            return;
        }
        
        // Get CSP configuration
        $moduleHandler = xoops_gethandler('module');
        $configHandler = xoops_gethandler('config');

        $module = $moduleHandler->getByDirname('protector');
        if (!is_object($module)) {
            $this->logDebug('Protector module not found');
            return;
        }
        
        $configs = $configHandler->getConfigsByCat(0, $module->getVar('mid'));

        // Check if CSP is enabled
        if (empty($configs['enable_csp'])) {
            $this->logDebug('CSP is disabled in Protector settings');
            return;
        }
        
        // Build CSP header
        $policy = $this->buildCSPPolicy($configs);

        // Add CSP header
        header("Content-Security-Policy: " . $policy);
        
        // Add report-only header if configured
        if (!empty($configs['csp_report_only'])) {
            header("Content-Security-Policy-Report-Only: " . $policy);
        }

        // Add CSP meta tag for older browsers
        if (!empty($configs['csp_legacy_support']) && is_object($xoopsTpl)) {
            $meta_tag = '<meta http-equiv="Content-Security-Policy" content="' . htmlspecialchars($policy, ENT_QUOTES) . '">';
            $xoopsTpl->assign('xoops_csp_meta', $meta_tag);
            
            // Also add to mета array for theme compatibility
            $metas = $xoopsTpl->get_template_vars('xoops_meta');
            if (!is_array($metas)) {
                $metas = array();
            }
            $metas['csp'] = array('http-equiv' => 'Content-Security-Policy', 'content' => $policy);
            $xoopsTpl->assign('xoops_meta', $metas);
        }
        
        $this->logDebug('CSP headers added: ' . $policy);
    }
    
    // Ensure CSP headers are set even if the normal hook fails
    public function ensureCSPHeaders(&$xoopsTpl)
    {
        // Only proceed if we haven't already set headers and they haven't been sent yet
        if (!headers_sent() && !isset($GLOBALS['CSP_HEADERS_ADDED'])) {
            $this->addCSPHeaders($xoopsTpl);
            $GLOBALS['CSP_HEADERS_ADDED'] = true;
        }
    }

    private function buildCSPPolicy($configs)
    {
        $policy = array();

        // Default sources
        if (!empty($configs['csp_default_src'])) {
            $policy[] = "default-src " . $configs['csp_default_src'];
        } else {
            $policy[] = "default-src 'self'";
        }

        // Script sources
        if (!empty($configs['csp_script_src'])) {
            $policy[] = "script-src " . $configs['csp_script_src'];
        }

        // Style sources
        if (!empty($configs['csp_style_src'])) {
            $policy[] = "style-src " . $configs['csp_style_src'];
        }

        // Image sources
        if (!empty($configs['csp_img_src'])) {
            $policy[] = "img-src " . $configs['csp_img_src'];
        }

        // Connect sources
        if (!empty($configs['csp_connect_src'])) {
            $policy[] = "connect-src " . $configs['csp_connect_src'];
        }

        // Font sources
        if (!empty($configs['csp_font_src'])) {
            $policy[] = "font-src " . $configs['csp_font_src'];
        }

        // Object sources
        if (!empty($configs['csp_object_src'])) {
            $policy[] = "object-src " . $configs['csp_object_src'];
        }

        // Media sources
        if (!empty($configs['csp_media_src'])) {
            $policy[] = "media-src " . $configs['csp_media_src'];
        }

        // Frame sources
        if (!empty($configs['csp_frame_src'])) {
            $policy[] = "frame-src " . $configs['csp_frame_src'];
        }

        // Always add report-uri to collect violations
        $report_uri = !empty($configs['csp_report_uri']) 
            ? $configs['csp_report_uri'] 
            : XOOPS_URL . '/modules/protector/csp-report.php';
        
        $policy[] = "report-uri " . $report_uri;

        return implode('; ', $policy);
    }
    
    // Helper function for debugging
    private function logDebug($message)
    {
        // Get module config
        $moduleHandler = xoops_gethandler('module');
        $configHandler = xoops_gethandler('config');
        $module = $moduleHandler->getByDirname('protector');
        
        if (is_object($module)) {
            $configs = $configHandler->getConfigsByCat(0, $module->getVar('mid'));
            
            // Only log if debug is enabled
            if (!empty($configs['csp_debug'])) {
                $log_file = XOOPS_CACHE_PATH . '/protector/logs/csp_debug.log';
                $log_entry = date('Y-m-d H:i:s') . ' - ' . $message . "\n";
                @file_put_contents($log_file, $log_entry, FILE_APPEND);
                
                // Also log to PHP error log
                error_log('CSP: ' . $message);
            }
        }
    }
}
