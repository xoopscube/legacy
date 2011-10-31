<?php

eval( ' function xoops_module_update_'.$mydirname.'( $module ) { return gnavi_onupdate_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'gnavi_onupdate_base' ) ) {

function gnavi_onupdate_base( $module , $mydirname )
{
	// transations on module update

	global $msgs ; // TODO :-D

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'gnavi_message_append_onupdate' ) ;
		$msgs = array() ;
	} else {
		if( ! is_array( $msgs ) ) $msgs = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;

	// TABLES (write here ALTER TABLE etc. if necessary)

	$check_sql = "SELECT arrowhtml FROM ".$db->prefix("{$mydirname}_text") ;
	if(  ! $db->query( $check_sql) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_text")." ADD arrowhtml tinyint(1) NOT NULL default '0',ADD addinfo text" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_cat")." CHANGE icd icd int(5) unsigned NOT NULL default '0'" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_photos")." CHANGE icd icd int(5) unsigned NOT NULL default '0'" ) ;	
	}
	
	//version 0.13 -> version 0.7 
	$check_sql = "SELECT * FROM ".$db->prefix("{$mydirname}_photos")." USE INDEX(submitter)" ;
	if(  ! $db->query( $check_sql) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_photos")." ADD INDEX (submitter)" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_cat")." CHANGE lat tmp_lat double(9,6) NOT NULL default '0'" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_cat")." CHANGE lng lat double(9,6) NOT NULL default '0'" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_cat")." CHANGE tmp_lat lng double(9,6) NOT NULL default '0'" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_photos")." CHANGE lat tmp_lat double(9,6) NOT NULL default '0'" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_photos")." CHANGE lng lat double(9,6) NOT NULL default '0'" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_photos")." CHANGE tmp_lat lng double(9,6) NOT NULL default '0'" ) ;
	}

	//version 0.8 -> version 0.9 
	$check_sql = "SELECT mtype FROM ".$db->prefix("{$mydirname}_photos") ;
	if(  ! $db->query( $check_sql) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_photos")." ADD mtype varchar(30) NOT NULL default ''" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_cat")." ADD mtype varchar(30) NOT NULL default ''" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_cat")." ADD   kmlurl varchar(150) NOT NULL default ''" ) ;
	}

	//version 0.95 -> version 0.96 
	$check_sql = "SELECT rss FROM ".$db->prefix("{$mydirname}_photos") ;
	if(  ! $db->query( $check_sql) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix("{$mydirname}_photos")." ADD rss varchar(255) NOT NULL default ''" ) ;
	}

	// TEMPLATES (all templates have been already removed by modulesadmin)
	$tplfile_handler =& xoops_gethandler( 'tplfile' ) ;

	// block templete must alldelete (for update from V2 module)
	$templates =& $tplfile_handler->find( null , 'block' , null , $mydirname) ;
	$tcount = count( $templates ) ;
	if( $tcount > 0 ) {
		$ret[] = 'Deleting templates...' ;
		for( $i = 0 ; $i < $tcount ; $i ++ ) {
			if( ! $tplfile_handler->delete( $templates[$i] ) ) {
				$msgs[] = '<span style="color:#ff0000;">ERROR: Could not delete template '.$templates[$i]->getVar('tpl_file','s').' from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id','s').'</b></span><br />';
			} else {
				$msgs[] = 'Template <b>'.$templates[$i]->getVar('tpl_file','s').'</b> deleted from the database. Template ID: <b>'.$templates[$i]->getVar('tpl_id','s').'</b><br />';
			}
		}
	}
	unset($templates);

	$tpl_path = dirname(__FILE__).'/templates' ;
	if( $handler = @opendir( $tpl_path . '/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			$file_path = $tpl_path . '/' . $file ;
			if( is_file( $file_path ) ) {
				$mtime = intval( @filemtime( $file_path ) ) ;
				$tplfile =& $tplfile_handler->create() ;
				$tplfile->setVar( 'tpl_source' , file_get_contents( $file_path ) , true ) ;
				$tplfile->setVar( 'tpl_refid' , $mid ) ;
				$tplfile->setVar( 'tpl_tplset' , 'default' ) ;
				$tplfile->setVar( 'tpl_file' , $mydirname . '_' . $file ) ;
				$tplfile->setVar( 'tpl_desc' , '' , true ) ;
				$tplfile->setVar( 'tpl_module' , $mydirname ) ;
				$tplfile->setVar( 'tpl_lastmodified' , $mtime ) ;
				$tplfile->setVar( 'tpl_lastimported' , 0 ) ;
				$tplfile->setVar( 'tpl_type' , 'module' ) ;
				if( ! $tplfile_handler->insert( $tplfile ) ) {
					$msgs[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> to the database.</span>';
				} else {
					$tplid = $tplfile->getVar( 'tpl_id' ) ;
					$msgs[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> added to the database. (ID: <b>'.$tplid.'</b>)';
					// generate compiled file
					include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
					include_once XOOPS_ROOT_PATH.'/class/template.php' ;
					if( ! xoops_template_touch( $tplid ) ) {
						$msgs[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b>.</span>';
					} else {
						$msgs[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> compiled.</span>';
					}
				}
			}
		}
		closedir( $handler ) ;
	}
	include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
	include_once XOOPS_ROOT_PATH.'/class/template.php' ;
	xoops_template_clear_module_cache( $mid ) ;

	return true ;
}

function gnavi_message_append_onupdate( &$module_obj , &$log )
{
	if( is_array( @$GLOBALS['msgs'] ) ) {
		foreach( $GLOBALS['msgs'] as $message ) {
			$log->add( strip_tags( $message ) ) ;
		}
	}

	// use mLog->addWarning() or mLog->addError() if necessary
}

}

?>