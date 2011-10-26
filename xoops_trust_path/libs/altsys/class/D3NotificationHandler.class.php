<?php

// notification handler for D3 modules

require_once XOOPS_ROOT_PATH . '/include/notification_functions.php' ;

class D3NotificationHandler {

function &getInstance( $conn = null )
{
	static $instance ;
	if( ! isset( $instance ) ) {
		$instance = new D3NotificationHandler() ;
	}
	return $instance ;
}


function getMailTemplateDir( $mydirname , $mytrustdirname = '' )
{
	global $xoopsConfig ;

	$mydirpath = XOOPS_ROOT_PATH.'/modules/'.$mydirname ;
	$mytrustdirpath = XOOPS_TRUST_PATH.'/modules/'.$mytrustdirname ;
	$language = empty( $xoopsConfig['language'] ) ? 'english' : $xoopsConfig['language'] ;

	$search_paths = array(
		"$mydirpath/language/$language/mail_template/" ,
		"$mytrustdirpath/language/$language/mail_template/" ,
		"$mydirpath/language/english/mail_template/" ,
		"$mytrustdirpath/language/english/mail_template/" ,
	) ;

	$mail_template_dir = "$mytrustdirpath/language/english/mail_template/" ;
	foreach( $search_paths as $path ) {
		if( file_exists( $path ) ) {
			$mail_template_dir = $path ;
			break ;
		}
	}

	return $mail_template_dir ;
}


function triggerEvent( $mydirname , $mytrustdirname , $category , $item_id , $event , $extra_tags=array() , $user_list=array() , $omit_user_id=null )
{
	$module_hanlder =& xoops_gethandler( 'module' ) ;
	$module =& $module_hanlder->getByDirname( $mydirname ) ;

	$notification_handler =& xoops_gethandler('notification') ;
	$mail_template_dir = $this->getMailTemplateDir( $mydirname , $mytrustdirname ) ;

	// calling a delegate before 
	if( class_exists( 'XCube_DelegateUtils' ) ) {
		$force_return = false ;
		XCube_DelegateUtils::raiseEvent( 'D3NotificationHandler.Trigger' , new XCube_Ref($category), new XCube_Ref($event), new XCube_Ref($item_id), new XCube_Ref($extra_tags), new XCube_Ref($module), new XCube_Ref($user_list), new XCube_Ref($omit_user_id), $module->getInfo( 'notification' ) , new XCube_Ref($force_return) , new XCube_Ref($mail_template_dir) , $mydirname , $mytrustdirname ) ;
		if( $force_return) return ;
	}

	$mid = $module->getVar('mid') ;

	// Check if event is enabled
	$config_handler =& xoops_gethandler('config');
	$mod_config =& $config_handler->getConfigsByCat(0,$mid);
	if (empty($mod_config['notification_enabled'])) {
		return false;
	}
	$category_info =& notificationCategoryInfo ($category, $mid);
	$event_info =& notificationEventInfo ($category, $event, $mid);
	if (!in_array(notificationGenerateConfig($category_info,$event_info,'option_name'),$mod_config['notification_events']) && empty($event_info['invisible'])) {
		return false;
	}

	if (!isset($omit_user_id)) {
		global $xoopsUser;
		if (!empty($xoopsUser)) {
			$omit_user_id = $xoopsUser->getVar('uid');
		} else {
			$omit_user_id = 0;
		}
	}
	$criteria = new CriteriaCompo();
	$criteria->add(new Criteria('not_modid', intval($mid)));
	$criteria->add(new Criteria('not_category', $category));
	$criteria->add(new Criteria('not_itemid', intval($item_id)));
	$criteria->add(new Criteria('not_event', $event));
	$mode_criteria = new CriteriaCompo();
	$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDALWAYS), 'OR');
	$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENDELETE), 'OR');
	$mode_criteria->add (new Criteria('not_mode', XOOPS_NOTIFICATION_MODE_SENDONCETHENWAIT), 'OR');
	$criteria->add($mode_criteria);
	if (!empty($user_list)) {
		$user_criteria = new CriteriaCompo();
		foreach ($user_list as $user) {
			$user_criteria->add (new Criteria('not_uid', $user), 'OR');
		}
		$criteria->add($user_criteria);
	}
	$notifications =& $notification_handler->getObjects($criteria);
	if (empty($notifications)) {
		return;
	}

	// Add some tag substitutions here
	$tags = array();
	// {X_ITEM_NAME} {X_ITEM_URL} {X_ITEM_TYPE} from lookup_func are disabled
	$tags['X_MODULE'] = $module->getVar('name','n');
	$tags['X_MODULE_URL'] = XOOPS_URL . '/modules/' . $module->getVar('dirname') . '/';
	$tags['X_NOTIFY_CATEGORY'] = $category;
	$tags['X_NOTIFY_EVENT'] = $event;

	$template = $event_info['mail_template'] . '.tpl';
	$subject = $event_info['mail_subject'];

	foreach ($notifications as $notification) {
		if (empty($omit_user_id) || $notification->getVar('not_uid') != $omit_user_id) {
			// user-specific tags
			//$tags['X_UNSUBSCRIBE_URL'] = 'TODO';
			// TODO: don't show unsubscribe link if it is 'one-time' ??
			$tags['X_UNSUBSCRIBE_URL'] = XOOPS_URL . '/notifications.php';
			$tags = array_merge ($tags, $extra_tags);

			$notification->notifyUser($mail_template_dir, $template, $subject, $tags);
		}
	}
}





}

?>