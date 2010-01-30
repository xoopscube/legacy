<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

/**
 * Lecat_AbstractAction
**/
abstract class Lecat_AbstractAction
{
    /**
     * @brief   XCube_Root
    **/
    public $mRoot = null;

    /**
     * @brief   Lecat_Module
    **/
    public $mModule = null;

    /**
     * @brief   Lecat_AssetManager
    **/
    public $mAsset = null;

    /**
     * __construct
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function __construct()
    {
        $this->mRoot =& XCube_Root::getSingleton();
        $this->mModule =& $this->mRoot->mContext->mModule;
        $this->mAsset =& $this->mModule->mAssetManager;
    }

    /**
     * _getStylesheet
     * 
     * @param   void
     * 
     * @return  String
    **/
    protected function _getStylesheet()
    {
        return $this->mRoot->mContext->mModuleConfig['css_file'];
    }

    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function prepare()
    {
        return true;
    }

    /**
     * hasPermission
     * 
     * @param   void
     * 
     * @return  bool
    **/
    public function hasPermission()
    {
        return true;
    }

    /**
     * getDefaultView
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function getDefaultView()
    {
        return LECAT_FRAME_VIEW_NONE;
    }

    /**
     * execute
     * 
     * @param   void
     * 
     * @return  Enum
    **/
    public function execute()
    {
        return LECAT_FRAME_VIEW_NONE;
    }

    /**
     * executeViewSuccess
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewSuccess(/*** XCube_RenderTarget ***/ &$render)
    {
    }

    /**
     * executeViewError
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewError(/*** XCube_RenderTarget ***/ &$render)
    {
    }

    /**
     * executeViewIndex
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewIndex(/*** XCube_RenderTarget ***/ &$render)
    {
    }

    /**
     * executeViewInput
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewInput(/*** XCube_RenderTarget ***/ &$render)
    {
    }

    /**
     * executeViewPreview
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewPreview(/*** XCube_RenderTarget ***/ &$render)
    {
    }

    /**
     * executeViewCancel
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewCancel(/*** XCube_RenderTarget ***/ &$render)
    {
    }
}

?>
