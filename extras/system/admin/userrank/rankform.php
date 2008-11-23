<?php
// $Id: rankform.php,v 1.1 2007/05/15 02:34:52 minahito Exp $
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

include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
$rank_form = new XoopsThemeForm($rank['form_title'], 'rankform', 'admin.php');
$rank_form->setExtra('enctype="multipart/form-data"');
$rank_form->addElement(new XoopsFormToken(XoopsMultiTokenHandler::quickCreate('userrank_'.$rank['op'])));
$rank_form->addElement(new XoopsFormText(_AM_RANKTITLE, 'rank_title', 50, 50, $rank['rank_title']), true);
$rank_form->addElement(new XoopsFormText(_AM_MINPOST, 'rank_min', 10, 10, $rank['rank_min']));
$rank_form->addElement(new XoopsFormText(_AM_MAXPOST, 'rank_max', 10, 10, $rank['rank_max']));
$rank_tray = new XoopsFormElementTray(_AM_IMAGE, '&nbsp;');
$rank_select = new XoopsFormFile('', 'rank_image', 5000000);
$rank_tray->addElement($rank_select);
if (trim($rank['rank_image']) != '' && file_exists(XOOPS_UPLOAD_PATH.'/'.$rank['rank_image'])) {
    $rank_label = new XoopsFormLabel('', '<img src="'.XOOPS_UPLOAD_URL.'/'.$rank['rank_image'].'" alt="" />');
    $rank_tray->addElement($rank_label);
}
$rank_form->addElement($rank_tray);
$tray = new XoopsFormElementTray(_AM_SPECIAL, '<br />');
$tray->addElement(new XoopsFormRadioYN('', 'rank_special', $rank['rank_special']));
$tray->addElement(new XoopsFormLabel('', _AM_SPECIALCAN));
$rank_form->addElement($tray);
$rank_form->addElement(new XoopsFormHidden('rank_id', $rank['rank_id']));
$rank_form->addElement(new XoopsFormHidden('op', $rank['op']));
$rank_form->addElement(new XoopsFormHidden('fct', 'userrank'));
$rank_form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
?>
