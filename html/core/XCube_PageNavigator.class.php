<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_PageNavigator.class.php,v 1.5 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

define('XCUBE_PAGENAVI_START', 1);
define('XCUBE_PAGENAVI_PERPAGE', 2);

define('XCUBE_PAGENAVI_SORT', 1);
define('XCUBE_PAGENAVI_PAGE', 4);

define('XCUBE_PAGENAVI_DEFAULT_PERPAGE', 20);

/**
 * This is a utility class which acquires page navigation informations
 * --- sort, offset and limit --- semiautomatically. And, the base modules may
 * offer place holders which is able to connect with interfaces of this class.
 */
class XCube_PageNavigator
{
    /**
     * Array for extra informations.
     * @var Array
     */
    public $mAttributes = array();
    
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
     * @var Array
     */
    public $mSort = array();
    
    /**
     * The base url for this navigator.
     * @var string
     */
    public $mUrl = "";

    /**
     * A prefix for variable names fetched by this navigator. If two independent
     * navigators are used, this property is must.
     */
    public $mPrefix = null;

    /**
     * Array of string for re-building the query strings.
     */
    public $mExtra = array();
    
    /**
     * Options indicating what this navigator fetches automatically.
     */
    public $mFlags = 0;

    /**
     * @XCube_Delegate
     */
    public $mFetch = null;
    
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
    public $mGetTotalItems = null;
    
    
    /**
     * Constructor.
     * @param string $url
     * @param int $total
     * @param int flag
     */
    public function __construct($url, $flags = XCUBE_PAGENAVI_START)
    {
        $this->mUrl = $url;
        $this->mFlags = $flags;
        
        $this->mFetch =new XCube_Delegate();
        $this->mFetch->add(array(&$this, 'fetchNaviControl'));
        
        $this->mGetTotalItems =new XCube_Delegate();
    }
    public function XCube_PageNavigator($url, $flags = XCUBE_PAGENAVI_START)
    {
        return self::__construct($url, $flags);
    }
    
    /**
     * Gets values which this navigator handles, from the request. And, sets
     * values to this object's properties.
     */
    public function fetch()
    {
        $this->mFetch->call(new XCube_Ref($this));
    }
    
    public function fetchNaviControl(&$navi)
    {
        $root =& XCube_Root::getSingleton();
        
        $startKey = $navi->getStartKey();
        $perpageKey = $navi->getPerpageKey();
        
        if ($navi->mFlags & XCUBE_PAGENAVI_START) {
            $t_start = $root->mContext->mRequest->getRequest($navi->getStartKey());
            if ($t_start != null && intval($t_start) >= 0) {
                $navi->mStart = intval($t_start);
            }
        }

        if ($navi->mFlags & XCUBE_PAGENAVI_PERPAGE && !$navi->mPerpageFreeze) {
            $t_perpage = $root->mContext->mRequest->getRequest($navi->getPerpageKey());
            if ($t_perpage != null && intval($t_perpage) > 0) {
                $navi->mPerpage = intval($t_perpage);
            }
        }
    }

    public function addExtra($key, $value)
    {
        $this->mExtra[$key] = $value;
    }
    
    public function removeExtra($key)
    {
        if ($this->mExtra[$key]) {
            unset($this->mExtra[$key]);
        }
    }
    

    protected function _renderExtra(/*** string ***/ $key, /*** mixed ***/ $extra, /*** string[] ***/ &$query)
    {
        if (! is_array($extra)) {
            $query[] = $key.'='.urlencode($extra);
        } else {    //array
            foreach ($extra as $k=>$value) {
                $this->_renderExtra($key."[".$k."]", $value, $query);
            }
        }
    }

    public function getRenderBaseUrl($mask = null)
    {
        if ($mask == null) {
            $mask = array();
        }
        if (!is_array($mask)) {
            $mask = array($mask);
        }
        
        if (count($this->mExtra) > 0) {
            $tarr=array();
            
            foreach ($this->mExtra as $key=>$value) {
                if (is_array($mask) && !in_array($key, $mask)) {
                    //$tarr[]=$key."=".urlencode($value);
                    $this->_renderExtra($key, $value, $tarr);
                }
            }
            
            if (count($tarr)==0) {
                return $this->mUrl;
            }
            
            if (strpos($this->mUrl, "?")!==false) {
                return $this->mUrl."&amp;".implode("&amp;", $tarr);
            } else {
                return $this->mUrl."?".implode("&amp;", $tarr);
            }
        }
        
        return $this->mUrl;
    }
    
