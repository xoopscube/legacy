<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_Module.class.php,v 1.6 2008/09/25 15:11:59 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

 /**
  * @public
  * @brief [Abstract] Represents modules and used for Legacy_Controller
  * 
  * This is an abstract class which has interfaces to connect with the controller about
  * the module process. Legacy controller makes an interface of this class and uses its
  * methods to call module programs.
  * 
  * So modules may define their sub-classes implementing this interface.
  * The instance is attached to the Legacy_Context after initializing, so modules can
  * defines members for module's features and can access them. But, most interfaces
  * defined by this class should be called by only Legacy_Controller.
  * 
  * @attention
  *    This interfaces are initialized by only Legacy_Controller.
  * 
  * @see Legacy_Utils::createModule()
  * @see XoopsModule
  */
class Legacy_AbstractModule
{
    /**
     * @public
     * @brief [READ ONLY] Map Array - std::map<string, mixed> - used freely for this module.
     * @remarks
     *    If references are must, access directly to this member.
     */
    public $mAttributes = array();
    
    /**
     * @public
     * @brief [READ ONLY] XoopsModule
     */
    public $mXoopsModule = null;
    
    /**
     * @public
     * @brief [READ ONLY] Map Array - std::map<string, string>
     */
    public $mModuleConfig = array();
    
    /**
     * @private
     * @brief Legacy_AbstractCacheInformation - The cached instance.
     * @see getCacheInfo()
     */
    public $mCacheInfo = null;
    
    /**
     * @private
     * @brief XCube_RenderTarget - The render target instance for this module.
     * @see getRenderTarget()
     */
    public $mRender = null;
    
    /**
     * @public
     * @brief constructor
     * @param $module XoopsModule
     * @attention
     *     Basically, only Legacy_Controller and its utility functions should call the
     *     constructor.
     */
    // !Fix PHP7
    public function __construct(&$module, $loadConfig=true)
    //public function Legacy_AbstractModule(&$module, $loadConfig=true)
    {
        $this->setXoopsModule($module);
        
        if ($loadConfig && ($module->get('hasconfig') == 1 || $module->get('hascomments') == 1 || $module->get('hasnotification') == 1)) {
            $handler =& xoops_gethandler('config');
            $this->setModuleConfig($handler->getConfigsByCat(0, $module->get('mid')));
        }
    }

    /**
     * @public
     * @brief Sets $value with $key to attributes.
     * @param $key string
     * @param $value mixed
     * @return void
     * @remarks
     *    If references are must, access directly to $mAttributes. Because PHP4 can't
     *    handle reference in the signature of this member function.
     */
    public function setAttribute($key, $value)
    {
        $this->mAttributes[$key] = $value;
    }

    /**
     * @public
     * @brief Gets a value indicating whether the value specified by $key exists.
     * @param $key string
     * @return bool
     */
    public function hasAttribute($key)
    {
        return isset($this->mAttributes[$key]);
    }
    
    /**
     * @public
     * @brief Gets a value of attributes with $key.
     * @param string $key
     * @return mixed - If the value specified by $key doesn't exist in attributes, returns null.
     */
    public function getAttribute($key)
    {
        return isset($this->mAttributes[$key]) ? $this->mAttributes[$key] : null;
    }

    /**
     * @public
     * @brief Binds an instance of XoopsModule to the property.
     * @param $xoopsModule XoopsModule
     * @return void
     */
    public function setXoopsModule(&$xoopsModule)
    {
        $this->mXoopsModule =& $xoopsModule;
    }
    
    /**
     * @public
     * @brief Gets the binded XoopsModule instance.
     * @return XoopsModule
     */
    public function &getXoopsModule()
    {
        return $this->mXoopsModule;
    }
    
    /**
     * @public
     * @brief Binds array of xoops module config to the property.
     * @param $config Map Array - std::map<string, mixed>
     * @return void 
     */
    public function setModuleConfig($config)
    {
        $this->mModuleConfig = $config;
    }

