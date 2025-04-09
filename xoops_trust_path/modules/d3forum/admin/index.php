<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.5.0
 * @author     Nobuhiro YASUTOMI, PHP8
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

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
(SELECT COUNT(`post_id`) FROM " . $xoopsDB->prefix($mod_name . '_posts') . " LIMIT 1) as totalPosts
";
$active = $xoopsDB->query($query); 
$total = $active->fetch_array();

// QUERY SETTINGS
$items = 10; // TODO Limit of items to display and pagination
$days  = 5; /* interval scheduled to display recent posts, default 5 */

// QUERY DB - Recent posts
$sql = "SELECT p.post_id as ID,
p.subject as Subject,
p.post_time as PostTime,
p.uid as UserID,
t.topic_id as TopicID,
t.topic_title as TopicTitle,
f.forum_id as ForumID,
f.forum_title as ForumTitle
FROM " . $xoopsDB->prefix($mod_name . '_posts') . " p
LEFT JOIN " . $xoopsDB->prefix($mod_name . '_topics') . " t ON p.topic_id = t.topic_id
LEFT JOIN " . $xoopsDB->prefix($mod_name . '_forums') . " f ON t.forum_id = f.forum_id
WHERE p.post_time > (UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL $days DAY)))
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

        $assignActivity[] = [
            're'         => $re++,
            'id'         => $id,
            'link'       => $link,
            'subject'    => $subject,
            'post_time'  => $postTime,
            'user_id'    => $userID,
            'topic_id'   => $topicID,
            'topic_link' => $topicLink,
            'topic_title'=> $topicTitle,
            'forum_id'   => $forumID,
            'forum_link' => $forumLink,
            'forum_title'=> $forumTitle
        ];
    }
}

// RENDER
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
        'active'           => $assignActivity,
        'days'             => $days
    ]
);

// Check if the template exists, otherwise use a fallback
if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $mod_name . '/templates/d3forum_admin_activity.html')) {
    $tpl->display('db:d3forum_admin_activity.html');
} else {
    // Display basic information if template doesn't exist
    echo '<h3>' . $xoopsModule->getVar('name') . ' - Admin Dashboard</h3>';
    echo '<div class="admin-stats">';
    echo '<p>Total Categories: ' . ($total['totalCat'] ?? 0) . '</p>';
    echo '<p>Total Forums: ' . ($total['totalForums'] ?? 0) . '</p>';
    echo '<p>Total Topics: ' . ($total['totalTopics'] ?? 0) . '</p>';
    echo '<p>Total Posts: ' . ($total['totalPosts'] ?? 0) . '</p>';
    echo '</div>';
    
    if (!empty($assignActivity)) {
        echo '<h4>Recent Activity (Last ' . $days . ' days)</h4>';
        echo '<table class="outer" width="100%">';
        echo '<tr><th>ID</th><th>Subject</th><th>Topic</th><th>Forum</th><th>Date</th></tr>';
        
        foreach ($assignActivity as $item) {
            echo '<tr class="' . ($item['re'] % 2 ? 'odd' : 'even') . '">';
            echo '<td>' . $item['id'] . '</td>';
            echo '<td><a href="' . $item['link'] . '">' . $item['subject'] . '</a></td>';
            echo '<td><a href="' . $item['topic_link'] . '">' . $item['topic_title'] . '</a></td>';
            echo '<td><a href="' . $item['forum_link'] . '">' . $item['forum_title'] . '</a></td>';
            echo '<td>' . date('Y-m-d H:i', $item['post_time']) . '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
}

require_once XOOPS_ROOT_PATH . "/footer.php";
