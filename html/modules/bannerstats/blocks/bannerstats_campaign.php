<?php
/**
 * Bannerstats - Module for XCL
 * Shows the campaign-specific banner block.
 * Fetches banner HTML using DelegateManager and passes it to the template.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) exit();

function b_bannerstats_campaign_show($options)
{
    $block = [
        'banner_html'       => '',    // store HTML from DelegateManager
        'banner_found'      => false, // Flag banner HTML
        // Variables for optional debug display in the template
        'show_debug_info'   => false, // $currentUser->isAdmin
        'campaign_id_used'  => null,
        'client_id_used'    => null,
        'banner_id_used'    => null
    ];

    $cid_option = isset($options[0]) ? (int)$options[0] : -1;
    $bid_option = isset($options[1]) ? (int)$options[1] : 0;
    $campaign_id_option = isset($options[2]) ? (int)$options[2] : 0;

    // Populate debug info based on options if the template will use it
    if ($campaign_id_option > 0) {
        $block['campaign_id_used'] = $campaign_id_option;
    }
    if ($cid_option >= 0) { // cid=0 is a valid option
        $block['client_id_used'] = $cid_option;
    }
    if ($bid_option > 0) {
        $block['banner_id_used'] = $bid_option;
    }

    $delegateManagerPath = XOOPS_ROOT_PATH . '/modules/bannerstats/kernel/DelegateManager.class.php';
    if (file_exists($delegateManagerPath)) {
        require_once $delegateManagerPath;
        if (class_exists('Bannerstats_DelegateManager')) {
            $params = [];
            if ($bid_option > 0) {
                $params['bid'] = $bid_option;
            }
            if ($campaign_id_option > 0) {
                $params['campaign_id'] = $campaign_id_option;
            }
            if ($cid_option >= 0) {
                $params['cid'] = $cid_option;
            }

            // Call method that returns an HTML string
            $htmlOutput = Bannerstats_DelegateManager::getBannerHtmlForDisplay($params);

            // Check if banner HTML
            if (!empty($htmlOutput) && trim($htmlOutput) !== '' && strpos($htmlOutput, '<!--') !== 0) {
                $block['banner_html'] = $htmlOutput;
                $block['banner_found'] = true;
            }
            // If no banner HTML is found, display nothing or a minimal message.
        }
    }

    // Determine if debug info should be shown (minimal admin check)
    $root = XCube_Root::getSingleton();
    $currentUser = $root->mContext->mXoopsUser;
    if ($currentUser) {
        $moduleHandler = xoops_gethandler('module');
        $bannerstatsModule = $moduleHandler->getByDirname('bannerstats');
        if ($bannerstatsModule instanceof XoopsModule && $currentUser->isAdmin($bannerstatsModule->get('mid'))) {
            $block['show_debug_info'] = true;
        }
    }

    return $block;
}

function b_bannerstats_campaign_edit($options)
{
    $saved_cid = isset($options[0]) ? (int)$options[0] : -1;
    $saved_bid = isset($options[1]) ? (int)$options[1] : 0;
    $saved_campaign_id = isset($options[2]) ? (int)$options[2] : 0;

    // Client Dropdown
    $client_options_html = '';
    $selected_any_client = ($saved_cid == -1) ? " selected='selected'" : "";
    $client_options_html .= "<option value='-1'{$selected_any_client}>" . _MB_BANNERSTATS_OPT_ANY_CLIENT . "</option>";

    $selected_client_0 = ($saved_cid == 0) ? " selected='selected'" : "";
    $client_options_html .= "<option value='0'{$selected_client_0}>" . _MB_BANNERSTATS_OPT_CLIENT_0 . "</option>";

    // get distinct client IDs present in banners
    $bannerHandlerForClients = xoops_getmodulehandler('banner', 'bannerstats');
    $client_ids_from_banners = [];
    if ($bannerHandlerForClients) {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('cid', 0, '>'));
        $all_banners = $bannerHandlerForClients->getObjects($criteria);
        foreach ($all_banners as $banner_obj) {
            $current_cid = $banner_obj->getVar('cid');
            if (!in_array($current_cid, $client_ids_from_banners)) {
                $client_ids_from_banners[] = $current_cid;
            }
        }
        sort($client_ids_from_banners);

        foreach ($client_ids_from_banners as $cid_from_banner) {
            $selected = ($cid_from_banner == $saved_cid && $saved_cid > 0) ? " selected='selected'" : "";
            $client_options_html .= "<option value='" . $cid_from_banner . "'{$selected}>Client ID: " . htmlspecialchars((string)$cid_from_banner, ENT_QUOTES) . "</option>";
        }
    }

    // Campaign ID Dropdown
    $campaign_options_html = '';
    $selected_any_campaign = ($saved_campaign_id == 0) ? " selected='selected'" : "";
    $campaign_options_html .= "<option value='0'{$selected_any_campaign}>" . (defined('_MB_BANNERSTATS_OPT_ANY_CAMPAIGN') ? _MB_BANNERSTATS_OPT_ANY_CAMPAIGN : '-- Any Campaign --') . "</option>";

    $bannerHandlerForCampaigns = xoops_getmodulehandler('banner', 'bannerstats');
    $campaign_ids = [];

    if ($bannerHandlerForCampaigns) {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('campaign_id', 0, '>'));
        $criteria->setSort('campaign_id');
        $banners_for_campaigns = $bannerHandlerForCampaigns->getObjects($criteria, false);

        foreach ($banners_for_campaigns as $banner_obj_for_campaign) {
            $current_campaign_id = $banner_obj_for_campaign->get('campaign_id');
            if (!in_array($current_campaign_id, $campaign_ids)) {
                $campaign_ids[] = $current_campaign_id;
            }
        }

        foreach ($campaign_ids as $id) {
            $selected = ($id == $saved_campaign_id) ? " selected='selected'" : "";
            $campaign_options_html .= "<option value='{$id}'{$selected}>" . htmlspecialchars((string)$id, ENT_QUOTES) . "</option>";
        }
    }

    $form = "<select name='options[0]'>{$client_options_html}</select> " . _MB_BANNERSTATS_OPT_CID . ": " . _MB_BANNERSTATS_OPT_CID_DESC . "<br />";
    $form .= "<input type='text' name='options[1]' value='{$saved_bid}' size='5' /> " . _MB_BANNERSTATS_OPT_BID . ": " . _MB_BANNERSTATS_OPT_BID_DESC . "<br />";
    $form .= "<select name='options[2]'>{$campaign_options_html}</select> " . _MB_BANNERSTATS_OPT_CAMPAIGN_ID . ": " . _MB_BANNERSTATS_OPT_CAMPAIGN_ID_DESC . "<br />";

    return $form;
}

