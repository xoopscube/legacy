<?php

//
// CHECK THE FUNCTION IN THE BOTTOM OF THIS FILE (for multibyte languages)
//
define( '_MD_PICO_NUM' , 'Items per page');
define( '_MD_PICO_TOP' , 'TOP');
define( '_MD_PICO_ALLCONTENTS' , 'All contents');
define( '_MD_PICO_DELETEDCONTENTS' , 'Deleted contents');
define( '_MD_PICO_MENU' , 'MENU');
define( '_MD_PICO_CREATED' , 'Created');
define( '_MD_PICO_MODIFIED' , 'Modified');
define( '_MD_PICO_EXPIRING' , 'Expiring');
define( '_MD_PICO_BYTE' , 'byte');
define( '_MD_PICO_HISTORY' , 'History');
define( '_MD_PICO_DIFF2NOW' , 'diff to now');
define( '_MD_PICO_DIFFFROMPREV' , 'diff from previous');
define( '_MD_PICO_REFERIT' , 'Refer it');
define( '_MD_PICO_DOWNLOADIT' , 'Download it');
define( '_MD_PICO_VIEWED' , 'Views');
define( '_MD_PICO_NEXT' , 'Next');
define( '_MD_PICO_PREV' , 'Prev');
define( '_MD_PICO_CATEGORYINDEX' , 'Top of category');
define( '_MD_PICO_NOSUBJECT' , '(no subject)');
define( '_MD_PICO_FMT_PUBLIC' , 'Public');
define( '_MD_PICO_FMT_PRIVATE' , 'Private');
define( '_MD_PICO_FMT_PUBLICCOUNT' , 'Public: %s items');
define( '_MD_PICO_FMT_PRIVATECOUNT' , 'Private: %s items');
define( '_MD_PICO_WAITINGRELEASE' , 'Waiting release');
define( '_MD_PICO_EXPIRED' , 'Expired');
define( '_MD_PICO_INVISIBLE' , 'Invisible');
define( '_MD_PICO_WAITINGAPPROVAL' , 'Waiting approval');
define( '_MD_PICO_WAITINGREGISTER' , 'waiting - New Article');
define( '_MD_PICO_WAITINGUPDATE' , 'Waiting update');
define( '_MD_PICO_REGISTERED_AUTOMATICALLY' , 'AUTOMATIC');
define( '_MD_PICO_ONOFF' , 'ON/OFF');

define( '_MD_PICO_CATEGORY' , 'Category');
define( '_MD_PICO_CATEGORIES' , 'Categories');
define( '_MD_PICO_SUBCATEGORY' , 'Subcategory');
define( '_MD_PICO_SUBCATEGORIES' , 'Subcategories');
define( '_MD_PICO_CONTENT' , 'Content');
define( '_MD_PICO_CONTENTS' , 'Contents');

define( '_MD_PICO_LINK_MAKECATEGORY' , 'New category');
define( '_MD_PICO_LINK_MAKESUBCATEGORY' , 'New subcategory');
define( '_MD_PICO_LINK_MAKECONTENT' , 'New content');
define( '_MD_PICO_LINK_EDITCATEGORY' , 'Edit category');
define( '_MD_PICO_LINK_EDITCONTENT' , 'Edit content');
define( '_MD_PICO_LINK_CATEGORYPERMISSIONS' , 'Permissions');
define( '_MD_PICO_LINK_BATCHCONTENTS' , 'Batch');
define( '_MD_PICO_LINK_PUBLICCATEGORYINDEX' , 'Public index');

define( '_MD_PICO_LINK_PRINTERFRIENDLY' , 'Printer-friendly');
define( '_MD_PICO_LINK_TELLAFRIEND' , 'Tell a friend');
define( '_MD_PICO_FMT_TELLAFRIENDSUBJECT' , 'Article found in %s');
define( '_MD_PICO_FMT_TELLAFRIENDBODY' , "I've just found an interesting article\nSubject:%s");
define( '_MD_PICO_JUMPTOTOPOFPICOBODY' , 'Jump to the top');
define( '_MD_PICO_CSVENCODING' , 'UTF-8');


define( '_MD_PICO_ERR_SQL' , 'SQL Error Occurred in: ');
define( '_MD_PICO_ERR_DUPLICATEDVPATH' , 'The virtual path is duplicated');
define( '_MD_PICO_ERR_PIDLOOP' , 'parent/child loop error');

define( '_MD_PICO_MSG_UPDATED' , 'Updated successfully');

define( '_MD_PICO_ERR_READCATEGORY' , 'You cannot access the specified category');
define( '_MD_PICO_ERR_CREATECATEGORY' , 'You cannot create a category');
define( '_MD_PICO_ERR_CATEGORYMANAGEMENT' , 'You are not a manager of the category');
define( '_MD_PICO_ERR_READCONTENT' , 'You cannot access the specified content');
define( '_MD_PICO_ERR_CREATECONTENT' , 'You cannot create new content');
define( '_MD_PICO_ERR_LOCKEDCONTENT' , 'The content is locked');
define( '_MD_PICO_ERR_EDITCONTENT' , 'You cannot edit the content');
define( '_MD_PICO_ERR_DELETECONTENT' , 'You cannot delete the content');
define( '_MD_PICO_ERR_PERMREADFULL' , 'You cannot read the full content');
define( '_MD_PICO_ERR_LOGINTOREADFULL' , 'Login as a member to read the full content');
define( '_MD_PICO_ERR_COMPILEERROR' , 'The content is not processed due to Smarty compilation errors. Try to edit and submit again.');

