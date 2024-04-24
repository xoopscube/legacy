<?php
/**
 * Form Date and time selection field
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

class XoopsFormDateTime extends XoopsFormElementTray
{

    public function __construct($caption, $name, $size = 15, $value=0)
    {
        $this->XoopsFormElementTray($caption, '&nbsp;');
        $value = (int)$value;
        $value = ($value > 0) ? $value : time();
        $datetime = getDate($value);
        $this->addElement(new XoopsFormTextDateSelect('', $name.'[date]', $size, $value));
        $timearray = [];
        for ($i = 0; $i < 24; $i++) {
            for ($j = 0; $j < 60; $j += 10) {
                $key = ($i * 3600) + ($j * 60);
                $timearray[$key] = (0 !== $j) ? $i . ':' . $j : $i . ':0' . $j;
            }
        }
        ksort($timearray);
        $timeselect = new XoopsFormSelect('', $name.'[time]', $datetime['hours'] * 3600 + 600 * ceil($datetime['minutes'] / 10));
        $timeselect->addOptionArray($timearray);
        $this->addElement($timeselect);
    }
    public function XoopsFormDateTime($caption, $name, $size = 15, $value=0)
    {
        return $this->__construct($caption, $name, $size, $value);
    }
}
