<?php
class XoopsDatabaseFactory
{

	function XoopsDatabaseFactory()
	{
	}

	/**
	 * Get a reference to the only instance of database class and connects to DB
     * 
     * if the class has not been instantiated yet, this will also take 
     * care of that
	 * 
     * @static
     * @staticvar   object  The only instance of database class
     * @return      object  Reference to the only instance of database class
	 */
	function &getDatabaseConnection()
	{
		static $instance;
		if (!isset($instance)) {
			$file = XOOPS_ROOT_PATH.'/class/database/'.XOOPS_DB_TYPE.'database.php';
			require_once $file;
			/* patch from */
			if ( defined('XOOPS_DB_ALTERNATIVE') && class_exists( XOOPS_DB_ALTERNATIVE ) ) {
				$class = XOOPS_DB_ALTERNATIVE ;
			} else /* patch to */if (!defined('XOOPS_DB_PROXY')) {
				$class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseSafe';
			} else {
				$class = 'Xoops'.ucfirst(XOOPS_DB_TYPE).'DatabaseProxy';
			}
			$instance = new $class();
			$instance->setLogger(XoopsLogger::instance());
			$instance->setPrefix(XOOPS_DB_PREFIX);
			if (!$instance->connect()) {
				trigger_error("notrace:Unable to connect to database", E_USER_ERROR);
			}
		}
		return $instance;
	}

	/**
	 * Gets a reference to the only instance of database class. Currently
	 * only being used within the installer.
	 * 
     * @static
     * @staticvar   object  The only instance of database class
     * @return      object  Reference to the only instance of database class
	 */
	function &getDatabase()
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
			$database = new $class();
		}
		return $database;
	}


}
?>