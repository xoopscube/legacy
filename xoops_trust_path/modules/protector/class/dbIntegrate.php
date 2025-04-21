<?php
/**
 * Database Integration Class for Protector
 *
 * @package    Protector
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

class protectorDbIntegrate {
    private $conn;
    
    /**
     * Constructor
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Get field flags
     */
    public function fieldFlags($result, $offset) {
        if (function_exists('mysqli_fetch_field_direct')) {
            $field = mysqli_fetch_field_direct($result, $offset);
            if (!$field) {
                throw new Exception("Failed to fetch field at offset $offset");
            }
            return $this->fieldFlagsToString($field->flags);
        } else {
            throw new Exception('mysql_field_flags function is deprecated. Use mysqli_fetch_field_direct instead.');
        }
    }
    
    /**
     * Convert field flags to string
     */
    private function fieldFlagsToString($flags_num) {
        $flags = [];
        
        if ($flags_num & MYSQLI_NOT_NULL_FLAG) {
            $flags[] = 'NOT_NULL';
        }
        
        if ($flags_num & MYSQLI_PRI_KEY_FLAG) {
            $flags[] = 'PRIMARY_KEY';
        }
        
        if ($flags_num & MYSQLI_UNIQUE_KEY_FLAG) {
            $flags[] = 'UNIQUE_KEY';
        }
        
        if ($flags_num & MYSQLI_MULTIPLE_KEY_FLAG) {
            $flags[] = 'MULTIPLE_KEY';
        }
        
        if ($flags_num & MYSQLI_BLOB_FLAG) {
            $flags[] = 'BLOB';
        }
        
        if ($flags_num & MYSQLI_UNSIGNED_FLAG) {
            $flags[] = 'UNSIGNED';
        }
        
        if ($flags_num & MYSQLI_ZEROFILL_FLAG) {
            $flags[] = 'ZEROFILL';
        }
        
        if ($flags_num & MYSQLI_AUTO_INCREMENT_FLAG) {
            $flags[] = 'AUTO_INCREMENT';
        }
        
        if ($flags_num & MYSQLI_TIMESTAMP_FLAG) {
            $flags[] = 'TIMESTAMP';
        }
        
        if ($flags_num & MYSQLI_SET_FLAG) {
            $flags[] = 'SET';
        }
        
        if ($flags_num & MYSQLI_NUM_FLAG) {
            $flags[] = 'NUM';
        }
        
        if ($flags_num & MYSQLI_PART_KEY_FLAG) {
            $flags[] = 'PART_KEY';
        }
        
        if ($flags_num & MYSQLI_GROUP_FLAG) {
            $flags[] = 'GROUP';
        }
        
        if ($flags_num & MYSQLI_ENUM_FLAG) {
            $flags[] = 'ENUM';
        }
        
        if ($flags_num & MYSQLI_BINARY_FLAG) {
            $flags[] = 'BINARY';
        }
        
        return implode(' ', $flags);
    }
    
    /**
     * Fetch field
     */
    public function fetchField($result, $offset) {
        if (function_exists('mysqli_fetch_field_direct')) {
            $field = mysqli_fetch_field_direct($result, $offset);
            if (!$field) {
                throw new Exception("Failed to fetch field at offset $offset");
            }
            return $field;
        } else {
            throw new Exception('mysql_fetch_field function is deprecated. Use mysqli_fetch_field_direct instead.');
        }
    }
    
    /**
     * Get error message
     */
    public function error() {
        if (function_exists('mysqli_error')) {
            return mysqli_error($this->conn);
        } else {
            throw new Exception('mysql_error function is deprecated. Use mysqli_error instead.');
        }
    }
    
    /**
     * Quote string for SQL
     */
    public function quoteString($string) {
        if (function_exists('mysqli_real_escape_string')) {
            return mysqli_real_escape_string($this->conn, $string);
        } else {
            throw new Exception('mysql_real_escape_string function is deprecated. Use mysqli_real_escape_string instead.');
        }
    }
    
    /**
     * Get tables with prefix
     * 
     * @param string $prefix Table prefix
     * @param string $dbName Database name
     * @return array Array of table names
     */
    public function getTablesWithPrefix($prefix, $dbName) {
        $tables = [];
        $escapedPrefix = $this->quoteString($prefix . '_');
        
        // Try different query approaches based on what's available
        if (function_exists('mysqli_query')) {
            // Method 1: Direct query with database name
            $sql = "SHOW TABLES FROM `$dbName` LIKE '$escapedPrefix%'";
            $result = mysqli_query($this->conn, $sql);
            
            if ($result) {
                while ($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
                mysqli_free_result($result);
                return $tables;
            }
            
            // Method 2: Use information_schema
            $sql = "SELECT TABLE_NAME FROM information_schema.TABLES 
                   WHERE TABLE_SCHEMA = '$dbName' 
                   AND TABLE_NAME LIKE '$escapedPrefix%'";
            $result = mysqli_query($this->conn, $sql);
            
            if ($result) {
                while ($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
                mysqli_free_result($result);
                return $tables;
            }
        } else {
            // Legacy mysql approach
            $sql = "SHOW TABLES FROM `$dbName` LIKE '$escapedPrefix%'";
            $result = mysqli_query($this->conn, $sql);
            
            if ($result) {
                while ($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
                mysqli_free_result($result);
                return $tables;
            }
        }
        
        return $tables;
    }
}
