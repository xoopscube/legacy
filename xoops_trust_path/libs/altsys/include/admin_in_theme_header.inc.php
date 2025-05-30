<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Admin in theme. This is a mimic file from header.php
 * @package    Altsys
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once dirname( __DIR__ ) . '/class/AltsysBreadcrumbs.class.php';

include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';

$xoopsOption['theme_use_smarty'] = 1;
// include Smarty template engine and initialize it
require_once XOOPS_ROOT_PATH . '/class/template.php';
$xoopsTpl = new XoopsTpl();
$xoopsTpl->xoops_setCaching( 2 );
if ( $xoopsConfig['debug_mode'] == 3) {
	$xoopsTpl->xoops_setDebugging( true );
}
$xoopsTpl->assign( [
	'xoops_theme'      => $xoopsConfig['theme_set'],
	'xoops_imageurl'   => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
	'xoops_themecss'   => xoops_getcss( $xoopsConfig['theme_set'] ),
	'xoops_requesturi' => htmlspecialchars( $GLOBALS['xoopsRequestUri'], ENT_QUOTES ),
	'xoops_sitename'   => htmlspecialchars( $xoopsConfig['sitename'], ENT_QUOTES ),
	'xoops_slogan'     => htmlspecialchars( $xoopsConfig['slogan'], ENT_QUOTES )
] );

// Meta tags
$config_handler =& xoops_gethandler( 'config' );
$criteria       = new CriteriaCompo( new Criteria( 'conf_modid', 0 ) );
$criteria->add( new Criteria( 'conf_catid', XOOPS_CONF_METAFOOTER ) );
$config = $config_handler->getConfigs( $criteria, true );
foreach ( array_keys( $config ) as $i ) {
	// prefix each tag with 'xoops_'
	$xoopsTpl->assign( 'xoops_' . $config[ $i ]->getVar( 'conf_name' ), $config[ $i ]->getConfValueForOutput() );
}

// unset($config);
// Weird, but need extra <script> tags for 2.0.x themes
// $xoopsTpl->assign('xoops_js', '//--></script><script type="text/javascript" src="'.XOOPS_URL.'/include/xoops.js"></script><script type="text/javascript"><!--');
// get all blocks and assign to smarty

// HACK by domifara
if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
	$handler    =& xoops_gethandler( 'block' );
	$xoopsblock =& $handler->create( false );
} else {
	$xoopsblock = new XoopsBlock();
}

$block_arr = [];

if ( is_object( $xoopsUser ) ) {
	$xoopsTpl->assign( [ 'xoops_isuser'  => true,
	                     'xoops_userid'  => $xoopsUser->getVar( 'uid' ),
	                     'xoops_uname'   => $xoopsUser->getVar( 'uname' ),
	                     'xoops_isadmin' => $xoopsUserIsAdmin
	] );
	if ( is_object( @$xoopsModule ) ) {
		if ( 1 == $xoopsModule->getVar( 'mid' ) && 'preferences' == @$_GET['fct'] && 'showmod' == @$_GET['op'] && ! empty( $_GET['mod'] ) ) {
			$module_handler =& xoops_gethandler( 'module' );
			$target_module  = $module_handler->get( (int) $_GET['mod'] );
		} else {
			$target_module =& $xoopsModule;
		}

		// set page title
		$xoopsTpl->assign( [ 'xoops_pagetitle'  => $target_module->getVar( 'name' ),
		                     'xoops_modulename' => $target_module->getVar( 'name' ),
		                     'xoops_dirname'    => $target_module->getVar( 'dirname' )
		] );

		// xoops_breadcrumbs
		$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
		if ( $breadcrumbsObj->hasPaths() ) {
			$xoops_breadcrumbs = $breadcrumbsObj->getXoopsbreadcrumbs();
		} else {
			$mod_url           = XOOPS_URL . '/modules/' . $target_module->getVar( 'dirname' );
			$mod_path          = XOOPS_ROOT_PATH . '/modules/' . $target_module->getVar( 'dirname' );
			$modinfo           = $target_module->getInfo();
			$xoops_breadcrumbs = [];
			if ( ! empty( $modinfo['hasMain'] ) ) {
				$xoops_breadcrumbs[] = [
					'url'  => $mod_url . '/',
					'name' => sprintf( _MD_A_AINTHEME_FMT_PUBLICTOP, $target_module->getVar( 'name' ) ),
				];
			}
			if ( ! empty( $modinfo['adminindex'] ) ) {
				$xoops_breadcrumbs[] = [
					'url'  => $mod_url . '/' . $modinfo['adminindex'],
					'name' => sprintf( _MD_A_AINTHEME_FMT_ADMINTOP, $target_module->getVar( 'name' ) ),
				];
			}
			if ( ! empty( $GLOBALS['altsysAdminPageTitle'] ) ) {
				$xoops_breadcrumbs[] = [ 'name' => htmlspecialchars( $GLOBALS['altsysAdminPageTitle'], ENT_QUOTES ) ];
			} elseif ( ! empty( $modinfo['adminmenu'] ) ) {
				@include $mod_path . '/' . $modinfo['adminmenu'];
				if ( is_array( @$adminmenu ) ) {
					foreach ( $adminmenu as $eachmenu ) {
						//if ( strstr( $_SERVER['REQUEST_URI'], $eachmenu['link'] ) ) {
						if ( false !== strpos( $_SERVER['REQUEST_URI'], (string) $eachmenu['link'] ) ) {
							$xoops_breadcrumbs[] = [ 'name' => $eachmenu['title'] ];
							break;
						}
					}
				}
			}
		}

		//$block_arr =& $xoopsblock->getAllByGroupModule($xoopsUser->getGroups(), $target_module->getVar('mid'), false, XOOPS_BLOCK_VISIBLE);
	} else {
		$xoopsTpl->assign( [ 'xoops_pagetitle' => _CPHOME ] );
		$xoops_breadcrumbs = [
			[
				'url'  => XOOPS_URL . '/admin.php',
				'name' => _CPHOME,
			]
		];
	}
} else {
	exit;
}

