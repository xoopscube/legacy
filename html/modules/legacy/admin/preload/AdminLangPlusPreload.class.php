<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

class Legacy_AdminLangPlusPreload extends XCube_ActionFilter
{
	function preBlockFilter()
	{
	//common and image management
	if (!defined('_AD_LEGACY_LANG_UPLOAD')) {
	define('_AD_LEGACY_LANG_UPLOAD', "Upload");
	}
	if (!defined('_AD_LEGACY_ERROR_REQUIRED')) {
	define('_AD_LEGACY_ERROR_REQUIRED', "{0} is required.");
	}
	if (!defined('_AD_LEGACY_ERROR_DBUPDATE_FAILED')) {
	define('_AD_LEGACY_ERROR_DBUPDATE_FAILED', "Database update failed.");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_UPLOAD')) {
	define('_AD_LEGACY_LANG_IMAGE_UPLOAD', "Image Batch-Upload");
	}
	if (!defined('_AD_LEGACY_TIPS_IMAGE_UPLOAD')) {
	define('_AD_LEGACY_TIPS_IMAGE_UPLOAD', "You can easily register many images by uploading Archive file including them! <br />This batch-upload doesn't check Length and File-Size of each image!<br />Please pre-adjust them before you archive them!<br />(Only tar.gz or zip archive)");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_UPLOAD_FILE')) {
	define('_AD_LEGACY_LANG_IMAGE_UPLOAD_FILE', "Image Archive(Only tar.gz or zip)");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_UPLOAD_RESULT')) {
	define('_AD_LEGACY_LANG_IMAGE_UPLOAD_RESULT', "Result of Image Batch-Upload");
	}
	if (!defined('_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE')) {
	define('_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE', "Could not save image file '{0}'");
	}
	if (!defined('_AD_LEGACY_LANG_IMGCAT_WRONG')) {
	define('_AD_LEGACY_LANG_IMGCAT_WRONG', "Wrong Image Category!");
	}
	if (!defined('_AD_LEGACY_ERROR_EXTENSION_IS_WRONG')) { 
	define('_AD_LEGACY_ERROR_EXTENSION_IS_WRONG', "The extension of the uploaded file is invalid.");
	}
	if (!defined('_AD_LEGACY_LANG_IMGCAT_UPDATECONF')) {
	define('_AD_LEGACY_LANG_IMGCAT_UPDATECONF', "Confirm image-cateroy update");
	}
	if (!defined('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_IMGCAT')) {
	define('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_IMGCAT', "Are you sure you want to update it?");
	}
	if (!defined('_AD_LEGACY_LANG_IMGCAT_MYTIPS')) {
	define('_AD_LEGACY_LANG_IMGCAT_MYTIPS', "Please write down your tips here!<br />( Customize _AD_LEGACY_LANG_IMGCAT_MYTIPS !)");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_UPDATECONF')) {
	define('_AD_LEGACY_LANG_IMAGE_UPDATECONF', "Confirm image update");
	}
	if (!defined('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_IMAGE')) {
	define('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_IMAGE', "Are you sure you want to update it?");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_MYTIPS')) {
	define('_AD_LEGACY_LANG_IMAGE_MYTIPS', "Please write down your tips here!<br />( Customize _AD_LEGACY_LANG_IMAGE_MYTIPS !)");
	}
	//smile management
	if (!defined('_AD_LEGACY_LANG_SMILES_UPLOAD')) {
	define('_AD_LEGACY_LANG_SMILES_UPLOAD', "Smiles Batch-Upload");
	}
	if (!defined('_AD_LEGACY_TIPS_SMILES_UPLOAD')) {
	define('_AD_LEGACY_TIPS_SMILES_UPLOAD', "You can easily register many smiles by uploading Archive file including them! <br />This batch-upload doesn't check Length and File-Size of each smiles!<br />Please pre-adjust them before you archive them!<br />(Only tar.gz or zip archive)");
	}
	if (!defined('_AD_LEGACY_LANG_SMILES_UPLOAD_FILE')) {
	define('_AD_LEGACY_LANG_SMILES_UPLOAD_FILE', "Smiles Archive(Only tar.gz or zip)");
	}
	if (!defined('_AD_LEGACY_LANG_SMILES_UPLOAD_RESULT')) {
	define('_AD_LEGACY_LANG_SMILES_UPLOAD_RESULT', "Result of Smiles Batch-Upload");
	}
	if (!defined('_AD_LEGACY_ERROR_COULD_NOT_SAVE_SMILES_FILE')) {
	define('_AD_LEGACY_ERROR_COULD_NOT_SAVE_SMILES_FILE', "Could not save smiles file '{0}'");
	}
	//
	if (!defined('_AD_LEGACY_LANG_SMILES_UPDATECONF')) {
	define('_AD_LEGACY_LANG_SMILES_UPDATECONF', "Confirm smiles update");
	}
	if (!defined('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_SMILES')) {
	define('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_SMILES', "Are you sure you want to update it?");
	}
	if (!defined('_AD_LEGACY_LANG_SMILES_MYTIPS')) {
	define('_AD_LEGACY_LANG_SMILES_MYTIPS', "Please write down your tips here!<br />( Customize _AD_LEGACY_LANG_SMILES_MYTIPS !)");
	}

	//module edit
	if (!defined('_AD_LEGACY_LANG_MOD_EDIT')) {
	define('_AD_LEGACY_LANG_MOD_EDIT', "Module Edit");
	}
	if (!defined('_AD_LEGACY_LANG_MOD_READGROUP')) {
	define('_AD_LEGACY_LANG_MOD_READGROUP', "Target Group(Read Right)");
	}
	if (!defined('_AD_LEGACY_LANG_MOD_ADMINGROUP')) {
	define('_AD_LEGACY_LANG_MOD_ADMINGROUP', "Target Group(Admin Right)");
	}
	//
	if (!defined('_AD_LEGACY_LANG_MOD_TOTAL')) {
	define('_AD_LEGACY_LANG_MOD_TOTAL', "Total of Module(s)");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_TOTAL')) {
	define('_AD_LEGACY_LANG_BLOCK_TOTAL', "Total of Block(s)");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_INSTALLEDTOTAL')) {
	define('_AD_LEGACY_LANG_BLOCK_INSTALLEDTOTAL', "Installed Block(s)");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_UNINSTALLEDTOTAL')) {
	define('_AD_LEGACY_LANG_BLOCK_UNINSTALLEDTOTAL', "Uninstalled Block(s)");
	}
	if (!defined('_AD_LEGACY_LANG_SMILES_TOTAL')) {
	define('_AD_LEGACY_LANG_SMILES_TOTAL', "Total of Smiles");
	}
	if (!defined('_AD_LEGACY_LANG_SMILES_DISPLAYTOTAL')) {
	define('_AD_LEGACY_LANG_SMILES_DISPLAYTOTAL', "Display Smiles");
	}
	if (!defined('_AD_LEGACY_LANG_SMILES_NOTDISPLAYTOTAL')) {
	define('_AD_LEGACY_LANG_SMILES_NOTDISPLAYTOTAL', "Not-display Smiles");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_TOTAL')) {
	define('_AD_LEGACY_LANG_IMAGE_TOTAL', "Total of Image(s)");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_DISPLAYTOTAL')) {
	define('_AD_LEGACY_LANG_IMAGE_DISPLAYTOTAL', "Display Image(s)");
	}
	if (!defined('_AD_LEGACY_LANG_IMAGE_NOTDISPLAYTOTAL')) {
	define('_AD_LEGACY_LANG_IMAGE_NOTDISPLAYTOTAL', "Not-display Image(s)");
	}
	if (!defined('_AD_LEGACY_LANG_IMGCAT_TOTAL')) {
	define('_AD_LEGACY_LANG_IMGCAT_TOTAL', "Total of Image Categories");
	}
	if (!defined('_AD_LEGACY_LANG_IMGCAT_FILETYPETOTAL')) {
	define('_AD_LEGACY_LANG_IMGCAT_FILETYPETOTAL', "File Store Type");
	}
	if (!defined('_AD_LEGACY_LANG_IMGCAT_DBTYPETOTAL')) {
	define('_AD_LEGACY_LANG_IMGCAT_DBTYPETOTAL', "DB Store Type");
	}
	if (!defined('_AD_LEGACY_LANG_COMMENT_TOTAL')) {
	define('_AD_LEGACY_LANG_COMMENT_TOTAL', "Total of Comments");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_ACTIVETOTAL')) {
	define('_AD_LEGACY_LANG_BLOCK_ACTIVETOTAL', "Total of active Block(s)");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_INACTIVETOTAL')) {
	define('_AD_LEGACY_LANG_BLOCK_INACTIVETOTAL', "Total of Inactive Block(s)");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_MYTIPS')) {
	define('_AD_LEGACY_LANG_BLOCK_MYTIPS', "Please write down your tips here!<br />( Customize _AD_LEGACY_LANG_BLOCK_MYTIPS !)");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_MYTIPS2')) {
	define('_AD_LEGACY_LANG_BLOCK_MYTIPS2', "Please write down your tips here!<br />( Customize _AD_LEGACY_LANG_BLOCK_MYTIPS2 !)");
	}
	if (!defined('_AD_LEGACY_LANG_BLOCK_UPDATECONF')) {
	define('_AD_LEGACY_LANG_BLOCK_UPDATECONF', "Confirm block update");
	}
	if (!defined('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_BLOCK')) {
	define('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_BLOCK', "Are you sure you want to update it?");
	}
	//
	if (!defined('_AD_LEGACY_LANG_MOD_MYTIPS')) {
	define('_AD_LEGACY_LANG_MOD_MYTIPS', "Please write down your tips here!<br />( Customize _AD_LEGACY_LANG_MOD_MYTIPS !)");
	}
	if (!defined('_AD_LEGACY_LANG_COMMENT_MYTIPS')) {
	define('_AD_LEGACY_LANG_COMMENT_MYTIPS', "Please write down your tips here!<br />( Customize _AD_LEGACY_LANG_COMMENT_MYTIPS !)");
	}
	if (!defined('_AD_LEGACY_LANG_COMMENT_UPDATECONF')) {
	define('_AD_LEGACY_LANG_COMMENT_UPDATECONF', "Confirm comment update");
	}
	if (!defined('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_COMMENT')) {
	define('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_COMMENT', "Are you sure you want to update it?");
	}
	if (!defined('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_MODULE')) {
	define('_AD_LEGACY_MESSAGE_CONFIRM_UPDATE_MODULE', "Are you sure you want to update it?");
	}

	}
	
}

?>