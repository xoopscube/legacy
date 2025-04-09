<?php
/**
 * Form radio buttons  Yes/No
 * A pair of radio buttons labelled _YES and _NO with values 1 and 0
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.5.0
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
 * base class
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/formradio.php';


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
        return $this->__construct($caption, $name, $value, $yes, $no);
    }
}
