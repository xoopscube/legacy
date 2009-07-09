<?php
/**
 * Filemaneger
 * (C)2007-2009 BeaBo Japan by Hiroki Seike
 * http://beabo.net/
 **/

require_once "../../../../mainfile.php";

$isPlay = false;
// Check file
if (isset($_GET["file"])) {
	$fileName = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES);
	$playfile = XOOPS_UPLOAD_URL. $fileName;
	$filePath = XOOPS_UPLOAD_PATH. $fileName;
	$filename = htmlspecialchars(urldecode($_GET["name"]), ENT_QUOTES);
	$isPlay = true;
}

if ($isPlay) {
?>
<html>
<head>
<script src="../../../../common/flowplayer/flowplayer-3.1.1.min.js"></script> 
</head>
<body>
<center>
<!-- setup player container -->
<div id="player" style="width:425px;height:300px"></div>
<!-- initialize flowplayer -->
<script type="text/javascript">
// Flowplayer installation with Flashembed parameters
flowplayer("player", {
	// Flash component
	src: "../../../../common/flowplayer/flowplayer-3.1.1.swf",
	// we need at least this version
	version: [9, 115],
	// older versions will see a custom message
	onFail: function()  {
		document.getElementById("info").innerHTML =
			"You need the latest Flash version to view MP4 movies. " +
			"Your version is " + this.getVersion()
		;
	}
}, {
	// Play file
	clip: "<?php echo $playfile ?>"
});
</script>
</center>
</body>
<?php
} else {
	echo _AD_FILEMANAGER_NOTFOUND;
}
?>
