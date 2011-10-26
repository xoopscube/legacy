<?php
// $Id: userform.php,v 1.1 2007/05/15 02:34:54 minahito Exp $
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

$uid_label = new XoopsFormLabel(_AM_USERID, $uid_value);
$uname_text = new XoopsFormText(_AM_NICKNAME, "username", 25, 25, $uname_value);
$name_text = new XoopsFormText(_AM_NAME, "name", 30, 60, $name_value);
$email_tray = new XoopsFormElementTray(_AM_EMAIL, "<br />");
$email_text = new XoopsFormText("", "email", 30, 60, $email_value);
$email_tray->addElement($email_text, true);
$email_cbox = new XoopsFormCheckBox("", "user_viewemail", $email_cbox_value);
$email_cbox->addOption(1, _AM_AOUTVTEAD);
$email_tray->addElement($email_cbox);
$url_text = new XoopsFormText(_AM_URL, "url", 30, 100, $url_value);
//  $avatar_select = new XoopsFormSelect("", "user_avatar", $avatar_value);
//  $avatar_array = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH."/images/avatar/");
//  $avatar_select->addOptionArray($avatar_array);
//  $a_dirlist = XoopsLists::getDirListAsArray(XOOPS_ROOT_PATH."/images/avatar/");
//  $a_dir_labels = array();
//  $a_count = 0;
//  $a_dir_link = "<a href=\"javascript:openWithSelfMain('".XOOPS_URL."/misc.php?action=showpopups&amp;type=avatars&amp;start=".$a_count."','avatars',600,400);\">XOOPS</a>";
//  $a_count = $a_count + count($avatar_array);
//  $a_dir_labels[] = new XoopsFormLabel("", $a_dir_link);
//  foreach ($a_dirlist as $a_dir) {
//      if ( $a_dir == "users" ) {
//          continue;
//      }
//      $avatars_array = XoopsLists::getImgListAsArray(XOOPS_ROOT_PATH."/images/avatar/".$a_dir."/", $a_dir."/");
//      $avatar_select->addOptionArray($avatars_array);
//      $a_dir_link = "<a href=\"javascript:openWithSelfMain('".XOOPS_URL."/misc.php?action=showpopups&amp;type=avatars&amp;subdir=".$a_dir."&amp;start=".$a_count."','avatars',600,400);\">".$a_dir."</a>";
//      $a_dir_labels[] = new XoopsFormLabel("", $a_dir_link);
//      $a_count = $a_count + count($avatars_array);
//  }
//  if (!empty($uid_value)) {
//      $myavatar = avatarExists($uid_value);
//      if ( $myavatar != false ) {
//          $avatar_select->addOption($myavatar, _US_MYAVATAR);
//      }
//  }
//  $avatar_select->setExtra("onchange='showImgSelected(\"avatar\", \"user_avatar\", \"images/avatar\", \"\", \"".XOOPS_URL."\")'");
//  $avatar_label = new XoopsFormLabel("", "<img src='".XOOPS_URL."/images/avatar/".$avatar_value."' name='avatar' id='avatar' alt='' />");
//  $avatar_tray = new XoopsFormElementTray(_AM_AVATAR, "&nbsp;");
//  $avatar_tray->addElement($avatar_select);
//  $avatar_tray->addElement($avatar_label);
//  foreach ($a_dir_labels as $a_dir_label) {
//      $avatar_tray->addElement($a_dir_label);
//  }
//  $theme_select = new XoopsFormSelectTheme(_AM_THEME, "theme", $theme_value);
$timezone_select = new XoopsFormSelectTimezone(_US_TIMEZONE, "timezone_offset", $timezone_value);
$icq_text = new XoopsFormText(_AM_ICQ, "user_icq", 15, 15, $icq_value);
$aim_text = new XoopsFormText(_AM_AIM, "user_aim", 18, 18, $aim_value);
$yim_text = new XoopsFormText(_AM_YIM, "user_yim", 25, 25, $yim_value);
$msnm_text = new XoopsFormText(_AM_MSNM, "user_msnm", 30, 100, $msnm_value);
$location_text = new XoopsFormText(_AM_LOCATION, "user_from", 30, 100, $location_value);
$occupation_text = new XoopsFormText(_AM_OCCUPATION, "user_occ", 30, 100, $occ_value);
$interest_text = new XoopsFormText(_AM_INTEREST, "user_intrest", 30, 150, $interest_value);
$sig_tray = new XoopsFormElementTray(_AM_SIGNATURE, "<br />");
$sig_tarea = new XoopsFormTextArea("", "user_sig", $sig_value);
$sig_tray->addElement($sig_tarea);
$sig_cbox = new XoopsFormCheckBox("", "attachsig", $sig_cbox_value);
$sig_cbox->addOption(1, _US_SHOWSIG);
$sig_tray->addElement($sig_cbox);
$umode_select = new XoopsFormSelect(_US_CDISPLAYMODE, "umode", $umode_value);
$umode_select->addOptionArray(array("nest"=>_NESTED, "flat"=>_FLAT, "thread"=>_THREADED));
$uorder_select = new XoopsFormSelect(_US_CSORTORDER, "uorder", $uorder_value);
$uorder_select->addOptionArray(array("0"=>_OLDESTFIRST, "1"=>_NEWESTFIRST));
// RMV-NOTIFY
include_once XOOPS_ROOT_PATH. '/language/' . $xoopsConfig['language'] . '/notification.php';
include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
$notify_method_select = new XoopsFormSelect(_NOT_NOTIFYMETHOD, 'notify_method', $notify_method_value);
$notify_method_select->addOptionArray(array(XOOPS_NOTIFICATION_METHOD_DISABLE=>_NOT_METHOD_DISABLE, XOOPS_NOTIFICATION_METHOD_PM=>_NOT_METHOD_PM, XOOPS_NOTIFICATION_METHOD_EMAIL=>_NOT_METHOD_EMAIL));
$notify_mode_select = new XoopsFormSelect(_NOT_NOTIFYMODE, 'notify_mode', $notify_mode_value);
$notify_mode_select->addOptionArray(array(XOOPS_NOTIFICATION_MODE_SENDALWAYS=>_NOT_MODE_SENDALWAYS, XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE=>_NOT_MODE_SENDONCE, XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT=>_NOT_MODE_SENDONCEPERLOGIN));
$bio_tarea = new XoopsFormTextArea(_US_EXTRAINFO, "bio", $bio_value);
$rank_select = new XoopsFormSelect(_AM_RANK, "rank", $rank_value);
$ranklist = XoopsLists::getUserRankList();
if ( count($ranklist) > 0 ) {
    $rank_select->addOption(0, _AM_NSRA);
    $rank_select->addOption(0, "--------------");
    $rank_select->addOptionArray($ranklist);
} else {
    $rank_select->addOption(0, _AM_NSRID);
}
$pwd_text = new XoopsFormPassword(_AM_PASSWORD, "pass", 10, 32);
$pwd_text2 = new XoopsFormPassword(_AM_RETYPEPD, "pass2", 10, 32);
$mailok_radio = new XoopsFormRadioYN(_US_MAILOK, 'user_mailok', $mailok_value);

