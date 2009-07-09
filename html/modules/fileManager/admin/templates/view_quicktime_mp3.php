<?php
require_once "../../../../mainfile.php";

$isPlay = false;
// Check file
if (isset($_GET["file"])) {
	$fileName = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES);
	$playfile = XOOPS_UPLOAD_URL. $fileName;
	$filePath = XOOPS_UPLOAD_PATH. $fileName;
	$isPlay = true;
}

if ($isPlay) {
?>
<html>
<head>
</head>
<body>
<center>
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="300" height="100" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
	<param name="src" value="<?php echo $playfile ?>">
	<param name="autoplay" value="false">
	<param name="controller" value="true">
	<embed src="<?php echo $playfile ?>" width="300" height="100" autoplay="true" controller="true" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/">
	</embed>
</object>
</center>
</body>
<?php
} else {
	echo _AD_FILEMANAGER_NOTFOUND;
}
?>
