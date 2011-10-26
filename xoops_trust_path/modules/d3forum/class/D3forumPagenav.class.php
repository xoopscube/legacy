<?php

class D3forumPageNav {

	var $total;
	var $perpage;
	var $current;
	var $url;

    public function __construct ($total_items, $items_perpage, $current_start, $start_name="start", $extra_arg="")
    {
        $this->total = intval($total_items);
        $this->perpage = intval($items_perpage);
        $this->current = intval($current_start);
        if ( $extra_arg != '' && ( substr($extra_arg, -5) != '&amp;' || substr($extra_arg, -1) != '&' ) ) {
            $extra_arg .= '&amp;';
        }
        list($_uri_) = explode('?', $_SERVER['REQUEST_URI'], 2);
        $this->url = $_uri_.'?'.$extra_arg.trim($start_name).'=';
    }


    /**
     * Create text navigation
     *
     * @param   integer $offset
     * @return  string
     **/
    function renderNav($offset = 4)
    {
        $ret = '';
        if ( $this->total <= $this->perpage ) {
            return $ret;
        }
        $total_pages = ceil($this->total / $this->perpage);
        if ( $total_pages > 1 ) {
            $prev = $this->current - $this->perpage;
            if ( $prev >= 0 ) {
                $ret .= '<a href="'.$this->url.$prev.'"><u>&laquo;</u></a> ';
            }
            $i = 1;
            $current_page = intval(floor(($this->current + $this->perpage) / $this->perpage));
            while ( $i <= $total_pages ) {
                if ( $i == $current_page ) {
                    $ret .= '<b>('.$i.')</b> ';
                } elseif ( ($i > $current_page - $offset && $i < $current_page + $offset ) || $i == 1 || $i == $total_pages ) {
                    if ( $i == $total_pages && $current_page < $total_pages - $offset ) {
                        $ret .= '... ';
                    }
                    $ret .= '<a href="'.$this->url.(($i - 1) * $this->perpage).'">'.$i.'</a> ';
                    if ( $i == 1 && $current_page > 1 + $offset ) {
                        $ret .= '... ';
                    }
                }
                $i++;
            }
            $next = $this->current + $this->perpage;
            if ( $this->total > $next ) {
                $ret .= '<a href="'.$this->url.$next.'"><u>&raquo;</u></a> ';
            }
        }
        return $ret;
    }

    /**
     * Create text navigation
     *
     * @param   integer $offset
     * @return  array [] (['txt'] , ['class'] , ['url'])
     **/
    function getNav($offset = 4)
    {
        $nav = array(); $i=0;
        if ( $this->total <= $this->perpage ) {
            return $nav;
        }
        $total_pages = ceil($this->total / $this->perpage);
        if ( $total_pages > 1 ) {
            $prev = $this->current - $this->perpage;
            if ( $prev >= 0 ) {
                $nav[0]['txt']= "prev";
                $nav[0]['class']= "link";
                $nav[0]['url']= $this->url.$prev;
            }
            
            $i = 1; $j=1;
            $current_page = intval(floor(($this->current + $this->perpage) / $this->perpage));
            while ( $i <= $total_pages ) {
                if ( $i == $current_page ) {
                	$nav[$j]['txt']= $i;
                	$nav[$j]['class']= "this";
                	$nav[$j]['url']= "";
                	$j++;
                } elseif ( ($i > $current_page - $offset && $i < $current_page + $offset ) || $i == 1 || $i == $total_pages ) {
                    if ( $i == $total_pages && $current_page < $total_pages - $offset ) {
                	$nav[$j]['txt']= '... ';
                	$nav[$j]['class']= "txt";
                	$nav[$j]['url']= "";
                	$j++;
                    }
                	$nav[$j]['txt']= $i;
                	$nav[$j]['class']= "link";
                	$nav[$j]['url']= $this->url.(($i - 1) * $this->perpage);
                	$j++;
                    if ( $i == 1 && $current_page > 1 + $offset ) {
                	$nav[$j]['txt']= '... ';
                	$nav[$j]['class']= "txt";
                	$nav[$j]['url']= "";
                	$j++;
                    }
                }
                $i++;
            }
            $next = $this->current + $this->perpage;
            if ( $this->total > $next ) {
                $nav[$i]['txt']= "next";
                $nav[$i]['class']= "link";
                $nav[$i]['url']= $this->url.$next;
            }
        }
        return $nav;
      }

} //end class

?>