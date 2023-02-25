<?php
/**
 * legacy_waiting.php
 * XOOPS2
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 * @brief      This file was entirely rewritten by the XOOPSCube Legacy project
 *             for compatibility with XOOPS2.
 */

function b_legacy_waiting_show()
{
    $modules = [];
    XCube_DelegateUtils::call('Legacyblock.Waiting.Show', new XCube_Ref($modules));
    $block['modules'] = $modules;
    return $block;
}
