<?php
/*
 * Created on 2011/11/17 by nao-pon http://xoops.hypweb.net/
 * $Id: xpwiki_render.php,v 1.2 2011/11/18 04:49:14 nao-pon Exp $
 */

if (defined('XOOPS_CUBE_LEGACY')) {
	if (is_file(XOOPS_ROOT_PATH . '/preload/SetupHyp_TextFilter.class.php')) {
		$config['error'][] = hypconf_constant( $constpref.'_TEXTFILTER_ALREADY_EXISTS' );
	}
	if (!defined('LEGACY_BASE_VERSION') || version_compare(LEGACY_BASE_VERSION, '2.2.1.0', '<')) {
		$config['error'][] = hypconf_constant( $constpref.'_XCL_REQUERE_2_2_1' );
	}
	$config[] = array(
		'name' => 'xpwiki_render_dirname',
		'title' => $constpref.'_XPWIKI_RENDER_DIRNAME',
		'description' => $constpref.'_XPWIKI_RENDER_DIRNAME_DESC',
		'formtype' => 'select',
		'valuetype' => 'text',
		'options' => 'xpwikis'
		);

	$config[] = array(
		'name' => 'xpwiki_render_use_wikihelper',
		'title' => $constpref.'_XPWIKI_RENDER_USE_WIKIHELPER',
		'description' => $constpref.'_XPWIKI_RENDER_USE_WIKIHELPER_DESC',
		'formtype' => 'yesno',
		'valuetype' => 'int',
		'default' => 0,
		);

	$config[] = array(
		'name' => 'xpwiki_render_notuse_wikihelper_modules',
		'title' => $constpref.'_XPWIKI_RENDER_NOTUSE_WIKIHELPER_MODULES',
		'description' => $constpref.'_XPWIKI_RENDER_NOTUSE_WIKIHELPER_MODULES_DESC',
		'formtype' => 'check',
		'valuetype' => 'array',
		'options' => 'modules'
		);

} else {
	$config['error'][] = hypconf_constant( $constpref.'_ADMENU_XPWIKI_RENDER' ) . ': ' . hypconf_constant( $constpref.'_REQUERE_XCL' );
}