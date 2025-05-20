<?php
//bannerstats

// Login Page
define('_MD_BANNERSTATS_LOGIN_FORM_TITLE', 'Client Login');
define('_MD_BANNERSTATS_LOGIN_USERNAME', 'Username:');
define('_MD_BANNERSTATS_LOGIN_PASSWORD', 'Password:');
define('_MD_BANNERSTATS_CONTACT_WEBMASTER', 'Contact Webmaster');

// Stats Page
define('_MD_BANNERSTATS_LOGOUT', 'Logout');
define('_MD_BANNERSTATS_ACTIVE_BANNERS', 'Current Active Banners');
define('_MD_BANNERSTATS_BANNER', 'Banner');
define('_MD_BANNERSTATS_IMPRESSIONS', 'Impressions');
define('_MD_BANNERSTATS_IMPRESSIONS_DESC', 'Display Opportunities (Site Count)');
define('_MD_BANNERSTATS_IMPTOTAL', 'Total');
define('_MD_BANNERSTATS_IMPTOTAL_DESC', 'Total Display Opportunities (Site Limit)');
define('_MD_BANNERSTATS_CLICKS', 'Clicks');
define('_MD_BANNERSTATS_CLICKS_DESC', 'Clicks (Site Count - Refer to Ad Service) Clicks Received');
define('_MD_BANNERSTATS_CTR', 'Click Thru %');
define('_MD_BANNERSTATS_CTR_DESC', 'CTR (Site Data - Refer to Ad Service)');

define('_MD_BANNERSTATS_IMPLEFT', 'Leftover');
define('_MD_BANNERSTATS_IMPLEFT_DESC', 'Display Opportunities Remaining (Site Limit)');

define('_MD_BANNERSTATS_ACTIONS', 'Actions');
define('_MD_BANNERSTATS_EMAIL_STATS', 'Email Stats');
define('_MD_BANNERSTATS_CHANGE_URL', 'Change URL');
define('_MD_BANNERSTATS_NO_ACTIVE_BANNERS', 'You have no active banners at this time.');
define('_MD_BANNERSTATS_MANAGE_URL', 'Manage URL');
define('_MD_BANNERSTATS_FINISHED_BANNERS', 'Previously Finished Banners');
define('_MD_BANNERSTATS_DATE_START', 'Start Date');
define('_MD_BANNERSTATS_DATE_END', 'End Date');
define('_MD_BANNERSTATS_NO_FINISHED_BANNERS', 'You have no previously finished banners.');
define('_MD_BANNERSTATS_HTML_BANNER_NOTICE', 'This is an HTML banner. 
The click URL is typically embedded within the HTML code itself and cannot be changed through this form. 
If you need to modify the destination, the banner\'s HTML code usually needs to be updated by an administrator or through your ad service provider.');
// ContactAction, EmailStatsAction, ChangeUrlAction messages
define('_MD_BANNERSTATS_EMAIL_SENT_SUCCESS', 'Statistics have been emailed to your registered address.');
define('_MD_BANNERSTATS_EMAIL_SENT_FAIL', 'Failed to send statistics email. Please contact the webmaster.');
define('_MD_BANNERSTATS_URL_UPDATE_SUCCESS', 'Banner URL updated successfully.');
define('_MD_BANNERSTATS_URL_UPDATE_FAIL', 'Failed to update banner URL. It might be an HTML banner or an invalid request.');
define('_MD_BANNERSTATS_URL_UPDATE_NO_CHANGE', 'Banner URL is the same, no update performed.');
define('_MD_BANNERSTATS_CONTACT_FORM_TITLE', 'Contact Webmaster');
define('_MD_BANNERSTATS_CONTACT_NAME', 'Your Name:');
define('_MD_BANNERSTATS_CONTACT_EMAIL', 'Your Email:');
define('_MD_BANNERSTATS_CONTACT_SUBJECT', 'Subject:');
define('_MD_BANNERSTATS_CONTACT_MESSAGE', 'Message:');
define('_MD_BANNERSTATS_CONTACT_SENT_SUCCESS', 'Your message has been sent. Thank you.');
define('_MD_BANNERSTATS_CONTACT_SENT_FAIL', 'Failed to send your message. Please try again later or contact the webmaster directly.');
define('_MD_BANNERSTATS_INVALID_TOKEN', 'Invalid security token. Please try again.');
define('_MD_BANNERSTATS_BANNER_NOT_FOUND', 'Banner not found or you do not have permission to access it.');

// For Email Stats Action
// define('_MD_BANNERSTATS_EMAIL_SENT_SUCCESS', 'Statistics have been emailed to your registered address.');
// define('_MD_BANNERSTATS_EMAIL_SENT_FAIL', 'Failed to send statistics email. Please contact the webmaster.');
// define('_MD_BANNERSTATS_INVALID_TOKEN', 'Invalid security token. Please try again or refresh the page.');
// define('_MD_BANNERSTATS_BANNER_NOT_FOUND', 'Banner not found or you do not have permission to access it.');

// For email content
define('_MB_BANNERSTATS_SUBJECT', 'Banner Statistics for %s'); // %s will be banner identifier
define('_MB_BANNERSTATS_MAILMSG', "Dear %s,\n\nHere are the statistics for your banner '%s' on %s:\n\nImpressions Made: %d\nClicks Received: %d\n\nThank you.");
// %s placeholders: client name, banner identifier, sitename, impressions, clicks

define('_MD_BANNERSTATS_SELECT_BANNER', '-- Select Banner --');
define('_MD_BANNERSTATS_ERR_BANNER_ID_REQUIRED', 'Please select or enter a valid Banner ID for this request type.'); // Added for validation

