<?php
/**
 * Altsys library (UI-Components) for D3 modules
 *
 * @package    Altsys
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

function altsys_admin_in_theme( $s ) {
	global $xoops_admin_contents;

	$xoops_admin_contents = '';

	if ( defined( 'ALTSYS_DONT_USE_ADMIN_IN_THEME' ) ) {
		return $s;
	}

	// check whether cp_functions.php is loaded
	if ( ! defined( 'XOOPS_CPFUNC_LOADED' ) ) {
		define( 'ALTSYS_DONT_USE_ADMIN_IN_THEME', 1 );

		return $s;
	}

	//!Fix redirect
	//strpos - Find the position of the first occurrence of a substring in a string
	//mb_strstr - Finds first occurrence of a string within another
	if (strpos($s, '<meta http-equiv="Refresh" ') !== false) {
	//if ( mb_strstr( $s, '<meta http-equiv="Refresh" ' ) ) {
		define( 'ALTSYS_DONT_USE_ADMIN_IN_THEME', 1 );

		return $s;
	}

	// outputs before cp_header()
	@[$former_outputs, $tmp_s] = explode( '<!DOCTYPE', $s, 2 );
	if ( empty( $tmp_s ) ) {
		$tmp_s = $s;
	}

	@[, $tmp_s] = explode( "<div class='content'>", $tmp_s, 2 );
	if ( empty( $tmp_s ) ) {
		define( 'ALTSYS_DONT_USE_ADMIN_IN_THEME', 1 );

		return $s;
	}

	[ $tmp_s, $tmp_after ] = explode( "<div width='1%'>", $tmp_s );
	if ( empty( $tmp_after ) ) {
		define( 'ALTSYS_DONT_USE_ADMIN_IN_THEME', 1 );

		return $s;
	}

	$xoops_admin_contents = $former_outputs . substr( strrev( strstr( strrev( $tmp_s ), strrev( '</div>' ) ) ), 0, - 6 );

	return '';
}


function altsys_admin_in_theme_in_last( $contents = null ) {
	$xoopsTpl = null;
 global $xoops_admin_contents, $xoopsConfig, $xoopsModule, $xoopsUser, $xoopsUserIsAdmin, $xoopsLogger, $altsysModuleConfig, $altsysModuleId;

	if ( ! isset( $contents ) ) {
		while ( ob_get_level() ) {
			ob_end_flush();
		}
	} else {
		$xoops_admin_contents = $contents;
	}

	if ( ! isset( $xoops_admin_contents ) ) {
		return;
	}
	if ( defined( 'ALTSYS_DONT_USE_ADMIN_IN_THEME' ) ) {
		return;
	}

	if ( ! is_object( $xoopsUser ) ) {
		exit;
	}

	// language files
	if ( is_file( dirname( __DIR__ ) . '/language/' . $xoopsConfig['language'] . '/admin_in_theme.php' ) ) {
		include_once dirname( __DIR__ ) . '/language/' . $xoopsConfig['language'] . '/admin_in_theme.php';
	} else {
		include_once dirname( __DIR__ ) . '/language/english/admin_in_theme.php';
	}

	// set the theme
	$xoopsConfig['theme_set'] = $altsysModuleConfig['admin_in_theme'];

	// language files under the theme
	$original_error_level = error_reporting();

	error_reporting( $original_error_level & ~E_NOTICE );

	if ( is_file( XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/language/' . $xoopsConfig['language'] . '.php' ) ) {
		include_once XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/language/' . $xoopsConfig['language'] . '.php';
	} elseif ( is_file( XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/language/english.php' ) ) {
		include_once XOOPS_THEME_PATH . '/' . $xoopsConfig['theme_set'] . '/language/english.php';
	}
	error_reporting( $original_error_level );

	include __DIR__ . '/admin_in_theme_header.inc.php';

	$xoops_module_header = '';
	if ( ALTSYS_CORE_TYPE_XCL21 == altsys_get_core_type() ) {
		$xoops_module_header .= '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/modules/legacyRender/admin/css.php?file=style.css">' . "\n";
		if ( is_object( @$xoopsModule ) ) {
			$xoops_module_header .= '<link rel="stylesheet" type="text/css" media="all" href="' . XOOPS_URL . '/modules/legacyRender/admin/css.php?file=module.css&amp;dirname=' . $xoopsModule->getVar( 'dirname' ) . '">' . "\n";
		}
	}

	// assignment
	$xoopsTpl->assign(
		[
			'xoops_theme'           => $xoopsConfig['theme_set'],
			'xoops_imageurl'        => XOOPS_THEME_URL . '/' . $xoopsConfig['theme_set'] . '/',
			'xoops_themecss'        => xoops_getcss( $xoopsConfig['theme_set'] ),
			'xoops_requesturi'      => htmlspecialchars( $GLOBALS['xoopsRequestUri'], ENT_QUOTES ),
			'xoops_sitename'        => htmlspecialchars( $xoopsConfig['sitename'], ENT_QUOTES ),
			'xoops_showlblock'      => 1,
			'xoops_js'              => '</script><script type="text/javascript" src="' . XOOPS_URL . '/common/js/x-utils.js"></script><script type="text/javascript">' . "\n",
			'xoops_runs_admin_side' => 1,
			'xoops_breadcrumbs'     => $xoops_breadcrumbs,
			'xoops_slogan'          => htmlspecialchars( $xoopsConfig['slogan'], ENT_QUOTES ),
			'xoops_contents'        => $xoops_admin_contents,
			//. '<div id="adminmenu_layers">' . $xoops_admin_menu_dv . '</div>',
			'xoops_module_header'   => $xoops_module_header,
		]
	);

	// rendering
	$xoopsTpl->display( $xoopsConfig['theme_set'] . '/theme.html' );

}
