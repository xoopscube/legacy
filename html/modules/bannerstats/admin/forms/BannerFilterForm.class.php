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

// Define sort keys for active banners
define('BANNER_SORT_KEY_BID', 1);
define('BANNER_SORT_KEY_CID', 2);
define('BANNER_SORT_KEY_NAME', 3);
define('BANNER_SORT_KEY_BANNER_TYPE', 4);
define('BANNER_SORT_KEY_IMPTOTAL', 5);
define('BANNER_SORT_KEY_IMPMADE', 6);
define('BANNER_SORT_KEY_CLICKS', 7);
define('BANNER_SORT_KEY_DATE_CREATED', 8);
define('BANNER_SORT_KEY_START_DATE', 9);
define('BANNER_SORT_KEY_END_DATE', 10);
define('BANNER_SORT_KEY_STATUS', 11);
define('BANNER_SORT_KEY_WEIGHT', 12);
define('BANNER_SORT_KEY_MAXVALUE', 12);

define('BANNER_SORT_KEY_DEFAULT', BANNER_SORT_KEY_DATE_CREATED);

class Bannerstats_BannerFilterForm extends Bannerstats_AbstractFilterForm
{
    /**
     * Maps sort key constants to their corresponding database field names
     * @var array<int, string>
     */
    public $mSortKeys = [
        BANNER_SORT_KEY_BID => 'bid',
        BANNER_SORT_KEY_CID => 'cid',
        BANNER_SORT_KEY_NAME => 'name',
        BANNER_SORT_KEY_BANNER_TYPE => 'banner_type',
        BANNER_SORT_KEY_IMPTOTAL => 'imptotal',
        BANNER_SORT_KEY_IMPMADE => 'impmade',
        BANNER_SORT_KEY_CLICKS => 'clicks',
        BANNER_SORT_KEY_DATE_CREATED => 'date_created',
        BANNER_SORT_KEY_START_DATE => 'start_date',
        BANNER_SORT_KEY_END_DATE => 'end_date',
        BANNER_SORT_KEY_STATUS => 'status',
        BANNER_SORT_KEY_WEIGHT => 'weight',
    ];

    /**
     * Gets the default sort key for active banners
     * @return int
     */
    public function getDefaultSortKey(): int
    {
        return BANNER_SORT_KEY_DEFAULT;
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

        if (isset($_REQUEST['status']) && is_numeric($_REQUEST['status'])) {
            $status = (int)xoops_getrequest('status');
            if ($status === 0 || $status === 1) {
                $this->mNavi->addExtra('status', (string)$status);
                $this->_mCriteria->add(new Criteria('status', $status));
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
