<?php

eval( ' function xoops_module_install_'.$mydirname.'( $module ) { return bulletin_oninstall_base( $module , "'.$mydirname.'" ) ; } ' ) ;


function bulletin_oninstall_base( $module , $mydirname )
{
	// transations on module install

	global $ret ;

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;

	// for Cube 2.1
	if( defined('XOOPS_CUBE_LEGACY')) {
		$isCube = true ;
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add("Module.Legacy.ModuleInstall.Success", 'bulletin_message_append_oninstall') ;
		$ret = array() ;
	} else {
		$isCube = false ;
		if( ! is_array( $ret ) ) $ret = array() ;
	}

	// transations on module installation
	$bulletin_posting_permissions = array( 1 , 2 , 3 , 7 ) ;
	$gperm_handler = xoops_gethandler('groupperm') ;
	foreach( $bulletin_posting_permissions as $itemid ) {
		$gperm =& $gperm_handler->create() ;
		$gperm->setVar( 'gperm_groupid' , 1 ) ;
		$gperm->setVar( 'gperm_name' , 'bulletin_permit' );
		$gperm->setVar( 'gperm_modid' , $mid ) ;
		$gperm->setVar( 'gperm_itemid' , $itemid ) ;
		$gperm_handler->insert( $gperm ) ;
	}

	// TABLES (loading mysql.sql)
	$sql_file_path = dirname(__FILE__).'/sql/mysql.sql' ;
	$prefix_mod = $db->prefix() . '_' . $mydirname ;
	if( file_exists( $sql_file_path ) ) {
		$ret[] = "SQL file found at <b>".htmlspecialchars($sql_file_path)."</b>.<br  /> Creating tables...<br />";

		if( file_exists( XOOPS_ROOT_PATH.'/class/database/oldsqlutility.php' ) ) {
			include_once XOOPS_ROOT_PATH.'/class/database/oldsqlutility.php' ;
			$sqlutil =& new OldSqlUtility ;
		} else {
			include_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php' ;
			$sqlutil =& new SqlUtility ;
		}

		$sql_query = trim( file_get_contents( $sql_file_path ) ) ;
		$sqlutil->splitMySqlFile( $pieces , $sql_query ) ;
		$created_tables = array() ;
		foreach( $pieces as $piece ) {
			$prefixed_query = $sqlutil->prefixQuery( $piece , $prefix_mod ) ;
			if( ! $prefixed_query ) {
				$ret[] = "Invalid SQL <b>".htmlspecialchars($piece)."</b><br />";
				return false ;
			}
			if( ! $db->query( $prefixed_query[0] ) ) {
				$ret[] = '<b>'.htmlspecialchars( $db->error() ).'</b><br />' ;
				var_dump( $db->error() ) ;
				return false ;
			} else {
				if( ! in_array( $prefixed_query[4] , $created_tables ) ) {
					$ret[] = '&nbsp;&nbsp;Table <b>'.htmlspecialchars($prefix_mod.'_'.$prefixed_query[4]).'</b> created.<br />';
					$created_tables[] = $prefixed_query[4];
				} else {
					$ret[] = '&nbsp;&nbsp;Data inserted to table <b>'.htmlspecialchars($prefix_mod.'_'.$prefixed_query[4]).'</b>.</br />';
				}
			}
		}
	}

	// TEMPLATES
	$tplfile_handler =& xoops_gethandler( 'tplfile' ) ;
	$tpl_path = dirname(__FILE__).'/templates' ;
	if( $handler = @opendir( $tpl_path . '/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			$file_path = $tpl_path . '/' . $file ;
			if( is_file( $file_path ) && substr( $file , -5 ) == '.html' ) {
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
					$ret[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> to the database.</span><br />';
				} else {
					$tplid = $tplfile->getVar( 'tpl_id' ) ;
					$ret[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> added to the database. (ID: <b>'.$tplid.'</b>)<br />';
					// generate compiled file
					include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
					include_once XOOPS_ROOT_PATH.'/class/template.php';
					if( ! xoops_template_touch( $tplid ) ) {
						$ret[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b>.</span><br />';
					} else {
						$ret[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> compiled.</span><br />';
					}
				}
			}
		}
		closedir( $handler ) ;
	}
	include_once XOOPS_ROOT_PATH.'/class/xoopsblock.php' ;
	include_once XOOPS_ROOT_PATH.'/class/template.php' ;
	xoops_template_clear_module_cache( $mid ) ;

	// BLOCKS
	$tpl_path = dirname(__FILE__).'/templates/blocks' ;
	if( $handler = @opendir( $tpl_path . '/' ) ) {
		while( ( $file = readdir( $handler ) ) !== false ) {
			if( substr( $file , 0 , 1 ) == '.' ) continue ;
			$file_path = $tpl_path . '/' . $file ;
			if( is_file( $file_path ) && substr( $file , -5 ) == '.html' ) {
				$mtime = intval( @filemtime( $file_path ) ) ;
				$tpl_file = $mydirname . '_' . $file;
				$sql = "SELECT tpl_id FROM ".$db->prefix('tplfile')." WHERE tpl_module='$mydirname' AND tpl_file='".mysql_escape_string($tpl_file)."'";
				list($tpl_id) = $db->fetchRow($db->query($sql));
				$tpl_source = file_get_contents( $file_path );
				if (!empty($tpl_id) && isset($tpl_source) && $tpl_source != '') {
					$sql = sprintf("INSERT INTO %s (tpl_id, tpl_source) VALUES (%u, %s)", $db->prefix('tplsource'), $tpl_id, $db->quoteString($tpl_source));
					if( !$result = $db->query($sql) ) {
						$ret[] = '<span style="color:#ff0000;">ERROR: Could not insert template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> to the database.</span><br />';
					} else {
						$ret[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> added to the database. (ID: <b>'.$tpl_id.'</b>)<br />';
						// generate compiled file
						include_once XOOPS_ROOT_PATH.'/class/template.php';
						if( ! xoops_template_touch( $tpl_id ) ) {
							$ret[] = '<span style="color:#ff0000;">ERROR: Failed compiling template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b>.</span><br />';
						} else {
							$ret[] = 'Template <b>'.htmlspecialchars($mydirname.'_'.$file).'</b> compiled.</span><br />';
						}
					}
				}
			}
		}
		closedir( $handler ) ;
	}

	return true ;
}

function bulletin_message_append_oninstall( &$controller , &$eventArgs )
{
	if( is_array( @$GLOBALS['ret'] ) ) {
		foreach( $GLOBALS['ret'] as $message ) {
			$controller->mLog->add( $message ) ;
		}
	}
}

?>