<?php
function b_sitemap_utype_bbs(){
	global $xoopsConfig;
	$db =& Database::getInstance();
	$myts =& MyTextSanitizer::getInstance();
	include_once(XOOPS_ROOT_PATH.'/modules/utype_bbs/language/'.$xoopsConfig['language'].'/main.php');
	$result = $db->query("
		SELECT cid,top
		FROM ".$db->prefix('u_type_category')."
		ORDER BY cid DESC
		",
		5, 0);

	$ret = [];
	while([$cid, $top] = $db->fetchRow($result)) {
		$ret['parent'][] = [
			'id' => $cid,
			'title' => _U_TYPE_CATEGORY.'&raquo;&raquo;'.$myts->makeTboxData4Show($top),
			'url' => 'index.php?mode=viewcat&amp;cat='.$cid
        ];
	}

	$result = $db->query("
		SELECT nid,a_title,hn
		FROM ".$db->prefix('u_typebbs')." as b
		WHERE sakujyo='f'
		ORDER BY v_day DESC
		",
		5, 0);
	if ($result) {
		while([$nid, $title, $hn] = $db->fetchRow($result)) {
			$ret['parent'][] = [
				'id' => $nid,
				'title' => _U_TYPE_NEWPOST.'&raquo;&raquo;'.$myts->makeTboxData4Show($title),
				'url' => 'index.php?mode=single&amp;article='.$nid
            ];
		}
	}
	return $ret;
}
