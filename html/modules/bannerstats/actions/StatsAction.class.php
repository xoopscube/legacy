<?php
// html/modules/bannerstats/actions/StatsAction.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once dirname(__DIR__) . '/class/BannerClientSession.class.php';
require_once dirname(__DIR__) . '/class/BannerStatsManager.class.php';
require_once dirname(__DIR__) . '/class/BannerClientToken.class.php';

class Bannerstats_StatsAction
{
    private $moduleDirname;

    public function __construct()
    {
        $this->moduleDirname = basename(dirname(dirname(__FILE__))); // 'bannerstats'
    }

    public function getPageTitle(): string
    {
        $clientLogin = BannerClientSession::getClientLogin();
        return $clientLogin ? "Statistics for " . htmlspecialchars($clientLogin, ENT_QUOTES) : "Banner Statistics";
    }

    public function getDefaultView(): ?string
    {
        global $xoopsTpl, $xoopsConfig;

        if (!BannerClientSession::isAuthenticated()) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login");
            exit();
        }

        $cid = BannerClientSession::getClientId();
        if ($cid === null) {
            header("Location: " . XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=Login&error=session");
            exit();
        }

        $statsManager = new BannerStatsManager();
        // BannerStatsManager will now fetch banners where imptotal > 0
        $activeBannersData = $statsManager->getActiveBanners($cid);
        $finishedBannersData = $statsManager->getFinishedBanners($cid);

        $activeBanners = [];
        foreach ($activeBannersData as $banner) {
            $isHtmlBanner = !empty($banner['htmlbanner']);

            // Raw integer values - imptotal is now always > 0 for active banners
            $rawImpressionsMade = (int)($banner['impmade'] ?? 0);
            $rawClicksReceived = (int)($banner['clicks'] ?? 0);
            $rawImpressionsTotal = (int)($banner['imptotal'] ?? 1); // Default to 1 if somehow 0 slips through, though admin should prevent this.

            // Calculate raw impressions remaining
            $rawImpressionsRemaining = $rawImpressionsTotal - $rawImpressionsMade;
            if ($rawImpressionsRemaining < 0) {
                $rawImpressionsRemaining = 0;
            }

            // --- Formatted display variables ---
            $displayImpressionsMade = $rawImpressionsMade;
            $displayClicksReceived = $rawClicksReceived;
            $displayCurrentCtr = '0%';
            $displayImpressionsTotalPurchased = $rawImpressionsTotal; // Always a number now
            $displayImpressionsRemaining = $rawImpressionsRemaining; // Always a number now
            $statsNote = '';

            if ($isHtmlBanner) {
                $displayCurrentCtr = ($rawImpressionsMade > 0 && $rawClicksReceived > 0) ? round(($rawClicksReceived * 100) / $rawImpressionsMade, 2) . '%' : '0% (Site Data)';
                $statsNote = "Note: For HTML/Ad Service banners, 'Impressions' and 'Clicks' reflect site-level display counts and may differ from your ad service provider's official statistics. Please consult your ad service dashboard for accurate performance metrics.";
            } else {
                $displayCurrentCtr = ($rawImpressionsMade > 0) ? round(($rawClicksReceived * 100) / $rawImpressionsMade, 2) . '%' : '0%';
            }

            // Alert logic (imptotal is always > 0 for active banners)
            $alert_message = '';
            $alert_class = '';
            // $rawImpressionsTotal should always be > 0 if it's an active banner from the new system
            if ($rawImpressionsTotal > 0) { // This check is still good practice
                $lowImpressionPercentageThreshold = 0.10;
                $criticalImpressionAbsoluteThreshold = 100; // Example

                if ($rawImpressionsRemaining <= ($rawImpressionsTotal * $lowImpressionPercentageThreshold)) {
                    if ($rawImpressionsRemaining == 0) {
                        $alert_message = sprintf(
                            "Critical: 0 of %d %s remaining. Banner may stop serving soon or has finished.",
                            $rawImpressionsTotal,
                            $isHtmlBanner ? "display opportunities" : "impressions"
                        );
                        $alert_class = 'banner-alert-critical';
                    } else {
                        $alert_message = sprintf(
                            "Warning: %s low. %d of %d remaining (%.1f%%).",
                            $isHtmlBanner ? "Display opportunities" : "Impressions",
                            $rawImpressionsRemaining,
                            $rawImpressionsTotal,
                            ($rawImpressionsRemaining / $rawImpressionsTotal) * 100
                        );
                        $alert_class = 'banner-alert-warning';
                    }
                } elseif ($rawImpressionsRemaining <= $criticalImpressionAbsoluteThreshold && $rawImpressionsTotal > $criticalImpressionAbsoluteThreshold) {
                    $alert_message = sprintf(
                        "Notice: Only %d %s remaining.",
                        $rawImpressionsRemaining,
                        $isHtmlBanner ? "display opportunities" : "impressions"
                    );
                    $alert_class = 'banner-alert-notice';
                }
            }

            $preview = '';
            if ($isHtmlBanner) {
                $preview = "[HTML Banner - ID: " . $banner['bid'] . "]";
            } elseif (!empty($banner['imageurl'])) {
                $preview = "<img src='" . htmlspecialchars(XOOPS_URL . "/" . $xoopsConfig['uploads_path'] . "/" . $banner['imageurl'], ENT_QUOTES) . "' alt='Banner ID " . $banner['bid'] . "' title='Banner ID " . $banner['bid'] . "' style='max-width:200px; max-height:100px;'>";
            } else {
                $preview = "Banner ID: " . $banner['bid'];
            }

            $changeUrlTokenHtml = '';
            if (!$isHtmlBanner && !empty($banner['clickurl'])) {
                $changeUrlTokenHtml = BannerClientToken::getHtml("ChangeUrl_" . $banner['bid']);
            }
            
            $activeBanners[] = [
                'bid' => $banner['bid'],
                'preview' => $preview,
                'impressions_made' => $displayImpressionsMade,
                'clicks_received' => $displayClicksReceived,
                'current_ctr' => $displayCurrentCtr,
                'impressions_total_purchased' => $displayImpressionsTotalPurchased,
                'impressions_remaining' => $displayImpressionsRemaining,
                'raw_impressions_made' => $rawImpressionsMade,
                'raw_clicks_received' => $rawClicksReceived,
                'raw_impressions_total' => $rawImpressionsTotal,
                'raw_impressions_remaining' => $rawImpressionsRemaining,
                // 'is_unlimited_impressions' => false, // This flag is no longer needed

                'clickurl' => htmlspecialchars($banner['clickurl'] ?? '', ENT_QUOTES),
                'is_html' => $isHtmlBanner,
                'stats_note' => $statsNote,
                'email_stats_link' => XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=EmailStats&bid=" . $banner['bid'] . "&amp;" . BannerClientToken::getUrlQuery('EmailStats_' . $banner['bid']),
                'manage_url_link' => XOOPS_URL . "/modules/" . $this->moduleDirname . "/index.php?action=ChangeUrl&bid=" . $banner['bid'],
                'change_url_token_html' => $changeUrlTokenHtml,
                'alert_message' => $alert_message,
                'alert_class' => $alert_class,
                'impressions_made_label' => $isHtmlBanner ? _MD_BANNERSTATS_IMPRESSIONS : 'Impressions Made',
                'clicks_received_label' => $isHtmlBanner ? _MD_BANNERSTATS_CLICKS : 'Clicks',
                'current_ctr_label' => $isHtmlBanner ? _MD_BANNERSTATS_CTR : 'Current CTR',
                'impressions_total_purchased_label' => $isHtmlBanner ? _MD_BANNERSTATS_IMPTOTAL : 'Total Purchased',
                'impressions_remaining_label' => $isHtmlBanner ? _MD_BANNERSTATS_IMPLEFT : 'Impressions Remaining',
            ];
        }

        // Finished Banners loop (unchanged as it deals with historical data)
        $finishedBanners = [];
        foreach ($finishedBannersData as $banner) {
            // pass raw values for consistency
            $isHtmlBannerFinished = !empty($banner['htmlbanner']);
            $rawTotalImpressionsServed = (int)($banner['impressions'] ?? 0);
            $rawTotalClicksReceived = (int)($banner['clicks'] ?? 0);
            $displayFinalCtr = '0%';
            $finishedStatsNote = '';

            if ($isHtmlBannerFinished) {
                $displayFinalCtr = ($rawTotalImpressionsServed > 0 && $rawTotalClicksReceived > 0) ? round(($rawTotalClicksReceived * 100) / $rawTotalImpressionsServed, 2) . '%' : '0% (Site Data)';
                $finishedStatsNote = "Note: For finished HTML/Ad Service banners, these stats reflect site-level counts.";
            } else {
                $displayFinalCtr = ($rawTotalImpressionsServed > 0) ? round(($rawTotalClicksReceived * 100) / $rawTotalImpressionsServed, 2) . '%' : '0%';
            }
            
            $preview = '';
            if ($isHtmlBannerFinished) {
                 $preview = "[HTML Banner - ID: " . $banner['bid'] . "]";
            } elseif (!empty($banner['imageurl'])) {
                $preview = "<img src='" . htmlspecialchars(XOOPS_URL . "/" . $xoopsConfig['uploads_path'] . "/" . $banner['imageurl'], ENT_QUOTES) . "' alt='Banner ID " . $banner['bid'] . "' title='Banner ID " . $banner['bid'] . "' style='max-width:100px; max-height:50px;'>";
            } else {
                $preview = "Banner ID: " . $banner['bid'];
            }

            $finishedBanners[] = [
                'bid' => $banner['bid'],
                'preview' => $preview,
                'total_impressions_served' => $rawTotalImpressionsServed,
                'total_clicks_received' => $rawTotalClicksReceived,
                'final_ctr' => $displayFinalCtr,
                'raw_total_impressions_served' => $rawTotalImpressionsServed,
                'raw_total_clicks_received' => $rawTotalClicksReceived,
                'is_html' => $isHtmlBannerFinished,
                'stats_note' => $finishedStatsNote,
                'datestart' => ($banner['datestart'] ?? 0) > 0 ? formatTimestamp((int)$banner['datestart'], 'm') : 'N/A',
                'dateend' => ($banner['dateend'] ?? 0) > 0 ? formatTimestamp((int)$banner['dateend'], 'm') : 'N/A',
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
