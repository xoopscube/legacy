<?php
/**
 * Web Proxy for Protector
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

// First check path !
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// Set module name directly - this is a fixed module
$mydirname = 'protector';

// Load permission check helper
require_once __DIR__ . '/include/permission_check.php';

// Check if proxy is enabled
$module_handler = xoops_getHandler('module');
$config_handler = xoops_getHandler('config');
$protectorModule = $module_handler->getByDirname($mydirname);

if (!is_object($protectorModule)) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Proxy service is not available.';
    exit();
}

// Get all module configs
$protector_configs = $config_handler->getConfigsByCat(0, $protectorModule->getVar('mid'));
$proxy_enabled = false;

// Check if proxy is enabled in module config
foreach ($protector_configs as $config) {
    if (is_object($config) && $config->getVar('conf_name') === 'proxy_enabled') {
        $proxy_enabled = (bool)$config->getVar('conf_value');
        break;
    }
}

// Force enable for debugging if needed
// Uncomment the line below to force enable the proxy for testing
// $proxy_enabled = true;

// Get user groups for permission check
global $xoopsUser;
$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : [XOOPS_GROUP_ANONYMOUS];
$gperm_handler = xoops_getHandler('groupperm');

// Direct permission check instead of using the helper function
$has_permission = $gperm_handler->checkRight('proxy_use', 0, $groups, $protectorModule->getVar('mid'));

// Admin users always have permission
$is_admin = is_object($xoopsUser) && $xoopsUser->isAdmin($protectorModule->getVar('mid'));

// Check if user has permission to use proxy
if (!$proxy_enabled && !$is_admin) {
    header('HTTP/1.0 403 Forbidden');
    echo 'Proxy service is disabled.';
    exit();
} elseif (!$has_permission && !$is_admin) {
    header('HTTP/1.0 403 Forbidden');
    echo 'You do not have permission to use this service.';
    
    // Debug information (remove in production)
    if (defined('_PROTECTOR_DEBUG') && _PROTECTOR_DEBUG) {
        echo '<br><br>Debug info:<br>';
        echo 'User ID: ' . (is_object($xoopsUser) ? $xoopsUser->getVar('uid') : 'Anonymous') . '<br>';
        echo 'Groups: ' . implode(', ', $groups) . '<br>';
        echo 'Module ID: ' . $protectorModule->getVar('mid') . '<br>';
        echo 'Proxy Enabled: ' . ($proxy_enabled ? 'Yes' : 'No') . '<br>';
        
        // Check which groups have permission
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('gperm_modid', $protectorModule->getVar('mid')));
        $criteria->add(new Criteria('gperm_name', 'proxy_use'));
        $criteria->add(new Criteria('gperm_itemid', 0));
        $perms = $gperm_handler->getObjects($criteria);
        
        echo 'Groups with permission: ';
        if (count($perms) > 0) {
            $perm_groups = [];
            foreach ($perms as $perm) {
                $perm_groups[] = $perm->getVar('gperm_groupid');
            }
            echo implode(', ', $perm_groups);
        } else {
            echo 'None';
        }
    }
    
    exit();
}

// Load Protector Proxy class
require_once XOOPS_TRUST_PATH . '/modules/protector/class/Proxy.class.php';

// Make sure we're in an object context before using the class
try {
    $proxy = new ProtectorProxy();
    
    // Get proxy configuration
    $config = $proxy->getConfig();
    
    // Override the config with our module setting
    $config['enabled'] = $proxy_enabled || $is_admin;
} catch (Exception $e) {
    // Handle any exceptions
    redirect_header(XOOPS_URL, 3, 'Proxy error: ' . $e->getMessage());
    exit();
}

// Check if proxy is enabled
if (empty($config['enabled']) && !$is_admin) {
    // Get URL parameter before checking if it's set
    $url = isset($_GET['url']) ? rawurldecode($_GET['url']) : '';
    
    // If URL was provided and user is not admin, redirect to index
    if (!empty($url) && (!is_object($xoopsUser) || !$xoopsUser->isAdmin())) {
        redirect_header(XOOPS_URL, 3, 'Proxy is disabled');
        exit();
    }
    
    // Set proxy_enabled flag for template
    $proxy_enabled = false;
    
    // Set admin notice about disabled status
    $admin_notice = 'The proxy service is currently disabled in module settings. Only administrators can see this interface.';
} else {
    $proxy_enabled = true;
    $admin_notice = '';
}

// Start session if not already started to handle recent requests
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Initialize recent_requests array
$recent_requests = [];

// Get recent requests from session if available
if (isset($_SESSION['protector_proxy_recent']) && is_array($_SESSION['protector_proxy_recent'])) {
    $recent_requests = $_SESSION['protector_proxy_recent'];
} else {
    // Check if we have cached requests
    $cache_dir = XOOPS_CACHE_PATH . '/proxy';
    $cache_file = $cache_dir . '/recent_' . md5(session_id()) . '.php';
    
    // Load cached requests if available
    if (file_exists($cache_file)) {
        include $cache_file;
        if (isset($cached_requests) && is_array($cached_requests)) {
            $_SESSION['protector_proxy_recent'] = $cached_requests;
            $recent_requests = $cached_requests;
        }
    }
}

// Get URL to proxy - use rawurldecode to handle URL encoding properly
$url = isset($_GET['url']) ? rawurldecode($_GET['url']) : '';
$iframe_content = '';
$iframe_url = '';

// If URL provided, process the proxy request
if (!empty($url)) {
    // Validate URL
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        redirect_header(XOOPS_MODULE_URL . '/protector/proxy.php', 3, 'Invalid URL');
        exit();
    }
    
    // Check cache for this URL before making a request
    $url_cache_file = XOOPS_CACHE_PATH . '/proxy/' . md5($url) . '.cache';
    $content = false;
    
    if (file_exists($url_cache_file) && (time() - filemtime($url_cache_file) < 3600)) { // 1 hour cache
        $content = file_get_contents($url_cache_file);
    }
    
    // If not in cache, process the request
    if ($content === false) {
        $content = $proxy->processRequest($url);
        
        if ($content === false) {
            redirect_header(XOOPS_MODULE_URL . '/protector/proxy.php', 3, 'Failed to proxy URL');
            exit();
        }
        
        // Save to cache
        $cache_dir = XOOPS_CACHE_PATH . '/proxy';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        file_put_contents($url_cache_file, $content);
    }
    
    // Store this URL in recent requests (if it's not an image or other media)
    if (strpos($url, '://') !== false && !preg_match('/\.(jpg|jpeg|png|gif|bmp|css|js)$/i', $url)) {
        // Extract title if possible
        $title = '';
        if (preg_match('/<title>(.*?)<\/title>/is', $content, $matches)) {
            $title = htmlspecialchars($matches[1], ENT_QUOTES);
        }
        
        // Add current URL to the beginning of the array
        array_unshift($recent_requests, [
            'url' => htmlspecialchars($url, ENT_QUOTES),
            'time' => date('Y-m-d H:i:s', time()),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'status' => 'Success',
            'title' => $title
        ]);
        
        // Keep only the 10 most recent requests
        $recent_requests = array_slice($recent_requests, 0, 10);
        $_SESSION['protector_proxy_recent'] = $recent_requests;
        
        // Save to cache
        $cache_dir = XOOPS_CACHE_PATH . '/proxy';
        $cache_file = $cache_dir . '/recent_' . md5(session_id()) . '.php';
        
        // Create cache directory if it doesn't exist
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        // Write to cache file
        $cache_content = "<?php\n";
        $cache_content .= "if (!defined('XOOPS_ROOT_PATH')) exit();\n";
        $cache_content .= '$cached_requests = ' . var_export($recent_requests, true) . ";\n";
        file_put_contents($cache_file, $cache_content);
    }
    
    // Determine content type from URL
    $contentType = 'text/html';
    if (preg_match('/\.(jpg|jpeg|png|gif|bmp)$/i', $url)) {
        $contentType = 'image/' . strtolower(pathinfo($url, PATHINFO_EXTENSION));
    } elseif (preg_match('/\.css$/i', $url)) {
        $contentType = 'text/css';
    } elseif (preg_match('/\.js$/i', $url)) {
        $contentType = 'application/javascript';
    } elseif (preg_match('/\.json$/i', $url)) {
        $contentType = 'application/json';
    } elseif (preg_match('/\.(mp4|webm|ogg)$/i', $url)) {
        $contentType = 'video/' . strtolower(pathinfo($url, PATHINFO_EXTENSION));
    }
    
    // TODO
    // Replace the iframe handling section with direct content display
    if ($contentType === 'text/html') {
        // Fix relative URLs in the content
        $base_url = parse_url($url);
        $base_domain = $base_url['scheme'] . '://' . $base_url['host'] . (isset($base_url['port']) ? ':' . $base_url['port'] : '');
        $base_path = isset($base_url['path']) ? dirname($base_url['path']) : '/';
        
        // Add base tag to handle relative URLs
        $content = str_replace('<head>', '<head><base href="' . $base_domain . $base_path . '/">', $content);
        
        // Add proxy footer to the content with more detailed information
        $proxy_footer = '<div style="position: fixed; bottom: 0; left: 0; width: 100%; background-color: #000; color: #fff; padding: 10px; text-align: center; z-index: 9999;">
            Proxied content from: ' . htmlspecialchars($url) . ' | 
            Your IP: ' . htmlspecialchars($_SERVER['REMOTE_ADDR']) . ' | 
            Proxy Server: ' . htmlspecialchars($_SERVER['SERVER_NAME']) . ' | 
            Time: ' . date('Y-m-d H:i:s') . ' | 
            <a href="' . XOOPS_MODULE_URL . '/protector/proxy.php" style="color: #fff;">Return to Proxy</a>
        </div>';
        
        // Add the footer before the closing body tag
        $content = str_replace('</body>', $proxy_footer . '</body>', $content);
        
        // Output the content directly
        echo $content;
        exit();
    } else {
        // For non-HTML content, output directly
        header('Content-Type: ' . $contentType);
        echo $content;
        exit();
    }
}

// Show proxy form or iframe with proxied content
// Remove this line as it's duplicated below
// $xoopsOption['template_main'] = 'protector_proxy.html';
$xoopsOption['xoops_pagetitle'] = 'Web Proxy';
// Set template for this page - use just 'proxy.html' since the system will add the module prefix
$xoopsOption['template_main'] = $mydirname . '_proxy.html';


// Include header
require_once XOOPS_ROOT_PATH . '/header.php';



// After all the processing, we need to display the template
// Prepare data for template
$xoopsTpl->assign('form_action', XOOPS_MODULE_URL . '/protector/proxy.php');
$xoopsTpl->assign('lang_enter_url', 'Enter URL to access through proxy:');
$xoopsTpl->assign('lang_access', 'Access via Proxy');
$xoopsTpl->assign('recent_requests', $recent_requests);
$xoopsTpl->assign('iframe_url', $iframe_url);
$xoopsTpl->assign('proxy_enabled', $proxy_enabled);
$xoopsTpl->assign('admin_notice', $admin_notice);

// Get module object for admin check
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname($mydirname);

// Check if user is admin and assign to template
$is_admin = (is_object($xoopsUser) && is_object($module) && $xoopsUser->isAdmin($module->mid()));
$xoopsTpl->assign('is_admin', $is_admin);

// Get user permissions for proxy access
$member_handler = xoops_getHandler('member');
$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : [XOOPS_GROUP_ANONYMOUS];
$gperm_handler = xoops_getHandler('groupperm');
$can_use_proxy = $gperm_handler->checkRight('proxy_use', 0, $groups, $module->mid());
$xoopsTpl->assign('can_use_proxy', $can_use_proxy);



// Display the template
// For D3 modules, we need to use display() with db: prefix to check database first
// Use the full template name with double prefix since that's how it's being compiled
$xoopsTpl->display('db:' . $mydirname . '_proxy.html');

// Include footer
include XOOPS_ROOT_PATH . '/footer.php';