<?php
/**
 * @package bannerstats
 * @version $Id: BannerfinishFilterForm.class.php,v 1.1 2007/05/15 02:34:40 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractFilterForm.class.php';

define('BANNERFINISH_SORT_KEY_BID', 1);
define('BANNERFINISH_SORT_KEY_CID', 2);
define('BANNERFINISH_SORT_KEY_IMPRESSIONS', 3);
define('BANNERFINISH_SORT_KEY_CLICKS', 4);
define('BANNERFINISH_SORT_KEY_DATESTART', 5);
define('BANNERFINISH_SORT_KEY_DATEEND', 6);
define('BANNERFINISH_SORT_KEY_MAXVALUE', 6);

define('BANNERFINISH_SORT_KEY_DEFAULT', BANNERFINISH_SORT_KEY_BID);

class Bannerstats_BannerfinishFilterForm extends Bannerstats_AbstractFilterForm
{
    public $mSortKeys = [
        BANNERFINISH_SORT_KEY_BID => 'bid',
        BANNERFINISH_SORT_KEY_CID => 'cid',
        BANNERFINISH_SORT_KEY_IMPRESSIONS => 'impressions',
        BANNERFINISH_SORT_KEY_CLICKS => 'clicks',
        BANNERFINISH_SORT_KEY_DATESTART => 'datestart',
        BANNERFINISH_SORT_KEY_DATEEND => 'dateend'
    ];

    public function getDefaultSortKey()
    {
        return BANNERFINISH_SORT_KEY_DEFAULT;
    }
    
    public function fetch()
    {
        parent::fetch();
    
        if (isset($_REQUEST['cid'])) {
            $this->mNavi->addExtra('cid', xoops_getrequest('cid'));
            $this->_mCriteria->add(new Criteria('cid', xoops_getrequest('cid')));
        }
        
        $this->_mCriteria->addSort($this->getSort(), $this->getOrder());
    }
}
