<?php
/**
 * Pico content management D3 module for XCL
 * This file can be included from transaction procedures
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

// delete a content
function pico_delete_content( $mydirname, $content_id, $skip_sync = false ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	// update the content by blank data
	$_POST = [];
	pico_updatecontent( $mydirname, $content_id, true, true );

	// backup the content, first
	pico_transact_backupcontent( $mydirname, $content_id, true );

	// delete content
	if ( ! $db->queryF( 'DELETE FROM ' . $db->prefix( $mydirname . '_contents' ) . ' WHERE content_id=' . (int) $content_id ) ) {
		die( _MD_PICO_ERR_SQL . __LINE__ );
	}

	// rebuild category tree
	if ( empty( $skip_sync ) ) {
		pico_sync_cattree( $mydirname );
	}

	return true;
}

// delete a category
function pico_delete_category( $mydirname, $cat_id, $delete_also_contents = true ) {
	global $xoopsModule;

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$cat_id = (int) $cat_id;
	if ( $cat_id <= 0 ) {
		return false;
	}

	// delete contents
	if ( $delete_also_contents ) {
		$sql = 'SELECT content_id FROM ' . $db->prefix( $mydirname . '_contents' ) . " WHERE cat_id=$cat_id";
		if ( ! $result = $db->query( $sql ) ) {
			die( _MD_PICO_ERR_SQL . __LINE__ );
		}
		while ( [$content_id] = $db->fetchRow( $result ) ) {
			pico_delete_content( $mydirname, $content_id );
		}
	}

	// delete notifications about this category
	$notification_handler =& xoops_gethandler( 'notification' );

	$notification_handler->unsubscribeByItem( $xoopsModule->getVar( 'mid' ), 'category', $cat_id );

	// delete category
	if ( ! $db->queryF( 'DELETE FROM ' . $db->prefix( $mydirname . '_categories' ) . " WHERE cat_id=$cat_id" ) ) {
		die( _MD_PICO_ERR_SQL . __LINE__ );
	}

	// delete category_permissions
	if ( ! $db->queryF( 'DELETE FROM ' . $db->prefix( $mydirname . '_category_permissions' ) . " WHERE cat_id=$cat_id" ) ) {
		die( _MD_PICO_ERR_SQL . __LINE__ );
	}

	// rebuild category tree
	pico_sync_cattree( $mydirname );

	return true;
}

// store tree informations of categories
function pico_sync_cattree( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	// rebuild tree information
	[
		$tree_array,
		$subcattree,
		$contents_total,
		$subcategories_total,
		$subcategories_ids_cs
	] = pico_makecattree_recursive( $mydirname, 0 );

	//array_shift( $tree_array ) ;
	$paths = [];

	$previous_depth = 0;

	if ( ! empty( $tree_array ) ) {
		foreach ( $tree_array as $key => $val ) {
			// build the absolute path of the category
			$depth_diff     = $val['depth'] - $previous_depth;
			$previous_depth = $val['depth'];
			if ( $depth_diff > 0 ) {
				for ( $i = 0; $i < $depth_diff; $i ++ ) {
					$paths[ $val['cat_id'] ] = $val['cat_title'];
				}
			} else if ( 0 !== $val['cat_id'] ) {
				for ( $i = 0; $i < - $depth_diff + 1; $i ++ ) {
					array_pop( $paths );
				}
				$paths[ $val['cat_id'] ] = $val['cat_title'];
			}

			// redundant array
			$redundants = [
				'cat_id'               => $val['cat_id'],
				'depth'                => $val['depth'],
				'cat_title'            => $val['cat_title'],
				'contents_count'       => $val['contents_count'],
				'contents_total'       => $val['contents_total'],
				'subcategories_count'  => $val['subcategories_count'],
				'subcategories_total'  => $val['subcategories_total'],
				'subcategories_ids_cs' => $val['subcategories_ids_cs'],
				'subcattree_raw'       => $val['subcattree_raw'],
			];

			$db->queryF(
				'UPDATE '
				. $db->prefix( $mydirname . '_categories' ) . ' SET cat_depth_in_tree='
				. (int) $val['depth'] . ', cat_order_in_tree='
				. ( $key ) . ', cat_path_in_tree='
				. $db->quoteString( pico_common_serialize( $paths ) ) . ', cat_redundants='
				. $db->quoteString( pico_common_serialize( $redundants ) ) . ' WHERE cat_id='
				. $val['cat_id'] );
		}
	}
}

function pico_makecattree_recursive( $mydirname, $cat_id, $order = 'cat_weight', $parray = [], $depth = 0, $cat_title = '' ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	// get number of contents of this category
	[ $contents_count ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( $mydirname . '_contents' ) . " WHERE cat_id=$cat_id AND visible" ) );

	$sql = 'SELECT cat_id,cat_title FROM ' . $db->prefix( $mydirname . '_categories' ) . " WHERE pid=$cat_id ORDER BY $order";

	$result = $db->query( $sql );

	/*	if( $db->getRowsNum( $result ) == 0 ) {
		return array( $parray , $parray[ $myindex ]['contents_total'] , $parray[ $myindex ]['subcategories_total'] ) ;
	} */

	$myindex = is_countable($parray) ? count( $parray ) : 0;

	$myarray = [
		'cat_id'               => $cat_id,
		'depth'                => $depth,
		'cat_title'            => $cat_title,
		'contents_count'       => (int) $contents_count,
		'contents_total'       => 0,
		'subcategories_count'  => $db->getRowsNum( $result ),
		'subcategories_ids_cs' => '',
		'subcategories_total'  => 0,
		'subcattree_raw'       => []
	];

	$parray[ $myindex ] = $myarray;
	//	$parray[ $myindex ]['subcattree_raw'][] = $parray ;

	$contents_total = (int) $myarray['contents_count'];

	$subcategories_total = (int) $myarray['subcategories_count'];

	while ( [$new_cat_id, $new_cat_title] = $db->fetchRow( $result ) ) {
		[
			$parray,
			$subarray,
			$contents_smallsum,
			$subcategories_smallsum,
			$subcateroeis_ids_cs_sub
		] = pico_makecattree_recursive( $mydirname, $new_cat_id, $order, $parray, $depth + 1, $new_cat_title );
		$myarray['subcattree_raw'][]     = $subarray;
		$contents_total                  += $contents_smallsum;
		$subcategories_total             += $subcategories_smallsum;
		$myarray['subcategories_ids_cs'] .= $new_cat_id . ',' . $subcateroeis_ids_cs_sub;
	}

	$parray[ $myindex ]['contents_total'] = $contents_total;

	$myarray['contents_total'] = $contents_total;

	$parray[ $myindex ]['subcategories_total'] = $subcategories_total;

	$myarray['subcategories_total'] = $subcategories_total;

	$parray[ $myindex ]['subcategories_ids_cs'] = $myarray['subcategories_ids_cs'];

	$parray[ $myindex ]['subcattree_raw'] = $myarray['subcattree_raw'];

	return [
		$parray,
		$myarray,
		$parray[ $myindex ]['contents_total'],
		$parray[ $myindex ]['subcategories_total'],
		$myarray['subcategories_ids_cs']
	];
}

