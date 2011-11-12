<?php

require XOOPS_ROOT_PATH.'/class/xoopsformloader.php';
require_once dirname(dirname(__FILE__))."/class/formselecttime.php";

$time = time();

$form = new XoopsThemeForm(_MD_SUBMITNEWS, 'storyform', '?page=submit');

if( $storyid > 0 ){
	if( $story->isActiveUser() ){
		$form->addElement(new XoopsFormLabel(_MD_POSTEDBY, $story->getUname()));
	}else{
		$form->addElement(new XoopsFormLabel(_MD_POSTEDBY, $story->getUname().'&nbsp;('.$story->getVar('hostname').')'));
	}
}else{
	$form->addElement(new XoopsFormLabel(_MD_POSTEDBY, XoopsUser::getUnameFromId($my_uid) ));
}

$form->addElement(new XoopsFormText(_MD_TITLE, 'title', 50, 255, $story->getVar('title', 'f') ), true); // GIJ
//$topic_select = new XoopsFormSelect(_MD_TOPIC, 'topicid', $story->getVar('topicid') );
//$topic_select->addOptionArray(Bulletin::makeCategoryArrayForSelect( $mydirname ));
$bt = new BulletinTopic( $mydirname ) ; // GIJ
$topic_select = new XoopsFormLabel( _MD_TOPIC , $bt->makeTopicSelBox( false , $story->getVar('topicid') , 'topicid' ) ) ; // GIJ
$form->addElement($topic_select);

$topicalign_select = new XoopsFormSelect(_MD_TOPIC_IMAGE, 'topicimg', $story->getVar('topicimg') );
$topicalign_select->addOptionArray(array('1' => _MD_TOPIC_RIGHT, '2' => _MD_TOPIC_LEFT, '0' => _MD_TOPIC_DISABLE));
$form->addElement($topicalign_select);

// bodytext
$bodytext_tray = new XoopsFormElementTray(_MD_THESCOOP, '');
if( empty( $xoopsModuleConfig['use_fckeditor'] ) || ! $gperm->group_perm(4) ) {
	// XoopsForm Dhtml
	$bodytext_tray->addElement(new XoopsFormDhtmlTextArea('', 'text', $story->getVar('text', 'f'), $bulletin_post_tray_row, $bulletin_post_tray_col), true);
} else {
	// fckeditor
	$wysiwyg_header = '
		<script type="text/javascript" src="'.XOOPS_URL.'/common/fckeditor/fckeditor.js"></script>
		<script type="text/javascript"><!--
			function fckeditor_exec() {
				var oFCKeditor = new FCKeditor( "bodytext" , "100%" , "500" , "Default" );
				
				oFCKeditor.BasePath = "'.XOOPS_URL.'/common/fckeditor/";
				
				oFCKeditor.ReplaceTextarea();
			}
		// --></script>
	' ;
	$xoopsTpl->assign( 'xoops_module_header' , $xoopsTpl->get_template_vars( "xoops_module_header" ) . "\n" . $wysiwyg_header ) ;
	$bodytext_tray->addElement(new XoopsFormLabel('', '<textarea id="bodytext" name="text">'.$story->getVar('text', 'f').'</textarea><script>fckeditor_exec();</script>' ));
}
$bodytext_tray->addElement(new XoopsFormLabel('', '<div>'._MULTIPAGE.'</div>'));
$form->addElement($bodytext_tray);


// if user has right to set date.
if( $gperm->group_perm(3) ){
	// autodate
	$autodate_tray = new XoopsFormElementTray(_MD_PUBLISHED, '');
	$autodate_checkbox = new XoopsFormCheckBox('', 'autodate', $story->getVar('autodate') );
	$autodate_checkbox->addOption(1, _MD_SETDATETIME);
	$autodate_tray->addElement($autodate_checkbox);
	$autodate_tray->addElement(new XoopsFormLabel('', '<span style="font-size:0.8em;">'._MD_SETDATETIME_DESC.'</span><br />'));
	$autodate_tray->addElement(new XoopsFormSelectTime('', 'auto', $auto, _MD_DATE_FORMAT));
	$form->addElement($autodate_tray);

	// auto expire
	$autoexpdate_tray = new XoopsFormElementTray(_MD_EXPIRED, '');
	$autoexpdate_checkbox = new XoopsFormCheckBox('', 'autoexpdate', $story->getVar('autoexpdate') );
	$autoexpdate_checkbox->addOption(1, _MD_SETEXPDATETIME);
	$autoexpdate_tray->addElement($autoexpdate_checkbox);
	$autoexpdate_tray->addElement(new XoopsFormLabel('', '<span style="font-size:0.8em;">'._MD_SETEXPDATETIME_DESC.'</span><br />'));
	$autoexpdate_tray->addElement(new XoopsFormSelectTime('', 'autoexp', $autoexp, _MD_DATE_FORMAT));
	$form->addElement($autoexpdate_tray);
}