    /**
     * @public
     * @brief Gets a value form xoops module config with $key. 
     * @param $key string
     * @return mixed If $key is specified null, returns map array (std::map<string, mixed>)
     */
    public function getModuleConfig($key = null)
    {
        if ($key == null) {
            return $this->mModuleConfig;
        }
        
        return isset($this->mModuleConfig[$key]) ? $this->mModuleConfig[$key] : null;
    }

    /**
     * @public
     * @brief Gets the cache information instance.
     * @return Legacy_ModuleCaceInformation
     * @see _createChaceInfo()
     */
    public function &getCacheInfo()
    {
        if (!is_object($this->mCacheInfo)) {
            $this->_createCacheInfo();
        }
        
        return $this->mCacheInfo;
    }
    
    /**
     * @protected
     * @brief Creates a cache information instance and returns it.
     * @return Legacy_ModuleCacheInformation
     * @remarks
     *     This member function sets the created instance to mCacheInfo because this
     *     instance has to keep the instance for many callbacks.
     * @see getCacheInfo()
     */
    public function _createCacheInfo()
    {
        $this->mCacheInfo = new Legacy_ModuleCacheInformation();
        $this->mCacheInfo->mURL = xoops_getenv('REQUEST_URI');
        $this->mCacheInfo->setModule($this->mXoopsModule);
    }
    
    /**
     * @public
     * @brief Gets the render target instance.
     * @return XCube_RenderTarget
     * @see _createRenderTarget()
     */
    public function &getRenderTarget()
    {
        if ($this->mRender == null) {
            $this->_createRenderTarget();
        }
        
        return $this->mRender;
    }

    /**
     * @protected
     * @brief Creates a render target instance and returns it.
     * @return XCube_RenderTarget
     * @remarks
     *     This member function sets the created instance to mRender because this
     *     instance has to keep the instance for many callbacks.
     * @see getRenderTarget()
     */
    public function _createRenderTarget()
    {
        $renderSystem =& $this->getRenderSystem();
        
        $this->mRender =& $renderSystem->createRenderTarget('main');
        if ($this->mXoopsModule != null) {
            $this->mRender->setAttribute('legacy_module', $this->mXoopsModule->get('dirname'));
        }
    }
    
    /**
     * @public
     * @brief Gets a name of the dependency render system.
     * @return string
     * @remarks
     *     If this module depends on other systems than the main render system  by Legacy,
     *     override this.
     * @see getRenderSystem()
     */
    public function getRenderSystemName()
    {
        $root =& XCube_Root::getSingleton();
        return $root->mContext->mBaseRenderSystemName;
    }
    
    /**
     * @public
     * @brief Gets the dependency render system.
     * @return XCube_RenderSystem
     * @remarks
     *     If this module uses the unregistered render system is used, override this.
     */
    public function &getRenderSystem()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem($this->getRenderSystemName());
        
