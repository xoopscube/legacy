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
require_once __DIR__ . '/BannerClient.class.php';


class Bannerstats_BannerObject extends XoopsSimpleObject
{
    /** @var Bannerstats_BannerclientObject|null Associated client object */
    public $mClient = null;
    public $_mClientLoadedFlag = false;

    public function __construct()
    {
        parent::__construct();

        // Define variables based on the {prefix}_banner table schema

        $this->initVar('bid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('cid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('campaign_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('banner_type', XOBJ_DTYPE_STRING, 'image', true, 10);
        $this->initVar('imageurl', XOBJ_DTYPE_STRING, null, false, 255);
        $this->initVar('clickurl', XOBJ_DTYPE_STRING, null, false, 255);
        $this->initVar('htmlcode', XOBJ_DTYPE_TEXT, null, false);
        $this->initVar('width', XOBJ_DTYPE_INT, null, false);
        $this->initVar('height', XOBJ_DTYPE_INT, null, false);
        $this->initVar('imptotal', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('impmade', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('clicks', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('start_date', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('end_date', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('last_impression_time', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('last_click_time', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('timezone', XOBJ_DTYPE_STRING, null, false, 50);
        $this->initVar('date_created', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('status', XOBJ_DTYPE_INT, 1, true);
        $this->initVar('weight', XOBJ_DTYPE_INT, 10, true);
        $this->initVar('low_impression_alert_sent', XOBJ_DTYPE_INT, 0, true);
    }

    /**
     * Returns the primary key field name for this object.
     * @return string
     */
    public function getPrimaryKey()
    {
        return 'bid';
    }

    /**
     * Loads the associated banner client object
     * @return void
     */
    public function loadBannerclient(): void
    {
        if (false === $this->_mClientLoadedFlag) {
            $handler = xoops_getmodulehandler('bannerclient', 'bannerstats');
            if ($handler) {
                $this->mClient = $handler->get($this->get('cid'));
            }
            $this->_mClientLoadedFlag = true;
        }
    }

    /**
     * Loads the associated client object
     * @return Bannerstats_BannerclientObject|null
     */
    public function loadClient(): ?Bannerstats_BannerclientObject
    {
        // Check if client object is loaded or cid is invalid
        if (null === $this->mClient && $this->get('cid') > 0) {
            $clientHandler = xoops_getmodulehandler('bannerclient', 'bannerstats');
            if ($clientHandler) {
                $client = $clientHandler->get($this->get('cid'));
                if ($client instanceof Bannerstats_BannerclientObject) {
                    $this->mClient = $client;
                } else {
                    // Client not found or null
                    $this->mClient = null;
                }
            } else {
                $this->mClient = null;
            }
        } elseif ($this->get('cid') == 0) {
            if (!($this->mClient instanceof Bannerstats_BannerclientObject)) {
                 $this->mClient = null;
            }
        }
        return $this->mClient;
    }

    /**
     * Increments the impression count for this banner
     * @return void
     */
    public function incrementImpressions(): void
    {
        $this->setVar('impmade', $this->getVar('impmade') + 1);
    }

    /**
     * Increments the click count for this banner
     * @return void
     */
    public function incrementClicks(): void
    {
        $this->setVar('clicks', $this->getVar('clicks') + 1);
    }

    /**
     * Checks if the banner type is HTML-like (html, ad_tag, video)
     * @return bool
     */
    public function isHtmlLike(): bool
    {
        $type = $this->get('banner_type');
        return in_array($type, ['html', 'ad_tag', 'video'], true);
    }

    /**
     * Checks if the banner type is 'image'
     * @return bool
     */
    public function isImage(): bool
    {
        return $this->get('banner_type') === 'image';
    }

}


