<?php
/**
 * Protector module for XCL
 *
 * @package    Protector
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

// Abstract of each filter classes
class ProtectorFilterAbstract {
	public $protector = null;

	public function __construct() {
		$this->protector = Protector::getInstance();
		$lang            = empty( $GLOBALS['xoopsConfig']['language'] ) ? @$this->protector->_conf['default_lang'] : $GLOBALS['xoopsConfig']['language'];
		@include_once dirname( __DIR__ ) . '/language/' . $lang . '/main.php';
		if ( ! defined( '_MD_PROTECTOR_YOUAREBADIP' ) ) {
			include_once dirname( __DIR__ ) . '/language/english/main.php';
		}
	}

	public function isMobile() {
		if ( class_exists( 'Wizin_User' ) ) {
			// WizMobile (gusagi)
			$user =& Wizin_User::getSingleton();

			return $user->bIsMobile;
		} elseif ( defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER ) {
			// hyp_common ktai-renderer (nao-pon)
			return true;
		} else {
			return false;
		}
	}
}


// Filter Handler class (singleton)
class ProtectorFilterHandler {
	public $protector = null;
	public $filters_base = '';
	public $filters_byconfig = '';

	public function __construct() {
		$this->protector        = Protector::getInstance();
		$this->filters_base     = dirname( __DIR__ ) . '/filters_enabled';
		$this->filters_byconfig = dirname( __DIR__ ) . '/filters_byconfig';
	}

	public static function &getInstance() {
		static $instance;
		if ( ! isset( $instance ) ) {
			$instance = new ProtectorFilterHandler();
		}

		return $instance;
	}

	// return: false : execute default action
	public function execute( $type ) {
		$ret = 0;

		$filters = [];

		// parse $protector->_conf['filters']
		foreach ( preg_split( '/[\s\n,]+/', $this->protector->_conf['filters'] ) as $file ) {
			if ( '.php' != substr( $file, - 4 ) ) {
				$file .= '.php';
			}
			if ( 0 === strncmp( $file, $type . '_', strlen( $type ) + 1 ) ) {
				$filters[] = [ 'file' => $file, 'base' => $this->filters_byconfig ];
			}
		}

		// search from filters_enabled/
		$dh = opendir( $this->filters_base );
		while ( false !== ( $file = readdir( $dh ) ) ) {
			if ( 0 === strncmp( $file, $type . '_', strlen( $type ) + 1 ) ) {
				$filters[] = [ 'file' => $file, 'base' => $this->filters_base ];
			}
		}
		closedir( $dh );

		// execute the filters
		foreach ( $filters as $filter ) {
			include_once $filter['base'] . '/' . $filter['file'];
			$plugin_name = 'protector_' . substr( $filter['file'], 0, - 4 );
			if ( function_exists( $plugin_name ) ) {
				// old way
				$ret |= call_user_func( $plugin_name );
			} elseif ( class_exists( $plugin_name ) ) {
				// newer way
				$plugin_obj = new $plugin_name();
				$ret        |= $plugin_obj->execute();
			}
		}

		return $ret;
	}
}
