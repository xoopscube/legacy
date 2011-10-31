<?php
include("admin_header.php");

xoops_cp_header();
include dirname(__FILE__).'/mymenu.php' ;
gnavi_opentable() ;

restore_error_handler() ;
error_reporting( E_ALL ) ;

if( imagecreatetruecolor(200,200) ) {
	echo _MD_A_GNAVI_MB_GD2SUCCESS ;
}

gnavi_closetable() ;
xoops_cp_footer();

?>