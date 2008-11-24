<?php
// $Id: smileform.php,v 1.1 2007/05/15 02:34:46 minahito Exp $
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
$smile_form = new XoopsThemeForm($smiles['smile_form'], 'smileform', 'admin.php');
$smile_form->setExtra('enctype="multipart/form-data"');
$smile_form->addElement(new XoopsFormToken(XoopsMultiTokenHandler::quickCreate('smilies_'.$smiles['op'])));
$smile_form->addElement(new XoopsFormText(_AM_SMILECODE, 'smile_code', 26, 25, $smiles['smile_code']), true);
$smile_form->addElement(new XoopsFormText(_AM_SMILEEMOTION, 'smile_desc', 26, 25, $smiles['smile_desc']), true);
$smile_select = new XoopsFormFile('', 'smile_url', 5000000);
$smile_label = new XoopsFormLabel('', '<img src="'.XOOPS_UPLOAD_URL.'/'.$smiles['smile_url'].'" alt="" />');
$smile_tray = new XoopsFormElementTray(_IMAGEFILE.':', '&nbsp;');
$smile_tray->addElement($smile_select);
$smile_tray->addElement($smile_label);
$smile_form->addElement($smile_tray);
$smile_form->addElement(new XoopsFormRadioYN(_AM_DISPLAYF, 'smile_display', $smiles['smile_display']));
$smile_form->addElement(new XoopsFormHidden('id', $smiles['id']));
$smile_form->addElement(new XoopsFormHidden('op', $smiles['op']));
$smile_form->addElement(new XoopsFormHidden('fct', 'smilies'));
$smile_form->addElement(new XoopsFormButton('', 'submit', _SUBMIT, 'submit'));
?>