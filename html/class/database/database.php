<?php
/**
 * Defines abstract database wrapper class
 * @package    kernel
 * @subpackage database
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

/**
 * make sure this is only included once!
 */
if (!defined('XOOPS_C_DATABASE_INCLUDED')) {
    define('XOOPS_C_DATABASE_INCLUDED', 1);


    class XoopsDatabase
    {
        /**
         * Prefix for tables in the database
         * @var string
         */
        public $prefix = '';

        /**
         * reference to a {@link XoopsLogger} object
         * @see XoopsLogger
         * @var object XoopsLogger
         */
        public $logger;

        /**
         * constructor
         *
         * will always fail, because this is an abstract class!
         */
        public function __construct()
        {
            // exit("Cannot instantiate this class directly");
        }

        /**
         * assign a {@link XoopsLogger} object to the database
         *
         * @see XoopsLogger
         * @param object $logger reference to a {@link XoopsLogger} object
         */
        public function setLogger(&$logger)
        {
            $this->logger =& $logger;
        }

        /**
         * set the prefix for tables in the database
         *
         * @param string $value table prefix
         */
        public function setPrefix($value)
        {
            $this->prefix = $value;
        }

        /**
         * attach the prefix.'_' to a given tablename
         *
         * if tablename is empty, only prefix will be returned
         *
         * @param string $tablename tablename
         * @return string prefixed tablename, just prefix if tablename is empty
         */
        public function prefix($tablename='')
        {
            if ('' != $tablename) {
                return $this->prefix .'_'. $tablename;
            } else {
                return $this->prefix;
            }
        }
    }
}


/**
 * Only for backward compatibility
 * Use instead  eg: $db = &XoopsDatabaseFactory::getDatabaseConnection();
 * @deprecated
 */
class Database
{

    public static function &getInstance()
    {
        $instance =& XoopsDatabaseFactory::getDatabaseConnection();
        return $instance;
    }
}