// store redundant informations to a content from its content_votes
function pico_sync_content_votes( $mydirname, $content_id ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$content_id = (int) $content_id;

	$sql = 'SELECT cat_id FROM ' . $db->prefix( $mydirname . '_contents' ) . " WHERE content_id=$content_id";
	if ( ! $result = $db->query( $sql ) ) {
		die( 'ERROR SELECT content in sync content_votes' );
	}

	[ $cat_id ] = $db->fetchRow( $result );

	$sql = 'SELECT COUNT(*),SUM(vote_point) FROM ' . $db->prefix( $mydirname . '_content_votes' ) . " WHERE content_id=$content_id";
	if ( ! $result = $db->query( $sql ) ) {
		die( 'ERROR SELECT content_votes in sync content_votes' );
	}

	[ $votes_count, $votes_sum ] = $db->fetchRow( $result );

	if ( ! $db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . ' SET votes_count=' . (int) $votes_count . ',votes_sum=' . (int) $votes_sum . " WHERE content_id=$content_id" ) ) {
		die( _MD_PICO_ERR_SQL . __LINE__ );
	}

	return true;
}

// store tags from contents
function pico_sync_tags( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	// get all tags in tags table
	$all_tags_array = [];

	$result = $db->query( 'SELECT label FROM ' . $db->prefix( $mydirname . '_tags' ) );

	while ( [$label] = $db->fetchRow( $result ) ) {
		$all_tags_array[ $label ] = [];
	}

	// count tags from contents table
	$result = $db->query( 'SELECT content_id,tags FROM ' . $db->prefix( $mydirname . '_contents' ) );

	while ( [$content_id, $tags] = $db->fetchRow( $result ) ) {
		foreach ( explode( ' ', $tags ) as $tag ) {
			if ( '' == trim( $tag ) ) {
				continue;
			}
			$all_tags_array[ $tag ][] = $content_id;
		}
	}

	// delete/insert or update tags table
	foreach ( $all_tags_array as $tag => $content_ids ) {
		$label4sql       = $db->quoteString( $tag );
		$content_ids4sql = implode( ',', $content_ids );
		$count           = $count = sizeof( $content_ids ) ; // Gigamaster fix refactor count( $content_ids );
        // @todo @gigamaster replace INSERT INTO (results Duplicate entry for key 'PRIMARY')
        // Using: INSERT IGNORE INTO
		$result    = $db->queryF( "INSERT IGNORE INTO ".$db->prefix($mydirname."_tags" )." SET label=$label4sql,weight=0,count='$count',content_ids='$content_ids4sql',created_time=UNIX_TIMESTAMP(),modified_time=UNIX_TIMESTAMP()" ) ;

		if ( ! $result ) {
			$db->queryF( "UPDATE " . $db->prefix( $mydirname . "_tags" ) . " SET count=$count,content_ids='$content_ids4sql',modified_time=UNIX_TIMESTAMP() WHERE label=$label4sql" );
		}
	}

	return true;
}

// clear body caches of all contents
function pico_clear_body_cache( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET body_cached='', for_search='', last_cached_time=0" );

	return true;
}

// sync content_votes and category's tree
function pico_sync_all( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$module_handler = xoops_gethandler( 'module' );

	$module = $module_handler->getByDirname( $mydirname );

	$config_handler = xoops_gethandler( 'config' );

	$configs = $config_handler->getConfigList( $module->mid() );

	// sync contents <- content_votes
	$result = $db->query( 'SELECT content_id FROM ' . $db->prefix( $mydirname . '_contents' ) );

	while ( [$content_id] = $db->fetchRow( $result ) ) {
		pico_sync_content_votes( $mydirname, (int) $content_id );
		//pico_sync_content( $mydirname , intval( $content_id ) ) ;
	}

	// sync tags
	pico_sync_tags( $mydirname );

	// d3forum comment integration
	if ( ! empty( $configs['comment_dirname'] ) && $configs['comment_forum_id'] > 0 ) {

		$target_module = &$module_handler->getByDirname( $configs['comment_dirname'] );

		if ( is_object( $target_module ) ) {

			$target_dirname = $target_module->getVar( 'dirname' );

			$forum_id = (int) $configs['comment_forum_id'];

			$result = $db->query( 'SELECT topic_external_link_id,COUNT(*) FROM ' . $db->prefix( $target_dirname . '_topics' ) . " WHERE topic_external_link_id>0 AND forum_id=$forum_id AND ! topic_invisible GROUP BY topic_external_link_id" );

			while ( [$content_id, $comments_count] = $db->fetchRow( $result ) ) {
				$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET comments_count=$comments_count WHERE content_id=$content_id" );
			}
		}
	}

	// fix null and '' confusion
	$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_categories' ) . " SET cat_vpath=null WHERE cat_vpath=''" );

	$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET vpath=null WHERE vpath=''" );

	// serialize_type conversion from PHP built-in serialize() to var_export()
	pico_convert_serialized_data( $mydirname );

	// sync category's tree
	pico_sync_cattree( $mydirname );
}

