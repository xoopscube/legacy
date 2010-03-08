<?php
// $Id: tplform.php,v 1.1 2007/05/15 02:34:47 minahito Exp $
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

include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
$form = new XoopsThemeForm(_MD_EDITTEMPLATE, 'template_form', 'admin.php');
$form->addElement(new XoopsFormLabel(_MD_FILENAME, $tform['tpl_file']));
$form->addElement(new XoopsFormLabel(_MD_FILEDESC, $tform['tpl_desc']));
$form->addElement(new XoopsFormLabel(_MD_LASTMOD, formatTimestamp($tform['tpl_lastmodified'], 'l')));
$form->addElement(new XoopsFormTextArea(_MD_FILEHTML, 'html', $tform['tpl_source'], 25, 70));
$form->addElement(new XoopsFormHidden('id', $tform['tpl_id']));
$form->addElement(new XoopsFormHidden('op', 'edittpl_go'));
$form->addElement(new XoopsFormToken(XoopsMultiTokenHandler::quickCreate('tplform')));
$form->addElement(new XoopsFormHidden('redirect', 'edittpl'));
$form->addElement(new XoopsFormHidden('fct', 'tplsets'));
$form->addElement(new XoopsFormHidden('moddir', $tform['tpl_module']));
if ($tform['tpl_tplset'] != 'default') {
    $button_tray = new XoopsFormElementTray('');
    $button_tray->addElement(new XoopsFormButton('', 'previewtpl', _PREVIEW, 'submit'));
    $button_tray->addElement(new XoopsFormButton('', 'submittpl', _SUBMIT, 'submit'));
    $form->addElement($button_tray);
} else {
    $form->addElement(new XoopsFormButton('', 'previewtpl', _MD_VIEW, 'submit'));
}
?>