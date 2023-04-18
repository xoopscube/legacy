<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_CacheInformation.class.php,v 1.4 2008/09/25 15:12:00 kilica Exp $
 * @copyright (c) 2005-2023 The XOOPSCube Project
 * @license   GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

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
    public $mIdentityArr = [];

    /**
     * Array of groupid. This is an information for cache store program to
     * generate an unique file name.
     *
     * @access public
     * @var Array of groupid
     */
    public $mGroupArr = [];

    /**
     * Boolean flag indicating whether this object asks caching to the
     * controller.
     *
     * @access private
     * @var bool
     */
    public $_mEnableCache = false;

    /**
     * For a special cache mechanism, free to use hashmap.
     *
     * @access public
     * @var array
     */
    public $mAttributes = [];

    public function __construct()
    //public function Legacy_AbstractCacheInformation()
    {
    }

    /**
     * Gets a value indicating whether someone has tried to set a flag to this
     * object.
     * @return bool
     */
    public function hasSetEnable()
    {
        return false !== $this->_mEnableCache;
    }

    /**
     * Sets a flag indicating whether this object decides executing cache.
     * @param bool $flag
     */
    public function setEnableCache($flag)
    {
        $this->_mEnableCache = $flag;
    }

    /**
     * Gets a flag indicating whether this object decides executing cache.
     * @return bool
     */
    public function isEnableCache()
    {
        return $this->_mEnableCache;
    }

    /**
     * Resets member properties to reuse this object.
     */
    public function reset()
    {
        $this->mIdentityArr = [];
        $this->mGroupArr = [];
        $this->_mEnableCache = null;
    }

    public function getCacheFilePath()
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
    public $mModule = null;

    /**
     * The current URL used as a base for a cache file name.
     * This should be modified by modules to not make extra cache files.
     *
     * @access public
     * @var string
     */
    public $mURL = null;

     /**
      * @var XCube_Delegate
      */
     public $mGetCacheFilePath = null;

     public function __construct()
    {
        parent::__construct();

        $this->mGetCacheFilePath = new XCube_Delegate();
        $this->mGetCacheFilePath->register('Legacy_ModuleCacheInformation.GetCacheFilePath');
    }

    /**
     * Sets a module object.
     * @param XoopsModule $module
     */
    public function setModule(&$module)
    {
        $this->mModule =& $module;
    }

    public function reset()
    {
        parent::reset();
        $this->mModule = null;
        $this->mURL = null;
    }

    /**
     * Gets a file path of a cache file for module contents.
     * @return string
     */
    public function getCacheFilePath()
    {
        $filepath = null;
        $this->mGetCacheFilePath->call(new XCube_Ref($filepath), $this);

        if (!$filepath) {
            $id = md5(XOOPS_SALT . $this->mURL . '(' . implode('_', $this->mIdentityArr) . ')' . implode('_', $this->mGroupArr));
            $filepath = XOOPS_CACHE_PATH . '/' . $id . '.cache.html';
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
     public $mBlock = null;

     /**
      * @var XCube_Delegate
      */
     public $mGetCacheFilePath = null;

     public function __construct()
    {
        parent::__construct();
        $this->mGetCacheFilePath = new XCube_Delegate();
        $this->mGetCacheFilePath->register('Legacy_BlockCachInformation.getCacheFilePath');
    }

     /**
      * Sets a block object.
      *
      * @param Legacy_AbstractBlockProcedure $blockProcedure
      */
     public function setBlock(&$blockProcedure)
     {
         $this->mBlock = $blockProcedure->_mBlock;
     }

    public function reset()
    {
        parent::reset();
        $this->mBlock = null;
    }

    /**
     * Gets a file path of a cache file for module contents.
     * @return string
     */
    public function getCacheFilePath()
    {
        $filepath = null;
        $this->mGetCacheFilePath->call(new XCube_Ref($filepath), $this);

        if (!$filepath) {
            $id = md5(XOOPS_SALT . '(' . implode('_', $this->mIdentityArr) . ')' . implode('_', $this->mGroupArr));
            $filepath = static::getCacheFileBase($this->mBlock->get('bid'), $id);
        }
        return $filepath;
    }

    public static function getCacheFileBase($bid, $context)
    {
        return XOOPS_CACHE_PATH . '/' . urlencode(XOOPS_URL) . '_bid'. $bid . '_' . $context . '.cache.html';
    }
}
