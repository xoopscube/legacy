<?php
/**
 * Protector Admin IP Safe List
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2024 The XOOPSCube Project
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

// Add export functionality at the top of the file (after the existing POST handler)
if (isset($_GET['op']) && $_GET['op'] === 'export') {
    // Skip CSRF check for exports since it's just reading data
    
    $format = $_GET['format'] ?? 'txt';

    // Get current safe IPs
    $module_handler = xoops_getHandler('module');
    $module = $module_handler->getByDirname('protector');
    $config_handler = xoops_getHandler('config');
    $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
    $criteria->add(new Criteria('conf_name', 'reliable_ips'));
    $configs = $config_handler->getConfigs($criteria);
    $safe_ips = '';

    if (count($configs) > 0) {
        $serialized_value = $configs[0]->getVar('conf_value');
        $decoded_value = html_entity_decode($serialized_value, ENT_QUOTES);

        if (preg_match('/^a:\d+:{/', $decoded_value)) {
            $ip_array = @unserialize($decoded_value);
            if (is_array($ip_array)) {
                $safe_ips = implode("\n", $ip_array);
            }
        }
    }

    // Set headers for download
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: private', false);

    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="safe_ips_' . date('Ymd') . '.csv"');

        $output = fopen('php://output', 'w');
        foreach (explode("\n", $safe_ips) as $ip) {
            if (trim($ip) !== '') {
                fputcsv($output, [trim($ip)]);
            }
        }
        fclose($output);
    } else {
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="safe_ips_' . date('Ymd') . '.txt"');
        echo $safe_ips;
    }
    exit;
}

// Process form submission
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header('index.php?page=safe_list', 3, _NOPERM);
        exit;
    }

    // Update safe IPs
    $safe_ips_input = isset($_POST['safe_ips']) ? trim($_POST['safe_ips']) : '';

    // Convert line-by-line format to array
    $ip_array = array_filter(array_map('trim', explode("\n", $safe_ips_input)));

    // Serialize the array for storage
    $safe_ips_serialized = serialize($ip_array);

    // Save to module config
    $module_handler = xoops_getHandler('module');
    $module = $module_handler->getByDirname('protector');
    $config_handler = xoops_getHandler('config');
    $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
    $criteria->add(new Criteria('conf_name', 'reliable_ips'));
    $configs = $config_handler->getConfigs($criteria);

    if (count($configs) > 0) {
        $config = $configs[0];
        $config->setVar('conf_value', $safe_ips_serialized);
        $config_handler->insertConfig($config);
        redirect_header('index.php?action=safe_list', 3, _AM_PROTECTOR_UPDATED);
        exit;
    }
}

// Add import functionality after the export handler
if (isset($_POST['action']) && $_POST['action'] === 'import') {
    // Verify CSRF token
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header('index.php?page=safe_list', 3, _NOPERM);
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

        // Convert line-by-line format to array
        $ip_array = array_filter(array_map('trim', explode("\n", $file_content)));

        // Serialize the array for storage
        $safe_ips_serialized = serialize($ip_array);

        // Save to module config
        $module_handler = xoops_getHandler('module');
        $module = $module_handler->getByDirname('protector');
        $config_handler = xoops_getHandler('config');
        $criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
        $criteria->add(new Criteria('conf_name', 'reliable_ips'));
        $configs = $config_handler->getConfigs($criteria);

        if (count($configs) > 0) {
            $config = $configs[0];
            $config->setVar('conf_value', $safe_ips_serialized);
            $config_handler->insertConfig($config);
            redirect_header('index.php?page=safe_list', 3, _AM_PROTECTOR_UPDATED);
            exit;
        }
    } else {
        redirect_header('index.php?page=safe_list', 3, _AM_PROTECTOR_IMPORT_ERROR);
        exit;
    }
}

// Get current safe IPs for display
$module_handler = xoops_getHandler('module');
$module = $module_handler->getByDirname('protector');
$config_handler = xoops_getHandler('config');
$criteria = new CriteriaCompo(new Criteria('conf_modid', $module->getVar('mid')));
$criteria->add(new Criteria('conf_name', 'reliable_ips'));
$configs = $config_handler->getConfigs($criteria);
$safe_ips = '';

if (count($configs) > 0) {
    $serialized_value = $configs[0]->getVar('conf_value');
    $decoded_value = html_entity_decode($serialized_value, ENT_QUOTES);

    if (preg_match('/^a:\d+:{/', $decoded_value)) {
        $ip_array = @unserialize($decoded_value);
        if (is_array($ip_array)) {
            $safe_ips = implode("\n", $ip_array);
        }
    }
}


// Display page title
echo '<h2>' . _AM_PROTECTOR_IPSAFELIST . '</h2>';

// Display description
echo '<div class="tips">';
echo '<p>' . _AM_PROTECTOR_IPSAFELISTDESC . '<br>'. _AM_PROTECTOR_IPSAFELISTFORMAT . '</p>';
echo '</div>';

// Display form
echo '<form action="index.php?page=safe_list" method="post">';
echo $xoopsGTicket->getTicketHtml('protector_admin');
echo '<input type="hidden" name="action" value="update">';
echo '<table class="outer" width="100%">';
echo '<thead><tr><th colspan="2">' . _AM_PROTECTOR_IPSAFELIST . '</th></tr></thead>';
echo '<tbody><tr class="even"><td width="50%">';
echo '<textarea name="safe_ips" rows="10" cols="60">' . htmlspecialchars($safe_ips) . '</textarea>';
echo '</td>';
echo '<td><p>While safelisting is typically managed at the server level, this process allows administrators
to specify allowed IP addresses for personalized website access from known sources.
</p>
<p>Whitelisting is a robust cybersecurity measure that, when implemented correctly, can inherently prevent many security issues.
However, it can be time-consuming and inconvenient for administrators, requiring careful implementation and ongoing maintenance.</p></td></tr></tbody>';
echo '<tfoot><tr class="foot"><td colspan="2">';
echo '<input type="submit" value="' . _AM_PROTECTOR_UPDATE . '" class="formButton">';
echo '</td></tr></tfoot>';
echo '</table>';
echo '</form>';

// Display export select form
echo '<div data-layout="row sm-column">

<div data-self="size-1of2 sm-full">
<div class="confirm">';
echo '<h4>' . _AM_PROTECTOR_EXPORT . '</h4>';

// Create separate buttons for each export format
echo '<form action="index.php" method="get" data-self="inline">
      <input type="hidden" name="page" value="safe_list">
      <input type="hidden" name="op" value="export">
      <input type="hidden" name="format" value="csv">
      <input type="submit" value="' . _AM_PROTECTOR_DOWNLOAD . ' CSV" class="formButton">
      </form>';

echo '<form action="index.php" method="get" data-self="inline">
      <input type="hidden" name="page" value="safe_list">
      <input type="hidden" name="op" value="export">
      <input type="hidden" name="format" value="txt">
      <input type="submit" value="' . _AM_PROTECTOR_DOWNLOAD . ' TXT" class="formButton">
      </form>';

echo '<p><small>' . _AM_PROTECTOR_EXPORT_TIPS . '</small></p>';
echo '</div>
</div>';


// Add import form
echo '<div data-self="size-1of2 sm-full">
<div class="danger">';
echo '<h4>' . _AM_PROTECTOR_IMPORT . '</h4>';
echo '<form action="index.php?page=safe_list" method="post" enctype="multipart/form-data">';
echo $xoopsGTicket->getTicketHtml('protector_admin');
echo '<input type="hidden" name="action" value="import">';
echo '<input type="file" name="import_file" accept=".txt,.csv" class="formButton"> ';
echo '<input type="submit" value="' . _AM_PROTECTOR_UPLOAD . '" class="formButton">';
echo '</form>';
echo '<p><small>' . _AM_PROTECTOR_IMPORT_TIPS . '</small></p>';
echo '</div>';
echo '</div>
</div>';

// Include footer
xoops_cp_footer();
