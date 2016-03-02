<?php
/**
 *
 * @package XOOPS2
 * @version $Id: legacy_siteinfo.php,v 1.3 2008/09/25 15:12:15 kilica Exp $
 * @copyright Copyright (c) 2000 XOOPS.org  <http://www.xoops.org/>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
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

function b_legacy_siteinfo_show($options)
{
    global $xoopsConfig, $xoopsUser;
    $xoopsDB =& Database::getInstance();
    $myts =& MyTextSanitizer::sGetInstance();
    $block = array();
    if (!empty($options[3])) {
        $block['showgroups'] = true;
        $result = $xoopsDB->query("SELECT u.uid, u.uname, u.email, u.user_viewemail, u.user_avatar, g.name AS groupname FROM ".$xoopsDB->prefix("groups_users_link")." l LEFT JOIN ".$xoopsDB->prefix("users")." u ON l.uid=u.uid LEFT JOIN ".$xoopsDB->prefix("groups")." g ON l.groupid=g.groupid WHERE g.group_type='Admin' ORDER BY l.groupid, u.uid");
        if ($xoopsDB->getRowsNum($result) > 0) {
            $prev_caption = "";
            $i = 0;
            while ($userinfo = $xoopsDB->fetchArray($result)) {
                if ($prev_caption != $userinfo['groupname']) {
                    $prev_caption = $userinfo['groupname'];
                    $block['groups'][$i]['name'] = $myts->htmlSpecialChars($userinfo['groupname']);
                }
                if (is_object($xoopsUser)) {
                    $block['groups'][$i]['users'][] = array('id' => $userinfo['uid'], 'name' => $myts->htmlspecialchars($userinfo['uname']), 'msglink' => "<a href=\"".XOOPS_URL."/modules/message/index.php?action=new&to_userid=".$userinfo['uid']."\"><img src=\"".XOOPS_URL."/images/icons/pm_small.gif\" border=\"0\" alt=\"\" /></a>", 'avatar' => XOOPS_UPLOAD_URL.'/'.$userinfo['user_avatar']);
                } else {
                    if ($userinfo['user_viewemail']) {
                        $block['groups'][$i]['users'][] = array('id' => $userinfo['uid'], 'name' => $myts->htmlspecialchars($userinfo['uname']), 'msglink' => '<a href="mailto:'.$userinfo['email'].'"><img src="'.XOOPS_URL.'/images/icons/em_small.gif" border="0" width="16" height="14" alt="" /></a>', 'avatar' => XOOPS_UPLOAD_URL.'/'.$userinfo['user_avatar']);
                    } else {
                        $block['groups'][$i]['users'][] = array('id' => $userinfo['uid'], 'name' => $myts->htmlspecialchars($userinfo['uname']), 'msglink' => '&nbsp;', 'avatar' => XOOPS_UPLOAD_URL.'/'.$userinfo['user_avatar']);
                    }
                }
                $i++;
            }
        }
    } else {
        $block['showgroups'] = false;
    }
    $block['logourl'] = XOOPS_URL.'/images/'.$options[2];
    if (is_object($xoopsUser)) {
        $block['recoomendtime'] = time();
    } else {
        $block['recoomendtime'] = 0;
    }
    $block['popup_width'] = $options[0];
    $block['popup_height'] = $options[1];
    return $block;
}

function b_legacy_siteinfo_edit($options)
{
    $form = _MB_LEGACY_PWWIDTH."&nbsp;";
    $form .= "<input type='text' name='options[]' value='".$options[0]."' />";
    $form .= "<br />"._MB_LEGACY_PWHEIGHT."&nbsp;";
    $form .= "<input type='text' name='options[]' value='".$options[1]."' />";
    $form .= "<br />".sprintf(_MB_LEGACY_LOGO, XOOPS_URL."/images/")."&nbsp;";
    $form .= "<input type='text' name='options[]' value='".$options[2]."' />";
    $chk = "";
    $form .= "<br />"._MB_LEGACY_SADMIN."&nbsp;";
    if ($options[3] == 1) {
        $chk = " checked='checked'";
    }
    $form .= "<input type='radio' name='options[3]' value='1'".$chk." />&nbsp;"._YES."";
    $chk = "";
    if ($options[3] == 0) {
        $chk = " checked=\"checked\"";
    }
    $form .= "&nbsp;<input type='radio' name='options[3]' value='0'".$chk." />"._NO."";
    return $form;
}
