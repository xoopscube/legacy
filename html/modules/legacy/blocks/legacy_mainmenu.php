<?php
/**
 * Module Legacy Block legacy_mainmenu.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 * @brief      This file has been modified for Legacy from XOOPS2 System module block
 */

/**
 *
 * @param $options
 * @return array
 */
function b_legacy_mainmenu_show($options)
{
    $root =& XCube_Root::getSingleton();
    $xoopsModule =& $root->mContext->mXoopsModule;
    $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;

    $block = [];
    $block['_display_'] = true;
    $block['icon'] = isset($options[1])?$options[1]:'';
    $module_handler =& xoops_gethandler('module');
    $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('isactive', 1));
    $criteria->add(new Criteria('weight', 0, '>'));
    $modules =& $module_handler->getObjects($criteria, true);
    $moduleperm_handler =& xoops_gethandler('groupperm');
    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $read_allowed = $moduleperm_handler->getItemIds('module_read', $groups);
    $all_links = (int)$options[0];
    $mid = is_object($xoopsModule)?$xoopsModule->getVar('mid', 'N'):'';
    foreach (array_keys($modules) as $i) {
        if (in_array($i, $read_allowed)) {
            $module = &$modules[$i];
            $blockm = &$block['modules'][$i];
            $blockm['name'] = $module->getVar('name');
            if (!empty($options[1])) {
                $blockm['icon'] = $module->getInfo('icon'); // TODO @gigamaster XCL v2.3.x Module icon option
            }
            $moddir = XOOPS_URL.'/modules/';
            $moddir .= $blockm['directory'] = $module->getVar('dirname', 'N');
            $info = $module->getInfo();
            $sublinks =& $module->subLink();
            if (count($sublinks)>0 && ($all_links || $i==$mid)) {
                foreach ($sublinks as $sublink) {
                    $blockm['sublinks'][] = ['name' => $sublink['name'], 'url' => $moddir . '/' . $sublink['url']];
                }
            } else {
                $blockm['sublinks'] = [];
            }
        }
    }
    return $block;
}

function b_legacy_mainmenu_edit($options)
{
    $off='checked="checked"';
    $on='';
    if ($options[0]) {
        $on = $off;
        $off = '';
    }
    $icon_off = 'checked="checked"';
    $icon_on = '';
    if ($options[1]) {
        $icon_on = $icon_off;
        $icon_off = '';
    }
    return '<div>' . _MB_LEGACY_MAINMENU_EXPAND_SUB .
           "<input type=\"radio\" name=\"options[0]\" value=\"0\" $off>" . _NO .
           " &nbsp; <input type=\"radio\" name=\"options[0]\" value=\"1\" $on>" . _YES .
           "<br>Show icon
           <input type=\"radio\" name=\"options[1]\" value=\"0\" $icon_off>" . _NO .
           " &nbsp; <input type=\"radio\" name=\"options[1]\" value=\"1\" $icon_on>" . _YES . '
</div>';
}
