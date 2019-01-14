<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:	 function
 * Name:	 legacy_tree
 * Version:  1.2
 * Date:	 Mar 28, 2008 / Feb 19, 2010
 * Author:	 HIKAWA Kilica
 * Purpose:  format category tree object
 * Input:	 tree=Legacy_AbstractCategoryObject object[]
 *			 control=bool	:display control(edit,delete,add child) or not
 *			 dirname=string
 *			 className=string
 * Examples: {legacy_tree tree=$cattree control=false dirname=$dirname className=legacy_tree}
 * -------------------------------------------------------------
 */
 
function smarty_function_legacy_tree($params, &$smarty)
{
    $tree = isset($params['tree']) ? $params['tree'] : null;
    if (!is_array($tree) || !($tree[0] instanceof Legacy_AbstractCategoryObject)) {
        echo '<p>Invalid parameter `tree` in {legacy_tree}</p>';
        return;
    }

    $control = !empty($params['control']);
    $dirname = isset($params['dirname']) ? $params['dirname'] : '';
    $className = isset($params['className']) ? $params['className'] : 'tree';
    $template = isset($params['template']) ? $params['template'] : 'legacy_inc_tree.html';

    //render template
    $render = new XCube_RenderTarget();
    $render->setTemplateName($template);
    $render->setAttribute('legacy_buffertype', XCUBE_RENDER_TARGET_TYPE_MAIN);
    $render->setAttribute('tree', $tree);
    $render->setAttribute('control', $control);
    $render->setAttribute('dirname', $dirname);
    $render->setAttribute('className', $className);
    XCube_Root::getSingleton()->getRenderSystem('Legacy_RenderSystem')->render($render);

    echo $render->getResult();
}
