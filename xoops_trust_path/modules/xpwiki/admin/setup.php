<?php
/*
 * Created on 2007/05/13 by nao-pon http://hypweb.net/
 * $Id: setup.php,v 1.9 2011/11/26 12:03:10 nao-pon Exp $
 */

$ng = $out = '';

// Install setting
if (! is_file($mydirpath . '/.installed')) {

	// Set imagemagick, jpegtran path.
	$out .= "* Now imagemagick & jpegtran path setting.\n";
	$dat = $path = "";

	if ( substr(PHP_OS, 0, 3) !== 'WIN' ) {

		$dat .= "<?php\n";

		if (is_file(XOOPS_ROOT_PATH.'/class/hyp_common/image_magick.cgi')) {
			$image_magick_cgi = XOOPS_ROOT_PATH.'/class/hyp_common/image_magick.cgi';
		} else {
			$image_magick_cgi = $mydirpath . '/include/hyp_common/image_magick.cgi';
		}
		if (chmod($image_magick_cgi, 0705)) {
			$out .= '- chmod( '. $image_magick_cgi . ', 0705 ) - OK.' . "\n";
			$_path = str_replace(XOOPS_ROOT_PATH, XOOPS_URL, $image_magick_cgi);
			$dat .= "define('HYP_IMAGE_MAGICK_URL', '{$_path}');\n";
		} else {
			$ng  .= '- chmod( '. $mydirpath . '/include/hyp_common/image_magick.cgi, 0705 ) - NG.' . "\n";
		}

		$exec = array();
		exec( "whereis -b kakasi" , $exec) ;
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

		if (!$ng) {
			@ touch ($mydirpath . '/.installed');
		}
	}

	$filename = XOOPS_TRUST_PATH . '/class/hyp_common/execpath.inc.php';

	if ($dat && ($fp = @fopen($filename,"wb")))
	{
		fputs($fp, $dat);
		fclose($fp);
		$out .= "Edited a file ( {$filename} ) - OK\n";
	}
	else
	{
		$ng .= "Edited a file ( {$filename} ) - NG\n";
	}

	// permission
	$out .= "* Now permission setting.\n";

	$dirs = array(
		'attach',
		'attach/s',
		'private/backup',
		'private/cache',
		'private/cache/page',
		'private/cache/plugin',
		'private/counter',
		'private/diff',
		'private/trackback',
		'private/wiki'
	);
	foreach($dirs as $dir) {
		if (chmod($mydirpath . '/' . $dir, 0707)) {
			$out .= '- chmod( '.$mydirpath .'/'.$dir.', 0707 ) - OK.' . "\n";
		} else {
			$ng  .= '- chmod( '.$mydirpath .'/'.$dir.', 0707 ) - NG.' . "\n";
		}
	}
	if (chmod(XOOPS_TRUST_PATH . '/class/hyp_common/favicon/cache', 0707)){
		$out .= '- chmod( '.XOOPS_TRUST_PATH . '/class/hyp_common/favicon/cache, 0707 ) - OK.' . "\n";
	} else {
		$ng  .= '- chmod( '.XOOPS_TRUST_PATH . '/class/hyp_common/favicon/cache, 0707 ) - NG.' . "\n";
	}
	$out .= str_repeat('-', 40) . "\n";
}

// VerUP to 2
if (@ $myhtml_version < 2) {

	$base = $mydirpath . '/';
	$trust = XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/ID/VerUp/2/';
	$rmfiles = array('blocks.php');
	$mkdirs = array('blocks');
	$cpfiles = array('mytrustdirname.php','attach/s/.htaccess','blocks/blocks.php');

	files_copy ($base, $trust, $rmfiles, $mkdirs, $cpfiles);
}

// VerUP to 3
if (@ $myhtml_version < 3) {

	$base = $mydirpath . '/';
	$trust = XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/ID/VerUp/3/';
	$rmfiles = array();
	$mkdirs = array();
	$cpfiles = array(
		'gate.php',
		'mytrustdirname.php',
		'notification_update.php',
		'notification.php',
		'xoops_uname.php',
		'private/wiki/.cvsignore',
		'skin/pukiwiki/pukiwiki.skin.php',
		'skin/xpwiki/pukiwiki.skin.php',
	);

	files_copy ($base, $trust, $rmfiles, $mkdirs, $cpfiles);
}

// VerUP to 4
if (@ $myhtml_version < 4) {

	$base = $mydirpath . '/';
	$trust = XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/ID/VerUp/4/';
	$rmfiles = array();
	$mkdirs = array();
	$cpfiles = array(
		'gate.php',
		'mytrustdirname.php'
	);

	files_copy ($base, $trust, $rmfiles, $mkdirs, $cpfiles);
}

// VerUP to 5
if (@ $myhtml_version < 5) {

	$base = $mydirpath . '/';
	$trust = XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/ID/VerUp/5/';
	$rmfiles = array();
	$mkdirs = array();
	$cpfiles = array(
		'gate.php',
		'index.php',
		'mytrustdirname.php',
		'xoops_version.php',
		'skin/loader.php',
		'skin/plain/pukiwiki.skin.php'
	);

	files_copy ($base, $trust, $rmfiles, $mkdirs, $cpfiles);
}

// Finish
$out .= "All processing was completed.\n";

if ($ng) {
	$out .= "But next commands was not executed. Please do yourself by FTP etc.\n";
	$out .= $ng;
	$out .= str_repeat('-', 40) . "\n";
}

chmod($mydirpath . '/admin/setup.cgi', 0400);

if (php_sapi_name() == "cli")
{
	echo "Content-Length: ".strlen($out)."\n";
	echo "Content-Type: text/plain\n\n";
}
else
{
	@ini_set('default_charset','');
	@mb_http_output('pass');
	header("Content-Length: ".strlen($out));
	header("Content-Type: text/plain");
}
echo $out;
exit();

// Functions

function files_copy ($base, $trust, $rmfiles, $mkdirs, $cpfiles) {
	global $out, $ng;

	$out .= "* Now copying new files.\n";

	foreach($rmfiles as $file) {
		if (is_file($base . $file)) {
			if (@ unlink($base . $file)) {
				$out .= '- Delete file( '.$base . $file .' ) - OK.' . "\n";
			} else {
				$ng  .= '- Delete file( '.$base . $file .' ) - NG.' . "\n";
			}
		}
	}

	foreach($mkdirs as $dir) {
		if (! is_dir($base . $dir)) {
			if (@ mkdir($base . $dir)) {
				$out .= '- Make dirctory( '.$base . $dir .' ) - OK.' . "\n";
			} else {
				$ng  .= '- Make dirctory( '.$base . $dir .' ) - NG.' . "\n";
			}
		}
	}

	foreach($cpfiles as $file) {
		if (@ copy($trust . $file, $base . $file)) {
			$out .= '- File copy ('.$trust . $file .' TO ' . $base . $file . ' ) - OK.' . "\n";
		} else {
			$ng  .= '- File copy ('.$trust . $file .' TO ' . $base . $file . ' ) - NG.' . "\n";
		}
	}

	$out .= str_repeat('-', 40) . "\n";

}
?>