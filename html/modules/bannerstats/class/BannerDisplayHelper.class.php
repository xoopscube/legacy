<?php
// html/modules/bannerstats/class/BannerDisplayHelper.class.php
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class Bannerstats_BannerDisplayHelper
{
    public static function getRandomBannerHtml(): string
    {
        global $xoopsConfig;
        $db = XoopsDatabaseFactory::getDatabaseConnection();
        $moduleDirname = 'bannerstats';

        // 1. Count only active banners (impmade < imptotal AND imptotal > 0)
        $countSql = 'SELECT COUNT(*) FROM ' . $db->prefix('banner') . ' WHERE impmade < imptotal AND imptotal > 0';
        $countResult = $db->query($countSql);
        if (!$countResult) {
            error_log("Bannerstats_BannerDisplayHelper: DB error counting active banners - " . $db->error());
            return '';
        }
        [$numActiveBanners] = $db->fetchRow($countResult);

        if ($numActiveBanners <= 0) {
            return '';
        }

        // 2. Select a random offset
        $bannumOffset = 0;
        if ($numActiveBanners > 1) {
            try {
                $bannumOffset = random_int(0, $numActiveBanners - 1);
            } catch (Exception $e) {
                $bannumOffset = mt_rand(0, $numActiveBanners - 1);
                error_log("Bannerstats_BannerDisplayHelper: random_int failed: " . $e->getMessage());
            }
        }

        // 3. Fetch the selected active banner
        $bannerSql = sprintf(
            'SELECT bid, cid, imptotal, impmade, clicks, imageurl, clickurl, date, htmlbanner, htmlcode
             FROM %s
             WHERE impmade < imptotal AND imptotal > 0
             LIMIT 1 OFFSET %d',
            $db->prefix('banner'),
            $bannumOffset
        );
        $bannerResult = $db->query($bannerSql);
        if (!$bannerResult || $db->getRowsNum($bannerResult) == 0) {
            error_log("Bannerstats_BannerDisplayHelper: No banner found at offset {$bannumOffset}. SQL: " . $bannerSql);
            return '';
        }
        $bannerData = $db->fetchArray($bannerResult);

        $bid = (int)$bannerData['bid'];
        $cid = (int)$bannerData['cid'];
        $imptotal = (int)$bannerData['imptotal'];
        $impmade = (int)$bannerData['impmade']; // Current impmade from DB
        $clicks = (int)$bannerData['clicks'];
        $imageurl_db = $bannerData['imageurl'];
        $clickurl_db = $bannerData['clickurl'];
        $startDate = (int)$bannerData['date'];
        $isHtmlBanner = !empty($bannerData['htmlbanner']);
        $htmlcode = $bannerData['htmlcode'];

        $currentIp = xoops_getenv('REMOTE_ADDR');
        $isAdminIp = isset($xoopsConfig['my_ip']) && !empty($xoopsConfig['my_ip']) &&
                     in_array($currentIp, array_map('trim', explode(',', $xoopsConfig['my_ip'])));


        // 4. Impression Tracking & Campaign Completion
        if (!$isAdminIp) {
            if ($isHtmlBanner) {
                // For HTML banners, increment 'impmade' for "display opportunities"
                // $imptotal is always > 0 here for an active banner
                if ($impmade < $imptotal) { // Check if limit not yet reached
                    $db->queryF(sprintf('UPDATE %s SET impmade = impmade + 1 WHERE bid = %u', $db->prefix('banner'), $bid));
                    $impmade++;
                }
            }
            // For Image Banners: The impression is counted by banner-imp.php when the image is requested.
            // So, no direct increment of 'impmade' here for image banners.
            // The 'impmade' value from the DB is used for the completion check.

            // Campaign Completion Check (imptotal is always > 0)
            if ($impmade >= $imptotal) {
                $finishSql = sprintf(
                    'INSERT INTO %s (bid, cid, impressions, clicks, datestart, dateend) VALUES (%u, %u, %u, %u, %u, %u)',
                    $db->prefix('bannerfinish'),
                    $bid, $cid, $impmade, $clicks, $startDate, time()
                );
                $db->queryF($finishSql);
                $db->queryF(sprintf('DELETE FROM %s WHERE bid = %u', $db->prefix('banner'), $bid));
                return ''; // Banner finished
            }
        }

        // 5. Render Banner HTML
        $bannerObjectHtml = '';
        if ($isHtmlBanner) {
            // For HTML banners, output the code directly. No XOOPS click/impression tracking wrappers.
            $bannerObjectHtml = '<div class="banner">' . $htmlcode . '</div>';
        } else {
            // For image banners, use bannerstats module's own tracking URLs
            $imgFileSrc = XOOPS_URL . '/' . ($xoopsConfig['uploads_path'] ?? 'uploads') . '/' . $imageurl_db;
            $impressionCountingImageSrc = XOOPS_URL . '/modules/' . $moduleDirname . '/banner-imp.php?bid=' . $bid;
            $clickTrackingUrl = XOOPS_URL . '/modules/' . $moduleDirname . '/banner-click.php?bid=' . $bid;

            $bannerObjectHtml = '<div class="banner">';
            $bannerObjectHtml .= '<a href="' . htmlspecialchars($clickTrackingUrl, ENT_QUOTES) . '" rel="noopener" target="_blank">';
            $bannerObjectHtml .= '<img src="' . htmlspecialchars($impressionCountingImageSrc, ENT_QUOTES) . '" alt="Banner ID ' . $bid . '" loading="lazy">';
            $bannerObjectHtml .= '</a></div>';
        }
        return $bannerObjectHtml;
    }
}
?>
