
<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
define('_MI_FILEMANAGER_NAME', "FileManager");
define('_MI_FILEMANAGER_DESC', "Contorl your uploads & easy uploading files.");
define('_MI_FILEMANAGER_UPDATE', 'Update');

// --------------------------------------------------------
// Names of admin menu items
// --------------------------------------------------------
define('_MI_FILEMANAGER_MAIN', "File List");
define('_MI_FILEMANAGER_MAIN_DSC', "File list");
define('_MI_FILEMANAGER_UPLOAD', "File Upload");
define('_MI_FILEMANAGER_UPLOAD_DSC', "upload files for uploads folder");

define('_MI_FILEMANAGER_FOLDER', "Folder");
define('_MI_FILEMANAGER_FOLDER_DSC', "dirctory control");
// --------------------------------------------------------
// PreferenceEdit
// --------------------------------------------------------
define('_MI_FILEMANAGER_PATH',"Main Upload Directory");
define('_MI_FILEMANAGER_PATH_DSC',"Please set upload path( under XOOPS_UPLOAD_PATH , / is not need)");
define('_MI_FILEMANAGER_DIRHANDLE',"Upload directory management");
define('_MI_FILEMANAGER_DIRHANDLE_DSC',"Check 'Yes' to allow webmasters to create and delete folders.");
define('_MI_FILEMANAGER_THUMBSIZE',"Thumbnail size");
define('_MI_FILEMANAGER_THUMBSIZE_DSC',"Specify the thumbnail size for the file list.");
define('_MI_FILEMANAGER_DEBUGON',"Turn on the debugging Uploader");
define('_MI_FILEMANAGER_DEBUGON_DSC',"enable upload debuging.");


define('_MI_FILEMANAGER_XOOPSLOCK',"Non-indication of the system image");
define('_MI_FILEMANAGER_XOOPSLOCK_DSC',"System using image is nonindication. Image maneger files, Abters, Smiles");
define('_MI_FILEMANAGER_EXTENSIONS',"Extension of upload files");
define('_MI_FILEMANAGER_EXTENSIONS_DSC',"The extension is seperate each requirement with |. The extension is need type in all lowercase.");

// reserved  options setting 

define('_MI_FILEMANAGER_FUSE',"[ffmpeg]using ffmpeg");
define('_MI_FILEMANAGER_FUSE_DSC',"If you want to use ffmpeg, please select Yes. Ffmpeg is must be supported by the server.<br />Please installed the binary corresponded ffmpeg to your server, or build ffmpeg.");
define('_MI_FILEMANAGER_FPATH',"[ffmpeg]path for ffmpeg");
define('_MI_FILEMANAGER_FPATH_DSC',"Specify the installation ffmpeg path.<br />(Example:<tt>/usr/local/bin</tt><tt>:/usr/bin</tt>)");
define('_MI_FILEMANAGER_FOPT',"[ffmpeg]Option");
define('_MI_FILEMANAGER_FOPT_DSC',"Please specify the command option.（This version is not available）");
define('_MI_FILEMANAGER_FCAPTURE',"[ffmpeg]Screen Shot time");
define('_MI_FILEMANAGER_FCAPTURE_DSC',"Time from the beginning of the video to take a screenshot from the video file (s).");
define('_MI_FILEMANAGER_FCONVERT',"[ffmpeg]Maximum size of FLV conversion");
define('_MI_FILEMANAGER_FCONVERT_DSC',"FLV video files to specify the maximum size to be converted to video format. The unit is MB.");


?>