// serialize_type conversion from PHP built-in serialize() to var_export()
function pico_convert_serialized_data( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	// update data in content_extras
	$sql = 'SELECT content_extra_id,data FROM ' . $db->prefix( $mydirname . '_content_extras' ) . " WHERE data NOT LIKE 'array%'";

	$result = $db->query( $sql );

	if ( $db->getRowsNum( $result ) > 0 ) {
		while ( [$id, $data] = $db->fetchRow( $result ) ) {
			$data4sql = $db->quoteString( pico_common_serialize( pico_common_unserialize( $data ) ) );
			$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_content_extras' ) . " SET data=$data4sql WHERE content_extra_id=$id" );
		}
	}

	// update extra_fields in contents
	$sql = 'SELECT content_id,extra_fields FROM ' . $db->prefix( $mydirname . '_contents' ) . " WHERE extra_fields NOT LIKE 'array%' OR extra_fields IS NULL";

	$result = $db->query( $sql );

	if ( $db->getRowsNum( $result ) > 0 ) {
		while ( [$id, $data] = $db->fetchRow( $result ) ) {
			$data4sql = $db->quoteString( pico_common_serialize( pico_common_unserialize( $data ) ) );
			$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET extra_fields=$data4sql WHERE content_id=$id" );
		}
	}
}

// get requests for category's sql (parse options)
/* function pico_get_requests4category( $mydirname, $cat_id = null ) {
	$myts = null;
 ( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &MyTextSanitizer::getInstance();

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	include dirname( __DIR__ ) . '/include/configs_can_override.inc.php';

	$cat_options = [];

	foreach ( $GLOBALS['xoopsModuleConfig'] as $key => $val ) {

		if ( empty( $pico_configs_can_be_override[ $key ] ) ) {
			continue;
		}

		foreach ( explode( "\n", @$_POST['cat_options'] ) as $line ) {
			if ( preg_match( '/^' . $key . '\:(.{1,100})$/', $line, $regs ) ) {
				switch ( $pico_configs_can_be_override[ $key ] ) {
					case 'text':
						$cat_options[ $key ] = trim( $regs[1] );
						break;
					case 'int':
						$cat_options[ $key ] = (int) $regs[1];
						break;
					case 'bool':
						$cat_options[ $key ] = (int) $regs[1] > 0 ? 1 : 0;
						break;
				}
			}
		}
	}

	if ( 0 === $cat_id ) {
		// top category
		$cat_vpath = null;
		$pid       = 0xffff;
	} else {
		// normal category
		$cat_vpath = trim( $myts->stripSlashesGPC( $_POST['cat_vpath'] ?? '' ) );

		$pid = (int) @$_POST['pid'];

		// check $pid
		if ( $pid ) {
			$sql = 'SELECT * FROM ' . $db->prefix( $mydirname . '_categories' ) . " c WHERE c.cat_id=$pid";
			if ( ! $crs = $db->query( $sql ) ) {
				die( _MD_PICO_ERR_SQL . __LINE__ );
			}
			if ( $db->getRowsNum( $crs ) <= 0 ) {
				die( _MD_PICO_ERR_READCATEGORY );
			}
		}
	}

	return [
		'cat_title'   => $myts->stripSlashesGPC( @$_POST['cat_title'] ),
		'cat_desc'    => $myts->stripSlashesGPC( @$_POST['cat_desc'] ),
		'cat_weight'  => (int) @$_POST['cat_weight'],
		'cat_vpath'   => $cat_vpath,
		'pid'         => $pid,
		'cat_options' => pico_common_serialize( $cat_options ),
	];
} */

