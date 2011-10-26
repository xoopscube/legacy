<?php

// contents list admin
define('_MD_A_PICO_H2_CONTENTS','Contents list');
define('_MD_A_PICO_TH_CONTENTSID','ID');
define('_MD_A_PICO_TH_CONTENTSSUBJECT','Subject');
define('_MD_A_PICO_TH_CONTENTSWEIGHT','weight');
define('_MD_A_PICO_TH_CONTENTSVISIBLE','VIS');
define('_MD_A_PICO_TH_CONTENTSSHOWINNAVI','NAVI');
define('_MD_A_PICO_TH_CONTENTSSHOWINMENU','MENU');
define('_MD_A_PICO_TH_CONTENTSALLOWCOMMENT','COM');
define('_MD_A_PICO_TH_CONTENTSFILTERS','Filters');
define('_MD_A_PICO_TH_CONTENTSACTIONS','Actions');
define('_MD_A_PICO_LEGEND_CONTENTSTHS','VIS: visible &nbsp; NAVI:show in page navigation &nbsp; MENU:show in menu &nbsp; COM:commentable');
define('_MD_A_PICO_BTN_MOVE','MOVED');
define('_MD_A_PICO_LABEL_CONTENTSRIGHTCHECKED','selected items will be:');
define('_MD_A_PICO_MSG_CONTENTSMOVED','Contents have been moved');
define('_MD_A_PICO_LABEL_MAINDISP','View');
define('_MD_A_PICO_BTN_DELETE','delete');
define('_MD_A_PICO_CONFIRM_DELETE','Are you sure to delete them?');
define('_MD_A_PICO_MSG_CONTENTSDELETED','Deleted successfully');
define('_MD_A_PICO_BTN_EXPORT','Export');
define('_MD_A_PICO_CONFIRM_EXPORT','Selected contents are exported as the top contents of the module. Comments will not be copied. Are you OK?');
define('_MD_A_PICO_MSG_CONTENTSEXPORTED','Exported successfully');
define('_MD_A_PICO_MSG_FMT_DUPLICATEDVPATH','Some contents have not been updated because of duplicated vpath (ID: %s)');

// category_access
define('_MD_A_PICO_LABEL_SELECTCATEGORY','Select a category');
define('_MD_A_PICO_H2_INDEPENDENTPERMISSION','Independent Permission');
define('_MD_A_PICO_LABEL_INDEPENDENTPERMISSION','Set permissions for this category independently from the parent category');
define('_MD_A_PICO_LINK_CATPERMISSIONID','Check the parental permissions');
define('_MD_A_PICO_H2_GROUPPERMS','Permissions about each groups');
define('_MD_A_PICO_H2_USERPERMS','Permissions about each users');
define('_MD_A_PICO_TH_UID','uid');
define('_MD_A_PICO_TH_UNAME','uname');
define('_MD_A_PICO_TH_GROUPNAME','groupname');
define('_MD_A_PICO_NOTICE_ADDUSERS','Input either uid or uname.');

// import
define('_MD_A_PICO_H2_IMPORTFROM','Import');
define('_MD_A_PICO_LABEL_SELECTMODULE','Select a module');
define('_MD_A_PICO_BTN_DOIMPORT','Do import');
define('_MD_A_PICO_CONFIRM_DOIMPORT','Are you OK?');
define('_MD_A_PICO_MSG_IMPORTDONE','Imported successfully');
define('_MD_A_PICO_ERR_INVALIDMID','You\'ve specified wrong module to be imported');
define('_MD_A_PICO_ERR_SQLONIMPORT','Faild to import. You have to check the versions of each modules');
define('_MD_A_PICO_HELP_IMPORTFROM','You can import from pico and TinyD instances. And you should know this is not a perfect copy. Especially you should check permissions. You also have to know all data in this module will be lost when you execute to import.');
define('_MD_A_PICO_H2_SYNCALL','Synchronize redundant informations');
define('_MD_A_PICO_BTN_DOSYNCALL','Do synchronize');
define('_MD_A_PICO_MSG_SYNCALLDONE','Synchronized successfully');
define('_MD_A_PICO_HELP_SYNCALL','Execute it if your categories or contents display contradictory data.');
define('_MD_A_PICO_H2_CLEARBODYCACHE','Clear body cache');
define('_MD_A_PICO_BTN_DOCLEARBODYCACHE','clear');
define('_MD_A_PICO_MSG_CLEARBODYCACHEDONE','all caches for contents have just been cleared');
define('_MD_A_PICO_HELP_CLEARBODYCACHE','Just execute it when some caching troubles are occurred eg) after moving site.');

// extras
define('_MD_A_PICO_H2_EXTRAS','Extras');
define('_MD_A_PICO_TH_ID','ID');
define('_MD_A_PICO_TH_TYPE','Type');
define('_MD_A_PICO_TH_SUMMARY','Summary');
define('_MD_A_PICO_LINK_DETAIL','Detail');
define('_MD_A_PICO_LINK_EXTRACT','Extract');
define('_MD_A_PICO_LABEL_SEARCHBYPHRASE','Search by phrase');
define('_MD_A_PICO_TH_EXTRASACTIONS','Actions');
define('_MD_A_PICO_LABEL_EXTRASRIGHTCHECKED','selected items will be:');
define('_MD_A_PICO_BTN_CSVOUTPUT','CSV output');
define('_MD_A_PICO_MSG_DELETED','Deleted successfully');

// tags
define('_MD_A_PICO_H2_TAGS','Tag Manager');
define('_MD_A_PICO_TH_TAG','Tag');
define('_MD_A_PICO_TH_USED','Used');
define('_MD_A_PICO_LABEL_ORDER','Order');


?>