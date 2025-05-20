<?php
// html/modules/bannerstats/class/BannerStatsManager.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class BannerStatsManager
{
    private $db;

    public function __construct()
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();
    }

    public function getActiveBanners(int $cid): array
    {
        $banners = [];
        // Corrected WHERE clause:
        // An active banner now always has imptotal > 0 (due to admin enforcement and DB default)
        // and impmade is less than imptotal.
        $sql = sprintf(
            "SELECT bid, cid, imptotal, impmade, clicks, imageurl, clickurl, htmlbanner, htmlcode, date
            FROM %s
            WHERE cid = %d AND impmade < imptotal AND imptotal > 0 
            ORDER BY bid", // imptotal > 0 is an extra safeguard here
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

    // ... (getFinishedBanners, getBannerClientEmail, getBannerDetails, updateBannerUrl remain largely the same) ...
    // Ensure getFinishedBanners joins correctly if you need htmlbanner flag there.
    public function getFinishedBanners(int $cid): array
    {
        $banners = [];
        $sql = sprintf(
            "SELECT bf.bid, bf.cid, bf.impressions, bf.clicks, bf.datestart, bf.dateend, b.imageurl, b.clickurl, b.htmlbanner 
             FROM %s bf
             LEFT JOIN %s b ON bf.bid = b.bid 
             WHERE bf.cid = %d 
             ORDER BY bf.dateend DESC, bf.bid", // Removed bf.cid = b.cid from JOIN as bid should be unique PK
            $this->db->prefix('bannerfinish'),
            $this->db->prefix('banner'), // Original banner table to get htmlbanner flag
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
    
    public function getBannerClientEmail(int $cid): ?string // No change needed
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

    public function getBannerDetails(int $bid, int $cid): ?array // No change needed
    {
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

    public function updateBannerUrl(int $bid, int $cid, string $newUrl): bool // No change needed
    {
        $banner = $this->getBannerDetails($bid, $cid);
        if (!$banner || !empty($banner['htmlbanner'])) {
            return false; 
        }

        $sql = sprintf(
            "UPDATE %s SET clickurl = %s WHERE bid = %d AND cid = %d AND (htmlbanner = 0 OR htmlbanner IS NULL)", // Ensure htmlbanner is not true
            $this->db->prefix('banner'),
            $this->db->quoteString($newUrl),
            $bid,
            $cid
        );
        return (bool)$this->db->queryF($sql);
    }
}
