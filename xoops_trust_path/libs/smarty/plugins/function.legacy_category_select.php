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
 * Examples: {legacy_category_select tree=$cattree selectedValue=$cat_id show="all"}
 * -------------------------------------------------------------
 */
 
function smarty_function_legacy_category_select($params, &$smarty)
{
	$selectHtml = '';
	foreach(array_keys($params['tree']['catObj']) as $key){
		if($params['tree']['permit'][$key]==1||$params['show']=='all'){
			$d = $params['tree']['catObj'][$key]->getDepth();	//depth of tree
			if($params['selectedValue']==$params['tree']['catObj'][$key]->getShow('cat_id')){
				$selectHtml .= '<option value="'.$params['tree']['catObj'][$key]->getShow('cat_id').'" selected="selected">';
			}
			else{
				$selectHtml .= '<option value="' .$params['tree']['catObj'][$key]->getShow('cat_id'). '">';
			}
			for($i=0;$i<$d;$i++){
				if($params['tree']['permit'][$key]==1){
					$selectHtml .= '-';
				}
				else{
					$selectHtml .= 'x';
				}
			}
			$selectHtml .= $params['tree']['catObj'][$key]->getShow('title') .'</option>';
		}
	}

	echo $selectHtml;
}
?>
