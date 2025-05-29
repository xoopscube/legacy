<?php
/**
 * Bannerstats - Module for XCL
 * Block file for displaying a single banner and handling AJAX requests for its edit form.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

// AJAX endpoint
if (isset($_GET['bs_block_ajax_action']) && !defined('XOOPS_ROOT_PATH')) {

    $mainfilePath = dirname(__FILE__, 4) . '/mainfile.php';
                                                            
    if (file_exists($mainfilePath)) {
        include_once $mainfilePath;
    } else {
        // If mainfile.php cannot be loaded, AJAX fails
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Server bootstrap error. AJAX handler cannot proceed.']);
        exit();
    }
}

require_once XOOPS_MODULE_PATH . '/bannerstats/class/Banner.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/Banner.class.php';
require_once XOOPS_ROOT_PATH . '/modules/bannerstats/kernel/DelegateManager.class.php';

// AJAX Request Handler
if (isset($_GET['bs_block_ajax_action']) && $_GET['bs_block_ajax_action'] === 'get_banners_by_type') {
 
    global $xoopsUser;
    $moduleHandler = xoops_gethandler('module');
    $bannerstatsModuleObject = $moduleHandler->getByDirname('bannerstats');

    if (!is_object($xoopsUser) || !$bannerstatsModuleObject || !$xoopsUser->isAdmin($bannerstatsModuleObject->getVar('mid'))) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Permission denied.']);
        exit();
    }

    $banner_type = isset($_GET['banner_type']) ? trim((string)$_GET['banner_type']) : '';
    $output_banners = [];

    if ($banner_type !== '' && class_exists('Bannerstats_BannerHandler')) {
        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        if ($bannerHandler instanceof Bannerstats_BannerHandler) {
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('status', 1));
            $criteria->add(new Criteria('banner_type', $banner_type));
            $criteria->setSort('name');
            $criteria->setOrder('ASC');

            $banners = $bannerHandler->getObjects($criteria);

            if (is_array($banners)) {
                foreach ($banners as $banner) {
                    if ($banner instanceof Bannerstats_BannerObject) {
                        $output_banners[] = [
                            'bid' => $banner->getVar('bid'),
                            'name' => $banner->getVar('name')
                        ];
                    }
                }
            }
        } else {
            // error_log("BannerStats AJAX: Could not get 'banner' handler for module 'bannerstats'.");
        }
    }

    header('Content-Type: application/json');
    ob_clean(); 
    echo json_encode($output_banners);
    exit();
}

/**
 * Show function for the single banner block.
 * Displays a specific banner based on the BID selected in block options.
 *
 * @param array $options Block options:
 *                       $options[0] = banner_type (string, selected in edit form)
 *                       $options[1] = bid (int, specific banner ID)
 * @return array Associative array with 'banner_html' and 'banner_found' keys.
 */
function b_bannerstats_banner_show($options)
{
    $block = [
        'banner_html'  => '',    // To store the HTML from DelegateManager
        'banner_found' => false, // Flag to indicate if banner HTML was retrieved
    ];

    // $options[1] is the selected Banner ID (bid)
    $bid_option = isset($options[1]) ? (int)$options[1] : 0;

    // Only proceed if a specific Banner ID is selected
    if ($bid_option > 0) {
        if (class_exists('Bannerstats_DelegateManager')) {
            $params = ['bid' => $bid_option];

            // Call method that returns an HTML string
            $htmlOutput = Bannerstats_DelegateManager::getBannerHtmlForDisplay($params);

            // Check banner HTML
            if (!empty($htmlOutput) && trim($htmlOutput) !== '' && strpos($htmlOutput, '<!--') !== 0) {
                $block['banner_html'] = $htmlOutput;
                $block['banner_found'] = true;
            }
        }
    }

    return $block;
}

/**
 * Edit function for the single banner block
 */
