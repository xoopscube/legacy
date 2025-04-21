<?php
/**
 * Protector Database prefix manager and backup
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster, XCL PHP8
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// Display admin menu
include __DIR__ . '/mymenu.php';

include_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

// Check if user has admin rights
if (!is_object($xoopsUser) || !$xoopsUser->isAdmin()) {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);
}

// Get database instance
$db = XoopsDatabaseFactory::getDatabaseConnection();

// Get current prefix
$current_prefix = XOOPS_DB_PREFIX;

// Get Ticket
$ticket = $xoopsGTicket->issue('protector_admin');

// Process form submissions
if (isset($_POST['prefix']) && isset($_POST['action'])) {
    // Validate prefix (alphanumeric and underscore only)
    if (preg_match('/[^0-9A-Za-z_]/', $_POST['prefix'])) {
        redirect_header('index.php?page=prefix_manager', 3, 'Invalid prefix: Only alphanumeric characters and underscores are allowed');
        exit;
    }

    // Ticket check
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
        exit;
    }

    $prefix = $_POST['prefix'];
    $action = $_POST['action'];

    // Handle different actions
    switch ($action) {
        case 'change_prefix':
            // Redirect to the prefix change confirmation page
            redirect_header('index.php?page=prefix_manager&op=confirm_change&prefix=' . urlencode($prefix), 0, '');
            break;
            
        case 'backup_sql':
            // Redirect to backup process
            redirect_header('index.php?page=prefix_manager&op=backup&format=sql&prefix=' . urlencode($prefix), 0, '');
            break;
            
        case 'backup_zip':
            // Redirect to backup process
            redirect_header('index.php?page=prefix_manager&op=backup&format=zip&prefix=' . urlencode($prefix), 0, '');
            break;
            
        case 'backup_tgz':
            // Redirect to backup process
            redirect_header('index.php?page=prefix_manager&op=backup&format=tgz&prefix=' . urlencode($prefix), 0, '');
            break;
    }
    exit;
}

// Handle operations
$op = isset($_GET['op']) ? $_GET['op'] : '';

switch ($op) {
    case 'confirm_change':
        // Show confirmation page for prefix change
        confirm_prefix_change();
        break;
        
    case 'execute_change':
        // Execute the prefix change
        execute_prefix_change();
        break;
        
    case 'backup':
        // Execute backup
        execute_backup();
        break;
        
    case 'clear_logs':
        // Clear backup logs
        unset($_SESSION['backup_logs']);
        redirect_header('index.php?page=prefix_manager', 0, '');
        break;
        
    default:
        // Show main interface
        prefix_manager();
        break;
}



    // Main interface function - xcl v2.5.0 renamed from show_main_interface to prefix_manager
function prefix_manager() {
    global $db, $current_prefix, $ticket;
    


    echo '<h2><svg xmlns="http://www.w3.org/2000/svg" focusable="false" width="1em" height="1em" viewBox="0 0 24 24"><path d="M3 1h16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1m0 8h16a1 1 0 0 1 1 1v.67l-2.5-1.11l-6.5 2.88V15H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1m0 8h8c.06 2.25 1 4.4 2.46 6H3a1 1 0 0 1-1-1v-4a1 1 0 0 1 1-1M8 5h1V3H8v2m0 8h1v-2H8v2m0 8h1v-2H8v2M4 3v2h2V3H4m0 8v2h2v-2H4m0 8v2h2v-2H4m13.5-7l4.5 2v3c0 2.78-1.92 5.37-4.5 6c-2.58-.63-4.5-3.22-4.5-6v-3l4.5-2m0 1.94L15 15.06v2.66c0 1.54 1.07 2.98 2.5 3.34v-7.12z" fill="currentColor"/></svg> ' . _AM_H3_PREFIXMANAGER . '</h2>';
    
    // Display current prefix info
    echo '<div class="tips">' . _AM_TXT_PREFIXMANAGER . '</div>';


    // Current prefix information
    echo '<h3>' . _AM_H3_CURRENTPREFIX . '</h3>';
    echo '<div class="success"><strong>' . _AM_LABEL_CURRENTPREFIX . ':</strong> ' . htmlspecialchars($current_prefix) . '</div>';
    
    echo '<div class="danger">' . _AM_MSG_CHANGEPREFIX_WARNING . '</div>';


    // Todo: Get table count - FIX: Handle query failure
/*     $result = $db->query("SHOW TABLES LIKE '" . $current_prefix . "\_%'");
    $table_count = ($result !== false) ? $db->getRowsNum($result) : 0;
    echo '<div><strong>' . _AM_LABEL_TABLECOUNT . ':</strong> ' . $table_count . '</div>'; */

     echo '<div data-layout="row sm-column">';
    echo '<div data-self="size-1of2 sm-full">';
    
    // Change prefix form
    echo '<h3>' . _AM_H3_CHANGEPREFIX . '</h3>';

    echo '<div class="tips">';
    echo '<p>' . _AM_TXT_PREFIXPATTERN . '</p>';
    echo '<form action="index.php?page=prefix_manager" method="post">';
    echo '<input type="hidden" name="action" value="change_prefix">';
    echo '<input type="hidden" name="XOOPS_G_TICKET" value="' . $ticket . '">';
    echo '<p>';
    echo '<label for="prefix"><strong>' . _AM_LABEL_NEWPREFIX . ':</strong></label> ';
    echo '<input type="text" name="prefix" id="prefix" value="' . htmlspecialchars($current_prefix) . '" pattern="[a-zA-Z0-9_]+" required>';
    echo '</p>';
    echo '<p>';
    echo '<input type="submit" class="button primary" value="' . _AM_BUTTON_CHANGEPREFIX . '">';
    echo '</p>';
    echo '</form>';
    echo '</div>';

    echo '</div>';
    echo '<div data-self="size-1of2 sm-full">';

    // Backup database form
    echo '<h3>' . _AM_H3_BACKUPDB . '</h3>';
    echo '<div class="tips">'; 
    echo '<p>' . _AM_TXT_BACKUPDB . '</p>';
    // Changed redirect form with target="_blank" to open in new tab
    echo '<form action="index.php" method="get" target="_blank">';
    echo '<input type="hidden" name="page" value="prefix_manager">';
    echo '<input type="hidden" name="op" value="backup">';
    
    echo '<p>';
    echo '<label for="backup_prefix"><strong>' . _AM_LABEL_PREFIXTOBACKUP . ':</strong></label> ';
    echo '<input type="text" name="prefix" id="backup_prefix" value="' . htmlspecialchars($current_prefix) . '" pattern="[a-zA-Z0-9_]+" required>';
    echo '</p>';
    
    echo '<div style="margin: 10px 0;">';
    echo '<label><input type="radio" name="format" value="sql" checked> SQL</label> ';
    if (function_exists('gzcompress')) {
        echo '<label><input type="radio" name="format" value="zip"> ZIP</label> ';
    }
    
    if (function_exists('gzencode')) {
        echo '<label><input type="radio" name="format" value="tgz"> TGZ</label> ';
    }
    echo '</div>';
    
    echo '<p>';
    echo '<input type="submit" class="button" value="' . _AM_H3_BACKUPDB . '">';
    echo '</p>';
    
    echo '</form>';
    echo '</div>';
    
    echo '</div>';
    echo '</div>';

    // Display backup logs
    show_backup_logs();
    
    xoops_cp_footer();
}

