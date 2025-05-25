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

// Define constants for banner finish reasons
define('BANNER_FINISH_REASON_MANUAL', 'Manually Finished');
define('BANNER_FINISH_REASON_IMPRESSIONS', 'Impressions Reached');
define('BANNER_FINISH_REASON_ADMIN', 'Admin Terminated');
define('BANNER_FINISH_REASON_CLIENT', 'Client Terminated');
define('BANNER_FINISH_REASON_DATE_EXPIRED', 'Date Expired');
define('BANNER_FINISH_REASON_OTHER', 'Other Reason');

class Bannerstats_BannerfinishHandler extends XoopsObjectGenericHandler
{
    public $mTable = 'bannerfinish';
    public $mPrimary = 'bid';
    public $mClass = 'Bannerstats_BannerfinishObject';

    public function __construct(&$db)
    {
        parent::__construct($db);
    }

     /**
     * Gets all available finish reason constants
     * @return array Array of finish reason constants
     */
    public function getFinishReasons(): array
    {
        return [
            BANNER_FINISH_REASON_MANUAL => _BANNERSTATS_FINISH_MANUAL,
            BANNER_FINISH_REASON_IMPRESSIONS => _BANNERSTATS_FINISH_IMPRESSIONS,
            BANNER_FINISH_REASON_ADMIN => _BANNERSTATS_FINISH_ADMIN,
            BANNER_FINISH_REASON_CLIENT => _BANNERSTATS_FINISH_CLIENT,
            BANNER_FINISH_REASON_DATE_EXPIRED => _BANNERSTATS_FINISH_DATE_EXPIRED,
            BANNER_FINISH_REASON_OTHER => _BANNERSTATS_FINISH_OTHER
        ];
    }
    
    /**
     * Gets banners finished by a specific reason
     * @param string $reason The finish reason to filter by
     * @param int $limit Maximum number of records to return
     * @param int $start Starting position
     * @return array Array of Bannerstats_BannerfinishObject objects
     */
    public function getByFinishReason(string $reason, int $limit = 0, int $start = 0): array
    {
        $criteria = new CriteriaCompo(new Criteria('finish_reason', $reason));
        $criteria->setSort('date_finished');
        $criteria->setOrder('DESC');
        
        if ($limit > 0) {
            $criteria->setLimit($limit);
            $criteria->setStart($start);
        }
        
        return $this->getObjects($criteria);
    }
}


class Bannerstats_BannerfinishObject extends XoopsSimpleObject
{
    protected $_mClientLoadedFlag = false; // Initialize the flag property
    public $mClient = null; // Initialize the client property

    public function __construct()
    {
        parent::__construct(); // Essential for initialization

        // These must match the {prefix}_bannerfinish table schema from mysql.sql
        $this->initVar('bid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('cid', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('campaign_id', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('name', XOBJ_DTYPE_STRING, '', true, 255);
        $this->initVar('banner_type', XOBJ_DTYPE_STRING, 'image', true, 10);
        $this->initVar('imageurl', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('clickurl', XOBJ_DTYPE_STRING, '', false, 255);
        $this->initVar('htmlcode', XOBJ_DTYPE_TEXT, '', false);
        $this->initVar('width', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('height', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('imptotal_allocated', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('impressions_made', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('clicks_made', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('datestart_original', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('dateend_original', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('timezone_original', XOBJ_DTYPE_STRING, '', false, 50);
        $this->initVar('date_created_original', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('date_finished', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('finish_reason', XOBJ_DTYPE_STRING, 0, false, 255);
        $this->initVar('finished_by_uid', XOBJ_DTYPE_INT, 0, false);
    }

    // loadBannerclient() if needed
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
     * Gets a human-readable description of the finish reason
     * @return string The formatted finish reason
     */
    public function getFinishReasonText(): string
    {
        $reason = $this->get('finish_reason');
        
        // Add additional context if available
        if ($reason === BANNER_FINISH_REASON_IMPRESSIONS) {
            return sprintf(
                _BANNERSTATS_FINISH_IMPRESSIONS_DETAIL,
                $this->get('impressions_made'),
                $this->get('imptotal_allocated')
            );
        } elseif ($reason === BANNER_FINISH_REASON_ADMIN || $reason === BANNER_FINISH_REASON_CLIENT) {
            $uid = $this->get('finished_by_uid');
            if ($uid > 0) {
                $userHandler = xoops_gethandler('user');
                $user = $userHandler->get($uid);
                if (is_object($user)) {
                    return sprintf(
                        _BANNERSTATS_FINISH_BY_USER,
                        $reason,
                        $user->getVar('uname')
                    );
                }
            }
        } elseif ($reason === BANNER_FINISH_REASON_DATE_EXPIRED) {
            $endDate = $this->get('dateend_original');
            if ($endDate > 0) {
                return sprintf(
                    _BANNERSTATS_FINISH_DATE_EXPIRED,
                    formatTimestamp($endDate)
                );
            }
        }
        
        return $reason;
    }

}
 