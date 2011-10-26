<?php

eval( ' function xoops_module_update_'.$mydirname.'( $module ) { return pico_onupdate_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'pico_onupdate_base' ) ) {

function pico_onupdate_base( $module , $mydirname )
{
	// transations on module update

	global $msgs ; // TODO :-D

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'pico_message_append_onupdate' ) ;
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

	// 0.1 -> 0.2
	$check_sql = "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_category_permissions") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "DROP TABLE ".$db->prefix($mydirname."_category_access") ) ;
		$db->queryF( "CREATE TABLE ".$db->prefix($mydirname."_category_permissions")." ( cat_id smallint(5) unsigned NOT NULL default 0, uid mediumint(8) default NULL, groupid smallint(5) default NULL, permissions text, UNIQUE KEY (cat_id,uid), UNIQUE KEY (cat_id,groupid), KEY (cat_id), KEY (uid), KEY (groupid)) ENGINE=MyISAM" ) ;
	}

	// 0.2 -> 0.9
	$check_sql = "SELECT cat_vpath FROM ".$db->prefix($mydirname."_categories") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_categories")." ADD   `cat_vpath` varchar(255) AFTER `cat_id`, ADD UNIQUE KEY (`cat_vpath`)" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_contents")." ADD   `vpath` varchar(255) AFTER `content_id`, ADD UNIQUE KEY (`vpath`)" ) ;
	}

	// 0.9 -> 0.95
	$check_sql = "SELECT cat_vpath_mtime FROM ".$db->prefix($mydirname."_categories") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_categories")." ADD cat_created_time int(10) NOT NULL default 0, ADD cat_modified_time int(10) NOT NULL default 0, ADD cat_vpath_mtime int(10) NOT NULL default 0" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_contents")." MODIFY weight smallint(5) NOT NULL default 0" ) ;
	}

	// 1.0 -> 1.1/1.2
	$check_sql = "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_content_histories") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "CREATE TABLE ".$db->prefix($mydirname."_content_histories")." ( content_history_id int(10) unsigned NOT NULL auto_increment, content_id int(10) unsigned NOT NULL default 0, vpath varchar(255), cat_id smallint(5) unsigned NOT NULL default 0, created_time int(10) NOT NULL default 0, modified_time int(10) NOT NULL default 0, poster_uid mediumint(8) unsigned NOT NULL default 0, poster_ip varchar(15) NOT NULL default '', modifier_uid mediumint(8) unsigned NOT NULL default 0, modifier_ip varchar(15) NOT NULL default '', subject varchar(255) NOT NULL default '', htmlheader mediumtext, body mediumtext, filters text, PRIMARY KEY (content_history_id), KEY (content_id), KEY (created_time), KEY (modified_time), KEY (modifier_uid) ) ENGINE=MyISAM" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_contents")." MODIFY htmlheader mediumtext, MODIFY htmlheader_waiting mediumtext, MODIFY body mediumtext, MODIFY body_waiting mediumtext, MODIFY body_cached mediumtext" ) ;
	}

	// 1.1/1.2 -> 1.3/1.4
	$check_sql = "SELECT cat_redundants FROM ".$db->prefix($mydirname."_categories") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_categories")." MODIFY cat_id smallint(5) unsigned NOT NULL, ADD cat_redundants text AFTER cat_vpath_mtime" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_contents")." ADD comments_count int(10) unsigned NOT NULL default 0 AFTER votes_count" ) ;
		$db->queryF( "INSERT INTO ".$db->prefix($mydirname."_categories")." (cat_id,pid,cat_title) VALUES (0,0xffff,'TOP')" ) ;
	}

	// 1.3/1.4 -> 1.5/1.6
	$check_sql = "SELECT COUNT(*) FROM ".$db->prefix($mydirname."_content_extras") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "CREATE TABLE ".$db->prefix($mydirname."_content_extras")." ( content_extra_id int(10) unsigned NOT NULL auto_increment, content_id int(10) unsigned NOT NULL default 0, extra_type varchar(255) NOT NULL default '', created_time int(10) NOT NULL default 0, modified_time int(10) NOT NULL default 0, data mediumtext, PRIMARY KEY (content_extra_id), KEY (content_id), KEY (extra_type), KEY (created_time) ) ENGINE=MyISAM" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_contents")." ADD `locked` tinyint(1) NOT NULL default 0 AFTER subject_waiting, ADD `redundants` text AFTER filters" ) ;
	}
	$check_sql = "SHOW CREATE TABLE ".$db->prefix($mydirname."_content_histories") ;
	list( , $create_sql ) = $db->fetchRow( ( $db->queryF( $check_sql ) ) ) ;
	if( stristr( $create_sql , '`body` text' ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_content_histories")." MODIFY `htmlheader` mediumtext, MODIFY `body` mediumtext" ) ;
	}
	$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_categories")." MODIFY `cat_redundants` mediumtext" ) ;

	// 1.5/1.6 -> 1.7/1.8
	$check_sql = "SELECT cat_permission_id FROM ".$db->prefix($mydirname."_categories") ;
	if( ! $db->query( $check_sql ) ) {
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_contents")." ADD permission_id int(10) unsigned NOT NULL default 0 AFTER `content_id`, ADD expiring_time int(10) NOT NULL default 0x7fffffff AFTER `modified_time`, ADD last_cached_time int(10) NOT NULL default 0 AFTER `modified_time`, ADD `extra_fields` mediumtext AFTER `filters`, ADD `for_search` mediumtext AFTER `redundants`, MODIFY `redundants` mediumtext, ADD `tags` text AFTER `filters`, ADD KEY (`modified_time`), ADD KEY (`expiring_time`), ADD KEY (`permission_id`)" ) ;
		$db->queryF( "UPDATE ".$db->prefix($mydirname."_contents")." SET `expiring_time`=0x7fffffff" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_content_histories")." ADD tags text, ADD extra_fields mediumtext" ) ;
		$db->queryF( "ALTER TABLE ".$db->prefix($mydirname."_categories")." ADD `cat_permission_id` int(10) unsigned NOT NULL AFTER `cat_id`, ADD KEY (`cat_permission_id`)" ) ;
		$db->queryF( "UPDATE ".$db->prefix($mydirname."_categories")." SET `cat_permission_id`=`cat_id`" ) ;
		$db->queryF( "CREATE TABLE ".$db->prefix($mydirname."_tags")." ( label varchar(255) NOT NULL default '', weight int(10) unsigned NOT NULL default 0, count int(10) unsigned NOT NULL default 0, content_ids mediumtext, created_time int(10) NOT NULL default 0, modified_time int(10) NOT NULL default 0, PRIMARY KEY (label), KEY (count), KEY (weight), KEY (created_time) ) ENGINE=MyISAM" ) ;
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

function pico_message_append_onupdate( &$module_obj , &$log )
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