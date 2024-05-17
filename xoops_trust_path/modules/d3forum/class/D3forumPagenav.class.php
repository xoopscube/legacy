<?php
/**
 * D3Forum module for XCL
 * @package    D3Forum
 * @version    XCL 2.4.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class D3forumPageNav {
	public $total;
	public $perpage;
	public $current;
	public $url;

	public function __construct( $total_items, $items_perpage, $current_start, $start_name = 'start', $extra_arg = '' ) {
		$this->total = (int) $total_items;

		$this->perpage = (int) $items_perpage;

		$this->current = (int) $current_start;

		if ( '' !== $extra_arg && ( '&amp;' !== substr( $extra_arg, - 5 ) || '&' !== substr( $extra_arg, - 1 ) ) ) {
			$extra_arg .= '&amp;';
		}

		[ $_uri_ ] = explode( '?', $_SERVER['REQUEST_URI'], 2 );

		$this->url = $_uri_ . '?' . $extra_arg . trim( $start_name ) . '=';
	}

	/**
	 * Create text navigation
	 *
	 * @param int $offset
	 *
	 * @return  string
	 **/
	public function renderNav( $offset = 4 ) {
		$ret = '';

		if ( $this->total <= $this->perpage ) {
			return $ret;
		}

		$total_pages = ceil( $this->total / $this->perpage );

		if ( $total_pages > 1 ) {

			$prev = $this->current - $this->perpage;

            $ret .= '<ul class="pagination pagenavi">';

            if ( $prev >= 0 ) {
				$ret .= '<li class="page-item"><a href="' . $this->url . $prev . '" class="page-link" aria-label="Previous">&laquo;</a></li>';
			}
			$i = 1;

			$current_page = (int) floor( ( $this->current + $this->perpage ) / $this->perpage );

			while ( $i <= $total_pages ) {
				if ( $i === $current_page ) {
					$ret .= '<li class="page-item active" aria-label="page '. $i .'" aria-current="page"><b>' . $i . '</b></li>';
				} elseif ( ( $i > $current_page - $offset && $i < $current_page + $offset ) || 1 === $i || $i === $total_pages ) {
					if ( $i === $total_pages && $current_page < $total_pages - $offset ) {
						$ret .= '<li class="page-item">...</li>';
					}
					$ret .= '<li class="page-item"><a href="' . $this->url . ( ( $i - 1 ) * $this->perpage ) . '" class="page-link">' . $i . '</a></li> ';
					if ( 1 === $i && $current_page > 1 + $offset ) {
						$ret .= '<li class="page-item">...</li>';
					}
				}
				$i ++;
			}

			$next = $this->current + $this->perpage;

			if ( $this->total > $next ) {
				$ret .= '<li class="page-item"><a href="' . $this->url . $next . '" class="page-link" aria-label="Next">&raquo;</a></li>';
			}
            $ret .='</ul>';
		}

		return $ret;
	}

	/**
	 * Create text navigation
	 *
	 * @param int $offset
	 *
	 * @return  array [] (['txt'] , ['class'] , ['url'])
	 **/
	public function getNav( $offset = 4 ) {
		$nav = [];

		$i = 0;

		if ( $this->total <= $this->perpage ) {
			return $nav;
		}

		$total_pages = ceil( $this->total / $this->perpage );

		if ( $total_pages > 1 ) {

			$prev = $this->current - $this->perpage;

			if ( $prev >= 0 ) {
				$nav[0]['txt']   = 'prev';
				$nav[0]['class'] = 'link';
				$nav[0]['url']   = $this->url . $prev;
			}

			$i = 1;

			$j = 1;

			$current_page = (int) floor( ( $this->current + $this->perpage ) / $this->perpage );

			while ( $i <= $total_pages ) {

				if ( $i === $current_page ) {
					$nav[ $j ]['txt']   = $i;
					$nav[ $j ]['class'] = 'this';
					$nav[ $j ]['url']   = '';
					$j ++;
				} elseif ( ( $i > $current_page - $offset && $i < $current_page + $offset ) || 1 === $i || $i === $total_pages ) {
					if ( $i === $total_pages && $current_page < $total_pages - $offset ) {
						$nav[ $j ]['txt']   = '... ';
						$nav[ $j ]['class'] = 'txt';
						$nav[ $j ]['url']   = '';
						$j ++;
					}
					$nav[ $j ]['txt']   = $i;
					$nav[ $j ]['class'] = 'link';
					$nav[ $j ]['url']   = $this->url . ( ( $i - 1 ) * $this->perpage );
					$j ++;
					if ( 1 === $i && $current_page > 1 + $offset ) {
						$nav[ $j ]['txt']   = '... ';
						$nav[ $j ]['class'] = 'txt';
						$nav[ $j ]['url']   = '';
						$j ++;
					}
				}
				$i ++;
			}

			$next = $this->current + $this->perpage;

			if ( $this->total > $next ) {
				$nav[ $i ]['txt']   = 'next';
				$nav[ $i ]['class'] = 'link';
				$nav[ $i ]['url']   = $this->url . $next;
			}
		}

		return $nav;
	}

} //end class
