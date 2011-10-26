<?php

// The next line is special fix for IE6.
session_cache_limiter('private_no_expire');

define ('_LEGACY_ALLOW_ACCESS_FROM_ANY_ADMINS_', true);

require_once "../../../mainfile.php";
$root =& XCube_Root::getSingleton();
unset($root->mContext->mXoopsModule);

//
// @todo Why does this file know Legacy_RenderSystem?
//

function Legacy_modifier_css_theme($string)
{
	$infoArr = Legacy_get_override_file($string, null, true);
	
	if (!empty($infoArr['theme']) && !empty($infoArr['dirname'])) {
		return XOOPS_THEME_URL . "/" . $infoArr['theme'] . "/modules/" . $infoArr['dirname'] . "/" . $string;
	}
	elseif (!empty($infoArr['theme'])) {
		return XOOPS_THEME_URL . "/" . $infoArr['theme'] . "/" . $string;
	}
	elseif (!empty($infoArr['dirname'])) {
		return XOOPS_MODULE_URL . "/" . $infoArr['dirname'] . "/admin/templates/" . $string;
	}
	
	return LEGACY_ADMIN_RENDER_FALLBACK_URL . "/" . $string;
}

$theme = isset($_GET['theme']) ? trim($_GET['theme']) : null;
$dirname = isset($_GET['dirname']) ? trim($_GET['dirname']) : null;
$_GET['file'] = isset($_GET['file']) ? $_GET['file'] : 'style.css';
$file = "stylesheets/" . trim(@$_GET['file']);

if (strstr($theme, "..") !== false || strstr($dirname, "..") !== false || strstr($file, "..") !== false) {
	exit();
}
require_once XOOPS_ROOT_PATH.'/modules/legacyRender/kernel/Legacy_AdminRenderSystem.class.php';

$smarty =new Legacy_AdminSmarty();
$smarty->register_modifier("theme", "Legacy_modifier_css_theme");
$smarty->register_function("stylesheet", "Legacy_function_stylesheet");

//
// TODO Emergency WORK AROUND for compile cache problem.
//
$smarty->force_compile = true;

if ($theme != null && $dirname != null) {
	$path = XOOPS_THEME_PATH . "/${theme}/modules/${dirname}";
}
elseif ($theme != null) {
	$path = XOOPS_THEME_PATH . "/" .$theme;
}
elseif ($dirname != null) {
	$path = XOOPS_MODULE_PATH . "/${dirname}/admin/templates";
}
else {
	$path = LEGACY_ADMIN_RENDER_FALLBACK_PATH;
}

$smarty->template_dir = $path;
$smarty->setModulePrefix('_css_' . $theme);

$result = "";
if (is_file($path . "/" . $file))
	$result = $smarty->fetch("file:" . $file);

header('Content-Type:text/css; charset='._CHARSET);
echo $result;

?>
