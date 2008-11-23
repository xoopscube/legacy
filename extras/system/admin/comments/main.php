<?php
// $Id: main.php,v 1.1 2007/05/15 02:34:52 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //


if ( !is_object($xoopsUser) || !is_object($xoopsModule) || !$xoopsUser->isAdmin($xoopsModule->getVar('mid')) ) {
    exit("Access Denied");
} else {
    $op = 'list';
    if (isset($_GET['op'])) {
        $op = trim($_GET['op']);
    }
    switch ($op) {
    case 'list':
        include_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
        include_once XOOPS_ROOT_PATH.'/language/'.$xoopsConfig['language'].'/comment.php';
        $limit_array = array(10, 20, 50, 100);
        $status_array = array(XOOPS_COMMENT_PENDING => _CM_PENDING, XOOPS_COMMENT_ACTIVE => _CM_ACTIVE, XOOPS_COMMENT_HIDDEN => _CM_HIDDEN);
        $status_array2 = array(XOOPS_COMMENT_PENDING => '<span style="text-decoration: none; font-weight: bold; color: #00ff00;">'._CM_PENDING.'</span>', XOOPS_COMMENT_ACTIVE => '<span style="text-decoration: none; font-weight: bold; color: #ff0000;">'._CM_ACTIVE.'</span>', XOOPS_COMMENT_HIDDEN => '<span style="text-decoration: none; font-weight: bold; color: #0000ff;">'._CM_HIDDEN.'</span>');
        $status = (!isset($_GET['status']) || !in_array(intval($_GET['status']), array_keys($status_array))) ? 0 : intval($_GET['status']);
        $module = !isset($_GET['module']) ? 0 : intval($_GET['module']);
        $module_handler =& xoops_gethandler('module');
        $module_array =& $module_handler->getList(new Criteria('hascomments', 1));
        $comment_handler =& xoops_gethandler('comment');
        $criteria = new CriteriaCompo();
        if ($status > 0) {
            $criteria->add(new Criteria('com_status', $status));
        }
        if ($module > 0) {
            $criteria->add(new Criteria('com_modid', $module));
        }
        $total = $comment_handler->getCount($criteria);
        if ($total > 0) {
            $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
            $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
            if (!in_array($limit, $limit_array)) {
                $limit = 50;
            }
            $sort = (!isset($_GET['sort']) || !in_array($_GET['sort'], array('com_modid', 'com_status', 'com_created', 'com_uid', 'com_ip', 'com_title'))) ? 'com_id' : $_GET['sort'];
            if (!isset($_GET['order']) || $_GET['order'] != 'ASC') {
                $order = 'DESC';
                $otherorder = 'ASC';
            } else {
                $order = 'ASC';
                $otherorder = 'DESC';
            }
            $criteria->setSort($sort);
            $criteria->setOrder($order);
            $criteria->setLimit($limit);
            $criteria->setStart($start);
            $comments =& $comment_handler->getObjects($criteria, true);
        } else {
            $start = 0;
            $limit = 0;
            $otherorder = 'DESC';
            $comments = array();
        }
        $form = '<form action="admin.php" method="get">';
        $form .= '<select name="module">';
        $module_array[0] = _MD_AM_ALLMODS;
        foreach ($module_array as $k => $v) {
            $sel = '';
            if ($k == $module) {
                $sel = ' selected="selected"';
            }
            $form .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
        }
        $form .= '</select>&nbsp;<select name="status">';
        $status_array[0] = _MD_AM_ALLSTATUS;
        foreach ($status_array as $k => $v) {
            $sel = '';
            if (isset($status) && $k == $status) {
                $sel = ' selected="selected"';
            }
            $form .= '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';
        }
        $form .= '</select>&nbsp;<select name="limit">';
        foreach ($limit_array as $k) {
            $sel = '';
            if (isset($limit) && $k == $limit) {
                $sel = ' selected="selected"';
            }
            $form .= '<option value="'.$k.'"'.$sel.'>'.$k.'</option>';
        }
        $form .= '</select>&nbsp;<input type="hidden" name="fct" value="comments" /><input type="submit" value="'._GO.'" name="selsubmit" /></form>';

        xoops_cp_header();
        echo '<h4 style="text-align:left">'._MD_AM_COMMMAN.'</h4>';
        echo $form;
        echo '<table width="100%" class="outer" cellspacing="1"><tr><th colspan="8">'._MD_AM_LISTCOMM.'</th></tr><tr align="center"><td class="head">&nbsp;</td><td class="head" align="left"><a href="admin.php?fct=comments&amp;op=list&amp;sort=com_title&amp;order='.$otherorder.'&amp;module='.$module.'&amp;status='.$status.'&amp;start='.$start.'&amp;limit='.$limit.'">'._CM_TITLE.'</a></td><td class="head"><a href="admin.php?fct=comments&amp;op=list&amp;sort=com_created&amp;order='.$otherorder.'&amp;module='.$module.'&amp;status='.$status.'&amp;start='.$start.'&amp;limit='.$limit.'">'._CM_POSTED.'</a></td><td class="head"><a href="admin.php?fct=comments&amp;op=list&amp;sort=com_uid&amp;order='.$otherorder.'&amp;module='.$module.'&amp;status='.$status.'&amp;start='.$start.'&amp;limit='.$limit.'">'._CM_POSTER.'</a></td><td class="head"><a href="admin.php?fct=comments&amp;op=list&amp;sort=com_ip&amp;order='.$otherorder.'&amp;module='.$module.'&amp;status='.$status.'&amp;start='.$start.'&amp;limit='.$limit.'">IP</a></td><td class="head"><a href="admin.php?fct=comments&amp;op=list&amp;sort=com_modid&amp;order='.$otherorder.'&amp;module='.$module.'&amp;status='.$status.'&amp;start='.$start.'&amp;limit='.$limit.'">'._MD_AM_MODULE.'</a></td><td class="head"><a href="admin.php?fct=comments&amp;op=list&amp;sort=com_status&amp;order='.$otherorder.'&amp;module='.$module.'&amp;status='.$status.'&amp;start='.$start.'&amp;limit='.$limit.'">'._CM_STATUS.'</a></td><td class="head">&nbsp;</td></tr>';
        $class = 'even';
        foreach (array_keys($comments) as $i) {
            $class = ($class == 'odd') ? 'even' : 'odd';
            $poster_uname = $xoopsConfig['anonymous'];
            if ($comments[$i]->getVar('com_uid') > 0) {
                $poster =& $member_handler->getUser($comments[$i]->getVar('com_uid'));
                if (is_object($poster)) {
                    $poster_uname = '<a href="'.XOOPS_URL.'/userinfo.php?uid='.$comments[$i]->getVar('com_uid').'">'.$poster->getVar('uname').'</a>';
                }
            }
            $icon = ($comments[$i]->getVar('com_icon') != '') ? '<img src="'.XOOPS_URL.'/images/subject/'.htmlspecialchars($comments[$i]->getVar('com_icon')).'" alt="" />' : '<img src="'.XOOPS_URL.'/images/icons/no_posticon.gif" alt="" />';
            echo '<tr align="center"><td class="'.$class.'">'.$icon.'</td><td class="'.$class.'" align="left"><a href="admin.php?fct=comments&amp;op=jump&amp;com_id='.$i.'">'. $comments[$i]->getVar('com_title').'</a></td><td class="'.$class.'">'.formatTimestamp($comments[$i]->getVar('com_created'), 'm').'</td><td class="'.$class.'">'.$poster_uname.'</td><td class="'.$class.'">'.$comments[$i]->getVar('com_ip').'</td><td class="'.$class.'">'.$module_array[$comments[$i]->getVar('com_modid')].'</td><td class="'.$class.'">'.$status_array2[$comments[$i]->getVar('com_status')].'</td><td class="'.$class.'" align="right"><a href="admin/comments/comment_edit.php?com_id='.$i.'">'._EDIT.'</a> <a href="admin/comments/comment_delete.php?com_id='.$i.'">'._DELETE.'</a></td></tr>';
        }
        echo '</table>';
        echo '<table style="width: 100%; border: 0; margin: 3px; padding: 3px;"><tr><td>'.sprintf(_MD_AM_COMFOUND, '<b>'.$total.'</b>');
        if ($total > $limit) {
            include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
            $nav = new XoopsPageNav($total, $limit, $start, 'start', 'fct=comments&amp;op=list&amp;limit='.$limit.'&amp;sort='.$sort.'&amp;order='.$order.'&amp;module='.$module);
            echo '</td><td align="right">'.$nav->renderNav();
        }
        echo '</td></tr></table>';
        xoops_cp_footer();
        break;

    case 'jump':
        $com_id = (isset($_GET['com_id'])) ? intval($_GET['com_id']) : 0;
        if ($com_id > 0) {
            $comment_handler =& xoops_gethandler('comment');
            $comment =& $comment_handler->get($com_id);
            if (is_object($comment)) {
                $module_handler =& xoops_gethandler('module');
                $module =& $module_handler->get($comment->getVar('com_modid'));
                $comment_config = $module->getInfo('comments');
                header('Location: '.XOOPS_URL.'/modules/'.$module->getVar('dirname').'/'.$comment_config['pageName'].'?'.$comment_config['itemName'].'='.$comment->getVar('com_itemid').'&com_id='.$comment->getVar('com_id').'&com_rootid='.$comment->getVar('com_rootid').'&com_mode=thread&'.$comment->getVar('com_exparams').'#comment'.$comment->getVar('com_id'));
                exit();
            }
        }
        redirect_header('admin.php?fct=comments', 1);
        break;

    default:
        break;
    }

}
?>