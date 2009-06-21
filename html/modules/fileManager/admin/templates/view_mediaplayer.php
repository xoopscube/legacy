<html>
<head>
</head>
<body>
<center>
<object classid="clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95" ID="mplayer" width="320" height="240">
	<param name="src" value="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>">
	<param name="ShowStatusBar" value="false">
	<param name="AutoStart" value="true">
	<param name="enableContextMenu" value="false">
	<param name="stretchToFit" value="true">
	<param name="uiMode" value="full">
	<param name="Volume" value="80">
	<embed src="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>" type="application/x-mplayer2" width="320" height="240" AnimationStart="1" PlayCount="1" ShowControls="1" EnablePositionControls="1" ShowPositionControls="1" ShowAudioControls="1" ShowTracker="1" ShowStatusBar="1" CanSeek="1" AutoSize="0" AllowScan="0" AutoStart="1" ClickToPlay="0" EnableContextmenu="0" TransparentStart="0" Volume="100" pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/">
	</embed>
</object>
</center>
</body>