define( '_MD_PICO_MSG_CATEGORYMADE' , 'The category has been created successfully');
define( '_MD_PICO_MSG_CATEGORYUPDATED' , 'The category has been modified successfully');
define( '_MD_PICO_MSG_CATEGORYDELETED' , 'The category has been deleted successfully');
define( '_MD_PICO_MSG_CONTENTMADE' , 'The content has been created successfully');
define( '_MD_PICO_MSG_CONTENTWAITINGREGISTER' , 'The content has been submitted for approval');
define( '_MD_PICO_MSG_CONTENTUPDATED' , 'The content has been modified successfully');
define( '_MD_PICO_MSG_CONTENTWAITINGUPDATE' , 'The content has been submitted for update');
define( '_MD_PICO_MSG_CONTENTDELETED' , 'The content has been deleted successfully');

define( '_MD_PICO_CATEGORYMANAGER' , 'Category management');
define( '_MD_PICO_CONTENTMANAGER' , 'Content manager');
define( '_MD_PICO_TH_VIRTUALPATH' , 'Virtual path');
define( '_MD_PICO_TH_SUBJECT' , 'Subject');
define( '_MD_PICO_TH_SUBJECT_WAITING' , 'Waiting subject');
define( '_MD_PICO_TH_HTMLHEADER' , 'HTML headers');
define( '_MD_PICO_TH_HTMLHEADER_WAITING' , 'Waiting HTML headers');
define( '_MD_PICO_TH_BODY' , 'Body');
define( '_MD_PICO_TH_BODY_WAITING' , 'Waiting body');
define( '_MD_PICO_TH_FILTERS' , 'Filters');
define( '_MD_PICO_TH_TAGS' , 'Tags');
define( '_MD_PICO_TH_TAGSDSC' , 'multiple tags separated by spaces');
define( '_MD_PICO_TH_WEIGHT' , 'Order weight');
define( '_MD_PICO_TH_CONTENTOPTIONS' , 'Options');
define( '_MD_PICO_LABEL_USECACHE' , 'Use cache');
define( '_MD_PICO_NOTE_USECACHEDSC' , 'Enable only for static content');
define( '_MD_PICO_LABEL_LOCKED' , 'Lock (only moderators can edit/delete)');
define( '_MD_PICO_LABEL_SPECIFY_DATETIME' , 'Specify datetime');
define( '_MD_PICO_LABEL_VISIBLE' , 'Visible');
define( '_MD_PICO_LABEL_SHOWINNAVI' , 'Show in navigation');
define( '_MD_PICO_LABEL_SHOWINMENU' , 'Show in menu');
define( '_MD_PICO_LABEL_ALLOWCOMMENT' , 'Allow comments');
define( '_MD_PICO_TH_CATEGORYTITLE' , 'Title');
define( '_MD_PICO_TH_CATEGORYDESC' , 'Description');
define( '_MD_PICO_TH_CATEGORYPARENT' , 'Parent');
define( '_MD_PICO_TH_CATEGORYWEIGHT' , 'Weight');
define( '_MD_PICO_TH_CATEGORYOPTIONS' , 'Options');
define( '_MD_PICO_CONTENTS_TOTAL' , 'Total content');
define( '_MD_PICO_SUBCATEGORIES_TOTAL' , 'Total subcategories');
define( '_MD_PICO_SUBCATEGORY_COUNT' , 'Number of subcategories');
define( '_MD_PICO_MSG_CONFIRMDELETECATEGORY' , 'All content of the category will be removed. Please confirm?');
define( '_MD_PICO_MSG_CONFIRMDELETECONTENT' , 'Are you sure you want to delete?');
define( '_MD_PICO_MSG_CONFIRMSAVEASCONTENT' , 'Are you sure you want to save as...?');
//define('_MD_PICO_MSG_GOTOPREFERENCE4EDITTOP','The TOP category is the special. You can change the settings of the TOP in module preferences.');
define( '_MD_PICO_LABEL_HTMLHEADERONOFF' , 'Display the textarea');
define( '_MD_PICO_LABEL_HTMLHEADERCONFIGALERT' , '(HTML header for each content is disabled in preferences)');
define( '_MD_PICO_LABEL_INPUTHELPER' , 'Helper ON/OFF');
define( '_MD_PICO_BTN_SUBMITEDITING' , 'Save');
define( '_MD_PICO_BTN_SUBMITSAVEAS' , 'Save as...');
define( '_MD_PICO_BTN_COPYFROMWAITING' , 'Save waiting content');
define( '_MD_PICO_MSG_CONFIRMCOPYFROMWAITING' , ' If you do not save the waiting content, the data will be lost. Please confirm!');
define( '_MD_PICO_HOWTO_OVERRIDEOPTIONS' , 'Override preferences by writing a line e.g.:<br>(option name):(option value)<br>Example: show_breadcrumbs:1<br><b>Overridable options current values</b>');


