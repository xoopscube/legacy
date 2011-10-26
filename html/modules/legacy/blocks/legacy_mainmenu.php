<?php
/**
 *
 * @package XOOPS2
 * @version $Id: legacy_mainmenu.php,v 1.3 2008/09/25 15:12:12 kilica Exp $
 * @copyright Copyright (c) 2000 XOOPS.org  <http://www.xoops.org/>
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
//  This file has been modified for Legacy from XOOPS2 System module block   //
// ------------------------------------------------------------------------- //

function b_legacy_mainmenu_show() {
    $root =& XCube_Root::getSingleton();
    $xoopsModule =& $root->mContext->mXoopsModule;
    $xoopsUser =& $root->mController->mRoot->mContext->mXoopsUser;
    
    $block = array();
	$block['_display_'] = true;

    $module_handler =& xoops_gethandler('module');
    $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('isactive', 1));
    $criteria->add(new Criteria('weight', 0, '>'));
    $modules =& $module_handler->getObjects($criteria, true);
    $moduleperm_handler =& xoops_gethandler('groupperm');
    $groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
    $read_allowed = $moduleperm_handler->getItemIds('module_read', $groups);
    foreach (array_keys($modules) as $i) {
        if (in_array($i, $read_allowed)) {
            $block['modules'][$i]['name'] = $modules[$i]->getVar('name');
            $block['modules'][$i]['directory'] = $modules[$i]->getVar('dirname');
            $sublinks =& $modules[$i]->subLink();
            if ((count($sublinks) > 0) && (!empty($xoopsModule)) && ($i == $xoopsModule->getVar('mid'))) {
                foreach($sublinks as $sublink){
                    $block['modules'][$i]['sublinks'][] = array('name' => $sublink['name'], 'url' => XOOPS_URL.'/modules/'.$modules[$i]->getVar('dirname').'/'.$sublink['url']);
                }
            } else {
                $block['modules'][$i]['sublinks'] = array();
            }
        }
    }
    return $block;
}
?>
