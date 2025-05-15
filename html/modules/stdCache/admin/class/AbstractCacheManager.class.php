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

class stdCache_AbstractCacheManager
{
    protected $xoopsConfig;
    protected $logger;
    protected $cacheDirs;

    public function __construct()
    {
        $root = XCube_Root::getSingleton();
        $this->xoopsConfig = $root->mContext->mModuleConfig;
        // Corrected line to get the logger from the controller
        if (isset($root->mController) && is_object($root->mController)) {
            $this->logger = $root->mController->mLogger;
        } else {
            // Fallback or error handling if controller or logger isn't available
            // This might happen if the constructor is called very early
            $this->logger = null; 
            // Optionally, log an error here if a more robust logging mechanism is available
            // error_log('stdCache_AbstractCacheManager: XCube_Controller or mLogger not available.');
        }
        
        $this->cacheDirs = [
            'templates_c' => XOOPS_TRUST_PATH . '/templates_c', // Smarty compiled templates
            'cache' => XOOPS_TRUST_PATH . '/cache',            // Smarty cache (typically from XOOPS_TRUST_PATH)
            'logs' => XOOPS_TRUST_PATH . '/cache/logs',       // Targets only files logs
            'uploads' => XOOPS_TRUST_PATH . '/uploads'       // Changed from target the public uploads directory
        ];
    }

    protected function validateDirectory($dir)
    {
        if (!is_dir($dir)) {
            throw new Exception(sprintf('Directory %s does not exist', $dir));
        }
        if (!is_writable($dir)) {
            throw new Exception(sprintf('Directory %s is not writable', $dir));
        }
    }

    protected function logOperation($message, $type = 'info')
    {
        // Check if logger was successfully initialized before using it
        if ($this->logger && method_exists($this->logger, 'add')) {
            $this->logger->add($type, $message, 'stdCache');
        }
    }
}
