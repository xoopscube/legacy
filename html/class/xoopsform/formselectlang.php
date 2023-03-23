<?php
/**
 * Form select field with available languages
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.3.3
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
 * parent class
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';

class XoopsFormSelectLang extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param	string	$caption
     * @param	string	$name
     * @param	mixed	$value	Pre-selected value (or array of them).
     * 							Legal is any name of a XOOPS_ROOT_PATH."/language/" subdirectory.
     * @param	int		$size	Number of rows. "1" makes a drop-down-list.
     */
    public function __construct($caption, $name, $value=null, $size=1)
    {
        $this->XoopsFormSelect($caption, $name, $value, $size);
        $this->addOptionArray(XoopsLists::getLangList());
    }
    public function XoopsFormSelectLang($caption, $name, $value=null, $size=1)
    {
        return $this->__construct($caption, $name, $value, $size);
    }
}
