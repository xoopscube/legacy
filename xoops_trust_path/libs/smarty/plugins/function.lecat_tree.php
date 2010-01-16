<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     lecat_tree
 * Version:  1.1
 * Date:     Mar 28, 2008 / Jul 11, 2008
 * Author:   HIKAWA Kilica
 * Purpose:  format xoopstree object
 * Input:    tree=xoopstree object
 *           control=bool   :display control(edit,delete,add child) or not
 * Examples: {lecat_tree tree=$cattree control=false dirname=$dirname}
 * -------------------------------------------------------------
 */
 
function smarty_function_lecat_tree($params, &$smarty)
{
	$dirname = $params['dirname'];
	if(! $params['tree'][0]){
		return '<div class="lecat_tree"></div>';
	}
	$d = $params['tree'][0]->getDepth($dirname);	//depth of tree
	$treeHtml = '<div class="lecat_tree"><ul>';
	foreach(array_keys($params['tree']) as $key){
		if($d < $params['tree'][$key]->getDepth($dirname)){
			$treeHtml .= '<ul class="catL'. $params['tree'][$key]->getDepth($dirname) .'">';
			$treeHtml .= '<li><a href="./index.php?action=CatView&amp;cat_id='.$params['tree'][$key]->getShow('cat_id').'">'. $params['tree'][$key]->getShow('title') .'</a>';
		}
		elseif($d == $params['tree'][$key]->getDepth($dirname)){
			$treeHtml .= '<li><a href="./index.php?action=CatView&amp;cat_id='.$params['tree'][$key]->getShow('cat_id').'">'. $params['tree'][$key]->getShow('title') .'</a>';
		}
		elseif($d > $params['tree'][$key]->getDepth($dirname)){
			for($i=0; $i < $d-$params['tree'][$key]->getDepth($dirname);$i++){
				$treeHtml .= '</ul>';
			}
			$treeHtml .= '<li><a href="./index.php?action=CatView&amp;cat_id='.$params['tree'][$key]->getShow('cat_id').'">'. $params['tree'][$key]->getShow('title') .'</a>';
		}
		//create content list html if exist
		if($params['control']==true){
			$treeHtml .= ' &nbsp; <a href="'. LECAT_TRUST_PATH .'/index.php?action=CatEdit&amp;cat_id='.$params['tree'][$key]->getShow('cat_id').'"><img src="'. XOOPS_URL .'/images/icons/edit.gif" alt="'. _EDIT .'" /></a> <a href="'. LECAT_TRUST_PATH .'/index.php?action=CatDelete&amp;cat_id='.$params['tree'][$key]->getShow('cat_id').'"><img src="'. XOOPS_URL .'/images/icons/delete.gif" alt="'. _DELETE .'" /></a> [<a href="'. LECAT_TRUST_PATH .'/index.php?action=CatEdit&amp;p_id='.$params['tree'][$key]->getShow('cat_id').'">+ CHILD</a>]';
		}
		$treeHtml .= '</li>';
		$d = $params['tree'][$key]->getDepth($dirname);
	}
	for($i=0; $i < $params['tree'][$key]->getDepth($dirname)-$params['tree'][0]->getDepth($dirname);$i++){
		$treeHtml .= '</ul>';
	}

	$treeHtml .= '</ul></div>';

	echo $treeHtml;
}


?>
