<?php
// $Id: formdhtmltextarea.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
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

if (!defined('XOOPS_ROOT_PATH')) exit();

/**
 *
 *
 * @package     kernel
 * @subpackage  form
 *
 * @author      Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 */
/**
 * base class
 */
include_once XOOPS_ROOT_PATH."/class/xoopsform/formtextarea.php";

// Make sure you have included /include/xoopscodes.php, otherwise DHTML will not work properly!

/**
 * A textarea with xoopsish formatting and smilie buttons
 *
 * @author  Kazumi Ono  <onokazu@xoops.org>
 * @copyright   copyright (c) 2000-2003 XOOPS.org
 *
 * @package     kernel
 * @subpackage  form
 */
class XoopsFormDhtmlTextArea extends XoopsFormTextArea
{
    /**
     * Hidden text
     * @var string
     * @access  private
     */
    var $_hiddenText;

    /**
     * Constructor
     *
     * @param   string  $caption    Caption
     * @param   string  $name       "name" attribute
     * @param   string  $value      Initial text
     * @param   int     $rows       Number of rows
     * @param   int     $cols       Number of columns
     * @param   string  $hiddentext Hidden Text
     */
    function XoopsFormDhtmlTextArea($caption, $name, $value, $rows=5, $cols=50, $hiddentext="xoopsHiddenText")
    {
        $this->XoopsFormTextArea($caption, $name, $value, $rows, $cols);
        $this->_xoopsHiddenText = $hiddentext;
    }

    /**
     * Prepare HTML for output
     *
     * @return  string  HTML
     */
    function render()
    {
		$root =& XCube_Root::getSingleton();
		$renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
		
		$renderTarget =& $renderSystem->createRenderTarget('main');
	
		$renderTarget->setAttribute('legacy_module', 'legacy');
		$renderTarget->setTemplateName("legacy_xoopsform_dhtmltextarea.html");
		$renderTarget->setAttribute("element", $this);
		$renderTarget->setAttribute("class", $this->getClass());

		$renderSystem->render($renderTarget);
	
		$ret = $renderTarget->getResult();
        $ret .= $this->_renderSmileys();
		
		return $ret;
    }

    /**
     * prepare HTML for output of the smiley list.
     *
     * @return  string HTML
     */
    function _renderSmileys()
    {
		$handler =& xoops_getmodulehandler('smiles', 'legacy');
		$smilesArr =& $handler->getObjects(new Criteria('display', 1));
		
		$root =& XCube_Root::getSingleton();
		$renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
		$renderTarget =& $renderSystem->createRenderTarget('main');
	
		$renderTarget->setAttribute('legacy_module', 'legacy');
		$renderTarget->setTemplateName("legacy_xoopsform_opt_smileys.html");
		$renderTarget->setAttribute("element", $this);
		$renderTarget->setAttribute("smilesArr", $smilesArr);

		$renderSystem->render($renderTarget);
		
		return $renderTarget->getResult();
    }
}
?>
