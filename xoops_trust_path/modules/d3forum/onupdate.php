<?php

eval( ' function xoops_module_update_'.$mydirname.'( $module ) { return d3forum_onupdate_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'd3forum_onupdate_base' ) ) {

function d3forum_onupdate_base( $module , $mydirname )
{
	// transations on module update

	global $msgs ; // TODO :-D

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'd3forum_message_append_onupdate' ) ;
		$msgs = array() ;
	} else {
		if( ! is_array( $msgs ) ) $msgs = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;



	// TABLES (write here ALTER TABLE etc. if necessary)

	// configs (Though I know it is not a recommended way...)
	$check_sql = "SHOW COLUMNS FROM ".$db->prefix("config")." LIKE 'conf_title'" ;
	if( ( $result = $db->query( $check_sql ) ) && ( $myrow = $db->fetchArray( $result ) ) && @$myrow['Type'] == 'varchar(30)' ) {
		$db->queryF( "ALTER TABLE ".$db->prefix("config")." MODIFY `conf_title` varchar(255) NOT NULL default '', MODIFY `conf_desc` varchar(255) NOT NULL default ''" ) ;
	}

	// 0.1x -> 0.2x
	$check_sql = "SELECT cat_unique_path FROM ".$db->prefix($mydirname."_categories") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_categories")." ADD cat_unique_path text NOT NULL default '' AFTER cat_path_in_tree" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_forums")." ADD forum_external_link_format varchar(255) NOT NULL default '' AFTER cat_id" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_topics")." ADD topic_external_link_id int(10) unsigned NOT NULL default 0 AFTER forum_id, ADD KEY (`topic_external_link_id`)" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_posts")." ADD path_in_tree text NOT NULL default '' AFTER order_in_tree , ADD unique_path text NOT NULL default '' AFTER order_in_tree" ) ;
	}

	// 0.3x -> 0.4x
	$check_sql = "SELECT subject_waiting FROM ".$db->prefix($mydirname."_posts") ;	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_posts")." ADD subject_waiting varchar(255) NOT NULL default '' AFTER `subject`, ADD post_text_waiting text NOT NULL AFTER `post_text`, ADD uid_hidden mediumint(8) unsigned NOT NULL default 0 AFTER `uid`, DROP hide_uid" ) ;
	}

	// 0.4x/0.6x -> 0.7x
	$check_sql = "SHOW COLUMNS FROM ".$db->prefix($mydirname."_topics")." LIKE 'topic_external_link_id'" ;
	if( ( $result = $db->query( $check_sql ) ) && ( $myrow = $db->fetchArray( $result ) ) && substr( @$myrow['Type'] , 0 , 3 ) == 'int' ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_topics")." MODIFY topic_external_link_id varchar(255) NOT NULL default ''" ) ;
	}
	$check_sql = "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_post_histories") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "CREATE TABLE ".$db->prefix($mydirname."_post_histories")." ( history_id int(10) unsigned NOT NULL auto_increment, post_id int(10) unsigned NOT NULL default 0, history_time int(10) NOT NULL default 0, data text, PRIMARY KEY (history_id), KEY (post_id) ) TYPE=MyISAM" ) ;
	}

	// TEMPLATES (all templates have been already removed by modulesadmin)
	$tplfile_handler =& xoops_gethandler( 'tplfile' ) ;
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

function d3forum_message_append_onupdate( &$module_obj , &$log )
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