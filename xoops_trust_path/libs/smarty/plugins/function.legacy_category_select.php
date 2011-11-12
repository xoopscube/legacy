<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 legacy_category_select
 * Version:  1.0
 * Date:	 May 23, 2008
 * Author:	 HIKAWA Kilica
 * Purpose:  format xoopstree object fot select
 * Input:	 tree: xoopstree array
 *			 selectedValue: selected category id
 *			 show: if 'all', show all categories, even if it were not permitted
 * Examples: {legacy_category_select tree=$cattree selectedValue=$cat_id}
 * -------------------------------------------------------------
 */
 
function smarty_function_legacy_category_select($params, &$smarty)
{
	$selectHtml = '';

	$tree = $params['tree'];
	$selectedValue = $params['selectedValue'];
	$canSelectBranch = isset($params['canSelectBranch']) ? $params['canSelectBranch'] : true;

	foreach(array_keys($tree) as $key){
		$pkey = $tree[$key]->getPrimary();
		$d = method_exists($tree[$key], 'getDepth') ? $tree[$key]->getDepth() : 0;	//depth of tree
		if($selectedValue==$tree[$key]->getShow($pkey)){
			$optionTag = '<option value="'.$tree[$key]->getShow($pkey).'" selected="selected"%s>';
		}
		else{
			$optionTag = '<option value="' .$tree[$key]->getShow($pkey). '"%s>';
		}
		$disabled = null;
		if($canSelectBranch===false){
			if(isset($tree[$key+1]) && $tree[$key+1]->getDepth()>$d){
				$disabled = ' disabled="disabled"';
			}
		}
		$selectHtml .= sprintf($optionTag, $disabled);
		for($i=0;$i<$d;$i++){
			$selectHtml .= '-';
		}
		$selectHtml .= $tree[$key]->getShow('title') .'</option>';
	}

	echo $selectHtml;
}
?>
