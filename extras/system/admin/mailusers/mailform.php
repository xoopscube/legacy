<?php
// $Id: mailform.php,v 1.1 2007/05/15 02:35:06 minahito Exp $
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

$form = new XoopsThemeForm(_AM_SENDMTOUSERS, "mailusers", "admin.php?fct=mailusers");
$form->addElement(new XoopsFormToken(XoopsSingleTokenHandler::quickCreate('mailusers_send')));

// from finduser section
if (!empty($_POST['memberslist_id'])) {
    $user_count = count($_POST['memberslist_id']);
    $display_names = "";
    for ( $i = 0; $i < $user_count; $i++ ) {
        $uid_hidden = new XoopsFormHidden("mail_to_user[]", $_POST['memberslist_id'][$i]);
        $form->addElement($uid_hidden);
        $display_names .= "<a href='".XOOPS_URL."/userinfo.php?uid=".$_POST['memberslist_id'][$i]."' rel='external'>".$_POST['memberslist_uname'][$_POST['memberslist_id'][$i]]."</a>, ";
        unset($uid_hidden);
    }
    $users_label = new XoopsFormLabel(_AM_SENDTOUSERS2, substr($display_names, 0, -2));
    $form->addElement($users_label);
    $display_criteria = 0;
}
if ( !empty($display_criteria) ) {
    $selected_groups = array();
    $group_select = new XoopsFormSelectGroup(_AM_GROUPIS."<br />", "mail_to_group", false, $selected_groups, 5, true);
    $lastlog_min = new XoopsFormText(_AM_LASTLOGMIN."<br />"._AM_TIMEFORMAT."<br />", "mail_lastlog_min", 20, 10);
    $lastlog_max = new XoopsFormText(_AM_LASTLOGMAX."<br />"._AM_TIMEFORMAT."<br />", "mail_lastlog_max", 20, 10);
    $regd_min = new XoopsFormText(_AM_REGDMIN."<br />"._AM_TIMEFORMAT."<br />", "mail_regd_min", 20, 10);
    $regd_max = new XoopsFormText(_AM_REGDMAX."<br />"._AM_TIMEFORMAT."<br />", "mail_regd_max", 20, 10);
    $idle_more = new XoopsFormText(_AM_IDLEMORE."<br />", "mail_idle_more", 10, 5);
    $idle_less = new XoopsFormText(_AM_IDLELESS."<br />", "mail_idle_less", 10, 5);
    $mailok_cbox = new XoopsFormCheckBox('', 'mail_mailok');
    $mailok_cbox->addOption(1, _AM_MAILOK);
    $inactive_cbox = new XoopsFormCheckBox(_AM_INACTIVE."<br />", "mail_inactive");
    $inactive_cbox->addOption(1, _AMIFCHECKD);
    $inactive_cbox->setExtra("onclick='javascript:disableElement(\"mail_lastlog_min\");disableElement(\"mail_lastlog_max\");disableElement(\"mail_idle_more\");disableElement(\"mail_idle_less\");disableElement(\"mail_to_group[]\");'");
    $criteria_tray = new XoopsFormElementTray(_AM_SENDTOUSERS, "<br /><br />");
    $criteria_tray->addElement($group_select);
    $criteria_tray->addElement($lastlog_min);
    $criteria_tray->addElement($lastlog_max);
    $criteria_tray->addElement($idle_more);
    $criteria_tray->addElement($idle_less);
    $criteria_tray->addElement($mailok_cbox);
    $criteria_tray->addElement($inactive_cbox);
    $criteria_tray->addElement($regd_min);
    $criteria_tray->addElement($regd_max);
    $form->addElement($criteria_tray);
}

$fname_text = new XoopsFormText(_AM_MAILFNAME, "mail_fromname", 30, 255, htmlspecialchars($xoopsConfig['sitename'], ENT_QUOTES));
$fromemail = !empty($xoopsConfig['adminmail']) ? $xoopsConfig['adminmail'] : $xoopsUser->getVar("email", "E");
$femail_text = new XoopsFormText(_AM_MAILFMAIL, "mail_fromemail", 30, 255, $fromemail);
//$subject_caption = _AM_MAILSUBJECT."<br /><br /><span style='font-size:x-small;font-weight:bold;'>"._AM_MAILTAGS."</span><br /><span style='font-size:x-small;font-weight:normal;'>"._AM_MAILTAGS1."<br />"._AM_MAILTAGS2."<br />"._AM_MAILTAGS3."</span>";
$subject_caption = _AM_MAILSUBJECT."<br /><br /><span style='font-size:x-small;font-weight:bold;'>"._AM_MAILTAGS."</span><br /><span style='font-size:x-small;font-weight:normal;'>"._AM_MAILTAGS2."</span>";
$subject_text = new XoopsFormText($subject_caption, "mail_subject", 50, 255);
$body_caption = _AM_MAILBODY."<br /><br /><span style='font-size:x-small;font-weight:bold;'>"._AM_MAILTAGS."</span><br /><span style='font-size:x-small;font-weight:normal;'>"._AM_MAILTAGS1."<br />"._AM_MAILTAGS2."<br />"._AM_MAILTAGS3."<br />"._AM_MAILTAGS4."</span>";
$body_text = new XoopsFormTextArea($body_caption, "mail_body", "", 10);
$to_checkbox = new XoopsFormCheckBox(_AM_SENDTO, "mail_send_to", "mail");
$to_checkbox->addOption("mail", _AM_EMAIL);
$to_checkbox->addOption("pm", _AM_PM);
$start_hidden = new XoopsFormHidden("mail_start", 0);
$op_hidden = new XoopsFormHidden("op", "send");
$submit_button = new XoopsFormButton("", "mail_submit", _SEND, "submit");

$form->addElement($fname_text);
$form->addElement($femail_text);
$form->addElement($subject_text);
$form->addElement($body_text);
$form->addElement($to_checkbox);
$form->addElement($op_hidden);
$form->addElement($start_hidden);
$form->addElement($submit_button);
$form->setRequired($subject_text);
$form->setRequired($body_text);
//$form->setRequired($to_checkbox);
?>