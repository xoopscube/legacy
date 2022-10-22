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

require_once XUPDATE_TRUST_PATH . '/class/AbstractAction.class.php';

define( 'XUPDATE_FRAME_PERFORM_SUCCESS', 1 );
define( 'XUPDATE_FRAME_PERFORM_FAIL', 2 );
define( 'XUPDATE_FRAME_INIT_SUCCESS', 3 );

define( 'XUPDATE_FRAME_VIEW_NONE', 'none' );
define( 'XUPDATE_FRAME_VIEW_SUCCESS', 'success' );
define( 'XUPDATE_FRAME_VIEW_ERROR', 'error' );
define( 'XUPDATE_FRAME_VIEW_INDEX', 'index' );
define( 'XUPDATE_FRAME_VIEW_INPUT', 'input' );
define( 'XUPDATE_FRAME_VIEW_PREVIEW', 'preview' );
define( 'XUPDATE_FRAME_VIEW_CANCEL', 'cancel' );

/**
 * Xupdate_Module
 **/
class Xupdate_Module extends Legacy_ModuleAdapter {
	/*** string ***/
	public $mActionName = null;

	/*** Xupdate_AbstractAction ***/
	public $mAction = null;

	/*** bool ***/
	public $mAdminFlag = false;

	/*** Xupdate_AssetManager ***/
	public $mAssetManager = null;

	/*** string ***/
	protected $_mPreferenceEditUrl = null;

	/*** string ***/
	protected $_mHelpViewUrl = null;

	/*** Enum[] ***/
	protected $_mAllowViewNames = [
		XUPDATE_FRAME_VIEW_NONE,
		XUPDATE_FRAME_VIEW_SUCCESS,
		XUPDATE_FRAME_VIEW_ERROR,
		XUPDATE_FRAME_VIEW_INDEX,
		XUPDATE_FRAME_VIEW_INPUT,
		XUPDATE_FRAME_VIEW_PREVIEW,
		XUPDATE_FRAME_VIEW_CANCEL
	];

	/**
	 * startup
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	public function startup() {
		parent::startup();

		XCube_DelegateUtils::call( 'Module.xupdate.Global.Event.GetAssetManager', new XCube_Ref( $this->mAssetManager ), $this->mXoopsModule->get( 'dirname' ) );

		$root =& XCube_Root::getSingleton();
		$root->mController->mExecute->add( [ &$this, 'execute' ] );

		//
		// TODO/Insert your initialization code.
		//
	}

	/**
	 * setAdminMode
	 *
	 * @param bool $flag
	 *
	 * @return  void
	 **/
	public function setAdminMode( /*** bool ***/ $flag ) {
		$this->mAdminFlag = $flag;
	}

	/**
	 * _getDefaultActionName
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	private function _getDefaultActionName() {
		// TODO insert your default action name
//		return 'index.php?action=StoreList';
		return 'StoreList';
	}

	/**
	 * setActionName
	 *
	 * @param string $name
	 *
	 * @return  void
	 **/
	public function setActionName( /*** string ***/ $name ) {
		$this->mActionName = $name;
	}