// Confirmation page for prefix change
function confirm_prefix_change() {
    global $db, $current_prefix, $ticket;
    
    // Get the new prefix
    $new_prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
    
    if (empty($new_prefix)) {
        redirect_header('index.php?page=prefix_manager', 3, 'No prefix specified');
        exit;
    }
    
    echo '<h2>' . _AM_H3_CONFIRMCHANGE . '</h2>';
    
    echo '<div class="ui-card-full" style="margin-top: 20px;">';
    
    echo '<div class="warning" style="margin-bottom: 20px;">';
    echo '<h3>' . _AM_MSG_CONFIRMCHANGE_TITLE . '</h3>';
    echo '<p>' . _AM_MSG_CONFIRMCHANGE_DESC . '</p>';
    echo '</div>';
    
    echo '<div style="margin-bottom: 20px;">';
    echo '<strong>' . _AM_LABEL_CURRENTPREFIX . ':</strong> ' . htmlspecialchars($current_prefix) . '<br>';
    echo '<strong>' . _AM_LABEL_NEWPREFIX . ':</strong> ' . htmlspecialchars($new_prefix) . '<br>';
    
    // Get table count - FIX: Handle query failure
    $result = $db->query("SHOW TABLES LIKE '" . $db->quoteString($current_prefix) . "\_%'");
    $table_count = ($result !== false) ? $db->getRowsNum($result) : 0;
    echo '<strong>' . _AM_LABEL_TABLECOUNT . ':</strong> ' . $table_count;
    echo '</div>';
    
    // Show tables that will be affected
    echo '<div style="margin-bottom: 20px;">';
    echo '<h3>' . _AM_H3_AFFECTEDTABLES . '</h3>';
    
    echo '<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">';
    
    // FIX: Handle query failure
    $result = $db->query("SHOW TABLES LIKE '" . $db->quoteString($current_prefix) . "\_%'");
    if ($result !== false && $db->getRowsNum($result) > 0) {
        echo '<ul>';
        while ($row = $db->fetchRow($result)) {
            $old_table = $row[0];
            $new_table = $new_prefix . substr($old_table, strlen($current_prefix));
            echo '<li>' . htmlspecialchars($old_table) . ' → ' . htmlspecialchars($new_table) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No tables found with the current prefix.</p>';
    }
    
    echo '</div>';
    echo '</div>';
    
    // Confirmation buttons
    echo '<div style="margin-bottom: 20px;">';
    // Fix: Correct the form action URL syntax (change ? to &)
    echo '<form action="index.php?page=prefix_manager&op=execute_change" method="post">';
    echo '<input type="hidden" name="XOOPS_G_TICKET" value="' . $ticket . '">';
    echo '<input type="hidden" name="new_prefix" value="' . htmlspecialchars($new_prefix) . '">';
    echo '<input type="submit" class="button primary" value="' . _AM_BUTTON_EXECUTE_CHANGE . '"> ';
    echo '<a href="index.php?page=prefix_manager" class="button">' . _AM_BUTTON_CANCEL . '</a>';
    echo '</form>';
    echo '</div>';
    
    echo '</div>'; // End ui-card-full
    
    xoops_cp_footer();
}

// Execute prefix change
function execute_prefix_change() {
    global $db, $xoopsGTicket, $xoopsLogger;
    
    // Check ticket
    if (!$xoopsGTicket->check(true, 'protector_admin')) {
        redirect_header(XOOPS_URL . '/', 3, $xoopsGTicket->getErrors());
        exit;
    }
    
    $new_prefix = isset($_POST['new_prefix']) ? $_POST['new_prefix'] : '';
    $old_prefix = XOOPS_DB_PREFIX;
    
    if (empty($new_prefix)) {
        redirect_header('index.php?page=prefix_manager', 3, 'No prefix specified');
        exit;
    }
    
    // Start transaction if supported
    $db->queryF('START TRANSACTION');
    
    // Get all tables with the old prefix - FIX: Handle query failure
    $result = $db->query("SHOW TABLES LIKE '" . $db->quoteString($old_prefix) . "\_%'");
    
    $success = true;
    $error_messages = [];
    
    // Process each table
    if ($result !== false && $db->getRowsNum($result) > 0) {
        while ($row = $db->fetchRow($result)) {
            $old_table = $row[0];
            $new_table = $new_prefix . substr($old_table, strlen($old_prefix));
            
            // Create new table with same structure
            $crs = $db->queryF('SHOW CREATE TABLE `' . $db->quoteString($old_table) . '`');
            if (!$crs || $db->getRowsNum($crs) == 0) {
                $error_messages[] = "Error: SHOW CREATE TABLE ($old_table)";
                $success = false;
                continue;
            }
            
            $row_create = $db->fetchArray($crs);
            $create_sql = preg_replace("/^CREATE TABLE `" . preg_quote($old_table, '/') . "`/", 
                                       "CREATE TABLE `" . $db->quoteString($new_table) . "`", 
                                       $row_create['Create Table'], 1);
            
            $crs = $db->queryF($create_sql);
            if (!$crs) {
                $error_messages[] = "Error: CREATE TABLE ($new_table): " . $db->error();
                $success = false;
                continue;
            }
            
            // Copy data
            $irs = $db->queryF("INSERT INTO `" . $db->quoteString($new_table) . "` SELECT * FROM `" . $db->quoteString($old_table) . "`");
            if (!$irs) {
                $error_messages[] = "Error: INSERT INTO ($new_table): " . $db->error();
                $success = false;
                continue;
            }
        }
    } else {
        $error_messages[] = "Error: No tables found with the current prefix.";
        $success = false;
    }
    
    // Commit or rollback based on success
    if ($success) {
        $db->queryF('COMMIT');
        $_SESSION['protector_logger'] = isset($xoopsLogger) ? $xoopsLogger->dumpQueries() : '';
        redirect_header('index.php?page=prefix_manager', 1, _AM_MSG_DBUPDATED);
    } else {
        $db->queryF('ROLLBACK');
        $error_message = implode('<br>', $error_messages);
        redirect_header('prefix_manager.php', 3, "Errors occurred: <br>" . $error_message);
    }
    
    exit;
}

// Execute backup function
function execute_backup() {
    global $db, $xoopsDB;
    
    // Get parameters
    $prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
    $format = isset($_GET['format']) ? $_GET['format'] : 'sql';
    
    if (empty($prefix)) {
        redirect_header('index.php?page=prefix_manager', 3, 'No prefix specified');
        exit;
    }
    
    // Validate prefix
    if (preg_match('/[^0-9A-Za-z_]/', $prefix)) {
        redirect_header('index.php?page=prefix_manager', 3, 'Invalid prefix');
        exit;
    }

        // Log the backup attempt before starting
        $_SESSION['last_backup'] = [
            'prefix' => $prefix,
            'format' => $format,
            'time' => time()
        ];
    
    // Get database name from configuration
    $db_name = XOOPS_DB_NAME;
    
    // Create SQL file name
    $sqlfile_name = $prefix . '_' . date('YmdHis') . '.sql';
    

// Handle different formats
switch ($format) {

    case 'zip':
        // Use PHP's built-in ZipArchive instead of XoopsZipDownloader
        if (!class_exists('ZipArchive')) {
            log_backup_operation($prefix, 'zip', false, 'ZipArchive class not available');
            redirect_header('index.php?page=prefix_manager', 3, 'ZipArchive class not available');
            exit;
        }
        
        // Create temporary SQL file
        $temp_file = tempnam(sys_get_temp_dir(), 'xcl_sql_');
        $fp = fopen($temp_file, 'w');
        
        if (!$fp) {
            log_backup_operation($prefix, 'zip', false, 'Failed to create temporary file');
            redirect_header('index.php?page=prefix_manager', 3, 'Failed to create temporary file');
            exit;
        }
        
        // Write SQL content to temporary file
        fwrite($fp, "-- XoopsCube Legacy Database Backup\n");
        fwrite($fp, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($fp, "-- Database: " . $db_name . "\n");
        fwrite($fp, "-- Prefix: " . $prefix . "\n\n");
        
        // Use the XoopsDB object directly to get tables
        $tables = [];
        $sql = "SHOW TABLES LIKE '" . $prefix . "\_%'";
        $result = $xoopsDB->queryF($sql);
        
        if (!$result) {
            fwrite($fp, "-- Error: Failed to get tables: " . $xoopsDB->error() . "\n\n");
        } else {
            while ($myrow = $xoopsDB->fetchRow($result)) {
                $tables[] = $myrow[0];
            }
            
            if (count($tables) === 0) {
                fwrite($fp, "-- No tables found with prefix: $prefix\n\n");
            } else {
                // Process each table
                foreach ($tables as $table) {
                    // Get table structure
                    $result = $xoopsDB->queryF("SHOW CREATE TABLE `$table`");
                    if (!$result) {
                        fwrite($fp, "-- Error: Failed to get table structure for $table: " . $xoopsDB->error() . "\n\n");
                        continue;
                    }
                    
                    $row = $xoopsDB->fetchRow($result);
                    $create_sql = $row[1];
                    
                    // Write table structure
                    fwrite($fp, "-- Table structure for table `$table`\n");
                    fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
                    fwrite($fp, "$create_sql;\n\n");
                    
                    // Get table data
                    $result = $xoopsDB->queryF("SELECT * FROM `$table`");
                    if (!$result) {
                        fwrite($fp, "-- Error: Failed to get data for $table: " . $xoopsDB->error() . "\n\n");
                        continue;
                    }
                    
                    $num_rows = $xoopsDB->getRowsNum($result);
                    $num_fields = $xoopsDB->getFieldsNum($result);
                    
                    if ($num_rows > 0) {
                        // Get column information
                        $field_result = $xoopsDB->queryF("SHOW COLUMNS FROM `$table`");
                        $columns = [];
                        while ($field_row = $xoopsDB->fetchArray($field_result)) {
                            $columns[] = $field_row['Field'];
                        }
                        
                        fwrite($fp, "-- Dumping data for table `$table`\n");
                        fwrite($fp, "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES\n");
                        
                        $row_index = 0;
                        while ($row = $xoopsDB->fetchRow($result)) {
                            $values = [];
                            foreach ($row as $value) {
                                if (is_null($value)) {
                                    $values[] = "NULL";
                                } elseif (is_numeric($value)) {
                                    $values[] = $value;
                                } else {
                                    $values[] = "'" . addslashes($value) . "'";
                                }
                            }
                            
                            fwrite($fp, "(" . implode(", ", $values) . ")");
                            
                            if (++$row_index < $num_rows) {
                                fwrite($fp, ",\n");
                            } else {
                                fwrite($fp, ";\n\n");
                            }
                        }
                    } else {
                        fwrite($fp, "-- Table `$table` has no data\n\n");
                    }
                }
            }
        }
        
        fclose($fp);
        
        // Create ZIP file
        $zipfile_name = $prefix . '_' . date('YmdHis') . '.zip';
        $zip_path = tempnam(sys_get_temp_dir(), 'xcl_zip_');
        unlink($zip_path); // Remove the temp file as ZipArchive needs to create it
        
        $zip = new ZipArchive();
        if ($zip->open($zip_path, ZipArchive::CREATE) !== TRUE) {
            log_backup_operation($prefix, 'zip', false, 'Failed to create ZIP archive');
            @unlink($temp_file);
            redirect_header('index.php?page=prefix_manager', 3, 'Failed to create ZIP archive');
            exit;
        }
        
        // Add the SQL file to the ZIP
        $zip->addFile($temp_file, $sqlfile_name);
        $zip->close();
        
        // Log successful backup before sending file
        log_backup_operation($prefix, 'ZIP', true);

        // Make sure no output has been sent before
        if (headers_sent($filename, $linenum)) {
            @unlink($temp_file);
            @unlink($zip_path);
            die("Headers already sent in $filename on line $linenum. Cannot download file.");
        }
        
        // Clear any previous output
        ob_clean();
        flush();

        // Set headers for download
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipfile_name . '"');
        header('Content-Length: ' . filesize($zip_path));
        header('Content-Transfer-Encoding: binary');
        
        // Send the file
        readfile($zip_path);
        
        // Clean up temporary files
        @unlink($temp_file);
        @unlink($zip_path);
        
    // Make sure to exit immediately after sending the file
    exit;
            
    case 'tgz':
        // Include required classes
        include_once XOOPS_ROOT_PATH.'/class/tardownloader.php';

        // Check if the class exists
        if (!class_exists('XoopsTarDownloader')) {
            log_backup_operation($prefix, 'tgz', false, 'XoopsTarDownloader class not available');
            redirect_header('index.php?page=prefix_manager', 3, 'XoopsTarDownloader class not available');
            exit;
        }
                // Create temporary SQL file
                $temp_file = tempnam(sys_get_temp_dir(), 'xcl_sql_');
                $fp = fopen($temp_file, 'w');
                
                if (!$fp) {
                    log_backup_operation($prefix, 'tgz', false, 'Failed to create temporary file');
                    redirect_header('index.php?page=prefix_manager', 3, 'Failed to create temporary file');
                    exit;
                }
                
                // Write SQL content to temporary file
                fwrite($fp, "-- XoopsCube Legacy Database Backup\n");
                fwrite($fp, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
                fwrite($fp, "-- Database: " . $db_name . "\n");
                fwrite($fp, "-- Prefix: " . $prefix . "\n\n");
                
                // Use the XoopsDB object directly to get tables
                $tables = [];
                $sql = "SHOW TABLES LIKE '" . $prefix . "\_%'";
                $result = $xoopsDB->queryF($sql);
                
                if (!$result) {
                    fwrite($fp, "-- Error: Failed to get tables: " . $xoopsDB->error() . "\n\n");
                } else {
                    while ($myrow = $xoopsDB->fetchRow($result)) {
                        $tables[] = $myrow[0];
                    }
                    
                    if (count($tables) === 0) {
                        fwrite($fp, "-- No tables found with prefix: $prefix\n\n");
                    } else {
                        // Process each table
                        foreach ($tables as $table) {
                            // Get table structure
                            $result = $xoopsDB->queryF("SHOW CREATE TABLE `$table`");
                            if (!$result) {
                                fwrite($fp, "-- Error: Failed to get table structure for $table: " . $xoopsDB->error() . "\n\n");
                                continue;
                            }
                            
                            $row = $xoopsDB->fetchRow($result);
                            $create_sql = $row[1];
                            
                            // Write table structure
                            fwrite($fp, "-- Table structure for table `$table`\n");
                            fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");
                            fwrite($fp, "$create_sql;\n\n");
                            
                            // Get table data
                            $result = $xoopsDB->queryF("SELECT * FROM `$table`");
                            if (!$result) {
                                fwrite($fp, "-- Error: Failed to get data for $table: " . $xoopsDB->error() . "\n\n");
                                continue;
                            }
                            
                            $num_rows = $xoopsDB->getRowsNum($result);
                            $num_fields = $xoopsDB->getFieldsNum($result);
                            
                            if ($num_rows > 0) {
                                // Get column information
                                $field_result = $xoopsDB->queryF("SHOW COLUMNS FROM `$table`");
                                $columns = [];
                                while ($field_row = $xoopsDB->fetchArray($field_result)) {
                                    $columns[] = $field_row['Field'];
                                }
                                
                                fwrite($fp, "-- Dumping data for table `$table`\n");
                                fwrite($fp, "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES\n");
                                
                                $row_index = 0;
                                while ($row = $xoopsDB->fetchRow($result)) {
                                    $values = [];
                                    foreach ($row as $value) {
                                        if (is_null($value)) {
                                            $values[] = "NULL";
                                        } elseif (is_numeric($value)) {
                                            $values[] = $value;
                                        } else {
                                            $values[] = "'" . addslashes($value) . "'";
                                        }
                                    }
                                    
                                    fwrite($fp, "(" . implode(", ", $values) . ")");
                                    
                                    if (++$row_index < $num_rows) {
                                        fwrite($fp, ",\n");
                                    } else {
                                        fwrite($fp, ";\n\n");
                                    }
                                }
                            } else {
                                fwrite($fp, "-- Table `$table` has no data\n\n");
                            }
                        }
                    }
                }
                
                    // Make sure to close the file before using it
        fclose($fp);

        // Log successful backup before sending file
        log_backup_operation($prefix, 'TGZ', true);

        // Create TGZ file
        $tgzfile_name = $prefix . '_' . date('YmdHis') . '.tar.gz';
        
        // Create the downloader
        $downloader = new XoopsTarDownloader();

        // Add the SQL file to the archive
        $downloader->addFile($temp_file, $sqlfile_name);
                
        // Send the file - don't pass additional parameters that might cause issues
        $downloader->download($tgzfile_name);
                
        // Clean up temporary file - this won't execute until after download completes
        @unlink($temp_file);
            
    // Exit after sending the file
    exit;
            
    case 'sql':
    default:
        // Set headers for SQL download
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: text/plain');
        header('Content-Disposition: attachment; filename="' . $sqlfile_name . '"');
        
        // Log successful backup before sending content
        log_backup_operation($prefix, 'SQL', true);

        // Output SQL header
        echo "-- XoopsCube Legacy Database Backup\n";
        echo "-- Generated: " . date('Y-m-d H:i:s') . "\n";
        echo "-- Database: " . $db_name . "\n";
        echo "-- Prefix: " . $prefix . "\n\n";
        
        // Use the XoopsDB object directly to get tables
        $tables = [];
        $sql = "SHOW TABLES LIKE '" . $prefix . "\_%'";
        $result = $xoopsDB->queryF($sql);
        
        if (!$result) {
            echo "-- Error: Failed to get tables: " . $xoopsDB->error() . "\n\n";
            exit;
        }
        
        while ($myrow = $xoopsDB->fetchRow($result)) {
            $tables[] = $myrow[0];
        }
        
        $total_tables = count($tables);
        
        if ($total_tables === 0) {
            echo "-- No tables found with prefix: $prefix\n\n";
            exit;
        }
        
        // Process each table
        foreach ($tables as $table) {
            // Get table structure
            $result = $xoopsDB->queryF("SHOW CREATE TABLE `$table`");
            if (!$result) {
                echo "-- Error: Failed to get table structure for $table: " . $xoopsDB->error() . "\n\n";
                continue;
            }
            
            $row = $xoopsDB->fetchRow($result);
            $create_sql = $row[1];
            
            // Output table structure
            echo "-- Table structure for table `$table`\n";
            echo "DROP TABLE IF EXISTS `$table`;\n";
            echo "$create_sql;\n\n";
            
            // Get table data
            $result = $xoopsDB->queryF("SELECT * FROM `$table`");
            if (!$result) {
                echo "-- Error: Failed to get data for $table: " . $xoopsDB->error() . "\n\n";
                continue;
            }
            
            $num_rows = $xoopsDB->getRowsNum($result);
            $num_fields = $xoopsDB->getFieldsNum($result);
            
            if ($num_rows > 0) {
                // Get column information
                $field_result = $xoopsDB->queryF("SHOW COLUMNS FROM `$table`");
                $columns = [];
                while ($field_row = $xoopsDB->fetchArray($field_result)) {
                    $columns[] = $field_row['Field'];
                }
                
                echo "-- Dumping data for table `$table`\n";
                echo "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES\n";
                
                $row_index = 0;
                while ($row = $xoopsDB->fetchRow($result)) {
                    $values = [];
                    foreach ($row as $value) {
                        if (is_null($value)) {
                            $values[] = "NULL";
                        } elseif (is_numeric($value)) {
                            $values[] = $value;
                        } else {
                            $values[] = "'" . addslashes($value) . "'";
                        }
                    }
                    
                    echo "(" . implode(", ", $values) . ")";
                    
                    if (++$row_index < $num_rows) {
                        echo ",\n";
                    } else {
                        echo ";\n\n";
                    }
                }
            } else {
                echo "-- Table `$table` has no data\n\n";
            }
        }
        
        // Log successful backup
        log_backup_operation($prefix, 'SQL', true);
        
        exit;
    }
}

/**
 * Track backup progress
 */
function track_backup_progress($current, $total, $message = '') {
    // Store progress in session
    $_SESSION['backup_progress'] = [
        'current' => $current,
        'total' => $total,
        'message' => $message,
        'timestamp' => time()
    ];
    
    // If this is an AJAX request, return progress
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        header('Content-Type: application/json');
        echo json_encode($_SESSION['backup_progress']);
        exit;
    }
}

/**
 * Log backup operation
 */
function log_backup_operation($prefix, $format, $success, $error_message = '') {
    // Get current logs
    $logs = isset($_SESSION['backup_logs']) ? $_SESSION['backup_logs'] : [];
    
    // Add new log entry
    $logs[] = [
        'timestamp' => time(),
        'prefix' => $prefix,
        'format' => $format,
        'success' => $success,
        'error' => $error_message
    ];
    
    // Keep only the last 10 logs
    if (count($logs) > 10) {
        array_shift($logs);
    }
    
    // Save logs to session
    $_SESSION['backup_logs'] = $logs;
    
    // If this is an AJAX request, return success status
    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        header('Content-Type: application/json');
        echo json_encode(['success' => $success, 'error' => $error_message]);
        exit;
    }
}