    /**
     * Return url string for navigation. The return value is lose start value.
     * The user need to add start value. For example, It is "$navi->getRenderUrl().'20'".
     * This method name is bad. I must rename this.
     * @return string
     */
    public function getRenderUrl($mask = null)
    {
        if ($mask != null && !is_array($mask)) {
            $mask = array($mask);
        }
        
        $demiliter = "?";
        $url = $this->getRenderBaseUrl($mask);
        
        if (strpos($url, "?")!==false) {
            $demiliter = "&amp;";
        }
        
        return $url . $demiliter . $this->getStartKey() . "=";
    }
    
    public function renderUrlForSort()
    {
        if (count($this->mExtra) > 0) {
            $tarr=array();
            
            foreach ($this->mExtra as $key=>$value) {
                //$tarr[]=$key."=".urlencode($value);
                $this->_renderExtra($key, $value, $tarr);
            }
            
            $tarr[] = $this->getPerpageKey() . "=" . $this->mPerpage;
            
            if (strpos($this->mUrl, "?")!==false) {
                return $this->mUrl."&amp;".implode("&amp;", $tarr);
            } else {
                return $this->mUrl."?".implode("&amp;", $tarr);
            }
        }
        
        return $this->mUrl;
    }
    
    public function renderUrlForPage($page = null)
    {
        $tarr=array();
    
        foreach ($this->mExtra as $key=>$value) {
            //$tarr[]=$key."=".urlencode($value);
            $this->_renderExtra($key, $value, $tarr);
        }
    
        foreach ($this->mSort as $key=>$value) {
            $tarr[]=$key."=".urlencode($value);
        }
    
        $tarr[] = $this->getPerpageKey() . "=" . $this->getPerpage();
    
        if ($page !== null) {
            $tarr[] = $this->getStartKey() . '=' . intval($page);
        }
    
        if (strpos($this->mUrl, "?") !== false) {
            return $this->mUrl."&amp;".implode("&amp;", $tarr);
        }
    
        return $this->mUrl."?".implode("&amp;", $tarr);
    }
    
    /**
     * Return url string for sort. The return value is complete style.
     * @deprecated
     */
    public function renderSortUrl($mask = null)
    {
        return $this->renderUrlForSort();
    }

    public function setStart($start)
    {
        $this->mStart = intval($start);
    }
    
    public function getStart()
    {
        return $this->mStart;
    }
    
    public function setTotalItems($total)
    {
        $this->mTotal = intval($total);
        $this->_mIsSpecifiedTotal = true;
    }
    
    public function getTotalItems()
    {
        if ($this->_mIsSpecifedTotalItems == false) {
            $this->mGetTotalItems->call(new XCube_Ref($this->mTotal));
            $this->_mIsSpecifedTotalItems = true;
        }
        
        return $this->mTotal;
    }
    
    public function getTotalPages()
    {
        if ($this->getPerpage() > 0) {
            return ceil($this->getTotalItems() / $this->getPerpage());
        }
        
        return 0;
    }

    public function setPerpage($perpage)
    {
        $this->mPerpage = intval($perpage);
    }
    
    public function freezePerpage()
    {
        $this->mPerpageFreeze = true;
    }
    
    public function getPerpage()
    {
        return $this->mPerpage;
    }

    public function setPrefix($prefix)
    {
        $this->mPrefix = $prefix;
    }
    
    public function getPrefix()
    {
        return $this->mPrefix;
    }

    public function getStartKey()
    {
        return $this->mPrefix . "start";
    }

    public function getPerpageKey()
    {
        return $this->mPrefix . "perpage";
    }
    
    public function getCurrentPage()
    {
        return intval(floor(($this->getStart() + $this->getPerpage()) / $this->getPerpage()));
    }
    
    public function hasPrivPage()
    {
        return ($this->getStart() - $this->getPerpage()) >= 0;
    }

    public function getPrivStart()
    {
        $prev = $this->getStart() - $this->getPerpage();
        
        return ($prev > 0) ? $prev : 0;
    }

    public function hasNextPage()
    {
        return $this->getTotalItems() > ($this->getStart() + $this->getPerpage());
    }

    public function getNextStart()
    {
        $next = $this->getStart() + $this->getPerpage();
        
        return ($this->getTotalItems() > $next) ? $next : 0;
    }
}