	/**
	 * getRenderSystemName
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	public function getRenderSystemName() {
		if ( ! $this->mAdminFlag ) {
			return parent::getRenderSystemName();
		}

		// TODO will be use site config
		if ( ! defined( 'XUPDATE_ADMIN_RENDER_REGISTED' ) ) {
			define( 'XUPDATE_ADMIN_RENDER_REGISTED', true );
			$root =& XCube_Root::getSingleton();
			$root->overrideSiteConfig(
				[
					'RenderSystems'             => [
						'Xupdate_AdminRenderSystem' => 'Xupdate_AdminRenderSystem'
					],
					'Xupdate_AdminRenderSystem' => [
						'root'  => XUPDATE_TRUST_PATH,
						'path'  => '/admin/class/XupdateAdminRenderSystem.class.php',
						'class' => 'Xupdate_AdminRenderSystem'
					]
				]
			);
		}

		return 'Xupdate_AdminRenderSystem';
	}

	/**
	 * getAdminMenu
	 *
	 * @return array|null {string 'title',string 'link',string 'keywords',bool 'show',bool 'absolute'}[]
	 */
	public function getAdminMenu() {
		if ( is_array( $this->mAdminMenu ) ) {
			return $this->mAdminMenu;
		}

		$root =& XCube_Root::getSingleton();

		// load admin menu
		$adminMenu = $this->mXoopsModule->getInfo( 'adminmenu' );
		if ( ! is_array( $adminMenu ) ) {
			$adminMenu = [];
		}

		// add preference menu
		if ( $url = $this->getPreferenceEditUrl() ) {
			$adminMenu[] = [
				'title'    => _PREFERENCES,
				'link'     => $url,
				'absolute' => true
			];
		}

		// add help menu
		if ( $url = $this->getHelpViewUrl() ) {
			$adminMenu[] = [
				'title'    => _HELP,
				'link'     => $url,
				'absolute' => true
			];
		}

		$this->mAdminMenu = [];
		foreach ( $adminMenu as $menu ) {
			if ( ! ( isset( $menu['absolute'] ) && $menu['absolute'] ) ) {
				$menu['link'] = XOOPS_MODULE_URL . '/' . $this->mXoopsModule->get( 'dirname' ) . '/' . $menu['link'];
			}
			$this->mAdminMenu[] = $menu;
		}

		return $this->mAdminMenu;
	}

	/**
	 * getPreferenceEditUrl
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	public function getPreferenceEditUrl() {
		if ( null === $this->_mPreferenceEditUrl ) {
			if ( is_array( $this->mXoopsModule->getInfo( 'config' ) ) && count( $this->mXoopsModule->getInfo( 'config' ) ) > 0 ) {
				$root                      =& XCube_Root::getSingleton();
				$this->_mPreferenceEditUrl = $root->mController->getPreferenceEditUrl( $this->mXoopsModule );
			} else {
				$this->_mPreferenceEditUrl = false;
			}
		}

		return $this->_mPreferenceEditUrl;
	}

	/**
	 * getHelpViewUrl
	 *
	 * @param void
	 *
	 * @return  string
	 **/
	public function getHelpViewUrl() {
		if ( null === $this->_mHelpViewUrl ) {
			if ( $this->mXoopsModule->hasHelp() ) {
				$root                =& XCube_Root::getSingleton();
				$this->_mHelpViewUrl = $root->mController->getHelpViewUrl( $this->mXoopsModule );
			} else {
				$this->_mHelpViewUrl = false;
			}
		}

		return $this->_mHelpViewUrl;
	}

	/**
	 * execute
	 *
	 * @param XCube_Controller  &$controller
	 *
	 * @return  void
	 **/
	public function execute( /*** XCube_Controller ***/ &$controller ) {
		if ( false === $this->_createAction() ) {
			$this->doActionNotFoundError();
			die();
		}

		if ( false === $this->mAction->prepare() ) {
			$this->doPreparationError();
			die();
		}

		if ( false === $this->mAction->hasPermission() ) {
			$this->doPermissionError();
			die();
		}

		$viewStatus = ( 'POST' === Xupdate_Utils::getEnv( 'REQUEST_METHOD' ) ) ?
			$this->mAction->execute() :
			$this->mAction->getDefaultView();

		if ( in_array( $viewStatus, $this->_mAllowViewNames ) ) {
			$methodName = 'executeView' . ucfirst( $viewStatus );
			if ( is_callable( [ $this->mAction, $methodName ] ) ) {
				$render = $this->getRenderTarget();
				$this->mAction->$methodName( $render );
				$render->setAttribute( 'xoops_pagetitle', $this->mAction->getPagetitle() );
				$this->mAction->setHeaderScript();
			}
		}
	}