        return $renderSystem;
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this modules is an active.
     * @return bool
     */
    public function isActive()
    {
        if (!is_object($this->mXoopsModule)) {  //< FIXME
            return false;
        }
        
        return $this->mXoopsModule->get('isactive') ? true : false;
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether the current module has a option of
     *        configurations to use the cache system.
     * @return bool
     */
    public function isEnableCache()
    {
        if (xoops_getenv('REQUEST_METHOD') == 'POST') {
            return false;
        }
        
        $root =& XCube_Root::getSingleton();
        
        return is_object($this->mXoopsModule) && !empty($root->mContext->mXoopsConfig['module_cache'][$this->mXoopsModule->get('mid')]);
    }
    
    /**
     * @public
     * @brief Initializes a cache information object, and returns it.
     * @return Legacy_ModuleCacheInformation
     */
    public function &createCacheInfo()
    {
        $this->mCacheInfo = new Legacy_ModuleCacheInformation();
        $this->mCacheInfo->mURL = xoops_getenv('REQUEST_URI');
        $this->mCacheInfo->setModule($this->mXoopsModule);
        
        return $this->mCacheInfo;
    }

    /**
     * @public
     * @brief [Abstract] This method is called by the controller strategy, if this module
     *        is the current module.
     * @return void
     */
    public function startup()
    {
    }
    
    /**
     * @public
     * @brief [Abstract] This method is called back by the action search feature in the
     *        control panel.
     * @param Legacy_ActionSearchArgs $searchArgs
     * @return void
     * @see Legacy_ActionSearchArgs
     */
    public function doActionSearch(&$searchArgs)
    {
    }
    
    /**
     * @public
     * @brief This method is called back by the xoops global search feature.
     */
    public function doLegacyGlobalSearch($queries, $andor, $max_hit, $start, $uid)
    {
    }
    
    /**
     * @public
     * @brief Gets a value indicating whether this module has the page controller in
     *        the control panel side.
     * @return bool
     * @note
     *    Side menu blocks may not display the admin menu if this member function returns
     *    false.
     * @attention
     *    Controller fetches the list of modules from DB before. So, 'override' may not be
     *    able to change the process.
     */
    public function hasAdminIndex()
    {
        return false;
    }
    
    /**
     * @public
     * @brief [Abstract] Gets an absolute URL indicating the top page of this module for
     *        the control panel side.
     * @return string
     * @attention
     *     Controller fetches the list of modules from DB before. So, 'override' may not
     *     be able to change the process.
     */
    public function getAdminIndex()
    {
        return null;
    }
    
    /**
     * @public
     * @brief Gets an array having menus for the side menu of the control panel.
     * @return Complex Array
     * @see /modules/legacy/admin/templates/blocks/legacy_admin_block_sidemenu.html
     */
    public function getAdminMenu()
    {
    }
}

/**
 * @public
 * @brief Used for adapting $xoopsModule to imitate XOOPS2 responses.
 * @remarks
 *    This class is the standard class implementing Legacy_AbstractModule, and is helpful
 *    to be used by Legacy_Controller. If a module doesn't define its sub-class of
 *    Legacy_AbstractModule, this class is used as generic Legacy_AbstractModule.
 */
class Legacy_ModuleAdapter extends Legacy_AbstractModule
{
    /**
     * @private
     * @brief bool
     */
    public $_mAdminMenuLoadedFlag = false;
    
    /**
     * @protected
     * @brief Complex Array - cached
     */
    public $mAdminMenu = null;
        // !Fix PHP7
        public function __construct($module, $loadConfig=true)
    //public function Legacy_ModuleAdapter($module, $loadConfig=true)
    {
        // ! call parent::__construct() instead of parent::Controller()
        parent::__construct($module, $loadConfig);
        //parent::Legacy_AbstractModule($module, $loadConfig);
    }

