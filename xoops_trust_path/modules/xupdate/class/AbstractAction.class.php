<?php
/**
 * @file
 * @package xupdate
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

/**
 * Xupdate_AbstractAction
**/
abstract class Xupdate_AbstractAction
{
	public /*** XCube_Root ***/ $mRoot = null;

	public /*** Xupdate_Module ***/ $mModule = null;

	public /*** Xupdate_AssetManager ***/ $mAsset = null;


	public $Xupdate  ;	// Xupdate instance
	public $Ftp  ;	// FTP instance
	public $Func ;	// Functions instance
	public $mod_config ;


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

		// Xupdate_ftp class object
		require_once XUPDATE_TRUST_PATH .'/class/Root.class.php';

		$this->Xupdate = new Xupdate_Root ;// Xupdate instance
		$this->Ftp = $this->Xupdate->Ftp ;		// FTP instance
		$this->Func = $this->Xupdate->func ;		// Functions instance
		$this->mod_config = $this->mRoot->mContext->mModuleConfig ;	// mod_config
		// FTP login check
		$this->mod_config['_FtpLoginCheck'] = $this->Ftp->checkLogin();
		//	adump($this->mod_config);

   }

    /**
     * getPageTitle
     *
     * @param   void
     *
     * @return  string
    **/
    public function getPagetitle()
    {
    	//XCL 2.2 only
        //return Legacy_Utils::formatPagetitle($this->mRoot->mContext->mModule->mXoopsModule->get('name'), $this->_getPagetitle(), $this->_getActionName());
        return $this->mRoot->mContext->mModule->mXoopsModule->get('name') .':'. $this->_getPagetitle();
    }

    /**
     * _getPageTitle
     *
     * @param   void
     *
     * @return  string
    **/
    protected function _getPagetitle()
    {
        return null;
    }

    /**
     * _getActionName
     *
     * @param   void
     *
     * @return  string
    **/
    protected function _getActionName()
    {
        return null;
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
     * setHeaderScript
     *
     * @param   void
     *
     * @return  void
    **/
    public function setHeaderScript()
    {
		$headerScript = $this->mRoot->mContext->getAttribute('headerScript');
		$headerScript->addStylesheet($this->_getStylesheet());
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
        return XUPDATE_FRAME_VIEW_NONE;
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
        return XUPDATE_FRAME_VIEW_NONE;
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
    
    protected function modalBoxJs() {
    	return <<<EOD
jQuery(document).ready(function($) {
    //select all the a tag with name equal to modal
    $('#contentBody form').submit(function(e) {
        //Cancel the link behavior
        //e.preventDefault();
        //Get the A tag
        var id = $('#xupdate_dialog');
     
        //Get the screen height and width
        var maskHeight = $(document).height();
        var maskWidth = $(window).width();
     
        //Set height and width to mask to fill up the whole screen
        $('#xupdate_mask').css({'width':maskWidth,'height':maskHeight});
         
        //transition effect    
        $('#xupdate_mask').fadeIn(1000);   
        $('#xupdate_mask').fadeTo("slow",0.8); 
     
        //Get the window height and width
        var winH = $(window).height();
        var winW = $(window).width();
               
        //Set the popup window to center
        $(id).css('top',  winH/2-$(id).height()/2);
        $(id).css('left', winW/2-$(id).width()/2);
     
        //transition effect
        $(id).fadeIn(2000);

    });
     
    ////if close button is clicked
    //$('.window .close').click(function (e) {
    //    //Cancel the link behavior
    //    e.preventDefault();
    //    $('#mask, .window').hide();
    //});    
     
    ////if mask is clicked
    //$('#mask').click(function () {
    //    $(this).hide();
    //    $('.window').hide();
    //});        
     
});
EOD;
    }
}

?>
