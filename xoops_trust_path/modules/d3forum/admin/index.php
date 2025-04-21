<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.5.0
 * @author     Nuno Luciano aka gigamaster
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

// Include header
xoops_cp_header();

// <{$mod_name|ucfirst}>
$mod_name = $xoopsModule->getVar( 'dirname' );

// Query Total from Multiple Tables
$query = "SELECT
(SELECT COUNT(`cat_id`) FROM " . $xoopsDB->prefix($mod_name . '_categories') . " LIMIT 1) as totalCat,
(SELECT SUM(`cat_topics_count`) FROM " . $xoopsDB->prefix($mod_name . '_categories') . " LIMIT 1) as totalCatTopics,
(SELECT SUM(`cat_posts_count`) FROM " . $xoopsDB->prefix($mod_name . '_categories') . " LIMIT 1) as totalCatPosts,
(SELECT COUNT(`forum_id`) FROM " . $xoopsDB->prefix($mod_name . '_forums') . " LIMIT 1) as totalForums,
(SELECT SUM(`forum_topics_count`) FROM " . $xoopsDB->prefix($mod_name . '_forums') . " LIMIT 1) as totalForumTopics,
(SELECT SUM(`forum_posts_count`) FROM " . $xoopsDB->prefix($mod_name . '_forums') . " LIMIT 1) as totalForumPosts,
(SELECT COUNT(`topic_id`) FROM " . $xoopsDB->prefix($mod_name . '_topics') . " LIMIT 1) as totalTopics,
(SELECT COUNT(`post_id`) FROM " . $xoopsDB->prefix($mod_name . '_posts') . " LIMIT 1) as totalPosts,
(SELECT COUNT(*) FROM " . $xoopsDB->prefix($mod_name . '_topics') . " WHERE topic_solved = 1) as totalSolvedTopics,
(SELECT COUNT(*) FROM " . $xoopsDB->prefix($mod_name . '_topics') . " WHERE topic_solved = 0) as totalUnsolved";


$active = $xoopsDB->query($query);
$total = $active->fetch_array();

// QUERY SETTINGS
$default_items = 10; // Default limit of items to display
$items = isset($_GET['items']) ? (int)$_GET['items'] : $default_items;

// If no items parameter is provided, check localStorage via JavaScript
if (!isset($_GET['items'])) {
    // The JavaScript will handle this by setting the select value from localStorage
    $items = $default_items;
}

// Validate items to only allow specific values
if (!in_array($items, [10, 20, 30])) {
    $items = $default_items;
}

$days = 30; /* interval scheduled to display recent posts, default 5 */

// QUERY DB - Recent posts
$sql = "SELECT p.post_id as ID,
p.subject as Subject,
p.post_time as PostTime,
p.uid as UserID,
t.topic_id as TopicID,
t.topic_title as TopicTitle,
t.topic_views as TopicViews,
t.topic_posts_count as TopicPostsCount,
f.forum_id as ForumID,
f.forum_title as ForumTitle,
COUNT(v.vote_id) as VoteCount,
SUM(v.vote_point) as TotalPoints,
ROUND(AVG(v.vote_point), 1) as AverageRating
FROM " . $xoopsDB->prefix($mod_name . '_posts') . " p
LEFT JOIN " . $xoopsDB->prefix($mod_name . '_topics') . " t ON p.topic_id = t.topic_id
LEFT JOIN " . $xoopsDB->prefix($mod_name . '_forums') . " f ON t.forum_id = f.forum_id
LEFT JOIN " . $xoopsDB->prefix($mod_name . '_post_votes') . " v ON p.post_id = v.post_id
WHERE p.post_time > (UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL $days DAY)))
GROUP BY p.post_id, p.subject, p.post_time, p.uid, t.topic_id, t.topic_title, t.topic_views, t.topic_posts_count, f.forum_id, f.forum_title
ORDER BY p.post_time DESC LIMIT $items";

$result = $xoopsDB->query($sql);
// if no content, prevent warnings
$assignActivity = [] ?? '';

