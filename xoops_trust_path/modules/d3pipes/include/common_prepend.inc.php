<?php

require_once dirname(__FILE__).'/main_functions.php' ;
require_once dirname(__FILE__).'/common_functions.php' ;
require_once XOOPS_TRUST_PATH.'/libs/altsys/include/altsys_functions.php' ;
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;

$myts =& MyTextSanitizer::getInstance() ;
$db =& Database::getInstance() ;

// init xoops_breadcrumbs
$xoops_breadcrumbs[0] = array( 'url' => XOOPS_URL.'/modules/'.$mydirname.'/index.php' , 'name' => $xoopsModule->getVar( 'name' ) ) ;

if( altsys_get_core_type() == ALTSYS_CORE_TYPE_X20S ) {
	// patch for XOOPS 2.0.14/15/16 (what a silly core...)
	$D3Tpl = new D3Tpl() ;
	require_once SMARTY_CORE_DIR . 'core.load_resource_plugin.php' ;
	smarty_core_load_resource_plugin( array('type' => 'db') , $D3Tpl ) ;
}

?>