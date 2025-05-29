<?php
define('_MD_BANNERSTATS_ACTION', 'Action');
define('_MD_BANNERSTATS_ACTIVE_BANNERS', 'Active Banners');
define('_MD_BANNERSTATS_CHANGE_URL', 'Change URL');
define('_MD_BANNERSTATS_CHANGE_URL_TITLE', 'Change Banner URL');
define('_MD_BANNERSTATS_CLICKS', "Clicks");
define('_MD_BANNERSTATS_CONTACT_EMAIL', 'Your Email:');
define('_MD_BANNERSTATS_CONTACT_FORM_TITLE', 'Contact Support');
define('_MD_BANNERSTATS_CONTACT_MESSAGE', 'Message:');
define('_MD_BANNERSTATS_CONTACT_NAME', 'Your Name:');
define('_MD_BANNERSTATS_CONTACT_SENT_FAIL', 'Failed to send your message. Please try again later or contact the webmaster directly.');
define('_MD_BANNERSTATS_CONTACT_SENT_SUCCESS', 'Your message has been sent. Thank you.');
define('_MD_BANNERSTATS_CONTACT_SUBJECT', 'Subject:');
define('_MD_BANNERSTATS_CONTACT_SUPPORT', 'Contact Support');
define('_MD_BANNERSTATS_CTR', 'Click Thru %');
define('_MD_BANNERSTATS_CTR_LABEL', 'CTR');
define('_MD_BANNERSTATS_CTR_DESC', 'CTR (Site Data - Refer to Ad Service)');
define('_MD_BANNERSTATS_EMAIL_BANNER_NAME', "Banner Name");
define('_MD_BANNERSTATS_EMAIL_CLIENT_NAME', "Client Name");
define('_MD_BANNERSTATS_EMAIL_DATE_FINISHED', "Date Concluded");
define('_MD_BANNERSTATS_EMAIL_DEAR', "Dear");
define('_MD_BANNERSTATS_EMAIL_FINISHED_INTRO_CLIENT', "This is an automated notification to inform you that your banner campaign has concluded.");
define('_MD_BANNERSTATS_EMAIL_FINISH_REASON', "Reason for Conclusion");
define('_MD_BANNERSTATS_EMAIL_IMPRESSIONS_MADE', "Impressions Served");
define('_MD_BANNERSTATS_EMAIL_IMPRESSIONS_REMAINING', "Impressions Remaining");
define('_MD_BANNERSTATS_EMAIL_IMPRESSIONS_TOTAL', "Total Impressions Purchased");
define('_MD_BANNERSTATS_EMAIL_LOW_IMP_INTRO_CLIENT', "This is an automated notification to inform you that your banner campaign is running low on impressions.");
define('_MD_BANNERSTATS_EMAIL_LOW_IMP_SUBJECT_ADMIN', 'Admin Alert: Banner "%s" (Client: %s) Low on Impressions');
define('_MD_BANNERSTATS_EMAIL_LOW_IMP_SUBJECT_CLIENT', 'Alert: Your Banner Campaign "%s" is Low on Impressions');
define('_MD_BANNERSTATS_EMAIL_STATS', 'Email stats');
// define('_MD_BANNERSTATS_EMAIL_STATS_LINK_TEXT', "View Full Statistics");
define('_MD_BANNERSTATS_EMAIL_ADMIN_LINK_TEXT', "Manage Banner (Admin)");
define('_MD_BANNERSTATS_EMAIL_ADMIN_REVIEW_NEEDED', 'Admin Review Needed');
define('_MD_BANNERSTATS_EMAIL_SENT_FAIL', 'Failed to send statistics email. Please contact the webmaster.');
define('_MD_BANNERSTATS_EMAIL_SENT_SUCCESS', 'Statistics have been emailed to your registered address.');
define('_MD_BANNERSTATS_ERR_BANNER_ID_REQUIRED', 'Please select or enter a valid Banner ID for this request type.');
define('_MD_BANNERSTATS_ERR_BANNER_ADTAG_LINK', 'Ad-Tag banners cannot have their URL changed directly.');
//define('_MD_BANNERSTATS_ERROR_INVALID_LOGIN_PASS', 'INVALID_LOGIN_PASS');
// define('_MD_BANNERSTATS_ERROR_LOGIN_REQUIRED', 'Login is required,');
define('_MD_BANNERSTATS_ERR_MAILER','ERROR: Could not initialize the mailer service.');
define('_MD_BANNERSTATS_ERR_NAME_REQUIRED', 'Name is required.');
define('_MD_BANNERSTATS_ERR_SUPPORT_SENT', 'There was an error sending your request. Please try again later.');
// define('_MD_BANNERSTATS_ERR_TOKEN_INVALID', 'ERROR: Token is invalid.');

define('_MD_BANNERSTATS_FINISHED_BANNERS', 'Previously Finished Banners');
define('_MD_BANNERSTATS_DATE_START', 'Start Date');
define('_MD_BANNERSTATS_DATE_END', 'Date End');
define('_MD_BANNERSTATS_DATE_FINISHED', 'Finished');

