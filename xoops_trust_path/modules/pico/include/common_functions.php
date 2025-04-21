<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

// this file can be included from d3forum's blocks or getSublink().

require_once dirname( __DIR__ ) . '/class/PicoPermission.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelCategory.class.php';
require_once dirname( __DIR__ ) . '/class/PicoModelContent.class.php';

@include_once __DIR__ . '/constants.php';

if ( ! defined( '_MD_PICO_WRAPBASE' ) ) {
	require_once __DIR__ . '/constants.dist.php';
}


// get $cat_id from $content_id
function pico_common_get_cat_id_from_content_id( $mydirname, $content_id ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	[ $cat_id ] = $db->fetchRow( $db->query( 'SELECT cat_id FROM ' . $db->prefix( $mydirname . '_contents' ) . ' WHERE content_id=' . (int) $content_id ) );

	return (int) $cat_id;
}

// get both $categoryObj and $contentObj from specified content_id
function pico_common_get_objects_from_content_id( $mydirname, $content_id ) {
	$picoPermission = &PicoPermission::getInstance();

	$permissions = $picoPermission->getPermissions( $mydirname );

	$cat_id = pico_common_get_cat_id_from_content_id( $mydirname, $content_id );

	$categoryObj = new PicoCategory( $mydirname, (int) $cat_id, $permissions );

	$contentObj = new PicoContent( $mydirname, $content_id, $categoryObj );

	return [ $categoryObj, $contentObj ];
}

// deprecated
function pico_get_categories_can_read( $mydirname ) {
	return pico_common_get_categories_can_read( $mydirname );
}

// deprecated
function pico_common_get_categories_can_read( $mydirname, $uid = null ) {
	$cat_ids = [];
 $db = XoopsDatabaseFactory::getDatabaseConnection();

	if ( $uid > 0 ) {
		$user_handler = &xoops_gethandler( 'user' );
		$user         = &$user_handler->get( $uid );
	} else {
		$user = @$GLOBALS['xoopsUser'];
	}

	if ( is_object( $user ) ) {

		$uid = (int) $user->getVar( 'uid' );

		$groups = $user->getGroups();

		if ( ! empty( $groups ) ) {
			$whr4cat = "`uid`=$uid || `groupid` IN (" . implode( ',', $groups ) . ')';
		} else {
			$whr4cat = "`uid`=$uid";
		}
	} else {
		$whr4cat = '`groupid`=' . (int) XOOPS_GROUP_ANONYMOUS;
	}

	// get categories
	$sql = 'SELECT distinct c.cat_id FROM ' . $db->prefix( $mydirname . '_categories' ) . ' c LEFT JOIN ' . $db->prefix( $mydirname . '_category_permissions' ) . " cp ON c.cat_permission_id=cp.cat_id WHERE ($whr4cat)";

	$result = $db->query( $sql );

	if ( $result ) {
		while ( [$cat_id] = $db->fetchRow( $result ) ) {
			$cat_ids[] = (int) $cat_id;
		}
	}

	if ( empty( $cat_ids ) ) {
		return [ 0 ];
	} else {
		return $cat_ids;
	}
}

// deprecated
function pico_make_content_link4html( $mod_config, $content_row, $mydirname = null ) {
	return pico_common_make_content_link4html( $mod_config, $content_row, $mydirname );
}

// deprecated
function pico_common_make_content_link4html( $mod_config, $content_row, $mydirname = null ) {
	if ( ! empty( $mod_config['use_wraps_mode'] ) ) {
		// wraps mode
		if ( ! is_array( $content_row ) && ! empty( $mydirname ) ) {
			// specify content by content_id instead of content_row
			$db = XoopsDatabaseFactory::getDatabaseConnection();

			$content_row = $db->fetchArray( $db->query( 'SELECT content_id,vpath FROM ' . $db->prefix( $mydirname . '_contents' ) . ' WHERE content_id=' . (int) $content_row ) );
		}

		if ( ! empty( $content_row['vpath'] ) ) {
			$ret = 'index.php' . htmlspecialchars( $content_row['vpath'], ENT_QUOTES );
		} else {
			$ret = 'index.php' . sprintf( _MD_PICO_AUTONAME4SPRINTF, (int) $content_row['content_id'] );
		}

		return empty( $mod_config['use_rewrite'] ) ? $ret : substr( $ret, 10 );
	}

// normal mode
	$content_id = is_array( $content_row ) ? (int) $content_row['content_id'] : (int) $content_row;

	return empty( $mod_config['use_rewrite'] ) ? 'index.php?content_id=' . $content_id : substr( sprintf( _MD_PICO_AUTONAME4SPRINTF, $content_id ), 1 );
}

