<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

$current_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , @$_GET['dirname'] ) ;

$db =& Database::getInstance() ;

// get custom templates
list( $count ) = $db->fetchRow( $db->query( "SELECT COUNT(t.tpl_module) AS tpl_count FROM ".$db->prefix("tplfile")." t WHERE t.tpl_type='custom'" ) ) ;
if( $current_dirname == '_custom' ) {
//	$GLOBALS['altsysXoopsBreadcrumbs'][] = array( 'name' => _MYTPLSADMIN_CUSTOMTEMPLATE ) ;
	$custom_selected = true ;
} else {
	$custom_selected = false ;
}

$adminmenu = array(
	array(
		'selected' => $custom_selected ,
		'title' => _MYTPLSADMIN_CUSTOMTEMPLATE . " ($count)" ,
		'link' => '?mode=admin&lib=altsys&page=mytplsadmin&dirname=_custom' ,
	) ,
) ;

// get modules/templates
$mrs = $db->query( "SELECT m.name,m.dirname,COUNT(t.tpl_module) AS tpl_count FROM ".$db->prefix("modules")." m LEFT JOIN ".$db->prefix("tplfile")." t ON m.dirname=t.tpl_module WHERE m.isactive GROUP BY m.mid HAVING tpl_count>0 ORDER BY m.weight,m.mid" ) ;

// module loop
while( list( $name , $dirname , $count ) = $db->fetchRow( $mrs ) ) {
	if( $dirname == $current_dirname ) {
		$adminmenu[] = array(
			'selected' => true ,
			'title' => $name . " ($count)" ,
			'link' => '?mode=admin&lib=altsys&page=mytplsadmin&dirname='.$dirname ,
		) ;
		//$GLOBALS['altsysXoopsBreadcrumbs'][] = array( 'name' => htmlspecialchars( $name , ENT_QUOTES ) ) ;
	} else {
		$adminmenu[] = array(
			'selected' => false ,
			'title' => $name . " ($count)" ,
			'link' => '?mode=admin&lib=altsys&page=mytplsadmin&dirname='.$dirname ,
		) ;
	}
}

// display
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
$tpl = new D3Tpl() ;
$tpl->assign( array(
	'adminmenu' => $adminmenu ,
	'highlight_color' => '#99ff99' ,
) ) ;
$tpl->display( 'db:altsys_inc_mymenusub.html' ) ;

?>
