<?php
/**
 * Sitemap blocks
 * Automated Sitemap and XML file for search engines
 * @package    Sitemap
 * @version    2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     chanoir
 * @copyright  (c) 2005-2024 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/docs/GPL_V2.txt
 */

function b_sitemap_show( $options )
{
	$member_handler = null;
	$sitemap_configsBackup = null;
	global $xoopsConfig, $xoopsDB, $xoopsUser, $xoopsUserIsAdmin;
	global $sitemap_configs ;

	$cols = empty( $options[0] ) ? 1 : (int) $options[0];

	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname('sitemap');
	$config_handler =& xoops_gethandler('config');
	$sitemap_configs = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

	$block = [];
	
	include_once(XOOPS_ROOT_PATH . '/modules/sitemap/include/sitemap.php');

	// for All-time guest mode (backup uid & set as Guest)
	if( is_object( $xoopsUser ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
		$backup_uid = $xoopsUser->getVar('uid') ;
		$backup_userisadmin = $xoopsUserIsAdmin ;
		$member_handler =& xoops_gethandler('member');
		$xoopsUser =& $member_handler->getUser( 0 ) ;
		$xoopsUserIsAdmin = false ;
	}

	$sitemap = sitemap_show();

	// for All-time guest mode (restore $xoopsUser*)
	if( ! empty( $backup_uid ) && ! empty( $sitemap_configs['alltime_guest'] ) ) {
		$member_handler =& xoops_gethandler('member');
		$xoopsUser =& $member_handler->getUser( $backup_uid ) ;
		$xoopsUserIsAdmin = $backup_userisadmin ;
	}

	$myts =& MyTextSanitizer::getInstance();

	$block['this']['mods'] = 'sitemap';
	$block['cols'] = $cols ;
	$block['div_width'] = '-'.$cols ;
	$block['sitemap'] = $sitemap;
	$block['msgs'] = $myts->displayTarea( $sitemap_configs['msgs'] , 1 ) ;
	$block['show_subcategoris'] = $sitemap_configs['show_subcategoris'];

	if( $sitemap_configs['alltime_guest'] ) {
		$block['isuser'] = 0 ;
		$block['isadmin'] = 0 ;
	} else {
		$block['isuser'] = is_object( $xoopsUser ) ;
		$block['isadmin'] = $xoopsUserIsAdmin ;
	}

	$sitemap_configs = $sitemap_configsBackup ;

	return $block;
}

function b_sitemap_edit( $options )
{
	return '
		'._MB_SITEMAP_COLS.': <input type="text" size="2" maxlength="2" name="options[0]" value="'. (int)$options[0] .'" />
	' ;
}