function pico_common_make_category_link4html( $mod_config, $cat_row, $mydirname = null ) {
	if ( ! empty( $mod_config['use_wraps_mode'] ) ) {
		if ( empty( $cat_row ) || ( is_array( $cat_row ) && 0 == $cat_row['cat_id'] ) ) {
			return '';
		}
		if ( ! is_array( $cat_row ) && ! empty( $mydirname ) ) {
			// specify category by cat_id instead of cat_row
			$db      = XoopsDatabaseFactory::getDatabaseConnection();
			$cat_row = $db->fetchArray( $db->query( 'SELECT cat_id,cat_vpath FROM ' . $db->prefix( $mydirname . '_categories' ) . ' WHERE cat_id=' . (int) $cat_row ) );
		}
		if ( ! empty( $cat_row['cat_vpath'] ) ) {
			$ret = 'index.php' . htmlspecialchars( $cat_row['cat_vpath'], ENT_QUOTES );
			if ( '/' != substr( $ret, - 1 ) ) {
				$ret .= '/';
			}
		} else {
			$ret = 'index.php' . sprintf( _MD_PICO_AUTOCATNAME4SPRINTF, (int) $cat_row['cat_id'] );
		}

		return empty( $mod_config['use_rewrite'] ) ? $ret : substr( $ret, 10 );
	}

// normal mode
	$cat_id = is_array( $cat_row ) ? (int) $cat_row['cat_id'] : (int) $cat_row;
	if ( $cat_id ) {
		return empty( $mod_config['use_rewrite'] ) ? 'index.php?cat_id=' . $cat_id : substr( sprintf( _MD_PICO_AUTOCATNAME4SPRINTF, $cat_id ), 1 );
	}

	return '';
}

function pico_common_get_submenu( $mydirname, $caller = 'xoops_version' ) {
	$myts = null;
 static $submenus_cache;

	if ( ! empty( $submenus_cache[ $caller ][ $mydirname ] ) ) {
		return $submenus_cache[ $caller ][ $mydirname ];
	}

	$module_handler = &xoops_gethandler( 'module' );

	$module = &$module_handler->getByDirname( $mydirname );

	if ( ! is_object( $module ) ) {
		return [];
	}
	$config_handler = &xoops_gethandler( 'config' );

	$mod_config = &$config_handler->getConfigsByCat( 0, $module->getVar( 'mid' ) );

	$db = XoopsDatabaseFactory::getDatabaseConnection();

	( method_exists( 'MyTextSanitizer', 'sGetInstance' ) and $myts = &MyTextSanitizer::sGetInstance() ) || $myts = &MyTextSanitizer::getInstance();

	$whr_read   = '`cat_id` IN (' . implode( ',', pico_common_get_categories_can_read( $mydirname ) ) . ')';
	$categories = [ 0 => [ 'pid' => - 1, 'name' => '', 'url' => '', 'sub' => [] ] ];

	// categories query
	$sql = 'SELECT cat_id,pid,cat_title,cat_vpath FROM ' . $db->prefix( $mydirname . '_categories' ) . " WHERE ($whr_read) ORDER BY cat_order_in_tree";
	$crs = $db->query( $sql );
	if ( $crs ) {
		while ( $cat_row = $db->fetchArray( $crs ) ) {
			$cat_id                = (int) $cat_row['cat_id'];
			$categories[ $cat_id ] = [
				'name'        => $myts->makeTboxData4Show( $cat_row['cat_title'], 1, 1 ),
				'url'         => pico_common_make_category_link4html( $mod_config, $cat_row ),
				'is_category' => true,
				'pid'         => $cat_row['pid'],
			];
		}
	}

	if ( ! ( 'sitemap_plugin' == $caller && ! @$mod_config['sitemap_showcontents'] ) && ! ( 'xoops_version' == $caller && ! @$mod_config['submenu_showcontents'] ) ) {
		// contents query
		$ors = $db->query( 'SELECT cat_id,content_id,vpath,subject FROM ' . $db->prefix( $mydirname . '_contents' ) . " WHERE show_in_menu AND visible AND created_time <= UNIX_TIMESTAMP() AND expiring_time > UNIX_TIMESTAMP() AND $whr_read ORDER BY weight,content_id" );

		if ( $ors ) {
			while ( $content_row = $db->fetchArray( $ors ) ) {
				$cat_id                         = (int) $content_row['cat_id'];
				$categories[ $cat_id ]['sub'][] = [
					'name'        => $myts->makeTboxData4Show( $content_row['subject'], 1, 1 ),
					'url'         => pico_common_make_content_link4html( $mod_config, $content_row ),
					'is_category' => false,
				];
			}
		}
	}

	// restruct categories
	$top_sub = ! empty( $categories[0]['sub'] ) ? $categories[0]['sub'] : [];

	$submenus_cache[ $caller ][ $mydirname ] = array_merge( $top_sub, pico_common_restruct_categories( $categories, 0 ) );

	return $submenus_cache[ $caller ][ $mydirname ];
}

