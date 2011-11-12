<?php

include "admin_header.php" ;
require_once( 'mygrouppermform.php' ) ;

// check $xoopsModule
if( ! is_object( $xoopsModule ) ) redirect_header( "$mod_url/" , 1 , _NOPERM ) ;

// language files
if(defined( 'XOOPS_CUBE_LEGACY' )){
	// Cube Legacy without altsys
	include_once( XOOPS_ROOT_PATH."/modules/legacy/language/".$xoopsConfig['language']."/admin.php" ) ;
} else {
	// conventinal X2
	include_once( XOOPS_ROOT_PATH."/modules/system/language/".$xoopsConfig['language']."/admin.php" ) ;
}


function list_groups()
{
	global $xoopsModule ;

	$global_perms_array = array(
		GNAV_GPERM_INSERTABLE => _GNAV_GPERM_G_INSERTABLE ,
		GNAV_GPERM_SUPERINSERT | GNAV_GPERM_INSERTABLE => _GNAV_GPERM_G_SUPERINSERT ,
		GNAV_GPERM_SUPEREDIT | GNAV_GPERM_EDITABLE => _GNAV_GPERM_G_SUPEREDIT ,
		GNAV_GPERM_SUPERDELETE | GNAV_GPERM_DELETABLE => _GNAV_GPERM_G_SUPERDELETE ,
		GNAV_GPERM_RATEVIEW => _GNAV_GPERM_G_RATEVIEW ,
		GNAV_GPERM_RATEVOTE | GNAV_GPERM_RATEVIEW => _GNAV_GPERM_G_RATEVOTE ,
		GNAV_GPERM_WYSIWYG => _GNAV_GPERM_G_WYSIWYG ,
	) ;

	$form = new MyXoopsGroupPermForm( '' , $xoopsModule->mid() , 'gnavi_global' , _MD_A_GNAVI_GROUPPERM_GLOBALDESC ) ;
	foreach( $global_perms_array as $perm_id => $perm_name ) {
		$form->addItem( $perm_id , $perm_name ) ;
	}

	echo $form->render() ;
}



if( ! empty( $_POST['submit'] ) ) {
	include( "mygroupperm.php" ) ;
	redirect_header("index.php?page=groupperm" , 1 , _MD_A_GNAVI_GPERMUPDATED );
}

xoops_cp_header() ;
include dirname(__FILE__).'/mymenu.php' ;
echo "" ;
echo "<h3 style='text-align:left;'>".$xoopsModule->name()."</h3>\n" ;
echo "<h4 style='text-align:left;'>"._MD_A_GNAVI_GROUPPERM_GLOBAL."</h4>\n" ;
list_groups() ;
xoops_cp_footer() ;

?>