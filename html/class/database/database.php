<?php
// $Id: database.php,v 1.1 2007/05/15 02:35:14 minahito Exp $
// database.php - defines abstract database wrapper class 
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
/**
 * @package     kernel
 * @subpackage  database
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * make sure this is only included once!
 */
if (!defined("XOOPS_C_DATABASE_INCLUDED")) {
    define("XOOPS_C_DATABASE_INCLUDED", 1);

/**
 * Abstract base class for Database access classes
 * 
 * @abstract
 * 
 * @author Kazumi Ono <onokazu@xoops.org>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 * 
 * @package kernel
 * @subpackage database
 */
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
        // !Fix PHP7
        public function __construct()
        //public function XoopsDatabase()
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
            if ($tablename != '') {
                return $this->prefix .'_'. $tablename;
            } else {
                return $this->prefix;
            }
        }
}
}


/**
 * Only for backward compatibility
 * 
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
