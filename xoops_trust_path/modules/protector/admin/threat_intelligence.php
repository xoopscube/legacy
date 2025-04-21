<?php
/**
 * Protector Admin Threat Intelligence
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include gtickets class
require_once dirname(__DIR__) . '/class/gtickets.php';
$xoopsGTicket = new XoopsGTicket();

// Include header
xoops_cp_header();



// Get protector instance
$protector = protector::getInstance();

// Process form submission
if (isset($_POST['action']) && $_POST['action'] === 'update_ti_config') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header('index.php?page=threat_intelligence', 3, _NOPERM);
        exit;
    }
    
    // Get module config handler
    $module_handler = xoops_getHandler('module');
    $module = $module_handler->getByDirname('protector');
    $config_handler = xoops_getHandler('config');
    
    // Update HTTP:BL settings
    $httpbl_enabled = isset($_POST['httpbl_enabled']) ? 1 : 0;
    $httpbl_key = isset($_POST['httpbl_key']) ? trim($_POST['httpbl_key']) : '';
    $httpbl_threat_threshold = isset($_POST['httpbl_threat_threshold']) ? (int)$_POST['httpbl_threat_threshold'] : 25;
    
    // Update feed URLs
    $feed_urls = isset($_POST['ti_feed_urls']) ? trim($_POST['ti_feed_urls']) : '';
    
    // Update check points
    $check_login = isset($_POST['ti_check_login']) ? 1 : 0;
    $check_register = isset($_POST['ti_check_register']) ? 1 : 0;
    $check_forms = isset($_POST['ti_check_forms']) ? 1 : 0;
    $check_admin = isset($_POST['ti_check_admin']) ? 1 : 0;
    
    // Update cache settings
    $cache_duration = isset($_POST['ti_cache_duration']) ? (int)$_POST['ti_cache_duration'] : 3600;
    
    // Save all configs
    $configs = [
        'httpbl_enabled' => $httpbl_enabled,
        'httpbl_key' => $httpbl_key,
        'httpbl_threat_threshold' => $httpbl_threat_threshold,
        'ti_feed_urls' => $feed_urls,
        'ti_check_login' => $check_login,
        'ti_check_register' => $check_register,
        'ti_check_forms' => $check_forms,
        'ti_check_admin' => $check_admin,
        'ti_cache_duration' => $cache_duration
    ];
    
    // Update each config
    foreach ($configs as $name => $value) {
        $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
        $criteria->add(new Criteria('conf_name', $name));
        $config_matches = $config_handler->getConfigs($criteria);
        
        if (count($config_matches) > 0) {
            // Update existing config
            $config = $config_matches[0];
            $config->setVar('conf_value', $value);
            $config_handler->insertConfig($config);
        } else {
            // Create new config
            $config = $config_handler->createConfig();
            $config->setVar('conf_modid', $module->getVar('mid'));
            $config->setVar('conf_catid', 0);
            $config->setVar('conf_name', $name);
            $config->setVar('conf_value', $value);
            $config_handler->insertConfig($config);
        }
    }
    
    // Clear cache if settings changed
    $cache_file = XOOPS_TRUST_PATH . '/cache/protector_ti_cache.php';
    if (file_exists($cache_file)) {
        @unlink($cache_file);
    }
    
    redirect_header('index.php?page=threat_intelligence', 3, _AM_PROTECTOR_UPDATED);
    exit;
}

// Test HTTP:BL connection if requested
if (isset($_GET['op']) && $_GET['op'] === 'test_httpbl') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(false, 'protector_admin')) {
        redirect_header('index.php?page=threat_intelligence', 3, _NOPERM);
        exit;
    }
    
    // Get current config
    $module_handler = xoops_getHandler('module');
    $module = $module_handler->getByDirname('protector');
    $config_handler = xoops_getHandler('config');
    $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
    $criteria->add(new Criteria('conf_name', 'httpbl_key'));
    $configs = $config_handler->getConfigs($criteria);
    
    $api_key = '';
    if (count($configs) > 0) {
        $api_key = $configs[0]->getVar('conf_value');
    }
    
    // Test with a known bad IP (Project Honeypot test IP)
    $test_ip = '127.1.1.1';
    $reverse_ip = implode('.', array_reverse(explode('.', $test_ip)));
    $lookup = $api_key . '.' . $reverse_ip . '.dnsbl.httpbl.org';
    
    $result = gethostbyname($lookup);
    
    if ($result !== $lookup && strpos($result, '127.') === 0) {
        $message = _AM_PROTECTOR_HTTPBL_TEST_SUCCESS;
    } else {
        $message = _AM_PROTECTOR_HTTPBL_TEST_FAILURE;
    }
    
    redirect_header('index.php?page=threat_intelligence', 3, $message);
    exit;
}

// Get current config values
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname('protector');
$config_handler = xoops_getHandler('config');
$configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

// Set default values if not set
$httpbl_enabled = $configs['httpbl_enabled'] ?? 0;
$httpbl_key = $configs['httpbl_key'] ?? '';
$httpbl_threat_threshold = $configs['httpbl_threat_threshold'] ?? 25;
$feed_urls = $configs['ti_feed_urls'] ?? '';
$check_login = $configs['ti_check_login'] ?? 1;
$check_register = $configs['ti_check_register'] ?? 1;
$check_forms = $configs['ti_check_forms'] ?? 0;
$check_admin = $configs['ti_check_admin'] ?? 0;
$cache_duration = $configs['ti_cache_duration'] ?? 3600;


// Display admin menu
include __DIR__ . '/mymenu.php';

// Display page title
echo '<h3>' . _AM_PROTECTOR_THREAT_INTELLIGENCE . '</h3>';

// Display description
echo '<div class="tips">';
echo '<p>' . _AM_PROTECTOR_THREAT_INTELLIGENCE_DESC . '</p>';
echo '</div>';

// Display form
echo '<form action="index.php?page=threat_intelligence" method="post">';
echo $xoopsGTicket->getTicketHtml('protector_admin');
echo '<input type="hidden" name="action" value="update_ti_config">';

// HTTP:BL Settings
echo '<table class="outer" width="100%">';
echo '<thead><tr><th colspan="2">' . _AM_PROTECTOR_HTTPBL_SETTINGS . '</th></tr></thead>';

echo '<tbody><tr class="even"><td width="30%">' . _AM_PROTECTOR_HTTPBL_ENABLED . '</td>';
echo '<td><input type="checkbox" name="httpbl_enabled" value="1"' . ($httpbl_enabled ? ' checked' : '') . '></td></tr>';

echo '<tr class="odd"><td>' . _AM_PROTECTOR_HTTPBL_KEY . '</td>';
echo '<td><input type="text" name="httpbl_key" value="' . htmlspecialchars($httpbl_key) . '" size="40">';

echo ' <a href="index.php?page=threat_intelligence&op=test_httpbl' . $xoopsGTicket->getTicketParamString('protector_admin') . '" class="formButton">' . _AM_PROTECTOR_HTTPBL_TEST . '</a>';
echo '<br><small>' . _AM_PROTECTOR_HTTPBL_KEY_DESC . '</small></td></tr>';

echo '<tr class="even"><td>' . _AM_PROTECTOR_HTTPBL_THREAT_THRESHOLD . '</td>';
echo '<td><input type="number" name="httpbl_threat_threshold" value="' . $httpbl_threat_threshold . '" min="0" max="255">';
echo '<br><small>' . _AM_PROTECTOR_HTTPBL_THREAT_THRESHOLD_DESC . '</small></td></tr>';

// Feed URLs
echo '<tr><th colspan="2">' . _AM_PROTECTOR_FEED_SETTINGS . '</th></tr>';

echo '<tr class="odd"><td>' . _AM_PROTECTOR_FEED_URLS . '</td>';
echo '<td><textarea name="ti_feed_urls" rows="4" cols="60">' . htmlspecialchars($feed_urls) . '</textarea>';
echo '<br><small>' . _AM_PROTECTOR_FEED_URLS_DESC . '</small></td></tr>';

// Check Points
echo '<tr><th colspan="2">' . _AM_PROTECTOR_CHECK_POINTS . '</th></tr>';

echo '<tr class="even"><td>' . _AM_PROTECTOR_CHECK_LOGIN . '</td>';
echo '<td><input type="checkbox" name="ti_check_login" value="1"' . ($check_login ? ' checked' : '') . '>';
echo '<br><small>' . _AM_PROTECTOR_CHECK_LOGIN_DESC . '</small></td></tr>';

echo '<tr class="odd"><td>' . _AM_PROTECTOR_CHECK_REGISTER . '</td>';
echo '<td><input type="checkbox" name="ti_check_register" value="1"' . ($check_register ? ' checked' : '') . '>';
echo '<br><small>' . _AM_PROTECTOR_CHECK_REGISTER_DESC . '</small></td></tr>';

echo '<tr class="even"><td>' . _AM_PROTECTOR_CHECK_FORMS . '</td>';
echo '<td><input type="checkbox" name="ti_check_forms" value="1"' . ($check_forms ? ' checked' : '') . '>';
echo '<br><small>' . _AM_PROTECTOR_CHECK_FORMS_DESC . '</small></td></tr>';

echo '<tr class="odd"><td>' . _AM_PROTECTOR_CHECK_ADMIN . '</td>';
echo '<td><input type="checkbox" name="ti_check_admin" value="1"' . ($check_admin ? ' checked' : '') . '>';
echo '<br><small>' . _AM_PROTECTOR_CHECK_ADMIN_DESC . '</small></td></tr>';

// Cache Settings
echo '<tr><th colspan="2">' . _AM_PROTECTOR_CACHE_SETTINGS . '</th></tr>';

echo '<tr class="even"><td>' . _AM_PROTECTOR_CACHE_DURATION . '</td>';
echo '<td><select name="ti_cache_duration">';
echo '<option value="3600"' . ($cache_duration == 3600 ? ' selected' : '') . '>' . _AM_PROTECTOR_CACHE_1HOUR . '</option>';
echo '<option value="21600"' . ($cache_duration == 21600 ? ' selected' : '') . '>' . _AM_PROTECTOR_CACHE_6HOURS . '</option>';
echo '<option value="86400"' . ($cache_duration == 86400 ? ' selected' : '') . '>' . _AM_PROTECTOR_CACHE_1DAY . '</option>';
echo '<option value="604800"' . ($cache_duration == 604800 ? ' selected' : '') . '>' . _AM_PROTECTOR_CACHE_1WEEK . '</option>';
echo '</select></td></tr></tbody>';

// Submit button
echo '<tfoot><tr class="foot"><td colspan="2">';
echo '<input type="submit" value="' . _AM_PROTECTOR_UPDATE . '" class="formButton">';
echo '</td></tr></tfoot>';

echo '</table>';
echo '</form>';

// Include footer
xoops_cp_footer();