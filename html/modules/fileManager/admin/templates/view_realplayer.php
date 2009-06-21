<html>
<head>
</head>
<body>
<center>
<object id="Clip1" classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="320" height="260">
	<param name="controls" value="ImageWindow">
	<param name="console" value="Clip1">
	<param name="autostart" value="false">
	<param name="maintainaspect" value="true">
	<param name="nojava" value="true">
	<param name="src" value="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>">
	<embed type="audio/x-pn-realaudio-plugin" src="<?php if (isset($_GET["file"])) { $file = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES); } else { $file = ''; } echo $file ?>" width="320" height="260" nojava="true" console="Clip1" controls="ImageWindow" autostart="false" pluginspage="http://www.real.com/player/index.html">
	</embed>
</object>
</center>
</body>
