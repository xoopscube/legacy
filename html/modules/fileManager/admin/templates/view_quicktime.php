<html>
<head>
</head>
<body>
<center>
<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" width="380" height="250" codebase="http://www.apple.com/qtactivex/qtplugin.cab">
	<param name="src" value="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>">
	<param name="autoplay" value="false">
	<param name="controller" value="true">
	<embed src="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>" width="380" height="250" autoplay="false" controller="true" type="video/quicktime" pluginspage="http://www.apple.com/quicktime/download/">
	</embed>
</object>
</center>
</body>
