<?php
/**
 * Class to facilitate navigation in a multipage document/list
 * @package    kernel
 * @subpackage util
 * @author     Kazumi Ono (aka onokazu)
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


class XoopsPageNav
{
    /**#@+
     * @access  private
     */
    public $total;
    public $perpage;
    public $current;
    public $url;
    /**#@-*/

    /**
     * Constructor
     *
     * @param   int     $total_items    Total number of items
     * @param   int     $items_perpage  Number of items per page
     * @param   int     $current_start  First item on the current page
     * @param   string  $start_name     Name for "start" or "offset"
     * @param   string  $extra_arg      Additional arguments to pass in the URL
     **/
    public function __construct($total_items, $items_perpage, $current_start, $start_name= 'start', $extra_arg= '')
    {
        $this->total   = (int)$total_items;
        $this->perpage = (int)$items_perpage;
        $this->current = (int)$current_start;
        if ('' !== $extra_arg && ('&amp;' !== substr($extra_arg, -5) || '&' !== substr($extra_arg, -1))) {
            $extra_arg .= '&amp;';
        }
        $this->url = xoops_getenv('PHP_SELF').'?'.$extra_arg.trim($start_name).'=';
    }
    public function XoopsPageNav($total_items, $items_perpage, $current_start, $start_name= 'start', $extra_arg= '')
    {
        return $this->__construct($total_items, $items_perpage, $current_start, $start_name, $extra_arg);
    }

    /**
     * Create text navigation
     *
     * @param int $offset
     * @return  string
     **/
    public function renderNav($offset = 4)
    {
        $ret = '';
        if ($this->total <= $this->perpage) {
            return $ret;
        }
        $total_pages = ceil($this->total / $this->perpage);
        if ($total_pages > 1) {
            $ret .= '<ul class="pagenavi">';
            $prev = $this->current - $this->perpage;
            if ($prev >= 0) {
                $ret .= '<li><a href="'.$this->url.$prev.'">&laquo;</a></li>';
            }
            $counter = 1;
            $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
            while ($counter <= $total_pages) {
                if ($counter == $current_page) {
                    $ret .= '<li aria-label="page '.$counter.'" aria-current="page"><b>'.$counter.'</b></li>';
                } elseif (($counter > $current_page-$offset && $counter < $current_page + $offset) || 1 == $counter || $counter == $total_pages) {
                    if ($counter == $total_pages && $current_page < $total_pages - $offset) {
                        $ret .= '<li>...</li>';
                    }
                    $ret .= '<li><a href="'.$this->url.(($counter - 1) * $this->perpage).'">'.$counter.'</a></li>';
                    if (1 == $counter && $current_page > 1 + $offset) {
                        $ret .= '<li>...</li>';
                    }
                }
                $counter++;
            }
            $next = $this->current + $this->perpage;
            if ($this->total > $next) {
                $ret .= '<li><a href="'.$this->url.$next.'"><u>&raquo;</u></a></li>';
            }
            $ret .= '</ul>';
        }
        return $ret;
    }

    /**
     * Create a navigational dropdown list
     *
     * @param bool $showbutton Show the "Go" button?
     * @return  string
     **/
    public function renderSelect($showbutton = false)
    {
        if ($this->total < $this->perpage) {
            return;
        }
        $total_pages = ceil($this->total / $this->perpage);
        $ret = '';
        if ($total_pages > 1) {
            $ret = '<form name="pagenavform" action="'.xoops_getenv('PHP_SELF').'">';
            $ret .= '<select name="pagenavselect" onchange="location=this.options[this.options.selectedIndex].value;">';
            $counter = 1;
            $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
            while ($counter <= $total_pages) {
                if ($counter == $current_page) {
                    $ret .= '<option value="'.$this->url.(($counter - 1) * $this->perpage).'" selected="selected">'.$counter.'</option>';
                } else {
                    $ret .= '<option value="'.$this->url.(($counter - 1) * $this->perpage).'">'.$counter.'</option>';
                }
                $counter++;
            }
            $ret .= '</select>';
            if ($showbutton) {
                $ret .= '&nbsp;<input type="submit" value="'._GO.'" />';
            }
            $ret .= '</form>';
        }
        return $ret;
    }

    /**
     * Create navigation with images
     *
     * @param int $offset
     * @return  string
     **/
    public function renderImageNav($offset = 4)
    {
        if ($this->total < $this->perpage) {
            return;
        }
        $total_pages = ceil($this->total / $this->perpage);
        $ret = '';
        if ($total_pages > 1) {
            $ret = '<table><tr>';
            $prev = $this->current - $this->perpage;
            if ($prev >= 0) {
                $ret .= '<td class="pagneutral"><a href="'.$this->url.$prev.'">&lt;</a></td><td><img src="'.XOOPS_URL.'/images/blank.gif" width="6" alt="" /></td>';
            }
            $counter = 1;
            $current_page = (int)floor(($this->current + $this->perpage) / $this->perpage);
            while ($counter <= $total_pages) {
                if ($counter == $current_page) {
                    $ret .= '<td class="pagact"><b>'.$counter.'</b></td>';
                } elseif (($counter > $current_page-$offset && $counter < $current_page + $offset) || 1 == $counter || $counter == $total_pages) {
                    if ($counter == $total_pages && $current_page < $total_pages - $offset) {
                        $ret .= '<td class="paginact">...</td>';
                    }
                    $ret .= '<td class="paginact"><a href="'.$this->url.(($counter - 1) * $this->perpage).'">'.$counter.'</a></td>';
                    if (1 == $counter && $current_page > 1 + $offset) {
                        $ret .= '<td class="paginact">...</td>';
                    }
                }
                $counter++;
            }
            $next = $this->current + $this->perpage;
            if ($this->total > $next) {
                $ret .= '<td><img src="'.XOOPS_URL.'/images/blank.gif" width="6" alt="" /></td><td class="pagneutral"><a href="'.$this->url.$next.'">&gt;</a></td>';
            }
            $ret .= '</tr></table>';
        }
        return $ret;
    }
}
