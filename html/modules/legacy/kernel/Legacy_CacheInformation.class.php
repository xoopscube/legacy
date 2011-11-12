<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_CacheInformation.class.php,v 1.4 2008/09/25 15:12:00 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 * The structure which have a policy and an information of a module, which
 * Legacy_Controller must know. In the later version, this class may be
 * replaced with just array.
 * 
 * For a performance, this class has reset() to reuse a object.
 */
class Legacy_AbstractCacheInformation
{
    /**
     * Array of uid. This is an information for cache store program to generate
     * an unique file name. Uid isn't must. Sets identity data.
     * 
     * @access public
     * @var Array of uid
     */
    var $mIdentityArr = array();
    
    /**
     * Array of groupid. This is an information for cache store program to
     * generate an unique file name.
     * 
     * @access public
     * @var Array of groupid
     */
    var $mGroupArr = array();

    /**
     * Boolean flag indicating whether this object asks caching to the
     * controller.
     * 
     * @access private
     * @var bool
     */ 
    var $_mEnableCache = false;
    
    /**
     * For a special cache mechanism, free to use hashmap.
     * 
     * @access public
     * @var array
     */
    var $mAttributes = array();
    
    function Legacy_AbstractCacheInformation()
    {
    }
    
    /**
     * Gets a value indicating whether someone has tried to set a flag to this
     * object.
     * @return bool
     */
    function hasSetEnable()
    {
        return $this->_mEnableCache !== false;
    }
    
    /**
     * Sets a flag indicating whether this object decides executing cache.
     * @param bool $flag
     */
    function setEnableCache($flag)
    {
        $this->_mEnableCache = $flag;
    }
    
    /**
     * Gets a flag indicating whether this object decides executing cache.
     * @return bool
     */
    function isEnableCache()
    {
        return $this->_mEnableCache;
    }
    
    /**
     * Resets member properties to reuse this object.
     */
    function reset()
    {
        $this->mIdentityArr = array();
        $this->mGroupArr = array();
        $this->_mEnableCache = null;
    }
    
    function getCacheFilePath()
    {
    }
}

class Legacy_ModuleCacheInformation extends Legacy_AbstractCacheInformation
{
    /**
     * [READ ONLY] Xoops Module Object.
     * 
     * @access protected
     * @var XoopsModule
     */
    var $mModule = null;
    
    /**
     * The current URL used as a base for a cache file name. This should be
     * modified by modules to not make extra cache files.
     * 
     * @access public
     * @var string
     */
    var $mURL = null;

     /**
      * @var XCube_Delegate
      */
     var $mGetCacheFilePath = null;
     
     function Legacy_ModuleCacheInformation()
     {
         parent::Legacy_AbstractCacheInformation();
         $this->mGetCacheFilePath = new XCube_Delegate();
         $this->mGetCacheFilePath->register('Legacy_ModuleCacheInformation.GetCacheFilePath');
     }
     
    /**
     * Sets a module object.
     * @param XoopsModule $module
     */
    function setModule(&$module)
    {
        $this->mModule =& $module;
    }
    
    function reset()
    {
        parent::reset();
        $this->mModule = null;
        $this->mURL = null;
    }
    
    /**
     * Gets a file path of a cache file for module contents.
     * @param Legacy_ModuleCacheInformation $cacheInfo
     * @return string
     */
    function getCacheFilePath()
    {
        $filepath = null;
        $this->mGetCacheFilePath->call(new XCube_Ref($filepath), $this);
        
        if (!$filepath) {
            $id = md5(XOOPS_SALT . $this->mURL . "(" . implode("_", $this->mIdentityArr) . ")" . implode("_", $this->mGroupArr));
            $filepath = XOOPS_CACHE_PATH . "/" . $id . ".cache.html";
        }
        
        return $filepath;
    }
}

class Legacy_BlockCacheInformation extends Legacy_AbstractCacheInformation
{
    /**
     * [READ ONLY] Xoops Block Object.
     * 
     * @access protected
     * @var XoopsBlock
     */
     var $mBlock = null;
     
     /**
      * @var XCube_Delegate
      */
     var $mGetCacheFilePath = null;
     
     function Legacy_BlockCacheInformation()
     {
         parent::Legacy_AbstractCacheInformation();
         $this->mGetCacheFilePath = new XCube_Delegate();
         $this->mGetCacheFilePath->register('Legacy_BlockCachInformation.getCacheFilePath');
     }
     
     /**
      * Sets a block object.
      * 
      * @param Legacy_AbstractBlockProcedure $blockProcedure
      */
     function setBlock(&$blockProcedure)
     {
         $this->mBlock =& $blockProcedure->_mBlock;
     }
     
     function reset()
     {
         parent::reset();
         $this->mBlock = null;
     }

    /**
     * Gets a file path of a cache file for module contents.
     * @param Legacy_BlockCacheInformation $cacheInfo
     * @return string
     */
    function getCacheFilePath()
    {
        $filepath = null;
        $this->mGetCacheFilePath->call(new XCube_Ref($filepath), $this);
        
        if (!$filepath) {
            $id = md5(XOOPS_SALT . "(" . implode("_", $this->mIdentityArr) . ")" . implode("_", $this->mGroupArr));
            $filepath = XOOPS_CACHE_PATH . "/bid".$this->mBlock->get('bid') . '_' . $id . ".cache.html";
        }
        
        return $filepath;
    }
}

?>