    /**
     * @public
     * @brief This method is called back by the action search feature in the control
     *        panel.
     * @param Legacy_ActionSearchArgs $searchArgs
     * @return void
     * @see Legacy_ActionSearchArgs
     */
    public function doActionSearch(&$searchArgs)
    {
        if (!is_object($searchArgs)) {
            return;
        }

        $this->mXoopsModule->loadAdminMenu();
        if (count($this->mXoopsModule->adminmenu) == 0 && !isset($this->mXoopsModule->modinfo['config'])) {
            return;
        }
            
        //
        // Search preference
        //
        if (isset($this->mXoopsModule->modinfo['config'])&&count($this->mXoopsModule->modinfo['config'])>0) {
            $findFlag = false;
            foreach ($searchArgs->getKeywords() as $word) {
                if (stristr(_PREFERENCES, $word) !== false) {
                    $root =& XCube_Root::getSingleton();
                    $searchArgs->addRecord($this->mXoopsModule->getVar('name'), $root->mController->getPreferenceEditUrl($this->mXoopsModule), _PREFERENCES);
                    $findFlag = true;
                    break;
                }
            }
            
            if (!$findFlag) {
                $configInfos=array();
                foreach ($this->mXoopsModule->modinfo['config'] as $config) {
                    if (isset($config['title'])) {
                        $configInfos[]=@constant($config['title']);
                    }
                    if (isset($config['description'])) {
                        $configInfos[]=@constant($config['description']);
                    }
                    if (isset($config['options'])&&count($config['options'])>0) {
                        foreach ($config['options'] as $key=>$val) {
                            $configInfos[]=(@constant($key) ? @constant($key) : $key);
                        }
                    }
                }
    
                $findFlag=true;
                foreach ($searchArgs->getKeywords() as $word) {
                    $findFlag&=(stristr(implode(" ", $configInfos), $word)!==false);
                }
                    
                if ($findFlag) {
                    $searchArgs->addRecord($this->mXoopsModule->getVar('name'),
                                      XOOPS_URL.'/modules/legacy/admin/index.php?action=PreferenceEdit&amp;confmod_id='.$this->mXoopsModule->getVar('mid'),
                                      _PREFERENCES);
                }
            }
        }
        
        //
        // Search AdminMenu
        //
        if (count($this->mXoopsModule->adminmenu)>0) {
            foreach ($this->mXoopsModule->adminmenu as $menu) {
                $findFlag=true;
                foreach ($searchArgs->getKeywords() as $word) {
                    $tmpFlag=false;
                    $tmpFlag|=(stristr($menu['title'], $word)!==false);

                    // Search keyword
                    if (isset($menu['keywords'])) {
                        $keyword=is_array($menu['keywords']) ? implode(" ", $menu['keywords']) : $menu['keywords'];
                        $tmpFlag|=(stristr($keyword, $word)!==false);
                    }

                    $findFlag&=$tmpFlag;
                }

                if ($findFlag) {
                    //
                    // Create url string with absolute information.
                    //
                    $url="";
                    if (isset($menu['absolute'])&&$menu['absolute']) {
                        $url=$menu['link'];
                    } else {
                        $url=XOOPS_URL."/modules/".$this->mXoopsModule->getVar('dirname')."/".$menu['link'];
                    }

                    //
                    // Add record
                    //
                    $searchArgs->addRecord($this->mXoopsModule->getVar('name'), $url, $menu['title']);
                }
            }
        }
        
        //
        // Search help
        //
        if ($this->mXoopsModule->hasHelp()) {
            $findFlag = false;
            
            foreach ($searchArgs->getKeywords() as $word) {
                if (stristr(_HELP, $word) !== false) {
                    $root =& XCube_Root::getSingleton();
                    $searchArgs->addRecord($this->mXoopsModule->getVar('name'), $root->mController->getHelpViewUrl($this->mXoopsModule), _HELP);
                    $findFlag = true;
                    break;
                }
            }
            
            if (!$findFlag) {
                $root =& XCube_Root::getSingleton();
                $language = $root->mContext->getXoopsConfig('language');
                $helpfile = $this->mXoopsModule->getHelp();
                $dir = XOOPS_MODULE_PATH . "/" . $this->mXoopsModule->getVar('dirname') . "/language/" . $language. "/help";
    
                if (!file_exists($dir . "/" . $helpfile)) {
                    $dir = XOOPS_MODULE_PATH . "/" . $this->mXoopsModule->getVar('dirname') . "/language/english/help";
                    if (!file_exists($dir . "/" . $helpfile)) {
                        return;
                    }
                }
                $lines = file($dir . "/" . $helpfile);
                foreach ($lines as $line) {
                    foreach ($searchArgs->getKeywords() as $word) {
                        if (stristr($line, $word) !== false) {
                            $url = XOOPS_MODULE_URL . "/legacy/admin/index.php?action=Help&amp;dirname=" . $this->mXoopsModule->getVar('dirname');
                            $searchArgs->addRecord($this->mXoopsModule->getVar('name'), $url, _HELP);
                            return;
                        }
                    }
                }
            }
        }
    }

