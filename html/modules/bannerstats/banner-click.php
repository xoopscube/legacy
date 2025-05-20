<?php
// html/modules/bannerstats/banner-click.php

/**
 * Banner Click Tracker for BannerStats Module
 *
 * This script should only be called for image banners managed by this module.
 * It increments the click count and redirects to the banner's target URL.
 */

// Attempt to find and include mainfile.php to bootstrap XOOPS
// This assumes the 'public' directory is directly under 'bannerstats' module directory
$xoops_root_path_detected = false;
if (file_exists(dirname(dirname(__DIR__)) . '/mainfile.php')) { // ../../../mainfile.php from modules/bannerstats/public
    require_once dirname(dirname(__DIR__)) . '/mainfile.php';
    $xoops_root_path_detected = true;
} elseif (defined('XOOPS_ROOT_PATH') && file_exists(XOOPS_ROOT_PATH . '/mainfile.php')) {
    // If XOOPS_ROOT_PATH is already defined (e.g., by a custom entry point)
    require_once XOOPS_ROOT_PATH . '/mainfile.php';
    $xoops_root_path_detected = true;
} else {
    // Fallback if mainfile.php can't be found.
    // This is a critical failure.
    header("HTTP/1.1 500 Internal Server Error");
    echo "Critical Error: XOOPS Environment not loaded. Cannot track click.";
    exit();
}

if (!$xoops_root_path_detected) {
    // Should have exited above, but as a safeguard
    header("HTTP/1.1 500 Internal Server Error");
    echo "Critical Error: XOOPS Environment not loaded (safeguard). Cannot track click.";
    exit();
}

$bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;

if ($bid <= 0) {
    // Invalid banner ID, redirect to homepage or show an error
    header("Location: " . XOOPS_URL . "/");
    exit();
}

$db = XoopsDatabaseFactory::getDatabaseConnection();
if (!$db) {
    // Log error, can't connect to DB
    error_log("BannerStats Click Tracker: Could not connect to the database.");
    header("Location: " . XOOPS_URL . "/"); // Fallback redirect
    exit();
}

// Fetch banner details, specifically the clickurl and ensure it's not an HTML banner
// (though the calling code should already prevent this for HTML banners)
$sql = sprintf(
    "SELECT clickurl, htmlbanner FROM %s WHERE bid = %u",
    $db->prefix('banner'),
    $bid
);
$result = $db->query($sql);

if (!$result || $db->getRowsNum($result) == 0) {
    // Banner not found
    error_log("BannerStats Click Tracker: Banner ID {$bid} not found.");
    header("Location: " . XOOPS_URL . "/");
    exit();
}

$banner_data = $db->fetchArray($result);

// Double-check: This script should ideally not be called for HTML banners
// if the BannerDisplayHelper is correctly generating links.
if (!empty($banner_data['htmlbanner'])) {
    error_log("BannerStats Click Tracker: Attempted to track click for an HTML banner (ID: {$bid}). This should not happen if links are generated correctly.");
    // If it's an HTML banner, its own code handles clicks.
    // Redirecting to its clickurl might be redundant or incorrect.
    // For safety, redirect to homepage or the clickurl if it's somehow set.
    if (!empty($banner_data['clickurl']) && filter_var($banner_data['clickurl'], FILTER_VALIDATE_URL)) {
        header("Location: " . htmlspecialchars_decode($banner_data['clickurl'])); // Decode if it was stored HTML-encoded
    } else {
        header("Location: " . XOOPS_URL . "/");
    }
    exit();
}

// Increment click count for the banner
$update_sql = sprintf(
    "UPDATE %s SET clicks = clicks + 1 WHERE bid = %u",
    $db->prefix('banner'),
    $bid
);
$db->queryF($update_sql); // Use queryF for "fire and forget" updates

// Get the destination URL
$destination_url = $banner_data['clickurl'];

if (empty($destination_url) || !filter_var($destination_url, FILTER_VALIDATE_URL)) {
    // No valid click URL defined for this banner, redirect to homepage
    error_log("BannerStats Click Tracker: No valid clickurl for banner ID {$bid}.");
    header("Location: " . XOOPS_URL . "/");
    exit();
}

// Perform the redirect
// Use htmlspecialchars_decode in case the URL was stored with HTML entities
header("Location: " . htmlspecialchars_decode($destination_url));
exit();

?>
