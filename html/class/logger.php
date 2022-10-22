<?php
/**
 * Collects information for a page request
 * Singleton: There can be only one instance of this class and it must
 * be accessed through the {@link instance()} method!
 * records information about database queries, blocks, and execution time
 * and can display it as HTML
 * @package    kernel
 * @subpackage core
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


class XoopsLogger
{
    /**#@+
     * @var array
     */
    public $queries = [];
    public $blocks = [];
    public $extra = [];
    public $logstart = [];
    public $logend = [];
    /**#@-*/

    /**
     * constructor
     *
     * @access  private
     */
    public function __construct()
    {
    }

    /**
     * get a reference to the only instance of this class
     *
     * @return  object XoopsLogger  reference to the only instance
     */
    public static function &instance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new XoopsLogger();
        }
        return $instance;
    }

    /**
     * start a timer
     *
     * @param   string  $name   name of the timer
     *
     */
    public function startTime($name = 'XOOPS')
    {
        $this->logstart[$name] = explode(' ', microtime());
    }

    /**
     * stop a timer
     *
     * @param   string  $name   name of the timer
     */
    public function stopTime($name = 'XOOPS')
    {
        $this->logend[$name] = explode(' ', microtime());
    }

    /**
     * log a database query
     *
     * @param   string  $sql    SQL string
     * @param   string  $error  error message (if any)
     * @param   int     $errno  error number (if any)
     */
    public function addQuery($sql, $error=null, $errno=null)
    {
        if (defined('XOOPS_LOGGER_ADDQUERY_DISABLED') && XOOPS_LOGGER_ADDQUERY_DISABLED) {
            return;
        }
        $this->queries[] = ['sql' => $sql, 'error' => $error, 'errno' => $errno];
        if ($error && defined('XOOPS_MYSQL_ERROR_LOG') && XOOPS_MYSQL_ERROR_LOG) {
            error_log('XOOPS_MYSQL_ERROR_LOG: ' . print_r(end($this->queries), true));
        }
    }

    /**
     * log display of a block
     *
     * @param   string  $name       name of the block
     * @param   bool    $cached     was the block cached?
     * @param   int     $cachetime  cachetime of the block
     */
    public function addBlock($name, $cached = false, $cachetime = 0)
    {
        $this->blocks[] = ['name' => $name, 'cached' => $cached, 'cachetime' => $cachetime];
    }

    /**
     * log extra information
     *
     * @param   string  $name       name for the entry
     * @param   int     $msg  text message for the entry
     */
    public function addExtra($name, $msg)
    {
        $this->extra[] = ['name' => $name, 'msg' => $msg];
    }

    /**
     * get the logged queries in a HTML table
     *
     * @return  string  HTML table with queries
     */
    public function dumpQueries()
    {
        $ret = '<table class="outer" width="100%" cellspacing="1"><tr><th>Queries</th></tr>';
        $class = 'even';
        foreach ($this->queries as $q) {
            if (isset($q['error'])) {
                $ret .= '<tr class="'.$class.'"><td><span style="color:#ff0000;">'.htmlentities($q['sql']).'<br><b>Error number:</b> '.$q['errno'].'<br><b>Error message:</b> '.$q['error'].'</span></td></tr>';
            } else {
                $ret .= '<tr class="'.$class.'"><td>'.htmlentities($q['sql']).'</td></tr>';
            }
            $class = ('odd' == $class) ? 'even' : 'odd';
        }
        $ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->queries).'</span> queries</td></tr></table><br>';
        return $ret;
    }

    /**
     * get the logged blocks in a HTML table
     *
     * @return  string  HTML table with blocks
     */
    public function dumpBlocks()
    {
        $ret = '<table class="outer" width="100%" cellspacing="1"><tr><th colspan="2">Blocks</th></tr>';
        $class = 'even';
        foreach ($this->blocks as $b) {
            if ($b['cached']) {
                $ret .= '<tr><td class="'.$class.'"><b>'.htmlspecialchars($b['name']).':</b> Cached (regenerates every ' . (int)$b['cachetime'] . ' seconds)</td></tr>';
            } else {
                $ret .= '<tr><td class="'.$class.'"><b>'.htmlspecialchars($b['name']).':</b> No Cache</td></tr>';
            }
            $class = ('odd' == $class) ? 'even' : 'odd';
        }
        $ret .= '<tr class="foot"><td>Total: <span style="color:#ff0000;">'.count($this->blocks).'</span> blocks</td></tr></table><br>';
        return $ret;
    }

    /**
     * get the current execution time of a timer
     *
     * @param   string  $name   name of the counter
     * @return  float   current execution time of the counter
     */
    public function dumpTime($name = 'XOOPS')
    {
        if (!isset($this->logstart[$name])) {
            return 0;
        }
        if (!isset($this->logend[$name])) {
            $stop_time = explode(' ', microtime());
        } else {
            $stop_time = $this->logend[$name];
        }
        return ((float)$stop_time[1] + (float)$stop_time[0]) - ((float)$this->logstart[$name][1] + (float)$this->logstart[$name][0]);
    }

    /**
     * get extra information in a HTML table
     *
     * @return  string  HTML table with extra information
     */
    public function dumpExtra()
    {
        $ret = '<table class="outer" width="100%" cellspacing="1"><tr><th colspan="2">Extra</th></tr>';
        $class = 'even';
        foreach ($this->extra as $ex) {
            $ret .= '<tr><td class="'.$class.'"><b>'.htmlspecialchars($ex['name']).':</b> '.htmlspecialchars($ex['msg']).'</td></tr>';
            $class = ('odd' == $class) ? 'even' : 'odd';
        }
        $ret .= '</table><br>';
        return $ret;
    }

    /**
     * get all logged information formatted in HTML tables
     *
     * @return  string  HTML output
     */
    public function dumpAll()
    {
        $ret = $this->dumpQueries();
        $ret .= $this->dumpBlocks();
        if (count($this->logstart) > 0) {
            $ret .= '<table class="outer" width="100%" cellspacing="1"><tr><th>Execution Time</th></tr>';
            $class = 'even';
            foreach ($this->logstart as $k => $v) {
                $ret .= '<tr><td class="'.$class.'"><b>'.htmlspecialchars($k).'</b> took <span style="color:#ff0000;">'.$this->dumpTime($k).'</span> seconds to load.</td></tr>';
                $class = ('odd' == $class) ? 'even' : 'odd';
            }
            $ret .= '</table><br>';
        }
        $ret .= $this->dumpExtra();
        return $ret;
    }
}
