<?php 

/**
 * Database operations for Protector module
 * 
 * Handles database operations specific to the Protector module
 * such as storing CSP violation reports
 * 
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2025 The XOOPSCube Project
 * @license    GPL v2.0
 */

class ProtectorDB {

    /**
     * Insert a CSP violation report into the database
     * 
     * @param array $report The CSP report data
     * @return bool Success or failure
     */
    public function insertCSPViolation(array $report): bool
    {
        global $xoopsDB;
        
        // Check if the table exists
        $table = $xoopsDB->prefix('protector_csp_violations');
        $result = $xoopsDB->queryF("SHOW TABLES LIKE '$table'");
        if (!$result || $xoopsDB->getRowsNum($result) == 0) {
            $this->createCSPViolationsTable();
        }

        // Extract data from report with proper defaults
        $document_uri = $report['document-uri'] ?? '';
        $violated_directive = $report['violated-directive'] ?? '';
        $blocked_uri = $report['blocked-uri'] ?? '';
        $source_file = $report['source-file'] ?? null;
        $line_number = isset($report['line-number']) ? (int)$report['line-number'] : null;
        $column_number = isset($report['column-number']) ? (int)$report['column-number'] : null;
        $referrer = $report['referrer'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $created = time();

        $sql = "INSERT INTO {$table} (document_uri, violated_directive, blocked_uri, source_file,
                        line_number, column_number, referrer, user_agent, ip, created)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $xoopsDB->prepare($sql);

        if (!$stmt) {
            error_log("Protector: Failed to prepare CSP violation statement: " . $xoopsDB->error());
            return false;
        }

        // Fix the bind_param types - there are 10 parameters
        $stmt->bind_param(
            'ssssiiissi',
            $document_uri,
            $violated_directive,
            $blocked_uri,
            $source_file,
            $line_number,
            $column_number,
            $referrer,
            $user_agent,
            $ip,
            $created
        );

        $result = $stmt->execute();
        
        if (!$result) {
            error_log("Protector: Failed to insert CSP violation: " . $stmt->error);
        }
        
        return $result;
    }
    
    /**
     * Create the CSP violations table if it doesn't exist
     * 
     * @return bool Success or failure
     */
    private function createCSPViolationsTable(): bool
    {
        global $xoopsDB;
        
        $table = $xoopsDB->prefix('protector_csp_violations');
        
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `document_uri` varchar(255) NOT NULL,
            `violated_directive` varchar(255) NOT NULL,
            `blocked_uri` varchar(255) NOT NULL,
            `source_file` varchar(255) DEFAULT NULL,
            `line_number` int(10) unsigned DEFAULT NULL,
            `column_number` int(10) unsigned DEFAULT NULL,
            `referrer` varchar(255) DEFAULT NULL,
            `user_agent` varchar(255) DEFAULT NULL,
            `ip` varchar(45) NOT NULL,
            `created` int(10) unsigned NOT NULL,
            PRIMARY KEY (`id`),
            KEY `created` (`created`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
        
        $result = $xoopsDB->queryF($sql);
        
        if (!$result) {
            error_log("Protector: Failed to create CSP violations table: " . $xoopsDB->error());
        }
        
        return (bool)$result;
    }
    
    /**
     * Get CSP violations from the database
     * 
     * @param int $limit Maximum number of violations to return
     * @param int $offset Starting position
     * @return array Array of violation records
     */
    public function getCSPViolations(int $limit = 100, int $offset = 0): array
    {
        global $xoopsDB;
        
        $table = $xoopsDB->prefix('protector_csp_violations');
        $violations = [];
        
        $sql = "SELECT * FROM {$table} ORDER BY created DESC LIMIT {$offset}, {$limit}";
        $result = $xoopsDB->query($sql);
        
        if ($result) {
            while ($row = $xoopsDB->fetchArray($result)) {
                $violations[] = $row;
            }
        }
        
        return $violations;
    }
    
    /**
     * Get a single CSP violation by ID
     * 
     * @param int $id Violation ID
     * @return array|null Violation record or null if not found
     */
    public function getCSPViolation(int $id): ?array
    {
        global $xoopsDB;
        
        $table = $xoopsDB->prefix('protector_csp_violations');
        
        $sql = "SELECT * FROM {$table} WHERE id = " . (int)$id;
        $result = $xoopsDB->query($sql);
        
        if ($result && $xoopsDB->getRowsNum($result) > 0) {
            return $xoopsDB->fetchArray($result);
        }
        
        return null;
    }
    
    /**
     * Delete a CSP violation by ID
     * 
     * @param int $id Violation ID
     * @return bool Success or failure
     */
    public function deleteCSPViolation(int $id): bool
    {
        global $xoopsDB;
        
        $table = $xoopsDB->prefix('protector_csp_violations');
        
        $sql = "DELETE FROM {$table} WHERE id = " . (int)$id;
        $result = $xoopsDB->queryF($sql);
        
        return (bool)$result;
    }
    
    /**
     * Clear all CSP violations
     * 
     * @return bool Success or failure
     */
    public function clearCSPViolations(): bool
    {
        global $xoopsDB;
        
        $table = $xoopsDB->prefix('protector_csp_violations');
        
        $sql = "TRUNCATE TABLE {$table}";
        $result = $xoopsDB->queryF($sql);
        
        return (bool)$result;
    }
}