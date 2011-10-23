<?php
/**
 * @version $Id: xoops_version.php,v 1.1 2008/03/09 02:26:16 minahito Exp $
 * @package legacyRender
 */

$modversion['name']=_MI_LEGACYRENDER_NAME;
$modversion['version']=1.03;
$modversion['description']=_MI_LEGACYRENDER_NAME_DESC;
$modversion['author']="";
$modversion['credits']="The XOOPS Cube Project";
$modversion['help'] = "help.html";
$modversion['license']="GPL see LICENSE";
$modversion['image']="images/legacyRender.png";
$modversion['dirname']="legacyRender";

$modversion['cube_style'] = true;

//
// SQL File
//
// $modversion['sqlfile']['mysql'] = "sql/mysql.sql";
// $modversion['tables'][] = "legacyrender_theme";

//
// Template
//
$modversion['templates'][1]['file']="legacy_render_dialog.html";

//
// Admin things
//
$modversion['hasAdmin']=1;
$modversion['adminindex']="admin/index.php";
$modversion['adminmenu']="admin/menu.php";

//Preference
$modversion['config'][]=array (
		"name"=>"meta_keywords",
		"title"=>"_MI_LR_META_KEYWORDS",
		"description"=>"_MI_LR_META_KEYWORDS_DESC",
		"formtype"=>"textarea",
		"valuetype"=>"text",
		"default"=>"news, technology, headlines, xoops, xoop, nuke, myphpnuke, myphp-nuke, phpnuke, SE, geek, geeks, hacker, hackers, linux, software, download, downloads, free, community, mp3, forum, forums, bulletin, board, boards, bbs, php, survey, poll, polls, kernel, comment, comments, portal, odp, open, source, opensource, FreeSoftware, gnu, gpl, license, Unix, *nix, mysql, sql, database, databases, web site, weblog, guru, module, modules, theme, themes, cms, content management"
	);

$modversion['config'][]=array (
		"name"=>"meta_description",
		"title"=>"_MI_LR_META_DESCRIPTION",
		"description"=>"_MI_LR_META_DESCRIPTION_DESC",
		"formtype"=>"textarea",
		"valuetype"=>"text",
		"default"=>"XOOPS Cube is a dynamic Object Oriented based open source portal script written in PHP."
	);

$modversion['config'][]=array (
		"name"=>"meta_robots",
		"title"=>"_MI_LR_META_ROBOTS",
		"description"=>"_MI_LR_META_ROBOTS_DESC",
		"formtype"=>"select",
		"valuetype"=>"text",
		"options"=>array("_MI_LR_ROBOT_INDEXFOLLOW"=>"index,follow","_MI_LR_ROBOT_NOINDEXFOLLOW"=>"noindex,follow","_MI_LR_ROBOT_INDEXNOFOLLOW"=>"index,nofollow","_MI_LR_ROBOT_NOINDEXNOFOLLOW"=>"noindex,nofollow"),
		"default"=>"index,follow"
	);

$modversion['config'][]=array (
		"name"=>"meta_rating",
		"title"=>"_MI_LR_META_RATING",
		"description"=>"_MI_LR_META_RATING_DESC",
		"formtype"=>"select",
		"valuetype"=>"text",
		"options"=>array("_MI_LR_ROBOT_METAOGEN"=>"general","_MI_LR_ROBOT_METAO14YRS"=>"14 years","_MI_LR_ROBOT_METAOREST"=>"restricted","_MI_LR_ROBOT_METAOMAT"=>"mature"),
		"default"=>"general"
	);

$modversion['config'][]=array (
		"name"=>"meta_author",
		"title"=>"_MI_LR_META_AUTHOR",
		"description"=>"_MI_LR_META_AUTHOR_DESC",
		"formtype"=>"textbox",
		"valuetype"=>"text",
		"default"=>"XOOPS Cube"
	);

$modversion['config'][]=array (
		"name"=>"meta_copyright",
		"title"=>"_MI_LR_META_COPYRIGHT",
		"description"=>"_MI_LR_META_COPYRIGHT_DESC",
		"formtype"=>"textbox",
		"valuetype"=>"text",
		"default"=>"Copyright &copy; 2000-2008"
	);

$modversion['config'][]=array (
		"name"=>"footer",
		"title"=>"_MI_LR_FOOTER",
		"description"=>"_MI_LR_FOOTER_DESC",
		"formtype"=>"textarea",
		"valuetype"=>"text",
		"default"=>"Powered by XOOPS Cube 2.1&copy; 2000-2008 <a href=\"http://xoopscube.sourceforge.net/\" target=\"_blank\">XOOPS Cube Project</a>"
	);

$modversion['config'][]=array (
		"name"=>"banners",
		"title"=>"_MI_LEGACYRENDER_CONF_BANNERS",
		"formtype"=>"yesno",
		"valuetype"=>"int",
		"default"=>0
	);
	
// Menu
$modversion['hasMain']=0;

?>
