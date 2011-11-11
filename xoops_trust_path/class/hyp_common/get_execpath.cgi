<?php
$dat = $path = "";

$dat .= "<?php\n";

$image_magick_cgi = XOOPS_ROOT_PATH.'/class/hyp_common/image_magick.cgi';
if (file_exists($image_magick_cgi)) {
	@ chmod($image_magick_cgi, 0705);
	$_path = str_replace(XOOPS_ROOT_PATH, XOOPS_URL, $image_magick_cgi);
	$dat .= "define('HYP_IMAGE_MAGICK_URL', '{$_path}');\n";
}

$exec = array();
@ exec( "whereis -b kakasi" , $exec) ;
if ($exec)
{
	$path = array_pad(explode(" ",$exec[0]),2,"");
	$path = (preg_match("#^(/.+/)kakasi$#",$path[1],$match))? $match[1] : "";
	$dat .= "define('HYP_KAKASI_PATH', '{$path}');\n";
}

$exec = array();
@ exec( "whereis -b convert" , $exec) ;
if ($exec)
{
	$path = array_pad(explode(" ",$exec[0]),2,"");
	$path = (preg_match("#^(/.+/)convert$#",$path[1],$match))? $match[1] : "";
	$dat .= "define('HYP_IMAGEMAGICK_PATH', '{$path}');\n";
}

$exec = array();
@ exec( "whereis -b jpegtran" , $exec) ;
if ($exec)
{
	$path = array_pad(explode(" ",$exec[0]),2,"");
	$path = (preg_match("#^(/.+/)jpegtran$#",$path[1],$match))? $match[1] : "";
	$dat .= "define('HYP_JPEGTRAN_PATH', '{$path}');\n";
}
$dat .= "?>\n";

@ chmod(dirname(__FILE__).'/favicon/cache', 0707);
@ chmod(XOOPS_ROOT_PATH.'/class/hyp_common/cache', 0707);

@ chmod(XOOPS_TRUST_PATH.'/cache', 0707);
@ chmod(XOOPS_TRUST_PATH.'/uploads/hyp_common', 0707);
@ chmod(XOOPS_TRUST_PATH.'/uploads/hyp_common/kakasi', 0707);

$filename = dirname(__FILE__)."/execpath.inc.php";

if ($fp = @fopen($filename,"wb"))
{
	fputs($fp, $dat);
	fclose($fp);
	chmod("get_execpath.cgi", 0600);
	chmod("image_magick.cgi", 0705);
	if (php_sapi_name() == "cli")
	{
		echo "Content-Type: text/plain\n\n";
	}
	else
	{
		header("Content-Type: text/plain");
	}
	echo "Made a file '{$filename}'. It's OK.";
}
else
{
	if (php_sapi_name() == "cli")
	{
		echo "Content-Disposition: attachment; filename=\"{$filename}\"\n";
		echo "Content-Length: ".strlen($dat)."\n";
		echo "Content-Type: text/plain\n\n";
	}
	else
	{
		@ini_set('default_charset','');
		@mb_http_output('pass');
		header("Content-Disposition: attachment; filename=\"$filename\"");
		header("Content-Length: ".strlen($dat));
		header("Content-Type: text/plain");
	}
	echo $dat;
}
exit();
?>