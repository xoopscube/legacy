<?php
// $Id: comment_form.php,v 1.1 2007/05/15 02:34:18 minahito Exp $
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

if (!defined('XOOPS_ROOT_PATH') || !is_object($xoopsModule)) {
    exit();
}
$com_modid = $xoopsModule->getVar('mid');
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
$cform = new XoopsThemeForm(_CM_POSTCOMMENT, "commentform", 'comment_post.php'); if (isset($xoopsModuleConfig['com_rule'])) {
    include_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
    switch ($xoopsModuleConfig['com_rule']) {
    case XOOPS_COMMENT_APPROVEALL:
        $rule_text = _CM_COMAPPROVEALL;
        break;
    case XOOPS_COMMENT_APPROVEUSER:
        $rule_text = _CM_COMAPPROVEUSER;
        break;
    case XOOPS_COMMENT_APPROVEADMIN:
        default:
        $rule_text = _CM_COMAPPROVEADMIN;
        break;
    }
    $cform->addElement(new XoopsFormLabel(_CM_COMRULES, $rule_text));
}

$cform->addElement(new XoopsFormText(_CM_TITLE, 'com_title', 50, 255, $com_title), true);
$icons_radio = new XoopsFormRadio(_MESSAGEICON, 'com_icon', $com_icon);
$subject_icons = XoopsLists::getSubjectsList();
foreach ($subject_icons as $iconfile) {
    $icons_radio->addOption($iconfile, '<img src="'.XOOPS_URL.'/images/subject/'.$iconfile.'" alt="" />');
}
$cform->addElement($icons_radio);
$cform->addElement(new XoopsFormDhtmlTextArea(_CM_MESSAGE, 'com_text', $com_text, 10, 50), true);
$option_tray = new XoopsFormElementTray(_OPTIONS, '<br />');

$button_tray = new XoopsFormElementTray('', '&nbsp;');


if (is_object($xoopsUser)) {
    if ($xoopsModuleConfig['com_anonpost'] == 1) {
        $noname = !empty($noname) ? 1 : 0;
        $noname_checkbox = new XoopsFormCheckBox('', 'noname', $noname);
        $noname_checkbox->addOption(1, _POSTANON);
        $option_tray->addElement($noname_checkbox);
    }
    if (false != $xoopsUser->isAdmin($com_modid)) {
        // show status change box when editing (comment id is not empty)
        if (!empty($com_id)) {
            include_once XOOPS_ROOT_PATH.'/include/comment_constants.php';
            $status_select = new XoopsFormSelect(_CM_STATUS, 'com_status', $com_status);
            $status_select->addOptionArray(array(XOOPS_COMMENT_PENDING => _CM_PENDING, XOOPS_COMMENT_ACTIVE => _CM_ACTIVE, XOOPS_COMMENT_HIDDEN => _CM_HIDDEN));
            $cform->addElement($status_select);
            $button_tray->addElement(new XoopsFormButton('', 'com_dodelete', _DELETE, 'submit'));
        }
        $html_checkbox = new XoopsFormCheckBox('', 'dohtml', $dohtml);
        $html_checkbox->addOption(1, _CM_DOHTML);
        $option_tray->addElement($html_checkbox);
    }
}
$smiley_checkbox = new XoopsFormCheckBox('', 'dosmiley', $dosmiley);
$smiley_checkbox->addOption(1, _CM_DOSMILEY);
$option_tray->addElement($smiley_checkbox);
$xcode_checkbox = new XoopsFormCheckBox('', 'doxcode', $doxcode);
$xcode_checkbox->addOption(1, _CM_DOXCODE);
$option_tray->addElement($xcode_checkbox);
$br_checkbox = new XoopsFormCheckBox('', 'dobr', $dobr);
$br_checkbox->addOption(1, _CM_DOAUTOWRAP);
$option_tray->addElement($br_checkbox);

$cform->addElement($option_tray);
$cform->addElement(new XoopsFormHidden('com_pid', (int)$com_pid));
$cform->addElement(new XoopsFormHidden('com_rootid', (int)$com_rootid));
$cform->addElement(new XoopsFormHidden('com_id', $com_id));
$cform->addElement(new XoopsFormHidden('com_itemid', $com_itemid));
$cform->addElement(new XoopsFormHidden('com_order', $com_order));
$cform->addElement(new XoopsFormHidden('com_mode', $com_mode));

// add module specific extra params

if ('system' != $xoopsModule->getVar('dirname')) {
    $comment_config = $xoopsModule->getInfo('comments');
    if (isset($comment_config['extraParams']) && is_array($comment_config['extraParams'])) {
        $myts =& MyTextSanitizer::sGetInstance();
        foreach ($comment_config['extraParams'] as $extra_param) {
            // This routine is included from forms accessed via both GET and POST
            if (isset($_POST[$extra_param])) {
                $hidden_value = $myts->stripSlashesGPC($_POST[$extra_param]);
            } elseif (isset($_GET[$extra_param])) {
                $hidden_value = $myts->stripSlashesGPC($_GET[$extra_param]);
            } else {
                $hidden_value = '';
            }
            $cform->addElement(new XoopsFormHidden($extra_param, htmlspecialchars($hidden_value, ENT_QUOTES)));
        }
    }
}
$button_tray->addElement(new XoopsFormButton('', 'com_dopreview', _PREVIEW, 'submit'));
$button_tray->addElement(new XoopsFormButton('', 'com_dopost', _CM_POSTCOMMENT, 'submit'));
$cform->addElement($button_tray);
//$cform->addElement(new XoopsFormHiddenToken());
$cform->display();
