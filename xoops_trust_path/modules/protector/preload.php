<?php
/**
 * Protector module for XCL
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Check for direct access
if (!defined('XOOPS_ROOT_PATH')) {
    exit;
}

// Modernize by using proper class structure and namespacing
class ProtectorPreload extends XCube_ActionFilter
{
    public function preBlockFilter()
    {
        $this->mRoot->mDelegateManager->add('Legacy.PreBlockFilter', array($this, 'protectSystem'));
    }

    public function protectSystem()
    {
        // Include the main protection file
        require_once dirname(__FILE__) . '/class/protector.php';
        
        // Initialize protector
        $protector = Protector::getInstance();
        $protector->setLevel();
        
        // Run security checks
        $protector->filterReferer();
        $protector->filterSQL();
        $protector->filterXSS();
        $protector->filterDOS();
        $protector->filterBruteForce();
        
        // Check for bad behavior
        $protector->checkBadips();
        
        // Register shutdown function for cleanup
        register_shutdown_function(array($protector, 'cleanup'));
    }
}