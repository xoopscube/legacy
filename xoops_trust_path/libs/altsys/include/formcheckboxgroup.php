<?php
// $Id: formcheckboxgroup.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
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

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * Parent
 */
include_once XOOPS_ROOT_PATH."/class/xoopsform/formcheckbox.php";

/**
 * A checkbox field with a choice of available groups
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class AltsysFormCheckboxGroup extends XoopsFormCheckbox
{
    /**
     * Constructor
     * 
     * @param	string	$caption	
     * @param	string	$name
     * @param	bool	$include_anon	Include group "anonymous"?
     * @param	mixed	$value	    	Pre-selected value (or array of them).
     */
    public function AltsysFormCheckboxGroup($caption, $name, $include_anon=false, $value=null)
    {
        $this->XoopsFormCheckbox($caption, $name, $value);
        $member_handler =& xoops_gethandler('member');
        if (!$include_anon) {
            $options = $member_handler->getGroupList(new Criteria('groupid', XOOPS_GROUP_ANONYMOUS, '!='));
        } else {
            $options = $member_handler->getGroupList();
        }
        foreach ($options as $k => $v) {
            $options[$k] = $v . '<br />';
        }
        $this->addOptionArray($options);
    }
}
