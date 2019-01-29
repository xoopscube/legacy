<?php
// $Id: formtext.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
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
 * A simple text field
 * 
 * @package     kernel
 * @subpackage  form
 * 
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class XoopsFormText extends XoopsFormElement
{

    /**
     * Size
     * @var	int 
     * @access	private
     */
    public $_size;

    /**
     * Maximum length of the text
     * @var	int 
     * @access	private
     */
    public $_maxlength;

    /**
     * Initial text
     * @var	string  
     * @access	private
     */
    public $_value;

    /**
     * Constructor
     * 
     * @param	string	$caption	Caption
     * @param	string	$name       "name" attribute
     * @param	int		$size	    Size
     * @param	int		$maxlength	Maximum length of text
     * @param	string  $value      Initial text
     */
    public function __construct($caption, $name, $size, $maxlength, $value="")
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_size = intval($size);
        $this->_maxlength = intval($maxlength);
        $this->setValue($value);
    }
    public function XoopsFormText($caption, $name, $size, $maxlength, $value="")
    {
        return self::__construct($caption, $name, $size, $maxlength, $value);
    }

    /**
     * Get size
     * 
     * @return	int
     */
    public function getSize()
    {
        return $this->_size;
    }

    /**
     * Get maximum text length
     * 
     * @return	int
     */
    public function getMaxlength()
    {
        return $this->_maxlength;
    }

    /**
     * Get initial text value
     * 
     * @return  string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * Set initial text value
     * 
     * @param	$value  string
     */
    public function setValue($value)
    {
        $this->_value = $value;
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
        $renderTarget->setTemplateName("legacy_xoopsform_text.html");
        $renderTarget->setAttribute("element", $this);

        $renderSystem->render($renderTarget);
    
        return $renderTarget->getResult();
    }
}
