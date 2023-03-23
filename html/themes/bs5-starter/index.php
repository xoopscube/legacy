<?php
/*
 * Theme XCL Bootstrap 5 Starter
 *
 * @version   5.3.0
 * @author    Nuno Luciano ( https://github.com/gigamaster )
 * @copyright (c) 2023 The XOOPSCube Project, author
 * @license   MIT license for Bootstrap
 * @license   BSD license for XOOPSCube XCL Theme
 * @link      https://github.com/xoopscube
*/

// Process Script start getrusage — Gets the current resource usages
$rustart = getrusage();

$main = "../../mainfile.php";
if (file_exists(stream_resolve_include_path($main))) {
    require_once $main;
} else {
    throw new Exception("Failed to find and include required mainfile.");
}

require_once("../../header.php");

$root->mLanguageManager->loadPageTypeMessageCatalog('global');
$xoopsConfig = $root->mContext->mXoopsConfig;
$root->mController->execute();

////////////////////////////////////////////////////////
//global $xoopsConfig, $xoopsTheme, $xoopsRequestUri;
/* Render Blocks :
 * 0 hide
 * 1 display
 */
$GLOBALS['show_lblock']=1; // SIDE BLOCK LEFT
$GLOBALS['show_rblock']=0; // SIDE BLOCK RIGHT
$GLOBALS['show_cblock']=0;
// $GLOBALS['xoops_clblocks']=0; // CENTER BLOCK LEFT
// $GLOBALS['xoops_crblocks']=0; // CENTER BLOCK RIGHT
// $GLOBALS['xoops_ccblocks']=0; // CENTER BLOCK CENTER

//$theme_options = [];

$handler =& xoops_getmodulehandler('theme', 'legacy');
$theme =& $handler->get($root->mController->mRoot->mContext->getThemeName());

////////////////////////////////////////////////////////

$sys_info = [];
if (defined('XOOPS_DISTRIBUTION_VERSION')) {
    $sys_info[] = 'Distribution : ' .XOOPS_DISTRIBUTION_VERSION;
}
$sys_info[] = _VERSION. ' : ' .XOOPS_VERSION;
$sys_info[] = 'THEME : ' .$root->mContext->mXoopsConfig['theme_set'];
$sys_info[] = 'Template SET : ' .$root->mContext->mXoopsConfig['template_set'];
$sys_info[] = 'LANGUAGE : ' .$root->mContext->mXoopsConfig['language'];

    $debugmode = (int)($root->mContext->mXoopsConfig['debug_mode']);
    if ($debugmode == 0) {
        $sys_info[] = _DEBUG_MODE. ' : ' ._DEBUG_MODE_DESC;
    } elseif ($debugmode == 1) {
        $sys_info[] = _DEBUG_MODE. ' : ' ._DEBUG_MODE_PHP;
    } elseif ($debugmode == 2) {
        $sys_info[] = _DEBUG_MODE. ' : ' ._DEBUG_MODE_SQL;
    } elseif ($debugmode == 3) {
        $sys_info[] = _DEBUG_MODE. ' : ' ._DEBUG_MODE_SMARTY;
    }

    $sys_config = [];
    $sys_config['phpversion'] = phpversion();
        $db = &$root->mController->getDB();
        $result = $db->query('SELECT VERSION()');
        [$mysqlversion] = $db->fetchRow($result);
    $sys_config['mysqlversion'] = $mysqlversion;
    $sys_config['os'] = substr(php_uname(), 0, 7);
    $sys_config['server'] = xoops_getenv('SERVER_SOFTWARE');
    $sys_config['useragent'] = xoops_getenv('HTTP_USER_AGENT');

    $sys_info[] = _SYS_OS . ' : ' . $sys_config['os'];
    $sys_info[] = _SYS_SERVER . ' : ' . $sys_config['server'];
    $sys_info[] = _SYS_USERAGENT . ' : ' . $sys_config['useragent'];
    $sys_info[] = _SYS_PHPVERSION . ' : ' . $sys_config['phpversion'];
    $sys_info[] = _SYS_MYSQLVERSION . ' : ' . $sys_config['mysqlversion'];

$server_info = $sys_info;

$title = "Theme Style Guide";
$link = basename(dirname(__FILE__));
$style = XOOPS_THEME_PATH . "/$link/guide/ui-guide.html";
$guide = XOOPS_THEME_PATH . "/$link/guide/";

// Assign
$xoopsTpl->assign(
    [
        'theme'     => $theme->mVars,
        'platform'  => $server_info,
        'style'     => $style,
        'guide'     => $guide,
        'title'     => $title,
        'link'      => $link,
    ]
  );

// Render
$xoopsTpl->display(__DIR__.'/component/about.html');
require_once("../../footer.php");

// Process script end getrusage — Gets the current resource usages
// Unix systems and, Windows as well, can use getrusage in PHP 7+
// https://www.php.net/getrusage
function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
        -  ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
}
$ru = getrusage();
echo '<div class="toast-container position-fixed top-0 end-0 p-3"><div id="runTime" class="toast bg-black bg-gradient text-white border-0" role="status" aria-live="polite" aria-atomic="true" data-bs-delay="5000">'
.'<div class="d-flex"><div class="toast-body"><h6>Running Process Time</h6><span class="badge bg-dark-subtle text-info-emphasis">';
echo rutime($ru, $rustart, "utime") . " ms</span> computations<br><span class=\"badge  bg-dark-subtle text-info-emphasis\">" . rutime($ru, $rustart, "stime") . " ms</span> system calls";
echo '<progress value="0" max="100" class="progressTime" id="progressInvisible" style="position:relative;width:348px;height:4px;bottom:-14px;left:-12px"></progress></div></div></div></div>';
