<?php

define('_MD_A_MYMENU_MYTPLSADMIN','Templates');
define('_MD_A_MYMENU_MYBLOCKSADMIN','Blocks/Permissions');
define('_MD_A_MYMENU_MYPREFERENCES','Preferences');

// forum_access and category_access
define('_MD_A_D3FORUM_LABEL_SELECTFORUM','Select a forum');
define('_MD_A_D3FORUM_LABEL_SELECTCATEGORY','Select a category');
define('_MD_A_D3FORUM_H2_GROUPPERMS','Permissions about each groups');
define('_MD_A_D3FORUM_H2_USERPERMS','Permissions about each users');
define('_MD_A_D3FORUM_TH_CAN_READ','View');
define('_MD_A_D3FORUM_TH_CAN_POST','Post');
define('_MD_A_D3FORUM_TH_CAN_EDIT','Edit');
define('_MD_A_D3FORUM_TH_CAN_DELETE','Delete');
define('_MD_A_D3FORUM_TH_POST_AUTO_APPROVED','AutoApproval');
define('_MD_A_D3FORUM_TH_IS_MODERATOR','Moderator');
define('_MD_A_D3FORUM_TH_CAN_MAKEFORUM','Making forums');
define('_MD_A_D3FORUM_TH_UID','uid');
define('_MD_A_D3FORUM_TH_UNAME','uname');
define('_MD_A_D3FORUM_TH_GROUPNAME','groupname');
define('_MD_A_D3FORUM_NOTICE_ADDUSERS','Input either uid or uname.');
define('_MD_A_D3FORUM_ERR_CREATECATEGORYFIRST','Create a category first');
define('_MD_A_D3FORUM_ERR_CREATEFORUMFIRST','Create a forum first');

// advanced
define('_MD_A_D3FORUM_H2_SYNCALLTABLES','Synchronize redundant informations');
define('_MD_A_D3FORUM_MAX_TOPIC_ID','Max topic id');
define('_MD_A_D3FORUM_LABEL_SYNCTOPICS_START','topic started from');
define('_MD_A_D3FORUM_LABEL_SYNCTOPICS_NUM','topics at once');
define('_MD_A_D3FORUM_BTN_DOSYNCTABLES','Do synchronize');
define('_MD_A_D3FORUM_FMT_SYNCTOPICSDONE','%s topics have been synchronized');
define('_MD_A_D3FORUM_MSG_SYNCTABLESDONE','Synchronized successfully');
define('_MD_A_D3FORUM_HELP_SYNCALLTABLES','Execute it if your forum displays contradictory data. You have to execute it just after IMPORT from some modules');
define('_MD_A_D3FORUM_H2_IMPORTFROM','Import');
define('_MD_A_D3FORUM_H2_COMIMPORTFROM','Import from XOOPS comments');
define('_MD_A_D3FORUM_LABEL_SELECTMODULE','Select a module');
define('_MD_A_D3FORUM_BTN_DOIMPORT','Do import');
define('_MD_A_D3FORUM_CONFIRM_DOIMPORT','Are you OK?');
define('_MD_A_D3FORUM_MSG_IMPORTDONE','Imported successfully');
define('_MD_A_D3FORUM_MSG_COMIMPORTDONE','XOOPS comments of the module are imported as comment-integration');
define('_MD_A_D3FORUM_ERR_INVALIDMID','You\'ve specified wrong module to be imported');
define('_MD_A_D3FORUM_ERR_SQLONIMPORT','Faild to import. You have to check the versions of each modules');
define('_MD_A_D3FORUM_HELP_IMPORTFROM','You can import from newbb1,xhnewbb, and the other d3forum instances. And you should know this is not a perfect copy. Especially you should check permissions. You also have to know all data in this module will be lost when you execute to import.');
define('_MD_A_D3FORUM_HELP_COMIMPORTFROM','XOOPS Comments will be imported as d3forum message. Also you have to enable comment-integration feature to use them. (Editing templates or modify preferences etc.)');

// post_histories
define('_MD_A_D3FORUM_H2_POSTHISTORIES','Histories of editing/deleting posts');
define('_MD_A_D3FORUM_LINK_REFERDELETED','Deleted');

?>