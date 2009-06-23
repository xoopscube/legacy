
<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
define('_MI_FILEMANAGER_NAME', "FileManager");
define('_MI_FILEMANAGER_DESC', "File and folder management with multiple files upload.");
define('_MI_FILEMANAGER_UPDATE', 'Update');

// --------------------------------------------------------
// Names of admin menu items
// --------------------------------------------------------
define('_MI_FILEMANAGER_MAIN', "File List");
define('_MI_FILEMANAGER_MAIN_DSC', "File list with thumbnails and image viewer");
define('_MI_FILEMANAGER_UPLOAD', "File Upload");
define('_MI_FILEMANAGER_UPLOAD_DSC', "Upload files to the uploads directory");

define('_MI_FILEMANAGER_FOLDER', "Folder");
define('_MI_FILEMANAGER_FOLDER_DSC', "dirctory management and permissions");
// --------------------------------------------------------
// PreferenceEdit
// --------------------------------------------------------
define('_MI_FILEMANAGER_PATH',"Main Upload Directory");
define('_MI_FILEMANAGER_PATH_DSC',"Define the upload path under /uploads/ (folder name only, without '/' slash)");
define('_MI_FILEMANAGER_DIRHANDLE',"Upload Directory Management");
define('_MI_FILEMANAGER_DIRHANDLE_DSC',"Allow or not webmasters to create and delete folders.");
define('_MI_FILEMANAGER_THUMBSIZE',"Thumbnail size");
define('_MI_FILEMANAGER_THUMBSIZE_DSC',"Specify the maximum width of thumbnails for the file list.");
define('_MI_FILEMANAGER_DEBUGON',"Turn on the debugging Uploader");
define('_MI_FILEMANAGER_DEBUGON_DSC',"Enable or not debug console of uploader that is rendered inside an iframe.");


define('_MI_FILEMANAGER_XOOPSLOCK',"Hide system images?");
define('_MI_FILEMANAGER_XOOPSLOCK_DSC',"Display or not files from 'Image Manager' (ie. avatars, smilies, etc");
define('_MI_FILEMANAGER_EXTENSIONS',"File extension allowed for upload");
define('_MI_FILEMANAGER_EXTENSIONS_DSC',"Separate files extensions with a '|' pipe.<br />Make sure you use all lowercase characters for the file extension.");

// reserved  options setting 

define('_MI_FILEMANAGER_FUSE',"[ffmpeg] Use FFmpeg");
define('_MI_FILEMANAGER_FUSE_DSC',"FFmpeg is a complete, cross-platform solution to record, convert and stream audio and video.<br /> FFmpeg must be supported by the server. If not, Install the FFmpeg binary to your server.");
define('_MI_FILEMANAGER_FPATH',"[ffmpeg] Path of FFmpeg");
define('_MI_FILEMANAGER_FPATH_DSC',"Specify the path installation of FFmpeg.<br />(Example:<tt>/usr/local/bin</tt>, <tt>/usr/bin</tt>)");
define('_MI_FILEMANAGER_FOPT',"[ffmpeg] Option");
define('_MI_FILEMANAGER_FOPT_DSC',"Please specify the command option.（This version is not available）");
define('_MI_FILEMANAGER_FCAPTURE',"[ffmpeg] Screen Shot time");
define('_MI_FILEMANAGER_FCAPTURE_DSC',"Time from the beginning of the video to take a screenshot.");
define('_MI_FILEMANAGER_FCONVERT',"[ffmpeg] Maximum size of FLV conversion");
define('_MI_FILEMANAGER_FCONVERT_DSC',"Specify the maximum size of FLV video files to be converted to video format. The unit is MB.");


?>