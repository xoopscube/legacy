<?php
/**
 * Protector IP Ban Manager
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Include gtickets class
require_once dirname(__DIR__) . '/class/gtickets.php';
$xoopsGTicket = new XoopsGTicket();

// Include header
xoops_cp_header();

// Display admin menu
include __DIR__ . '/mymenu.php';

// Get protector instance
$protector = protector::getInstance();

// Process form submission
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header('index.php?page=ban', 3, _NOPERM);
        exit;
    }
    
    // Update banned IPs
    $bad_ips = isset($_POST['bad_ips']) ? trim($_POST['bad_ips']) : '';
    
    // Save to file
    $file_path = $protector->get_filepath4badips();
    if ($fp = @fopen($file_path, 'w')) {
        fwrite($fp, $bad_ips);
        fclose($fp);
        redirect_header('index.php?page=ban', 3, _AM_MSG_IPFILESUPDATED);
        exit;
    } else {
        redirect_header('index.php?page=ban', 3, _AM_MSG_BADIPSCANTOPEN);
        exit;
    }
}

// Add export functionality at the top of the file (after the existing POST handler)
if (isset($_GET['op']) && $_GET['op'] === 'export') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(false, 'protector_admin')) {
        redirect_header('index.php?page=ban', 3, _NOPERM);
        exit;
    }
    
    $format = $_GET['format'] ?? 'txt';
    
    // Get current banned IPs
    $file_path = $protector->get_filepath4badips();
    $bad_ips = '';
    if (file_exists($file_path)) {
        $bad_ips = file_get_contents($file_path);
    }
    
    // Set headers for download
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);
    
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="banned_ips_' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        foreach (explode("\n", $bad_ips) as $ip) {
            if (trim($ip) !== '') {
                fputcsv($output, [trim($ip)]);
            }
        }
        fclose($output);
    } else {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="banned_ips_' . date('Ymd') . '.txt"');
        echo $bad_ips;
    }
    exit;
}

// Add import functionality after the export handler
if (isset($_POST['action']) && $_POST['action'] === 'import') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header('index.php?page=ban', 3, _AM_PROTECTOR_IMPORT_ERROR);
        exit;
    }
    
    // Check if file was uploaded
    if (isset($_FILES['import_file']) && $_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['import_file']['tmp_name'];
        $file_content = file_get_contents($tmp_name);
        
        // Process file content based on file type
        $file_ext = strtolower(pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION));
        
        if ($file_ext === 'csv') {
            // Parse CSV file
            $ips = [];
            if (($handle = fopen($tmp_name, "r")) !== FALSE) {
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if (!empty($data[0])) {
                        $ips[] = trim($data[0]);
                    }
                }
                fclose($handle);
            }
            $file_content = implode("\n", $ips);
        }
        
        // Save to file
        $file_path = $protector->get_filepath4badips();
        if ($fp = @fopen($file_path, 'w')) {
            fwrite($fp, $file_content);
            fclose($fp);
            redirect_header('index.php?page=ban', 3, _AM_MSG_IPFILESUPDATED);
            exit;
        } else {
            redirect_header('index.php?page=ban', 3, _AM_MSG_BADIPSCANTOPEN);
            exit;
        }
    } else {
        redirect_header('index.php?page=ban', 3, _AM_PROTECTOR_IMPORT_ERROR);
        exit;
    }
}

// Get current banned IPs
$file_path = $protector->get_filepath4badips();
$bad_ips = '';
if (file_exists($file_path)) {
    $bad_ips = file_get_contents($file_path);
}



// Display page title
echo '<h3>' . _AM_TH_IP_BAN . '</h3>';

// Display description
echo '<div class="tips">';
echo '<p>' . _AM_TH_BADIPS . '</p>';
echo '</div>';

// Display form
echo '<form action="index.php?page=ban" method="post">';
echo $xoopsGTicket->getTicketHtml('protector_admin');
echo '<input type="hidden" name="action" value="update">';
echo '<table class="outer" width="100%">';
echo '<tr><th colspan="2">' . _AM_TH_IP_BAN . '</th></tr>';
echo '<tr class="even"><td colspan="2">';
echo '<textarea name="bad_ips" rows="10" cols="60">' . htmlspecialchars($bad_ips) . '</textarea>';
echo '</td></tr>';
echo '<tr class="foot"><td colspan="2">';
echo '<input type="submit" value="' . _AM_PROTECTOR_UPDATE . '" class="formButton">';
echo '</td></tr>';
echo '</table>';
echo '</form>';

// Display export select form
echo '<div data-layout="row sm-column">

<div data-self="size-1of2 sm-full">
<div class="confirm">';
echo '<h4>' . _AM_PROTECTOR_EXPORT . '</h4>';
echo '<form action="index.php?page=ban&op=export" method="get" style="display:inline;">';
echo '<input type="hidden" name="page" value="ban">';
echo '<input type="hidden" name="op" value="export">';
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
echo '<form action="index.php?page=ban" method="post" enctype="multipart/form-data">';
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