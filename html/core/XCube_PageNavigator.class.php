<?php
/**
 * /core/XCube_PageNavigator.class.php
 * @package    XCube
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      This is a utility class which acquires page navigation information
 * --- sort, offset and limit --- semiautomatically.
 *  the base modules may offer placeholders which is able to connect with interfaces of this class.
 */

define( 'XCUBE_PAGENAVI_START', 1 );
define( 'XCUBE_PAGENAVI_PERPAGE', 2 );

define( 'XCUBE_PAGENAVI_SORT', 1 );
define( 'XCUBE_PAGENAVI_PAGE', 4 );

define( 'XCUBE_PAGENAVI_DEFAULT_PERPAGE', 20 );


class XCube_PageNavigator {
	/**
	 * Array for extra information.
	 * @var
	 */
	public $mAttributes = [];

	/**
	 * Offset.
	 * @var int
	 */
	public $mStart = 0;

	/**
	 * The max number of items which this navigator handles.
	 * @var int
	 */
	public $mTotalItems = 0;

	/**
	 * Per page.
	 * @var int
	 */
	public $mPerpage = XCUBE_PAGENAVI_DEFAULT_PERPAGE;

	/**
	 * Flag indicating whether this class receives the perpage value specified
	 * by the request.
	 * @var bool
	 */
	public $mPerpageFreeze = false;

	/**
	 * Array for sort.
	 * @var
	 */
	public $mSort = [];

	/**
	 * The base url for this navigator.
	 * @var string
	 */
	public $mUrl = '';

	/**
	 * A prefix for variable names fetched by this navigator. If two independent
	 * navigators are used, this property is must.
	 */
	public $mPrefix;

	/**
	 * Array of string for re-building the query strings.
	 */
	public $mExtra = [];

	/**
	 * Options indicating what this navigator fetches automatically.
	 */
	public $mFlags = 0;

	/**
	 * @XCube_Delegate
	 */
	public $mFetch;

	/**
	 * The value indicating whether the mTotal property already has been
	 * specified.
	 * @var bool
	 */
	public $_mIsSpecifedTotalItems = false;

	/**
	 * This delegate is used in only case which mTotal isn't set yet.
	 *
	 * void getTotal(int &total, const XCube_Navigator);
	 *
	 * @var XCube_Delegate
	 */
	public $mGetTotalItems;
	/**
	 * @var bool
	 */
	public $_mIsSpecifiedTotal;
	/**
	 * @var int
	 */
	public $mTotal;
	/**
	 * @var int
	 */


	/**
	 * Constructor.
	 *
	 * @param string $url
	 * @param int $flags
	 */
	public function __construct( $url, $flags = XCUBE_PAGENAVI_START ) {
		$this->mUrl   = $url;
		$this->mFlags = $flags;

		$this->mFetch = new XCube_Delegate();
		$this->mFetch->add( [ &$this, 'fetchNaviControl' ] );

		$this->mGetTotalItems = new XCube_Delegate();
	}

	public function XCube_PageNavigator( $url, $flags = XCUBE_PAGENAVI_START ) {
		return $this->__construct( $url, $flags );
	}

	/**
	 * Gets values which this navigator handles, from the request. And, sets
	 * values to this object's properties.
	 */
	public function fetch() {
		$this->mFetch->call( new XCube_Ref( $this ) );
	}

	public function fetchNaviControl( &$navi ) {
		$root =& XCube_Root::getSingleton();

		$startKey   = $navi->getStartKey();
		$perpageKey = $navi->getPerpageKey();

		if ( $navi->mFlags & XCUBE_PAGENAVI_START ) {
			$t_start = $root->mContext->mRequest->getRequest( $navi->getStartKey() );
			if ( null !== $t_start && (int) $t_start >= 0 ) {
				$navi->mStart = (int) $t_start;
			}
		}

		if ( $navi->mFlags & XCUBE_PAGENAVI_PERPAGE && ! $navi->mPerpageFreeze ) {
			$t_perpage = $root->mContext->mRequest->getRequest( $navi->getPerpageKey() );
			if ( null !== $t_perpage && (int) $t_perpage > 0 ) {
				$navi->mPerpage = (int) $t_perpage;
			}
		}
	}

	public function addExtra( $key, $value ) {
		$this->mExtra[ $key ] = $value;
	}

	public function removeExtra( $key ) {
		if ( $this->mExtra[ $key ] ) {
			unset( $this->mExtra[ $key ] );
		}
	}


	protected function _renderExtra( /*** string ***/ $key, /*** mixed ***/ $extra, /*** string[] ***/ &$query ) {
		if ( ! is_array( $extra ) ) {
			$query[] = $key . '=' . urlencode( $extra );
		} else {    //array
			foreach ( $extra as $k => $value ) {
				$this->_renderExtra( $key . '[' . $k . ']', $value, $query );
			}
		}
	}

