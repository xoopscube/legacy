<?php
require_once "../../../../mainfile.php";

$isPlay = false;
// Check file
if (isset($_GET["file"])) {
	$fileName = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES);
	$playfile = XOOPS_UPLOAD_URL. $fileName;
	$filePath = XOOPS_UPLOAD_PATH. $fileName;
//	$filename = htmlspecialchars(urldecode($_GET["name"]), ENT_QUOTES);
	$isPlay = true;
}

if ($isPlay) {
?>
<html>
<head>
</head>
<body>
<center>
<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" ID="mplayer" width="450" height="300">
	<param name="src" value="<?php echo $playfile ?>">
	<param name="ShowStatusBar" value="false">
	<param name="AutoStart" value="true">
	<param name="enableContextMenu" value="false">
	<param name="stretchToFit" value="true">
	<param name="uiMode" value="full">
	<param name="Volume" value="80">
	<embed src="<?php echo $playfile ?>" type="application/x-mplayer2" width="450" height="300" AnimationStart="1" PlayCount="1" ShowControls="1" EnablePositionControls="1" ShowPositionControls="1" ShowAudioControls="1" ShowTracker="1" ShowStatusBar="1" CanSeek="1" AutoSize="0" AllowScan="0" AutoStart="1" ClickToPlay="0" EnableContextmenu="0" TransparentStart="0" Volume="100" pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/">
	</embed>
</object>
</center>
</body>
<?php
} else {
	echo _AD_FILEMANAGER_NOTFOUND;
}
?>

