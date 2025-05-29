<?php
/**
 * Bannerstats - Module for XCL
 * Gets active banners for a specific client
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

class BannerStatsManager
{
    private $db;
    private string $moduleDirname;

    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
        $this->moduleDirname = basename(dirname(dirname(__FILE__)));
    }

    /**
     * Gets active banners for a specific client
     *
     * @param int $cid Client ID.
     * @return array Array of banner data arrays
     */
    public function getActiveBanners(int $cid): array
    {
        $banners = [];
        // Select banner_type and htmlcode instead of htmlbanner
        $sql = sprintf(
            "SELECT bid, cid, imptotal, impmade, clicks, imageurl, clickurl, banner_type, htmlcode, date_created, name
            FROM %s
            WHERE cid = %d AND impmade < imptotal AND imptotal > 0
            ORDER BY bid",
            $this->db->prefix('banner'),
            $cid
        );
        $result = $this->db->query($sql);
        if ($result) {
            while ($row = $this->db->fetchArray($result)) {
                $banners[] = $row;
            }
        }
        return $banners;
    }

    /**
     * Gets finished banners for a specific client
     *
     * @param int $cid Client ID.
     * @return array Array of finished banner data arrays
     */
    public function getFinishedBanners(int $cid): array
    {
        $banners = [];
        // Select all necessary fields directly from bannerfinish table
        // No need to join the active banner table anymore
        $sql = sprintf(
            "SELECT bid, cid, impressions_made, clicks_made, datestart_original, date_finished, imageurl, clickurl, banner_type, htmlcode, name, finish_reason
             FROM %s
             WHERE cid = %d
             ORDER BY date_finished DESC, bid",
            $this->db->prefix('bannerfinish'),
            $cid
        );
        $result = $this->db->query($sql);
        if ($result) {
            while ($row = $this->db->fetchArray($result)) {
                $banners[] = $row;
            }
        }
        return $banners;
    }

    /**
     * Gets the email address for a banner client
     *
     * @param int $cid Client ID.
     * @return string|null Client email or null if not found
     */
    public function getBannerClientEmail(int $cid): ?string
    {
        $sql = sprintf(
            "SELECT email FROM %s WHERE cid = %d",
            $this->db->prefix('bannerclient'),
            $cid
        );
        $result = $this->db->query($sql);
        if ($result && $row = $this->db->fetchArray($result)) {
            return $row['email'];
        }
        return null;
    }

    /**
     * Gets details for a specific banner belonging to a client
     *
     * @param int $bid Banner ID
     * @param int $cid Client ID
     * @return array|null Banner details array or null if not found/not owned by client
     */
    public function getBannerDetails(int $bid, int $cid): ?array
    {
        // Select * is fine as long as the object definition and DB table are correct
        $sql = sprintf(
            "SELECT * FROM %s WHERE bid = %d AND cid = %d",
            $this->db->prefix('banner'),
            $bid,
            $cid
        );
        $result = $this->db->query($sql);
        if ($result && $row = $this->db->fetchArray($result)) {
            return $row;
        }
        return null;
    }

    /**
     * Updates the click URL for a banner
     * Only allowed for image banners
     *
     * @param int $bid Banner ID
     * @param int $cid Client ID
     * @param string $newUrl The new click URL.
     * @return bool True on success, false on failure or if not an image banner
     */
    public function updateBannerUrl(int $bid, int $cid, string $newUrl): bool
    {
        $banner = $this->getBannerDetails($bid, $cid);
        // Check if banner exists and is an image type
        if (!$banner || $banner['banner_type'] !== 'image') {
            return false;
        }

        // Update only if the banner type is 'image'
        $sql = sprintf(
            "UPDATE %s SET clickurl = %s WHERE bid = %d AND cid = %d AND banner_type = 'image'",
            $this->db->prefix('banner'),
            $this->db->quoteString($newUrl),
            $bid,
            $cid
        );
        return (bool)$this->db->queryF($sql);
    }
}
