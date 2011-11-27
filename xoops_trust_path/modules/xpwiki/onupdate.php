<?php

eval( ' function xoops_module_update_'.$mydirname.'( $module ) { return xpwiki_onupdate_base( $module , "'.$mydirname.'" ) ; } ' ) ;


if( ! function_exists( 'xpwiki_onupdate_base' ) ) {

function xpwiki_onupdate_base( $module , $mydirname )
{
	// transations on module update

	global $msgs ; // TODO :-D

	// for Cube 2.1
	if( defined( 'XOOPS_CUBE_LEGACY' ) ) {
		$root =& XCube_Root::getSingleton();
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Success', 'xpwiki_message_append_onupdate' ) ;
		$root->mDelegateManager->add( 'Legacy.Admin.Event.ModuleUpdate.' . ucfirst($mydirname) . '.Fail' , 'xpwiki_message_append_onupdate' ) ;
		$msgs = array() ;
	} else {
		if( ! is_array( $msgs ) ) $msgs = array() ;
	}

	$db =& Database::getInstance() ;
	$mid = $module->getVar('mid') ;

	// DB Check for db non support version
	$query = "SELECT * FROM ".$db->prefix($mydirname."_pginfo") ;
	if(! $db->query($query)) {
		// TABLES (loading mysql.sql)
		$sql_file_path = dirname(__FILE__).'/sql/mysql.sql' ;
		$prefix_mod = $db->prefix() . '_' . $mydirname ;
		if( file_exists( $sql_file_path ) ) {
			$ret[] = "SQL file found at <b>".htmlspecialchars($sql_file_path)."</b>.<br /> Creating tables...";

			if( is_file( XOOPS_ROOT_PATH.'/class/database/oldsqlutility.php' ) ) {
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
					//var_dump( $db->error() ) ;
					return false ;
				} else {
					if( ! in_array( $prefixed_query[4] , $created_tables ) ) {
						$ret[] = 'Table <b>'.htmlspecialchars($prefix_mod.'_'.$prefixed_query[4]).'</b> created.<br />';
						$created_tables[] = $prefixed_query[4];
					} else {
						$ret[] = 'Data inserted to table <b>'.htmlspecialchars($prefix_mod.'_'.$prefixed_query[4]).'</b>.</br />';
					}
				}
			}
		}
	}

	// TABLES (write here ALTER TABLE etc. if necessary)
	$query = "SELECT `reading` FROM ".$db->prefix($mydirname."_pginfo") ;
	if(! $db->query($query)) {
		$db->queryF('ALTER TABLE `'.$db->prefix($mydirname."_pginfo").'` ADD `reading` VARCHAR( 255 ) BINARY NOT NULL');
	}

	$query = "SELECT `name_ci` FROM ".$db->prefix($mydirname."_pginfo") ;
	if(! $db->query($query)) {
		$db->query('ALTER TABLE `'.$db->prefix($mydirname.'_pginfo').'` ADD `name_ci` VARCHAR( 255 ) NOT NULL');
		$db->query('ALTER TABLE `'.$db->prefix($mydirname.'_pginfo').'` ADD INDEX ( `name_ci` )');
		$db->query('UPDATE `'.$db->prefix($mydirname.'_pginfo').'` SET `name_ci` = `name`');
	}

	$query = "SELECT `pgorder` FROM ".$db->prefix($mydirname."_pginfo") ;
	if(! $db->query($query)) {
		$db->query('ALTER TABLE `'.$db->prefix($mydirname.'_pginfo').'` ADD `pgorder` FLOAT DEFAULT \'1\' NOT NULL');
	}

	$query = "SELECT count(*) FROM ".$db->prefix($mydirname."_cache") ;
	if(! $db->query($query)) {
		$db->query(
'CREATE TABLE `'.$db->prefix($mydirname.'_cache').'` (
  `key` varchar(64) NOT NULL default \'\',
  `plugin` varchar(100) NOT NULL default \'\',
  `data` mediumblob NOT NULL,
  `mtime` int(11) NOT NULL default \'0\',
  `ttl` int(11) NOT NULL default \'0\',
  KEY `key` (`key`),
  KEY `plugin` (`plugin`)
)'
		);
	}

	// ADD Keys
	$table = $db->prefix($mydirname.'_attach');
    if ($result = $db->query('SHOW INDEX FROM `' . $table . '`')) {
        $keys = array( 'name' => '',
		               'type' => '',
		               'mode' => '',
		               'age' => '' );
        while($arr = $db->fetchArray($result)) {
        	unset($keys[$arr['Key_name']]);
        }
        foreach ($keys as $_key => $_val) {
        	$query = 'ALTER TABLE `' . $table . '` ADD INDEX(`'.$_key.'`'.$_val.')';
        	$db->query($query);
        	//$msgs[] = $query;
        }
    }
	$table = $db->prefix($mydirname.'_pginfo');
    if ($result = $db->query('SHOW INDEX FROM `' . $table . '`')) {
        $keys = array( 'editedtime' => '',
		               'freeze' => '',
		               'egids' => '',
		               'vgids' => '',
		               'eaids' => '(255)',
		               'vaids' => '(255)',
		               'vids' => array('vaids' => '(200)', 'vgids' => '(133)') );
        while($arr = $db->fetchArray($result)) {
        	unset($keys[$arr['Key_name']]);
        }
        foreach ($keys as $_key => $_val) {
        	if (is_array($_val)) {
        		$_index = array();
        		foreach($_val as $__key => $__val) {
        			$_index[] = '`'.$__key.'`'.$__val;
        		}
        		$_index = join(', ', $_index);
        	} else {
        		$_index = '`'.$_key.'`'.$_val;
        	}
        	$query = 'ALTER TABLE `' . $table . '` ADD INDEX `'.$_key.'`('.$_index.')';
        	$db->query($query);
        	//$msgs[] = $query;
        }
    }
	$table = $db->prefix($mydirname.'_rel');
    if ($result = $db->query('SHOW INDEX FROM `' . $table . '`')) {
        $keys = array( 'PRIMARY' => '' );
        while($arr = $db->fetchArray($result)) {
        	unset($keys[$arr['Key_name']]);
        }
        if ($keys) {
			$dels = array();
			$query = 'SELECT CONCAT(pgid, \'_\', relid) as id, (count(*)-1) as count FROM `'.$table.'` GROUP BY id HAVING count >= 1';
			if ($result = $db->query($query)) {
				while($arr = $db->fetchRow($result)) {
					$dels[$arr[0]] = $arr[1];
				}
			}
			foreach($dels as $key => $limit) {
				$arr = explode('_', $key);
				$query = 'DELETE FROM ' . $table . ' WHERE pgid='.$arr[0].' AND relid='.$arr[1].' LIMIT '.$limit;
				$db->query($query);
				//$msgs[] = $query;
			}
        	$query = 'ALTER TABLE `' . $table . '` ADD PRIMARY KEY(`pgid`,`relid`)';
        	$db->query($query);
        	//$msgs[] = $query;
        }
    }
	$table = $db->prefix($mydirname.'_count');
    if ($result = $db->query('SHOW INDEX FROM `' . $table . '`')) {
        $keys = array( 'today' => '' );
        while($arr = $db->fetchArray($result)) {
        	unset($keys[$arr['Key_name']]);
        }
        foreach ($keys as $_key => $_val) {
        	$query = 'ALTER TABLE `' . $table . '` ADD INDEX(`'.$_key.'`'.$_val.')';
        	$db->query($query);
        	//$msgs[] = $query;
        }
    }

	// TEMPLATES (all templates have been already removed by modulesadmin)
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

	// xpWiki original functions
	include_once dirname(__FILE__).'/include/check.func.php';
	$_ret = xpwikifunc_permission_check($mydirname);
	if (!$_ret) {
		$msgs = array_merge($msgs, xpwikifunc_defdata_check($mydirname, 'update'));
	} else {
		$msgs = array_merge($msgs, $_ret);
		return false;
	}

	// Delete COUNTER_DIR/*.counter
	$msgs = array_merge($msgs, xpwikifunc_delete_counter($mydirname));

	return true ;
}

function xpwiki_message_append_onupdate( &$module_obj , &$log )
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