<?php

$mytrustdirname = basename( dirname( dirname( __FILE__ ) ) ) ;
$mytrustdirpath = dirname( dirname( __FILE__ ) ) ;

// check permission of 'module_read' of this module
// (already checked by common.php)

$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
require_once( $langmanpath ) ;
$langman =& D3LanguageManager::getInstance() ;
$langman->read( 'main.php' , $mydirname , $mytrustdirname ) ;


require_once XOOPS_TRUST_PATH.'/modules/d3pipes/include/common_functions.php' ;

// mod_config
$module_handler =& xoops_gethandler('module');
$module =& $module_handler->getByDirname($mydirname);
$config_handler =& xoops_gethandler('config');
$configs = $config_handler->getConfigList( $module->mid() ) ;

// force to remove all cache of all pipes
d3pipes_common_delete_all_cache( $mydirname , 0 , true , false ) ;

// pipes loop
$db =& Database::getInstance() ;
$result = $db->query( "SELECT pipe_id FROM ".$db->prefix($mydirname."_pipes") ) ;
while( list( $pipe_id ) = $db->fetchRow( $result ) ) {
	$pipe4assign = d3pipes_common_get_pipe4assign( $mydirname , intval( $pipe_id ) ) ;
	d3pipes_common_fetch_entries( $mydirname , $pipe4assign , $configs['entries_per_eachpipe'] , $errors , $configs ) ;
}


?>