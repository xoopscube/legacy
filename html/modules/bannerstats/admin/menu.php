<?php
/**
 * Bannerstats - Admin menu
 * Migrated from legacyRender module
 */
$adminmenu[0]['title'] = _MI_BANNERSTATS_MENU_BANNER_LIST;
$adminmenu[0]['link'] = "admin/index.php?action=BannerList";

$adminmenu[1]['title'] = _MI_BANNERSTATS_MENU_BANNER_NEW;
$adminmenu[1]['link'] = "admin/index.php?action=BannerEdit"; // For creating a new banner, often the edit action is used without an ID

$adminmenu[2]['title'] = _MI_BANNERSTATS_MENU_CLIENT_LIST;
$adminmenu[2]['link'] = "admin/index.php?action=BannerclientList"; // CORRECTED

$adminmenu[3]['title'] = _MI_BANNERSTATS_MENU_CLIENT_NEW;
$adminmenu[3]['link'] = "admin/index.php?action=BannerclientEdit"; // CORRECTED (for creating a new client)

// You might also want to add links for Finished Banners and Reactivation:
/*
$adminmenu[4]['title'] = _MI_BANNERSTATS_MENU_BANNERFINISH_LIST; // Define this constant
$adminmenu[4]['link'] = "admin/index.php?action=BannerfinishList";

// Note: Reactivation is often done from the finished banner list,
// so a direct menu link to BannerReactivate might not be needed if
// the BannerfinishList template provides links to reactivate individual banners.
// If BannerfinishEditAction is your reactivate action, the link would be:
// $adminmenu[5]['title'] = _MI_BANNERSTATS_MENU_BANNER_REACTIVATE; // Define this constant
// $adminmenu[5]['link'] = "admin/index.php?action=BannerfinishEdit"; // Or BannerReactivate if that's the one
*/
?>
