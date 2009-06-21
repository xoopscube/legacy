<html>
<head>
</head>
<body>
<center>
<object id="Clip1" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="320" height="260">
	<param name="movie" value="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>">
	<param name="quality" value="high">
	<param name="wmode" value="opaque">
	<param name="bgcolor" value="##ffffff">
	<param name="scale" value="noscale">
	<embed name="Clip1" src="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>" width="320" height="260" quality="high" wmode="opaque" bgcolor="#ffffff0" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">
	</embed>
</object>
</center>
</body>

