<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_RenderCache.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/>
 * @license http://xoopscube.sourceforge.net/license/bsd_licenses.txt Modified BSD license
 *
 */

class XCube_RenderCache
{
	var $mCacheId = null;
	var $mResourceName = null;
	
	function XCube_RenderCache()
	{
	}

	/**
	 * @return bool
	 */
	function isCache($cachetime = null)
	{
	}
	
	/**
	 * @return bool
	 */
	function enableCache()
	{
		return true;
	}
	
	function setResourceName($name)
	{
		$this->mResourceName = $name;
	}
	
	/**
	 * @return string
	 */
	function getCacheId()
	{
	}
	
	/**
	 * @return string
	 */
	function _getFileName()
	{
	}

	function save($renderTarget)
	{
		if ($this->enableCache()) {
			$filename = $this->_getFileName();
			$fp = fopen($filename, "wb");
			fwrite($fp, $renderTarget->getResult());
			fclose($fp);
		}
	}

	function load()
	{
		if ($this->isCache()) {
			return file_get_contents($this->_getFileName());
		}
	}
	
	function clear()
	{
	}
	
	function reset()
	{
		$this->mResourceName = null;
	}
}


?>