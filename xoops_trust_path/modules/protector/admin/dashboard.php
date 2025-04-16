<?php
/**
 * Protector Admin Dashboard
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// Display admin menu
include __DIR__ . '/mymenu.php';

// Get protector instance
$protector = protector::getInstance();

// Display dashboard content
echo '<h3>' . _MI_PROTECTOR_NAME . '</h3>';

// Display module information
echo '<div class="protector-dashboard">';
echo '<h4>' . _MI_PROTECTOR_DESC . '</h4>';
echo '<ul>';
// Use module version from xoopsModule instead of non-existent getVersion method
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname('protector');
echo '<li>Version: ' . $module->getVar('version') . '</li>';
// Check if module is active instead of using non-existent isEnabled method
echo '<li>Status: ' . ($module->getVar('isactive') ? 'Enabled' : 'Disabled') . '</li>';
echo '</ul>';

// Display quick links
echo '<h4>Admin Menu</h4>';
echo '<ul>';
echo '<li><a href="index.php?page=log">' . _MI_PROTECTOR_LOGLIST . '</a></li>';
echo '<li><a href="index.php?page=ban">' . _MI_PROTECTOR_IPBAN . '</a></li>';
echo '<li><a href="index.php?page=safe_list">IP Safe List</a></li>';
echo '<li><a href="index.php?page=prefix_manager">' . _MI_PROTECTOR_PREFIXMANAGER . '</a></li>';
echo '<li><a href="index.php?page=advisory">' . _MI_PROTECTOR_ADVISORY . '</a></li>';
echo '</ul>';
echo '</div>';

// Display Threat Intelligence section if enabled
$module_handler = xoops_getHandler('module');
$config_handler = xoops_getHandler('config');
$module = $module_handler->getByDirname('protector');
$configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

// Check if HTTP:BL is enabled
$httpbl_enabled = $configs['httpbl_enabled'] ?? 0;

if ($httpbl_enabled) {
    echo '<h3>' . _MI_PROTECTOR_THREAT_INTELLIGENCE_DASHBOARD . '</h3>';
    
    // Load ThreatIntelligence class
    require_once XOOPS_TRUST_PATH . '/modules/protector/class/ThreatIntelligence.class.php';
    $ti = new ProtectorThreatIntelligence();
    
    // Get database connection
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    // Get recent threat intelligence logs
    $result = $db->query('SELECT l.*, INET_NTOA(l.ip) AS ip_text 
                         FROM ' . $db->prefix($protector->mydirname . '_log') . ' l 
                         WHERE l.type = "THREAT-INTELLIGENCE"
                         ORDER BY timestamp DESC LIMIT 5');
    
    echo '<table class="outer" width="100%">';
    echo '<thead><tr><th>' . _AM_TH_DATETIME . '</th><th>' . _AM_TH_IP . '</th><th>' . _AM_TH_AGENT . '</th><th>' . _AM_TH_DESC . '</th></tr></thead>';
    
    $count = 0;
    while ($row = $db->fetchArray($result)) {
        echo '<tbody><tr class="' . ($count % 2 ? 'even' : 'odd') . '">';
        echo '<td>' . date('Y-m-d H:i:s', $row['timestamp']) . '</td>';
        echo '<td>' . $row['ip_text'] . '</td>';
        echo '<td>' . htmlspecialchars($row['agent']) . '</td>';
        echo '<td>' . htmlspecialchars($row['description']) . '</td>';
        echo '</tr></tbody>';
        $count++;
    }
    
    if ($count === 0) {
        echo '<tr><td colspan="4">' . _AM_PROTECTOR_NOTHREATSTATS . '</td></tr>';
    }
    
    echo '</table>';
    
    // Display quick settings link
    echo '<div class="tips">';
    echo '<p><a href="index.php?page=threat_intelligence" class="button">' . _MI_PROTECTOR_THREAT_INTELLIGENCE_SETTINGS . '</a></p>';
    echo '</div>';
}

// Display Web Proxy section if enabled
$proxy_enabled = $configs['proxy_enabled'] ?? 0;

if ($proxy_enabled) {
    echo '<h3>Web Proxy Protection</h3>';
    
    // Display proxy information
    echo '<div class="protector-dashboard">';
    echo '<p>The Web Proxy feature provides secure access to external resources while protecting your site from malicious content.</p>';
    
    // Get proxy statistics
    $proxy_stats = [
        'total_requests' => 0,
        'blocked_requests' => 0,
        'cached_resources' => 0
    ];
    
    if (file_exists(XOOPS_TRUST_PATH . '/modules/protector/cache/proxy_stats.php')) {
        include XOOPS_TRUST_PATH . '/modules/protector/cache/proxy_stats.php';
    }
    
    echo '<h4>Proxy Statistics</h4>';
    echo '<ul>';
    echo '<li>Total Requests: ' . $proxy_stats['total_requests'] . '</li>';
    echo '<li>Blocked Malicious Requests: ' . $proxy_stats['blocked_requests'] . '</li>';
    echo '<li>Cached Resources: ' . $proxy_stats['cached_resources'] . '</li>';
    echo '</ul>';
    
    // Display quick links
    echo '<h4>Proxy Management</h4>';
    echo '<ul>';
    echo '<li><a href="index.php?page=proxy_settings">Proxy Settings</a></li>';
    echo '<li><a href="index.php?page=proxy_logs">Proxy Logs</a></li>';
    echo '<li><a href="index.php?page=proxy_plugins">Proxy Plugins</a></li>';
    echo '<li><a href="' . XOOPS_URL . '/modules/protector/proxy.php" target="_blank">Access Proxy Interface</a></li>';
    echo '</ul>';
    echo '</div>';
}

// Include footer
xoops_cp_footer();