/**
 * Show backup logs
 */
function show_backup_logs() {
    $logs = isset($_SESSION['backup_logs']) ? $_SESSION['backup_logs'] : [];
    
    if (empty($logs)) {
        return;
    }
    
    echo '<hr /><h3>' . _AM_H3_BACKUPLOGS . '</h3>';
    
    echo '<table class="outer" width="100%">';
    echo '<thead><tr>';
    echo '<th>' . _AM_TH_DATETIME . '</th>';
    echo '<th>' . _AM_TH_TIMESTAMP . '</th>';
    echo '<th>' . _AM_TH_TYPE . '</th>';
    echo '<th>' . _AM_TH_STATUS . '</th>';
    echo '<th>' . _AM_TH_MESSAGE . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach (array_reverse($logs) as $log) {
        
        echo '<tr class="list_center">';
        echo '<td>' . date('Y-m-d H:i:s', $log['timestamp']) . '</td>';
        echo '<td>' . htmlspecialchars($log['prefix']) . '</td>';
        echo '<td>' . strtoupper(htmlspecialchars($log['format'])) . '</td>';
        echo '<td>' . ($log['success'] ? '<span style="color: green;">Success</span>' : '<span style="color: red;">Failed</span>') . '</td>';
        echo '<td>' . ($log['success'] ? '' : htmlspecialchars($log['error'])) . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '<tfoot><tr><td colspan="5">';
        // Add Clear Logs button
        echo '<form action="index.php" method="get">';
        echo '<input type="hidden" name="page" value="prefix_manager">';
        echo '<input type="hidden" name="op" value="clear_logs">';
        echo '<input type="submit" class="button" value="Clear Logs">';
        echo '</form>';
    echo '</td></tr></tfoot>';
    echo '</table>';

}
