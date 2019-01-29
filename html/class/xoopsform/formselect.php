<?php
// $Id: formselect.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
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
 * A select field
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormSelect extends XoopsFormElement
{

    /**
     * Options
     * @var array   
     * @access	private
     */
    public $_options = array();

    /**
     * Allow multiple selections?
     * @var	bool    
     * @access	private
     */
    public $_multiple = false;

    /**
     * Number of rows. "1" makes a dropdown list.
     * @var	int 
     * @access	private
     */
    public $_size;

    /**
     * Pre-selcted values
     * @var	array   
     * @access	private
     */
    public $_value = array();

    /**
     * Constructor
     * 
     * @param	string	$caption	Caption
     * @param	string	$name       "name" attribute
     * @param	mixed	$value	    Pre-selected value (or array of them).
     * @param	int		$size	    Number or rows. "1" makes a drop-down-list
     * @param	bool    $multiple   Allow multiple selections?
     */
    public function __construct($caption, $name, $value=null, $size=1, $multiple=false)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_multiple = $multiple;
        $this->_size = intval($size);
        if (isset($value)) {
            $this->setValue($value);
        }
    }
    public function XoopsFormSelect($caption, $name, $value=null, $size=1, $multiple=false)
    {
        return self::__construct($caption, $name, $value, $size, $multiple);
    }

    /**
     * Are multiple selections allowed?
     * 
     * @return	bool
     */
    public function isMultiple()
    {
        return $this->_multiple;
    }

    /**
     * Get the size
     * 
     * @return	int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get an array of pre-selected values
     * 
     * @return	array
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set pre-selected values
     * 
     * @param	$value	mixed
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                $this->_value[] = $v;
            }
        } else {
            $this->_value[] = $value;
        }
    }

    /**
     * Add an option
     * 
     * @param	string  $value  "value" attribute
     * @param	string  $name   "name" attribute
     */
    public function addOption($value, $name="")
    {
        if ($name != "") {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple options
     * 
     * @param	array   $options    Associative array of value->name pairs
     */
    public function addOptionArray($options)
    {
        if (is_array($options)) {
            foreach ($options as $k=>$v) {
                $this->addOption($k, $v);
            }
        }
    }

    /**
     * Get all options
     * 
     * @return	array   Associative array of value->name pairs
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Prepare HTML for output
     * 
     * @return	string  HTML
     */
    public function render()
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);
        
        $renderTarget =& $renderSystem->createRenderTarget('main');
    
        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName("legacy_xoopsform_select.html");
        $renderTarget->setAttribute("element", $this);

        $renderSystem->render($renderTarget);
    
        return $renderTarget->getResult();
    }
}
