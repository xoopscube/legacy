<?php
/**
 * Protector Log Viewer
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// Get protector instance
$protector = protector::getInstance();

// Get database connection
$db = XoopsDatabaseFactory::getDatabaseConnection();

// Process clear logs action
if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['confirm']) && $_POST['confirm'] === '1') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header('index.php?page=log', 3, _NOPERM);
        exit;
    }
    
    // Delete logs
    $db->query('TRUNCATE TABLE ' . $db->prefix($protector->mydirname . '_log'));
    
    // Redirect with success message
    redirect_header('index.php?page=log', 3, _MI_PROTECTOR_LOGCLEARED);
    exit;
}

// Add export functionality
if (isset($_GET['op']) && $_GET['op'] === 'export') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(false, 'protector_admin')) {
        redirect_header('index.php?page=log', 3, _NOPERM);
        exit;
    }
    
    $format = $_POST['format'] ?? 'csv';
    
    // Get all log entries for export
    $result = $db->query('SELECT l.*, INET_NTOA(l.ip) AS ip_text 
                         FROM ' . $db->prefix($protector->mydirname . '_log') . ' l 
                         ORDER BY timestamp DESC');
    
    $data = [];
    $headers = ['Date/Time', 'IP', 'Type', 'Agent', 'Description'];
    
    while ($row = $db->fetchArray($result)) {
        $data[] = [
            date('Y-m-d H:i:s', $row['timestamp']),
            $row['ip_text'],
            $row['type'],
            $row['agent'],
            $row['description']
        ];
    }
    
    // Set headers for download
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="protector_logs_' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    } else {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="protector_logs_' . date('Ymd') . '.txt"');
        
        echo implode("\t", $headers) . "\n";
        foreach ($data as $row) {
            echo implode("\t", $row) . "\n";
        }
    }
    exit;
}

// Add import functionality
if (isset($_POST['action']) && $_POST['action'] === 'import') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header('index.php?page=log', 3, _NOPERM);
        exit;
    }
    
    // Check if file was uploaded
    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['import_file']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION));
        
        // Process file based on type
        $entries = [];
        
        if ($file_ext === 'csv') {
            // Parse CSV file
            if (($handle = fopen($tmp_name, "r")) !== FALSE) {
                // Skip header row
                fgetcsv($handle);
                
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if (count($data) >= 5) {
                        $timestamp = strtotime($data[0]);
                        $ip = $data[1];
                        $type = $data[2];
                        $agent = $data[3];
                        $description = $data[4];
                        
                        // Insert into database
                        $sql = "INSERT INTO " . $db->prefix($protector->mydirname . '_log') . 
                               " (timestamp, ip, type, agent, description) VALUES " .
                               "(" . $timestamp . ", INET_ATON('" . $db->escape($ip) . "'), '" . 
                               $db->escape($type) . "', '" . $db->escape($agent) . "', '" . 
                               $db->escape($description) . "')";
                        $db->query($sql);
                    }
                }
                fclose($handle);
            }
        } else {
            // Parse TXT file
            $lines = file($tmp_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            // Skip header row
            array_shift($lines);
            
            foreach ($lines as $line) {
                $data = explode("\t", $line);
                if (count($data) >= 5) {
                    $timestamp = strtotime($data[0]);
                    $ip = $data[1];
                    $type = $data[2];
                    $agent = $data[3];
                    $description = $data[4];
                    
                    // Insert into database
                    $sql = "INSERT INTO " . $db->prefix($protector->mydirname . '_log') . 
                           " (timestamp, ip, type, agent, description) VALUES " .
                           "(" . $timestamp . ", INET_ATON('" . $db->escape($ip) . "'), '" . 
                           $db->escape($type) . "', '" . $db->escape($agent) . "', '" . 
                           $db->escape($description) . "')";
                    $db->query($sql);
                }
            }
        }
        
        redirect_header('index.php?page=log', 3, _AM_PROTECTOR_IMPORT_SUCCESS);
        exit;
    } else {
        redirect_header('index.php?page=log', 3, _AM_PROTECTOR_IMPORT_ERROR);
        exit;
    }
}

// Get page parameters
$offset = (int)($_GET['pos'] ?? 0);
$num = 30;

// Get total log count
$result = $db->query('SELECT COUNT(*) FROM ' . $db->prefix($protector->mydirname . '_log'));
[$total] = $db->fetchRow($result);

// Get log entries
$result = $db->query('SELECT l.*, INET_NTOA(l.ip) AS ip_text 
                     FROM ' . $db->prefix($protector->mydirname . '_log') . ' l 
                     ORDER BY timestamp DESC', $num, $offset);

// Display admin menu
include __DIR__ . '/mymenu.php';

// Start output
echo '<h3>' . _MI_PROTECTOR_LOGLIST . '</h3>';



// Display pagination
if ($total > 0) {
    echo '<div class="pagenav">';
    if ($offset > 0) {
        echo '<a href="index.php?page=log&pos=' . max(0, $offset - $num) . '">&lt; ' . _AM_PAGE_PREV . '</a> ';
    }
    if ($offset + $num < $total) {
        echo '<a href="index.php?page=log&pos=' . ($offset + $num) . '">' . _AM_PAGE_NEXT . ' &gt;</a>';
    }
    echo '</div>';
}

// Display log table
echo '<table class="outer" width="100%">
      <tr>
        <th>' . _AM_TH_DATETIME . '</th>
        <th>' . _AM_TH_TYPE . '</th>
        <th>' . _AM_TH_IP . '</th>
        <th>' . _AM_TH_AGENT . '</th>
        <th>' . _AM_TH_URI . '</th>
      </tr>';

// Display log entries
$class = 'even';
while ($row = $db->fetchArray($result)) {
    $class = ($class === 'even') ? 'odd' : 'even';
    
    echo '<tr class="' . $class . '">
          <td>' . date(_DATESTRING, (int)$row['timestamp']) . '</td>
          <td>' . htmlspecialchars($row['type'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['ip_text'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['agent'] ?? '') . '</td>
          <td>' . htmlspecialchars($row['uri'] ?? '') . '</td>
        </tr>';
}

echo '</table>';

// Display clear logs form
echo '<div class="floatright">
      <form method="post" action="index.php?page=log">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="confirm" value="1">
        ' . $xoopsGTicket->getTicketHtml('protector_admin') . '
        <input type="submit" value="' . _AM_CLEARLOG . '" class="formButton">
      </form>
      </div>';

// Display export select form
echo '<div data-layout="row sm-column">

<div data-self="size-1of2 sm-full">
<div class="confirm">';
echo '<h4>' . _AM_PROTECTOR_EXPORT . '</h4>';
echo '<form action="index.php?page=log&op=export" method="post" style="display:inline;">';
echo $xoopsGTicket->getTicketHtml('protector_admin');
echo '<select name="format" class="formButton">';
echo '<option value="csv">CSV</option>';
echo '<option value="txt">Text</option>';
echo '</select> ';
echo '<input type="submit" value="' . _AM_PROTECTOR_DOWNLOAD . '" class="formButton">';
echo '</form>';
echo '</div>
</div>';

// Add import form
echo '<div data-self="size-1of2 sm-full">
<div class="danger">';
echo '<h4>' . _AM_PROTECTOR_IMPORT . '</h4>';
echo '<form action="index.php?page=log" method="post" enctype="multipart/form-data">';
echo $xoopsGTicket->getTicketHtml('protector_admin');
echo '<input type="hidden" name="action" value="import">';
echo '<input type="file" name="import_file" accept=".txt,.csv" class="formButton"> ';
echo '<input type="submit" value="' . _AM_PROTECTOR_UPLOAD . '" class="formButton">';
echo '</form>';
echo '<small>' . _AM_PROTECTOR_IMPORT_TIPS . '</small>';
echo '</div>';
echo '</div>
</div>';

// Include footer
xoops_cp_footer();