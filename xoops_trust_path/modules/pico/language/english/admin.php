<?php

// Altsys admin menu and breadcrumbs
define( '_MD_A_MYMENU_MYTPLSADMIN' , 'Templates');
define( '_MD_A_MYMENU_MYBLOCKSADMIN' , 'Blocks');
define( '_MD_A_MYMENU_MYPREFERENCES' , 'Preferences');

// contents list admin
define( '_MD_A_PICO_H2_CONTENTS' , 'Content list');
define( '_MD_A_PICO_TH_CONTENTSID' , 'ID');
define( '_MD_A_PICO_TH_CONTENTSSUBJECT' , 'Subject');
define( '_MD_A_PICO_TH_CONTENTSWEIGHT' , 'Order');
define( '_MD_A_PICO_TH_CONTENTSVISIBLE' , 'VIS');
define( '_MD_A_PICO_TH_CONTENTSSHOWINNAVI' , 'NAVI');
define( '_MD_A_PICO_TH_CONTENTSSHOWINMENU' , 'MENU');
define( '_MD_A_PICO_TH_CONTENTSALLOWCOMMENT' , 'COM');
define( '_MD_A_PICO_TH_CONTENTSFILTERS' , 'Filters');
define( '_MD_A_PICO_TH_CONTENTSACTIONS' , 'Action');
define( '_MD_A_PICO_LEGEND_CONTENTSTHS' , 'VIS: visible &nbsp; NAVI:show in page navigation &nbsp; MENU:show in menu &nbsp; COM:commentable');
define( '_MD_A_PICO_BTN_MOVE' , 'Move');
define( '_MD_A_PICO_LABEL_CONTENTSRIGHTCHECKED' , 'Batch action for selected items');
define( '_MD_A_PICO_MSG_CONTENTSMOVED' , 'Contents have been moved');
define( '_MD_A_PICO_LABEL_MAINDISP' , 'View');
define( '_MD_A_PICO_BTN_DELETE' , 'Delete');
define( '_MD_A_PICO_CONFIRM_DELETE' , 'Are you sure to delete them?');
define( '_MD_A_PICO_MSG_CONTENTSDELETED' , 'Deleted successfully');
define( '_MD_A_PICO_BTN_EXPORT' , 'Export');
define( '_MD_A_PICO_CONFIRM_EXPORT' , 'Selected items are exported as the top contents of the module. Comments will not be copied. Please confirm to export the data');
define( '_MD_A_PICO_MSG_CONTENTSEXPORTED' , 'Exported successfully');
define( '_MD_A_PICO_MSG_FMT_DUPLICATEDVPATH' , 'Some contents have not been updated because of duplicated vpath (ID: %s)');

// category_access
define( '_MD_A_PICO_LABEL_SELECTCATEGORY' , 'Select a category');
define( '_MD_A_PICO_H2_INDEPENDENTPERMISSION' , 'Create unique set of permissions');
define( '_MD_A_PICO_LABEL_INDEPENDENTPERMISSION' , 'This item is currently inheriting permissions from the parent. You can select the check box and submit to customize unique permissions for this category.');
define( '_MD_A_PICO_LINK_CATPERMISSIONID' , 'Verify inherited permissions from parent category.');
define( '_MD_A_PICO_H2_GROUPPERMS' , 'Group Permissions');
define( '_MD_A_PICO_H2_USERPERMS' , 'User Permissions');
define( '_MD_A_PICO_TH_UID' , 'uid');
define( '_MD_A_PICO_TH_UNAME' , 'uname');
define( '_MD_A_PICO_TH_GROUPNAME' , 'Group name');
define( '_MD_A_PICO_NOTICE_ADDUSERS' , 'You can grant or deny permissions to specific users.<br>Add the <b>uid</b> or <b>uname</b> of the user, and then assign permissions.');

// import
define( '_MD_A_PICO_H2_IMPORTFROM' , 'Import');
define( '_MD_A_PICO_LABEL_SELECTMODULE' , 'Select a module');
define( '_MD_A_PICO_BTN_DOIMPORT' , 'Import Data');
define( '_MD_A_PICO_CONFIRM_DOIMPORT' , 'Please confirm!');
define( '_MD_A_PICO_MSG_IMPORTDONE' , 'Imported successfully');
define( '_MD_A_PICO_ERR_INVALIDMID' , 'You\'ve specified a wrong module to be imported');
define( '_MD_A_PICO_ERR_SQLONIMPORT' , 'Faild to import. You have to check the version of each module.');
define( '_MD_A_PICO_HELP_IMPORTFROM' , 'You can import from Pico and TinyD instances. It is recommended to check permissions. Note this will reset any data and settings of the instance importing data!');
define( '_MD_A_PICO_H2_SYNCALL' , 'Synchronize redundant data');
define( '_MD_A_PICO_BTN_DOSYNCALL' , 'Synchronize Data');
define( '_MD_A_PICO_MSG_SYNCALLDONE' , 'Synchronized successfully');
define( '_MD_A_PICO_HELP_SYNCALL' , 'Execute to solve data inconsistency, for example after importing data from other instances.');
define( '_MD_A_PICO_H2_CLEARBODYCACHE' , 'Clear compiled cache');
define( '_MD_A_PICO_BTN_DOCLEARBODYCACHE' , 'Clear Cache');
define( '_MD_A_PICO_MSG_CLEARBODYCACHEDONE' , 'All compiled files in cache have been removed.');
define( '_MD_A_PICO_HELP_CLEARBODYCACHE' , 'Execute to remove compiled files and solve cache issues, for example, after moving a website.');

// extras
define( '_MD_A_PICO_H2_EXTRAS' , 'Extra Forms');
define( '_MD_A_PICO_TH_ID' , 'ID');
define( '_MD_A_PICO_TH_TYPE' , 'Type');
define( '_MD_A_PICO_TH_SUMMARY' , 'Summary');
define( '_MD_A_PICO_LINK_DETAIL' , 'Detail');
define( '_MD_A_PICO_LINK_EXTRACT' , 'Delete');
define( '_MD_A_PICO_LABEL_SEARCHBYPHRASE' , 'Search by phrase');
define( '_MD_A_PICO_TH_EXTRASACTIONS' , 'Action');
define( '_MD_A_PICO_LABEL_EXTRASRIGHTCHECKED' , 'Batch action for selected items');
define( '_MD_A_PICO_BTN_CSVOUTPUT' , 'CSV output');
define( '_MD_A_PICO_MSG_DELETED' , 'Deleted successfully');

// tags
define( '_MD_A_PICO_H2_TAGS' , 'Tag Manager');
define( '_MD_A_PICO_TH_TAG' , 'Tag');
define( '_MD_A_PICO_TH_USED' , 'Used');
define( '_MD_A_PICO_LABEL_ORDER' , 'Order');

// tips
define( '_MD_A_PICO_TIPS_CONTENTS' , 'Content Tips');
define( '_MD_A_PICO_TIPS_TAGS' , 'Tags Tips');
define( '_MD_A_PICO_TIPS_EXTRAS' , 'Extras Tips');

// ACTIVITY
define( '_MD_A_PICO_ACTIVITY_OVERVIEW' , 'Activity Overview');
define( '_MD_A_PICO_ACTIVITY_SCHEDULE' , 'Expired and scheduled content');
define( '_MD_A_PICO_ACTIVITY_INTERVAL' , 'days of interval before and after today');
define( '_MD_A_PICO_ACTIVITY_LATEST' , 'latest scheduled contents');
define( '_MD_A_PICO_ITEMS_PER_PAGE' , 'Items to display');