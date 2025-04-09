<?php
/**
 * Get a reference to the only instance of database class and connects to DB
 * if the class has not been instantiated yet, this will also take care of that
 * @package    kernel
 * @subpackage database
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

class XoopsDatabaseFactory
{
    public function __construct()
    {
    }

    /**
     * @static
     * @staticvar   object  The only instance of database class
     * @return      object  Reference to the only instance of database class
     */
    public static function &getDatabaseConnection()
    {
        static $instance;
        if (!isset($instance)) {
            $file = XOOPS_ROOT_PATH.'/class/database/'.XOOPS_DB_TYPE.'database.php';
            require_once $file;
            /* begin DB Layer Trapping patch */
            if (defined('XOOPS_DB_ALTERNATIVE') && class_exists(XOOPS_DB_ALTERNATIVE)) {
                $class = XOOPS_DB_ALTERNATIVE;
            } elseif (!defined('XOOPS_DB_PROXY')) {
                $class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseSafe';
            } else {
                $class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseProxy';
            }
            $instance = new $class();
            $instance->setLogger(XoopsLogger::instance());
            $instance->setPrefix(XOOPS_DB_PREFIX);
            if (!$instance->connect()) {
                echo 'Unable to connect to the database.';
                header('HTTP/1.1 503 Service Temporarily Unavailable');
                trigger_error('Unable to connect to database', E_USER_ERROR);
            }
        }
        return $instance;
    }

    /**
     * Gets a reference to the only instance of database class.
     * Currently only being used within the installer.
     *
     * @static
     * @staticvar   object  The only instance of database class
     * @return      object  Reference to the only instance of database class
     */
    public static function &getDatabase()
    {
        static $database;
        if (!isset($database)) {
            $file = XOOPS_ROOT_PATH.'/class/database/'.XOOPS_DB_TYPE.'database.php';
            require_once $file;
            if (!defined('XOOPS_DB_PROXY')) {
                $class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseSafe';
            } else {
                $class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseProxy';
            }
            $database =new $class();
        }
        return $database;
    }
}
