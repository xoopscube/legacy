<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_BannerObject extends XoopsSimpleObject
{
    public $mClient = null;
    public $_mClientLoadedFlag = false;

    public function __construct()
    {
        static $initVars;
        if (isset($initVars)) {
            $this->mVars = $initVars;
            return;
        }
        $this->initVar('bid', XOBJ_DTYPE_INT, '', false);
        $this->initVar('cid', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('imptotal', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('impmade', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('clicks', XOBJ_DTYPE_INT, '0', true);
        $this->initVar('imageurl', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('clickurl', XOBJ_DTYPE_STRING, '', true, 191);
        $this->initVar('date', XOBJ_DTYPE_INT, time(), true);
        $this->initVar('htmlbanner', XOBJ_DTYPE_BOOL, '0', true);
        $this->initVar('htmlcode', XOBJ_DTYPE_TEXT, '', true);
        $initVars = $this->mVars;
    }

    public function loadBannerclient()
    {
        if (false == $this->_mClientLoadedFlag) {
            $handler =& xoops_getmodulehandler('bannerclient', 'bannerstats');
            $this->mClient =& $handler->get($this->get('cid'));
            $this->_mClientLoadedFlag = true;
        }
    }
}

// Corrected class name from LegacyRenderBannerHandler to Bannerstats_BannerHandler
class Bannerstats_BannerHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'banner';
    public $mPrimary = 'bid';
    // Corrected class name reference
    public $mClass = 'Bannerstats_BannerObject'; 
    
    /**
     * Finish a banner by moving it to the bannerfinish table
     * 
     * @param Bannerstats_BannerObject $banner The banner object to finish
     * @return Bannerstats_BannerfinishObject|false The new bannerfinish object or false on failure
     */
    public function finishBanner(Bannerstats_BannerObject $banner) // Type hint with corrected object name
    {
        // Ensure this uses the 'bannerstats' module for the handler
        $bannerfinishHandler = xoops_getmodulehandler('bannerfinish', 'bannerstats');
        if (!$bannerfinishHandler) {
            error_log("Bannerstats_BannerHandler::finishBanner - Failed to get bannerfinish handler.");
            return false;
        }
        
        $bannerfinish = $bannerfinishHandler->create();
        
        // Copy basic fields
        $bannerfinish->set('bid', $banner->get('bid'));
        $bannerfinish->set('cid', $banner->get('cid'));
        $bannerfinish->set('impressions', $banner->get('impmade'));
        $bannerfinish->set('clicks', $banner->get('clicks'));
        $bannerfinish->set('datestart', $banner->get('date'));
        $bannerfinish->set('dateend', time());
        
        // Copy content fields
        $bannerfinish->set('imageurl', $banner->get('imageurl'));
        $bannerfinish->set('clickurl', $banner->get('clickurl'));
        $bannerfinish->set('htmlbanner', $banner->get('htmlbanner'));
        $bannerfinish->set('htmlcode', $banner->get('htmlcode'));
        
        // Insert and return
        if ($bannerfinishHandler->insert($bannerfinish)) {
            // After successfully inserting into bannerfinish, delete from active banners
            if ($this->delete($banner, true)) { // true to force delete
                 return $bannerfinish;
            } else {
                error_log("Bannerstats_BannerHandler::finishBanner - CRITICAL: Inserted to bannerfinish but FAILED to delete original banner (bid: " . $banner->get('bid') . ")");
                return $bannerfinish; // Return the finished object anyway, but log the error.
            }
        } else {
            error_log("Bannerstats_BannerHandler::finishBanner - Failed to insert bannerfinish record for bid: " . $banner->get('bid'));
        }
        
        return false;
    }

    // You should also move/implement getRandomActiveBanner, countImpression, countClick here
    // if they were previously in LegacyRenderBannerHandler or BannerDisplayHelper
    // and are meant to be part of this handler.

    public function getRandomActiveBanner()
    {
        $criteria = new CriteriaCompo();
        $criteria->add(new Criteria('impmade', 'imptotal', '<'));
        $criteria->add(new Criteria('imptotal', 0, '>'));
        // Optionally add date checks

        $count = $this->getCount($criteria);
        if ($count <= 0) {
            return false;
        }
        $offset = 0;
        if ($count > 1) {
            try {
                $offset = random_int(0, $count - 1);
            } catch (Exception $e) {
                $offset = mt_rand(0, $count - 1);
            }
        }
        
        $criteria->setStart($offset);
        $criteria->setLimit(1);
        $banners = $this->getObjects($criteria);
        return $banners ? $banners[0] : false;
    }

    public function countImpression(int $bid): bool
    {
        $banner = $this->get($bid);
        if ($banner instanceof Bannerstats_BannerObject) {
            $banner->setVar('impmade', $banner->getVar('impmade') + 1);
            $result = $this->insert($banner, true);

            if ($result && $banner->getVar('imptotal') > 0 && $banner->getVar('impmade') >= $banner->getVar('imptotal')) {
                $this->finishBanner($banner);
            }
            return (bool)$result;
        }
        return false;
    }

    public function countClick(int $bid): bool
    {
        $banner = $this->get($bid);
        if ($banner instanceof Bannerstats_BannerObject) {
            $banner->setVar('clicks', $banner->getVar('clicks') + 1);
            return $this->insert($banner, true);
        }
        return false;
    }
}
