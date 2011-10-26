<?php
/*
 * 2011/09/09 16:45
 * Multi-Menu block function
 * copyright(c) Yoshi Sakai at Bluemoon inc 2011
 * GPL ver3.0 All right reserved.
 */
include_once XOOPS_ROOT_PATH . '/modules/multiMenu/class/getMultiMenu.class.php';
function a_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu01' ); 
	return $block;
}
function b_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu02' ); 
	return $block;
}
function c_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu03' ); 
	return $block;
}
function d_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu04' );
	return $block;
}
function e_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu05' );
	return $block;
}
function f_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu06' );
	return $block;
}
function g_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu07' );
	return $block;
}
function h_multimenu_show($options) {
	$gmm = new getMultiMenu();
	$gmm->assign_css();
	$block = $gmm->getblock( $options, 'multimenu08' );
	return $block;
}
function a_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
function b_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
function c_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
function d_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
function e_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
function f_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
function g_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
function h_multimenu_edit($options) {
	$form = _BM_MULTIMENU_CHARS."&nbsp;<input type='text' name='options[]' value='".$options[0]."' />&nbsp;"._BM_MULTIMENU_LENGTH."";
	return $form;
}
?>