<?php
/**
 * Protector Web Proxy Logs
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

// Get database connection
$db = XoopsDatabaseFactory::getDatabaseConnection();

// Get total number of proxy logs
$result = $db->query('SELECT COUNT(*) FROM ' . $db->prefix($protector->mydirname . '_log') . ' WHERE type = "PROXY"');
list($total_logs) = $db->fetchRow($result);

// Pagination
$limit = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$total_pages = ceil($total_logs / $limit);

// Get logs
$result = $db->query('SELECT * FROM ' . $db->prefix($protector->mydirname . '_log') . 
                    ' WHERE type = "PROXY" ORDER BY timestamp DESC LIMIT ' . $offset . ', ' . $limit);

// Display logs
echo '<h3>Web Proxy Logs</h3>';

if ($total_logs == 0) {
    echo '<p>No proxy logs found.</p>';
} else {
    // Display pagination
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
    
    // Display logs table
    echo '<table class="outer" width="100%">';
    echo '<tr>';
    echo '<th>Time</th>';
    echo '<th>IP</th>';
    echo '<th>User Agent</th>';
    echo '<th>URL</th>';
    echo '<th>Status</th>';
    echo '<th>Message</th>';
    echo '</tr>';
    
    $class = 'even';
    while ($row = $db->fetchArray($result)) {
        $class = ($class == 'even') ? 'odd' : 'even';
        
        echo '<tr class="' . $class . '">';
        echo '<td>' . date('Y-m-d H:i:s', $row['timestamp']) . '</td>';
        echo '<td>' . long2ip($row['ip']) . '</td>';
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
    
    echo '</table>';
    
    // Display pagination again
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

// Include footer
xoops_cp_footer();