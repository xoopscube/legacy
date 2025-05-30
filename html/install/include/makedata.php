<?php
/**
 * Make Data
 * @package    XCL
 * @subpackage Installation Wizard
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

include_once './class/dbmanager.php';

// RMV
// TODO: Shouldn't we insert specific field names??  That way we can use
// the defaults specified in the database...!!!! (and don't have problem
// of missing fields in install file, when add new fields to database)

function make_groups( &$dbm ) {
	$gruops = [];
 $gruops['XOOPS_GROUP_ADMIN']     = $dbm->insert( 'groups', " VALUES (0, '" . addslashes( _INSTALL_WEBMASTER ) . "', '" . addslashes( _INSTALL_WEBMASTERD ) . "', 'Admin')" );
	$gruops['XOOPS_GROUP_USERS']     = $dbm->insert( 'groups', " VALUES (0, '" . addslashes( _INSTALL_REGUSERS ) . "', '" . addslashes( _INSTALL_REGUSERSD ) . "', 'User')" );
	$gruops['XOOPS_GROUP_ANONYMOUS'] = $dbm->insert( 'groups', " VALUES (0, '" . addslashes( _INSTALL_ANONUSERS ) . "', '" . addslashes( _INSTALL_ANONUSERSD ) . "', 'Anonymous')" );

	if ( ! $gruops['XOOPS_GROUP_ADMIN'] || ! $gruops['XOOPS_GROUP_USERS'] || ! $gruops['XOOPS_GROUP_ANONYMOUS'] ) {
		return false;
	}

	return $gruops;
}

function make_data( &$dbm, &$cm, $adminname, $adminpass, $adminmail, $language, $gruops, $timezone ) {
	$tables = [];


	// default theme

	$time = time();
	$dbm->insert( 'tplset', " VALUES (1, 'default', 'Default Template Set', '', " . $time . ')' );

	// data for table 'config'

	$dbm->insert( 'config', " VALUES (1, 0, 1, 'sitename', '_MD_AM_SITENAME', 'Web Application Platform', '_MD_AM_SITENAMEDSC', 'textbox', 'text', 0)" );
	$dbm->insert( 'config', " VALUES (2, 0, 1, 'slogan', '_MD_AM_SLOGAN', 'Just Use it!', '_MD_AM_SLOGANDSC', 'textbox', 'text', 2)" );
	$dbm->insert( 'config', " VALUES (3, 0, 1, 'language', '_MD_AM_LANGUAGE', '" . addslashes( $language ) . "', '_MD_AM_LANGUAGEDSC', 'language', 'other', 4)" );
	$dbm->insert( 'config', " VALUES (4, 0, 1, 'startpage', '_MD_AM_STARTPAGE', '--', '_MD_AM_STARTPAGEDSC', 'startpage', 'other', 6)" );
	//Get Server timezone Setting
	//if ( PHP_VERSION_ID >= 50100 && function_exists( 'date_default_timezone_set' ) ) {
		date_default_timezone_set( $timezone );
	//}
	$time_diff_val = date( 'O' );
	$time_diff     = (float) ( $time_diff_val[0] . ( substr( $time_diff_val, 1, 2 ) + substr( $time_diff_val, 3, 2 ) / 60 ) );
	$dbm->insert( 'config', " VALUES (5, 0, 1, 'server_TZ', '_MD_AM_SERVERTZ', '" . $time_diff . "', '_MD_AM_SERVERTZDSC', 'timezone', 'float', 8)" );
	$dbm->insert( 'config', " VALUES (6, 0, 1, 'default_TZ', '_MD_AM_DEFAULTTZ', '" . $time_diff . "', '_MD_AM_DEFAULTTZDSC', 'timezone', 'float', 10)" );
	$dbm->insert( 'config', " VALUES (7, 0, 1, 'theme_set', '_MD_AM_DTHEME', 'xcl_default', '_MD_AM_DTHEMEDSC', 'theme', 'other', 12)" );
	$dbm->insert( 'config', " VALUES (8, 0, 1, 'anonymous', '_MD_AM_ANONNAME', '" . addslashes( _INSTALL_ANON ) . "', '_MD_AM_ANONNAMEDSC', 'textbox', 'text', 15)" );
	$dbm->insert( 'config', " VALUES (9, 0, 1, 'gzip_compression', '_MD_AM_USEGZIP', '0', '_MD_AM_USEGZIPDSC', 'yesno', 'int', 16)" );
	$dbm->insert( 'config', " VALUES (11, 0, 1, 'session_expire', '_MD_AM_SESSEXPIRE', '15', '_MD_AM_SESSEXPIREDSC', 'textbox', 'int', 22)" );
	$dbm->insert( 'config', " VALUES (13, 0, 1, 'debug_mode', '_MD_AM_DEBUGMODE', '1', '_MD_AM_DEBUGMODEDSC', 'select', 'int', 24)" );
	$dbm->insert( 'config', " VALUES (14, 0, 1, 'my_ip', '_MD_AM_MYIP', '127.0.0.1', '_MD_AM_MYIPDSC', 'textbox', 'text', 29)" );
	$dbm->insert( 'config', " VALUES (15, 0, 1, 'use_ssl', '_MD_AM_USESSL', '0', '_MD_AM_USESSLDSC', 'yesno', 'int', 30)" );
	$dbm->insert( 'config', " VALUES (16, 0, 1, 'session_name', '_MD_AM_SESSNAME', 'xcl_wap_session', '_MD_AM_SESSNAMEDSC', 'textbox', 'text', 20)" );
	$dbm->insert( 'config', " VALUES (30, 0, 1, 'adminmail', '_MD_AM_ADMINML', '" . addslashes( $adminmail ) . "', '_MD_AM_ADMINMLDSC', 'textbox', 'text', 3)" );
	$dbm->insert( 'config', " VALUES (32, 0, 1, 'com_mode', '_MD_AM_COMMODE', 'nest', '_MD_AM_COMMODEDSC', 'select', 'text', 34)" );
	$dbm->insert( 'config', " VALUES (33, 0, 1, 'com_order', '_MD_AM_COMORDER', '0', '_MD_AM_COMORDERDSC', 'select', 'int', 36)" );
	$dbm->insert( 'config', " VALUES (37, 0, 1, 'bad_ips', '_MD_AM_BADIPS', '" . addslashes( serialize( [ '127.0.0.1' ] ) ) . "', '_MD_AM_BADIPSDSC', 'textarea', 'array', 42)" );
	$dbm->insert( 'config', " VALUES (40, 0, 4, 'censor_enable', '_MD_AM_DOCENSOR', '0', '_MD_AM_DOCENSORDSC', 'yesno', 'int', 0)" );
	$dbm->insert( 'config', " VALUES (41, 0, 4, 'censor_words', '_MD_AM_CENSORWRD', '" . addslashes( serialize( [
			'fuck',
			'shit'
		] ) ) . "', '_MD_AM_CENSORWRDDSC', 'textarea', 'array', 1)" );
	$dbm->insert( 'config', " VALUES (42, 0, 4, 'censor_replace', '_MD_AM_CENSORRPLC', '#OOPS#', '_MD_AM_CENSORRPLCDSC', 'textbox', 'text', 2)" );
	$dbm->insert( 'config', " VALUES (44, 0, 5, 'enable_search', '_MD_AM_DOSEARCH', '1', '_MD_AM_DOSEARCHDSC', 'yesno', 'int', 0)" );
	$dbm->insert( 'config', " VALUES (45, 0, 5, 'keyword_min', '_MD_AM_MINSEARCH', '5', '_MD_AM_MINSEARCHDSC', 'textbox', 'int', 1)" );
	$dbm->insert( 'config', " VALUES (47, 0, 1, 'enable_badips', '_MD_AM_DOBADIPS', '0', '_MD_AM_DOBADIPSDSC', 'yesno', 'int', 40)" );
	$dbm->insert( 'config', " VALUES (48, 0, 1, 'cool_uri', '_MD_AM_COOLURI', '0', '_MD_AM_COOLURIDSC', 'yesno', 'int', 17)" );
	$dbm->insert( 'config', " VALUES (53, 0, 1, 'use_mysession', '_MD_AM_USEMYSESS', '0', '_MD_AM_USEMYSESSDSC', 'yesno', 'int', 19)" );
	$dbm->insert( 'config', " VALUES (57, 0, 1, 'theme_fromfile', '_MD_AM_THEMEFILE', '1', '_MD_AM_THEMEFILEDSC', 'yesno', 'int', 13)" );
	$dbm->insert( 'config', " VALUES (58, 0, 1, 'closesite', '_MD_AM_CLOSESITE', '1', '_MD_AM_CLOSESITEDSC', 'yesno', 'int', 26)" );
	$dbm->insert( 'config', " VALUES (59, 0, 1, 'closesite_okgrp', '_MD_AM_CLOSESITEOK', '" . addslashes( serialize( [ '1' ] ) ) . "', '_MD_AM_CLOSESITEOKDSC', 'group_multi', 'array', 27)" );
	$dbm->insert( 'config', " VALUES (60, 0, 1, 'closesite_text', '_MD_AM_CLOSESITETXT', '" . _INSTALL_L165 . "', '_MD_AM_CLOSESITETXTDSC', 'textarea', 'text', 28)" );
	$dbm->insert( 'config', " VALUES (61, 0, 1, 'sslpost_name', '_MD_AM_SSLPOST', 'wap_ssl', '_MD_AM_SSLPOSTDSC', 'textbox', 'text', 31)" );
	$dbm->insert( 'config', " VALUES (62, 0, 1, 'module_cache', '_MD_AM_MODCACHE', '', '_MD_AM_MODCACHEDSC', 'module_cache', 'array', 50)" );
	$dbm->insert( 'config', " VALUES (63, 0, 1, 'template_set', '_MD_AM_DTPLSET', 'default', '_MD_AM_DTPLSETDSC', 'tplset', 'other', 14)" );

	$dbm->insert( 'config', " VALUES (64,0,6,'mailmethod','_MD_AM_MAILERMETHOD','mail','_MD_AM_MAILERMETHODDESC','select','text',4)" );
	$dbm->insert( 'config', " VALUES (65,0,6,'smtphost','_MD_AM_SMTPHOST','a:1:{i:0;s:0:\"\";}', '_MD_AM_SMTPHOSTDESC','textarea','array',6)" );
	$dbm->insert( 'config', " VALUES (66,0,6,'smtpuser','_MD_AM_SMTPUSER','','_MD_AM_SMTPUSERDESC','textbox','text',7)" );
	$dbm->insert( 'config', " VALUES (67,0,6,'smtppass','_MD_AM_SMTPPASS','','_MD_AM_SMTPPASSDESC','password','text',8)" );
	$dbm->insert( 'config', " VALUES (68,0,6,'sendmailpath','_MD_AM_SENDMAILPATH','/usr/sbin/sendmail','_MD_AM_SENDMAILPATHDESC','textbox','text',5)" );
	$dbm->insert( 'config', " VALUES (69,0,6,'from','_MD_AM_MAILFROM','','_MD_AM_MAILFROMDESC','textbox','text', 1)" );
	$dbm->insert( 'config', " VALUES (70,0,6,'fromname','_MD_AM_MAILFROMNAME','','_MD_AM_MAILFROMNAMEDESC','textbox','text',2)" );
	$dbm->insert( 'config', " VALUES (71,0,1,'sslloginlink', '_MD_AM_SSLLINK', 'https://', '_MD_AM_SSLLINKDSC', 'textbox', 'text', 33)" );
	$dbm->insert( 'config', " VALUES (72,0,1,'theme_set_allowed', '_MD_AM_THEMEOK', '" . serialize( [ 'xcl_default', 'bs5-starter' ] ) . "', '_MD_AM_THEMEOKDSC', 'theme_multi', 'array', 13)" );
	// RMV-NOTIFY... Need to specify which user is sender of notification PM
	$dbm->insert( 'config', " VALUES (73,0,6,'fromuid','_MD_AM_MAILFROMUID','1','_MD_AM_MAILFROMUIDDESC','user','int',3)" );
	// data for table 'users'

	$temp    = md5( $adminpass );
	$regdate = time();
	$dbm->insert( 'users', " VALUES (1,'','" . addslashes( $adminname ) . "','" . addslashes( $adminmail ) . "','" . XOOPS_URL . "/','blank.gif','" . $regdate . "','','','',1,'','','','','" . $temp . "',0,0,7,5,'xcl_default','" . $time_diff . "'," . time() . ",'thread',0,1,0,'','','',0)" );

	// data for table 'groups_users_link'

	$dbm->insert( 'groups_users_link', ' VALUES (0, ' . $gruops['XOOPS_GROUP_ADMIN'] . ', 1)' );
	$dbm->insert( 'groups_users_link', ' VALUES (0, ' . $gruops['XOOPS_GROUP_USERS'] . ', 1)' );

    // Block Welcome
    // $dbm->insert( 'groups_users_link', ' VALUES (11, ' . $gruops['XOOPS_GROUP_ADMIN'] . ',	9)' );

	return $gruops;
}

// ToDo : remove following lines
/**
 *
 * Install module by module's name, language and specified mid.
 *
 * @param object $dbm Database manager instance
 * @param int    $mid module's ID
 * @param string $module module's name
 * @param        $module_name
 * @param string $language language
 * @param        $groups
 */
