<?php
/**
 * Standard cache - Module for XCL
 * Action to update last_cache_alert_time, primarily via AJAX from preload.
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

class StdCache_UpdateLastNotificationAction extends stdCache_Action
{
    /**
     * Determines if standard security checks (like admin login) by ActionFrame should be applied
     * For our specific AJAX call (identified by POST and specific parameters),
     * we bypass ActionFrame's admin check and handle token security within execute()
     *
     * @return bool False if expected AJAX call, true to enforce admin login
     */
    public function isSecure()
    {
        if ('POST' === xoops_getenv('REQUEST_METHOD') && isset($_POST['timestamp']) && isset($_POST['token'])) {
            // This is our expected AJAX call. Security (token) will be handled in execute()
            return false; 
        }
        return true;
    }
    
    /**
     * Executes the action to update the last notification time
     * Expects a POST request with 'timestamp' and 'token'
     */
    public function execute(&$controller, &$xoopsUser)
    {
        // the AJAX call we expect, otherwise it's an invalid request
        if (!('POST' === xoops_getenv('REQUEST_METHOD') && isset($_POST['timestamp']) && isset($_POST['token']))) {
            $this->logOperation('Invalid request (not POST or missing required parameters).', 'error');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method or parameters.']);
            exit();
        }

        $submittedToken = $_POST['token'] ?? '';
        $timestampValue = $_POST['timestamp'] ?? ''; 

        // Validate security token
        if (!$GLOBALS['xoopsSecurity']->validateToken($submittedToken, false)) { 
            $this->logOperation('Token validation FAILED. Submitted: ' . htmlspecialchars($submittedToken), 'error');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid security token.']);
            exit();
        }
        $this->logOperation('Token validation SUCCESSFUL.', 'info');

        // Validate and cast timestamp
        if (!is_numeric($timestampValue)) {
            $this->logOperation('Invalid timestamp received: ' . htmlspecialchars($timestampValue), 'error');
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid timestamp format.']);
            exit();
        }
        $timestampInt = (int)$timestampValue;

        $response = ['success' => false, 'message' => 'Update failed.']; 

        try {
            $cacheManager = new stdCache_CacheManager();
            $this->logOperation("Calling cacheManager->updateLastNotificationTime({$timestampInt})", 'info');
            
            if ($cacheManager->updateLastNotificationTime($timestampInt)) {
                $response['success'] = true;
                $response['message'] = 'Notification time updated successfully.';
                $response['timestamp_updated_to_raw'] = $timestampInt;
                $response['timestamp_updated_to_formatted'] = date('Y-m-d H:i:s', $timestampInt);
                // CacheManager::updateLastNotificationTime already logs its success/failure
            } else {
                // CacheManager::updateLastNotificationTime (via saveConfig) should have logged specific errors
                $response['message'] = 'Failed to update notification time via CacheManager. Check stdCache logs.';
                $this->logOperation('CacheManager->updateLastNotificationTime returned false for timestamp ' . $timestampInt, 'error');
            }
        } catch (Exception $e) {
            $response['message'] = 'An exception occurred during update: ' . $e->getMessage();
            $this->logOperation('Exception during update: ' . $e->getMessage(), 'error');
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    /**
     * This action is primarily an AJAX endpoint and does not render a standard view
     * If accessed directly, it redirects
     */
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        $this->logOperation('getDefaultView called - this action is AJAX only. Redirecting.', 'warning');
        if (class_exists('XCube_DelegateUtils')) {
            XCube_DelegateUtils::call('Legacy.Admin.Event.AddErrorMessage', 'This action is intended for AJAX calls only.');
        }
        $controller->executeRedirect(XOOPS_URL . '/modules/stdCache/admin/index.php?action=CacheStats', 1);
        return STDCACHE_FRAME_VIEW_NONE;
    }

    /**
     * Local logging helper for this action
     *
     * @param string $message Message to log
     * @param string $type    Log type (e.g., 'info', 'error', 'warning', 'debug')
     */
    protected function logOperation($message, $type = 'info') {
        // use STDCACHE_LOG prefix
        error_log("STDCACHE_LOG ({$type}) UpdateLastNotificationAction: {$message}");
    }
}
