<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class User_AdminLangPlusPreload extends XCube_ActionFilter
{
	function preBlockFilter()
	{
	//common and avatar management
	if (!defined('_AD_USER_LANG_UPLOAD')) {
	define('_AD_USER_LANG_UPLOAD', "Upload");
	}
	if (!defined('_AD_USER_ERROR_REQUIRED')) {
	define('_AD_USER_ERROR_REQUIRED', "{0} is required.");
	}
	if (!defined('_AD_USER_ERROR_DBUPDATE_FAILED')) {
	define('_AD_USER_ERROR_DBUPDATE_FAILED', "Database update failed.");
	}
	if (!defined('_AD_USER_ERROR_EXTENSION_IS_WRONG')) { 
	define('_AD_USER_ERROR_EXTENSION_IS_WRONG', "The extension of the uploaded file is invalid.");
	}
	if (!defined('_AD_USER_LANG_AVATAR_UPLOAD')) {
	define('_AD_USER_LANG_AVATAR_UPLOAD', "Avatar Batch-Upload");
	}
	if (!defined('_AD_USER_TIPS_AVATAR_UPLOAD')) {
	define('_AD_USER_TIPS_AVATAR_UPLOAD', "You can easily register many avatars by uploading Archive file including them! <br />This batch-upload doesn't check Length and File-Size of each avatar!<br />Please pre-adjust them before you archive them!<br />(Only tar.gz or zip archive)");
	}
	if (!defined('_AD_USER_LANG_AVATAR_UPLOAD_FILE')) {
	define('_AD_USER_LANG_AVATAR_UPLOAD_FILE', "Avatars Archive(Only tar.gz or zip)");
	}
	if (!defined('_AD_USER_LANG_AVATAR_UPLOAD_RESULT')) {
	define('_AD_USER_LANG_AVATAR_UPLOAD_RESULT', "Result of Avatar Batch-Upload");
	}
	if (!defined('_AD_USER_ERROR_COULD_NOT_SAVE_AVATAR_FILE')) {
	define('_AD_USER_ERROR_COULD_NOT_SAVE_AVATAR_FILE', "Could not save avatar file '{0}'");
	}
	if (!defined('_AD_USER_LANG_AVATAR_TOTAL')) {
	define('_AD_USER_LANG_AVATAR_TOTAL', "Total of Avatar(s)");
	}
	//
	if (!defined('_AD_USER_LANG_AVATAR_UPDATECONF')) {
	define('_AD_USER_LANG_AVATAR_UPDATECONF', "Confirm avatar update");
	}
	if (!defined('_AD_USER_MESSAGE_CONFIRM_UPDATE_AVATAR')) {
	define('_AD_USER_MESSAGE_CONFIRM_UPDATE_AVATAR', "Are you sure you want to update it?");
	}
	if (!defined('_AD_USER_LANG_AVATAR_MYTIPS')) {
	define('_AD_USER_LANG_AVATAR_MYTIPS', "Please write down your tips here!<br />( Customize _AD_USER_LANG_AVATAR_MYTIPS !)");
	}
	//user
	if (!defined('_AD_USER_LANG_FOUNDUSERS')) {
	define('_AD_USER_LANG_FOUNDUSERS', "Total of Found user(s)");
	}
	if (!defined('_AD_USER_LANG_USER_TOTAL')) {
	define('_AD_USER_LANG_USER_TOTAL', "Total of User(s)");
	}
	if (!defined('_AD_USER_LANG_TOTAL')) {
	define('_AD_USER_LANG_TOTAL', "Total");
	}
	//
	if (!defined('_AD_USER_LANG_USER_UPDATECONF')) {
	define('_AD_USER_LANG_USER_UPDATECONF', "Confirm user update");
	}
	if (!defined('_AD_USER_MESSAGE_CONFIRM_UPDATE_USER')) {
	define('_AD_USER_MESSAGE_CONFIRM_UPDATE_USER', "Are you sure you want to update it?");
	}
	if (!defined('_AD_USER_LANG_USER_MYTIPS')) {
	define('_AD_USER_LANG_USER_MYTIPS', "Please write down your tips here!<br />( Customize _AD_USER_LANG_USER_MYTIPS !)");
	}
	if (!defined('_AD_USER_LANG_USER_SEARCH_MYTIPS')) {
	define('_AD_USER_LANG_USER_SEARCH_MYTIPS', "Please write down your tips here!<br />( Customize _AD_USER_LANG_USER_SEARCH_MYTIPS !)");
	}
	//rank
	if (!defined('_AD_USER_LANG_RANK_TOTAL')) {
	define('_AD_USER_LANG_RANK_TOTAL', "Total of Rank(s)");
	}
	if (!defined('_AD_USER_LANG_RANK_UPDATECONF')) {
	define('_AD_USER_LANG_RANK_UPDATECONF', "Confirm rank update");
	}
	if (!defined('_AD_USER_MESSAGE_CONFIRM_UPDATE_RANK')) {
	define('_AD_USER_MESSAGE_CONFIRM_UPDATE_RANK', "Are you sure you want to update it?");
	}
	if (!defined('_AD_USER_LANG_RANK_MYTIPS')) {
	define('_AD_USER_LANG_RANK_MYTIPS', "Please write down your tips here!<br />( Customize _AD_USER_LANG_RANK_MYTIPS !)");
	}

	}
	
}

?>