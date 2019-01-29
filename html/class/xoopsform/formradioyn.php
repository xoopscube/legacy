<?php
// $Id: formradioyn.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
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
 * base class
 */
include_once XOOPS_ROOT_PATH."/class/xoopsform/formradio.php";

/**
 * Yes/No radio buttons.
 * 
 * A pair of radio buttons labelled _YES and _NO with values 1 and 0
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormRadioYN extends XoopsFormRadio
{
    /**
     * Constructor
     * 
     * @param	string	$caption
     * @param	string	$name
     * @param	string	$value		Pre-selected value, can be "0" (No) or "1" (Yes)
     * @param	string	$yes		String for "Yes"
     * @param	string	$no			String for "No"
     */
    public function __construct($caption, $name, $value=null, $yes=_YES, $no=_NO)
    {
        $this->XoopsFormRadio($caption, $name, $value);
        $this->addOption(1, $yes);
        $this->addOption(0, $no);
    }
    public function XoopsFormRadioYN($caption, $name, $value=null, $yes=_YES, $no=_NO)
    {
        return self::__construct($caption, $name, $value, $yes, $no);
    }
}
