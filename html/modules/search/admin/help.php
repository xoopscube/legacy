<?php
# show language help.html
# $Id: help.php,v 1.1 2007-02-23 05:27:28 nobu Exp $

include '../../../include/cp_header.php';

// for compat older PHP 4.x
if(!function_exists("file_get_contents")) {
   function file_get_contents($filename) {
       $fp = fopen($filename, "rb");
       if (!$fp) return false;
       $contents = "";
       while (! feof($fp)) {
	   $contents .= fread($fp, 4096);
       }
       return $contents;
   }
}

xoops_cp_header();
include 'mymenu.php';

$myts =& MyTextSanitizer::getInstance();
$file = isset($_GET['file'])?
    $myts->stripSlashesGPC($_GET['file']):"/help/help.html";
display_lang_file($file);
xoops_cp_footer();
exit;

// show under language/XX/$file only <body> part.
function display_lang_file($file, $link='') {
    global $xoopsConfig, $xoopsModule;
    if (empty($link)) {
	$link = preg_replace('/[&\?]?file=[^&]*|\?$/', '', $_SERVER['REQUEST_URI']);
	$link .= preg_match('/\?/', $link)?'&':'?';
	$link .= 'file=';
    }
    $file = preg_replace('/^\/+/','',preg_replace('/\/?\\.\\.?\/|\/+/', '/', $file));
    $lang = "language/".$xoopsConfig['language'];
    $help = "../$lang/$file";
    if (!file_exists($help)) {
	$lang = 'language/english';
	$help = "../$lang/$file";
    }
    $content = file_get_contents($help);
    list($h, $b) = preg_split('/<\/?body>/', $content);
    if (empty($b)) $b =& $content;
    $murl = XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname');

    if (preg_match('/<link[^>]*>/', $b, $match)) {
	foreach ($match as $item) {
	    if (preg_match('/href=[\"\']?([^\"\']+)/', $item, $d)) {
		$x = preg_replace('/'.preg_quote($d[1],'/').'/', "../$lang/".$d[1], $item);
		$b = preg_replace('/'.preg_quote($item, '/').'/', $x, $b);
	    }
	}
    }
    // link image
    // need quote! (sence has protocol)
    // follow only 1 level depth folder
    $pat = array('/\ssrc=\'([^#][^\':]*)\'/',
		 '/\ssrc="([^#][^":]*)"/',
		 '/\shref=\'([^#\\.][^\':]*)\'/',
		 '/\shref="([^#\\.][^\':]*)"/',
		 '/\shref=([\'"]?)\\.\\.\\/\\.\\.\\//',
	);
    $rep = array(" src='../$lang/\$1'",
		 " src=\"../$lang/\$1\"",
		 " href='$link\$1'",
		 " href=\"$link\$1\"",
		 " href=$1$murl/",
	);
    echo preg_replace($pat, $rep, $b);
}

?>