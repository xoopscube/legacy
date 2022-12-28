<?php
/**
 * X-Update package management for XCL
 *
 * @package XCL
 * @subpackage Xupdate
 * @version 2.3
 * @author Naoki Sawada, Naoki Okino, Gigamaster (XCL 2.3)
 * @copyright Copyright 2005-2022 XOOPS Cube Project
 * @license https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */


if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit;
}

// Set include_path
if ( ! defined( 'PATH_SEPARATOR' ) ) {
	define( 'PATH_SEPARATOR', ( 0 !== stripos( PHP_OS, 'WIN' ) ) ? ':' : ';' );
}
// TODO gigamaster check
//set_include_path( get_include_path() . PATH_SEPARATOR . dirname( __DIR__ ) . '/PEAR' );
//set_include_path( get_include_path() . PEAR_PATH . PATH_SEPARATOR . '/PEAR' );
set_include_path( get_include_path() . PEAR_PATH );
/**
 * Xupdate_AbstractAction
 **/
abstract class Xupdate_AbstractAction {
	/*** XCube_Root ***/
	public $mRoot = null;

	/*** Xupdate_Module ***/
	public $mModule = null;

	/*** Xupdate_AssetManager ***/
	public $mAsset = null;


	public $Xupdate;    // Xupdate instance
	public $Ftp;    // FTP instance
	public $Func;    // Functions instance
	public $mod_config;


	/**
	 * __construct
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	public function __construct() {
		$this->mRoot   =& XCube_Root::getSingleton();
		$this->mModule =& $this->mRoot->mContext->mModule;
		$this->mAsset  =& $this->mModule->mAssetManager;

		// Xupdate_ftp class object
		require_once XUPDATE_TRUST_PATH . '/class/Root.class.php';

		$this->Xupdate    = new Xupdate_Root();// Xupdate instance
		$this->Ftp        = $this->Xupdate->Ftp;        // FTP instance
		$this->Func       = $this->Xupdate->func;        // Functions instance
		$this->mod_config = $this->mRoot->mContext->mModuleConfig;    // mod_config
		// FTP login check
		$this->mod_config['_FtpLoginCheck'] = $this->Ftp->checkLogin();
		// curl extention check
		$this->mod_config['_CurlCheck'] = ( extension_loaded( 'curl' ) );
		// php max_execution_time
		@ set_time_limit( 300 );
		$this->mod_config['_ExecutionTime'] = (int) ini_get( 'max_execution_time' );
		//	adump($this->mod_config);
	}

	/**
	 * getPageTitle
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	public function getPagetitle() {
		//XCL 2.2 only
		//return Legacy_Utils::formatPagetitle($this->mRoot->mContext->mModule->mXoopsModule->get('name'), $this->_getPagetitle(), $this->_getActionName());
		return $this->mRoot->mContext->mModule->mXoopsModule->get( 'name' ) . ':' . $this->_getPagetitle();
	}

	/**
	 * _getPageTitle
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	protected function _getPagetitle() {
		return null;
	}

	/**
	 * _getActionName
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	protected function _getActionName() {
		return null;
	}

	/**
	 * _getStylesheet
	 *
	 * @param void
	 *
	 * @return  String
	 **/
	protected function _getStylesheet() {
		//return $this->mRoot->mContext->mModuleConfig['css_file'];
		return '/modules/xupdate/admin/templates/stylesheets/module.css';
	}

	/**
	 * setHeaderScript
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	public function setHeaderScript() {
		$headerScript = $this->mRoot->mContext->getAttribute( 'headerScript' );
		$headerScript->addStylesheet( $this->_getStylesheet() );
	}

	/**
	 * prepare
	 *
	 * @param void
	 *
	 * @return  bool
	 **/
	public function prepare() {
		return true;
	}

	/**
	 * hasPermission
	 *
	 * @param void
	 *
	 * @return  bool
	 **/
	public function hasPermission() {
		return true;
	}

	/**
	 * getDefaultView
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function getDefaultView() {
		return XUPDATE_FRAME_VIEW_NONE;
	}

	/**
	 * execute
	 *
	 * @param void
	 *
	 * @return  Enum
	 **/
	public function execute() {
		return XUPDATE_FRAME_VIEW_NONE;
	}

