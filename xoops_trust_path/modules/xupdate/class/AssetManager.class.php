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

/**
 * Xupdate_AssetManager
 **/
class Xupdate_AssetManager {
	/*** string ***/
	public $mDirname = '';

	/*** string ***/
	public $mTrustDirname = 'xupdate';

	/*** string[][][] ***/
	public $mAssetList = [];

	/*** object[][] ***/
	private $_mCache = [];

	/**
	 * __construct
	 *
	 * @param string $dirname
	 *
	 * @return  void
	 **/
	public function __construct( /*** string ***/ $dirname ) {
		$this->mDirname = $dirname;
	}

	/**
	 * &getInstance
	 *
	 * @param string $dirname
	 *
	 * @return  Xupdate_AssetManager
	 **/
	public static function &getInstance( /*** string ***/ $dirname ) {
		/**
		 * @var    Xupdate_AssetManager[]
		 **/
		static $instance = [];

		if ( ! isset( $instance[ $dirname ] ) ) {
			$instance[ $dirname ] = new self( $dirname );
		}

		return $instance[ $dirname ];
	}

	/**
	 * &getObject
	 *
	 * @param string $type
	 * @param string $name
	 * @param bool $isAdmin
	 * @param string $mode
	 *
	 * @return mixed|object|null &object<XCube_ActionFilter,XCube_ActionForm,XoopsObjectGenericHandler>
	 */
	public function &getObject( /*** string ***/ $type, /*** string ***/ $name, /*** bool ***/ $isAdmin = false, /*** string ***/ $mode = null ) {
		if ( isset( $this->_mCache[ $type ][ $name ] ) ) {
			return $this->_mCache[ $type ][ $name ];
		}

		$instance = null;

		$methodName = 'create' . ucfirst( $name ) . ucfirst( $mode ) . ucfirst( $type );
		if ( method_exists( $this, $methodName ) ) {
			$instance =& $this->$methodName();
		}

		if ( null === $instance ) {
			$instance =& $this->_fallbackCreate( $type, $name, $isAdmin, $mode );
		}

		$this->_mCache[ $type ][ $name ] =& $instance;

		return $instance;
	}

	/**
	 * getRoleName
	 *
	 * @param string $role
	 *
	 * @return  string
	 **/
	public function getRoleName( /*** string ***/ $role ) {
		return 'Module.' . $this->mDirname . '.' . $role;
	}

	/**
	 * @public
	 *
	 * @param $type
	 * @param $name
	 *
	 * @return mixed|object|null
	 */
	public function &load( $type, $name ) {
		if ( isset( $this->_mCache[ $type ][ $name ] ) ) {
			return $this->_mCache[ $type ][ $name ];
		}

		return $this->create( $type, $name );
	}

	/**
	 * @public
	 *
	 * @param $type
	 * @param $name
	 *
	 * @return object|null
	 */
	public function &create( $type, $name ) {
		$instance = null;

		// TODO:Insert your creation code.

		// fallback
		if ( null === $instance ) {
			$instance =& $this->_fallbackCreate( $type, $name );
		}

		$this->_mCache[ $type ][ $name ] =& $instance;

		return $instance;
	}

	/**
	 * &_fallbackCreate
	 *
	 * @param string $type
	 * @param string $name
	 * @param bool $isAdmin
	 * @param string $mode
	 *
	 * @return null &object<XCube_ActionFilter,XCube_ActionForm,XoopsObjectGenericHandler>
	 */
	private function &_fallbackCreate( /*** string ***/ $type, /*** string ***/ $name, /*** bool ***/ $isAdmin = false, /*** string ***/ $mode = null ) {
		$className = null;
		$instance  = null;

		if ( isset( $this->mAssetList[ $type ][ $name ]['class'] ) ) {
			$asset = $this->mAssetList[ $type ][ $name ];
			if ( isset( $asset['absPath'] ) && $this->_loadClassFile( $asset['absPath'], $asset['class'] ) ) {
				$className = $asset['class'];
			}

			if ( null == $className && isset( $asset['path'] ) ) {
				if ( $this->_loadClassFile( $this->_getPublicPath() . $asset['path'], $asset['class'] ) ) {
					$className = $asset['class'];
				}

				if ( null == $className && $this->_loadClassFile( $this->_getTrustPath() . $asset['path'], $asset['class'] ) ) {
					$className = $asset['class'];
				}
			}
		}

		if ( null == $className ) {
			switch ( $type ) {
				case 'filter':
					$className = $this->_getFilterName( $name, $isAdmin );
					break;
				case 'form':
					$className = $this->_getActionFormName( $name, $isAdmin, $mode );
					break;
				case 'handler':
					$className = $this->_getHandlerName( $name );
					break;
				default:
					return $instance;
			}
		}

		if ( 'handler' === $type ) {
			$root     =& XCube_Root::getSingleton();
			$instance = new $className( $root->mController->getDB(), $this->mDirname );
		} else {
			$instance = new $className();
		}

		return $instance;
	}

