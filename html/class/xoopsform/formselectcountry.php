<?php
/**
 * Form select field with countries
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * lists of values
 */
include_once XOOPS_ROOT_PATH . '/class/xoopslists.php';

/**
 * Parent
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';


class XoopsFormSelectCountry extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param	string	$caption	Caption
     * @param	string	$name       "name" attribute
     * @param	mixed	$value	    Pre-selected value (or array of them).
     *                              Legal are all 2-letter country codes (in capitals).
     * @param	int		$size	    Number or rows. "1" makes a drop-down-list
     */
    public function __construct($caption, $name, $value=null, $size=1)
    {
        $this->XoopsFormSelect($caption, $name, $value, $size);
        $this->addOptionArray(XoopsLists::getCountryList());
    }
    public function XoopsFormSelectCountry($caption, $name, $value=null, $size=1)
    {
        return $this->__construct($caption, $name, $value, $size);
    }
}
