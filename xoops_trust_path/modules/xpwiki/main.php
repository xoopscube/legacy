<?php
// Forbid prefetch
if (
	(isset($_SERVER['HTTP_X_MOZ']) && $_SERVER['HTTP_X_MOZ'] === 'prefetch')
	||
	(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === 'Fasterfox')
) {
	header ( 'HTTP/1.1 403 Forbidden' );
	exit();
}

$mytrustdirname = basename( dirname( __FILE__ ) ) ;
$mytrustdirpath = dirname( __FILE__ ) ;

include_once "$mytrustdirpath/include.php";

$xpwiki = new XpWiki($mydirname);

// initialize
$xpwiki->init();

// XCL >= 2.2 Use "Legacy_Utils::formatPagetitle"
if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.2', '>=')) {
	$xpwiki->root->html_head_title = trim(str_replace('$module_title', '', $xpwiki->root->html_head_title), ' -');
}

// execute
$xpwiki->execute();

// gethtml
$xpwiki->catbody();

// Add error message
if ($xpwiki->root->userinfo['admin']) {
	$hyp_common_methods = get_class_methods('HypCommonFunc');
	if (is_null($hyp_common_methods) || ! in_array('get_version', $hyp_common_methods) || HypCommonFunc::get_version() < 20100725) {
		$xpwiki->admin_messages[] = '[Warning] Please install or update <a href="http://cvs.sourceforge.jp/cgi-bin/viewcvs.cgi/hypweb/XOOPS_TRUST/class/hyp_common.tar.gz?view=tar" title="Download">a newest HypCommonFunc</a> into "XOOPS_TRUST_PATH/class/".';
	}
	if ($xpwiki->admin_messages) {
		$xpwiki->html = '<p style="color:red;font-weight:bold;">' . join('<br />', $xpwiki->admin_messages).'</p><hr />'.$xpwiki->html;
	}
}

if ($xpwiki->runmode === 'xoops') {

	// For XCL >= 2.2.1.1 (clear cache of modinfo)
	// Is it XCL's bug? need check next
	// http://xoopscube.svn.sourceforge.net/viewvc/xoopscube/Package_Legacy/trunk/html/kernel/module.php?view=log
	if (defined('LEGACY_BASE_VERSION') && version_compare(LEGACY_BASE_VERSION, '2.2.1.1', '>=')) {
		$module_handler =& xoops_gethandler('module');
		$thisModule =& $module_handler->getByDirname($xpwiki->root->mydirname);
		$thisModule->modinfo = null;
	}

	// xoops header
	include XOOPS_ROOT_PATH.'/header.php';

	$_xoops_header = $xoopsTpl->get_template_vars('xoops_module_header');
	$xpwiki_head = array();
	foreach(explode("\n", $_xoops_header) as $_head) {
		$_head = trim($_head);
		if ($_head && (strpos($xpwiki->root->html_header, $_head) === FALSE || ! preg_match('#^(?:<script[^>]*?>.*?</script>|<link[^>]+?/>)$#i', $_head))) {
			$xpwiki_head[] = $_head;
		}
	}
	$xpwiki->root->html_header .= join("\n", $xpwiki_head);

	$xoopsTpl->assign(
		array(
			'xoops_pagetitle' => $xpwiki->root->pagetitle,
			'xoops_module_header' => $xpwiki->root->html_header,
			'xoops_breadcrumbs' => $xpwiki->get_var('breadcrumbs_array'),
			'xoops_meta_description' => $xpwiki->root->meta_description,
			'xpwiki_pagename' => $xpwiki->get_var('page'),
 			'xpwiki_pginfo' => $xpwiki->get_pginfo(),
		)
	);

	if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.2', '>=')) {
		// For XCL >= 2.2
		$xclRoot =& XCube_Root::getSingleton();
		$xclRoot->mContext->setAttribute('legacy_pagetitle', Legacy_Utils::formatPagetitle($xpwiki->root->module_title, $xpwiki->root->pagetitle, $xpwiki->root->pagetitle_action));
		$headerScript = $xclRoot->mContext->getAttribute('headerScript');
		$headerScript->addMeta('description', $xpwiki->root->meta_description);
	} elseif (isset($xoTheme) && is_object($xoTheme)) {
		// For XOOPS 2.3 or higher & Impress CMS.
		$xoTheme->addMeta('meta', 'description', $xpwiki->root->meta_description);
	}

	echo $xpwiki->html;

	// xoops footer
	include XOOPS_ROOT_PATH.'/footer.php';

} else if ($xpwiki->runmode === 'xoops_admin') {

	// Check referer
	if (! $xpwiki->func->refcheck()) {
		exit('Invalid REFERER.');
	}

	// environment
	require_once XOOPS_ROOT_PATH.'/class/template.php' ;
	$module_handler =& xoops_gethandler( 'module' ) ;
	$xoopsModule =& $module_handler->getByDirname( $xpwiki->root->mydirname ) ;
	$config_handler =& xoops_gethandler( 'config' ) ;
	$xoopsModuleConfig =& $config_handler->getConfigsByCat( 0 , $xoopsModule->getVar( 'mid' ) ) ;

	// check permission of 'module_admin' of this module
	$moduleperm_handler =& xoops_gethandler( 'groupperm' ) ;
	if( ! is_object( @$xoopsUser ) || ! $moduleperm_handler->checkRight( 'module_admin' , $xoopsModule->getVar( 'mid' ) , $xoopsUser->getGroups() ) ) die( 'only admin can access this area' ) ;

	$xoopsOption['pagetype'] = 'admin' ;
	require XOOPS_ROOT_PATH.'/include/cp_functions.php' ;

	// language files
	$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
	if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
	require_once( $langmanpath ) ;
	$langman =& D3LanguageManager::getInstance() ;
	$langman->read( 'admin.php' , $mydirname , $mytrustdirname , false ) ;

	// xoops admin header
	xoops_cp_header() ;

	// mymenu
	//$mymenu_fake_uri = '' ;
	include dirname(__FILE__).'/admin/mymenu.php' ;

	// Decide charset for CSS
	$css_charset = 'iso-8859-1';
	switch($xpwiki->cont['UI_LANG']){
		case 'ja': $css_charset = 'Shift_JIS'; break;
	}
	$dirname = $xpwiki->root->mydirname;
	// Head Tags
	list($head_pre_tag, $head_tag) = $xpwiki->func->get_additional_headtags();
	$cssprefix = $xpwiki->root->css_prefix ? 'pre=' . rawurlencode($xpwiki->root->css_prefix) . '&amp;' : '';

	echo <<<EOD
$head_pre_tag
<link rel="stylesheet" type="text/css" media="screen" href="{$xpwiki->cont['LOADER_URL']}?skin={$xpwiki->cont['SKIN_NAME']}&amp;pw={$xpwiki->root->pre_width}&amp;{$cssprefix}charset={$css_charset}&amp;src={$xpwiki->root->main_css}" charset="{$css_charset}" />
$head_tag
EOD;

	echo $xpwiki->html;

	// xoops admin footer
	xoops_cp_footer() ;

} else if ($xpwiki->runmode === 'standalone') {

	echo $xpwiki->html;

}

exit();