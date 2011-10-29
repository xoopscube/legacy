<?php

function b_bulletin_topics_show($options) {

	global $xoopsDB;

	$mydirname = $options[0] ;
	require_once XOOPS_ROOT_PATH.'/class/xoopstopic.php';
	require_once dirname(dirname(__FILE__)).'/class/bulletinTopic.php'; // GIJ

	$block = array();
//	$xt = new XoopsTopic($xoopsDB->prefix("{$mydirname}_topics"));
	$bt = new BulletinTopic( $mydirname ); // GIJ
	$jump = XOOPS_URL.'/modules/'.$mydirname.'/index.php?storytopic=';
	$storytopic = isset($_GET['storytopic']) ? intval($_GET['storytopic']) : 0;
//	ob_start();
//	$xt->makeTopicSelBox(1, $storytopic,"storytopic","location=\"".$jump."\"+this.options[this.selectedIndex].value");
//	$block['selectbox'] = ob_get_contents();
//	ob_end_clean();
	$block['selectbox'] = $bt->makeTopicSelBox( true , $storytopic , "storytopic" , "location=\"".$jump."\"+this.options[this.selectedIndex].value" ) ;
	$block['mydirname'] = $mydirname;
	return $block;

}
?>