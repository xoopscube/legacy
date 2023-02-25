<?php
/**
 *
 * @package Legacy
 * @version $Id: function.xoops_pagenavi.php,v 1.3 2008/09/25 15:12:37 kilica Exp $
 * @copyright (c) 2005-2022 The XOOPS Cube Project
 * @license GPL v2.0
 *
 */

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     xoops_pagenavi
 * Version:  1.1
 * Date:     Nov 11, 2020
 * Author:   gigamaster
 * Date:     Nov 13, 2005
 * Author:   minahito
 * Purpose:  the placeholder for xoops pagenavi.
 * Input:    pagenavi =
 *           offset =
 *
 * Examples: {xoops_pagenavi pagenavi=$pagenavi}
 * -------------------------------------------------------------
 */
function smarty_function_xoops_pagenavi($params, &$smarty)
{
    $ret = "<ul class='pagenavi'>";

    if (isset($params['pagenavi']) && is_object($params['pagenavi'])) {
        $navi =& $params['pagenavi'];

        $perPage = $navi->getPerpage();

        $total = $navi->getTotalItems();
        $totalPages = $navi->getTotalPages();

        if ($totalPages == 0) {
            return;
        }

        $url = $navi->renderURLForPage();
        $current = $navi->getStart();

        $offset = isset($params['offset']) ? intval($params['offset']) : 4;

        //
        // check prev '«'
        //
        if ($navi->hasPrivPage()) {
            $ret .= @sprintf("<li class='previous'><a href='%s'>&laquo;</a></li> ", $navi->renderURLForPage($navi->getPrivStart()));
        }

        //
        // counting
        //
        $counter=1;
        $currentPage = $navi->getCurrentPage();
        while ($counter<=$totalPages) {
            if ($counter==$currentPage) {
                // gigamaster removed '(' and ')'
                $ret.=@sprintf("<li aria-label='page' aria-current='page'><strong>%d</strong></li>", $counter);
            } elseif (($counter>$currentPage-$offset && $counter<$currentPage+$offset) || $counter==1 || $counter==$totalPages) {
                if ($counter==$totalPages && $currentPage<$totalPages-$offset) {
                    $ret.="<li>...</li>";
                }
                $ret .= @sprintf("<li><a href='%s'>%d</a></li> ", $navi->renderURLForPage(($counter-1)*$perPage), $counter);
                if ($counter==1 && $currentPage>1 + $offset) {
                    $ret.="<li>...</li>";
                }
            }
            $counter++;
        }

        //
        // check next '»'
        //
        $next=$current + $perPage;
        if ($navi->hasNextPage()) {
            $ret.=@sprintf("<li class='next'><a href='%s'>&raquo;</a></li>", $navi->renderURLForPage($navi->getNextStart()));
        }
        $ret.= "</ul>";
    }

    print $ret;
}
