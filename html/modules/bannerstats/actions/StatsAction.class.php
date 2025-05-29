<?php
/**
 * Bannerstats - Module for XCL
 * StatsAction.class.php
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

require_once dirname(__DIR__) . '/class/BannerClientSession.class.php';
require_once dirname(__DIR__) . '/class/BannerStatsManager.class.php';
require_once dirname(__DIR__) . '/class/BannerClientToken.class.php';

class Bannerstats_StatsAction
{
    private string $moduleDirname;

    public function __construct()
    {
        $this->moduleDirname = basename(dirname(dirname(__FILE__)));
    }

    public function getPageTitle(): string
    {
        $clientLogin = BannerClientSession::getClientLogin();
        return $clientLogin ? "Statistics for " . htmlspecialchars($clientLogin, ENT_QUOTES) : "Banner Statistics";
    }

    public function getDefaultView(): ?string
    {
        global $xoopsTpl; 

        if (!BannerClientSession::isAuthenticated()) {
            $root = XCube_Root::getSingleton();
            if ($root && $root->getController()) {
                $root->getController()->executeForward(XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            } else {
                header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            }
            exit();
        }

        $cid = BannerClientSession::getClientId();
        if ($cid === null) {
            $root = XCube_Root::getSingleton();
            if ($root && $root->getController()) {
                $root->getController()->executeForward(XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login&error=session");
            } else {
                header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login&error=session");
            }
            exit();
        }

        $statsManager = new BannerStatsManager();
        $activeBannersData = $statsManager->getActiveBanners($cid);
        $finishedBannersData = $statsManager->getFinishedBanners($cid);

        $activeBanners = [];
        foreach ($activeBannersData as $banner) {
            $bannerType = $banner['banner_type'] ?? 'unknown';
            $isHtmlBanner = in_array($bannerType, ['html', 'ad_tag', 'video'], true);

            $rawImpressionsMade = (int)($banner['impmade'] ?? 0);
            $rawClicksReceived = (int)($banner['clicks'] ?? 0);
            $rawImpressionsTotal = (int)($banner['imptotal'] ?? 1); 
            $rawImpressionsRemaining = max(0, $rawImpressionsTotal - $rawImpressionsMade);

            $displayImpressionsMade = $rawImpressionsMade;
            $displayClicksReceived = $rawClicksReceived;
            $displayCurrentCtr = ($rawImpressionsMade > 0) ? round(($rawClicksReceived * 100) / $rawImpressionsMade, 2) . '%' : '0%';
            $displayImpressionsTotalPurchased = $rawImpressionsTotal;
            $displayImpressionsRemaining = $rawImpressionsRemaining;
            
            $statsNote = '';
            if ($isHtmlBanner) {
                $statsNote = "Note: For Ad Service banners, statistics reflect site-level counts and may differ from your ad service provider's data. Please consult their dashboard for official metrics.";
            }

            $alert_message = '';
            $alert_class = '';
            if ($rawImpressionsTotal > 0) {
                $lowImpressionPercentageThreshold = 0.10; 
                if ($rawImpressionsRemaining <= ($rawImpressionsTotal * $lowImpressionPercentageThreshold)) {
                    $alert_class = ($rawImpressionsRemaining == 0) ? 'banner-alert-critical' : 'banner-alert-warning';
                    $alert_message = sprintf(
                        "%s: %d of %d %s remaining (%.1f%%).",
                        ($rawImpressionsRemaining == 0) ? "Critical" : "Warning",
                        $rawImpressionsRemaining,
                        $rawImpressionsTotal,
                        $isHtmlBanner ? "display opportunities" : "impressions",
                        ($rawImpressionsTotal > 0 ? ($rawImpressionsRemaining / $rawImpressionsTotal) * 100 : 0)
                    );
                }
            }

            $preview = '';
            $bannerNameForAlt = htmlspecialchars($banner['name'] ?? ('Banner ID ' . ($banner['bid'] ?? 'N/A')), ENT_QUOTES, 'UTF-8');

            if ($isHtmlBanner) {
                $preview = $banner['htmlcode'] ?? '[HTML Content Not Available]';
            } elseif ($bannerType === 'image' && !empty($banner['imageurl'])) {
                $imageUrl = $banner['imageurl'];
                if (strpos($imageUrl, '://') === false) { 
                    $imageUrl = XOOPS_URL . '/' . ltrim($imageUrl, '/');
                }
                $preview = "<img src='" . htmlspecialchars($imageUrl, ENT_QUOTES) . 
                           "' alt='" . $bannerNameForAlt . 
                           "' title='" . $bannerNameForAlt . 
                           "' style='display:block; margin:auto; border:1px solid #ccc;' loading='lazy'>";
            } else {
                $preview = "[Preview Not Available - Banner ID: " . ($banner['bid'] ?? 'N/A') . " - Type: " . htmlspecialchars($bannerType, ENT_QUOTES) . "]";
            }

             $changeUrlTokenHtml = '';
            if ($bannerType === 'image' && !empty($banner['clickurl'])) { 
                $changeUrlTokenHtml = BannerClientToken::getHtml("ChangeUrl_" . $banner['bid']);
            }
            
            $activeBanners[] = [
                'bid' => $banner['bid'],
                'name' => $banner['name'],
                'preview' => $preview,
                'impressions_made' => $displayImpressionsMade,
                'clicks_received' => $displayClicksReceived,
                'current_ctr' => $displayCurrentCtr,
                'impressions_total_purchased' => $displayImpressionsTotalPurchased,
                'impressions_remaining' => $displayImpressionsRemaining,
                'clickurl' => htmlspecialchars($banner['clickurl'] ?? '', ENT_QUOTES),
                'is_html' => $isHtmlBanner,
                'banner_type' => $bannerType,
                'stats_note' => $statsNote,
                'email_stats_link' => XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=EmailStats&bid=" . $banner['bid'] . "&amp;" . BannerClientToken::getUrlQuery('EmailStats_' . $banner['bid']),
                'manage_url_link' => ($bannerType === 'image') ? (XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl&bid=" . $banner['bid']) : '',
                'change_url_token_html' => $changeUrlTokenHtml,
                'alert_message' => $alert_message,
                'alert_class' => $alert_class,
            ];
        }

        $finishedBanners = [];
        foreach ($finishedBannersData as $banner) {
            $bannerTypeFinished = $banner['banner_type'] ?? 'unknown';
            $isHtmlBannerFinished = in_array($bannerTypeFinished, ['html', 'ad_tag', 'video'], true);

            $rawTotalImpressionsServed = (int)($banner['impressions_made'] ?? $banner['impressions'] ?? 0);
            $rawTotalClicksReceived = (int)($banner['clicks_made'] ?? $banner['clicks'] ?? 0);
            $displayFinalCtr = ($rawTotalImpressionsServed > 0) ? round(($rawTotalClicksReceived * 100) / $rawTotalImpressionsServed, 2) . '%' : '0%';
            
            $finishedStatsNote = '';
            if ($isHtmlBannerFinished) {
                $finishedStatsNote = "Note: For finished Ad Service banners, these stats reflect site-level counts.";
            }

            $previewFinished = '';
            $bannerNameForAltFinished = htmlspecialchars($banner['name'] ?? ('Banner ID ' . ($banner['bid'] ?? 'N/A')), ENT_QUOTES, 'UTF-8');

            if ($isHtmlBannerFinished) {
                $previewFinished = $banner['htmlcode'] ?? '[Finished HTML Content Not Available]';
            } elseif ($bannerTypeFinished === 'image' && !empty($banner['imageurl'])) {
                $imageUrlFinished = $banner['imageurl'];
                if (strpos($imageUrlFinished, '://') === false) {
                    $imageUrlFinished = XOOPS_URL . '/' . ltrim($imageUrlFinished, '/');
                }
                $previewFinished = "<div style='text-align: center;'>" .
                                   "<img src='" . htmlspecialchars($imageUrlFinished, ENT_QUOTES) . 
                                   "' alt='" . $bannerNameForAltFinished . 
                                   "' title='" . $bannerNameForAltFinished . 
                                   "' class='banner-preview' loading='lazy'>" .
                                   "</div>";
            } else {
                $previewFinished = "[Finished Banner Preview Not Available - ID: " . ($banner['bid'] ?? 'N/A') . " - Type: " . htmlspecialchars($bannerTypeFinished, ENT_QUOTES) . "]";
            }

            $finishedBanners[] = [
                'bid' => $banner['bid'],
                'name' => $banner['name'],
                'preview' => $previewFinished,
                'total_impressions_served' => $rawTotalImpressionsServed,
                'total_clicks_received' => $rawTotalClicksReceived,
                'final_ctr' => $displayFinalCtr,
                'is_html' => $isHtmlBannerFinished,
                'banner_type' => $bannerTypeFinished,
                'stats_note' => $finishedStatsNote,
                'datestart_original' => ($banner['datestart_original'] ?? 0) > 0 ? formatTimestamp((int)$banner['datestart_original'], 'm') : 'N/A',
                'dateend_original' => ($banner['dateend_original'] ?? 0) > 0 ? formatTimestamp((int)$banner['dateend_original'], 'm') : 'N/A',
                'date_finished' => ($banner['date_finished'] ?? 0) > 0 ? formatTimestamp((int)$banner['date_finished'], 'm') : 'N/A',
                'finish_reason' => htmlspecialchars($banner['finish_reason'] ?? 'N/A', ENT_QUOTES),
            ];
        }

        if (is_object($xoopsTpl)) {
            $xoopsTpl->assign('clientLogin', htmlspecialchars(BannerClientSession::getClientLogin() ?? '', ENT_QUOTES));
            $xoopsTpl->assign('activeBanners', $activeBanners);
            $xoopsTpl->assign('finishedBanners', $finishedBanners);
            $xoopsTpl->assign('logoutLink', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Logout");
            $xoopsTpl->assign('contactLink', XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=RequestSupport");
            $xoopsTpl->assign('module_dirname', $this->moduleDirname);
        }

        return 'bannerstats_stats.html';
    }
}
