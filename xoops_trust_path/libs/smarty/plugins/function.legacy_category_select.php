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

	foreach(array_keys($tree) as $key){
		$pkey = $tree[$key]->getPrimary();
		$d = method_exists($tree, 'getDepth') ? $tree[$key]->getDepth() : 0;	//depth of tree
		if($params['selectedValue']==$tree[$key]->getShow($pkey)){
			$selectHtml .= '<option value="'.$tree[$key]->getShow($pkey).'" selected="selected">';
		}
		else{
			$selectHtml .= '<option value="' .$tree[$key]->getShow($pkey). '">';
		}
		for($i=0;$i<$d;$i++){
			$selectHtml .= '-';
		}
		$selectHtml .= $tree[$key]->getShow('title') .'</option>';
	}

	echo $selectHtml;
}
?>