// get block_arr
$db     =& XoopsDatabaseFactory::getDatabaseConnection();
$sql    = 'SELECT DISTINCT gperm_itemid FROM ' . $db->prefix( 'group_permission' ) . " WHERE gperm_name = 'block_read' AND gperm_modid = 1 AND gperm_groupid IN (" . implode( ',', $xoopsUser->getGroups() ) . ')';
$result = $db->query( $sql );

$blockids = [];
while ( [$blockid] = $db->fetchRow( $result ) ) {
	$blockids[] = (int) $blockid;
}

global $block_arr, $i; // for piCal :-)
$block_arr = [];
if ( ! empty( $blockids ) ) {
	$sql    = 'SELECT b.* FROM ' . $db->prefix( 'newblocks' ) . ' b, ' . $db->prefix( 'block_module_link' ) . ' m WHERE m.block_id=b.bid AND b.isactive=1 AND b.visible=1 AND m.module_id=' . (int) $altsysModuleId . ' AND b.bid IN (' . implode( ',', $blockids ) . ') ORDER BY b.weight,b.bid';
	$result = $db->query( $sql );
	while ( $myrow = $db->fetchArray( $result ) ) {

//HACK by domifara
		if ( defined( 'XOOPS_CUBE_LEGACY' ) ) {
			$block =& $handler->create( false );
			$block->assignVars( $myrow );
		} else {
			$block = new XoopsBlock( $myrow );
		}

		$block_arr[ $myrow['bid'] ] = $block;
	}
}