function installModule( &$dbm, $mid, $module, $module_name, $language = 'english', &$groups ) {
	if ( file_exists( "../modules/{$module}/language/{$language}/modinfo.php" ) ) {
		include "../modules/{$module}/language/{$language}/modinfo.php";
	} else {
		include "../modules/{$module}/language/english/modinfo.php";
		$language = 'english';
	}

	$modversion = [];
	require_once "../modules/{$module}/xoops_version.php";
	$time = time();

	// RMV-NOTIFY (updated for extra column in table)
	//
	// TODO We should set hasconfig and more option values here.
	//
	$hasconfig = isset( $modversion['config'] ) ? 1 : 0;
	$hasmain   = 0;
	if ( isset( $modversion['hasMain'] ) && ( 1 === $modversion['hasMain'] ) ) {
		$hasmain = 1;
	}
	$dbm->insert( 'modules', " VALUES ({$mid}, '" . constant( $module_name ) . "', 100, " . $time . ", 0, 1, '{$module}', {$hasmain}, 1, 0, {$hasconfig}, 0, 0)" );

	//
	// Database
	// TODO Dependence on mysql, Now.
	//
	if ( isset( $modversion['sqlfile']['mysql'] ) ) {
		$dbm->queryFromFile( "../modules/{$module}/" . $modversion['sqlfile']['mysql'] );
	}

	if ( is_array( $modversion['templates'] ) && count( $modversion['templates'] ) > 0 ) {
		foreach ( $modversion['templates'] as $tplfile ) {
			if ( $fp = fopen( "../modules/{$module}/templates/" . $tplfile['file'], 'r' ) ) {
				$newtplid = $dbm->insert( 'tplfile', " VALUES (0, {$mid}, '{$module}', 'default', '" . addslashes( $tplfile['file'] ) . "', '" . addslashes( $tplfile['description'] ) . "', " . $time . ', ' . $time . ", 'module')" );
				//$newtplid = $xoopsDB->getInsertId();
				if ( filesize( "../modules/{$module}/templates/" . $tplfile['file'] ) > 0 ) {
					$tplsource = fread( $fp, filesize( "../modules/{$module}/templates/" . $tplfile['file'] ) );
				} else {
					$tplsource = '';
				}
				fclose( $fp );
				$dbm->insert( 'tplsource', ' (tpl_id, tpl_source) VALUES (' . $newtplid . ", '" . addslashes( $tplsource ) . "')" );
			}
		}
	}

	if ( is_array( $modversion['blocks'] ) && count( $modversion['blocks'] ) > 0 ) {
		foreach ( $modversion['blocks'] as $func_num => $newblock ) {
			if ( $fp = fopen( '../modules/{$module}/templates/blocks/' . $newblock['template'], 'rb' ) ) {
				//
				// The following checking is dependence on the structure of system module.
				//
				if ( in_array( $newblock['template'], [
					'system_block_user.html',
					'system_block_login.html',
					'system_block_mainmenu.html'
				] ) ) {
					$visible = 1;
				} else {
					$visible = 0;
				}
				$options   = ! isset( $newblock['options'] ) ? '' : trim( $newblock['options'] );
				$edit_func = ! isset( $newblock['edit_func'] ) ? '' : trim( $newblock['edit_func'] );
				$newbid    = $dbm->insert( 'newblocks', " VALUES (0, {$mid}, " . $func_num . ", '" . addslashes( $options ) . "', '" . addslashes( $newblock['name'] ) . "', '" . addslashes( $newblock['name'] ) . "', '', 0, 0, " . $visible . ", 'S', 'H', 1, '{$module}', '" . addslashes( $newblock['file'] ) . "', '" . addslashes( $newblock['show_func'] ) . "', '" . addslashes( $edit_func ) . "', '" . addslashes( $newblock['template'] ) . "', 0, " . $time . ')'
				);
				//$newbid = $xoopsDB->getInsertId();
				$newtplid = $dbm->insert( 'tplfile', ' VALUES (0, ' . $newbid . ", '{$module}', 'default', '" . addslashes( $newblock['template'] ) . "', '" . addslashes( $newblock['description'] ) . "', " . $time . ', ' . $time . ", 'block')" );
				//$newtplid = $xoopsDB->getInsertId();
				if ( filesize( "../modules/{$module}/templates/blocks/" . $newblock['template'] ) > 0 ) {
					$tplsource = fread( $fp, filesize( "../modules/{$module}/templates/blocks/" . $newblock['template'] ) );
				} else {
					$tplsource = '';
				}
				fclose( $fp );
				$dbm->insert( 'tplsource', ' (tpl_id, tpl_source) VALUES (' . $newtplid . ", '" . addslashes( $tplsource ) . "')" );

				$dbm->insert( 'group_permission', ' VALUES (0, ' . $groups['XOOPS_GROUP_ADMIN'] . ', ' . $newbid . ", 1, 'block_read')" );
				//$dbm->insert("group_permission", " VALUES (0, ".$gruops['XOOPS_GROUP_ADMIN'].", ".$newbid.", 'xoops_blockadmiin')");
				$dbm->insert( 'group_permission', ' VALUES (0, ' . $groups['XOOPS_GROUP_USERS'] . ', ' . $newbid . ", 1, 'block_read')" );
				$dbm->insert( 'group_permission', ' VALUES (0, ' . $groups['XOOPS_GROUP_ANONYMOUS'] . ', ' . $newbid . ", 1, 'block_read')" );
			}
		}
	}

	//
	// Install preferences
	//
	if ( isset( $modversion['config'] ) ) {
		$count = 0;
		foreach ( $modversion['config'] as $configInfo ) {
			//
			// TODO We have to sanitize.
			//
			$name      = $configInfo['name'];
			$title     = $configInfo['title'];
			$desc      = $configInfo['description'];
			$formtype  = $configInfo['formtype'];
			$valuetype = $configInfo['valuetype'];
			$default   = $configInfo['default'];

			if ( 'array' == $valuetype ) {
				$default = serialize( explode( '|', trim( $default ) ) );
			}

			$conf_id = $dbm->insert( 'config', " VALUES (0, {$mid}, 0, '{$name}', '{$title}', '{$default}', '{$desc}', '{$formtype}', '{$valuetype}', {$count})" );

			if ( isset( $configInfo['options'] ) && is_array( $configInfo['options'] ) ) {
				foreach ( $configInfo['options'] as $key => $value ) {
					$dbm->insert( 'configoption', " VALUES (0, '{$key}', '{$value}', {$conf_id})" );
				}
			}

			$count ++;
		}
	}
}
