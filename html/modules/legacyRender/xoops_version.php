<?php
/**
 * @version $Id: xoops_version.php,v 1.12 2008/10/12 03:55:38 minahito Exp $
 * @package legacyRender
 */

$modversion['name']=_MI_LEGACYRENDER_NAME;
$modversion['version']=2.01;
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
		"default"=>"news, technology, headlines, xoops, cube, legacy, social, network, module, plugin, theme, templates, css, web 2.0, ajax, cms, content management, software, blog, download, downloads, free, community, mp3, forum, forums, bulletin, board, boards, bbs, php, survey, poll, polls, kernel, comment, comments, portal, odp, open, source, opensource, FreeSoftware, bsd, gnu, gpl, license, Unix, *nix, mysql, sql, database, databases, web site"
	);

$modversion['config'][]=array (
		"name"=>"meta_description",
		"title"=>"_MI_LR_META_DESCRIPTION",
		"description"=>"_MI_LR_META_DESCRIPTION_DESC",
		"formtype"=>"textarea",
		"valuetype"=>"text",
		"default"=>"XOOPS Cube Legacy is a dynamic Object Oriented based open source portal script written in PHP."
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
		"default"=>"Copyright &copy; 2001-2011"
	);

$modversion['config'][]=array (
		"name"=>"footer",
		"title"=>"_MI_LR_FOOTER",
		"description"=>"_MI_LR_FOOTER_DESC",
		"formtype"=>"textarea",
		"valuetype"=>"text",
		"default"=>"Powered by <a href=\"http://xoopscube.org/\" rel=\"external\">XOOPS Cube</a> 2.2 &copy; 2001-2011 <a href=\"http://xoopscube.sourceforge.net/\" rel=\"external\">XOOPS Cube Project</a>"
	);

$modversion['config'][]=array (
		"name"=>"banners",
		"title"=>"_MI_LEGACYRENDER_CONF_BANNERS",
		"formtype"=>"yesno",
		"valuetype"=>"int",
		"default"=>0
	);

$modversion['config'][]=array (
		"name"=>"pagetitle",
		"title"=>"_MI_LR_PAGETITLE_FORMAT",
		"description"=>"_MI_LR_PAGETITLE_FORMAT_DESC",
		"formtype"=>"textbox",
		"valuetype"=>"text",
		"default"=>"{modulename} {action} [pagetitle]:[/pagetitle] {pagetitle}"
	);

$modversion['config'][]=array (
		"name"=>"css_file",
		"title"=>"_MI_LR_CSS_FILE",
		"description"=>"_MI_LR_CSS_FILE_DESC",
		"formtype"=>"textbox",
		"valuetype"=>"text",
		"default"=>"http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/smoothness/jquery-ui.css"
	);

$modversion['config'][]=array (
		"name"=>"feed_url",
		"title"=>"_MI_LR_FEED_URL",
		"description"=>"_MI_LR_FEED_URL_DESC",
		"formtype"=>"textbox",
		"valuetype"=>"text",
		"default"=>""
	);

$modversion['config'][]=array (
		"name"=>"jquery_core",
		"title"=>"_MI_LR_JQUERY_CORE",
		"description"=>"_MI_LR_JQUERY_CORE_DESC",
		"formtype"=>"textbox",
		"valuetype"=>"text",
		"default"=>"1"
	);

$modversion['config'][]=array (
		"name"=>"jquery_ui",
		"title"=>"_MI_LR_JQUERY_UI",
		"description"=>"_MI_LR_JQUERY_UI_DESC",
		"formtype"=>"textbox",
		"valuetype"=>"text",
		"default"=>"1"
	);

// Menu
$modversion['hasMain']=0;

?>
