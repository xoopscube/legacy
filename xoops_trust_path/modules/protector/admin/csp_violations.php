<?php
/**
 * CSP Violations Admin Page
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

$myts = MyTextSanitizer::getInstance();
$db = $xoopsDB;

// Get module config
$module_handler = xoops_gethandler('module');
$config_handler = xoops_gethandler('config');
$protector_module = $module_handler->getByDirname('protector');
$protector_configs = $config_handler->getConfigsByCat(0, $protector_module->getVar('mid'));

// Check if CSP is enabled
if (empty($protector_configs['enable_csp'])) {
    redirect_header('index.php', 3, _AM_PROTECTOR_CSP_DISABLED);
    exit();
}

// Get action
$op = isset($_REQUEST['op']) ? $_REQUEST['op'] : 'list';

// Handle actions
switch ($op) {
    case 'delete':
        // Check token
        if (!isset($_POST['confirm']) || !$xoopsGTicket->check(true, 'protector_admin')) {
            redirect_header('index.php?page=csp_violations', 3, $xoopsGTicket->getErrors());
        }
        
        // Get violation ID
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            $sql = "DELETE FROM " . $db->prefix('protector_csp_violations') . " WHERE id = $id";
            $db->query($sql);
            redirect_header('index.php?page=csp_violations', 3, _AM_PROTECTOR_CSP_DELETED);
        }
        break;
        
    case 'clear':
        // Check token
        if (!isset($_POST['confirm']) || !$xoopsGTicket->check(true, 'protector_admin')) {
            redirect_header('index.php?page=csp_violations', 3, $xoopsGTicket->getErrors());
        }
        
        // Clear all violations from database
        $sql = "TRUNCATE TABLE " . $db->prefix('protector_csp_violations');
        $db->query($sql);
        
        // Also clear log files
        $log_dir = XOOPS_CACHE_PATH . '/protector/logs';
        if (is_dir($log_dir)) {
            $files = glob($log_dir . '/csp_*.log');
            foreach ($files as $file) {
                @unlink($file);
            }
        }
        
        redirect_header('index.php?page=csp_violations', 3, _AM_PROTECTOR_CSP_CLEARED);
        break;
        
    case 'view':
        // Get violation ID
        $id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;
        if ($id <= 0) {
            redirect_header('index.php?page=csp_violations', 3, _AM_PROTECTOR_CSP_INVALID_ID);
        }
        
        // Get violation details
        $sql = "SELECT * FROM " . $db->prefix('protector_csp_violations') . " WHERE id = $id";
        $result = $db->query($sql);
        if (!$result || $db->getRowsNum($result) == 0) {
            redirect_header('index.php?page=csp_violations', 3, _AM_PROTECTOR_CSP_NOT_FOUND);
        }
        
        $violation = $db->fetchArray($result);
        
        // Display violation details
        xoops_cp_header();
        include dirname(__FILE__).'/mymenu.php';
        
        echo '<h2>' . _AM_PROTECTOR_CSP_VIEW_TITLE . '</h2>';
        echo '<div class="csp-violation-details">';
        echo '<p><strong>' . _AM_PROTECTOR_CSP_TIME . ':</strong> ' . date('Y-m-d H:i:s', $violation['created']) . '</p>';
        echo '<p><strong>' . _AM_PROTECTOR_CSP_IP . ':</strong> ' . htmlspecialchars($violation['ip']) . '</p>';
        echo '<p><strong>' . _AM_PROTECTOR_CSP_DOCUMENT_URI . ':</strong> ' . htmlspecialchars($violation['document_uri']) . '</p>';
        echo '<p><strong>' . _AM_PROTECTOR_CSP_VIOLATED_DIRECTIVE . ':</strong> ' . htmlspecialchars($violation['violated_directive']) . '</p>';
        echo '<p><strong>' . _AM_PROTECTOR_CSP_BLOCKED_URI . ':</strong> ' . htmlspecialchars($violation['blocked_uri']) . '</p>';
        
        if (!empty($violation['source_file'])) {
            echo '<p><strong>' . _AM_PROTECTOR_CSP_SOURCE_FILE . ':</strong> ' . htmlspecialchars($violation['source_file']) . '</p>';
        }
        
        if (!empty($violation['line_number'])) {
            echo '<p><strong>' . _AM_PROTECTOR_CSP_LINE_NUMBER . ':</strong> ' . (int)$violation['line_number'] . '</p>';
        }
        
        if (!empty($violation['column_number'])) {
            echo '<p><strong>' . _AM_PROTECTOR_CSP_COLUMN_NUMBER . ':</strong> ' . (int)$violation['column_number'] . '</p>';
        }
        
        if (!empty($violation['referrer'])) {
            echo '<p><strong>' . _AM_PROTECTOR_CSP_REFERRER . ':</strong> ' . htmlspecialchars($violation['referrer']) . '</p>';
        }
        
        if (!empty($violation['user_agent'])) {
            echo '<p><strong>' . _AM_PROTECTOR_CSP_USER_AGENT . ':</strong> ' . htmlspecialchars($violation['user_agent']) . '</p>';
        }
        
        echo '</div>';
        
        // Delete button
        echo '<form action="index.php?page=csp_violations" method="post">';
        echo $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'protector_admin');
        echo '<input type="hidden" name="op" value="delete">';
        echo '<input type="hidden" name="id" value="' . $id . '">';
        echo '<input type="hidden" name="confirm" value="1">';
        echo '<input type="submit" class="formButton" value="' . _AM_PROTECTOR_CSP_DELETE . '">';
        echo '&nbsp;<input type="button" class="formButton" value="' . _AM_PROTECTOR_BACK . '" onclick="location.href=\'index.php?page=csp_violations\'">';
        echo '</form>';
        
        xoops_cp_footer();
        exit();
        
    case 'view_log':
        // Get log file
        $log_file = isset($_REQUEST['file']) ? $_REQUEST['file'] : '';
        $log_dir = XOOPS_CACHE_PATH . '/protector/logs';
        $full_path = $log_dir . '/' . basename($log_file);
        
        if (!file_exists($full_path) || !is_file($full_path) || !is_readable($full_path)) {
            redirect_header('index.php?page=csp_violations', 3, 'Log file not found or not readable');
            exit();
        }
        
        // Display log file contents
        xoops_cp_header();
        include dirname(__FILE__).'/mymenu.php';
        
        echo '<h3>CSP Log File: ' . htmlspecialchars(basename($log_file)) . '</h3>';
        
        // Back button
        echo '<p><a href="index.php?page=csp_violations" class="formButton">' . _AM_PROTECTOR_BACK . '</a></p>';
        
        // Display log contents
        echo '<div class="csp-log-contents" style="background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; white-space: pre-wrap; font-family: monospace;">';
        echo htmlspecialchars(file_get_contents($full_path));
        echo '</div>';
        
        // Delete log file button
        echo '<form action="index.php?page=csp_violations" method="post" style="margin-top: 15px;">';
        echo $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'protector_admin');
        echo '<input type="hidden" name="op" value="delete_log">';
        echo '<input type="hidden" name="file" value="' . htmlspecialchars(basename($log_file)) . '">';
        echo '<input type="hidden" name="confirm" value="1">';
        echo '<input type="submit" class="formButton" value="Delete Log File" onclick="return confirm(\'Are you sure you want to delete this log file?\');">';
        echo '</form>';
        
        xoops_cp_footer();
        exit();
        
    case 'delete_log':
        // Check token
        if (!isset($_POST['confirm']) || !$xoopsGTicket->check(true, 'protector_admin')) {
            redirect_header('index.php?page=csp_violations', 3, $xoopsGTicket->getErrors());
        }
        
        // Get log file
        $log_file = isset($_POST['file']) ? $_POST['file'] : '';
        $log_dir = XOOPS_CACHE_PATH . '/protector/logs';
        $full_path = $log_dir . '/' . basename($log_file);
        
        if (file_exists($full_path) && is_file($full_path)) {
            @unlink($full_path);
        }
        
        redirect_header('index.php?page=csp_violations', 3, 'Log file deleted');
        break;
        
    case 'list':
    default:
        // Display list of violations
        xoops_cp_header();
        include dirname(__FILE__).'/mymenu.php';
        
        echo '<h3>' . _AM_PROTECTOR_CSP_VIOLATIONS . '</h3>';
        
        // Check for CSP log files and parse their contents
        $log_dir = XOOPS_CACHE_PATH . '/protector/logs';
        $log_entries = [];
        
        if (is_dir($log_dir)) {
            $files = glob($log_dir . '/csp_*.log');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    
                    // Try to parse the log entry
                    if (preg_match('/\[(.*?)\] \[IP: (.*?)\]/', $line, $matches)) {
                        $timestamp = strtotime($matches[1]);
                        $ip = $matches[2];
                        
                        // Extract other information
                        $document_uri = '';
                        $blocked_uri = '';
                        $violated_directive = '';
                        $user_agent = '';
                        
                        if (preg_match('/CSP Violation: (.*?)\n/', $line, $doc_matches)) {
                            $document_uri = $doc_matches[1];
                        }
                        
                        if (preg_match('/Blocked URI: (.*?)\n/', $line, $block_matches)) {
                            $blocked_uri = $block_matches[1];
                        }
                        
                        if (preg_match('/Violated Directive: (.*?)\n/', $line, $dir_matches)) {
                            $violated_directive = $dir_matches[1];
                        }
                        
                        if (preg_match('/User Agent: (.*?)\n/', $line, $ua_matches)) {
                            $user_agent = $ua_matches[1];
                        }
                        
                        $log_entries[] = [
                            'timestamp' => $timestamp,
                            'ip' => $ip,
                            'document_uri' => $document_uri,
                            'blocked_uri' => $blocked_uri,
                            'violated_directive' => $violated_directive,
                            'user_agent' => $user_agent,
                            'source' => 'log',
                            'file' => basename($file)
                        ];
                    } else if (strpos($line, 'Test page accessed from') !== false) {
                        // Handle test log entries
                        if (preg_match('/^(.*?) - Test page accessed from (.*)$/', $line, $test_matches)) {
                            $timestamp = strtotime($test_matches[1]);
                            $ip = $test_matches[2];
                            
                            $log_entries[] = [
                                'timestamp' => $timestamp,
                                'ip' => $ip,
                                'document_uri' => 'CSP Test Page',
                                'blocked_uri' => 'N/A',
                                'violated_directive' => 'Test Access',
                                'user_agent' => 'N/A',
                                'source' => 'test',
                                'file' => basename($file)
                            ];
                        }
                    } else {
                        // Generic log entry that doesn't match expected format
                        $log_entries[] = [
                            'timestamp' => filemtime($file),
                            'ip' => 'Unknown',
                            'document_uri' => 'Unknown Format',
                            'blocked_uri' => 'N/A',
                            'violated_directive' => 'N/A',
                            'user_agent' => 'N/A',
                            'source' => 'unknown',
                            'raw_data' => $line,
                            'file' => basename($file)
                        ];
                    }
                }
            }
        }
        
        // Get violations from database
        $db_entries = [];
        $sql = "SELECT * FROM " . $db->prefix('protector_csp_violations') . " ORDER BY created DESC";
        $result = $db->query($sql);
        
        // Check if the table exists
        $has_table = ($result !== false);
        
        if ($has_table && $db->getRowsNum($result) > 0) {
            while ($violation = $db->fetchArray($result)) {
                $db_entries[] = [
                    'timestamp' => $violation['created'],
                    'ip' => $violation['ip'],
                    'document_uri' => $violation['document_uri'],
                    'blocked_uri' => $violation['blocked_uri'],
                    'violated_directive' => $violation['violated_directive'],
                    'user_agent' => $violation['user_agent'] ?? 'N/A',
                    'source' => 'database',
                    'id' => $violation['id']
                ];
            }
        }
        
        // Merge and sort all entries by timestamp (newest first)
        $all_entries = array_merge($log_entries, $db_entries);
        usort($all_entries, function($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // Display combined table of violations
        if (!empty($all_entries)) {
            echo '<h4>All CSP Violations</h4>';
            echo '<table class="outer" width="100%">';
            echo '<thead><tr>';
            echo '<th>Time</th>';
            echo '<th>Source</th>';
            echo '<th>IP</th>';
            echo '<th>Document URI</th>';
            echo '<th>Violated Directive</th>';
            echo '<th>Blocked URI</th>';
            echo '<th>Actions</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            $class = 'even';
            foreach ($all_entries as $entry) {
                // $class = ($class == 'even') ? 'odd' : 'even';
                
                // echo '<tr class="' . $class . '">';
                echo '<tr>';
                echo '<td>' . date('Y-m-d H:i:s', $entry['timestamp']) . '</td>';
                
                // Source column with icon or text
                echo '<td>';
                if ($entry['source'] === 'database') {
                    echo '<span title="Database Record">DB</span>';
                } elseif ($entry['source'] === 'log') {
                    echo '<span title="Log File: ' . htmlspecialchars($entry['file']) . '">Log</span>';
                } elseif ($entry['source'] === 'test') {
                    echo '<span title="Test Access Log">Test</span>';
                } else {
                    echo '<span title="Unknown Source">?</span>';
                }
                echo '</td>';
                
                echo '<td>' . htmlspecialchars($entry['ip']) . '</td>';
                echo '<td>' . htmlspecialchars(substr($entry['document_uri'], 0, 30)) . (strlen($entry['document_uri']) > 30 ? '...' : '') . '</td>';
                echo '<td>' . htmlspecialchars(substr($entry['violated_directive'], 0, 30)) . (strlen($entry['violated_directive']) > 30 ? '...' : '') . '</td>';
                echo '<td>' . htmlspecialchars(substr($entry['blocked_uri'], 0, 30)) . (strlen($entry['blocked_uri']) > 30 ? '...' : '') . '</td>';
                
                // Actions column
                echo '<td>';
                if ($entry['source'] === 'database') {
                    echo '<a href="index.php?page=csp_violations&op=view&id=' . $entry['id'] . '">View</a>';
                } elseif (isset($entry['raw_data'])) {
                    echo '<span title="' . htmlspecialchars($entry['raw_data']) . '">Details</span>';
                } else {
                    echo '<span title="' . 
                         'Document: ' . htmlspecialchars($entry['document_uri']) . 
                         '\nBlocked: ' . htmlspecialchars($entry['blocked_uri']) . 
                         '\nDirective: ' . htmlspecialchars($entry['violated_directive']) . 
                         '\nUser Agent: ' . htmlspecialchars($entry['user_agent']) . 
                         '">Details</span>';
                }
                echo '</td>';
                
                echo '</tr>';
            }
        echo '</tbody>';
        echo '<tfoot><tr><td colspan="7">';
        // Clear all button
        echo '<form action="index.php?page=csp_violations" method="post">';
        echo $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'protector_admin');
        echo '<input type="hidden" name="op" value="clear">';
        echo '<input type="hidden" name="confirm" value="1">';
        echo '<input type="submit" class="formButton" value="' . _AM_PROTECTOR_CSP_CLEAR_ALL . '" onclick="return confirm(\'' . _AM_PROTECTOR_CSP_CONFIRM_CLEAR . '\');">';
        echo '</form>';
        echo '</td></tr></tfoot>';
        echo '</table>';
        } else {
            echo '<div class="errorMsg" style="margin-top: 20px;">No CSP violations have been detected. Either your CSP is working perfectly or no violations have occurred yet.</div>';
        }
        
        // Add export functionality
        echo '<div data-layout="row sm-column" style="margin-top: 20px;">';
        
        // Export section
        echo '<div data-self="size-1of2 sm-full">';
        echo '<div class="confirm">';
        echo '<h4>Export CSP Violations</h4>';
        
        // Create separate buttons for each export format
        echo '<form action="index.php" method="get" data-self="inline">
              <input type="hidden" name="page" value="csp_violations">
              <input type="hidden" name="op" value="export">
              <input type="hidden" name="format" value="csv">
              <input type="submit" value="Download CSV" class="formButton">
              </form>';
        
        echo '<form action="index.php" method="get" data-self="inline">
              <input type="hidden" name="page" value="csp_violations">
              <input type="hidden" name="op" value="export">
              <input type="hidden" name="format" value="txt">
              <input type="submit" value="Download TXT" class="formButton">
              </form>';
        
        echo '<form action="index.php" method="get" data-self="inline">
              <input type="hidden" name="page" value="csp_violations">
              <input type="hidden" name="op" value="export">
              <input type="hidden" name="format" value="json">
              <input type="submit" value="Download JSON" class="formButton">
              </form>';
        
        echo '<p><small>Export all CSP violations to a file for backup or analysis.</small></p>';
        echo '</div>';
        echo '</div>';
        
        // Import section
        echo '<div data-self="size-1of2 sm-full">';
        echo '<div class="danger">';
        echo '<h4>Import CSP Violations</h4>';
        echo '<form action="index.php?page=csp_violations" method="post" enctype="multipart/form-data">';
        echo $xoopsGTicket->getTicketHtml(__LINE__, 1800, 'protector_admin');
        echo '<input type="hidden" name="op" value="import">';
        echo '<input type="file" name="import_file" accept=".txt,.csv,.json" class="formButton"> ';
        echo '<input type="submit" value="Upload" class="formButton">';
        echo '</form><br>';
        echo '<small>Import CSP violations from a previously exported file.</small>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        
        xoops_cp_footer();
        break;
        
    // Add export functionality
    case 'export':
        // Skip CSRF check for exports since it's just reading data
        
        $format = $_REQUEST['format'] ?? 'csv';
        
        // Collect all violations from logs and database
        $all_entries = [];
        
        // Get log entries
        $log_dir = XOOPS_CACHE_PATH . '/protector/logs';
        if (is_dir($log_dir)) {
            $files = glob($log_dir . '/csp_*.log');
            foreach ($files as $file) {
                $content = file_get_contents($file);
                $lines = explode("\n", $content);
                
                foreach ($lines as $line) {
                    if (empty(trim($line))) continue;
                    
                    // Try to parse the log entry
                    if (preg_match('/\[(.*?)\] \[IP: (.*?)\]/', $line, $matches)) {
                        $timestamp = strtotime($matches[1]);
                        $ip = $matches[2];
                        
                        // Extract other information
                        $document_uri = '';
                        $blocked_uri = '';
                        $violated_directive = '';
                        $user_agent = '';
                        
                        if (preg_match('/CSP Violation: (.*?)\n/', $line, $doc_matches)) {
                            $document_uri = $doc_matches[1];
                        }
                        
                        if (preg_match('/Blocked URI: (.*?)\n/', $line, $block_matches)) {
                            $blocked_uri = $block_matches[1];
                        }
                        
                        if (preg_match('/Violated Directive: (.*?)\n/', $line, $dir_matches)) {
                            $violated_directive = $dir_matches[1];
                        }
                        
                        if (preg_match('/User Agent: (.*?)\n/', $line, $ua_matches)) {
                            $user_agent = $ua_matches[1];
                        }
                        
                        $all_entries[] = [
                            'timestamp' => date('Y-m-d H:i:s', $timestamp),
                            'ip' => $ip,
                            'document_uri' => $document_uri,
                            'blocked_uri' => $blocked_uri,
                            'violated_directive' => $violated_directive,
                            'user_agent' => $user_agent,
                            'source' => 'Log File: ' . basename($file)
                        ];
                    }
                }
            }
        }
        
        // Get database entries
        $sql = "SELECT * FROM " . $db->prefix('protector_csp_violations') . " ORDER BY created DESC";
        $result = $db->query($sql);
        
        if ($result && $db->getRowsNum($result) > 0) {
            while ($violation = $db->fetchArray($result)) {
                $all_entries[] = [
                    'timestamp' => date('Y-m-d H:i:s', $violation['created']),
                    'ip' => $violation['ip'],
                    'document_uri' => $violation['document_uri'],
                    'blocked_uri' => $violation['blocked_uri'],
                    'violated_directive' => $violation['violated_directive'],
                    'user_agent' => $violation['user_agent'] ?? 'N/A',
                    'source' => 'Database ID: ' . $violation['id']
                ];
            }
        }

        // Set headers for download
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);

        if ($format === 'csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="csp_violations_' . date('Ymd') . '.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['Timestamp', 'IP', 'Document URI', 'Blocked URI', 'Violated Directive', 'User Agent', 'Source']);
            foreach ($all_entries as $entry) {
                fputcsv($output, $entry);
            }
            fclose($output);
        } elseif ($format === 'json') {
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="csp_violations_' . date('Ymd') . '.json"');
            
            echo json_encode($all_entries, JSON_PRETTY_PRINT);
        } else {
            // Text format
            header('Content-Type: text/plain');
            header('Content-Disposition: attachment; filename="csp_violations_' . date('Ymd') . '.txt"');
            
            foreach ($all_entries as $entry) {
                echo "Timestamp: " . (isset($entry['timestamp']) ? $entry['timestamp'] : 'N/A') . "\n";
                echo "IP: " . (isset($entry['ip']) ? $entry['ip'] : 'N/A') . "\n";
                echo "Document URI: " . (isset($entry['document_uri']) ? $entry['document_uri'] : 'N/A') . "\n";
                echo "Blocked URI: " . (isset($entry['blocked_uri']) ? $entry['blocked_uri'] : 'N/A') . "\n";
                echo "Violated Directive: " . (isset($entry['violated_directive']) ? $entry['violated_directive'] : 'N/A') . "\n";
                echo "User Agent: " . (isset($entry['user_agent']) ? $entry['user_agent'] : 'N/A') . "\n";
                echo "Source: " . (isset($entry['source']) ? $entry['source'] : 'N/A') . "\n\n";
            }
        }

        // Exit after sending the file
        exit;
}