	public function getRenderBaseUrl( $mask = null ) {
		if ( null === $mask ) {
			$mask = [];
		}
		if ( ! is_array( $mask ) ) {
			$mask = [ $mask ];
		}

		if ( count( $this->mExtra ) > 0 ) {
			$tarr = [];

			foreach ( $this->mExtra as $key => $value ) {
				if ( is_array( $mask ) && ! in_array( $key, $mask, true ) ) {
					$this->_renderExtra( $key, $value, $tarr );
				}
			}

			if ( 0 === count( $tarr ) ) {
				return $this->mUrl;
			}

			if ( false !== strpos( $this->mUrl, '?' ) ) {
				return $this->mUrl . '&amp;' . implode( '&amp;', $tarr );
			}

			return $this->mUrl . '?' . implode( '&amp;', $tarr );
		}

		return $this->mUrl;
	}

	/**
	 * Returns a url string for navigation. The return value has lost the starting value.
	 * The user need to add a start value. For example, It is "$navi->getRenderUrl().'20'".
	 * NOTE : This method name is bad. This must be renamed!
	 * @return string
	 */
	public function getRenderUrl( $mask = null ) {
		if ( null !== $mask && ! is_array( $mask ) ) {
			$mask = [ $mask ];
		}

		$delimiter = '?';
		$url       = $this->getRenderBaseUrl( $mask );

		if ( false !== strpos( $url, '?' ) ) {
			$delimiter = '&amp;';
		}

		return $url . $delimiter . $this->getStartKey() . '=';
	}

	public function renderUrlForSort() {
		if ( count( $this->mExtra ) > 0 ) {
			$tarr = [];

			foreach ( $this->mExtra as $key => $value ) {
				//$tarr[]=$key."=".urlencode($value);
				$this->_renderExtra( $key, $value, $tarr );
			}

			$tarr[] = $this->getPerpageKey() . '=' . $this->mPerpage;

			if ( false !== strpos( $this->mUrl, '?' ) ) {
				return $this->mUrl . '&amp;' . implode( '&amp;', $tarr );
			}

			return $this->mUrl . '?' . implode( '&amp;', $tarr );
		}

		return $this->mUrl;
	}

	public function renderUrlForPage( $page = null ) {
		$tarr = [];

		foreach ( $this->mExtra as $key => $value ) {
			//$tarr[]=$key."=".urlencode($value);
			$this->_renderExtra( $key, $value, $tarr );
		}

		foreach ( $this->mSort as $key => $value ) {
			$tarr[] = $key . '=' . urlencode( $value );
		}

		$tarr[] = $this->getPerpageKey() . '=' . $this->getPerpage();

		if ( null !== $page ) {
			$tarr[] = $this->getStartKey() . '=' . (int) $page;
		}

		if ( false !== strpos( $this->mUrl, '?' ) ) {
			return $this->mUrl . '&amp;' . implode( '&amp;', $tarr );
		}

		return $this->mUrl . '?' . implode( '&amp;', $tarr );
	}

	/**
	 * Return url string for sort. The return value is complete style.
	 *
	 * @param null $mask
	 *
	 * @return string
	 * @deprecated
	 */
	public function renderSortUrl( $mask = null ) {
		return $this->renderUrlForSort();
	}

	public function setStart( $start ) {
		$this->mStart = (int) $start;
	}

	public function getStart() {
		return $this->mStart;
	}

	public function setTotalItems( $total ) {
		$this->mTotal             = (int) $total;
		$this->_mIsSpecifiedTotal = true;
	}

	public function getTotalItems() {
		if ( false === $this->_mIsSpecifedTotalItems ) {
			$this->mGetTotalItems->call( new XCube_Ref( $this->mTotal ) );
			$this->_mIsSpecifedTotalItems = true;
		}

		return $this->mTotal;
	}

	public function getTotalPages() {
		if ( $this->getPerpage() > 0 ) {
			return ceil( $this->getTotalItems() / $this->getPerpage() );
		}

		return 0;
	}

	public function setPerpage( $perpage ) {
		$this->mPerpage = (int) $perpage;
	}

	public function freezePerpage() {
		$this->mPerpageFreeze = true;
	}

	public function getPerpage() {
		return $this->mPerpage;
	}

	public function setPrefix( $prefix ) {
		$this->mPrefix = $prefix;
	}

	public function getPrefix() {
		return $this->mPrefix;
	}

	public function getStartKey() {
		return $this->mPrefix . 'start';
	}

	public function getPerpageKey() {
		return $this->mPrefix . 'perpage';
	}

	public function getCurrentPage() {
		return (int) floor( ( $this->getStart() + $this->getPerpage() ) / $this->getPerpage() );
	}

	public function hasPrivPage() {
		return ( $this->getStart() - $this->getPerpage() ) >= 0;
	}

	public function getPrivStart() {
		$prev = $this->getStart() - $this->getPerpage();

		return ( $prev > 0 ) ? $prev : 0;
	}

	public function hasNextPage() {
		return $this->getTotalItems() > ( $this->getStart() + $this->getPerpage() );
	}

	public function getNextStart() {
		$next = $this->getStart() + $this->getPerpage();

		return ( $this->getTotalItems() > $next ) ? $next : 0;
	}
}