// vote to post
define( '_MD_PICO_ERR_VOTEPERM' , 'You cannot vote');
define( '_MD_PICO_ERR_VOTEINVALID' , 'Invalid vote');
define( '_MD_PICO_MSG_VOTEDOUBLE' , 'You can vote once per content');
define( '_MD_PICO_MSG_VOTEACCEPTED' , 'Thank you for voting!');
define( '_MD_PICO_MSG_VOTEDISABLED' , 'You cannot vote for this item');
define( '_MD_PICO_VOTECOUNT' , 'Votes');
define( '_MD_PICO_VOTEPOINTAVG' , 'Average');
define( '_MD_PICO_VOTEPOINTDSCBEST' , 'Useful');
define( '_MD_PICO_VOTEPOINTDSCWORST' , 'Useless');

// query contents
define( '_MD_PICO_FMT_QUERYTAGTITLE' , 'Tag: %s');
define( '_MD_PICO_FMT_QUERYTAGDESC' , 'Content tagged %s');
define( '_MD_PICO_ERR_NOCONTENTMATCHED' , 'No content');

// filters
define( '_MD_PICO_FILTERS_EVALTITLE' , 'php code');
define( '_MD_PICO_FILTERS_EVALDESC' , 'It will be the parameter of eval() function');
define( '_MD_PICO_FILTERS_HTMLSPECIALCHARSTITLE' , 'HTML special character escape');
define( '_MD_PICO_FILTERS_HTMLSPECIALCHARSDESC' , 'If you want to use BBCode etc. also, set it the first place.');
define( '_MD_PICO_FILTERS_TEXTWIKITITLE' , 'PEAR TextWiki <a href="https://wiki.ciaweb.net/yawiki/index.php?area=Text_Wiki&amp;page=SamplePage" target="_blank">Sample</a>');
define( '_MD_PICO_FILTERS_TEXTWIKIDESC' , 'Rendered by TextWiki rule');
define( '_MD_PICO_FILTERS_XOOPSTPLTITLE' , 'Smarty(XoopsTpl)');
define( '_MD_PICO_FILTERS_XOOPSTPLDESC' , 'Rendered as a Smarty template');
define( '_MD_PICO_FILTERS_NL2BRTITLE' , 'Auto new line');
define( '_MD_PICO_FILTERS_NL2BRDESC' , 'LF will be replaced into &lt;br /&gt;');
define( '_MD_PICO_FILTERS_SMILEYTITLE' , 'Smiley');
define( '_MD_PICO_FILTERS_SMILEYDESC' , ':-) :-D etc.');
define( '_MD_PICO_FILTERS_XCODETITLE' , 'BBCode');
define( '_MD_PICO_FILTERS_XCODEDESC' , 'Auto link and BBCode will be enabled');
define( '_MD_PICO_FILTERS_WRAPSTITLE' , 'Page wraps (note: displays the file specified in the virtual path)');
define( '_MD_PICO_FILTERS_WRAPSDESC' , 'The target file is XOOPS_TRUST_PATH/wraps/(dirname)/file (same as wraps)');
define( '_MD_PICO_FILTERS_XOOPSTSTITLE' , 'Editor default filter (smiley, xcode , img, br)');
define( '_MD_PICO_FILTERS_XOOPSTSDESC' , 'Use default text filter, enabled smiley, xcode , img & br');


// permissions
define( '_MD_PICO_PERMS_CAN_READ' , 'Read');
define( '_MD_PICO_PERMS_CAN_READFULL' , 'Read Full');
define( '_MD_PICO_PERMS_CAN_POST' , 'Post');
define( '_MD_PICO_PERMS_CAN_EDIT' , 'Edit');
define( '_MD_PICO_PERMS_CAN_DELETE' , 'Delete');
define( '_MD_PICO_PERMS_POST_AUTO_APPROVED' , 'Auto Approved');
define( '_MD_PICO_PERMS_IS_MODERATOR' , 'Moderate');
define( '_MD_PICO_PERMS_CAN_MAKESUBCATEGORY' , 'Create a Subcategory');


// LTR or RTL
if ( defined( '_ADM_USE_RTL' ) ) {
    @define( '_ALIGN_START', _ADM_USE_RTL ? 'right' : 'left' );
    @define( '_ALIGN_END', _ADM_USE_RTL ? 'left' : 'right' );
} else {
    @define( '_ALIGN_START', 'left' ); // change it right for RTL
    @define( '_ALIGN_END', 'right' );  // change it left for RTL
}


if ( ! defined( 'FOR_XOOPS_LANG_CHECKER' ) && ! function_exists( 'pico_convert_encoding_to_ie' ) ) {
    function pico_convert_encoding_to_ie( $str ) {
        return $str;
    }
}
