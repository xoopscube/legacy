<?php
// html/modules/bannerstats/banner-imp.php

/**
 * Banner Impression Tracker and Image Server for BannerStats Module
 *
 * This script should only be called for image banners managed by this module.
 * It increments the impression count and serves the banner image.
 */

// Attempt to find and include mainfile.php to bootstrap XOOPS
$xoops_root_path_detected = false;
if (file_exists(dirname(dirname(__DIR__)) . '/mainfile.php')) { // ../../../mainfile.php
    require_once dirname(dirname(__DIR__)) . '/mainfile.php';
    $xoops_root_path_detected = true;
} elseif (defined('XOOPS_ROOT_PATH') && file_exists(XOOPS_ROOT_PATH . '/mainfile.php')) {
    require_once XOOPS_ROOT_PATH . '/mainfile.php';
    $xoops_root_path_detected = true;
} else {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Critical Error: XOOPS Environment not loaded. Cannot track impression.";
    exit();
}

if (!$xoops_root_path_detected) {
    header("HTTP/1.1 500 Internal Server Error");
    echo "Critical Error: XOOPS Environment not loaded (safeguard). Cannot track impression.";
    exit();
}

$bid = isset($_GET['bid']) ? (int)$_GET['bid'] : 0;

if ($bid <= 0) {
    // Invalid banner ID, perhaps return a 1x1 transparent pixel or 404
    header("HTTP/1.1 404 Not Found");
    exit();
}

$db = XoopsDatabaseFactory::getDatabaseConnection();
if (!$db) {
    error_log("BannerStats Impression Tracker: Could not connect to the database.");
    header("HTTP/1.1 500 Internal Server Error");
    exit();
}

// Fetch banner details
$sql = sprintf(
    "SELECT bid, cid, imptotal, impmade, clicks, imageurl, date, htmlbanner FROM %s WHERE bid = %u",
    $db->prefix('banner'),
    $bid
);
$result = $db->query($sql);

if (!$result || $db->getRowsNum($result) == 0) {
    // Banner not found
    error_log("BannerStats Impression Tracker: Banner ID {$bid} not found.");
    header("HTTP/1.1 404 Not Found");
    exit();
}

$banner_data = $db->fetchArray($result);

// This script is for IMAGE banners. If it's an HTML banner, something is wrong.
if (!empty($banner_data['htmlbanner'])) {
    error_log("BannerStats Impression Tracker: Attempted to serve/track impression for an HTML banner (ID: {$bid}). This should not happen.");
    header("HTTP/1.1 400 Bad Request"); // Or 404
    exit();
}

if (empty($banner_data['imageurl'])) {
    error_log("BannerStats Impression Tracker: No imageurl defined for banner ID {$bid}.");
    header("HTTP/1.1 404 Not Found");
    exit();
}

// Check if the viewer is an admin (using $xoopsConfig['my_ip'] if set)
// $xoopsConfig should be available after mainfile.php is included.
global $xoopsConfig;
$current_ip = xoops_getenv('REMOTE_ADDR');
$is_admin_ip = false;
if (isset($xoopsConfig['my_ip']) && !empty($xoopsConfig['my_ip'])) {
    $admin_ips = array_map('trim', explode(',', $xoopsConfig['my_ip']));
    if (in_array($current_ip, $admin_ips)) {
        $is_admin_ip = true;
    }
}

$impmade_updated = (int)$banner_data['impmade']; // Keep track of current impmade

// Increment impression count if not admin and campaign is active
if (!$is_admin_ip) {
    $imptotal = (int)$banner_data['imptotal'];
    
    // Only count if not yet met (imptotal is always > 0 for active banners)
    if ($impmade_updated < $imptotal) {
        $update_sql = sprintf(
            "UPDATE %s SET impmade = impmade + 1 WHERE bid = %u",
            $db->prefix('banner'),
            $bid
        );
        $db->queryF($update_sql);
        $impmade_updated++;
    }
}

// Check for campaign completion (only if not admin)
if (!$is_admin_ip) {
    $imptotal_check = (int)$banner_data['imptotal']; // Should be > 0
    // Campaign finished if impmade reaches or exceeds imptotal
    if ($impmade_updated >= $imptotal_check) {
        // Campaign finished, move to bannerfinish table
        $finish_sql = sprintf(
            'INSERT INTO %s (bid, cid, impressions, clicks, datestart, dateend) VALUES (%u, %u, %u, %u, %u, %u)',
            $db->prefix('bannerfinish'),
            (int)$banner_data['bid'],
            (int)$banner_data['cid'],
            $impmade_updated, // Use the potentially updated impmade
            (int)$banner_data['clicks'],
            (int)$banner_data['date'], // Start date
            time() // End date
        );
        $db->queryF($finish_sql);

        // Delete from active banners table
        $delete_sql = sprintf("DELETE FROM %s WHERE bid = %u", $db->prefix('banner'), (int)$banner_data['bid']);
        $db->queryF($delete_sql);
    }
}

// Serve the image
$image_filename = $banner_data['imageurl'];
// Construct the full path to the image.
// $xoopsConfig['uploads_path'] is relative to XOOPS_ROOT_PATH
$image_path = XOOPS_ROOT_PATH . '/' . ($xoopsConfig['uploads_path'] ?? 'uploads') . '/' . $image_filename;

if (!file_exists($image_path) || !is_readable($image_path)) {
    error_log("BannerStats Impression Tracker: Image file not found or not readable for banner ID {$bid} at path: {$image_path}");
    header("HTTP/1.1 404 Not Found");
    exit();
}

// Determine content type
$image_info = getimagesize($image_path);
if ($image_info === false) {
    error_log("BannerStats Impression Tracker: Could not get image info for banner ID {$bid} at path: {$image_path}");
    header("HTTP/1.1 500 Internal Server Error");
    exit();
}
$content_type = $image_info['mime'] ?? 'application/octet-stream'; // Fallback

header('Content-Type: ' . $content_type);
header('Content-Length: ' . filesize($image_path));
header('Cache-Control: no-cache, no-store, must-revalidate'); // Prevent caching of this dynamic script
header('Pragma: no-cache');
header('Expires: 0');

// Clear output buffer and serve the file
if (ob_get_level()) {
    ob_end_clean();
}
readfile($image_path);
exit();

?>
