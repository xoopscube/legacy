<?php
/**
 * Sitemap Module - Admin Menu
 * @package    Sitemap
 * @version    2.5.0
 * @author     gigamaster 2020 XCL/PHP7
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

$adminmenu = []; // Initialize for safety, though XCL usually handles this

$adminmenu[0]['title'] =_MI_SITEMAP_ADMENU_OVERVIEW;
$adminmenu[0]['link'] = 'admin/index.php';
$adminmenu[0]['keywords'] =_MI_SITEMAP_LIST_KEYWORD;
$adminmenu[0]['show'] = true;

$adminmenu[1]['title'] = _MI_SITEMAP_ADMENU_ROBOTS;
$adminmenu[1]['link'] = 'admin/robots_editor.php';
$adminmenu[1]['keywords'] = _MI_SITEMAP_ROBOTS_KEYWORD;
$adminmenu[1]['show'] = true;

$adminmenu[2]['title'] = _MI_SITEMAP_ADMENU_PAGESPEED;
$adminmenu[2]['link'] = 'admin/pagespeed.php';
$adminmenu[2]['keywords'] = _MI_SITEMAP_PAGESPEED_KEYWORD;
$adminmenu[2]['show'] = true;