	/**
	 * _createAction
	 *
	 * @param void
	 *
	 * @return  bool
	 **/
	private function _createAction() {
		$root =& XCube_Root::getSingleton();

		if ( null == $this->mActionName ) {
			$this->mActionName = $root->mContext->mRequest->getRequest( 'action' );
			if ( null == $this->mActionName ) {
				$this->mActionName = $this->_getDefaultActionName();
			}
		}

		if ( ! ctype_alnum( $this->mActionName ) ) {
			return false;
		}

		$fileName = ( $this->mAdminFlag ? '/admin' : '' )
		            . '/actions/' . ucfirst( $this->mActionName ) . 'Action.class.php';
		switch ( true ) {
			case file_exists(
				$path = XOOPS_MODULE_PATH . '/' . $this->mXoopsModule->get( 'dirname' ) . $fileName
			):
				break;
			case file_exists(
				$path = XUPDATE_TRUST_PATH . '/' . $fileName
			):
				break;
			default:
				return false;
		}

		require_once $path;

		$className = 'Xupdate_' . ( $this->mAdminFlag ? 'Admin_' : '' )
		             . ucfirst( $this->mActionName ) . 'Action';
		if ( class_exists( $className ) ) {
			$this->mAction = new $className();
		}
		if ( ! $this->mAction instanceof Xupdate_AbstractAction ) {
			return false;
		}

		return true;
	}

	/**
	 * doActionNotFoundError
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	private function doActionNotFoundError() {
		/**
		 * Module.xupdate.Global.Event.Exception.ActionNotFound
		 *
		 * @param string $dirname
		 *
		 * @return  void
		 **/
		XCube_DelegateUtils::call( 'Module.xupdate.Global.Event.Exception.ActionNotFound', $this->mAssetManager->mDirname );
		/**
		 * Module.{dirname}.Event.Exception.ActionNotFound
		 *
		 * @param void
		 *
		 * @return  void
		 **/
		XCube_DelegateUtils::call( 'Module.' . $this->mXoopsModule->get( 'dirname' ) . '.Event.Exception.ActionNotFound' );
		$root =& XCube_Root::getSingleton();
		$root->mController->executeForward( XOOPS_URL );
	}

	/**
	 * doPreparationError
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	private function doPreparationError() {
		/**
		 * Module.xupdate.Global.Event.Exception.Preparation
		 *
		 * @param string $dirname
		 *
		 * @return  void
		 **/
		XCube_DelegateUtils::call( 'Module.xupdate.Global.Event.Exception.Preparation', $this->mAssetManager->mDirname );
		/**
		 * Module.{dirname}.Event.Exception.Preparation
		 *
		 * @param void
		 *
		 * @return  void
		 **/
		XCube_DelegateUtils::call( 'Module.' . $this->mXoopsModule->get( 'dirname' ) . '.Event.Exception.Preparation' );
		$root =& XCube_Root::getSingleton();
		$root->mController->executeForward( XOOPS_URL );
	}

	/**
	 * doPermissionError
	 *
	 * @param void
	 *
	 * @return  void
	 **/
	private function doPermissionError() {
		/**
		 * Module.xupdate.Global.Event.Exception.Permission
		 *
		 * @param string $dirname
		 *
		 * @return  void
		 **/
		XCube_DelegateUtils::call( 'Module.xupdate.Global.Event.Exception.Permission', $this->mAssetManager->mDirname );
		/**
		 * Module.{dirname}.Event.Exception.Permission
		 *
		 * @param void
		 *
		 * @return  void
		 **/
		XCube_DelegateUtils::call( 'Module.' . $this->mXoopsModule->get( 'dirname' ) . '.Event.Exception.Permission' );
		$root =& XCube_Root::getSingleton();
		$root->mController->executeForward( XOOPS_URL );
	}
}
