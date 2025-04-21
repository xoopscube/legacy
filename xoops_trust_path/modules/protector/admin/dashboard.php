<?php

/**
 * Protector Admin Dashboard
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// Display admin menu
include __DIR__ . '/mymenu.php';

// Test notification trigger
if (isset($_GET['test_notification']) && $_GET['test_notification'] == 1) {
  // Get module info
  $module_handler = xoops_gethandler('module');
  $protector_module = $module_handler->getByDirname('protector');

  if (!is_object($protector_module)) {
    echo '<div class="errorMsg">Error: Could not find Protector module.</div>';
  } else {
    $module_id = $protector_module->getVar('mid');

    // Prepare notification tags
    $tags = [
      'MODULE_NAME' => $protector_module->getVar('name'),
      'SITE_NAME' => $GLOBALS['xoopsConfig']['sitename'],
      'SITE_URL' => XOOPS_URL,
      'ADMIN_URL' => XOOPS_URL . '/modules/protector/admin/index.php',
      'THREAT_TYPE' => 'Test Notification',
      'THREAT_IP' => $_SERVER['REMOTE_ADDR'],
      'THREAT_DATE' => date('Y-m-d H:i:s'),
      'THREAT_DESC' => 'This is a test notification triggered from the dashboard'
    ];

    // Get database connection
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    // Get notification handler
    $notification_handler = xoops_gethandler('notification');

    // Try to directly insert a notification message for all subscribed users
    try {
      // First, get all users subscribed to this event
      $sql = "SELECT not_uid FROM " . $db->prefix('xoopsnotifications') . " 
                    WHERE not_modid = " . $module_id . " 
                    AND not_category = 'global' 
                    AND not_event = 'security_threat'";
      $result = $db->query($sql);

      if (!$result) {
        throw new Exception("Database error: " . $db->error());
      }

      $subscribers = [];
      while ($row = $db->fetchArray($result)) {
        $subscribers[] = $row['not_uid'];
      }

      if (empty($subscribers)) {
        echo '<div class="errorMsg">No users are subscribed to security_threat notifications. Please subscribe users first.</div>';
      } else {
        // Now insert a notification message for each subscriber
        $success_count = 0;
        $error_count = 0;

        // Get current timestamp
        $now = time();

        // Create a notification message
        $subject = "[{$GLOBALS['xoopsConfig']['sitename']}] Test Security Alert";
        $message = "This is a test security notification from the Protector module.\n\n";
        $message .= "Threat Type: Test Notification\n";
        $message .= "IP Address: {$_SERVER['REMOTE_ADDR']}\n";
        $message .= "Date/Time: " . date('Y-m-d H:i:s') . "\n";
        $message .= "Description: This is a test notification triggered from the dashboard\n\n";
        $message .= "You can manage your notifications at: " . XOOPS_URL . "/notifications.php";

        // Try both methods - standard notification trigger and direct message insertion

        // Method 1: Standard notification trigger
        $trigger_result = $notification_handler->triggerEvent('global', 0, 'security_threat', $tags);

        // Method 2: Direct message insertion
        foreach ($subscribers as $uid) {
          // Check if the message_users table has an entry for this user
          $check_sql = "SELECT * FROM " . $db->prefix('message_users') . " WHERE uid = " . $uid;
          $check_result = $db->query($check_sql);

          if (!$check_result || $db->getRowsNum($check_result) == 0) {
            // Create a user entry if it doesn't exist - using correct columns from schema
            $create_user_sql = "INSERT INTO " . $db->prefix('message_users') . " 
                                          (uid, usepm, tomail, viewmsm, pagenum, blacklist) 
                                          VALUES 
                                          (" . $uid . ", 1, 0, 1, 10, '')";
            $db->queryF($create_user_sql);
          }

          // Insert into inbox with the correct column name (utime instead of udate)
          $sql = "INSERT INTO " . $db->prefix('message_inbox') . " 
                           (uid, from_uid, title, message, utime, is_read, uname) 
                           VALUES 
                           (" . $uid . ", 1, '" . addslashes($subject) . "', '" . addslashes($message) . "', " . $now . ", 0, 'System')";

          if ($db->queryF($sql)) {
            $success_count++;
          } else {
            $error_count++;
            echo '<div class="errorMsg">Failed to insert message: ' . $db->error() . '</div>';
            echo '<div class="errorMsg">SQL: ' . $sql . '</div>';
          }
        }

        // Display results
        echo '<div class="successMsg">Test notification processed.</div>';
        echo '<div class="successMsg">Standard notification trigger result: ' . ($trigger_result ? 'Success' : 'Failed') . '</div>';
        echo '<div class="successMsg">Direct message insertion: ' . $success_count . ' successful, ' . $error_count . ' failed</div>';
        echo '<div class="successMsg">Total subscribers: ' . count($subscribers) . '</div>';
        echo '<div class="successMsg">Check your notification inbox at: <a href="' . XOOPS_URL . '/notifications.php" target="_blank">Notifications</a></div>';
      }
    } catch (Exception $e) {
      echo '<div class="errorMsg">Error: ' . $e->getMessage() . '</div>';
    }
  }
}

// Get protector instance
$protector = protector::getInstance();
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname('protector');
$config_handler = xoops_getHandler('config');
$configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));
$modversion = sprintf('<span class="badge-count" style="font-size:14px;position:relative;bottom:.5em">v %2.2f </span>', $module->getVar('version') / 100.0);
// Get notification options - with proper error checking
$notification_handler = xoops_gethandler('notification');
$module_handler = xoops_getHandler('module');
$protector_module = $module_handler->getByDirname('protector');

// Check the global_disabled config instead of isactive
$is_protection_active = empty($configs['global_disabled']);

// Check if CSP is enabled
$csp_enabled = $configs['enable_csp'] ?? 0;

// Display Proxy section if enabled
$proxy_enabled = $configs['proxy_enabled'] ?? 0;


// Display dashboard content
// Use module name and version
echo '<h2>' . _MI_PROTECTOR_NAME . '<span class="badge-count" style="font-size:16px;position:relative;bottom:.5em">' . $modversion . '</span></h2>';
echo '<p>' . _MI_PROTECTOR_DESC . '</p>';

// Display module information
echo '<div class="' . ($is_protection_active ? 'success' : 'danger') . '">';
echo 'Protection Status: <strong>' . ($is_protection_active ? 'Active' : 'Temporarily Disabled') . '</strong>';
echo '</div>';

// Display CSP section if enabled
echo '<div class="' . ($csp_enabled ? 'success' : 'danger') . '">';
echo 'CSP Status: <strong>' . ($csp_enabled ? 'Content Security Policy is active' : _AM_PROTECTOR_CSP_DISABLED) . '</strong>';
echo '</div>';

// Display Proxy section if enabled
if ($proxy_enabled) {
  echo '<hr /><h2>Web Proxy Protection</h2>';

  // Display proxy information
  echo '<div class="protector-dashboard">';

  // Get proxy statistics
  $proxy_stats = [
    'total_requests' => 0,
    'blocked_requests' => 0,
    'cached_resources' => 0
  ];

  // Use the proper cache path as defined in settings
  $stats_file = XOOPS_CACHE_PATH . '/protector/proxy_stats.php';
  
  // Create directory if it doesn't exist
  if (!is_dir(dirname($stats_file))) {
    mkdir(dirname($stats_file), 0777, true);
  }
  
  // Try to load stats from the cache location
  if (file_exists($stats_file)) {
    include $stats_file;
  } 
  // If no stats file exists, try to get stats from database
  else {
    // Get database connection
    $db = XoopsDatabaseFactory::getDatabaseConnection();
    
    // Count total requests from logs
    $sql = "SELECT COUNT(*) AS total FROM " . $db->prefix($protector->mydirname . '_log') . " 
            WHERE type LIKE 'PROXY-%'";
    $result = $db->query($sql);
    if ($result && $row = $db->fetchArray($result)) {
      $proxy_stats['total_requests'] = (int)$row['total'];
    }
    
    // Count blocked requests
    $sql = "SELECT COUNT(*) AS blocked FROM " . $db->prefix($protector->mydirname . '_log') . " 
            WHERE type = 'PROXY-BLOCKED'";
    $result = $db->query($sql);
    if ($result && $row = $db->fetchArray($result)) {
      $proxy_stats['blocked_requests'] = (int)$row['blocked'];
    }
    
    // Count cached resources by checking cache directory
    $cache_dir = XOOPS_CACHE_PATH . '/proxy';
    if (is_dir($cache_dir)) {
      $cached_files = glob($cache_dir . '/*.cache');
      $proxy_stats['cached_resources'] = count($cached_files ?: []);
    }
    
    // Save the stats for future use
    $stats_content = "<?php\n";
    $stats_content .= "// Proxy statistics - auto-generated\n";
    $stats_content .= "\$proxy_stats['total_requests'] = " . $proxy_stats['total_requests'] . ";\n";
    $stats_content .= "\$proxy_stats['blocked_requests'] = " . $proxy_stats['blocked_requests'] . ";\n";
    $stats_content .= "\$proxy_stats['cached_resources'] = " . $proxy_stats['cached_resources'] . ";\n";
    
    file_put_contents($stats_file, $stats_content);
  }

  echo '<div class="ui-card-overview">';

  echo '<div class="ui-card-small">
    <div class="ui-card-small-icon ui-icon-blue">
    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" role="img">
    <path d="M16 17v2H2v-2s0-4 7-4s7 4 7 4m-3.5-9.5A3.5 3.5 0 1 0 9 11a3.5 3.5 0 0 0 3.5-3.5m3.44 5.5A5.32 5.32 0 0 1 18 17v2h4v-2s0-3.63-6.06-4M15 4a3.39 3.39 0 0 0-1.93.59a5 5 0 0 1 0 5.82A3.39 3.39 0 0 0 15 11a3.5 3.5 0 0 0 0-7z" fill="currentColor">
    </path></svg>
    </div>
    <div class="ui-card-small-info">
      <h4 class="ui-card-small-title">Total Requests: <strong>' . $proxy_stats['total_requests'] . '</strong></h4>
    </div>
  </div>';

  echo '<div class="ui-card-small">
    <div class="ui-card-small-icon ui-icon-green">
    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
    <path d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4 4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4z" fill="currentColor">
    </path></svg>
    </div>
    <div class="ui-card-small-info">
      <h4 class="ui-card-small-title">Cached Resources: <strong>' . $proxy_stats['cached_resources'] . '</strong></h4>
    </div>
  </div>';

  echo '<div class="ui-card-small">
    <div class="ui-card-small-icon ui-icon-red">
    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" role="img">
    <path d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 2a2 2 0 0 0-2 2a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2m0 7c2.67 0 8 1.33 8 4v3H4v-3c0-2.67 5.33-4 8-4m0 1.9c-2.97 0-6.1 1.46-6.1 2.1v1.1h12.2V17c0-.64-3.13-2.1-6.1-2.1z" fill="currentColor">
    </path></svg>
    </div>
    <div class="ui-card-small-info">
      <h4 class="ui-card-small-title">Blocked Malicious: <strong>' . $proxy_stats['blocked_requests'] . '</strong></h4>
    </div>
  </div>';

  echo '</div>'; // ui-card-overview


  // Display quick links
  echo '<h4>Proxy Management</h4>';
  echo '<section data-layout="row center-justify" class="action-control">
	<div>
  <a href="index.php?page=proxy_settings" class="button"><i class="i-edit"></i>Proxy Settings</a>
  <a href="index.php?page=proxy_logs" class="button"><i class="i-lock"></i>Proxy Logs</a>
  <a href="index.php?page=proxy_plugins" class="button"><i class="i-add"></i>Proxy Plugins</a>
  <a href="' . XOOPS_URL . '/modules/protector/proxy.php" target="_blank" class="button"><i class="i-view"></i>Access Proxy Interface</a>
	</div>
  <div class="control-view">
      <button class="help-admin button-icon" type="button" data-id="4" data-module="protector" data-help-article="#help-proxy" title="Help"><b>?</b></button>
  </div></section>';

  echo '<div class="tips">The Web Proxy feature provides secure access to external resources while protecting your site from malicious content.</div>';

  echo '</div>'; // protector-dashboard

} else {
  echo '<hr /><h2>Web Proxy Disable</h2>';
  echo '<div class="confirm">The Proxy is disabled. Enable in ⭢ <a href=index.php?page=proxy_settings">Proxy Settings</a></div>';
}

// Display Threat Intelligence section if enabled
// Check if HTTP:BL is enabled
$httpbl_enabled = $configs['httpbl_enabled'] ?? 0;

if ($httpbl_enabled) {
  echo '<hr /><h2>' . _MI_PROTECTOR_THREAT_INTELLIGENCE_DASHBOARD . '</h2>';

  // Load ThreatIntelligence class
  require_once XOOPS_TRUST_PATH . '/modules/protector/class/ThreatIntelligence.class.php';
  $ti = new ProtectorThreatIntelligence();

  // Get database connection
  $db = XoopsDatabaseFactory::getDatabaseConnection();

  // Get recent threat intelligence logs
  $result = $db->query('SELECT l.*
                         FROM ' . $db->prefix($protector->mydirname . '_log') . ' l
                         WHERE l.type = "THREAT-INTELLIGENCE"
                         ORDER BY timestamp DESC LIMIT 5');

  echo '<table class="outer" width="100%">';
  echo '<thead><tr><th>' . _AM_TH_DATETIME . '</th><th>' . _AM_TH_IP . '</th><th>' . _AM_TH_AGENT . '</th><th>' . _AM_TH_DESC . '</th></tr></thead>';

  $count = 0;
  while ($row = $db->fetchArray($result)) {
    echo '<tbody><tr class="' . ($count % 2 ? 'even' : 'odd') . '">';
    echo '<td>' . date('Y-m-d H:i:s', $row['timestamp']) . '</td>';
    echo '<td>' . htmlspecialchars($row['ip']) . '</td>';
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
} else {
  echo '<hr /><h2>' . _MI_PROTECTOR_THREAT_INTELLIGENCE_DASHBOARD . '</h2>';
  echo '<div class="confirm">Threat Intelligence is disabled. To monitor and block malicious visitors, enable in ⭢ <a href="index.php?page=threat_intelligence">TI Settings</a></div>';
}

// Add notification subscription section
// Simplified notification approach that doesn't rely on complex objects
echo '<hr /><h2>' . _AM_PROTECTOR_NOTIFICATIONS . '</h2>';

// Notifications options and tests
echo '
<style>
#tabs {
    border: var(--border);
    display:block;
    height: 460px;
    overflow-y: auto;
}
#tabs > ul {
    box-shadow: var(--shadow-4);
    display: block;
    margin:0;
    padding:0;
    max-width: 100%;
    position: sticky;
    top: 0;
    z-index: 2;
}
#tabs > ul li {
    border:1px solid transparent;
}
</style>
<script>
$( function() {
  $( "#tabs" ).tabs();
} );
</script>
 
<div id="tabs" class="ui-tabs-nav ui-corner-all ui-helper-reset ui-helper-clearfix ui-widget-header">
<ul>
  <li><a href="#tabs-1">' . _AM_TH_INFO . '</a></li>
  <li><a href="#tabs-2">' . _AM_PROTECTOR_NOTIFICATION_SUBSCRIBE . '</a></li>
  <li><a href="#tabs-3">' . _AM_PROTECTOR_NOTIFICATION_TEST . '</a></li>
</ul>
<div id="tabs-1">';

echo '<p>' . _AM_PROTECTOR_NOTIFICATIONS_DESC . '</p>';
echo '<p>' . _AM_PROTECTOR_NOTIFICATIONS_AVAILABLE . ':</p>';
echo '<div class="danger"><strong>' . _AM_PROTECTOR_NOTIFY_SECURITY_EVENTS . '</strong> - ' . _AM_PROTECTOR_NOTIFY_SECURITY_EVENTS_DESC . '</div>';
echo '<div class="confirm"><strong>' . _AM_PROTECTOR_NOTIFY_PROXY_EVENTS . '</strong> - ' . _AM_PROTECTOR_NOTIFY_PROXY_EVENTS_DESC . '</div>';

echo '</div>';

// If admin, show option to subscribe all admins
if (isset($GLOBALS['xoopsUser']) && is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin()) {

echo '<div id="tabs-2">';

echo '<p>' . _AM_PROTECTOR_NOTIFICATIONS_ADMINS . '</p>';
echo '<form action="index.php?page=dashboard" method="post">';
echo '<input type="hidden" name="subscribe_admins" value="1">';
echo '<input type="submit" value="' . _AM_PROTECTOR_SUBSCRIBE_ADMINS . '" class="button">';
echo '</form>';
// Direct link to notifications page
echo '<p>' . _AM_PROTECTOR_NOTIFICATIONS_MANAGE . ':</p>';
echo '<a href="' . XOOPS_URL . '/notifications.php" class="button">' . _AM_PROTECTOR_MANAGE_NOTIFICATIONS . '</a>';

// Process admin subscription if requested
if (isset($_POST['subscribe_admins'])) {
  // Get module handler
  $module_handler = xoops_gethandler('module');
  $protector_module = $module_handler->getByDirname('protector');

  if (is_object($protector_module)) {
    // Force update the hasnotification flag
    $protector_module->setVar('hasnotification', 1);
    $module_handler->insert($protector_module);

    // Use standard handlers
    $notification_handler = xoops_gethandler('notification');
    $member_handler = xoops_gethandler('member');

    // Get webmasters group
    $webmasters_group_id = defined('XOOPS_GROUP_ADMIN') ? XOOPS_GROUP_ADMIN : 1;
    $webmasters = $member_handler->getUsersByGroup($webmasters_group_id);

    if (empty($webmasters)) {
      echo '<div class="errorMsg">Error: No webmasters found to subscribe.</div>';
    } else {
      $module_id = $protector_module->getVar('mid');
      $success = true;
      $subscribed_count = 0;

      // Events to subscribe to
      $events = [
        ['category' => 'global', 'event' => 'security_threat'],
        ['category' => 'global', 'event' => 'proxy_access']
      ];

      // Get database connection
      $db = XoopsDatabaseFactory::getDatabaseConnection();

      // Check if notification table exists - use xoopsnotifications instead of notifications
      $tableExists = $db->query("SHOW TABLES LIKE '" . $db->prefix('xoopsnotifications') . "'");

      if (!$tableExists || $db->getRowsNum($tableExists) == 0) {
        echo '<div class="errorMsg">Error: XOOPSCube notification table does not exist.</div>';
      } else {
        // Register notification events directly in the database
        // This is a workaround for modules that don't have proper notification registration

        // First, check if we need to register notification categories
        $sql = "SELECT COUNT(*) FROM " . $db->prefix('xoopsnotifications') . " 
                      WHERE not_modid = " . $module_id;
        $result = $db->query($sql);
        $event_registered = false;

        if ($result) {
          list($count) = $db->fetchRow($result);
          $event_registered = ($count > 0);
        }

        // If no events are registered, try to register a sample notification
        // This will help the system recognize that the module has notifications
        if (!$event_registered) {
          // Insert a sample notification for the current admin
          $current_uid = $GLOBALS['xoopsUser']->getVar('uid');
          $sample_event = $events[0]; // Use the first event as sample

          $sql = "INSERT INTO " . $db->prefix('xoopsnotifications') . " 
                          (not_modid, not_category, not_itemid, not_uid, not_event, not_mode) 
                          VALUES 
                          (" . $module_id . ", '" . $sample_event['category'] . "', 0, " . $current_uid . ", '" . $sample_event['event'] . "', 1)";

          if ($db->queryF($sql)) {
            echo '<div class="successMsg">Successfully registered notification events.</div>';
            $event_registered = true;
          } else {
            echo '<div class="errorMsg">Failed to register notification events: ' . $db->error() . '</div>';
          }
        }

        // Now subscribe each webmaster to each event
        if ($event_registered) {
          foreach ($webmasters as $user) {
            if (!is_object($user)) {
              continue;
            }

            $uid = $user->getVar('uid');

            foreach ($events as $event) {
              // Check if already subscribed
              $sql = "SELECT COUNT(*) FROM " . $db->prefix('xoopsnotifications') . " 
                                  WHERE not_modid = " . $module_id . " 
                                  AND not_category = '" . $event['category'] . "' 
                                  AND not_itemid = 0 
                                  AND not_event = '" . $event['event'] . "' 
                                  AND not_uid = " . $uid;

              $result = $db->query($sql);
              if ($result) {
                list($count) = $db->fetchRow($result);
                if ($count > 0) {
                  // Already subscribed
                  continue;
                }
              }

              // Insert notification
              $sql = "INSERT INTO " . $db->prefix('xoopsnotifications') . " 
                                  (not_modid, not_category, not_itemid, not_uid, not_event, not_mode) 
                                  VALUES 
                                  (" . $module_id . ", '" . $event['category'] . "', 0, " . $uid . ", '" . $event['event'] . "', 1)";

              if ($db->queryF($sql)) {
                $subscribed_count++;
              } else {
                $success = false;
                echo '<div class="errorMsg">SQL Error: ' . $db->error() . '</div>';
              }
            }
          }

          // Display result message
          if ($success && $subscribed_count > 0) {
            echo '<div class="successMsg">' . _AM_PROTECTOR_ADMINS_SUBSCRIBED . '</div>';
          } else if ($subscribed_count == 0) {
            echo '<div class="successMsg">All webmasters are already subscribed to notifications.</div>';
          } else {
            echo '<div class="errorMsg">' . _AM_PROTECTOR_SUBSCRIPTION_ERROR . '</div>';
          }
        }
      }
    }
  } else {
    echo '<div class="errorMsg">Error: Could not find Protector module.</div>';
  }
}

echo '</div>';
}

// Add a section for testing notifications
echo '<div id="tabs-3">';

echo "<p>" . _AM_PROTECTOR_NOTIFICATION_TEST_DESC . "</p>";
echo '<div class="tips">This will send a test security threat notification to all subscribed users.</div>';
echo '<section data-layout="row center-justify" class="action-control">';
// Test links for different threat levels
$test_levels = [
  1 => 'Basic Threat (Level 1)',
  16 => 'DoS/Crawler Attack (Level 16)',
  32 => 'SQL Injection (Level 32)',
  64 => 'Directory Traversal (Level 64)',
  128 => 'Spam Attack (Level 128)'
];
foreach ($test_levels as $level => $description) {
  // Change the URL to match the working format
  echo "<a href='index.php?page=dashboard&test_notification=1&level={$level}' class='button'>";
  echo $description;
  echo "</a>";
}
echo "</section>";
echo '<p><strong>Note</strong> If you do not receive notifications, check these things:</p>';
echo '<ol>';
echo '<li>Make sure users are subscribed (use the "Subscribe Admins")</li>';
echo '<li>Check your notification preferences in your user profile</li>';
echo '<li>Look in your active notifications: <a href="' . XOOPS_URL . '/notifications.php" target="_blank">notifications ⭧</a></li>';
echo '</ol>';
echo "</div>";
echo '</div>'; //id="tabs"


// Process test notification request - modify this section to work with both formats
if (isset($_GET['test_notification'])) {
  // Get protector instance
  $protector = protector::getInstance();

  // Set default level if not specified
  $level = isset($_GET['level']) ? (int)$_GET['level'] : 32;

  // Create appropriate message
  $protector->message = "This is a test notification for threat level {$level}";

  // Map level to threat type for more realistic testing
  $threat_types = [
    1 => 'TEST_BASIC',
    16 => 'TEST_DOS',
    32 => 'TEST_SQL',
    64 => 'TEST_TRAVERSAL',
    128 => 'TEST_SPAM'
  ];

  $type = $threat_types[$level] ?? 'TEST';

  // Get current user ID
  $uid = isset($GLOBALS['xoopsUser']) && is_object($GLOBALS['xoopsUser']) ?
    $GLOBALS['xoopsUser']->getVar('uid') : 0;

  // Trigger the log output which will send notifications
  $protector->output_log($type, $uid, false, $level);

  // Only redirect if we're processing the level-specific test
  if (isset($_GET['level'])) {
    redirect_header('index.php?page=dashboard', 3, sprintf(_AM_PROTECTOR_NOTIFICATION_SENT, $level));
  }
}

xoops_cp_footer();
