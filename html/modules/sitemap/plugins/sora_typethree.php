<?php

function b_sitemap_sora_typethree(){
	global $xoopsConfig;
	include_once(XOOPS_ROOT_PATH.'/modules/sora_typethree/language/'.$xoopsConfig['language'].'/main.php');
	$ret = [];
	$ret['parent'] =
		[
			0 => [
				'id' => '',
				'title' => _U_SORA3_GO,
				'url' => 'index.php'
            ],
			1 => [
				'id' => '',
				'title' => _U_SORA3_USERLIST,
				'url' => 'index.php?mode=ulist'
            ],
			2 => [
				'id' => '',
				'title' => _U_SORA3_SORALIST,
				'url' => 'index.php?mode=slist'
            ],
			3 => [
				'id' => '',
				'title' => '&nbsp;&raquo;&raquo;'._U_SORA3_VOTEDN,
				'url' => 'ndex.php?mode=slist&amp;qsort=vote&amp;order=desc&amp;page=1'
            ],
        ];
	return $ret;
}
