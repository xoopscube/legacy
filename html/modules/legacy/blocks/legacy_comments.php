<?php
/**
 *
 * @package XOOPS2
 * @version $Id: legacy_comments.php,v 1.3 2008/09/25 15:12:13 kilica Exp $
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

function b_legacy_comments_show($options) {
    $block = array();
    include_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
    $comment_handler =& xoops_gethandler('comment');
    $criteria = new CriteriaCompo(new Criteria('com_status', XOOPS_COMMENT_ACTIVE));
    $criteria->setLimit(intval($options[0]));
    $criteria->setSort('com_created');
    $criteria->setOrder('DESC');
    $comments =& $comment_handler->getObjects($criteria, true);
    $member_handler =& xoops_gethandler('member');
    $module_handler =& xoops_gethandler('module');
    $modules =& $module_handler->getObjects(new Criteria('hascomments', 1), true);
    $comment_config = array();
    foreach (array_keys($comments) as $i) {
        $mid = $comments[$i]->getVar('com_modid');
        $com['module'] = '<a href="'.XOOPS_URL.'/modules/'.$modules[$mid]->getVar('dirname').'/">'.$modules[$mid]->getVar('name').'</a>';
        if (!isset($comment_config[$mid])) {
            $comment_config[$mid] = $modules[$mid]->getInfo('comments');
        }
        $com['id'] = $i;
        $com['title'] = '<a href="'.XOOPS_URL.'/modules/'.$modules[$mid]->getVar('dirname').'/'.$comment_config[$mid]['pageName'].'?'.$comment_config[$mid]['itemName'].'='.$comments[$i]->getVar('com_itemid').'&amp;com_id='.$i.'&amp;com_rootid='.$comments[$i]->getVar('com_rootid').'&amp;'.htmlspecialchars($comments[$i]->getVar('com_exparams')).'#comment'.$i.'">'.$comments[$i]->getVar('com_title').'</a>';
        $com['icon'] = $comments[$i]->getVar('com_icon');
        $com['icon'] = ($com['icon'] != '') ? $com['icon'] : 'icon1.gif';
        $com['time'] = $comments[$i]->getVar('com_created');
        if ($comments[$i]->getVar('com_uid') > 0) {
            $poster =& $member_handler->getUser($comments[$i]->getVar('com_uid'));
            if (is_object($poster)) {
                $com['poster'] = '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$comments[$i]->getVar('com_uid').'">'.$poster->getVar('uname').'</a>';
            } else {
                $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
            }
        } else {
            $com['poster'] = $GLOBALS['xoopsConfig']['anonymous'];
        }
        $block['comments'][] =& $com;
        unset($com);
    }
    return $block;
}

function b_legacy_comments_edit($options) {
    $inputtag = "<input type='text' name='options[]' value='".intval($options[0])."' />";
    $form = sprintf(_MB_LEGACY_DISPLAYC, $inputtag);
    return $form;
}
?>
