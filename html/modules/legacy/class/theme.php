<?php
/**
 *
 * @package Legacy
 * @version $Id: theme.php,v 1.4 2008/09/25 15:11:21 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_ROOT_PATH . '/include/comment_constants.php';

class LegacyThemeObject extends XoopsSimpleObject
{
	function LegacyThemeObject()
	{
		static $initVars;
		if (isset($initVars)) {
			$this->mVars = $initVars;
			return;
		}
		$this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('dirname', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('screenshot', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('description', XOBJ_DTYPE_STRING, '', false, 255);
		$this->initVar('format', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('render_system', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('version', XOBJ_DTYPE_STRING, '', true, 32);
		$this->initVar('author', XOBJ_DTYPE_STRING, '', true, 64);
		$this->initVar('url', XOBJ_DTYPE_STRING, '', true, 255);
		$this->initVar('license', XOBJ_DTYPE_STRING, '', true, 255);
		
		// For TYPO
		$this->initVar('licence', XOBJ_DTYPE_STRING, '', true, 255);
		$initVars = $this->mVars;
	}
}

class LegacyThemeHandler extends XoopsObjectHandler
{
	var $_mResults = array();
	
	/**
	 * @var XCube_Delegate
	 */
	var $mGetInstalledThemes = null;
	
	function LegacyThemeHandler(&$db)
	{
		$this->mGetInstalledThemes =new XCube_Delegate();
		$this->mGetInstalledThemes->register('LegacyThemeHandler.GetInstalledThemes');
	}
	
	function &create()
	{
		$ret =new LegacyThemeObject();
		return $ret;
	}
	
	function &get($name)
	{
		$ret = null;
		$this->_makeCache();
		
		foreach (array_keys($this->_mResults) as $key) {
			if ($this->_mResults[$key]->get('dirname') == $name) {
				return $this->_mResults[$key];
			}
		}
		
		return $ret;
	}
	
	function &getObjects($criteria = null, $id_as_key = false)
	{
		$this->_makeCache();
		return $this->_mResults;
	}
	
	/**
	 * Create cache at $this->mResult by Delegate, if cache is empty.
	 */
	function _makeCache()
	{
		if (count($this->_mResults) == 0) {
			$t_themeArr = array();
			$this->mGetInstalledThemes->call(new XCube_Ref($t_themeArr));
			
			foreach ($t_themeArr as $theme) {
				$obj =& $this->create();
				$obj->set('name', $theme->mName);
				$obj->set('dirname', $theme->mDirname);
				$obj->set('screenshot', $theme->mScreenShot);
				$obj->set('description', $theme->mDescription);
				$obj->set('format', $theme->mFormat);
				$obj->set('render_system', $theme->mRenderSystemName);
				$obj->set('version', $theme->mVersion);
				$obj->set('author', $theme->mAuthor);
				$obj->set('url', $theme->mUrl);
				$obj->set('licence', $theme->mLicense);
				$obj->set('license', $theme->mLicence);
				$this->_mResults[] =& $obj;
				unset($obj);
			}
		}
	}
	
	function insert(&$obj, $force = false)
	{
		return false;
	}

	function delete(&$obj, $force = false)
	{
		return false;
	}
}

?>
