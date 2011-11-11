<?php

if( ! defined( 'XOOPS_ROOT_PATH' ) ) exit ;

$core_type = altsys_get_core_type() ;
$db =& Database::getInstance() ;

$current_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , @$_GET['dirname'] ) ;
if( $current_dirname == '__CustomBlocks__' ) return ;

	$module_handler4menu =& xoops_gethandler('module');
	$criteria4menu = new CriteriaCompo(new Criteria('isactive', 1));
	//$criteria4menu->add(new Criteria('hasmain', 1));
	$criteria4menu->add(new Criteria('mid', '1', '>'));
	$modules4menu =& $module_handler4menu->getObjects($criteria4menu, true);
	$system_module =& $module_handler4menu->get(1) ;
	if( is_object( $system_module ) ) array_unshift( $modules4menu , $system_module ) ;

$adminmenu = array() ;
foreach( $modules4menu as $m4menu ) {
	// get block info
	if( $core_type != ALTSYS_CORE_TYPE_X22 ) {
		list( $block_count_all ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix("newblocks")." WHERE mid=".$m4menu->getVar('mid') ) ) ;
		list( $block_count_visible ) = $db->fetchRow( $db->query( "SELECT COUNT(*) FROM ".$db->prefix("newblocks")." WHERE mid=".$m4menu->getVar('mid')." AND visible>0" ) ) ;
		// $block_desc = " $block_count_all($block_count_visible)" ;
		$block_desc = " ($block_count_visible/$block_count_all)" ;
	} else {
		$block_desc = '' ;
	}

	if( $m4menu->getVar('dirname') == $current_dirname ) {
		$adminmenu[] = array(
			'selected' => true ,
			'title' => $m4menu->getVar('name','n') . $block_desc ,
			'link' => '?mode=admin&lib=altsys&page=myblocksadmin&dirname='.$m4menu->getVar('dirname','n') ,
		) ;
		//$GLOBALS['altsysXoopsBreadcrumbs'][] = array( 'name' => $m4menu->getVar('name') ) ;
	} else {
		$adminmenu[] = array(
			'selected' => false ,
			'title' => $m4menu->getVar('name','n') . $block_desc ,
			'link' => '?mode=admin&lib=altsys&page=myblocksadmin&dirname='.$m4menu->getVar('dirname','n') ,
		) ;
	}
}


// display
require_once XOOPS_TRUST_PATH.'/libs/altsys/class/D3Tpl.class.php' ;
$tpl = new D3Tpl() ;
$tpl->assign( array(
	'adminmenu' => $adminmenu ,
	'highlight_color' => '#ffdd99' ,
) ) ;
$tpl->display( 'db:altsys_inc_mymenusub.html' ) ;

?>
