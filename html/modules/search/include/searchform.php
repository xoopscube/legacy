<?php
// $Id: searchform.php,v 1.1 2004/09/09 05:15:01 onokazu Exp $
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
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

global $xoopsUser;

// create form
$search_form = new XoopsThemeForm(_MD_SEARCH, "search", "index.php", 'get');

// create form elements
$search_form->addElement(new XoopsFormText(_MD_KEYWORDS, "query", 30, 255, htmlspecialchars(stripslashes(implode(" ", $queries)), ENT_QUOTES)), true);
$type_select = new XoopsFormSelect(_MD_TYPE, "andor", $andor);
$type_select->addOptionArray(array("AND"=>_MD_ALL, "OR"=>_MD_ANY, "exact"=>_MD_EXACT));
$search_form->addElement($type_select);
if (!empty($mids)) {
	$mods_checkbox = new XoopsFormCheckBox(_MD_SEARCHIN, "mids[]", $mids);
} else {
	$mods_checkbox = new XoopsFormCheckBox(_MD_SEARCHIN, "mids[]", $mid);
}
if (empty($modules)) {
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('hassearch', 1));
	$criteria->add(new Criteria('isactive', 1));
	if (!empty($available_modules)) {
		$criteria->add(new Criteria('mid', "(".implode(',', $available_modules).")", 'IN'));
	}
	$db =& Database::getInstance();
	$result = $db->query("SELECT mid FROM ".$db->prefix("search")." WHERE notshow!=0");
    	while (list($badmid) = $db->fetchRow($result)) {
		$criteria->add(new Criteria('mid', $badmid, '!='));
	}
	$module_handler =& xoops_gethandler('module');
	$mod_arr = $module_handler->getList($criteria);
	$mods_checkbox->addOptionArray($mod_arr);
	if( count($mod_arr) == 0){
		$mods_checkbox = new XoopsFormLabel(_MD_SEARCHIN,_MD_UNABLE_TO_SEARCH);
	}
}
else {
    foreach ($modules as $mid => $module) {
        $module_array[$mid] = $module->getVar('name');
    }
    $mods_checkbox->addOptionArray($module_array);
}
$search_form->addElement($mods_checkbox);
if( $xoopsModuleConfig['search_display_text'] == 1 ){
	$search_form->addElement(new XoopsFormRadioYN(_MD_SHOW_CONTEXT, "showcontext", $showcontext));
}
$lessthan = ($xoopsConfigSearch['keyword_min'] > 1) ? sprintf(_MD_KEYIGNORE, $xoopsConfigSearch['keyword_min'], ceil($xoopsConfigSearch['keyword_min']/2)).'<br />' : "" ;
$search_form->addElement(new XoopsFormLabel(_MD_SEARCHRULE, $lessthan._MD_KEY_SPACE));
$search_form->addElement(new XoopsFormHidden("action", "results"));
$search_form->addElement(new XoopsFormButton("", "submit", _MD_SEARCH, "submit"));
?>