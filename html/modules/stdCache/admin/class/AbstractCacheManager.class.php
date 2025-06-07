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

class stdCache_AbstractCacheManager
{
    protected $xoopsConfig;
    protected $logger;
    protected $cacheDirs;

    public function __construct()
    {
        $root = XCube_Root::getSingleton();
        $this->xoopsConfig = $root->mContext->mModuleConfig;
        if (isset($root->mController) && is_object($root->mController)) {
            $this->logger = $root->mController->mLogger;
        } else {
            $this->logger = null; 
        }
        
        $this->cacheDirs = [
            'templates_c' => XOOPS_TRUST_PATH . '/templates_c',
            'cache' => XOOPS_TRUST_PATH . '/cache',
            'logs' => XOOPS_TRUST_PATH . '/cache/logs', 
            'uploads' => XOOPS_TRUST_PATH . '/uploads'
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

    public function logOperation($message, $type = 'info')
    {
        error_log("STDCACHE_LOG ({$type}): {$message}");
    }

}
