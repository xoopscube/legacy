<?php
//ini_set( 'display_errors', 1 );
//error_reporting(E_ALL);
if (! defined('HYP_COMMON_PRELOAD_CONF')) die('HypCommonPreLoad not found or that is old version.');

if (! defined('_GLOBAL_LEFT')) define('_GLOBAL_LEFT', 'left');

require_once dirname(__FILE__).'/class/gtickets.php' ;

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

// environment
require_once XOOPS_ROOT_PATH.'/class/template.php' ;
$module_handler =& xoops_gethandler( 'module' ) ;
$xoopsModule =& $module_handler->getByDirname( $mydirname ) ;
$config_handler =& xoops_gethandler( 'config' ) ;
$xoopsModuleConfig =& $config_handler->getConfigsByCat( 0 , $xoopsModule->getVar( 'mid' ) ) ;

// check permission of 'module_admin' of this module
$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;
if( ! is_object( @$xoopsUser ) || ! $moduleperm_handler->checkRight( 'module_admin' , $xoopsModule->getVar( 'mid' ) , $xoopsUser->getGroups() ) ) die( 'only admin can access this area' ) ;

$xoopsOption['pagetype'] = 'admin' ;
require XOOPS_ROOT_PATH.'/include/cp_functions.php' ;

// initialize language manager
$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;

include_once dirname(__FILE__).'/include/admin_func.php' ;

if( ! empty( $_GET['lib'] ) ) {
	// common libs (eg. altsys)
	$lib = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , $_GET['lib'] ) ;
	$page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] ) ;

//	if ($lib === 'altsys' && $page === 'mypreferences' && (empty($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], $_SERVER['REQUEST_URI']) !== false)) {
//		header('Location: ' . XOOPS_URL  . '/modules/' . $mydirname . '/admin/index.php');
//		exit();
//	}

	// check the page can be accessed (make controllers.php just under the lib)
	$controllers = array() ;
	if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/controllers.php' ) ) {
		require XOOPS_TRUST_PATH.'/libs/'.$lib.'/controllers.php' ;
		if( ! in_array( $page , $controllers ) ) $page = $controllers[0] ;
	}

	if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/'.$page.'.php' ;
	} else if( file_exists( XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ) ) {
		include XOOPS_TRUST_PATH.'/libs/'.$lib.'/index.php' ;
	} else {
		die( 'wrong request' ) ;
	}
} else {
	$constpref = '_MI_' . strtoupper( $mydirname ) ;

	// load language files (main.php & admin.php)
	$langman->read('modinfo.php', $mydirname, $mytrustdirname);

	if (!empty($_POST)) {
		if ( ! $xoopsGTicket->check( true , $mydirname ) ) {
			redirect_header(XOOPS_URL.'/',3,$xoopsGTicket->getErrors());
		}
		if (isset($_POST['page'])) $_GET['page'] = $_POST['page'];
	}

	// fork each pages of this module
	$page = preg_replace( '/[^a-zA-Z0-9_-]/' , '' , @$_GET['page'] );

	if( file_exists( "$mytrustdirpath/admin/$page.php" ) ) {

		// load $config
		include "$mytrustdirpath/admin/$page.php" ;

		$op = '';
		if (isset($_POST) && isset($_POST['op'])) {
			$op = $_POST['op'];
		}

		if ($op === 'save') {
			hypconfSaveConf($config); // send redirect header
			exit();

		} else {

			hypconfSetValue($config, $page);

			// display form
			xoops_cp_header();

			echo '<style type="text/css">' .
					'form label {display:inline-block;width:30%;vertical-align:top;margin:5px;}' .
					'form td div {max-height:15em;overflow:auto;}' .
					'form td.head {white-space:nowrap;vertical-align:middle !important}' .
					'form textarea {width:100%;}' .
					'form td.odd {padding-left:2em;border-bottom:2px solid;}' .
					'</style>';

			include dirname(__FILE__).'/admin/mymenu.php';

			echo '<h3 style="text-align:'._GLOBAL_LEFT.';">'.hypconf_constant($constpref . '_DESC')."</h3>\n" ;

			hypconfShowForm($config);

			xoops_cp_footer();

			exit();
		}

	} else if( file_exists( "$mytrustdirpath/admin/index.php" ) ) {
		include "$mytrustdirpath/admin/index.php" ;
	} else {
		die( 'wrong request' ) ;
	}
}
