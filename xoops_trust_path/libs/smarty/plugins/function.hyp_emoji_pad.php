<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     hyp_emoji_pad
 * Version:  1.0
 * Date:     
 * Author:   nao-pon
 * Purpose:  
 * Input:    
 * 
 * Examples: {hyp_emoji_pad id=(Target element Id) msg=(Message) showDomId=(Parent Element Id) emojiUrl=(emoji Image URL) outputWithJS=(1 or 0) emojiList=("all" or emoji numbers split ",")}
 * -------------------------------------------------------------
 */
function smarty_function_hyp_emoji_pad($params, &$smarty)
{
	if (! function_exists('XC_CLASS_EXISTS') || ! XC_CLASS_EXISTS('HypCommonFunc')) return 'Class "HypCommonFunc" not exists.';
	
	if (empty($params['id'])) return 'Parameter "id" is not set.';
	
	$id = $params['id'];
	
	$checkmsg = (empty($params['msg']))? '' : $params['msg'];
	$clearDisplayId = (empty($params['showDomId']))? '' : $params['showDomId'];
	$emojiurl = (empty($params['emojiUrl']))? '' : $params['emojiUrl'];
	$writeJS = (empty($params['outputWithJS']))? TRUE : (bool)$params['outputWithJS'];
	$emj_list = (empty($params['emojiList']))? NULL : $params['emojiList'];
	
	if (strtolower($emj_list) === 'all') {
		$emj_list = 'all';
	} else if (!empty($emj_list)) {
		$emj_list = explode(',', $emj_list);
		$emj_list = array_map('trim', $emj_list);
		$emj_list = array_map('intval', $emj_list);
	}
	
	return HypCommonFunc::make_emoji_pad($id, $checkmsg, $clearDisplayId, $emojiurl, $writeJS, $emj_list);
}

?>