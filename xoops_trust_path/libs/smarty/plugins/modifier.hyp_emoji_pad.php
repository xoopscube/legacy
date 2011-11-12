<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     hyp_emoji_pad
 * Version:  1.0
 * Date:     
 * Author:   nao-pon
 * Purpose:  
 * Input:    
 * 
 * Examples: {$targetId|hyp_emoji_pad:(Message):(Parent Element Id):(emoji Image URL):([Output by JavaScript]1 or 0):([Emoji list]"all" or emoji numbers split ",")}
 * -------------------------------------------------------------
 */
function smarty_modifier_hyp_emoji_pad($id = '', $checkmsg = '', $clearDisplayId = '', $emojiurl = '', $writeJS = TRUE, $emj_list = NULL)
{
	if (! function_exists('XC_CLASS_EXISTS') || ! XC_CLASS_EXISTS('HypCommonFunc')) return 'Class "HypCommonFunc" not exists.';
	
	if (empty($id)) return 'Parameter "id" is not set.';
	
	$writeJS = (bool)$writeJS;
	$emj_list = (empty($params['emojiList']))? NULL : $params['emojiList'];
	
	if (is_string($emj_list)) {
		if (strtolower($emj_list) === 'all') {
			$emj_list = 'all';
		} else if (!empty($emj_list)) {
			$emj_list = explode(',', $emj_list);
			$emj_list = array_map('trim', $emj_list);
			$emj_list = array_map('intval', $emj_list);
		}
	}
	
	return HypCommonFunc::make_emoji_pad($id, $checkmsg, $clearDisplayId, $emojiurl, $writeJS, $emj_list);
}

?>