	/**
	 * executeViewSuccess
	 *
	 * @param XCube_RenderTarget  &$render
	 *
	 * @return  void
	 **/
	public function executeViewSuccess( /*** XCube_RenderTarget ***/ &$render ) {
	}

	/**
	 * executeViewError
	 *
	 * @param XCube_RenderTarget  &$render
	 *
	 * @return  void
	 **/
	public function executeViewError( /*** XCube_RenderTarget ***/ &$render ) {
	}

	/**
	 * executeViewIndex
	 *
	 * @param XCube_RenderTarget  &$render
	 *
	 * @return  void
	 **/
	public function executeViewIndex( /*** XCube_RenderTarget ***/ &$render ) {
	}

	/**
	 * executeViewInput
	 *
	 * @param XCube_RenderTarget  &$render
	 *
	 * @return  void
	 **/
	public function executeViewInput( /*** XCube_RenderTarget ***/ &$render ) {
	}

	/**
	 * executeViewPreview
	 *
	 * @param XCube_RenderTarget  &$render
	 *
	 * @return  void
	 **/
	public function executeViewPreview( /*** XCube_RenderTarget ***/ &$render ) {
	}

	/**
	 * executeViewCancel
	 *
	 * @param XCube_RenderTarget  &$render
	 *
	 * @return  void
	 **/
	public function executeViewCancel( /*** XCube_RenderTarget ***/ &$render ) {
	}

	protected function modalBoxJs() {
		return <<<EOD
jQuery(document).ready(function($) {
    //select all the a tag with name equal to modal
    var isCancel = false;
    $('#contentBody form [name=_form_control_cancel]').click(function(){
    	isCancel = true;
    });
    $('#contentBody form').submit(function(e) {
        if (isCancel) {
        	return;
        }
        
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
});
EOD;
	}

	/**
	 * Remove html/install & chmod mainfile.php 0404
	 *
	 * @return bool
	 */
	protected function _removeInstallDir() {
		$ret = false;
		if ( $this->Ftp->app_login() ) {
			// enable protector in mainfile.php
			$this->Func->write_mainfile_protector();

			// write protect mainfile.php
			$this->Func->mainfile_to_readonly();

			// remove install directory
			$this->Ftp->localRmdirRecursive( XOOPS_ROOT_PATH . '/install' );

			// set writable "mod_config['temp_path']"
			if ( ! $this->Xupdate->params['is_writable']['result'] ) {
				$this->Ftp->localChmod( $this->Xupdate->params['is_writable']['path'], _MD_XUPDATE_WRITABLE_DIR_PERM_T );
			}

			// set writable protector config dir
			$protector_config = XOOPS_TRUST_PATH . '/modules/protector/configs';
			if ( ! Xupdate_Utils::checkDirWritable( $protector_config ) ) {
				$this->Ftp->localChmod( $protector_config, _MD_XUPDATE_WRITABLE_DIR_PERM_T );
			}

			clearstatcache();

			// edit /preload/CorePackPreload.class.php
			$src = file_get_contents( XOOPS_ROOT_PATH . '/preload/CorePackPreload.class.php' );
			if ( ! is_dir( XOOPS_ROOT_PATH . '/install' ) && ! preg_match( '/define\s*\(\'XUPDATE_INSTALLERCHECKER_ACTIVE\'/', $src ) ) {
				$ret        = true;
				$add        = '

// Already checked with X-update install checker
define(\'XUPDATE_INSTALLERCHECKER_ACTIVE\', false);';
				$src        = str_replace( '<?php', '<?php' . $add, $src );
				$sourcePath = $this->Xupdate->params['is_writable']['path'] . '/preload';
				$this->Ftp->localMkdir( $sourcePath );
				$this->Ftp->localChmod( $sourcePath, _MD_XUPDATE_WRITABLE_DIR_PERM_T );
				file_put_contents( $sourcePath . '/CorePackPreload.class.php', $src );
				$this->Ftp->uploadNakami( $sourcePath, XOOPS_ROOT_PATH . '/preload/' );
				$this->Ftp->localRmdirRecursive( $sourcePath );
			}

			$this->Ftp->app_logout();
		}

		return $ret;
	}
}
