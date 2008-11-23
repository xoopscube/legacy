<?php
/**
 *
 * @package Legacy
 * @version $Id: non_installation_module.php,v 1.4 2008/09/25 15:11:22 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/***
 * @internal
 * This handler handles XoopsModule objects without DB. So it doesn't implement
 * some methods for difficult query. Only override methods are usable.
 */
class LegacyNon_installation_moduleHandler extends XoopsObjectHandler
{
	/***
	 * object cache.
	 * @var Array
	 */
	var $_mXoopsModules = array();

	/***
	 * readonly property
	 */
	var $_mExclusions = array(".", "..", "CVS");
	
	function LegacyNon_installation_moduleHandler(&$db)
	{
		parent::XoopsObjectHandler($db);
		$this->_setupObjects();
	}

	/***
	 * Once, load module objects to a member property from XOOPS_MODULE_PATH.
	 */
	function _setupObjects()
	{
		if (count($this->_mXoopsModules) == 0) {
			if ($handler = opendir(XOOPS_MODULE_PATH))	{
				while (($dir = readdir($handler)) !== false) {
					if (!in_array($dir, $this->_mExclusions) && is_dir(XOOPS_MODULE_PATH . "/" . $dir)) {
						$module =& $this->get($dir);
						if ($module !== false ) {
							$this->_mXoopsModules[] =& $module;
						}
						unset($module);
					}
				}
			}
		}
	}
	
	/***
	 * Return module object by $dirname that is specified module directory.
	 * If specified module has been installed or doesn't keep xoops_version, not return it.
	 * @param $dirname string
	 * @param XoopsModule or false
	 */
	function &get($dirname)
	{
		$ret = false;
		
		if (!file_exists(XOOPS_MODULE_PATH . "/" . $dirname . "/xoops_version.php")) {
			return $ret;
		}

		$moduleHandler =& xoops_gethandler('module');

		$check =& $moduleHandler->getByDirname($dirname);
		if (is_object($check)) {
			return $ret;
		}

		$module =& $moduleHandler->create();
		$module->loadInfoAsVar($dirname);
		
		return $module;
	}

	function &getObjects($criteria=null)
	{
		return $this->_mXoopsModules;
	}
	
	function &getObjectsFor2ndInstaller()
	{
		$ret = array();
		
		foreach (array_keys($this->_mXoopsModules) as $key) {
			if (empty($this->_mXoopsModules[$key]->modinfo['disable_legacy_2nd_installer'])) {
				$ret[] =& $this->_mXoopsModules[$key];
			}
		}
		
		return $ret;
	}
}

?>
