<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_PublicControllerStrategy.class.php,v 1.7 2008/11/14 09:45:23 mumincacao Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Legacy_PublicControllerStrategy extends Legacy_AbstractControllerStrategy
{
    public $mStatusFlag = LEGACY_CONTROLLER_STATE_PUBLIC;
        // !Fix PHP7
        public function __construct(&$controller)
    //public function Legacy_PublicControllerStrategy(&$controller)
    {
        // ! call parent::__construct() instead of parent::Controller()
        //parent::Legacy_AbstractControllerStrategy($controller);
        parent::__construct($controller);
        
        $controller->mRoot->mContext->mBaseRenderSystemName = "Legacy_RenderSystem";
        
        if (!defined("LEGACY_DEPENDENCE_RENDERER")) {
            define("LEGACY_DEPENDENCE_RENDERER", "Legacy_RenderSystem");
        }
    }

    public function setupBlock()
    {
        $showFlag =0;
        $mid=0;

        if ($this->mController->mRoot->mContext->mModule != null) {
            $showFlag = (preg_match("/index\.php$/i", xoops_getenv('PHP_SELF')) && $this->mController->mRoot->mContext->mXoopsConfig['startpage'] == $this->mController->mRoot->mContext->mXoopsModule->get('dirname'));
            $mid = $this->mController->mRoot->mContext->mXoopsModule->get('mid');
        } else {
            //
            // If you does not have module_contoller, this request is to toppage or other pages of toppage.
            //

            // $mid = preg_match("/index\.php$/i", xoops_getenv('PHP_SELF')) ? -1 : 0;
            $pathArray = parse_url(!empty($_SERVER['PATH_INFO']) ? substr($_SERVER['PHP_SELF'], 0, - strlen($_SERVER['PATH_INFO'])) : $_SERVER['PHP_SELF']);
            $mid = preg_match("#(/index\.php|/)$#i", @$pathArray['path']) ? -1 : 0;
        }

        $blockHandler =& xoops_gethandler('block');
        $showCenterFlag = (SHOW_CENTERBLOCK_LEFT | SHOW_CENTERBLOCK_CENTER | SHOW_CENTERBLOCK_RIGHT);
        $showRightFlag = SHOW_SIDEBLOCK_RIGHT;
        $showFlag = SHOW_SIDEBLOCK_LEFT | $showRightFlag | $showCenterFlag;
        $groups = is_object($this->mController->mRoot->mContext->mXoopsUser) ? $this->mController->mRoot->mContext->mXoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;

        $blockObjects =& $blockHandler->getBlocks($groups, $mid, $showFlag);
        foreach ($blockObjects as $blockObject) {
            $block =& Legacy_Utils::createBlockProcedure($blockObject);

            if ($block->prepare() !== false) {
                $this->mController->_mBlockChain[] =& $block;
            }
            unset($block);
            unset($blockObject);
        }
    }

    public function &getMainThemeObject()
    {
        // [TODO]
        // Because get() of the virtual handler is heavy, we have to consider
        // the new solution about this process.
        //
        $handler =& xoops_getmodulehandler('theme', 'legacy');
        $theme =& $handler->get($this->mController->mRoot->mContext->getThemeName());
        if (is_object($theme)) {
            return $theme;
        }

        //-----------
        // Fail safe
        //-----------

        $root =& XCube_Root::getSingleton();
        foreach ($root->mContext->mXoopsConfig['theme_set_allowed'] as $theme) {
            $theme =& $handler->get($theme);
            if (is_object($theme)) {
                $root->mContext->setThemeName($theme->get('dirname'));
                return $theme;
            }
        }
        
        $objs =& $handler->getObjects();
        if (count($objs) > 0) {
            return $objs[0];
        }

        $theme = null;
        return $theme;
    }
    
    public function isEnableCacheFeature()
    {
        return true;
    }
    
    public function enableAccess()
    {
        if ($this->mController->mRoot->mContext->mModule != null) {
            $dirname = $this->mController->mRoot->mContext->mXoopsModule->get('dirname');
            
            return $this->mController->mRoot->mContext->mUser->isInRole("Module.${dirname}.Visitor");
        }
        
        return true;
    }
    
    public function setupModuleLanguage()
    {
        $root =& XCube_Root::getSingleton();
        $root->mLanguageManager->loadModuleMessageCatalog($root->mContext->mXoopsModule->get('dirname'));
    }
}
