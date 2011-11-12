<?php

eval( ' function xoops_module_uninstall_'.$mydirname.'( $module ) { return bulletin_onuninstall_base( $module , "'.$mydirname.'" ) ; } ' ) ;


function bulletin_onuninstall_base( $module , $mydirname )
{
	// transations on module uninstall

	global $ret ;

	// for Cube 2.1
	if( defined('XOOPS_CUBE_LEGACY')) {
		$isCube = true ;
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add("Module.Legacy.ModuleUninstall.Success", 'bulletin_message_append_onuninstall') ;
		$ret = array() ;
	} else {
		$isCube = false ;
		if( ! is_array( $ret ) ) $ret = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;

	// TABLES (loading mysql.sql)
	$sql_file_path = dirname(__FILE__).'/sql/mysql.sql' ;
	$prefix_mod = $db->prefix() . '_' . $mydirname ;
	if( file_exists( $sql_file_path ) ) {
		$ret[] = "SQL file found at <b>".htmlspecialchars($sql_file_path)."</b>.<br  /> Deleting tables...<br />";
		$sql_lines = file( $sql_file_path ) ;
		foreach( $sql_lines as $sql_line ) {
			if( preg_match( '/^CREATE TABLE \`?([a-zA-Z0-9_-]+)\`? /i' , $sql_line , $regs ) ) {
				$sql = 'DROP TABLE '.addslashes($prefix_mod.'_'.$regs[1]);
				if (!$db->query($sql)) {
					$ret[] = '<span style="color:#ff0000;">ERROR: Could not drop table <b>'.htmlspecialchars($prefix_mod.'_'.$regs[1]).'<b>.</span><br />';
				} else {
					$ret[] = 'Table <b>'.htmlspecialchars($prefix_mod.'_'.$regs[1]).'</b> dropped.<br />';
				}
			}
		}
	}

	// TEMPLATES (Not necessary because modulesadmin removes all templates)
	/* $tplfile_handler =& xoops_gethandler( 'tplfile' ) ;
	$templates =& $tplfile_handler->find( null , 'module' , $mid ) ;
	$tcount = count( $templates ) ;
	if( $tcount > 0 ) {
		$ret[] = 'Deleting templates...' ;
		for( $i = 0 ; $i < $tcount ; $i ++ ) {
			if( ! $tplfile_handler->delete( $templates[$i] ) ) {
				$ret[] = '<span style="color:#ff0000;">ERROR: Could not delete template '.$templates[$i]->getVar('tpl_file','s').' from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id','s').'</b></span><br />';
			} else {
				$ret[] = 'Template <b>'.$templates[$i]->getVar('tpl_file','s').'</b> deleted from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id','s').'</b><br />';
			}
		}
	}
	unset($templates); */


	return true ;
}

function bulletin_message_append_onuninstall( &$controller , &$eventArgs )
{
	if( is_array( @$GLOBALS['ret'] ) ) {
		foreach( $GLOBALS['ret'] as $message ) {
			$controller->mLog->add( $message ) ;
		}
	}
}

?>