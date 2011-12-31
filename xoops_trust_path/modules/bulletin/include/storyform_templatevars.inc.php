<?php

require_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php' ;
require_once XOOPS_ROOT_PATH.'/class/xoopsform/formelement.php' ;
require_once dirname(dirname(__FILE__)).'/class/formselecttime.php' ;

if( $storyid > 0 ){
	if( $story->isActiveUser() ){
		$xoopsTpl->assign('poster', $story->getUname());
	}else{
		$xoopsTpl->assign('poster', $story->getUname().'&nbsp;('.$story->getVar('hostname').')');
	}
}else{
	$xoopsTpl->assign('poster', XoopsUser::getUnameFromId($my_uid));
}
$xoopsTpl->assign('title', $story->getVar('title', 'e'));
$xoopsTpl->assign('title_raw', $story->getVar('title', 'n'));
$xoopsTpl->assign('text', $story->getVar('text', 'e'));
$xoopsTpl->assign('text_raw', $story->getVar('text', 'n'));
$xoopsTpl->assign('bulletin_post_tray_row', $bulletin_post_tray_row);
$xoopsTpl->assign('bulletin_post_tray_col', $bulletin_post_tray_col);
$xoopsTpl->assign('topicid', $story->getVar('topicid'));
$xoopsTpl->assign('topicimg', $story->getVar('topicimg'));


// if user has right to set date.
if( $can_use_date = $gperm->group_perm(3) ){
	// autodate
	$xoopsTpl->assign('autodate', $story->getVar('autodate'));
	$post_date_xf = new XoopsFormSelectTime('', 'auto', $auto, _MD_DATE_FORMAT);
	$xoopsTpl->assign('post_date_selector', $post_date_xf->render());
	// auto expire
	$xoopsTpl->assign('autoexpdate', $story->getVar('autoexpdate'));
	$expire_date_xf = new XoopsFormSelectTime('', 'autoexp', $autoexp, _MD_DATE_FORMAT);
	$xoopsTpl->assign('expire_date_selector', $expire_date_xf->render());
}
$xoopsTpl->assign('can_use_date', $can_use_date);

// relation
if( $use_relation = $gperm->group_perm(7) && $bulletin_use_relations ){
	foreach( $relations as $key => $relation ){
		$relations[$key]['title'] = $story->getRelatedTitle($relation['linkedid'], $relation['dirname']);
	}
	$xoopsTpl->assign('relations', $relations);
}
$xoopsTpl->assign('use_relation', $use_relation);

// options
// event notifaction (if mode is edit, not display.)
//ver2.0
/*
if ($use_notify = ($xoopsUser && $storyid == 0 && !$gperm->group_perm(2))) {
	$xoopsTpl->assign('notifypub', $story->getVar('notifypub'));
}
*/
//ver3.0
$use_notify = false;
if ($xoopsUser && $storyid == 0 ) {
	if ( $gperm->group_perm(2) || !$gperm->proceed4topic("post_auto_approved",$story->getVar('topicid'))) {
		$use_notify = true;
	}
}
if ($use_notify) {
	$xoopsTpl->assign('notifypub', $story->getVar('notifypub'));
}
$xoopsTpl->assign('use_notify', $use_notify);


// If the user has a right to use HTML
$use_html = $gperm->group_perm(4);
$xoopsTpl->assign('use_html', $use_html);

// fckeditor
$use_fckeditor = ! empty( $xoopsModuleConfig['use_fckeditor'] ) && $use_html;
$common_fck_installed = file_exists( XOOPS_ROOT_PATH.'/common/fckeditor/fckeditor.js');
$xoopsTpl->assign( 'use_fckeditor' , $use_fckeditor ) ;
$xoopsTpl->assign( 'common_fck_installed' , $common_fck_installed ) ;

//initial option
if( ! isset($_POST['topicid']) && $story->getVar('topicid')==0 ){
	if ( $use_fckeditor && $common_fck_installed ){
		//for fckeditor
		$story->setVar('html', 1);
		$story->setVar('br', 0);
		$story->setVar('smiley', 0);
		$story->setVar('xcode', 0);
	}elseif ($use_html){
		//for other
		$story->setVar('html', 1);
		$story->setVar('br', 0);
		$story->setVar('smiley', 0);
		$story->setVar('xcode', 0);
	}
}

$xoopsTpl->assign('html', $story->getVar('html'));
// br (GIJ)
$xoopsTpl->assign('br', $story->getVar('br'));
$xoopsTpl->assign('smiley', $story->getVar('smiley'));
$xoopsTpl->assign('xcode', $story->getVar('xcode'));

$xoopsTpl->assign('block', $story->getVar('block'));
$xoopsTpl->assign('ihome', $story->getVar('ihome'));
$xoopsTpl->assign('html', $story->getVar('html'));
if( $use_approve = $gperm->group_perm(2) ){
	$approve_value = isset($_POST['approve']) ? $story->getVar('approve') : $story->getVar('type');
	$xoopsTpl->assign('approve', $approve_value);
}
$xoopsTpl->assign('use_approve', $use_approve);

// for edit
if( $storyid > 0){
	$xoopsTpl->assign('storyid', $storyid);
}

$xoopsTpl->assign('return', empty( $return ) ? 0 : 1 ) ;
$xoopsTpl->assign('gticket', $xoopsGTicket->getTicketHtml( __LINE__ ));

$mod_header = "
<script type=\"text/javascript\">
//<![CDATA[
jQuery(document).ready(function($){
    var stop = false;
    $ ( '.jquery-ui-accordion-title' )
        . click ( function ( event ){
            if ( stop ) {
                event . stopImmediatePropagation ();
                event . preventDefault ();
                stop = false;
            }
        } ) ;
    $ ( '#jquery-ui-accordion' )
        . accordion ( {
            icons: {
                'header': 'ui-icon-folder-collapsed',
                'headerSelected': 'ui-icon-folder-open'
            },
            header: '.jquery-ui-accordion-title'
        } )
        . sortable ( {
            axis: 'y',
            handle: '.jquery-ui-accordion-title',
            stop: function () {
                stop = true;
            }
        } ) ;
} ) ;
//]]>
</script>";


$xoopsTpl->assign('xoops_module_header', $mod_header . '<script type="text/javascript" src="'.$mydirurl.'/index.php?page=javascript"></script>' . $xoopsTpl->get_template_vars( "xoops_module_header" ));

?>