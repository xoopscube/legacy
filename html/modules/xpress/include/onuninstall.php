<?php
$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;

eval( ' function xoops_module_uninstall_'.$mydirname.'( $module ) { return xpress_onuninstall_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'xpress_onuninstall_base' ) ) {

function xpress_onuninstall_base( $module , $mydirname )
{
	// transations on module uninstall

	global $ret ; // TODO :-D

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUninstall.' . ucfirst($mydirname) . '.Success' , 'xpress_message_append_onuninstall' ) ;
		$ret = array() ;
	} else {
		if( ! is_array( $ret ) ) $ret = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;
	
	$xp_prefix = preg_replace('/wordpress/','wp',$mydirname);
	
	$xoops_prefix = $db->prefix();

	if (empty($xoops_prefix) || empty($xp_prefix)) {
		$ret[] = '<span style="color:#ff0000;">ERROR: Empty Prefix.</span><br />';
		return false;
	}
	
	$prefix_mod = $xoops_prefix  . '_' . $xp_prefix;
	$sql = "SHOW TABLES LIKE '$prefix_mod%'";
	if ($result = $db->query($sql)) {
		while ($table = $db->fetchRow($result)){
			$drop_sql = 'DROP TABLE '. $table[0] ;
			if (!$db->queryF($drop_sql)) {
				$ret[] = '<span style="color:#ff0000;">ERROR: Could not drop table <b>'.htmlspecialchars($table[0]).'<b>.</span><br />';
			} else {
				$ret[] = 'Table <b>'.htmlspecialchars($table[0]).'</b> dropped.<br />';
			}
		}
	} else {
		$ret[] = '<span style="color:#ff0000;">ERROR: Table not found of prefix <b>'.htmlspecialchars($prefix_mod).'<b> .</span><br />';
		return false;
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

function xpress_message_append_onuninstall( &$module_obj , &$log )
{
	if( is_array( @$GLOBALS['ret'] ) ) {
		foreach( $GLOBALS['ret'] as $message ) {
			$log->add( strip_tags( $message ) ) ;
		}
	}

	// use mLog->addWarning() or mLog->addError() if necessary
}

}

?>