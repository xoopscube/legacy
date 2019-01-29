<?php
// $Id: formelementtray.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
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
 * 
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
 
/**
 * A group of form elements
 * 
 * @author	Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 * 
 * @package     kernel
 * @subpackage  form
 */
class XoopsFormElementTray extends XoopsFormElement
{

    /**
     * array of form element objects
     * @var array   
     * @access  private
     */
    public $_elements = array();

    /**
     * required elements
     * @var array   
     */
    public $_required = array();

    /**
     * HTML to seperate the elements
     * @var	string  
     * @access  private
     */
    public $_delimeter;

    /**
     * constructor
     * 
     * @param	string  $caption    Caption for the group.
     * @param	string  $delimiter  HTML to separate the elements
     */
    public function __construct($caption, $delimeter="&nbsp;", $name="")
    {
        $this->setName($name);
        $this->setCaption($caption);
        $this->_delimeter = $delimeter;
    }
    public function XoopsFormElementTray($caption, $delimeter="&nbsp;", $name="")
    {
        return self::__construct($caption, $delimeter, $name);
    }

    /**
     * Is this element a container of other elements?
     * 
     * @return	bool true
     */
    public function isContainer()
    {
        return true;
    }

    /**
     * Add an element to the group
     * 
     * @param	object  &$element    {@link XoopsFormElement} to add
     */
    public function addElement(&$formElement, $required=false)
    {
        $this->_elements[] =& $formElement;
        if ($required) {
            if (!$formElement->isContainer()) {
                $this->_required[] =& $formElement;
            } else {
                $required_elements =& $formElement->getElements(true);
                $count = count($required_elements);
                for ($i = 0 ; $i < $count; $i++) {
                    $this->_required[] =& $required_elements[$i];
                }
            }
        }
    }

    /**
     * get an array of "required" form elements
     * 
     * @return	array   array of {@link XoopsFormElement}s 
     */
    public function &getRequired()
    {
        return $this->_required;
    }

    /**
     * Get an array of the elements in this group
     * 
     * @param	bool	$recurse	get elements recursively?
     * @return  array   Array of {@link XoopsFormElement} objects. 
     */
    public function &getElements($recurse = false)
    {
        if (!$recurse) {
            return $this->_elements;
        } else {
            $ret = array();
            $count = count($this->_elements);
            for ($i = 0; $i < $count; $i++) {
                if (!$this->_elements[$i]->isContainer()) {
                    $ret[] =& $this->_elements[$i];
                } else {
                    $elements =& $this->_elements[$i]->getElements(true);
                    $count2 = count($elements);
                    for ($j = 0; $j < $count2; $j++) {
                        $ret[] =& $elements[$j];
                    }
                    unset($elements);
                }
            }
            return $ret;
        }
    }

    /**
     * Get the delimiter of this group
     * 
     * @return	string  The delimiter
     */
    public function getDelimeter()
    {
        return $this->_delimeter;
    }

    /**
     * prepare HTML to output this group
     * 
     * @return	string  HTML output
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
        
        $renderTarget =& $renderSystem->createRenderTarget('main');
    
        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName("legacy_xoopsform_elementtray.html");
        $renderTarget->setAttribute("tray", $this);

        $renderSystem->render($renderTarget);
    
        return $renderTarget->getResult();
    }
}
