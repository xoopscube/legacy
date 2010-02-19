<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     lecat_tree
 * Version:  1.2
 * Date:     Mar 28, 2008 / Feb 19, 2010
 * Author:   HIKAWA Kilica
 * Purpose:  format category tree object
 * Input:    tree=Lecat_CatObject object[]
 *           control=bool   :display control(edit,delete,add child) or not
 *           dirname=string
 *           className=string
 * Examples: {lecat_tree tree=$cattree control=false dirname=$dirname className=legacy_tree}
 * -------------------------------------------------------------
 */
 
function smarty_function_lecat_tree($params, &$smarty)
{
    $li = '<li><a href="./index.php?action=CatView&amp;cat_id=%s">%s</a>';
    $dirname = $params['dirname'];
    $className = $params['className'] ? $params['className'] : 'legacy_tree';

    if(count($params['tree'])==0){
        return '<div class="'.$className.'"></div>';
    }
    $d = current($params['tree'])->getDepth($dirname);  //depth of tree
    $treeHtml = '<div class="'.$className.'"><ul>';
    foreach($params['tree'] as $category){
        if($category->mProhibitedFlag==true){
            continue;
        }
        if($d < $category->getDepth($dirname)){
            $treeHtml .= '<ul class="catL'. $category->getDepth($dirname) .'">';
            $treeHtml .= sprintf($li, $category->getShow('cat_id'), $category->getShow('title'));
        }
        elseif($d == $category->getDepth($dirname)){
            $treeHtml .= sprintf($li, $category->getShow('cat_id'), $category->getShow('title'));
        }
        elseif($d > $category->getDepth($dirname)){
            for($i=0; $i < $d-$category->getDepth($dirname);$i++){
                $treeHtml .= '</ul>';
            }
            $treeHtml .= sprintf($li, $category->getShow('cat_id'), $category->getShow('title'));
        }
        //create content list html if exist
        if($params['control']==true){
            $treeHtml .= ' &nbsp; <a href="'. LECAT_TRUST_PATH .'/index.php?action=CatEdit&amp;cat_id='.$category->getShow('cat_id').'"><img src="'. XOOPS_URL .'/images/icons/edit.gif" alt="'. _EDIT .'" /></a> <a href="'. LECAT_TRUST_PATH .'/index.php?action=CatDelete&amp;cat_id='.$category->getShow('cat_id').'"><img src="'. XOOPS_URL .'/images/icons/delete.gif" alt="'. _DELETE .'" /></a> [<a href="'. LECAT_TRUST_PATH .'/index.php?action=CatEdit&amp;p_id='.$category->getShow('cat_id').'">+ CHILD</a>]';
        }
        $treeHtml .= '</li>';
        $d = $category->getDepth($dirname);
    }
    for($i=0; $i < $category->getDepth($dirname)-$params['tree'][0]->getDepth($dirname);$i++){
        $treeHtml .= '</ul>';
    }

    $treeHtml .= '</ul></div>';

    echo $treeHtml;
}


?>
