<?php

/**
 * Protector MySQL Database Handler
 *
 * This class extends the core MySQL database handler to add SQL injection protection
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Include the base database class
require_once XOOPS_ROOT_PATH . '/class/database/mysqldatabase.php';

// Create a simpler implementation that just uses the global database connection
class ProtectorMysqlDatabase
{
    public $conn;
    protected $protector;
    protected $doubtful_requests = [];
    protected $doubtful_needles = [];
    
    /**
     * Constructor
     */
    public function __construct()
    {
        global $xoopsDB;
        
        if (isset($xoopsDB) && is_object($xoopsDB)) {
            $this->conn = $xoopsDB->conn;
        }
        
        $this->initializeProtection();
    }

    /**
     * Initialize SQL injection protection
     */
    private function initializeProtection()
    {
        $this->protector = protector::getInstance();
        $this->doubtful_requests = $this->protector->getDblayertrapDoubtfuls();
        $this->doubtful_needles = ['UNION', 'SELECT', 'UPDATE', 'DELETE', 'INSERT', 'DROP', 'TRUNCATE'];
    }

    /**
     * Check if a query is potentially malicious
     */
    private function checkMaliciousQuery($sql)
    {
        foreach ($this->doubtful_requests as $request) {
            foreach ($this->doubtful_needles as $needle) {
                if (strpos($request, $needle) !== false && strpos(strtoupper($sql), $needle) !== false) {
                    $this->protector->last_error_type = 'SQL Injection';
                    $this->protector->message .= "Doubtful SQL found: {$sql}\n";
                    $this->protector->output_log('SQL Injection', 0, false, 32);
                    
                    // Call the filter
                    $ret = $this->protector->call_filter('dblayertrap_sql_check');
                    if ($ret === false) {
                        die('SQL Injection found');
                    }
                    
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Execute a query with protection
     */
    public function query($sql)
    {
        $sql4check = substr($sql, 0, 4096);
        
        // Check for malicious queries
        $this->checkMaliciousQuery($sql4check);
        
        // Execute the query using the global database
        global $xoopsDB;
        return $xoopsDB->query($sql);
    }

    /**
     * Prepare a statement with proper escaping
     */
    public function prepare($sql, $params = [])
    {
        if (empty($params)) {
            return $sql;
        }
        
        $parts = explode('?', $sql);
        $prepared = $parts[0];
        
        for ($i = 1; $i < count($parts); $i++) {
            if (isset($params[$i-1])) {
                $value = $params[$i-1];
                
                if (is_string($value)) {
                    $prepared .= "'" . mysqli_real_escape_string($this->conn, $value) . "'";
                } elseif (is_numeric($value)) {
                    $prepared .= $value;
                } elseif (is_null($value)) {
                    $prepared .= 'NULL';
                } elseif (is_bool($value)) {
                    $prepared .= $value ? '1' : '0';
                } else {
                    $prepared .= "'" . mysqli_real_escape_string($this->conn, (string)$value) . "'";
                }
            }
            
            $prepared .= $parts[$i];
        }
        
        return $prepared;
    }

    /**
     * Execute a prepared statement
     */
    public function executeStatement($sql, $params = [])
    {
        $prepared = $this->prepare($sql, $params);
        return $this->query($prepared);
    }
}