// relation
if( $gperm->group_perm(7) && $bulletin_use_relations ){
	$relation_content  = '<div id="relation">';
	foreach( $relations as $relation ){
		$relation_content .= '<input type="checkbox" name="storyidR[]" value="'.intval($relation['linkedid']).'" />';
		$relation_content .= '<input type="hidden" name="titleR[]" value="'.$story->getRelatedTitle($relation['linkedid'], $relation['dirname']).'" />';
		$relation_content .= '<input type="hidden" name="dirnameR[]" value="'.htmlspecialchars($relation['dirname']).'" />';
		$relation_content .= '<input type="hidden" name="storyidRH[]" value="'.intval($relation['linkedid']).'" />';
		$relation_content .= ''.$story->getRelatedTitle($relation['linkedid'], $relation['dirname']).'<br />';
	}
	$relation_content .= '</div>';
	$relation_content .= '<input type="button" value="'._MD_ADD_RELATION.'" name="opensub" onclick="window.open(\'index.php?page=search\',\'sub\',\'width=400,height=500\');">';
	$relation_content .= '<input type="button" value="'._DELETE.'" name="updatevar" onclick="updateRelations(\'storyform\', false)">';
	$form->addElement(new XoopsFormLabel(_MD_RELATION, $relation_content));
}

// options
$option_tray = new XoopsFormElementTray(_OPTIONS,'<br />');

// event notifaction (if mode is edit, not display.)
if ($xoopsUser && $storyid == 0 && !$gperm->group_perm(2)) {
	$notify_checkbox = new XoopsFormCheckBox('', 'notifypub', $story->getVar('notifypub') );
	$notify_checkbox->addOption(1, _MD_NOTIFYPUBLISH);
	$option_tray->addElement($notify_checkbox);
}

// If user have right to use HTML
if( $gperm->group_perm(4) ){
	$html_checkbox = new XoopsFormCheckBox('', 'html', $story->getVar('html') );
	$html_checkbox->addOption(1, _MD_USE_HTML);
	$option_tray->addElement($html_checkbox);
}

// br (GIJ)
$br_checkbox = new XoopsFormCheckBox('', 'br', $story->getVar('br') );
$br_checkbox->addOption(1, _MD_USE_BR);
$option_tray->addElement($br_checkbox);

$smiley_checkbox = new XoopsFormCheckBox('', 'smiley', $story->getVar('smiley') );
$smiley_checkbox->addOption(1, _MD_USE_SMILEY);
$option_tray->addElement($smiley_checkbox);
$xcode_checkbox = new XoopsFormCheckBox('', 'xcode', $story->getVar('xcode') );
$xcode_checkbox->addOption(1, _MD_USE_XCODE);
$option_tray->addElement($xcode_checkbox);
$block_checkbox = new XoopsFormCheckBox('', 'block', $story->getVar('block') );
$block_checkbox->addOption(1, _MD_DISP_BLOCK);
$option_tray->addElement($block_checkbox);
$ihome_checkbox = new XoopsFormCheckBox('', 'ihome', $story->getVar('ihome') );
$ihome_checkbox->addOption(1, _MD_PUBINHOME);
$option_tray->addElement($ihome_checkbox);
if( $gperm->group_perm(2) ){
	$approve_value = isset($_POST['approve']) ? $story->getVar('approve') : $story->getVar('type');
	$approve_checkbox = new XoopsFormCheckBox('', 'approve', $approve_value );
	$approve_checkbox->addOption(1, _MD_APPROVE);
	$option_tray->addElement($approve_checkbox);
}
$form->addElement($option_tray);

// for edit
if( $storyid > 0){
	$form->addElement(new XoopsFormHidden('storyid', $storyid));
}

if( $return == 1){
	$form->addElement(new XoopsFormHidden('return', 1));
}
$button_tray = new XoopsFormElementTray('' ,'');
$button_tray->addElement(new XoopsFormButton('', 'preview', _PREVIEW, 'submit'));
$button_tray->addElement(new XoopsFormButton('', 'post', _MD_POST, 'submit'));
$form->addElement($xoopsGTicket->getTicketXoopsForm( __LINE__ ));
$form->addElement($button_tray);
$form->display();

$xoopsTpl->assign('xoops_module_header', '<script type="text/javascript" src="'.$mydirurl.'/index.php?page=javascript"></script>' . $xoopsTpl->get_template_vars( "xoops_module_header" ));
?>