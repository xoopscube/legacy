<?php
/**
 * Protector Web Proxy Plugins
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// Get protector instance
$protector = protector::getInstance();

// Get module config
$module_handler = xoops_getHandler('module');
$config_handler = xoops_getHandler('config');
$module = $module_handler->getByDirname('protector');
$configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

// Plugins directory
$plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';

// Create plugins directory if it doesn't exist
if (!is_dir($plugins_dir)) {
    if (!@mkdir($plugins_dir, 0755, true)) {
        echo '<div class="errorMsg">Failed to create plugins directory at: ' . $plugins_dir . '. Please create it manually and ensure it is writable.</div>';
    } else {
        // Create an index.php file for security
        $index_content = "<?php\nheader('HTTP/1.0 403 Forbidden');\nexit('Access Denied');\n";
        if (!file_put_contents($plugins_dir . '/index.php', $index_content)) {
            echo '<div class="errorMsg">Warning: Failed to create security index.php in plugins directory</div>';
        }
    }
}

// Check if directory is writable
if (is_dir($plugins_dir) && !is_writable($plugins_dir)) {
    echo '<div class="errorMsg">Warning: Plugins directory is not writable. You will not be able to upload or delete plugins.</div>';
}

// Handle plugin upload
if (isset($_POST['upload_plugin']) && isset($_FILES['plugin_file'])) {
    $file = $_FILES['plugin_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        // Validate file
        $file_info = pathinfo($file['name']);
        if ($file_info['extension'] === 'php') {
            // Move file to plugins directory
            $target_file = $plugins_dir . '/' . $file['name'];
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                redirect_header('index.php?page=proxy_plugins', 3, 'Plugin uploaded successfully');
                exit();
            } else {
                $error_message = 'Failed to move uploaded file';
            }
        } else {
            $error_message = 'Only PHP files are allowed';
        }
    } else {
        $error_message = 'Upload error: ' . $file['error'];
    }
}

// Handle plugin deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $plugin_file = $plugins_dir . '/' . $_GET['delete'] . '.php';
    if (file_exists($plugin_file) && unlink($plugin_file)) {
        redirect_header('index.php?page=proxy_plugins', 3, 'Plugin deleted successfully');
        exit();
    } else {
        $error_message = 'Failed to delete plugin';
    }
}


// Handle plugin testing
if (isset($_GET['test']) && !empty($_GET['test'])) {
    $plugin_name = $_GET['test'];
    $plugin_file = $plugins_dir . '/' . $plugin_name . '.php';
    
    if (file_exists($plugin_file)) {
        // Try to load and test the plugin
        try {
            require_once XOOPS_TRUST_PATH . '/modules/protector/class/Proxy.class.php';
            $proxy = new ProtectorProxy();
            
            // Include the plugin file
            include_once $plugin_file;
            
            // Create class name from file name
            $class_name = 'Plugin_' . substr($plugin_name, 7); // Remove "Plugin_" prefix if it exists
            
            // Check if class exists
            if (class_exists($class_name)) {
                // Create plugin instance
                $plugin = new $class_name($proxy);
                
                // Test initialization
                $init_result = $plugin->init();
                
                if ($init_result) {
                    // Test required methods
                    $methods_exist = method_exists($plugin, 'preProcess') && 
                                    method_exists($plugin, 'postProcess') && 
                                    method_exists($plugin, 'handleDomain');
                    
                    if ($methods_exist) {
                        redirect_header('index.php?page=proxy_plugins', 3, 'Plugin tested successfully. All required methods exist.');
                    } else {
                        redirect_header('index.php?page=proxy_plugins', 3, 'Plugin test failed: Missing required methods. Please ensure the plugin implements preProcess(), postProcess(), and handleDomain().');
                    }
                } else {
                    redirect_header('index.php?page=proxy_plugins', 3, 'Plugin initialization failed.');
                }
            } else {
                redirect_header('index.php?page=proxy_plugins', 3, 'Plugin test failed: Class ' . $class_name . ' not found in file.');
            }
        } catch (Exception $e) {
            redirect_header('index.php?page=proxy_plugins', 3, 'Plugin test failed with error: ' . $e->getMessage());
        }
        exit();
    } else {
        $error_message = 'Plugin file not found';
    }
}

// Note that 'redirect_header' must be placed before any html layout element (e.g. mymnenu.php) 
// Display admin menu
include __DIR__ . '/mymenu.php';

// Display plugins page
echo '<h2>Proxy Plugins</h2>';

// Display error message if any
if (isset($error_message)) {
    echo '<div class="errorMsg">' . $error_message . '</div>';
}

// List installed plugins
echo '<div class="admin-block">';
echo '<h4>Installed Plugins</h4>';

$plugins = [];
$dir = opendir($plugins_dir);
while (($file = readdir($dir)) !== false) {
    if (substr($file, -4) === '.php') {
        $plugin_name = substr($file, 0, -4);
        
        // Skip index.php and only include files with Plugin_ prefix
        if ($plugin_name === 'index' || strpos($plugin_name, 'Plugin_') !== 0) {
            continue;
        }
        
        $plugins[] = $plugin_name;
    }
}
closedir($dir);

// Get enabled plugins from config
$enabled_plugins = [];
if (isset($configs['proxy_plugins_enabled'])) {
    $value = $configs['proxy_plugins_enabled'];
    
    // Handle different possible formats for backward compatibility
    if (is_array($value)) {
        $enabled_plugins = $value;
    } elseif (is_string($value) && !empty($value)) {
        // Check if it's a serialized array
        if (preg_match('/^a:\d+:{/', $value)) {
            $unserialized = @unserialize($value);
            if ($unserialized !== false) {
                $enabled_plugins = $unserialized;
            }
        } elseif (strpos($value, '|') !== false) {
            // If not serialized, try pipe-separated format
            $enabled_plugins = explode('|', $value);
        } else {
            // Single value
            $enabled_plugins = [$value];
        }
    }
    
    // Filter out any non-existent plugins from enabled list
    $enabled_plugins = array_filter($enabled_plugins); // Remove empty values
    $enabled_plugins = array_values($enabled_plugins); // Reindex array
}

// Debug output
// echo '<div style="display:none">Enabled plugins: ' . var_export($enabled_plugins, true) . '</div>';

if (empty($plugins)) {
    echo '<div class="confirm">No plugins installed.</div>';
} else {
    echo '<table class="outer" width="100%">';
    echo '<thead><tr><th>Plugin Name</th><th>Status</th><th>Actions</th></tr></thead>';
    
    foreach ($plugins as $plugin) {
        echo '<tbody>';
        // Check if plugin is enabled using our properly formatted array
        $enabled = in_array($plugin, $enabled_plugins);
        echo '<tr' . ($enabled ? ' class="active" style="border-left:2px solid green"' : '') . '>';
        echo '<td width="50%">' . $plugin . '</td>';
        
        echo '<td class="list_center" width="25%">' . ($enabled ? 'Enabled' : 'Disabled') . '</td>';
        
        echo '<td class="list_control width="25%">';
        echo '<a class="action-add" href="index.php?page=proxy_settings" title="Configure"><i class="i-add"></i></a>';
        echo '<a class="action-view" href="index.php?page=proxy_plugins&test=' . $plugin . '" title="Test"><i class="i-view"></i></a> ';
        echo '<a class="action-delete" href="index.php?page=proxy_plugins&delete=' . $plugin . '" onclick="return confirm(\'Are you sure you want to delete this plugin?\')" title="Delete"><i class="i-delete"></i></a>';
        echo '</td>';
        echo '</tr></tbody>';
    }
    
    echo '</table>';
}

echo '</div>';



// Upload form
echo '<div class="admin-block">';
echo '<h4>Upload New Plugin</h4>';
echo '<form action="index.php?page=proxy_plugins" method="post" enctype="multipart/form-data">';
echo '<input type="file" name="plugin_file" required>';
echo '<input type="submit" name="upload_plugin" value="Upload Plugin" class="formButton">';
echo '</form>';
echo '</div>';


// Add diagnostic tools section
echo '<div class="admin-block">';
echo '<h4>Diagnostic Tools</h4>';
echo '<p>Use these tools to test your proxy configuration and plugins:</p>';
echo '<div class="tips">';
echo '<a class="button" href="' . XOOPS_URL . '/modules/protector/test_path.php" target="_blank">Path Test Tool</a> - Check for path and URL configuration issues<br />';
echo '<a class="button" href="' . XOOPS_URL . '/modules/protector/proxy.php?url=' . urlencode('https://example.com/') . '" target="_blank">Plugin Test Tool</a> - Run the Plugin_Test to check proxy functionality';
echo '</div>';


// Plugin development guide
echo '<div class="admin-block">';
echo '<h4>Plugin Development Guide</h4>';
echo '<p>Plugins should follow this structure:</p>';
echo '<pre><code class="lang-php">';
echo '&lt;?php
if (!defined(\'XOOPS_ROOT_PATH\')) {
    exit();
}

require_once XOOPS_TRUST_PATH . \'/modules/protector/class/PluginBase.class.php\';

/**
 * Proxy plugin e.g.: Plugin_Name.php
 */
class Plugin_{Name} extends ProtectorProxyPluginBase {
    /**
     * Initialize plugin
     * 
     * @return bool Success
     */
    public function init() {
        // You could do any setup here
        return true;
    }
    
    /**
     * Pre-process URL before fetching
     * 
     * @param string $url The URL to process
     * @return string|false The processed URL or false to block
     */
    public function preProcess($url) {
        // Modify URL if needed or return false to block
        return $url;
    }
    
    /**
     * Post-process content after fetching
     * 
     * @param string $content The content to process
     * @param string $url The URL
     * @return string|false The processed content or false to block
     */
    public function postProcess($content, $url) {
        // Process and modify content here
        return $content;
    }
    
    /**
     * Handle specific domains directly
     * 
     * @param string $url The URL to handle
     * @return string|false Content or false to continue normal processing
     */
    public function handleDomain($url) {
        // Return HTML content for specific domains or false to continue
        return false;
    }
}';
echo '</pre></code>';
echo '<div class="tips">Save your plugin as Plugin_{Name}.php in the plugins/proxy directory.</div>';
echo '</div>';

// Include footer
xoops_cp_footer();