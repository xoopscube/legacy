<?php
/**
 * Protector Web Proxy Settings
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Get module config
$module_handler = xoops_getHandler('module');
$config_handler = xoops_getHandler('config');
$module = $module_handler->getByDirname('protector');

// Include header
xoops_cp_header();

// Handle form submission
if (isset($_POST['submit'])) {
    // Get all module configs
    $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
    $criteria->add(new Criteria('conf_name', 'proxy_%', 'LIKE'));
    $config_objects = $config_handler->getConfigs($criteria);

    // Create a map of config names to objects
    $config_map = [];
    foreach ($config_objects as $config) {
        $config_map[$config->getVar('conf_name')] = $config;
    }

    // Update each proxy-related config
    foreach ($config_map as $name => $config_obj) {
        switch ($name) {
            case 'proxy_enabled':
            case 'proxy_cache_enabled':
            case 'proxy_log_requests':
            case 'proxy_strip_js':
            case 'proxy_strip_cookies':
                $value = isset($_POST[$name]) ? 1 : 0;
                break;
            case 'proxy_cache_time':
                $value = (int)$_POST[$name];
                break;
            case 'proxy_allowed_domains':
            case 'proxy_blocked_domains':
                // Properly sanitize and handle textarea input
                $value = isset($_POST[$name]) ? trim($_POST[$name]) : '';
                break;
            case 'proxy_plugins_enabled':
                // Handle multi-select values - serialize the array before saving
                if (isset($_POST[$name]) && is_array($_POST[$name])) {
                    $value = serialize($_POST[$name]);
                } else {
                    $value = serialize([]);
                }
                break;
            case 'proxy_user_agent':
                $value = isset($_POST[$name]) ? trim($_POST[$name]) : '';
                break;
            default:
                $value = isset($_POST[$name]) ? $_POST[$name] : '';
        }

        $config_obj->setVar('conf_value', $value);
        if (!$config_handler->insertConfig($config_obj)) {
            error_log("Failed to save config: " . $name);
        }
    }

    // Redirect to refresh page - using direct URL instead of module dirname
    redirect_header('index.php?page=proxy_settings', 3, 'Settings updated successfully');
    exit();
}

// Display admin menu
include __DIR__ . '/mymenu.php';

// Include form loader
include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

// Get text sanitizer instance
(method_exists('MyTextSanitizer', 'sGetInstance') and $myts =& MyTextSanitizer::sGetInstance()) || $myts =& MyTextSanitizer::getInstance();

// Get protector instance
$protector = protector::getInstance();

// Get configs for display
$criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
$criteria->add(new Criteria('conf_name', 'proxy_%', 'LIKE'));
$config_objects = $config_handler->getConfigs($criteria);


// Create a map of config names to values for easier access in the form
$configs = [];
foreach ($config_objects as $config) {
    $name = $config->getVar('conf_name');
    $value = $config->getVar('conf_value');

    // Handle array values for display
    if ($name === 'proxy_plugins_enabled') {
        // For proxy_plugins_enabled, use a direct approach
        // Get the raw value first for debugging
        $raw_value = $config->getVar('conf_value', 'n');
        
        // Try to unserialize if it's a serialized array
        if (is_string($raw_value) && !empty($raw_value) && preg_match('/^a:\d+:{/', $raw_value)) {
            // Force unserialize approach
            $unserialized = @unserialize($raw_value);
            if ($unserialized !== false && is_array($unserialized)) {
                $value = $unserialized;
            } else {
                // If unserialize fails, try to extract plugin names directly using regex
                preg_match_all('/s:\d+:"([^"]+)";/', $raw_value, $matches);
                if (!empty($matches[1])) {
                    $value = $matches[1];
                } else {
                    $value = [];
                }
            }
        } else if (!is_array($value)) {
            // If not serialized and not an array, treat as single value or empty
            $value = !empty($value) ? [$value] : [];
        }
    }

    // ensure Textareas get the raw value without any processing
    if (in_array($name, ['proxy_allowed_domains', 'proxy_blocked_domains'])) {
        $value = $config->getVar('conf_value', 'n'); // 'n' for no formatting
    }

    $configs[$name] = $value;
}

// Display settings form
echo '<h3>Web Proxy Settings</h3>';

echo '<form action="index.php?page=proxy_settings" method="post">';
echo '<table class="outer" width="100%">';

// Enable proxy
echo '<thead><tr><th colspan="2">General Settings</th></tr></thead>';
echo '<tbody><tr class="even"><td width="30%">Enable Web Proxy</td>';
echo '<td><input type="checkbox" name="proxy_enabled" value="1" ' . ($configs['proxy_enabled'] ? 'checked' : '') . '></td></tr>';

// Allowed domains
echo '<tr class="odd"><td>Allowed Domains</td>';
echo '<td><textarea name="proxy_allowed_domains" rows="5" cols="50">' . $myts->htmlspecialchars($configs['proxy_allowed_domains'] ?? '') . '</textarea>';
echo '<br><small>Enter one domain per line. Leave empty to allow all domains not in the blocked list.<br>Use .example.com to match all subdomains.</small></td></tr>';

// Blocked domains
echo '<tr class="even"><td>Blocked Domains</td>';
echo '<td><textarea name="proxy_blocked_domains" rows="5" cols="50">' . $myts->htmlspecialchars($configs['proxy_blocked_domains'] ?? '') . '</textarea>';
echo '<br><small>Enter one domain per line. These domains will always be blocked.<br>Use .example.com to match all subdomains.</small></td></tr>';

// Cache settings
echo '<tr><th colspan="2">Cache Settings</th></tr>';
echo '<tr class="odd"><td>Enable Caching</td>';
echo '<td><input type="checkbox" name="proxy_cache_enabled" value="1" ' . ($configs['proxy_cache_enabled'] ? 'checked' : '') . '></td></tr>';

echo '<tr class="even"><td>Cache Time (seconds)</td>';
echo '<td><input type="number" name="proxy_cache_time" value="' . (int)$configs['proxy_cache_time'] . '" min="60" step="60" style="min-width:9ch"></td></tr>';

// Security settings
echo '<tr><th colspan="2">Security Settings</th></tr>';
echo '<tr class="odd"><td>Log Requests</td>';
echo '<td><input type="checkbox" name="proxy_log_requests" value="1" ' . ($configs['proxy_log_requests'] ? 'checked' : '') . '></td></tr>';

echo '<tr class="even"><td>Strip JavaScript</td>';
echo '<td><input type="checkbox" name="proxy_strip_js" value="1" ' . ($configs['proxy_strip_js'] ? 'checked' : '') . '></td></tr>';

echo '<tr class="odd"><td>Strip Cookies</td>';
echo '<td><input type="checkbox" name="proxy_strip_cookies" value="1" ' . ($configs['proxy_strip_cookies'] ? 'checked' : '') . '></td></tr>';

// Advanced settings
// Helper to select 'proxy_user_agent'
echo '<tr><th colspan="2">Advanced Settings</th></tr>';
echo '<tr class="even"><td>Select a proxy user agent</td>';
echo '<td>
<select title="select User Agent" id="user-agent-presets" onchange="update_proxy_user_agent(this)">
<option value="Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; .NET CLR 3.0.04506.648; .NET CLR 3.5.21022; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C; .NET4.0E)">XP with IE 8</option>
<option value="Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; Zune 4.0; InfoPath.3; MS-RTC LM 8; .NET4.0C; .NET4.0E)">Windows 7 with IE 9</option>
<option value="Opera/9.80 (Windows NT 5.1; U; en) Presto/2.9.168 Version/11.52">XP with Opera Browser</option>
<option value="Opera/9.80 (Windows NT 6.1; U; en) Presto/2.9.168 Version/11.52">Windows 7 with Opera Browser</option>
<option value="Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/533.20.25 (KHTML, like Gecko) Version/5.0.4 Safari/533.20.27">Windows 7 with Safari</option>
<option value="Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.36 Safari/535.7">Windows 7 with Chrome</option>
<option value="Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:8.0) Gecko/20100101 Firefox/8.0">XP with Firefox 8</option>
<option value="Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:8.0) Gecko/20100101 Firefox/8.0">Windows 7 with Firefox 8</option>
<option value="Mozilla/5.0 (X11; Linux i686; rv:8.0) Gecko/20100101 Firefox/8.0">Linux X11 with Firefox 8</option>
<option value="Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_8; en-US) AppleWebKit/533.21.1 (KHTML, like Gecko) Version/5.0.5 Safari/533.21.1">Mac OS X 10.6 with Safari</option>
<option value="Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.6.8; en-US; rv:8.0) Gecko/20100101 Firefox/8.0">Mac OS X 10.6 with Firefox 8</option>
<option value="Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.9.168 Version/11.52">Mac OS X 10.6 with Opera Browser</option>
<option value="Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.10">iPad</option>
<option value="Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.20 (KHTML, like Gecko) Mobile/7B298g">iPhone</option>
<option value="Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0)">Windows Phone OS 7.5 and IE 9</option>
<option value="Mozilla/5.0 (Linux; U; Android 2.3.5; en-us; HTC Vision Build/GRI40) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1">Android 2.3.5</option>
<option value="Mozilla/5.0 (BlackBerry; U; BlackBerry 9850; en-US) AppleWebKit/534.11+ (KHTML, like Gecko) Version/7.0.0.115 Mobile Safari/534.11+">Blackberry</option>
<option value="Opera/9.80 (J2ME/MIDP; Opera Mini/9.80 (S60; SymbOS; Opera Mobi/23.348; U; en) Presto/2.5.25 Version/10.54">Symbian with Opera Mini</option>
<option value="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36"> - Current/Real</option>
<option value=""> - None</option>
<option value="custom">Custom...</option>
</select>
</td></tr>';
echo '<tr class="even"><td>Custom User Agent</td>';
echo '<td><input type="text" name="proxy_user_agent" id="proxy_user_agent" value="' . htmlspecialchars($configs['proxy_user_agent']) . '" size="50">';
echo '<br><small>Leave empty to use the default user agent.</small></td></tr>';

// Plugins
echo '<tr><th colspan="2">Plugins</th></tr>';
echo '<tr class="odd"><td valign="top">Enabled Plugins</td><td>';

// Get available plugins
$plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';
$available_plugins = [];

// Simple check if plugins directory exists - no need to create it here
// onInstall and onUpdate should have handled this
if (!is_dir($plugins_dir) || !is_readable($plugins_dir)) {
    echo '<div class="errorMsg">Plugins directory not found or not readable at: ' . $plugins_dir . '</div>';
} else {
    // Scan directory for plugins
    $dir_contents = scandir($plugins_dir);
    if ($dir_contents !== false) {
        foreach ($dir_contents as $file) {
            if (substr($file, -4) === '.php' && $file !== 'index.php') {
                $plugin_name = substr($file, 0, -4);
                $available_plugins[$plugin_name] = $plugin_name;
            }
        }
    }
}

// Get enabled plugins from config (always as array)
$enabled_plugins = $configs['proxy_plugins_enabled'] ?? [];
if (!is_array($enabled_plugins)) {
    $enabled_plugins = [];
}

// Create select element for plugins
if (empty($available_plugins)) {
    echo 'No plugins available.';
} else {
    echo '<select name="proxy_plugins_enabled[]" id="proxy_plugins_enabled" multiple="multiple" size="5">';
    foreach ($available_plugins as $key => $label) {
        $selected = in_array($key, $enabled_plugins) ? ' selected="selected"' : '';
        echo '<option value="' . htmlspecialchars($key) . '"' . $selected . '>' . htmlspecialchars($label) . '</option>';
    }
    echo '</select>';
    echo '<br><small>Hold Ctrl (Windows) or Command (Mac) to select multiple plugins.</small>';
}

echo '</td></tr></tbody>';

// Submit button
echo '<tfoot><tr class="foot"><td colspan="2" align="center">';
echo '<input type="submit" name="submit" value="Save Settings" class="formButton"></td></tr></tfoot>';
echo '</table>';
echo '</form>';

// Add JavaScript for user agent dropdown
echo '<script type="text/javascript">
function update_proxy_user_agent(selectElement) {
    var userAgentInput = document.getElementById("proxy_user_agent");
    if (selectElement.value === "custom") {
        // If "Custom" is selected, focus on the input field but don\'t change its value
        userAgentInput.focus();
    } else {
        // Otherwise, set the input field value to the selected option value
        userAgentInput.value = selectElement.value;
    }
}

// Set the dropdown to match the current user agent value on page load
document.addEventListener("DOMContentLoaded", function() {
    var userAgentInput = document.getElementById("proxy_user_agent");
    var userAgentSelect = document.getElementById("user-agent-presets");
    var currentValue = userAgentInput.value;
    var found = false;
    
    // Check if the current value matches any option
    for (var i = 0; i < userAgentSelect.options.length; i++) {
        if (userAgentSelect.options[i].value === currentValue) {
            userAgentSelect.selectedIndex = i;
            found = true;
            break;
        }
    }
    
    // If not found and not empty, select "Custom"
    if (!found && currentValue !== "") {
        for (var i = 0; i < userAgentSelect.options.length; i++) {
            if (userAgentSelect.options[i].value === "custom") {
                userAgentSelect.selectedIndex = i;
                break;
            }
        }
    }
});
</script>';

// Include footer
xoops_cp_footer();
