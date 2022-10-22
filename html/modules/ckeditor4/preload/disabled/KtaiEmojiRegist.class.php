<?php

if ( !defined('XOOPS_ROOT_PATH') ) exit;

class ckeditor4_KtaiEmojiRegist extends XCube_ActionFilter
{
	private $emj2i_table;
	
	public function preBlockFilter() {
		$this->mRoot->mDelegateManager->add('Ckeditor4.Utils.PostBuild_ckconfig', array(&$this, 'regist'));  
	}

	public function regist(&$config) {
		$emj2i = XOOPS_TRUST_PATH . '/class/hyp_common/mpc/Carrier/map/emj2i_table.php';
		
		if (! is_readable($emj2i)) return;
		
		if (empty($this->emj2i_table)) {
			require XOOPS_TRUST_PATH . '/class/hyp_common/mpc/Carrier/map/emj2i_table.php';
		}
		
		$emj_list = array(
			140,141,142,143,144,1021,1022,1023,1024,1025,1026,1027,1029,1030,1031,1032,
			1033,1034,1035,1071,1072,1076,145,156,150,151,152,157,158,162,163,164,
			146,155,147,149,136,137,138,139,154,153,1028,86,87,88,85,84,
			1,2,3,4,5,6,8,1052,172,100,101,1068,1069,1070,1073,1074,
			9,10,11,12,13,14,15,16,17,18,19,20,80,81,82,83,
			1011,75,110,105,106,107,74,1012,76,103,1054,1056,1058,1059,1060,1061,
			115,116,123,125,126,127,128,129,130,131,132,133,134,1038,1039,1043,
			1010,113,114,119,120,135,1041,1044,59,104,89,90,48,49,50,94,
			38,39,40,41,42,43,44,1051,1053,45,46,47,1046,1048,66,67,
			1040,22,23,24,25,26,27,28,51,52,53,54,55,56,57,58,
			61,62,63,65,68,69,70,71,72,73,1063,1075,1066,1062,1019,1064,
			1065,77,79,91,92,1003,1005,1016,1015,1008,167,176,95,96,97,98,
			30,31,32,33,34,35,36,37,1018,102
		);
		
		$emj_path = 'images/emoji/i/';
		
		$img = $emo = $map = array();
		
		$emj2i_table = $this->emj2i_table;
		foreach($emj_list as $n) {
			if (isset($emj2i_table[$n])) {
				$str = '[emj:'.$n.']';
				$img[] = $emj_path . intval($emj2i_table[$n], 16) . '.gif';
				$emo[] = $str;
				$map[$str] = $str;
			}
		}
		
		$config['smiley_images'] = array_merge($config['smiley_images'], $img);
		$config['smiley_descriptions'] = array_merge($config['smiley_descriptions'], $emo);
		$config['xoopscode_smileyMap'] = array_merge($config['xoopscode_smileyMap'], $map);
		$config['smiley_columns'] = 16;

	}
}
