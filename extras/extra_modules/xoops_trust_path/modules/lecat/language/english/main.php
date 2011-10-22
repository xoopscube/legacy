<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

define('_MD_LECAT_ERROR_REQUIRED', '{0} is required.');
define('_MD_LECAT_ERROR_MINLENGTH', 'Input {0} with {1} or more characters.');
define('_MD_LECAT_ERROR_MAXLENGTH', 'Input {0} with {1} or less characters.');
define('_MD_LECAT_ERROR_EXTENSION', 'Uploaded file\'s extension does not match any entry in the allowed list.');
define('_MD_LECAT_ERROR_INTRANGE', 'Incorrect input on {0}.');
define('_MD_LECAT_ERROR_MIN', 'Input {0} with {1} or more numeric value.');
define('_MD_LECAT_ERROR_MAX', 'Input {0} with {1} or less numeric value.');
define('_MD_LECAT_ERROR_OBJECTEXIST', 'Incorrect input on {0}.');
define('_MD_LECAT_ERROR_DBUPDATE_FAILED', 'Failed updating database.');
define('_MD_LECAT_ERROR_EMAIL', '{0} is an incorrect email address.');
define('_MD_LECAT_ERROR_NO_CATEGORY_REQUESTED', 'No Category is requested');
define('_MD_LECAT_ERROR_HAS_CHILDREN', 'Cannot delete this category because some children categories exist. Delete them first or set &force=1 on GET request !');
define('_MD_LECAT_ERROR_HAS_CLIENT_DATA', 'Cannot delete this category because some data use this category. Delete them first !');
define('_MD_LECAT_MESSAGE_CONFIRM_DELETE', 'Are you sure to delete?');
define('_MD_LECAT_LANG_ADD_A_NEW_CAT', 'Add a new Category');
define('_MD_LECAT_LANG_CAT_ID', 'CAT_ID');
define('_MD_LECAT_LANG_TITLE', 'Title');
define('_MD_LECAT_LANG_P_ID', 'Parent Category ID');
define('_MD_LECAT_LANG_PARENT', 'Parent Category');
define('_MD_LECAT_LANG_MODULES', 'Modules');
define('_MD_LECAT_LANG_DESCRIPTION', 'Description');
define('_MD_LECAT_LANG_DEPTH', 'Depth');
define('_MD_LECAT_LANG_WEIGHT', 'Weight');
define('_MD_LECAT_LANG_OPTIONS', 'Option');
define('_MD_LECAT_LANG_CONTROL', 'CONTROL');
define('_MD_LECAT_LANG_CAT', 'Category');
define('_MD_LECAT_LANG_CAT_EDIT', 'Category Edit');
define('_MD_LECAT_LANG_CAT_DELETE', 'Category Delete');
define('_MD_LECAT_ERROR_CONTENT_IS_NOT_FOUND', 'Content is not found');
define('_MD_LECAT_LANG_LEVEL', 'Max Depth');
define('_MD_LECAT_LANG_ACTIONS', 'Action');
define('_MD_LECAT_LANG_ADD_A_NEW_PERMIT', 'Add a new Permission');
define('_MD_LECAT_LANG_PERMIT_ID', 'PERMIT_ID');
define('_MD_LECAT_LANG_UID', 'UID');
define('_MD_LECAT_LANG_GROUPID', 'User Group ID');
define('_MD_LECAT_LANG_PERMISSIONS', 'Permissions');
define('_MD_LECAT_LANG_PERMIT_EDIT', 'PERMIT_EDIT');
define('_MD_LECAT_LANG_PERMIT_DELETE', 'PERMIT_DELETE');
define('_MD_LECAT_LANG_AUTH_SETTING', 'auth setting');
define('_MD_LECAT_LANG_AUTH_KEY', 'auth key name');
define('_MD_LECAT_LANG_AUTH_TITLE', 'auth display title');
define('_MD_LECAT_LANG_AUTH_DEFAULT', 'auth default value');
define('_MD_LECAT_LANG_EDIT_ACTOR', 'Edit Actors');
define('_MD_LECAT_LANG_MODULES_CONFINEMENT', 'Module Confinement');
define('_MD_LECAT_LANG_PERMISSION_TYPE', 'Permission Type');
define('_MD_LECAT_LANG_DEFAULT_PERMISSIONS', 'Default Permission Setting');
define('_MD_LECAT_DESC_PERMISSION_TYPE', 'Set permission type and default values.');
define('_MD_LECAT_LANG_ADD_A_NEW_PERMISSION_TYPE', 'Add a new permission type');
define('_MD_LECAT_LANG_VIEWER', 'Viewer');
define('_MD_LECAT_LANG_POSTER', 'Poster');
define('_MD_LECAT_LANG_MANAGER', 'Manager');
define('_MD_LECAT_LANG_CATEGORY', 'Category');
define('_MD_LECAT_LANG_TOP_CAT', 'Top Category');
define('_MD_LECAT_LANG_DELEET_ALL_PERMIT', 'Delete all Permission on this category');
define('_MD_LECAT_LANG_PERMISSION_ON', 'permitted');
define('_MD_LECAT_LANG_LEVEL_UNLIMITED', 'unlimited depth');
define('_MD_LECAT_TIPS_CATEGORY_SET','<p>Lecat is category management module.<br />This means many other module like forum, news and article use lecat for their category management.</p><p>Lecat provides two main function to other modules.<ul><li>Category List(tree like)</li><li>Permission check for each category.</li></ul></p><h3>Category Set</h3><p>Each module request different category. For example, a news module requests \'site update\', \'new member\', \'new shop\'. On the other hand, a forum request \'question\', \'request\', \'talk\'.<br />So, you can create several category sets. For news, for forum, for ...</p><p>In such case, You should copy and install another lecat module.</p>');
define('_MD_LECAT_TIPS_LEVEL', 'max depth in category tree. 0 means no limit.');
define('_MD_LECAT_TIPS_MODULE_CONFINEMENT', 'set module name separeted by "," if you want to apply this category at specific modules only.');
define('_MD_LECAT_MESSAGE_CONFIRM_SET_DELETE', 'Are you sure to delete ? All of categories belonging to this category sets are deleted too.');
define('_MD_LECAT_TIPS_PERMISSIONS', 'Leave the following permissions as is if you want to inherit parent category\'s permissions.');
?>
