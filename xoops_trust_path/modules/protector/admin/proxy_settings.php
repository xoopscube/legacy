<?php
/**
 * Protector Web Proxy Settings
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

// Get module config
$module_handler = xoops_getHandler('module');
$config_handler = xoops_getHandler('config');
$module = $module_handler->getByDirname('protector');
$configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

// Process form submission
if (isset($_POST['submit'])) {
    // Update config values
    $config_handler->updateConfig($configs['proxy_enabled']['conf_id'], isset($_POST['proxy_enabled']) ? 1 : 0);
    $config_handler->updateConfig($configs['proxy_allowed_domains']['conf_id'], $_POST['proxy_allowed_domains']);
    $config_handler->updateConfig($configs['proxy_blocked_domains']['conf_id'], $_POST['proxy_blocked_domains']);
    $config_handler->updateConfig($configs['proxy_cache_enabled']['conf_id'], isset($_POST['proxy_cache_enabled']) ? 1 : 0);
    $config_handler->updateConfig($configs['proxy_cache_time']['conf_id'], (int)$_POST['proxy_cache_time']);
    $config_handler->updateConfig($configs['proxy_log_requests']['conf_id'], isset($_POST['proxy_log_requests']) ? 1 : 0);
    $config_handler->updateConfig($configs['proxy_strip_js']['conf_id'], isset($_POST['proxy_strip_js']) ? 1 : 0);
    $config_handler->updateConfig($configs['proxy_strip_cookies']['conf_id'], isset($_POST['proxy_strip_cookies']) ? 1 : 0);
    $config_handler->updateConfig($configs['proxy_user_agent']['conf_id'], $_POST['proxy_user_agent']);
    
    // Handle plugins
    $plugins_enabled = isset($_POST['proxy_plugins_enabled']) ? $_POST['proxy_plugins_enabled'] : [];
    $config_handler->updateConfig($configs['proxy_plugins_enabled']['conf_id'], $plugins_enabled);
    
    // Redirect to refresh page
    redirect_header('index.php?page=proxy_settings', 3, 'Settings updated successfully');
    exit();
}

// Display settings form
echo '<h3>Web Proxy Settings</h3>';

echo '<form action="index.php?page=proxy_settings" method="post">';
echo '<table class="outer" width="100%">';

// Enable proxy
echo '<tr><th colspan="2">General Settings</th></tr>';
echo '<tr class="even"><td width="30%">Enable Web Proxy</td>';
echo '<td><input type="checkbox" name="proxy_enabled" value="1" ' . ($configs['proxy_enabled'] ? 'checked' : '') . '></td></tr>';

// Allowed domains
echo '<tr class="odd"><td>Allowed Domains</td>';
echo '<td><textarea name="proxy_allowed_domains" rows="5" cols="50">' . htmlspecialchars($configs['proxy_allowed_domains']) . '</textarea>';
echo '<br><small>Enter one domain per line. Leave empty to allow all domains not in the blocked list.<br>Use .example.com to match all subdomains.</small></td></tr>';

// Blocked domains
echo '<tr class="even"><td>Blocked Domains</td>';
echo '<td><textarea name="proxy_blocked_domains" rows="5" cols="50">' . htmlspecialchars($configs['proxy_blocked_domains']) . '</textarea>';
echo '<br><small>Enter one domain per line. These domains will always be blocked.<br>Use .example.com to match all subdomains.</small></td></tr>';

// Cache settings
echo '<tr><th colspan="2">Cache Settings</th></tr>';
echo '<tr class="odd"><td>Enable Caching</td>';
echo '<td><input type="checkbox" name="proxy_cache_enabled" value="1" ' . ($configs['proxy_cache_enabled'] ? 'checked' : '') . '></td></tr>';

echo '<tr class="even"><td>Cache Time (seconds)</td>';
echo '<td><input type="text" name="proxy_cache_time" value="' . (int)$configs['proxy_cache_time'] . '"></td></tr>';

// Security settings
echo '<tr><th colspan="2">Security Settings</th></tr>';
echo '<tr class="odd"><td>Log Requests</td>';
echo '<td><input type="checkbox" name="proxy_log_requests" value="1" ' . ($configs['proxy_log_requests'] ? 'checked' : '') . '></td></tr>';

echo '<tr class="even"><td>Strip JavaScript</td>';
echo '<td><input type="checkbox" name="proxy_strip_js" value="1" ' . ($configs['proxy_strip_js'] ? 'checked' : '') . '></td></tr>';

echo '<tr class="odd"><td>Strip Cookies</td>';
echo '<td><input type="checkbox" name="proxy_strip_cookies" value="1" ' . ($configs['proxy_strip_cookies'] ? 'checked' : '') . '></td></tr>';

// Advanced settings
echo '<tr><th colspan="2">Advanced Settings</th></tr>';
echo '<tr class="even"><td>Custom User Agent</td>';
echo '<td><input type="text" name="proxy_user_agent" value="' . htmlspecialchars($configs['proxy_user_agent']) . '" size="50">';
echo '<br><small>Leave empty to use the default user agent.</small></td></tr>';

// Plugins
echo '<tr><th colspan="2">Plugins</th></tr>';
echo '<tr class="odd"><td valign="top">Enabled Plugins</td><td>';

// Get available plugins
$plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';
$available_plugins = [];

if (is_dir($plugins_dir)) {
    $dir = opendir($plugins_dir);
    while (($file = readdir($dir)) !== false) {
        if (substr($file, -4) === '.php') {
            $plugin_name = substr($file, 0, -4);
            $available_plugins[] = $plugin_name;
        }
    }
    closedir($dir);
}

if (empty($available_plugins)) {
    echo 'No plugins available.';
} else {
    foreach ($available_plugins as $plugin) {
        $checked = in_array($plugin, $configs['proxy_plugins_enabled']) ? 'checked' : '';
        echo '<input type="checkbox" name="proxy_plugins_enabled[]" value="' . $plugin . '" ' . $checked . '> ' . $plugin . '<br>';
    }
}

echo '</td></tr>';

// Submit button
echo '<tr class="foot"><td colspan="2" align="center">';
echo '<input type="submit" name="submit" value="Save Settings" class="formButton"></td></tr>';

echo '</table>';
echo '</form>';

// Include footer
xoops_cp_footer();