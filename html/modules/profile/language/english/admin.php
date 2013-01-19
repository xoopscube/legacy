<?php
/**
 * 2012-5-2: Update by Yoshi.Sakai
 */
define("_AD_PROFILE_LANG_DEFINITIONS_DELETE","Remove field");
define("_AD_PROFILE_LANG_DEFINITIONS_EDIT","Edit field");
define("_AD_PROFILE_LANG_DEFINITIONS_VIEW","Display field");
define("_AD_PROFILE_TIPS_DATA_DOWNLOAD", "You are only able to get CSV User data order by user_id.");
define("_AD_PROFILE_DATA_NUM", "%d users are registered.");
define("_AD_PROFILE_DATA_DOWNLOAD_DO", "Download by CSV");
define('_AD_PROFILE_DESC_FIELD_SELECTBOX', 'Set options by dividing |');
define('_AD_PROFILE_DESC_FIELD_CHECKBOX', 'Set the display string when "checked" and "unchecked", divided by |. When empty, "'._YES.'" and "'._NO.'" is used.');
define('_AD_PROFILE_DESC_FIELD_STRING', 'Set the default value.');
define('_AD_PROFILE_DESC_FIELD_INT', 'Set the default value.');
define('_AD_PROFILE_DESC_FIELD_FLOAT', 'Set the default value.');
define('_AD_PROFILE_DESC_FIELD_TEXT', 'Select "html" if you use wysiwyg editor(required wysiwig editor module).');
define('_AD_PROFILE_DESC_FIELD_CATEGORY', 'Select LEGACY_CATEGORY module\'s dirname.');

define('_AD_PROFILE_TIPS1_DATA_UPLOAD', 'The profile batch registration with CSV file is possible.');
define('_AD_PROFILE_TIPS2_DATA_UPLOAD', 'Use CSV file downloaded from <a href="?action=UserDataDownload" style="color:#941d55;font-weight:bold;">'._MI_PROFILE_DATA_DOWNLOAD.'</a> Do not increase and decrease columns.');
define('_AD_PROFILE_TIPS3_DATA_UPLOAD', 'Please describe only the user who wants to update and wants to register information newly in CSV file.');
define('_AD_PROFILE_TIPS4_DATA_UPLOAD', 'When the row of leftmost UID is emptied(or 0), it would not work.');
define('_AD_PROFILE_TIPS5_DATA_UPLOAD', 'The profile information is updated when there is a value of the row of leftmost(UID).');
define('_AD_PROFILE_DATA_UPLOAD_DONE', 'The profile data was updated according to CSV data.');
define('_AD_PROFILE_DATA_UPLOAD_SELECT_CSVFILE', 'Please select the file of registered CSV.');
define('_AD_PROFILE_DATA_UPLOAD_CONF', 'Confirm the content of registration');
define('_AD_PROFILE_DATA_UPLOAD_DO', 'Register');

define('_AD_USER_DATA_UPLOAD_BACK', 'Select The CSV file again');
define('_AD_USER_DATA_UPLOAD_CHECK_USER_CSVFILE', 'Please confirm the content of registration.');
?>