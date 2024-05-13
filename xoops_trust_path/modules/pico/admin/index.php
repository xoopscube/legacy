<?php
/**
 * Pico content management D3 module for XCL
 * @package    Pico
 * @version    XCL 2.4.0
 * @author     Nuno Luciano aka Gigamaster, 2020 XCL PHP7
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/header.php';

// <{$mod_name|ucfirst}>
$mod_name = $xoopsModule->getVar( 'name' );

// Query Total from Multiple Tables
$query = "SELECT 
(SELECT COUNT(`cat_id`) FROM " . $xoopsDB->prefix($mod_name .'_categories') ." LIMIT 1) as totalCat, 
(SELECT COUNT(`content_id`) FROM " . $xoopsDB->prefix($mod_name .'_contents') ." LIMIT 1) as totalContent,
(SELECT COUNT(`content_id`) FROM " . $xoopsDB->prefix($mod_name .'_content_histories') ." LIMIT 1) as totalRevision,
(SELECT COUNT(`count`) FROM " . $xoopsDB->prefix($mod_name .'_tags') ." LIMIT 1) as totalTags,
(SELECT SUM(`comments_count`) FROM " . $xoopsDB->prefix($mod_name .'_contents') ." LIMIT 1) as totalComments,
(SELECT COUNT(`vote_id`) FROM " . $xoopsDB->prefix($mod_name .'_content_votes') ." LIMIT 1) as totalVotes,
(SELECT COUNT(`content_id`) FROM " . $xoopsDB->prefix($mod_name .'_contents') ." WHERE 'subject_waiting' IS NOT NULL 
AND TRIM(subject_waiting) <> '' LIMIT 1) as totalApproval,
(SELECT COUNT(`content_extra_id`) FROM " . $xoopsDB->prefix($mod_name .'_content_extras') ." LIMIT 1) as totalExtra
";
$active = $xoopsDB->query($query); 
$total = $active->fetch_array();

// QUERY SETTINGS
$items = 10; // TODO Limit of items to display and pagination
$days  = 5; /* interval scheduled to expire, default 5 */

// QUERY DB
$sql = "SELECT content_id as ID,
subject as Subject,
visible as Visible,
locked as Locked,
viewed as Views,
comments_count as Comments,
expiring_time as Expire 
FROM " . $xoopsDB->prefix($mod_name .'_contents'). " 
WHERE expiring_time 
BETWEEN (UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL $days DAY))) 
AND (UNIX_TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL $days DAY))) 
ORDER BY expiring_time ASC LIMIT $items";

$result = $xoopsDB->query($sql);
// if no content, prevent warnings
$assignActivity = []??'';

if ($row = $result->fetch_assoc()) {

$re=1;

    foreach($result as $active){     
        $re; // used to style interval
        $id      = $active['ID']??'';
        $link    = htmlspecialchars( XOOPS_URL.'/modules/'.$mod_name.'/index.php?content_id='.$id, ENT_QUOTES );
        $edit    = htmlspecialchars( XOOPS_URL.'/modules/'.$mod_name.'/index.php?page=contentmanager&content_id='.$id, ENT_QUOTES );
        $subject = htmlspecialchars( $active['Subject']??'', ENT_QUOTES );
        if (str_contains($active['Visible']??'', '1')) { 
            $visible = $active['Visible']; // icon 
        } else { $visible= '';}
        if (str_contains($active['Locked']??'', '1')) { 
            $locked = $active['Locked']; // icon 
        } else{$locked ='';}
        $comments = $active['Comments'];
        $views    = $active['Views'];
        $expire   = $active['Expire']??'';

        $assignActivity[] = [
            're'        => $re++,
            'id'        => $id,
            'link'      => $link,
            'edit'      => $edit,
            'subject'   => $subject,
            'visible'   => $visible,
            'locked'    => $locked,
            'comments'  => $comments,
            'views'     => $views,
            'expire'    => $expire,
        ];
    } 
} 

// RENDER
xoops_cp_header();
include __DIR__ . '/mymenu.php';
$tpl = new XoopsTpl();
$tpl->assign(
	[
		'mydirname'         => $mod_name,
		'mod_name'          => $xoopsModule->getVar( 'name' ),
		'mod_url'           => XOOPS_URL . '/modules/' . $mod_name,
		'mod_imageurl'      => XOOPS_URL . '/modules/' . $mod_name . '/' . $xoopsModuleConfig['images_dir'],
		'mod_config'        => $xoopsModuleConfig,
        'totalCat'      =>$total['totalCat'],
        'totalContent'  =>$total['totalContent'],
        'totalRevision' =>$total['totalRevision'],
        'totalTags'     =>$total['totalTags'],
        'totalComments' =>$total['totalComments'],
        'totalVotes'    =>$total['totalVotes'],
        'totalApproval' =>$total['totalApproval'],
        'totalExtra'    =>$total['totalExtra'],
        'active'            => $assignActivity,
        'days'              => $days
	]
);

$tpl->display( 'db:' . $mod_name . '_admin_activity.html' );

require_once XOOPS_ROOT_PATH . "/footer.php";
