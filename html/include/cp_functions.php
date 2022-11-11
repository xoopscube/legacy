<?php
/**
 * Xoops Control panel header
 * @package    XCL
 * @subpackage core
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.tx
 */

define('XOOPS_CPFUNC_LOADED', 1);

function xoops_cp_header()
{
    //
    // [Special Mission] Additional CHECK!!
    // Old modules may call this file from other admin directory.
    // In this case, the controller does not have Admin Module Object.
    //
    $root=&XCube_Root::getSingleton();
    require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/Legacy_AdminControllerStrategy.class.php';

    $strategy =new Legacy_AdminControllerStrategy($root->mController);

    $root->mController->setStrategy($strategy);
    $root->mController->setupModuleContext();
    $root->mController->_mStrategy->setupModuleLanguage();    //< Umm...

    require_once XOOPS_ROOT_PATH . '/header.php';
}

function xoops_cp_footer()
{
    require_once XOOPS_ROOT_PATH . '/footer.php';
}

// We need these because theme files will not be included
function OpenTable()
{
    echo "<table><tr><td>\n";
}

function CloseTable()
{
    echo '</td></tr></table>';
}

function themecenterposts($title, $content)
{
    echo '<table class="outer"><thead><tr><td class="head">'.$title.'</td></tr></thead><tbody><tr><td><br>'.$content.'<br></td></tr></tbody></table>';
}

function myTextForm($url, $value)
{
    return '<form action="'.$url.'" method="post"><input type="submit" value="'.$value.'"></form>';
}

function xoopsfwrite()
{
    if ('POST' != $_SERVER['REQUEST_METHOD']) {
        return false;
    }
    if (!xoops_refcheck()) {
        return false;
    }
    return true;
}

