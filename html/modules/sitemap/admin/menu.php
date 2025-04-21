<?php
/**
 * @file
 * @package Sitemap
 * @version 2.5.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

##[cubson:adminmenu]
$adminmenu[0]['title'] =_MI_SITEMAP_ADMENU_OVERVIEW;
$adminmenu[0]['link'] = 'admin/index.php';
$adminmenu[0]['keywords'] =_MI_SITEMAP_KEYWORD_LIST;
$adminmenu[0]['show'] = true;
##[/cubson:adminmenu]
;