function pico_common_restruct_categories( $categories, $parent ) {
	$ret = [];

	foreach ( $categories as $cat_id => $category ) {
		if ( $category['pid'] == $parent ) {
			if ( empty( $category['sub'] ) ) {
				$category['sub'] = [];
			}
			$ret[] = [
				'name'        => $category['name'],
				'url'         => $category['url'],
				'is_category' => $category['is_category'],
				'sub'         => array_merge( $category['sub'], pico_common_restruct_categories( $categories, $cat_id ) ),
			];
		}
	}

	return $ret;
}

function pico_common_utf8_encode_recursive( &$data ) {
	if ( is_array( $data ) ) {
		foreach ( array_keys( $data ) as $key ) {
			pico_common_utf8_encode_recursive( $data[ $key ] );
		}
	} else if ( ! is_numeric( $data ) ) {
		if ( XOOPS_USE_MULTIBYTES == 1 ) {
			if ( function_exists( 'mb_convert_encoding' ) ) {
				$data = mb_convert_encoding( $data, 'UTF-8', mb_internal_encoding() );
			}
		} else {
			$data = utf8_encode( $data );
		}
	}
}

// create category options as array
function pico_common_get_cat_options( $mydirname ) {
	$db = XoopsDatabaseFactory::getDatabaseConnection();

	$crs = $db->query( 'SELECT c.cat_id,c.cat_title,c.cat_depth_in_tree,COUNT(o.content_id) FROM ' . $db->prefix( $mydirname . '_categories' ) . ' c LEFT JOIN ' . $db->prefix( $mydirname . '_contents' ) . ' o ON c.cat_id=o.cat_id GROUP BY c.cat_id ORDER BY c.cat_order_in_tree' );

	$cat_options = [ 0 => _MD_PICO_TOP ];

	while ( [$id, $title, $depth, $contents_num] = $db->fetchRow( $crs ) ) {
		$cat_options[ $id ] = str_repeat( '--', $depth ) . htmlspecialchars( $title, ENT_QUOTES ) . " ($contents_num)";
	}

	return $cat_options;
}

// convert timezone user -> server
function pico_common_get_server_timestamp( $time ) {
	global $xoopsConfig, $xoopsUser;

	$offset = is_object( @$xoopsUser ) ? $xoopsUser->getVar( 'timezone_offset' ) : $xoopsConfig['default_TZ'];

	return $time - ( $offset - $xoopsConfig['server_TZ'] ) * 3600;
}

// reverse filter function of htmlspecialchars( , ENT_QUOTES ) ;
function pico_common_unhtmlspecialchars( $data ) {
	if ( is_array( $data ) ) {
		return array_map( 'pico_common_unhtmlspecialchars', $data );
	} else {
		return str_replace(
			[ '&lt;', '&gt;', '&amp;', '&quot;', '&#039;' ],
			[ '<', '>', '&', '"', "'" ],
			$data
		);
	}
}

function pico_common_serialize( $data ) {
	return var_export( $data, true );
}

function pico_common_unserialize( $serialized_data ) {
	if ( empty( $serialized_data ) ) {
		return [];
	}

	$ret = [];
	if ( strpos( trim( $serialized_data ), 'array' ) === 0 ) {
		// assume this is a string made from var_export( $var , true ) ;
		@eval( '$ret=' . $serialized_data . ';' );
	} else {
		// try PHP built-in unserialize()
		$ret = @unserialize( $serialized_data );
	}

	if ( ! is_array( $ret ) ) {
		$ret = [];
	}

	return $ret;
}

if ( ! function_exists( 'htmlspecialchars_ent' ) ) {
	function htmlspecialchars_ent( $string ) {
		return htmlspecialchars( $string, ENT_QUOTES );
	}
}

// Include error handler but check if $mydirname is defined before using it
include_once __DIR__ . '/error_handler.php';
// Only register the error handler when the module dirname is defined
if (isset($mydirname) && $mydirname) {
    pico_register_error_handler($mydirname);
}
