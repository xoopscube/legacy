<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

define('_MI_FILEMANAGER_NAME', "FileManager");
define('_MI_FILEMANAGER_DESC', "Gestion de dossiers et fichiers avec téléchargement multiple.");
define('_MI_FILEMANAGER_UPDATE', 'Mise à jour');

// --------------------------------------------------------
// Names of admin menu items
// --------------------------------------------------------
define('_MI_FILEMANAGER_MAIN', "Liste des fichiers");
define('_MI_FILEMANAGER_MAIN_DSC', "File list with thumbnails and image viewer");
define('_MI_FILEMANAGER_UPLOAD', "Télécherger Fichier");
define('_MI_FILEMANAGER_UPLOAD_DSC', "Upload files to the uploads directory");

define('_MI_FILEMANAGER_FOLDER', "Repértoire");
define('_MI_FILEMANAGER_FOLDER_DSC', "Gestion de dossiers permissions");

define('_MI_FILEMANAGER_CHECK', "Setting Check");
define('_MI_FILEMANAGER_CHECK_DSC', "Check the settings in the file manager.");

// --------------------------------------------------------
// PreferenceEdit
// --------------------------------------------------------
define('_MI_FILEMANAGER_PATH',"Main Upload Directory");
define('_MI_FILEMANAGER_PATH_DSC',"Define the upload path under /uploads/ (folder name only, without '/' slash)");
define('_MI_FILEMANAGER_DIRHANDLE',"Directory Management");
define('_MI_FILEMANAGER_DIRHANDLE_DSC',"Allow or not webmasters to create and delete folders.");
define('_MI_FILEMANAGER_THUMBSIZE',"Thumbnail size");
define('_MI_FILEMANAGER_THUMBSIZE_DSC',"Specify the maximum width of thumbnails for the file list.");
define('_MI_FILEMANAGER_DEBUGON',"Turn on the debugging Uploader");
define('_MI_FILEMANAGER_DEBUGON_DSC',"Enable or not debug console of uploader that is rendered inside an iframe.");


define('_MI_FILEMANAGER_XOOPSLOCK',"Hide system images?");
define('_MI_FILEMANAGER_XOOPSLOCK_DSC',"Display or not files from 'Image Manager' (ie. avatars, smilies, etc");
define('_MI_FILEMANAGER_EXTENSIONS',"File extension allowed for upload");
define('_MI_FILEMANAGER_EXTENSIONS_DSC',"Separate files extensions with a '|' pipe.<br />Make sure you use all lowercase characters for the file extension.<br />Default setting is gif|jpg|jpeg|png|avi|mov|wmv|mp3|mp4|flv|doc|xls|ods|odt|pdf");

// reserved  options setting 

define('_MI_FILEMANAGER_FUSE',"[ffmpeg] Use FFmpeg");
define('_MI_FILEMANAGER_FUSE_DSC',"FFmpeg is a complete, cross-platform solution to record, convert and stream audio and video.<br /> FFmpeg must be supported by the server. If not, Install the FFmpeg binary to your server.");
define('_MI_FILEMANAGER_FPATH',"[ffmpeg] Path of FFmpeg");
define('_MI_FILEMANAGER_FPATH_DSC',"Specify the path installation of FFmpeg.<br />(Example:<tt>/usr/local/bin</tt>, <tt>/usr/bin</tt>)");
define('_MI_FILEMANAGER_FOPT',"[ffmpeg] Option");
define('_MI_FILEMANAGER_FOPT_DSC',"Please specify the command option.・・his version is not available・・");
define('_MI_FILEMANAGER_FCAPTURE',"[ffmpeg] Screen Shot time");
define('_MI_FILEMANAGER_FCAPTURE_DSC',"Time from the beginning of the video to take a screenshot.");
define('_MI_FILEMANAGER_FCONVERT',"[ffmpeg] Maximum size of FLV conversion");
define('_MI_FILEMANAGER_FCONVERT_DSC',"Specify the maximum size of FLV video files to be converted to video format. The unit is MB.");
define('_MI_FILEMANAGER_FMOVIEFILE',"[ffmpeg] File format to convert to FLV");
define('_MI_FILEMANAGER_FMOVIEFILE_DSC',"Separate files format extensions with a '|' pipe.<br />Make sure you use all lowercase characters for the file extension.<br />File format is convert in ffmpeg setting.<br />Default setting is flv|avi|mwv|mov|mpg|qt|mov|3gp|3gp2|mp4");

?>