$adminmenublock_exists = false;
foreach ( array_keys( $block_arr ) as $i ) {
	if ( 'b_altsys_admin_menu_show' == $block_arr[ $i ]->getVar( 'show_func' ) ) {
		$adminmenublock_exists = true;
	}
	$bcachetime = $block_arr[ $i ]->getVar( 'bcachetime' );
	if ( empty( $bcachetime ) ) {
		$xoopsTpl->xoops_setCaching( 0 );
	} else {
		$xoopsTpl->xoops_setCaching( 2 );
		$xoopsTpl->xoops_setCacheTime( $bcachetime );
	}
	$btpl = $block_arr[ $i ]->getVar( 'template' );
	if ( '' != $btpl ) {
		if ( empty( $bcachetime ) || ! $xoopsTpl->is_cached( 'db:' . $btpl, 'blk_' . $block_arr[ $i ]->getVar( 'bid' ) ) ) {
			$xoopsLogger->addBlock( $block_arr[ $i ]->getVar( 'name' ) );
			$bresult = $block_arr[ $i ]->buildBlock();
			if ( ! $bresult ) {
				continue;
			}
			$xoopsTpl->assign_by_ref( 'block', $bresult );
			$bcontent = $xoopsTpl->fetch( 'db:' . $btpl, 'blk_' . $block_arr[ $i ]->getVar( 'bid' ) );
			$xoopsTpl->clear_assign( 'block' );
		} else {
			$xoopsLogger->addBlock( $block_arr[ $i ]->getVar( 'name' ), true, $bcachetime );
			$bcontent = $xoopsTpl->fetch( 'db:' . $btpl, 'blk_' . $block_arr[ $i ]->getVar( 'bid' ) );
		}
	} else {
		$bid = $block_arr[ $i ]->getVar( 'bid' );
		if ( empty( $bcachetime ) || ! $xoopsTpl->is_cached( 'db:system_dummy.html', 'blk_' . $bid ) ) {
			$xoopsLogger->addBlock( $block_arr[ $i ]->getVar( 'name' ) );
			$bresult = $block_arr[ $i ]->buildBlock();
			if ( ! $bresult ) {
				continue;
			}
			$xoopsTpl->assign_by_ref( 'dummy_content', $bresult['content'] );
			$bcontent = $xoopsTpl->fetch( 'db:system_dummy.html', 'blk_' . $bid );
			$xoopsTpl->clear_assign( 'block' );
		} else {
			$xoopsLogger->addBlock( $block_arr[ $i ]->getVar( 'name' ), true, $bcachetime );
			$bcontent = $xoopsTpl->fetch( 'db:system_dummy.html', 'blk_' . $bid );
		}
	}
	switch ( $block_arr[ $i ]->getVar( 'side' ) ) {
		case XOOPS_SIDEBLOCK_LEFT:
			if ( ! isset( $show_lblock ) ) {
				$xoopsTpl->assign( 'xoops_showlblock', 1 );
				$show_lblock = 1;
			}
			$xoopsTpl->append( 'xoops_lblocks', [ 'title'   => $block_arr[ $i ]->getVar( 'title' ),
			                                      'content' => $bcontent,
			                                      'weight'  => $block_arr[ $i ]->getVar( 'weight' )
			] );
			break;
		case XOOPS_CENTERBLOCK_LEFT:
			if ( ! isset( $show_cblock ) ) {
				$xoopsTpl->assign( 'xoops_showcblock', 1 );
				$show_cblock = 1;
			}
			$xoopsTpl->append( 'xoops_clblocks', [ 'title'   => $block_arr[ $i ]->getVar( 'title' ),
			                                       'content' => $bcontent,
			                                       'weight'  => $block_arr[ $i ]->getVar( 'weight' )
			] );
			break;
		case XOOPS_CENTERBLOCK_RIGHT:
			if ( ! isset( $show_cblock ) ) {
				$xoopsTpl->assign( 'xoops_showcblock', 1 );
				$show_cblock = 1;
			}
			$xoopsTpl->append( 'xoops_crblocks', [ 'title'   => $block_arr[ $i ]->getVar( 'title' ),
			                                       'content' => $bcontent,
			                                       'weight'  => $block_arr[ $i ]->getVar( 'weight' )
			] );
			break;
		case XOOPS_CENTERBLOCK_CENTER:
			if ( ! isset( $show_cblock ) ) {
				$xoopsTpl->assign( 'xoops_showcblock', 1 );
				$show_cblock = 1;
			}
			$xoopsTpl->append( 'xoops_ccblocks', [ 'title'   => $block_arr[ $i ]->getVar( 'title' ),
			                                       'content' => $bcontent,
			                                       'weight'  => $block_arr[ $i ]->getVar( 'weight' )
			] );
			break;
		case XOOPS_SIDEBLOCK_RIGHT:
			if ( ! isset( $show_rblock ) ) {
				$xoopsTpl->assign( 'xoops_showrblock', 1 );
				$show_rblock = 1;
			}
			$xoopsTpl->append( 'xoops_rblocks', [ 'title'   => $block_arr[ $i ]->getVar( 'title' ),
			                                      'content' => $bcontent,
			                                      'weight'  => $block_arr[ $i ]->getVar( 'weight' )
			] );
			break;
	}
	unset( $bcontent );
}

// FALLBACK inserting admin_menu_block in admin side
if ( ! $adminmenublock_exists ) {
	require_once XOOPS_ROOT_PATH . '/modules/altsys/blocks/blocks.php';

	$admin_menu_block = [ b_altsys_admin_menu_show( [ 'altsys' ] ) ];

	$admin_menu_block[0]['title'] = 'Admin Menu';
	$lblocks                      = $xoopsTpl->get_template_vars( 'xoops_lblocks' );

	if ( ! is_array( $lblocks ) ) {
		$lblocks = [];
	}
	$xoopsTpl->assign( 'xoops_lblocks', array_merge( $admin_menu_block, $lblocks ) );
}

//unset($block_arr);
if ( ! isset( $show_lblock ) ) {
	$xoopsTpl->assign( 'xoops_showlblock', 0 );
}
if ( ! isset( $show_rblock ) ) {
	$xoopsTpl->assign( 'xoops_showrblock', 0 );
}
if ( ! isset( $show_cblock ) ) {
	$xoopsTpl->assign( 'xoops_showcblock', 0 );
}

$xoopsTpl->xoops_setCaching( 0 );
