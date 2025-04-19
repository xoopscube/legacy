<?php
/**
 * Protector Web Proxy Logs
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// Display admin menu
include __DIR__ . '/mymenu.php';

// Get protector instance
$protector = protector::getInstance();

// Get database connection
$db = XoopsDatabaseFactory::getDatabaseConnection();

// Get total number of proxy logs
$result = $db->query('SELECT COUNT(*) FROM ' . $db->prefix($protector->mydirname . '_log') . ' WHERE type LIKE "PROXY%" OR description LIKE "%URL:%"');
list($total_logs) = $db->fetchRow($result);

// Pagination
$limit = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$total_pages = ceil($total_logs / $limit);

// Get logs
$result = $db->query('SELECT * FROM ' . $db->prefix($protector->mydirname . '_log') . 
                    ' WHERE type LIKE "PROXY%" OR description LIKE "%URL:%" ORDER BY timestamp DESC LIMIT ' . $offset . ', ' . $limit);



// Check for recent requests in all cache files
$recent_requests = [];
$cache_dir = XOOPS_CACHE_PATH . '/proxy';

// First check if directory exists
if (is_dir($cache_dir)) {
    // Get all recent_*.php files
    $recent_files = glob($cache_dir . '/recent_*.php');
    
    // Process each file to collect all recent requests
    if (!empty($recent_files)) {
        foreach ($recent_files as $recent_file) {
            // Include the file to get its cached_requests
            include $recent_file;
            if (isset($cached_requests) && is_array($cached_requests)) {
                // Merge with existing requests
                $recent_requests = array_merge($recent_requests, $cached_requests);
            }
        }
        
        // Sort by time (newest first)
        usort($recent_requests, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        // Limit to most recent 30 entries
        $recent_requests = array_slice($recent_requests, 0, 30);
    }
}

// Also check the stats file
$stats = [];
$stats_file = XOOPS_CACHE_PATH . '/protector/proxy_stats.php';
if (file_exists($stats_file)) {
    include $stats_file;
    if (isset($proxy_stats)) {
        $stats = $proxy_stats;
    }
}

// display stats
echo '<div class="ui-card-overview">';

echo '<div class="ui-card-small">
  <div class="ui-card-small-icon ui-icon-blue">
  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" role="img">
  <path d="M16 17v2H2v-2s0-4 7-4s7 4 7 4m-3.5-9.5A3.5 3.5 0 1 0 9 11a3.5 3.5 0 0 0 3.5-3.5m3.44 5.5A5.32 5.32 0 0 1 18 17v2h4v-2s0-3.63-6.06-4M15 4a3.39 3.39 0 0 0-1.93.59a5 5 0 0 1 0 5.82A3.39 3.39 0 0 0 15 11a3.5 3.5 0 0 0 0-7z" fill="currentColor">
  </path></svg>
  </div>
  <div class="ui-card-small-info">
    <h4 class="ui-card-small-title">Total Requests: <strong>' . (isset($stats['total_requests']) ? (int)$stats['total_requests'] : 0) . '</strong></h4>
  </div>
</div>';

echo '<div class="ui-card-small">
  <div class="ui-card-small-icon ui-icon-green">
  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
  <path d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4 4a4 4 0 0 1 4-4m0 10c4.42 0 8 1.79 8 4v2H4v-2c0-2.21 3.58-4 8-4z" fill="currentColor">
  </path></svg>
  </div>
  <div class="ui-card-small-info">
    <h4 class="ui-card-small-title">Cached Resources: <strong>' . (isset($stats['blocked_requests']) ? (int)$stats['blocked_requests'] : 0) . '</strong></h4>
  </div>
</div>';

echo '<div class="ui-card-small">
  <div class="ui-card-small-icon ui-icon-red">
  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24" role="img">
  <path d="M12 4a4 4 0 0 1 4 4a4 4 0 0 1-4 4a4 4 0 0 1-4-4a4 4 0 0 1 4-4m0 2a2 2 0 0 0-2 2a2 2 0 0 0 2 2a2 2 0 0 0 2-2a2 2 0 0 0-2-2m0 7c2.67 0 8 1.33 8 4v3H4v-3c0-2.67 5.33-4 8-4m0 1.9c-2.97 0-6.1 1.46-6.1 2.1v1.1h12.2V17c0-.64-3.13-2.1-6.1-2.1z" fill="currentColor">
  </path></svg>
  </div>
  <div class="ui-card-small-info">
    <h4 class="ui-card-small-title">Blocked Malicious: <strong>' . (isset($stats['blocked_requests']) ? (int)$stats['blocked_requests'] : 0) . '</strong></h4>
  </div>
</div>';

echo '</div>'; // ui-card-overview

// Display logs
echo '<h3>Web Proxy Logs</h3>';
echo '<section data-layout="rows center-justify" class="action-control">';
echo '<div>Database Logs: <span class="badge-count">' . $total_logs . '</span> Recent Requests: <span class="badge-count">' . count($recent_requests) . '</span></div>';
if (isset($stats['last_updated'])) {
    echo '<div>Last Updated: <span class="badge-count">' . date('Y-m-d H:i:s', (int)$stats['last_updated']) . '</span></div>';
}
echo '</section>';



if ($total_logs == 0 && empty($recent_requests)) {
    echo '<p>No proxy logs found.</p>';
} else {
    // Display pagination for database logs
    if ($total_logs > 0) {
        echo '<div class="pagenav">';
        if ($page > 1) {
            echo '<a href="index.php?page=proxy_logs&page=' . ($page - 1) . '">&lt; Prev</a> ';
        }
        
        for ($i = max(1, $page - 5); $i <= min($total_pages, $page + 5); $i++) {
            if ($i == $page) {
                echo '<strong>' . $i . '</strong> ';
            } else {
                echo '<a href="index.php?page=proxy_logs&page=' . $i . '">' . $i . '</a> ';
            }
        }
        
        if ($page < $total_pages) {
            echo '<a href="index.php?page=proxy_logs&page=' . ($page + 1) . '">Next &gt;</a>';
        }
        echo '</div>';
    }
    
    // Display logs table
    echo '<table class="outer" width="100%">';
    echo '<thead><tr>';
    echo '<th>Time</th>';
    echo '<th>IP</th>';
    echo '<th>User Agent</th>';
    echo '<th>URL</th>';
    echo '<th>Status</th>';
    echo '<th>Message</th>';
    echo '</tr></thead>';
    
    // First display database logs
    $class = 'even';
    while ($row = $db->fetchArray($result)) {
        $class = ($class == 'even') ? 'odd' : 'even';
        
        echo '<tr class="' . $class . '">';
        // Ensure timestamp is treated as an integer
        $timestamp = (int)$row['timestamp'];
        echo '<td>' . date('Y-m-d H:i:s', $timestamp) . '</td>';
        
        // Display IP directly as string
        echo '<td>' . htmlspecialchars($row['ip']) . '</td>';
        echo '<td>' . htmlspecialchars(substr($row['agent'], 0, 50)) . (strlen($row['agent']) > 50 ? '...' : '') . '</td>';
        
        // Extract URL from description
        preg_match('/URL: (.*?), Status:/', $row['description'], $matches);
        $url = isset($matches[1]) ? $matches[1] : '';
        
        // Extract status from description
        preg_match('/Status: (.*?), Message:/', $row['description'], $matches);
        $status = isset($matches[1]) ? $matches[1] : '';
        
        // Extract message from description
        preg_match('/Message: (.*)$/', $row['description'], $matches);
        $message = isset($matches[1]) ? $matches[1] : '';
        
        echo '<td>' . htmlspecialchars($url) . '</td>';
        echo '<td>' . htmlspecialchars($status) . '</td>';
        echo '<td>' . htmlspecialchars($message) . '</td>';
        echo '</tr>';
    }
    
    // Then display recent requests from cache files
    foreach ($recent_requests as $request) {
        $class = ($class == 'even') ? 'odd' : 'even';
        
        echo '<tr class="' . $class . '">';
        echo '<td>' . htmlspecialchars($request['time']) . '</td>';
        echo '<td>' . htmlspecialchars($request['ip']) . '</td>';
        echo '<td>' . htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . '</td>';
        echo '<td>' . htmlspecialchars($request['url']) . '</td>';
        echo '<td>' . htmlspecialchars($request['status']) . '</td>';
        echo '<td>' . htmlspecialchars($request['title'] ?? '') . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    
    // Display pagination again
    if ($total_logs > 0) {
        echo '<div class="pagenav">';
        if ($page > 1) {
            echo '<a href="index.php?page=proxy_logs&page=' . ($page - 1) . '">&lt; Prev</a> ';
        }
        
        for ($i = max(1, $page - 5); $i <= min($total_pages, $page + 5); $i++) {
            if ($i == $page) {
                echo '<strong>' . $i . '</strong> ';
            } else {
                echo '<a href="index.php?page=proxy_logs&page=' . $i . '">' . $i . '</a> ';
            }
        }
        
        if ($page < $total_pages) {
            echo '<a href="index.php?page=proxy_logs&page=' . ($page + 1) . '">Next &gt;</a>';
        }
        echo '</div>';
    }
    
/*     // Display stats if available
    if (!empty($stats)) {
        echo '<div class="proxy-stats" style="margin-top: 20px; padding: 10px; background-color: #f5f5f5; border: 1px solid #ddd;">';
        echo '<h4>Proxy Statistics</h4>';
        echo '<ul>';
        echo '<li>Total Requests: ' . (int)$stats['total_requests'] . '</li>';
        echo '<li>Blocked Requests: ' . (int)$stats['blocked_requests'] . '</li>';
        echo '<li>Cached Resources: ' . (int)$stats['cached_resources'] . '</li>';
        if (isset($stats['last_updated'])) {
            echo '<li>Last Updated: ' . date('Y-m-d H:i:s', (int)$stats['last_updated']) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    } */
}

// Include footer
xoops_cp_footer();