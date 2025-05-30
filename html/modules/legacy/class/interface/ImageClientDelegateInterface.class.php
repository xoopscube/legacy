<?php
/**
 * This Interface is generated by Cube tool.
 * @package    Legacy
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     code generator
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

/**
 * Interface of image client delegate
 * Modules which uses Legacy_Image must implement this interface.
 * Legacy_Image module must be unique.
 * You can get its dirname by constant LEGACY_IMAGE_DIRNAME
*/
interface Legacy_iImageClientDelegate
{
    /**
     * getClientList	Legacy_Image.{dirname}.GetClientList
     * Get client module's dirname and dataname(tablename)
     *
     * @param mixed[] &$list
     *  $list[]['dirname']	client module dirname
     *  $list[]['dataname']	client module dataname(tablename)
     *
     * @return	void
     */
    public static function getClientList(/*** array ***/ &$list);
}