if ($result && $result->num_rows > 0) {
    $re = 1;

    while ($active = $result->fetch_assoc()) {
        $id = $active['ID'] ?? '';
        $topicID = $active['TopicID'] ?? '';
        $forumID = $active['ForumID'] ?? '';

        $link = htmlspecialchars(XOOPS_URL . '/modules/' . $mod_name . '/index.php?post_id=' . $id, ENT_QUOTES);
        $topicLink = htmlspecialchars(XOOPS_URL . '/modules/' . $mod_name . '/index.php?topic_id=' . $topicID, ENT_QUOTES);
        $forumLink = htmlspecialchars(XOOPS_URL . '/modules/' . $mod_name . '/index.php?forum_id=' . $forumID, ENT_QUOTES);

        $subject = htmlspecialchars($active['Subject'] ?? '', ENT_QUOTES);
        $topicTitle = htmlspecialchars($active['TopicTitle'] ?? '', ENT_QUOTES);
        $forumTitle = htmlspecialchars($active['ForumTitle'] ?? '', ENT_QUOTES);
        $postTime = $active['PostTime'] ?? '';
        $userID = $active['UserID'] ?? '';
        $voteCount = $active['VoteCount'] ?? 0;
        $totalPoints = $active['TotalPoints'] ?? 0;
        $averageRating = $active['AverageRating'] ?? 0;
        $topicViews = $active['TopicViews'] ?? 0;
        $topicPostsCount = $active['TopicPostsCount'] ?? 0;

        $assignActivity[] = [
            're'              => $re++,
            'id'              => $id,
            'link'            => $link,
            'subject'         => $subject,
            'post_time'       => $postTime,
            'user_id'         => $userID,
            'topic_id'        => $topicID,
            'topic_link'      => $topicLink,
            'topic_title'     => $topicTitle,
            'topic_views'     => $topicViews,
            'topic_posts_count' => $topicPostsCount,
            'forum_id'        => $forumID,
            'forum_link'      => $forumLink,
            'forum_title'     => $forumTitle,
            'vote_count'      => $voteCount,
            'total_points'    => $totalPoints,
            'average_rating'  => $averageRating,
        ];
    }
}

// Check if this is an AJAX request
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == 1;

// RENDER
if ($isAjax) {
    // For AJAX requests, only render the table part
    $tpl = new XoopsTpl();
    $tpl->assign(
        [
            'mydirname'        => $mod_name,
            'mod_name'         => $xoopsModule->getVar('name'),
            'mod_url'          => XOOPS_URL . '/modules/' . $mod_name,
            'active'           => $assignActivity,
            'days'             => $days,
            'items'            => $items
        ]
    );
    $tpl->display('db:' . $mod_name . '_admin_activity_table.html');
    exit;
} else {
    // Regular page render
    xoops_cp_header();
    include __DIR__ . '/mymenu.php';
    $tpl = new XoopsTpl();
    $tpl->assign(
        [
            'mydirname'        => $mod_name,
            'mod_name'         => $xoopsModule->getVar('name'),
            'mod_url'          => XOOPS_URL . '/modules/' . $mod_name,
            'mod_imageurl'     => XOOPS_URL . '/modules/' . $mod_name . '/' . $xoopsModuleConfig['images_dir'] ?? 'images',
            'mod_config'       => $xoopsModuleConfig,
            'totalCat'         => $total['totalCat'] ?? 0,
            'totalCatTopics'   => $total['totalCatTopics'] ?? 0,
            'totalCatPosts'    => $total['totalCatPosts'] ?? 0,
            'totalForums'      => $total['totalForums'] ?? 0,
            'totalForumTopics' => $total['totalForumTopics'] ?? 0,
            'totalForumPosts'  => $total['totalForumPosts'] ?? 0,
            'totalTopics'      => $total['totalTopics'] ?? 0,
            'totalPosts'       => $total['totalPosts'] ?? 0,
            'totalSolved'      => $total['totalSolvedTopics'] ?? 0,
            'totalUnsolved'      => $total['totalUnsolved'] ?? 0,
            'active'           => $assignActivity,
            'days'             => $days,
            'items'            => $items
        ]
    );
    $tpl->display('db:' . $mod_name . '_admin_activity.html');

    xoops_cp_footer();
}
