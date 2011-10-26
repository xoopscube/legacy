<?php
// ------------------------------------------------------------------------- //
//                       myblocksadmin.php (altsys)                          //
//                - XOOPS block admin for each modules -                     //
//                       GIJOE <http://www.peak.ne.jp/>                      //
// ------------------------------------------------------------------------- //

require_once dirname(__FILE__).'/class/AltsysBreadcrumbs.class.php' ;
require_once dirname(__FILE__).'/include/gtickets.php' ;
include_once dirname(__FILE__).'/include/altsys_functions.php' ;
include_once dirname(__FILE__).'/include/mygrouppermform.php' ;
include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;


// language file
altsys_include_language_file( 'myblocksadmin' ) ;

// fork by core types
switch( altsys_get_core_type() ) {
	case ALTSYS_CORE_TYPE_X22 :
		include_once dirname(__FILE__).'/class/MyBlocksAdminForX22.class.php' ;
		$myba =& MyBlocksAdminForX22::getInstance() ;
		break ;
	case ALTSYS_CORE_TYPE_XCL21 :
		include_once dirname(__FILE__).'/class/MyBlocksAdminForXCL21.class.php' ;
		$myba =& MyBlocksAdminForXCL21::getInstance() ;
		break ;
	case ALTSYS_CORE_TYPE_ICMS :
		include_once dirname(__FILE__).'/class/MyBlocksAdminForICMS.class.php' ;
		$myba =& MyBlocksAdminForICMS::getInstance() ;
		break ;
	case ALTSYS_CORE_TYPE_X20S :
	case ALTSYS_CORE_TYPE_X23P :
		include_once dirname(__FILE__).'/class/MyBlocksAdminForX20S.class.php' ;
		$myba =& MyBlocksAdminForX20S::getInstance() ;
		break ;
	default :
		include_once dirname(__FILE__).'/class/MyBlocksAdmin.class.php' ;
		$myba =& MyBlocksAdmin::getInstance() ;
		break ;
}

// permission
$myba->checkPermission() ;

// set parameters target_mid , target_dirname etc.
$myba->init( $xoopsModule ) ;


//
// transaction stage
//

if( ! empty( $_POST ) ) {
	$myba->processPost() ;
}

//
// form stage
//

// header
xoops_cp_header() ;

// mymenu
altsys_include_mymenu() ;

$myba->processGet() ;

// footer
xoops_cp_footer() ;


?>