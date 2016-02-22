<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_RenderCache.class.php,v 1.3 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

class XCube_RenderCache
{
    public $mCacheId = null;
    public $mResourceName = null;
    
    public function XCube_RenderCache()
    {
    }

    /**
     * @return bool
     */
    public function isCache($cachetime = null)
    {
    }
    
    /**
     * @return bool
     */
    public function enableCache()
    {
        return true;
    }
    
    public function setResourceName($name)
    {
        $this->mResourceName = $name;
    }
    
    /**
     * @return string
     */
    public function getCacheId()
    {
    }
    
    /**
     * @return string
     */
    public function _getFileName()
    {
    }

    public function save($renderTarget)
    {
        if ($this->enableCache()) {
            $filename = $this->_getFileName();
            $fp = fopen($filename, "wb");
            fwrite($fp, $renderTarget->getResult());
            fclose($fp);
        }
    }

    public function load()
    {
        if ($this->isCache()) {
            return file_get_contents($this->_getFileName());
        }
    }
    
    public function clear()
    {
    }
    
    public function reset()
    {
        $this->mResourceName = null;
    }
}