// get requests for category's sql (parse options)
function pico_get_requests4category( $mydirname, $cat_id = null ) {
    $myts = null;
    ( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &MyTextSanitizer::getInstance();

    $db = XoopsDatabaseFactory::getDatabaseConnection();

    include dirname( __DIR__ ) . '/include/configs_can_override.inc.php';

    $cat_options = [];

    // Check if we're using the new UI format (cat_option array)
    if (isset($_POST['cat_option']) && is_array($_POST['cat_option'])) {
        foreach ($_POST['cat_option'] as $key => $val) {
            if (isset($pico_configs_can_be_override[$key])) {
                switch ($pico_configs_can_be_override[$key]) {
                    case 'text':
                        $cat_options[$key] = trim($val);
                        break;
                    case 'int':
                        $cat_options[$key] = (int) $val;
                        break;
                    case 'bool':
                        $cat_options[$key] = !empty($val) ? 1 : 0;
                        break;
                }
            }
        }
    } else {
        // Original method - parse from textarea
        foreach ($GLOBALS['xoopsModuleConfig'] as $key => $val) {
            if (empty($pico_configs_can_be_override[$key])) {
                continue;
            }

            foreach (explode("\n", @$_POST['cat_options']) as $line) {
                if (preg_match('/^' . $key . '\:(.{1,100})$/', $line, $regs)) {
                    switch ($pico_configs_can_be_override[$key]) {
                        case 'text':
                            $cat_options[$key] = trim($regs[1]);
                            break;
                        case 'int':
                            $cat_options[$key] = (int) $regs[1];
                            break;
                        case 'bool':
                            $cat_options[$key] = (int) $regs[1] > 0 ? 1 : 0;
                            break;
                    }
                }
            }
        }
    }

    if (0 === $cat_id) {
        // top category
        $cat_vpath = null;
        $pid = 0xffff;
    } else {
        // normal category
        $cat_vpath = trim($myts->stripSlashesGPC($_POST['cat_vpath'] ?? ''));

        $pid = (int) @$_POST['pid'];

        // check $pid
        if ($pid) {
            $sql = 'SELECT * FROM ' . $db->prefix($mydirname . '_categories') . " c WHERE c.cat_id=$pid";
            if (!$crs = $db->query($sql)) {
                die(_MD_PICO_ERR_SQL . __LINE__);
            }
            if ($db->getRowsNum($crs) <= 0) {
                die(_MD_PICO_ERR_READCATEGORY);
            }
        }
    }

    // Convert cat_options to serialized format
    $cat_options_str = '';
    foreach ($cat_options as $key => $val) {
        $cat_options_str .= "$key:$val\n";
    }
    $cat_options_str = trim($cat_options_str);

    return [
        'cat_title' => $myts->stripSlashesGPC(@$_POST['cat_title']),
        'cat_desc' => $myts->stripSlashesGPC(@$_POST['cat_desc']),
        'cat_weight' => (int) @$_POST['cat_weight'],
        'cat_vpath' => $cat_vpath,
        'pid' => $pid,
        'cat_options' => pico_common_serialize($cat_options),
    ];
}

// create a category
function pico_makecategory( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$requests = pico_get_requests4category( $mydirname );

	$set = '';

	foreach ( $requests as $key => $val ) {
		if ( 'cat_vpath' == $key && empty( $val ) ) {
			$set .= "`$key`=null,";
		} else {
			$set .= "`$key`=" . $db->quoteString( $val ) . ',';
		}
	}

	// get cat_permission_id of the parent category
	[ $cat_permission_id ] = $db->fetchRow( $db->query( 'SELECT cat_permission_id FROM ' . $db->prefix( $mydirname . '_categories' ) . ' WHERE cat_id=' . (int) @$requests['pid'] ) );

	// get $new_cat_id
	[ $new_cat_id ] = $db->fetchRow( $db->query( 'SELECT MAX(cat_id)+1 FROM ' . $db->prefix( $mydirname . '_categories' ) ) );

	// insert it
	if ( ! $db->queryF( 'INSERT INTO ' . $db->prefix( $mydirname . '_categories' ) . " SET $set `cat_path_in_tree`='',`cat_unique_path`='',cat_id=$new_cat_id,cat_permission_id=$cat_permission_id" ) ) {
		die( _MD_PICO_ERR_DUPLICATEDVPATH . ' or ' . _MD_PICO_ERR_SQL . __LINE__ );
	}

	// permissions are set same as the parent category. (also moderator)
	/*	$sql = "SELECT uid,groupid,permissions FROM ".$db->prefix($mydirname."_category_permissions")." WHERE cat_id={$requests['pid']}" ;
	if( ! $result = $db->query( $sql ) ) die( _MD_PICO_ERR_SQL.__LINE__ ) ;
	while( $row = $db->fetchArray( $result ) ) {
		$uid4sql = empty( $row['uid'] ) ? 'null' : intval( $row['uid'] ) ;
		$groupid4sql = empty( $row['groupid'] ) ? 'null' : intval( $row['groupid'] ) ;
		$sql = "INSERT INTO ".$db->prefix($mydirname."_category_permissions")." (cat_id,uid,groupid,permissions) VALUES ($new_cat_id,$uid4sql,$groupid4sql,".$db->quoteString($row['permissions']).")" ;
		if( ! $db->query( $sql ) ) die( _MD_PICO_ERR_SQL.__LINE__ ) ;
	}*/

	// rebuild category tree
	pico_sync_cattree( $mydirname );

	return $new_cat_id;
}

// update category
function pico_updatecategory( $mydirname, $cat_id ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$requests = pico_get_requests4category( $mydirname, $cat_id );

	$set = '';

	foreach ( $requests as $key => $val ) {
		if ( 'cat_vpath' == $key && empty( $val ) ) {
			$set .= "`$key`=null,";
		} else {
			$set .= "`$key`=" . $db->quoteString( $val ) . ',';
		}
	}

	// get children
	include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';

	$mytree = new XoopsTree( $db->prefix( $mydirname . '_categories' ), 'cat_id', 'pid' );

	$children = $mytree->getAllChildId( $cat_id );

	$children[] = $cat_id;

	// loop check
	if ( in_array( $requests['pid'], $children ) ) {
		die( _MD_PICO_ERR_PIDLOOP );
	}

	if ( ! $db->queryF( "UPDATE " . $db->prefix( $mydirname . "_categories" ) . " SET " . substr( $set, 0, - 1 ) . " WHERE cat_id=$cat_id" ) ) {
		die( _MD_PICO_ERR_DUPLICATEDVPATH );
	}

	// rebuild category tree
	pico_sync_cattree( $mydirname );

	return $cat_id;
}

// get requests for content's sql (parse options)
function pico_get_requests4content( $mydirname, &$errors, $auto_approval = true, $isadminormod = false, $content_id = 0 ) {
	$myts = null;
 global $xoopsUser;

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts =& MyTextSanitizer::sGetInstance() ) || $myts =& MyTextSanitizer::getInstance();

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	// get config
	$module_handler =& xoops_gethandler( 'module' );

	$module =& $module_handler->getByDirname( $mydirname );

	if ( ! is_object( $module ) ) {
		return [];
	}

	$config_handler =& xoops_gethandler( 'config' );

	$mod_config = &$config_handler->getConfigsByCat( 0, $module->getVar( 'mid' ) );

	// check $cat_id
	$cat_id = (int) @$_POST['cat_id'];

	if ( $cat_id ) {
		$sql = 'SELECT * FROM ' . $db->prefix( $mydirname . '_categories' ) . " c WHERE c.cat_id=$cat_id";
		if ( ! $crs = $db->query( $sql ) ) {
			die( _MD_PICO_ERR_SQL . __LINE__ );
		}
		if ( $db->getRowsNum( $crs ) <= 0 ) {
			die( _MD_PICO_ERR_READCATEGORY );
		}
	}

	// build filters
	$filters = [];

	foreach ( $_POST as $key => $val ) {
		if ( 'filter_enabled_' == substr( $key, 0, 15 ) && $val ) {

			$name = str_replace( '..', '', substr( $key, 15 ) );

			$constpref = '_MD_PICO_FILTERS_' . strtoupper( $name );

			$filter_file = dirname( __DIR__ ) . '/filters/pico_' . $name . '.php';

			if ( ! file_exists( $filter_file ) ) {
				continue;
			}
			require_once $filter_file;

			if ( ! $isadminormod && defined( $constpref . 'ISINSECURE' ) ) {
				continue;
			}

			$filters[ $name ] = (int) @$_POST[ 'filter_weight_' . $name ];
		}
	}
	asort( $filters );

	// forced filters
	$filters_forced = array_map( 'trim', explode( ',', $mod_config['filters_forced'] ) );

	foreach ( $filters_forced as $filter_forced ) {

		$regs = explode( ':', $filter_forced );

		if ( false !== stripos( $filter_forced, ':LAST' ) ) {
			$filters[ $regs[0] ] = 0;
		} else {
			$filters = [ $regs[0] => 0 ] + $filters;
		}
	}

	// prohibited filters
	$filters_prohibited = array_map( 'trim', explode( ',', $mod_config['filters_prohibited'] ) );

	foreach ( $filters_prohibited as $filter_prohibited ) {
		unset( $filters[ $filter_prohibited ] );
	}

	$ret = [
		'cat_id'        => $cat_id,
		'vpath'         => trim( $myts->stripSlashesGPC( $_POST['vpath'] ?? '' ) ),
		'subject'       => $myts->stripSlashesGPC( @$_POST['subject'] ),
		'htmlheader'    => $myts->stripSlashesGPC( @$_POST['htmlheader'] ),
		'body'          => $myts->stripSlashesGPC( @$_POST['body'] ),
		'filters'       => implode( '|', array_keys( $filters ) ),
		'tags'          => trim( $myts->stripSlashesGPC( $_POST['tags'] ?? '' ) ),
		'weight'        => (int) @$_POST['weight'],
		'use_cache'     => empty( $_POST['use_cache'] ) ? 0 : 1,
		'show_in_navi'  => empty( $_POST['show_in_navi'] ) ? 0 : 1,
		'show_in_menu'  => empty( $_POST['show_in_menu'] ) ? 0 : 1,
		'allow_comment' => empty( $_POST['allow_comment'] ) ? 0 : 1,
	];

	// tags (finding a custom tag filter for each languages)
	$custom_tag_filter_file = dirname( __DIR__ ) . '/language/' . $GLOBALS['xoopsConfig']['language'] . '/tag_filter.phtml';
	if ( file_exists( $custom_tag_filter_file ) ) {

		require_once $custom_tag_filter_file;

		$tags_array = pico_custom_tag_filter( $ret['tags'] );

	} else {
		$tags_array = preg_split( '/\s+/', preg_replace( '/[\x00-\x2f:-@\x5b-\x60\x7b-\x7f]/', ' ', $ret['tags'] ) );
	}
	$ret['tags'] = trim( implode( ' ', array_unique( $tags_array ) ) );

	// vpath duplication check
	if ( $ret['vpath'] ) {
		while ( 1 ) {
			[ $count ] = $db->fetchRow( $db->query( 'SELECT COUNT(*) FROM ' . $db->prefix( $mydirname . '_contents' ) . ' WHERE vpath=' . $db->quoteString( $ret['vpath'] ) . ' AND content_id<>' . (int) $content_id ) );
			if ( empty( $count ) ) {
				break;
			}
			$ext = strrchr( $ret['vpath'], '.' );
			if ( $ext ) {
				$ret['vpath'] = str_replace( $ext, '.1' . $ext, $ret['vpath'] );
			} else {
				$ret['vpath'] .= '.1';
			}
			$errors[] = _MD_PICO_ERR_DUPLICATEDVPATH;
		}
	}

	// approval
	if ( $auto_approval ) {
		$ret += [
			'subject_waiting'    => '',
			'htmlheader_waiting' => '',
			'body_waiting'       => '',
			'visible'            => empty( $_POST['visible'] ) ? 0 : 1,
			'approval'           => 1,
		];
	} else {
		$ret += [
			'subject_waiting'    => $myts->stripSlashesGPC( @$_POST['subject'] ),
			'htmlheader_waiting' => $myts->stripSlashesGPC( @$_POST['htmlheader'] ),
			'body_waiting'       => $myts->stripSlashesGPC( @$_POST['body'] ),
			'visible'            => 0,
			'approval'           => 0,
		];
	}

	// created_time,modified_time,poster_uid,modifier_uid,locked
	if ( $isadminormod ) {
		$ret['specify_created_time']  = empty( $_POST['specify_created_time'] ) ? 0 : 1;
		$ret['specify_modified_time'] = empty( $_POST['specify_modified_time'] ) ? 0 : 1;
		$ret['specify_expiring_time'] = empty( $_POST['specify_expiring_time'] ) ? 0 : 1;

		if ( $ret['specify_created_time'] && - 1 != strtotime( @$_POST['created_time'] ) ) {
			$created_time_safe             = preg_replace( '#[^\s0-9a-zA-Z:+/-]#', '', $_POST['created_time'] );
			$ret['created_time_formatted'] = $created_time_safe;
			$ret['created_time']           = pico_common_get_server_timestamp( strtotime( $_POST['created_time'] ) );
		}
		if ( $ret['specify_modified_time'] && - 1 != strtotime( @$_POST['modified_time'] ) ) {
			$modified_time_safe             = preg_replace( '#[^\s0-9a-zA-Z:+/-]#', '', $_POST['modified_time'] );
			$ret['modified_time_formatted'] = $modified_time_safe;
			$ret['modified_time']           = pico_common_get_server_timestamp( strtotime( $_POST['modified_time'] ) );
		}
		if ( $ret['specify_expiring_time'] && - 1 != strtotime( @$_POST['expiring_time'] ) ) {
			$expiring_time_safe             = preg_replace( '#[^\s0-9a-zA-Z:+/-]#', '', $_POST['expiring_time'] );
			$ret['expiring_time_formatted'] = $expiring_time_safe;
			$ret['expiring_time']           = pico_common_get_server_timestamp( strtotime( $_POST['expiring_time'] ) );
		}

		$ret['locked'] = empty( $_POST['locked'] ) ? 0 : 1;

		if ( isset( $_POST['poster_uid'] ) ) {
			$ret['poster_uid'] = pico_main_get_uid( $_POST['poster_uid'] );
		}
		if ( isset( $_POST['modifier_uid'] ) ) {
			$ret['modifier_uid'] = pico_main_get_uid( $_POST['modifier_uid'] );
		}
	}

	// version 2.3.0 HTML Purifier in Protector
/*     if ( file_exists( LIBRARY_PATH . '/htmlpurifier/library/HTMLPurifier.auto.php' ) ) {
		if ( is_object( $xoopsUser ) ) {
			$purifier_enable = 0 == count( array_intersect( $xoopsUser->getGroups(), @$mod_config['htmlpurify_except'] ) );
		} else {
			$purifier_enable = true;
		}

		$purifier_enable = $purifier_enable && ! isset( $filters['htmlspecialchars'] );

		if ( $purifier_enable ) {

			require_once LIBRARY_PATH . '/htmlpurifier/library/HTMLPurifier.auto.php';

			$config = HTMLPurifier_Config::createDefault();

			$config->set( 'Cache.SerializerPath', XOOPS_TRUST_PATH . '/modules/protector/configs' );

			$config->set( 'Core.Encoding', 'UTF-8' );

			//$config->set('HTML.Doctype', 'HTML 4.01 Transitional');
			$purifier = new HTMLPurifier( $config );

			if ( $_conv = ( _CHARSET !== 'UTF-8' ) ) {
				$_substitute = mb_substitute_character();
				mb_substitute_character( 'none' );
				$ret['body'] = mb_convert_encoding( $ret['body'], 'UTF-8', _CHARSET );
			}
			$ret['body'] = $purifier->purify( $ret['body'] );
			if ( $_conv ) {
				$ret['body'] = mb_convert_encoding( $ret['body'], _CHARSET, 'UTF-8' );
				mb_substitute_character( $_substitute );
			}
		}
	} */


// Start Code to review-  HTML Purifier in Protector
	if ( file_exists( LIBRARY_PATH . '/htmlpurifier/library/HTMLPurifier.auto.php' ) ) {
		if ( is_object( $xoopsUser ) ) {
			// Check if user belongs to any of the exempt groups
			$user_groups = $xoopsUser->getGroups();
			$exempt_groups = isset($mod_config['htmlpurify_except']) ? $mod_config['htmlpurify_except'] : [1];
			$purifier_enable = 0 == count( array_intersect( $user_groups, $exempt_groups ) );
		} else {
			$purifier_enable = true; // Always purify for guests
		}

		// Skip purification if htmlspecialchars filter is already applied
		$purifier_enable = $purifier_enable && ! isset( $filters['htmlspecialchars'] );

		if ( $purifier_enable ) {
			require_once LIBRARY_PATH . '/htmlpurifier/library/HTMLPurifier.auto.php';

			$config = HTMLPurifier_Config::createDefault();
			$config->set( 'Cache.SerializerPath', XOOPS_TRUST_PATH . '/modules/protector/configs' );
			$config->set( 'Core.Encoding', 'UTF-8' );
			
			// Allow more HTML elements if user is admin or moderator - why not all elements for Admins and limit here for other groups ?
			if ($isadminormod) {
				$config->set('HTML.Allowed', 'p,b,a[href|title],i,strong,em,br,img[src|alt|width|height],ul,ol,li,dl,dt,dd,table,tr,td,th,h1,h2,h3,h4,h5,h6,blockquote,pre,code,div,span[style],hr');
				$config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,color,background-color,text-align,margin,padding,border');
			}

			$purifier = new HTMLPurifier( $config );

			// Handle character encoding conversion if needed
			if ( $_conv = ( _CHARSET !== 'UTF-8' ) ) {
				$_substitute = mb_substitute_character();
				mb_substitute_character( 'none' );
				$ret['body'] = mb_convert_encoding( $ret['body'], 'UTF-8', _CHARSET );
			}
			
			$ret['body'] = $purifier->purify( $ret['body'] );
			
			if ( $_conv ) {
				$ret['body'] = mb_convert_encoding( $ret['body'], _CHARSET, 'UTF-8' );
				mb_substitute_character( $_substitute );
			}
		}
	}
// End Code to review - HTML Purifier in Protector

	// extra_fields (read ef class and create the object)
	$ef_class = empty( $mod_config['extra_fields_class'] ) ? 'PicoExtraFields' : preg_replace( '/[^0-9a-zA-Z_]/', '', $mod_config['extra_fields_class'] );

	require_once dirname( __DIR__ ) . '/class/' . $ef_class . '.class.php';

	$ef_obj = new $ef_class( $mydirname, $mod_config, $auto_approval, $isadminormod, $content_id );

	$ret['extra_fields'] = $ef_obj->getSerializedRequestsFromPost();

	return $ret;
}

