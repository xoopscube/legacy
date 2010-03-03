<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     legacy_category_select
 * Version:  1.0
 * Date:     May 23, 2008
 * Author:   HIKAWA Kilica
 * Purpose:  format xoopstree object fot select
 * Input:    tree: xoopstree array
 *           selectedValue: selected category id
 *           show: if 'all', show all categories, even if it were not permitted
 * Examples: {legacy_category_select tree=$cattree selectedValue=$cat_id}
 * -------------------------------------------------------------
 */
 
function smarty_function_legacy_category_select($params, &$smarty)
{
	$selectHtml = '';
	foreach(array_keys($params['tree']) as $key){
		$d = $params['tree'][$key]->getDepth();	//depth of tree
		if($params['selectedValue']==$params['tree'][$key]->getShow('cat_id')){
			$selectHtml .= '<option value="'.$params['tree'][$key]->getShow('cat_id').'" selected="selected">';
		}
		else{
			$selectHtml .= '<option value="' .$params['tree'][$key]->getShow('cat_id'). '">';
		}
		for($i=0;$i<$d;$i++){
			$selectHtml .= '-';
		}
		$selectHtml .= $params['tree'][$key]->getShow('title') .'</option>';
	}

	echo $selectHtml;
}
?>