	/**
	 * _getFilterName
	 *
	 * @param string $name
	 * @param bool $isAdmin
	 *
	 * @return  string
	 **/
	private function _getFilterName( /*** string ***/ $name, /*** bool ***/ $isAdmin = false ) {
		$name      = ucfirst( $name ) . 'FilterForm';
		$path      = 'forms/' . $name . '.class.php';
		$className = ucfirst( $this->mTrustDirname ) . ( $isAdmin ? '_Admin_' : '_' ) . $name;

		return (
			$this->_loadClassFile( $this->_getPublicPath( $isAdmin ) . $path, $className ) ||
			$this->_loadClassFile( $this->_getTrustPath( $isAdmin ) . $path, $className )
		) ? $className : null;
	}

	/**
	 * _getActionFormName
	 *
	 * @param string $name
	 * @param bool $isAdmin
	 * @param string $mode
	 *
	 * @return  string
	 **/
	private function _getActionFormName( /*** string ***/ $name, /*** bool ***/ $isAdmin = false, /*** string ***/ $mode = null ) {
		$name      = ucfirst( $name ) . ucfirst( $mode ) . 'Form';
		$path      = 'forms/' . $name . '.class.php';
		$className = ucfirst( $this->mTrustDirname ) . ( $isAdmin ? '_Admin_' : '_' ) . $name;

		return (
			$this->_loadClassFile( $this->_getPublicPath( $isAdmin ) . $path, $className ) ||
			$this->_loadClassFile( $this->_getTrustPath( $isAdmin ) . $path, $className )
		) ? $className : null;
	}

	/**
	 * _getHandlerName
	 *
	 * @param string $name
	 *
	 * @return  string
	 **/
	private function _getHandlerName( /*** string ***/ $name ) {
		$path      = 'class/handler/' . ucfirst( $name ) . '.class.php';
		$className = ucfirst( $this->mTrustDirname ) . '_' . ucfirst( $name ) . 'Handler';

		return (
			$this->_loadClassFile( $this->_getPublicPath() . $path, $className ) ||
			$this->_loadClassFile( $this->_getTrustPath() . $path, $className )
		) ? $className : null;
	}

	/**
	 * _loadClassFile
	 *
	 * @param string $path
	 * @param string $class
	 *
	 * @return  bool
	 **/
	private function _loadClassFile( /*** string ***/ $path, /*** string ***/ $class ) {
		if ( ! file_exists( $path ) ) {
			return false;
		}
		require_once $path;

		return class_exists( $class );
	}

	/**
	 * _getPublicPath
	 *
	 * @param bool $isAdmin
	 *
	 * @return  string
	 **/
	private function _getPublicPath( /*** bool ***/ $isAdmin = false ) {
		return XOOPS_MODULE_PATH . '/' . $this->mDirname . ( $isAdmin ? '/admin/' : '/' );
	}

	/**
	 * _getTrustPath
	 *
	 * @param bool $isAdmin
	 *
	 * @return  string
	 **/
	private function _getTrustPath( /*** bool ***/ $isAdmin = false ) {
		return XUPDATE_TRUST_PATH . ( $isAdmin ? '/admin/' : '/' );
	}
}
