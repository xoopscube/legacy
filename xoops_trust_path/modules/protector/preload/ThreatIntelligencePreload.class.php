<?php

/**
 * Protector Threat Intelligence Preload
 *
 * Integrates threat intelligence checks at key points in the system
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

class ThreatIntelligencePreload extends XCube_ActionFilter
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Register preload hooks
        $this->mRoot->mDelegateManager->add('Legacy.User.Login.Success', [$this, 'onLoginSuccess']);
        $this->mRoot->mDelegateManager->add('Legacy.User.Login.Fail', [$this, 'onLoginFail']);
        $this->mRoot->mDelegateManager->add('User.Register.Success', [$this, 'onRegisterSuccess']);
        $this->mRoot->mDelegateManager->add('Legacy.Event.UserDelete', [$this, 'onUserDelete']);
        
        // Form submission hooks
        $this->mRoot->mDelegateManager->add('Legacy.Event.PreBlockRender', [$this, 'onPreBlockRender']);
        
        // Admin area hooks
        $this->mRoot->mDelegateManager->add('Legacy.Admin.Event.GetMenu', [$this, 'onAdminGetMenu']);
    }
    
    /**
     * Check if threat intelligence is enabled and should check the current request
     */
    private function shouldCheckRequest($checkPoint)
    {
        // Get protector instance
        $protector = protector::getInstance();
        if (!$protector) {
            return false;
        }
        
        // Load ThreatIntelligence class
        require_once XOOPS_TRUST_PATH . '/modules/protector/class/ThreatIntelligence.class.php';
        $ti = new ProtectorThreatIntelligence();
        
        // Get config for the specific check point
        $module_handler = xoops_getHandler('module');
        $config_handler = xoops_getHandler('config');
        $module = $module_handler->getByDirname('protector');
        
        if (!$module) {
            return false;
        }
        
        $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
        $criteria->add(new Criteria('conf_name', 'ti_check_' . $checkPoint));
        $configs = $config_handler->getConfigs($criteria);
        
        if (count($configs) === 0 || $configs[0]->getVar('conf_value') != 1) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Check IP against threat intelligence
     */
    private function checkIp($ip, $context = '')
    {
        // Load ThreatIntelligence class
        require_once XOOPS_TRUST_PATH . '/modules/protector/class/ThreatIntelligence.class.php';
        $ti = new ProtectorThreatIntelligence();
        
        // Check IP against HTTP:BL
        $result = $ti->checkIpHttpBl($ip);
        
        // If malicious, log and take action
        if ($result && isset($result['is_malicious']) && $result['is_malicious']) {
            $protector = protector::getInstance();
            
            // Log the event
            $protector->message .= "Blocked malicious IP: {$ip} (Threat Score: {$result['threat_score']}, Type: " . 
                                  (is_array($result['visitor_type']) ? implode(',', $result['visitor_type']) : $result['visitor_type']) . 
                                  ") Context: {$context}\n";
            $protector->output_log('THREAT-INTELLIGENCE', 0, false, 128);
            
            // Return true to indicate the IP is malicious
            return true;
        }
        
        return false;
    }
    
    /**
     * Handle login success
     */
    public function onLoginSuccess(&$xoopsUser)
    {
        if (!$this->shouldCheckRequest('login')) {
            return;
        }
        
        // Get IP
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Check IP (we still log successful logins from malicious IPs)
        $this->checkIp($ip, 'login_success: ' . $xoopsUser->getVar('uname'));
    }
    
    /**
     * Handle login failure
     */
    public function onLoginFail($uname)
    {
        if (!$this->shouldCheckRequest('login')) {
            return;
        }
        
        // Get IP
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Check IP and block if malicious
        if ($this->checkIp($ip, 'login_fail: ' . $uname)) {
            // Add to temporary ban list
            $protector = protector::getInstance();
            $protector->register_bad_ips(time() + 86400);
            
            // Redirect to login page with error
            redirect_header(XOOPS_URL . '/user.php', 3, _MD_PROTECTOR_MALICIOUS_IP);
            exit;
        }
    }
    
    /**
     * Handle registration success
     */
    public function onRegisterSuccess(&$user, $isNew)
    {
        if (!$isNew || !$this->shouldCheckRequest('register')) {
            return;
        }
        
        // Get IP
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Check IP (we still log successful registrations from malicious IPs)
        $this->checkIp($ip, 'register: ' . $user->getVar('uname'));
    }
    
    /**
     * Handle form submissions
     */
    public function onPreBlockRender(&$block)
    {
        // Only check POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->shouldCheckRequest('forms')) {
            return;
        }
        
        // Get IP
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Check IP and block if malicious
        if ($this->checkIp($ip, 'form_submission: ' . $_SERVER['REQUEST_URI'])) {
            // Add to temporary ban list
            $protector = protector::getInstance();
            $protector->register_bad_ips(time() + 86400);
            
            // Redirect with error
            redirect_header(XOOPS_URL . '/index.php', 3, _MD_PROTECTOR_MALICIOUS_IP);
            exit;
        }
    }
    
    /**
     * Handle admin area access
     */
    public function onAdminGetMenu(&$menu)
    {
        if (!$this->shouldCheckRequest('admin')) {
            return;
        }
        
        // Get IP
        $ip = $_SERVER['REMOTE_ADDR'];
        
        // Check IP and block if malicious
        if ($this->checkIp($ip, 'admin_access: ' . $_SERVER['REQUEST_URI'])) {
            // Add to temporary ban list
            $protector = protector::getInstance();
            $protector->register_bad_ips(time() + 86400);
            
            // Redirect with error
            redirect_header(XOOPS_URL . '/index.php', 3, _MD_PROTECTOR_MALICIOUS_IP);
            exit;
        }
    }
}