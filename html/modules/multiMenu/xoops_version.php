<?php
$modversion['name'] = _IM_MULTIMENU_MODULE;
$modversion['version'] = 1.17 ;
$modversion['description'] = _IM_MULTIMENU_DESC;
$modversion['credits'] = "
Original :Solo&nbsp;<a href='http://www.wolfpackclan.com'>Wolf Pack Clan</a><br />
luinithil&nbsp;<a href='http://www.luinithil.com'>luinithil</a><br />
Tom&nbsp;<a href='http://malaika.s31.xrea.com/'>Malaika System</a>";
$modversion['author'] = "Yoshi Sakai&nbsp;a.k.a&nbsp;<a href='http://bluemooninc.jp/'>Bluemoon inc.</a>";
$modversion['help'] = "help.html";
$modversion['license'] = "GPL see LICENSE";
$modversion['official'] = 0;
$modversion['image'] = "images/multimenu_slogo.jpg";
$modversion['dirname'] = "multiMenu";

$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][0] = "multimenu01";
$modversion['tables'][1] = "multimenu02";
$modversion['tables'][2] = "multimenu03";
$modversion['tables'][3] = "multimenu04";
$modversion['tables'][4] = "multimenu05";
$modversion['tables'][5] = "multimenu06";
$modversion['tables'][6] = "multimenu07";
$modversion['tables'][7] = "multimenu08";

$modversion['hasMain'] = 0;

$modversion['blocks'][1]['file'] = "multimenu.php";
$modversion['blocks'][1]['name'] = _IM_MULTIMENU_NAME;
$modversion['blocks'][1]['description'] = "link menu";
$modversion['blocks'][1]['show_func'] = "a_multimenu_show";
$modversion['blocks'][1]['edit_func'] = "a_multimenu_edit";
$modversion['blocks'][1]['options'] = "40";
$modversion['blocks'][1]['template'] = 'multimenu_block.html';

$modversion['blocks'][2]['file'] = "multimenu.php";
$modversion['blocks'][2]['name'] = _IM_MULTIMENU_NAME_1;
$modversion['blocks'][2]['description'] = "link menu";
$modversion['blocks'][2]['show_func'] = "b_multimenu_show";
$modversion['blocks'][2]['edit_func'] = "b_multimenu_edit";
$modversion['blocks'][2]['options'] = "40";
$modversion['blocks'][2]['template'] = 'multimenu_block01.html';

$modversion['blocks'][3]['file'] = "multimenu.php";
$modversion['blocks'][3]['name'] = _IM_MULTIMENU_NAME_2;
$modversion['blocks'][3]['description'] = "link menu";
$modversion['blocks'][3]['show_func'] = "c_multimenu_show";
$modversion['blocks'][3]['edit_func'] = "c_multimenu_edit";
$modversion['blocks'][3]['options'] = "40";
$modversion['blocks'][3]['template'] = 'multimenu_block02.html';

$modversion['blocks'][4]['file'] = "multimenu.php";
$modversion['blocks'][4]['name'] = _IM_MULTIMENU_NAME_3;
$modversion['blocks'][4]['description'] = "link menu";
$modversion['blocks'][4]['show_func'] = "d_multimenu_show";
$modversion['blocks'][4]['edit_func'] = "d_multimenu_edit";
$modversion['blocks'][4]['options'] = "40";
$modversion['blocks'][4]['template'] = 'multimenu_block03.html';

$modversion['blocks'][5]['file'] = "multimenu.php";
$modversion['blocks'][5]['name'] = _IM_MULTIMENU_NAME_4;
$modversion['blocks'][5]['description'] = "link menu";
$modversion['blocks'][5]['show_func'] = "e_multimenu_show";
$modversion['blocks'][5]['edit_func'] = "e_multimenu_edit";
$modversion['blocks'][5]['options'] = "40";
$modversion['blocks'][5]['template'] = 'multimenu_block04.html';

$modversion['blocks'][6]['file'] = "multimenu.php";
$modversion['blocks'][6]['name'] = _IM_MULTIMENU_NAME_5;
$modversion['blocks'][6]['description'] = "link menu";
$modversion['blocks'][6]['show_func'] = "f_multimenu_show";
$modversion['blocks'][6]['edit_func'] = "f_multimenu_edit";
$modversion['blocks'][6]['options'] = "40";
$modversion['blocks'][6]['template'] = 'multimenu_block05.html';

$modversion['blocks'][7]['file'] = "multimenu.php";
$modversion['blocks'][7]['name'] = _IM_MULTIMENU_NAME_6;
$modversion['blocks'][7]['description'] = "link menu";
$modversion['blocks'][7]['show_func'] = "g_multimenu_show";
$modversion['blocks'][7]['edit_func'] = "g_multimenu_edit";
$modversion['blocks'][7]['options'] = "40";
$modversion['blocks'][7]['template'] = 'multimenu_block06.html';

$modversion['blocks'][8]['file'] = "multimenu.php";
$modversion['blocks'][8]['name'] = _IM_MULTIMENU_NAME_7;
$modversion['blocks'][8]['description'] = "link menu";
$modversion['blocks'][8]['show_func'] = "h_multimenu_show";
$modversion['blocks'][8]['edit_func'] = "h_multimenu_edit";
$modversion['blocks'][8]['options'] = "40";
$modversion['blocks'][8]['template'] = 'multimenu_block07.html';

$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index01.php";
$modversion['adminmenu'] = "admin/menu.php";

// Config
$modversion['hasconfig'] = 1;
$modversion['config'][] = array(
	'name' => 'css_file',
	'title' => 'CSS File URL',
	'description' => 'CSS File URL',
	'formtype' => 'text',
	'valuetype' => 'text',
	'default' => ''
);
$modversion['config'][] = array(
	'name'        => 'theme_menu',
	'title'       => 'Theme Menu Block',
	'description' => 'Make Theme Menu Structure',
	'valuetype'   => 'int',
	'formtype'    => 'select',
	'options'     => array(_NO=>0, 
		_IM_MULTIMENU_NAME   =>1,
		_IM_MULTIMENU_NAME_1 =>2,
		_IM_MULTIMENU_NAME_2 =>3,
		_IM_MULTIMENU_NAME_3 =>4,
		_IM_MULTIMENU_NAME_4 =>5,
		_IM_MULTIMENU_NAME_5 =>6,
		_IM_MULTIMENU_NAME_6 =>7,
		_IM_MULTIMENU_NAME_7 =>8,
		),
	'default'     => 1
);
?>