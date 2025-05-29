<?php
/**
 * Bannerstats - Module for XCL
 * Bannerstats Client Menu Block
 * Displays a navigation menu for logged-in banner clients.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/modules/bannerstats/class/BannerClientSession.class.php';

/**
 * Show function for the Banner Block Menu Client
 *
 * @param array $options Block options (not used in this menu)
 * @return array|false Associative array with 'menu_items' if client is logged in
 */
function b_bannerstats_menu_show($options)
{
    if (!BannerClientSession::isAuthenticated()) {
        return false;
    }

    $block = [];
    $clientId = BannerClientSession::getClientId();

    // Get client details
    $block['client_name'] = BannerClientSession::getClientName();
    $block['client_cid']  = $clientId;

    // Active banners
    $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
    $criteriaActive = new CriteriaCompo(new Criteria('cid', $clientId));
    $criteriaActive->add(new Criteria('status', 1));
    $block['active_banners'] = $bannerHandler->getCount($criteriaActive);

    // Finished banners
    $bannerFinishHandler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
    $criteriaFinished = new Criteria('cid', $clientId);
    $block['finished_banners'] = $bannerFinishHandler->getCount($criteriaFinished);

    // TODO Support requests (defaults to 0)
    $block['support_requests'] = 0;
    $clientHandler = xoops_getmodulehandler('banner', 'bannerstats');
    $client = $clientHandler->get($clientId);
    if (is_object($client) && method_exists($client, 'getSupportRequests')) {
        $block['support_requests'] = $client->getSupportRequests();
    }

    // Menu Items
    $block['menu_items'] = [
        ['link' => XOOPS_URL . '/modules/bannerstats/index.php?action=Stats',
         'title' => _MB_BANNERSTATS_MENU_STATS],
        // ['link' => XOOPS_URL . '/modules/bannerstats/index.php?action=ManageBanners',
        //  'title' => _MB_BANNERSTATS_MENU_MANAGE_BANNERS],
        ['link' => XOOPS_URL . '/modules/bannerstats//index.php?action=RequestSupport',
         'title' => _MB_BANNERSTATS_MENU_SUPPORT],
        ['link' => XOOPS_URL . '/modules/bannerstats/index.php?action=Logout',
         'title' => _MB_BANNERSTATS_MENU_LOGOUT]
    ];

    return $block;
}