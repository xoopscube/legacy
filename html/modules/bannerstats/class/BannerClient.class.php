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

require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/object.php';

if (!class_exists('XoopsObjectGenericHandler')) {
    require_once XOOPS_ROOT_PATH . '/modules/legacy/kernel/handler.php';
}


class Bannerstats_BannerclientObject extends XoopsSimpleObject
{
    /** @var Bannerstats_BannerObject[] Array of associated active banner objects */
    public array $mBanners = [];
    public bool $_mBannersLoadedFlag = false;

    /** @var Bannerstats_BannerfinishObject[] Array of associated finished banner objects */
    public array $mFinishBanners = [];
    public bool $_mFinishBannersLoadedFlag = false;

    /** @var int|null Count of associated active banners */
    public ?int $mBannerCount = null;
    public bool $_mBannerCountLoadedFlag = false;

    /** @var int|null Count of associated finished banners */
    public ?int $mFinishBannerCount = null;
    public bool $_mFinishBannerCountLoadedFlag = false;

    public function __construct()
    {
        parent::__construct();

        // Define variables based on the {prefix}_bannerclient table schema

        $this->initVar('cid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 60);
        $this->initVar('contact', XOBJ_DTYPE_STRING, null, false, 60);
        $this->initVar('email', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('tel', XOBJ_DTYPE_STRING, null, false, 50);
        $this->initVar('address1', XOBJ_DTYPE_STRING, null, false, 191);
        $this->initVar('address2', XOBJ_DTYPE_STRING, null, false, 191);
        $this->initVar('city', XOBJ_DTYPE_STRING, null, false, 100);
        $this->initVar('region', XOBJ_DTYPE_STRING, null, false, 100);
        $this->initVar('postal_code', XOBJ_DTYPE_STRING, null, false, 20);
        $this->initVar('country_code', XOBJ_DTYPE_STRING, null, false, 2);
        $this->initVar('login', XOBJ_DTYPE_STRING, '', true, 25);
        $this->initVar('passwd', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('extrainfo', XOBJ_DTYPE_TEXT, null, false);
        $this->initVar('status', XOBJ_DTYPE_INT, 1, true);
        $this->initVar('date_created', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('last_updated', XOBJ_DTYPE_INT, 0, true);
    }

    /**
     * Loads associated active banners for this client
     * @return void
     */
    public function loadBanner(): void
    {
        if (false === $this->_mBannersLoadedFlag) {
            $handler = xoops_getmodulehandler('banner', 'bannerstats');
            if ($handler instanceof Bannerstats_BannerHandler) {
                $criteria = new CriteriaCompo(new Criteria('cid', $this->get('cid')));
                $this->mBanners = $handler->getObjects($criteria);
            } else {
                 error_log("Bannerstats_BannerclientObject::loadBanner - Failed to get Bannerstats_BannerHandler for client ID: " . $this->get('cid'));
                 $this->mBanners = [];
            }
            $this->_mBannersLoadedFlag = true;
        }
    }

    /**
     * Loads associated finished banners for this client
     * @return void
     */
    public function loadFinishBanner(): void
    {
        if (false === $this->_mFinishBannersLoadedFlag) {
            $handler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
            if ($handler instanceof Bannerstats_BannerfinishHandler) {
                $criteria = new CriteriaCompo(new Criteria('cid', $this->get('cid')));
                $this->mFinishBanners = $handler->getObjects($criteria);
            } else {
                 error_log("Bannerstats_BannerclientObject::loadFinishBanner - Failed to get Bannerstats_BannerfinishHandler for client ID: " . $this->get('cid'));
                 $this->mFinishBanners = [];
            }
            $this->_mFinishBannersLoadedFlag = true;
        }
    }

    /**
     * Loads the count of associated active banners for this client
     * @return void
     */
    public function loadBannerCount(): void
    {
        if (false === $this->_mBannerCountLoadedFlag) {
            $handler = xoops_getmodulehandler('banner', 'bannerstats');
            if ($handler instanceof Bannerstats_BannerHandler) {
                 $criteria = new CriteriaCompo(new Criteria('cid', $this->get('cid')));
                $this->mBannerCount = $handler->getCount($criteria);
            } else {
                 error_log("Bannerstats_BannerclientObject::loadBannerCount - Failed to get Bannerstats_BannerHandler for client ID: " . $this->get('cid'));
                 $this->mBannerCount = 0;
            }
            $this->_mBannerCountLoadedFlag = true;
        }
    }

    /**
     * Loads the count of associated finished banners for this client
     * @return void
     */
    public function loadFinishBannerCount(): void
    {
        if (false === $this->_mFinishBannerCountLoadedFlag) {
            $handler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
            if ($handler instanceof Bannerstats_BannerfinishHandler) {
                $criteria = new CriteriaCompo(new Criteria('cid', $this->get('cid')));
                $this->mFinishBannerCount = $handler->getCount($criteria);
            } else {
                 error_log("Bannerstats_BannerclientObject::loadFinishBannerCount - Failed to get Bannerstats_BannerfinishHandler for client ID: " . $this->get('cid'));
                 $this->mFinishBannerCount = 0;
            }
            $this->_mFinishBannerCountLoadedFlag = true;
        }
    }
}

class Bannerstats_BannerclientHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'bannerclient';
    public $mPrimary = 'cid';
    public $mClass = 'Bannerstats_BannerclientObject';

    // Add constructor if not inheriting directly or if specific logic needed
    public function __construct(&$db)
    {
        parent::__construct($db);
    }
}