// get uid from free form (numeric=uid, not numeric=uname)
function pico_main_get_uid( $text ) {
	$user_handler = &xoops_gethandler( 'user' );

	$text = trim( $text );

	if ( is_numeric( $text ) ) {
		$uid  = (int) $text;
		$user = &$user_handler->get( $uid );
		if ( is_object( $user ) ) {
			return $uid;
		} else {
			return 0;
		}
	} else {
		$users = &$user_handler->getObjects( new Criteria( 'uname', addslashes( $text ) ) ); // ???
		if ( is_object( @$users[0] ) ) {
			return $users[0]->getVar( 'uid' );
		} else {
			return 0;
		}
	}
}

// create content
function pico_makecontent( $mydirname, $auto_approval = true, $isadminormod = false ) {
	global $xoopsUser;

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$uid = is_object( $xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

	$errors = [];

	$requests = pico_get_requests4content( $mydirname, $errors, $auto_approval, $isadminormod );

	$requests += [ 'poster_uid' => $uid, 'modifier_uid' => $uid ];

	unset( $requests['specify_created_time'], $requests['specify_modified_time'], $requests['specify_expiring_time'], $requests['created_time_formatted'], $requests['modified_time_formatted'], $requests['expiring_time_formatted'] );

	$ignore_requests = $auto_approval ? [] : [ 'subject', 'htmlheader', 'body', 'visible' ];

	// only adminormod can set htmlheader
	if ( ! $isadminormod ) {
		$requests['htmlheader_waiting'] = $requests['htmlheader'];
		$ignore_requests[]              = 'htmlheader';
	}

	$set = $auto_approval ? '' : "visible=0,subject='" . _MD_PICO_WAITINGREGISTER . "',htmlheader='',body='',";

	foreach ( $requests as $key => $val ) {
		if ( in_array( $key, $ignore_requests ) ) {
			continue;
		}
		if ( 'vpath' == $key && empty( $val ) ) {
			$set .= "`$key`=null,";
		} else {
			$set .= "`$key`=" . $db->quoteString( $val ) . ',';
		}
	}

	// some patches about times
	$time4sql = '';

	if ( empty( $requests['created_time'] ) ) {
		$time4sql .= 'created_time=UNIX_TIMESTAMP(),';
	}
	if ( empty( $requests['modified_time'] ) ) {
		$time4sql .= 'modified_time=UNIX_TIMESTAMP(),';
	}
	if ( empty( $requests['expiring_time'] ) ) {
		$time4sql .= 'expiring_time=0x7fffffff,';
	}

	// do insert
	$sql = 'INSERT INTO ' . $db->prefix( $mydirname . '_contents' ) . " SET $set $time4sql poster_ip=" . $db->quoteString( @$_SERVER['REMOTE_ADDR'] ) . ',modifier_ip=' . $db->quoteString( @$_SERVER['REMOTE_ADDR'] ) . ",body_cached='',for_search=''";

	if ( ! $db->queryF( $sql ) ) {
		die( _MD_PICO_ERR_DUPLICATEDVPATH . ' or ' . _MD_PICO_ERR_SQL . __LINE__ );
	}

	$new_content_id = $db->getInsertId();

	pico_transact_reset_body_cached( $mydirname, $new_content_id );

	// rebuild category tree
	pico_sync_cattree( $mydirname );

	// update tags
	pico_sync_tags( $mydirname );

	return $new_content_id;
}

// update content
function pico_updatecontent( $mydirname, $content_id, $auto_approval = true, $isadminormod = false ) {
	global $xoopsUser;

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$errors = [];

	$requests = pico_get_requests4content( $mydirname, $errors, $auto_approval, $isadminormod, $content_id );

	unset( $requests['specify_created_time'],
		$requests['specify_modified_time'],
		$requests['specify_expiring_time'],
		$requests['created_time_formatted'],
		$requests['modified_time_formatted'],
		$requests['expiring_time_formatted'] );

	$ignore_requests = $auto_approval ? [] : [
		'subject',
		'htmlheader',
		'body',
		'visible',
		'filters',
		'show_in_navi',
		'show_in_menu',
		'allow_comment',
		'use_cache',
		'weight',
		'tags',
		'cat_id'
	];

	if ( ! $isadminormod ) {
		// only adminormod can set htmlheader
		$requests['htmlheader_waiting'] = $requests['htmlheader'];

		$ignore_requests[] = 'htmlheader';
	}

	$set = '';

	foreach ( $requests as $key => $val ) {
		if ( in_array( $key, $ignore_requests ) ) {
			continue;
		}
		if ( 'vpath' == $key && empty( $val ) ) {
			$set .= "`$key`=null,";
		} else {
			$set .= "`$key`=" . $db->quoteString( $val ) . ',';
		}
	}

	// some patches about times
	$time4sql = '';

	if ( empty( $requests['modified_time'] ) ) {
		$time4sql .= 'modified_time=UNIX_TIMESTAMP(),';
	}

	// backup the content, first
	pico_transact_backupcontent( $mydirname, $content_id );

	// do update
	$uid = is_object( $xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

	$sql = 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET modifier_uid='$uid', $set $time4sql modifier_ip=" . $db->quoteString( @$_SERVER['REMOTE_ADDR'] ) . ",body_cached='',for_search='' WHERE content_id=$content_id";

	if ( ! $db->queryF( $sql ) ) {
		die( _MD_PICO_ERR_DUPLICATEDVPATH . ' or ' . _MD_PICO_ERR_SQL . __LINE__ );
	}

	pico_transact_reset_body_cached( $mydirname, $content_id );

	// rebuild category tree
	pico_sync_cattree( $mydirname );

	// update tags
	pico_sync_tags( $mydirname );

	return $content_id;
}

// sync body_cached as body (HTML tags are stripped) for searching
function pico_transact_reset_body_cached( $mydirname, $content_id ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	[
		$use_cache,
		$body
	] = $db->fetchRow( $db->query( 'SELECT use_cache,body FROM ' . $db->prefix( $mydirname . '_contents' ) . " WHERE content_id=$content_id" ) );

	if ( empty( $body ) ) {
		return;
	}
	$body4sql = $use_cache ? '\'\'' : $db->quoteString( strip_tags( $body ) );

	$db->queryF( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET body_cached=$body4sql WHERE content_id=$content_id" );
}

// copy from waiting fields
function pico_transact_copyfromwaitingcontent( $mydirname, $content_id ) {
	global $xoopsUser;

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	// backup the content, first
	pico_transact_backupcontent( $mydirname, $content_id );

	$uid = is_object( $xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

	if ( ! $db->query( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET body=body_waiting, subject=subject_waiting, htmlheader=htmlheader_waiting, visible=1, approval=1 WHERE content_id=$content_id" ) ) {
		die( _MD_PICO_ERR_SQL . __LINE__ );
	}
	if ( ! $db->query( 'UPDATE ' . $db->prefix( $mydirname . '_contents' ) . " SET body_waiting='',subject_waiting='',htmlheader_waiting='' ,body_cached='',for_search='' WHERE content_id=$content_id" ) ) {
		die( _MD_PICO_ERR_SQL . __LINE__ );
	}

	// /*,`modified_time`=UNIX_TIMESTAMP(),modifier_uid='$uid',modifier_ip=".$db->quoteString(@$_SERVER['REMOTE_ADDR'])."*/

	return $content_id;
}

// store a content into history table (before delete or update)
function pico_transact_backupcontent( $mydirname, $content_id, $forced = false ) {
	global $xoopsUser, $xoopsModuleConfig;

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$histories_per_content = (int) @$xoopsModuleConfig['histories_per_content'];

	$minlifetime_per_history = (int) @$xoopsModuleConfig['minlifetime_per_history'];

	// fetch the latest history first
	[ $last_ch_id ] = $db->fetchRow( $db->query( 'SELECT MAX(content_history_id) FROM ' . $db->prefix( $mydirname . '_content_histories' ) . ' WHERE content_id=' . (int) $content_id ) );
	//FIX by domifara 2011.09.21
	//	list( $last_ch_modified , $last_ch_4search ) = $db->fetchRow( $db->query( "SELECT `modified_time`,MD5(`for_search`) FROM ".$db->prefix($mydirname."_content_histories")." WHERE content_history_id=".intval($last_ch_id) ) ) ;
	//	list( $current_4search ) = $db->fetchRow( $db->query( "SELECT MD5(`for_search`) FROM ".$db->prefix($mydirname."_contents")." WHERE content_id=".intval($content_id) ) ) ;

	[
		$last_ch_modified,
		$last_ch_4search
	] = $db->fetchRow( $db->query( 'SELECT `modified_time`,MD5(`body`) FROM ' . $db->prefix( $mydirname . '_content_histories' ) . ' WHERE content_history_id=' . (int) $last_ch_id ) );

	[ $current_4search ] = $db->fetchRow( $db->query( 'SELECT MD5(`body`) FROM ' . $db->prefix( $mydirname . '_contents' ) . ' WHERE content_id=' . (int) $content_id ) );

	// compare for_search fileld (it is not saved if identical)
	if ( ! $forced && $current_4search == $last_ch_4search ) {
		return;
	}

	// min life time of each history
	if ( $minlifetime_per_history > 0 && $last_ch_modified > time() - $minlifetime_per_history ) {
		return;
	}

	// max histories
	if ( $histories_per_content > 0 ) {
		do {
			[
				$ch_count,
				$min_ch_id
			] = $db->fetchRow( $db->query( 'SELECT COUNT(*),MIN(content_history_id) FROM ' . $db->prefix( $mydirname . '_content_histories' ) . ' WHERE content_id=' . (int) $content_id ) );
			if ( $ch_count >= $histories_per_content ) {
				$db->queryF( 'DELETE FROM ' . $db->prefix( $mydirname . '_content_histories' ) . ' WHERE content_history_id=' . (int) $min_ch_id );
			}
		} while ( $ch_count >= $histories_per_content );
	}

	$uid = is_object( $xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;
	if ( ! $db->queryF(
		'INSERT INTO '
		. $db->prefix( $mydirname . '_content_histories' ) . ' (content_id,vpath,cat_id,created_time,modified_time,poster_uid,poster_ip,modifier_uid,modifier_ip,subject,htmlheader,body,filters,tags,extra_fields) SELECT content_id,vpath,cat_id,created_time,modified_time,poster_uid,poster_ip,modifier_uid,modifier_ip,subject,htmlheader,body,filters,tags,extra_fields FROM '
		. $db->prefix( $mydirname . '_contents' ) . ' WHERE content_id='
		. (int) $content_id
	) ) {
		die( _MD_PICO_ERR_SQL . __LINE__ );
	}
}
