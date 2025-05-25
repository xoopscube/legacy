<?php
/**
 * Bannerstats - Module for XCL
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

$adminmenu[0]['title'] = _MI_BANNERSTATS_MENU_BANNER_LIST;
$adminmenu[0]['link'] = "admin/index.php?action=BannerList";

$adminmenu[1]['title'] = _MI_BANNERSTATS_MENU_BANNER_NEW;
$adminmenu[1]['link'] = "admin/index.php?action=BannerEdit";

$adminmenu[2]['title'] = _MI_BANNERSTATS_MENU_CLIENT_LIST;
$adminmenu[2]['link'] = "admin/index.php?action=BannerclientList";

$adminmenu[3]['title'] = _MI_BANNERSTATS_MENU_CLIENT_NEW;
$adminmenu[3]['link'] = "admin/index.php?action=BannerclientEdit"; 

$adminmenu[4] = [
    'title' => _MI_BANNERSTATS_ADMENU_EMAIL_TEST, // We'll define this constant
    'link'  => 'admin/index.php?action=BannerEmailTest',
];
// todo add links for Finished Banners and Reactivation:
/*
$adminmenu[4]['title'] = _MI_BANNERSTATS_MENU_BANNERFINISH_LIST;
$adminmenu[4]['link'] = "admin/index.php?action=BannerfinishList";

// Note: Reactivation is often done from the finished banner list,
// so a direct menu link to BannerReactivate might not be needed
// BannerfinishList template provides links to reactivate individual banners.
// $adminmenu[5]['title'] = _MI_BANNERSTATS_MENU_BANNER_REACTIVATE;
// $adminmenu[5]['link'] = "admin/index.php?action=BannerfinishEdit";
*/
?>
