<?php
require_once "../../../../mainfile.php";

// get movie file name
if (isset($_GET["file"])) {
	$playfile = htmlspecialchars(urldecode($_GET["file"]), ENT_QUOTES);
} else {
	$playfile = '';
}

// make image file name
$fileNameCount = strlen($playfile) - strlen(strrchr( $playfile, "." )) ;
$image_file = substr($playfile, 0, $fileNameCount). ".jpg"; 

// preview fimage file check
if (file_exists(XOOPS_UPLOAD_PATH . $image_file)) {
	$view_image = true;
} else {
	$view_image = false;
}
?>

<html>
<head>
</head>
<body>
<center>
<div id="container_view"><a href="http://www.macromedia.com/go/getflashplayer">Get the Flash Player</a> to see this player.</div>
<script type="text/javascript" src="../../../../common/JWFLVmediaplayer/swfobject.js"></script>
<script type="text/javascript">
    var s1 = new SWFObject("../../../../common/JWFLVmediaplayer/mediaplayer.swf","mediaplayer","320","240","7");
    s1.addParam("allowfullscreen","true");
    s1.addVariable("width","320");
    s1.addVariable("height","240");
<?php if($view_image) echo '    s1.addVariable("image","'. XOOPS_UPLOAD_URL .$image_file. '");';?>
    s1.addVariable("file","<?php echo  XOOPS_UPLOAD_URL .$playfile ?>");
    s1.write("container_view");
</script>
</center>
</body>
