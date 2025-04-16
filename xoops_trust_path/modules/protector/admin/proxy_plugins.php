<?php
/**
 * Protector Web Proxy Plugins
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

// Plugins directory
$plugins_dir = XOOPS_TRUST_PATH . '/modules/protector/plugins/proxy';

// Create plugins directory if it doesn't exist
if (!is_dir($plugins_dir)) {
    mkdir($plugins_dir, 0755, true);
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

// Display plugins page
echo '<h3>Web Proxy Plugins</h3>';

// Display error message if any
if (isset($error_message)) {
    echo '<div class="errorMsg">' . $error_message . '</div>';
}

// Upload form
echo '<div class="admin-block">';
echo '<h4>Upload New Plugin</h4>';
echo '<form action="index.php?page=proxy_plugins" method="post" enctype="multipart/form-data">';
echo '<input type="file" name="plugin_file" required>';
echo '<input type="submit" name="upload_plugin" value="Upload Plugin" class="formButton">';
echo '</form>';
echo '</div>';

// List installed plugins
echo '<div class="admin-block">';
echo '<h4>Installed Plugins</h4>';

$plugins = [];
$dir = opendir($plugins_dir);
while (($file = readdir($dir)) !== false) {
    if (substr($file, -4) === '.php') {
        $plugin_name = substr($file, 0, -4);
        $plugins[] = $plugin_name;
    }
}
closedir($dir);

if (empty($plugins)) {
    echo '<p>No plugins installed.</p>';
} else {
    echo '<table class="outer" width="100%">';
    echo '<tr><th>Plugin Name</th><th>Status</th><th>Actions</th></tr>';
    
    $class = 'even';
    foreach ($plugins as $plugin) {
        $class = ($class == 'even') ? 'odd' : 'even';
        
        echo '<tr class="' . $class . '">';
        echo '<td>' . $plugin . '</td>';
        
        // Check if plugin is enabled
        $enabled = in_array($plugin, $configs['proxy_plugins_enabled'] ?? []);
        echo '<td>' . ($enabled ? 'Enabled' : 'Disabled') . '</td>';
        
        echo '<td>';
        echo '<a href="index.php?page=proxy_settings">Configure</a> | ';
        echo '<a href="index.php?page=proxy_plugins&delete=' . $plugin . '" onclick="return confirm(\'Are you sure you want to delete this plugin?\')">Delete</a>';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
}

echo '</div>';

// Plugin development guide
echo '<div class="admin-block">';
echo '<h4>Plugin Development Guide</h4>';
echo '<p>Plugins should follow this structure:</p>';
echo '<pre>';
echo 'class ProtectorProxy{PluginName}Plugin {
    private $proxy;
    
    public function __construct($proxy) {
        $this->proxy = $proxy;
    }
    
    public function processContent($url, $content) {
        // Process and modify content here
        return $content;
    }
}';
echo '</pre>';
echo '<p>Save your plugin as {plugin_name}.php in the plugins/proxy directory.</p>';
echo '</div>';

// Include footer
xoops_cp_footer();