// Groups administration addition XOOPS 2.0.9: Mith
global $xoopsUser;
$gperm_handler =& xoops_gethandler('groupperm');
//If user has admin rights on groups
if ($gperm_handler->checkRight("system_admin", XOOPS_SYSTEM_GROUP, $xoopsUser->getGroups(), 1)) {
    //add group selection
    $group_select = new XoopsFormSelectGroup(_US_GROUPS, 'groups', false, $groups, 5, true);
}
else {
    //add empty variable
    $group_select = new XoopsFormHidden('groups[]', XOOPS_GROUP_USERS);
}

$fct_hidden = new XoopsFormHidden("fct", "users");
$op_hidden = new XoopsFormHidden("op", $op_value);
$submit_button = new XoopsFormButton("", "submit", _SUBMIT, "submit");

$form = new XoopsThemeForm($form_title, "userinfo", "admin.php");
$form->addElement(new XoopsFormToken(XoopsMultiTokenHandler::quickCreate('users_'.$op_value)));
$form->addElement($uname_text, true);
$form->addElement($name_text);
$form->addElement($email_tray, true);
$form->addElement($url_text);
//  $form->addElement($avatar_tray);
//  $form->addElement($theme_select);
$form->addElement($timezone_select);
$form->addElement($icq_text);
$form->addElement($aim_text);
$form->addElement($yim_text);
$form->addElement($msnm_text);
$form->addElement($location_text);
$form->addElement($occupation_text);
$form->addElement($interest_text);
$form->addElement($sig_tray);
$form->addElement($umode_select);
$form->addElement($uorder_select);
// RMV-NOTIFY
$form->addElement($notify_method_select);
$form->addElement($notify_mode_select);
$form->addElement($bio_tarea);
$form->addElement($rank_select);
// adding a new user requires password fields
if (!$form_isedit) {
    $form->addElement($pwd_text, true);
    $form->addElement($pwd_text2, true);
} else {
    $form->addElement($pwd_text);
    $form->addElement($pwd_text2);
}
$form->addElement($mailok_radio);
$form->addElement($group_select);
$form->addElement($fct_hidden);
$form->addElement($op_hidden);
$form->addElement($submit_button);
if ( !empty($uid_value) ) {
    $uid_hidden = new XoopsFormHidden("uid", $uid_value);
    $form->addElement($uid_hidden);
}
//$form->setRequired($uname_text);
//$form->setRequired($email_text);
$form->display();
?>
