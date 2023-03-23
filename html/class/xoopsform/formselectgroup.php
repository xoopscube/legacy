<?php
/**
 * Form select field with a choice of available groups
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
 * Parent
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/formselect.php';


class XoopsFormSelectGroup extends XoopsFormSelect
{
    /**
     * Constructor
     *
     * @param	string	$caption
     * @param	string	$name
     * @param	bool	$include_anon	Include group "anonymous" ?
     * @param	mixed	$value	    	Pre-selected value (or array of them).
     * @param	int		$size	        Number or rows. "1" makes a drop-down-list.
     * @param	bool    $multiple       Allow multiple selections?
     */
    public function __construct($caption, $name, $include_anon=false, $value=null, $size=1, $multiple=false)
    {
        //$this->XoopsFormSelect($caption, $name, $value, $size, $multiple);
		parent::__construct($caption, $name, $value, $size, $multiple);
         $member_handler =& xoops_gethandler('member');
        if (!$include_anon) {
            $this->addOptionArray($member_handler->getGroupList(new Criteria('groupid', XOOPS_GROUP_ANONYMOUS, '!=')));
        } else {
            $this->addOptionArray($member_handler->getGroupList());
        }
    }
    public function XoopsFormSelectGroup($caption, $name, $include_anon=false, $value=null, $size=1, $multiple=false)
    {
        return $this->__construct($caption, $name, $include_anon, $value, $size, $multiple);
    }
}
