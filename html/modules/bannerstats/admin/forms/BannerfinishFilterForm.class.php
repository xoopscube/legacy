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

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractFilterForm.class.php';

// Define sort keys for finished banners
define('BANNERFINISH_SORT_KEY_BID', 1);
define('BANNERFINISH_SORT_KEY_CID', 2);
define('BANNERFINISH_SORT_KEY_NAME', 3);
define('BANNERFINISH_SORT_KEY_BANNER_TYPE', 4);
define('BANNERFINISH_SORT_KEY_IMPRESSIONS', 5);
define('BANNERFINISH_SORT_KEY_CLICKS', 6);
define('BANNERFINISH_SORT_KEY_DATESTART', 7);
define('BANNERFINISH_SORT_KEY_DATEEND', 8);
define('BANNERFINISH_SORT_KEY_REASON_FINISHED', 9);
define('BANNERFINISH_SORT_KEY_MAXVALUE', 9);

define('BANNERFINISH_SORT_KEY_DEFAULT', BANNERFINISH_SORT_KEY_REASON_FINISHED);

class Bannerstats_BannerfinishFilterForm extends Bannerstats_AbstractFilterForm
{
    /**
     * Maps sort key constants to their corresponding database field names
     * @var array<int, string>
     */
    public $mSortKeys = [
        BANNERFINISH_SORT_KEY_BID => 'bid',
        BANNERFINISH_SORT_KEY_CID => 'cid',
        BANNERFINISH_SORT_KEY_NAME => 'name',
        BANNERFINISH_SORT_KEY_BANNER_TYPE => 'banner_type',
        BANNERFINISH_SORT_KEY_IMPRESSIONS => 'impressions_made',
        BANNERFINISH_SORT_KEY_CLICKS => 'clicks_made',
        BANNERFINISH_SORT_KEY_DATESTART => 'datestart_original',
        BANNERFINISH_SORT_KEY_DATEEND => 'date_finished',
        BANNERFINISH_SORT_KEY_REASON_FINISHED => 'finish_reason',
    ];

    /**
     * Gets the default sort key for finished banners
     * @return int
     */
    public function getDefaultSortKey(): int
    {
        return BANNERFINISH_SORT_KEY_DEFAULT;
    }

    /**
     * Fetches filter criteria from the request and applies them
     * @return void
     */
    public function fetch(): void
    {
        parent::fetch();

        $cid = (int)xoops_getrequest('cid', 0);
        if ($cid > 0) {
            $this->mNavi->addExtra('cid', (string)$cid);
            $this->_mCriteria->add(new Criteria('cid', $cid));
        }

        $name_search_from_request = xoops_getrequest('name');
        if (isset($_REQUEST['name'])) {
            $name_search_processed = trim((string)$name_search_from_request);

            if ($name_search_processed !== '') {
                $this->mNavi->addExtra('name', $name_search_processed);
                $this->_mCriteria->add(new Criteria('name', '%' . $name_search_processed . '%', 'LIKE'));
            }
        }

        // Filter by Banner Type
        $banner_type_from_request = xoops_getrequest('banner_type');
        if (isset($_REQUEST['banner_type'])) {
            $banner_type_processed = trim((string)$banner_type_from_request);

            if ($banner_type_processed !== '') {
                $allowed_types = ['image', 'video', 'html', 'ad_tag'];
                if (in_array($banner_type_processed, $allowed_types, true)) {
                    $this->mNavi->addExtra('banner_type', $banner_type_processed);
                    $this->_mCriteria->add(new Criteria('banner_type', $banner_type_processed));
                }
            }
        }

        $reason_search_from_request = xoops_getrequest('finish_reason');
        if (isset($_REQUEST['finish_reason'])) {
            $reason_search_processed = trim((string)$reason_search_from_request);
            
            if ($reason_search_processed !== '') {
                $this->mNavi->addExtra('finish_reason', $reason_search_processed);
                $this->_mCriteria->add(new Criteria('finish_reason', '%' . $reason_search_processed . '%', 'LIKE'));
            }
        }

        $campaign_id = (int)xoops_getrequest('campaign_id', 0);
        if ($campaign_id > 0) {
            $this->mNavi->addExtra('campaign_id', (string)$campaign_id);
            $this->_mCriteria->add(new Criteria('campaign_id', $campaign_id));
        }

        // Apply sorting
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}