function b_bannerstats_banner_edit($options)
{
    $saved_banner_type = isset($options[0]) ? trim((string)$options[0]) : '';
    $saved_bid = isset($options[1]) ? (int)$options[1] : 0;

    $bannerTypes = [
        ''      => '-- ' . _MB_BANNERSTATS_OPT_BANNER_TYPE_SELECT . ' --',
        'image' => defined('_MB_BANNERSTATS_BTYPE_IMAGE') ? _MB_BANNERSTATS_BTYPE_IMAGE : 'Image',
        'html'  => defined('_MB_BANNERSTATS_BTYPE_HTML') ? _MB_BANNERSTATS_BTYPE_HTML : 'HTML',
        'ad_tag'=> defined('_MB_BANNERSTATS_BTYPE_ADTAG') ? _MB_BANNERSTATS_BTYPE_ADTAG : 'Ad Tag',
        'video' => defined('_MB_BANNERSTATS_BTYPE_VIDEO') ? _MB_BANNERSTATS_BTYPE_VIDEO : 'Video'
    ];
    $bannerTypeOptions = '';
    foreach ($bannerTypes as $type_value => $type_label) {
         $selected = ($saved_banner_type === $type_value) ? " selected='selected'" : "";
         $bannerTypeOptions .= "<option value='" . htmlspecialchars($type_value, ENT_QUOTES) . "'{$selected}>"
                                . htmlspecialchars($type_label, ENT_QUOTES) . "</option>";
    }
    $form = "<select name='options[0]' id='banner_type_selector_block_edit'>"
         . $bannerTypeOptions . "</select> " . _MB_BANNERSTATS_OPT_BANNER_TYPE_LABEL . "<br />";

    $bannerOptionsHTML = "<option value='0'>-- " . _MB_BANNERSTATS_OPT_BANNER_ID_SELECT . " --</option>";
    if (class_exists('Bannerstats_BannerHandler')) {
        $bannerHandler = xoops_getmodulehandler('banner', 'bannerstats');
        if ($bannerHandler instanceof Bannerstats_BannerHandler) { // Check if handler was retrieved
            $criteria = new CriteriaCompo();
            $criteria->add(new Criteria('status', 1));
            if (array_key_exists($saved_banner_type, $bannerTypes) && $saved_banner_type !== '') {
                $criteria->add(new Criteria('banner_type', $saved_banner_type));
            }
            $activeBanners = $bannerHandler->getObjects($criteria);
            if (is_array($activeBanners) && count($activeBanners) > 0) {
                foreach ($activeBanners as $banner) {
                    if ($banner instanceof Bannerstats_BannerObject) {
                        $bid = $banner->getVar('bid');
                        $name = $banner->getVar('name');
                        $selected = ($bid == $saved_bid && $saved_bid != 0) ? " selected='selected'" : "";
                        $bannerOptionsHTML .= "<option value='" . htmlspecialchars($bid, ENT_QUOTES) . "'{$selected}>"
                                         . htmlspecialchars($name, ENT_QUOTES) . " (ID: " . $bid . ")</option>";
                    }
                }
            } elseif (array_key_exists($saved_banner_type, $bannerTypes) && $saved_banner_type !== '') {
                 $no_banners_msg = _MB_BANNERSTATS_NO_BANNERS_OF_SELECTED_TYPE . "No active banners of the selected type found.";
                 $bannerOptionsHTML .= "<option value='0' disabled='disabled'>" . htmlspecialchars($no_banners_msg, ENT_QUOTES) . "</option>";
            }
        }
    }
    $form .= "<select name='options[1]' id='banner_id_selector_block_edit'>" . $bannerOptionsHTML . "</select> " . _MB_BANNERSTATS_OPT_BANNER_ID_LABEL . "<br />";

    $ajaxUrl = XOOPS_URL . '/modules/bannerstats/blocks/bannerstats_banner.php';

    $form .= "
    <script type='text/javascript'>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelector = document.getElementById('banner_type_selector_block_edit');
        const bannerSelector = document.getElementById('banner_id_selector_block_edit');

        if (typeSelector && bannerSelector) {
            typeSelector.addEventListener('change', function() {
                const selectedType = this.value;
                const currentSavedBid = " . (int)$saved_bid . ";
                bannerSelector.innerHTML = '<option value=\"0\">-- Loading... --</option>';

                if (selectedType === '') {
                    bannerSelector.innerHTML = '<option value=\"0\">-- " . (defined('_MB_BANNERSTATS_OPT_BANNER_ID_SELECT') ? addslashes(_MB_BANNERSTATS_OPT_BANNER_ID_SELECT) : 'Select Banner ID') . " --</option>';
                    return;
                }

                const xhr = new XMLHttpRequest();
                xhr.open('GET', '{$ajaxUrl}?bs_block_ajax_action=get_banners_by_type&banner_type=' + encodeURIComponent(selectedType) + '&saved_bid=' + currentSavedBid, true);
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        console.log('Raw AJAX Response:', xhr.responseText); // For debugging
                        try {
                            const banners = JSON.parse(xhr.responseText);
                            let optionsHtml = '<option value=\"0\">-- " . (defined('_MB_BANNERSTATS_OPT_BANNER_ID_SELECT') ? addslashes(_MB_BANNERSTATS_OPT_BANNER_ID_SELECT) : 'Select Banner ID') . " --</option>';
                            if (banners.error) {
                                console.error('AJAX Server Error:', banners.error);
                                optionsHtml += '<option value=\"0\" disabled=\"disabled\">Error: ' + banners.error + '</option>';
                            } else if (banners.length > 0) {
                                banners.forEach(function(banner) {
                                    const selectedAttr = (banner.bid == currentSavedBid) ? ' selected=\"selected\"' : '';
                                    optionsHtml += '<option value=\"' + banner.bid + '\"' + selectedAttr + '>' + banner.name + ' (ID: ' + banner.bid + ')</option>';
                                });
                            } else {
                                optionsHtml += '<option value=\"0\" disabled=\"disabled\">" . (defined('_MB_BANNERSTATS_NO_BANNERS_OF_TYPE') ? addslashes(_MB_BANNERSTATS_NO_BANNERS_OF_TYPE) : 'No banners of this type') . "</option>';
                            }
                            bannerSelector.innerHTML = optionsHtml;
                        } catch (e) {
                            bannerSelector.innerHTML = '<option value=\"0\">Error parsing server data</option>';
                            // This is where your current error is being caught
                            console.error('Error parsing JSON:', e, 'Response was:', xhr.responseText);
                        }
                    } else {
                        bannerSelector.innerHTML = '<option value=\"0\">Error fetching banners from server</option>';
                        console.error('Error fetching banners. Status:', xhr.status, 'Response:', xhr.responseText);
                    }
                };
                xhr.onerror = function() {
                    bannerSelector.innerHTML = '<option value=\"0\">Network request failed</option>';
                    console.error('AJAX network request failed.');
                };
                xhr.send();
            });
        }
    });
    </script>";

    return $form;
}
?>
