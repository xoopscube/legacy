<?php

// Abstract of each filter classes
class ProtectorFilterAbstract {
	var $protector = null ;

	function ProtectorFilterAbstract()
	{
		$this->protector =& Protector::getInstance() ;
		$lang = empty( $GLOBALS['xoopsConfig']['language'] ) ? @$this->protector->_conf['default_lang'] : $GLOBALS['xoopsConfig']['language'] ;
		@include_once dirname(dirname(__FILE__)).'/language/'.$lang.'/main.php' ;
		if( ! defined( '_MD_PROTECTOR_YOUAREBADIP' ) ) {
			include_once dirname(dirname(__FILE__)).'/language/english/main.php' ;
		}
	}

	function isMobile()
	{
		if( class_exists( 'Wizin_User' ) ) {
			// WizMobile (gusagi)
			$user =& Wizin_User::getSingleton();
			return $user->bIsMobile ;
		} else if( defined( 'HYP_K_TAI_RENDER' ) && HYP_K_TAI_RENDER ) {
			// hyp_common ktai-renderer (nao-pon)
			return true ;
		} else {
			return false ;
		}
	}
}


// Filter Handler class (singleton)
class ProtectorFilterHandler {
	var $protector = null ;
	var $filters_base = '' ;
	var $filters_byconfig = '' ;

	function ProtectorFilterHandler()
	{
		$this->protector =& Protector::getInstance() ;
		$this->filters_base = dirname(dirname(__FILE__)).'/filters_enabled' ;
		$this->filters_byconfig = dirname(dirname(__FILE__)).'/filters_byconfig' ;
	}

	public static function &getInstance()
	{
		static $instance ;
		if( ! isset( $instance ) ) {
			$instance = new ProtectorFilterHandler() ;
		}
		return $instance ;
	}

	// return: false : execute default action
	function execute( $type )
	{
		$ret = 0 ;

		$filters = array() ;

		// parse $protector->_conf['filters']
		foreach( preg_split( '/[\s\n,]+/' , $this->protector->_conf['filters'] ) as $file ) {
			if( substr( $file , -4 ) != '.php' ) $file .= '.php' ;
			if( strncmp( $file , $type.'_' , strlen( $type ) + 1 ) === 0 ) {
				$filters[] = array( 'file' => $file , 'base' => $this->filters_byconfig ) ;
			}
		}

		// search from filters_enabled/
		$dh = opendir( $this->filters_base ) ;
		while( ( $file = readdir( $dh ) ) !== false ) {
			if( strncmp( $file , $type.'_' , strlen( $type ) + 1 ) === 0 ) {
				$filters[] = array( 'file' => $file , 'base' => $this->filters_base ) ;
			}
		}
		closedir( $dh ) ;

		// execute the filters
		foreach( $filters as $filter ) {
			include_once $filter['base'].'/'.$filter['file'] ;
			$plugin_name = 'protector_'.substr($filter['file'],0,-4) ;
			if( function_exists( $plugin_name ) ) {
				// old way
				$ret |= call_user_func( $plugin_name ) ;
			} else if( class_exists( $plugin_name ) ) {
				// newer way
				$plugin_obj = new $plugin_name() ;
				$ret |= $plugin_obj->execute() ;
			}
		}

		return $ret ;
	}
}

?>