    public function doLegacyGlobalSearch($queries, $andor, $max_hit, $start, $uid)
    {
        $ret = array();
        $results = $this->mXoopsModule->search($queries, $andor, $max_hit, $start, $uid);
        
        if (is_array($results) && count($results) > 0) {
            foreach ($results as $result) {
                $item = array();
                if (isset($result['image']) && strlen($result['image']) > 0) {
                    if (file_exists(XOOPS_ROOT_PATH . '/uploads/' . $result['image'])) {
                        $item['image'] = XOOPS_URL . '/uploads/' . $result['image'];
                    } else {
                        $item['image'] = XOOPS_URL . '/modules/' . $this->mXoopsModule->get('dirname') . '/' . $result['image'];
                    }
                } else {
                    $item['image'] = XOOPS_URL . '/images/icons/posticon2.gif';
                }
                        
                $item['link'] = XOOPS_URL . '/modules/' . $this->mXoopsModule->get('dirname') . '/' . $result['link'];
                $item['title'] = $result['title'];
                $item['uid'] = $result['uid'];
                        
                //
                // TODO If this service will come to web service, we should
                // change format from unixtime to string by timeoffset.
                //
                $item['time'] = isset($result['time']) ? $result['time'] : 0;
                
                $ret[] = $item;
            }
        }
        
        return $ret;
    }
    
    /**
     * @public
     * @brief [Final] Gets a value indicating whether this module has the page controller in
     *        the control panel side.
     * @return bool
     */
    public function hasAdminIndex()
    {
        $dmy =& $this->mXoopsModule->getInfo();
        return isset($this->mXoopsModule->modinfo['adminindex']) && $this->mXoopsModule->modinfo['adminindex'] != null;
    }
    
    /**
     * @public
     * @brief Gets an absolute URL indicating the top page of this module for the control
     *        panel side.
     * @return string
     */
    public function getAdminIndex()
    {
        $dmy =& $this->mXoopsModule->getInfo();
        return XOOPS_MODULE_URL . '/' . $this->mXoopsModule->get('dirname') . '/' . $this->mXoopsModule->modinfo['adminindex'];
    }
    
    public function getAdminMenu()
    {
        if ($this->_mAdminMenuLoadedFlag) {
            return $this->mAdminMenu;
        }
        
        $info =& $this->mXoopsModule->getInfo();
        $root =& XCube_Root::getSingleton();

        //
        // Load admin menu, and add preference menu by own judge.
        //
        $this->mXoopsModule->loadAdminMenu();
        if ($this->mXoopsModule->get('hasnotification')
            || (isset($info['config']) && is_array($info['config']))
            || (isset($info['comments']) && is_array($info['comments']))) {
            $this->mXoopsModule->adminmenu[] = array(
                    'link' => $root->mController->getPreferenceEditUrl($this->mXoopsModule),
                    'title' => _PREFERENCES,
                    'absolute' => true);
        }
            
        if ($this->mXoopsModule->hasHelp()) {
            $this->mXoopsModule->adminmenu[] = array('link' =>  $root->mController->getHelpViewUrl($this->mXoopsModule),
                                          'title' => _HELP,
                                          'absolute' => true);
        }
        
        $this->_mAdminMenuLoadedFlag = true;
        
        if ($this->mXoopsModule->adminmenu) {
            $dirname = $this->mXoopsModule->get('dirname');
            foreach ($this->mXoopsModule->adminmenu as $menu) {
                if (!isset($menu['absolute']) || (isset($menu['absolute']) && $menu['absolute'] != true)) {
                    $menu['link'] = XOOPS_MODULE_URL . '/' . $dirname . '/' . $menu['link'];
                }
                $this->mAdminMenu[] = $menu;
            }
        }
        
        return $this->mAdminMenu;
    }
}
