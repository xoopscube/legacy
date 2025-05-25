<?php
/**
 * Shows the campaign-specific banner block.
 * Displays banners based on campaign_id, optionally filtered by cid or specific bid.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

function b_bannerstats_campaign_show($options)
{
    $block = [];
    // $options[0] = cid (0 for any in campaign, -1 for truly any if campaign is also 0)
    // $options[1] = bid
    // $options[2] = campaign_id
    
    // Interpret block options:
    // cid: 0 means "banners with cid=0", >0 means specific client, -1 (or not set) means "any client"
    $cid_option = isset($options[0]) ? (int)$options[0] : -1; // Use -1 to signify "any client" if not set
    $bid_option = isset($options[1]) ? (int)$options[1] : 0;
    $campaign_id_option = isset($options[2]) ? (int)$options[2] : 0;

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

            // Pass cid to DelegateManager:
            // - If block option cid is >0, pass it.
            // - If block option cid is 0, pass 0 (for client 0 banners).
            // - If block option cid is -1 (any client), DelegateManager's getBannerHtmlForDisplay will set its internal $cid to null.
            if ($cid_option >= 0) { // 0 or specific client
                $params['cid'] = $cid_option;
            }
            // If $cid_option is -1 (default "any"), 'cid' is not added to $params,
            // so DelegateManager::getBannerHtmlForDisplay will use its default (null for "any client").
            
            $block['content'] = Bannerstats_DelegateManager::getBannerHtmlForDisplay($params);
        } else {
            $block['content'] = '<!-- Bannerstats Error: DelegateManager class not found -->';
        }
    } else {
        $block['content'] = '<!-- Bannerstats Error: DelegateManager file not found -->';
    }
    return $block;
}

/**
 * Edit function for the campaign banner block.
 */
function b_bannerstats_campaign_edit($options)
{
    $cid_val = isset($options[0]) ? (int)$options[0] : -1; // Default to -1 for "Any Client"
    $bid_val = isset($options[1]) ? (int)$options[1] : 0;
    $campaign_id_val = isset($options[2]) ? (int)$options[2] : 0;

    // --- Client Dropdown ---
    $client_options_html = '<option value="-1">' . _MB_BANNERSTATS_OPT_ANY_CLIENT . '</option>'; // Option for "Any Client"
    $client_options_html .= '<option value="0">' . _MB_BANNERSTATS_OPT_CLIENT_0 . '</option>';   // Option for "Client 0 (System/Global)"
    
    $clientHandler = xoops_getmodulehandler('bannerclient', 'bannerstats');
    if ($clientHandler) {
        $clients = $clientHandler->getObjects(); // Consider adding criteria if list is too long
        foreach ($clients as $client) {
            $selected = ($client->getVar('cid') == $cid_val && $cid_val > 0) ? " selected='selected'" : ""; // Only select if > 0
            $client_options_html .= "<option value='" . $client->getVar('cid') . "'{$selected}>" . htmlspecialchars($client->getVar('name'), ENT_QUOTES) . " (ID: " . $client->getVar('cid') . ")</option>";
        }
    }
    if ($cid_val == 0 && $cid_val !== -1) $client_options_html = str_replace('<option value="0">', '<option value="0" selected="selected">', $client_options_html);
    if ($cid_val == -1) $client_options_html = str_replace('<option value="-1">', '<option value="-1" selected="selected">', $client_options_html);


    // --- Campaign ID Input (or Dropdown if you have a campaign management system) ---
    // For now, a text input for campaign_id.
    // If you had a campaign handler:
    // $campaign_options_html = '<option value="0">' . _MB_BANNERSTATS_OPT_ANY_CAMPAIGN . '</option>';
    // $campaignHandler = xoops_getmodulehandler('campaign', 'bannerstats'); // Hypothetical
    // if ($campaignHandler) { ... populate ... }

    $form = _MB_BANNERSTATS_OPT_CAMPAIGN_ID . ": <input type='text' name='options[2]' value='{$campaign_id_val}' size='5' /> <small>" . _MB_BANNERSTATS_OPT_CAMPAIGN_ID_DESC . "</small><br />";
    $form .= _MB_BANNERSTATS_OPT_CID . ": <select name='options[0]'>{$client_options_html}</select> <small>" . _MB_BANNERSTATS_OPT_CID_DESC . "</small><br />";
    $form .= _MB_BANNERSTATS_OPT_BID . ": <input type='text' name='options[1]' value='{$bid_val}' size='5' /> <small>" . _MB_BANNERSTATS_OPT_BID_DESC . "</small><br />";
    
    return $form;
}