define('_MD_BANNERSTATS_IMPMADE', 'Impressions Made');
define('_MD_BANNERSTATS_IMPRESSIONS', 'Impressions');
define('_MD_BANNERSTATS_IMPRESSIONS_DESC', 'Display Opportunities (Site Count)');
define('_MD_BANNERSTATS_IMPLEFT', 'Leftover');
define('_MD_BANNERSTATS_IMPLEFT_DESC', 'Display Opportunities Remaining (Site Limit)');
define('_MD_BANNERSTATS_IMPTOTAL', 'Total');
define('_MD_BANNERSTATS_IMPTOTAL_DESC', 'Total Display Opportunities (Site Limit)');

define('_MD_BANNERSTATS_LOGIN_PASSWORD', 'Password:');
define('_MD_BANNERSTATS_LOGIN_FORM_TITLE', 'Client Login');
define('_MD_BANNERSTATS_LOGIN_USERNAME', 'Username:');
define('_MD_BANNERSTATS_LOGOUT', 'Logout');
define('_MD_BANNERSTATS_NO_ACTIVE_BANNERS', 'You have no active banners at this time.');
define('_MD_BANNERSTATS_NO_BANNERS', 'You have no active banners at this time.');
define('_MD_BANNERSTATS_NO_FINISHED_BANNERS', 'You have no previously finished banners.');
define('_MD_BANNERSTATS_REQ_NEW_BANNER', 'Request New Banner Setup');
define('_MD_BANNERSTATS_REQ_OTHER', 'Other Reason');
define('_MD_BANNERSTATS_REQ_PROBLEM', 'Report Problem with Existing Banner');
define('_MD_BANNERSTATS_REQ_QUESTION', 'General Question about Banners');
define('_MD_BANNERSTATS_REQ_UPDATE_CODE', 'Update Ad Code for Existing Banner');
define('_MD_BANNERSTATS_SELECT_BANNER', '-- Select Banner --');
define('_MD_BANNERSTATS_URL_NOT_CHANGED', 'Banner URL is the same, no update performed.');
define('_MD_BANNERSTATS_URL_UPDATE_FAIL', 'Failed to update banner URL.');
define('_MD_BANNERSTATS_URL_UPDATE_SUCCESS', 'Banner URL updated successfully.');
define('_MD_BANNERSTATS_URL_UPDATE_NO_CHANGE', 'Banner URL is the same, no update performed.');
define('_MD_BANNERSTATS_INVALID_TOKEN', 'Invalid security token. Please try again.');
define('_MD_BANNERSTATS_BANNER_NOT_FOUND', 'Banner not found or you do not have permission to access it.');

// email content
define('_MB_BANNERSTATS_MAILMSG', "Dear %s,\n\nHere are the statistics for your banner '%s' on %s:\n\nImpressions Made: %d\nClicks Received: %d\n\nThank you.");
define('_MB_BANNERSTATS_SUBJECT', 'Banner Statistics for %s'); // %s will be banner identifier
define('_MD_BANNERSTATS_EMAIL_FINISHED_SUBJECT_CLIENT', 'Notification: Your Banner Campaign "%s" Has Concluded');
define('_MD_BANNERSTATS_EMAIL_FINISHED_SUBJECT_ADMIN', 'Admin Notification: Banner "%s" (Client: %s) Has Finished');

define('_MD_BANNERSTATS_MANAGE_URL', 'Manage URL');
define('_MD_BANNERSTATS_FINISH_IMPRESSIONS_DETAIL', 'Impressions Reached (%d of %d)');
define('_MD_BANNERSTATS_FINISH_BY_USER', '%s by %s');
define('_MD_BANNERSTATS_FINISH_DATE_EXPIRED', 'Date Expired (%s)');
define('_MD_BANNERSTATS_FINISH_MANUAL', 'Manually Finished');
define('_MD_BANNERSTATS_FINISH_IMPRESSIONS', 'Impressions Reached');
define('_MD_BANNERSTATS_FINISH_ADMIN', 'Admin Terminated');
define('_MD_BANNERSTATS_FINISH_CLIENT', 'Client Terminated');
define('_MD_BANNERSTATS_FINISH_OTHER', 'Other Reason');

define('_MD_BANNERSTATS_EMAIL_THANK_YOU', "Thank you,");
define('_MD_BANNERSTATS_EMAIL_SITENAME_TEAM', "The %s Team"); // %s: Site Name
define('_MD_BANNERSTATS_REQUEST_SUPPORT_TITLE', 'Other Reason');

define('_MD_BANNERSTATS_ERR_NAME_REQUIRE', 'Your name is required.');
define('_MD_BANNERSTATS_ERR_EMAIL_REQUIRED', 'A valid email address is required.');
define('_MD_BANNERSTATS_ERR_REQ_TYPE_REQUIRED', 'Please select a request type.');
define('_MD_BANNERSTATS_ERR_SUBJECT_REQUIRED', 'Subject is required.');
define('_MD_BANNERSTATS_ERR_MESSAGE_REQUIRED', 'Message is required.');

define('_MD_BANNERSTATS_URL_UPDATED', 'Banner URL updated successfully.');
define('_MD_BANNERSTATS_MISSING_BID', 'Banner ID is missing or invalid.');
define('_MD_BANNERSTATS_INVALID_URL', 'The new URL is invalid.');
define('_MD_BANNERSTATS_URL_UPDATE_FAILED', 'Failed to update banner URL.');
