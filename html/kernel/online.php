<?php
/**
 * A handler for "Who is Online?" information
 * @package    kernel
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */


class XoopsOnlineHandler
{

    /**
     * Database connection
     *
     * @var	object
     * @access	private
     */
    public $db;

    /**
     * Constructor
     * @param	object  &$db    {@link XoopsHandlerFactory}
     */
    public function __construct(&$db)
    {
        $this->db =& $db;
    }
    public function XoopsOnlineHandler(&$db)
    {
        return self::__construct($db);
    }

    /**
     * Write online information to the database
     *
     * @param int    $uid    UID of the active user
     * @param string $uname  Username
     * @param        $time
     * @param string $module Current module
     * @param string $ip     User's IP adress
     *
     * @return    bool    TRUE on success
     */
    public function write($uid, $uname, $time, $module, $ip)
    {
        $uid = (int)$uid;
        $ip = $this->db->quoteString($ip);
        if ($uid > 0) {
            $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('online') . ' WHERE online_uid=' . $uid;
        } else {
            $sql = 'SELECT COUNT(*) FROM ' . $this->db->prefix('online') . ' WHERE online_uid=' . $uid . ' AND online_ip=' . $ip;
        }
        list($count) = $this->db->fetchRow($this->db->queryF($sql));
        if ($count > 0) {
            $sql = 'UPDATE ' . $this->db->prefix('online') . ' SET online_updated=' . $time . ', online_module = ' . $module . ' WHERE online_uid = ' . $uid;
            if (0 == $uid) {
                $sql .= ' AND online_ip=' . $ip;
            }
        } else {
            $sql = sprintf('INSERT INTO %s (online_uid, online_uname, online_updated, online_ip, online_module) VALUES (%u, %s, %u, %s, %u)', $this->db->prefix('online'), $uid, $this->db->quoteString($uname), $time, $ip, $module);
        }
        if (!$this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Delete online information for a user
     *
     * @param	int $uid    UID
     *
     * @return	bool    TRUE on success
     */
    public function destroy($uid)
    {
        $sql = sprintf('DELETE FROM %s WHERE online_uid = %u', $this->db->prefix('online'), $uid);
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    /**
     * Garbage Collection
     *
     * Delete all online information that has not been updated for a certain time
     *
     * @param	int $expire Expiration time in seconds
     */
    public function gc($expire)
    {
        $sql = sprintf('DELETE FROM %s WHERE online_updated < %u', $this->db->prefix('online'), time() - (int)$expire);
        $this->db->queryF($sql);
    }

    /**
     * Get an array of online information
     *
     * @param	object  $criteria   {@link CriteriaElement}
     * @return	array   Array of associative arrays of online information
     */
    public function &getAll($criteria = null)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('online');
        if (is_object($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result =& $this->db->query($sql, $limit, $start);
        if (!$result) {
            $ret = false;
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] =& $myrow;
            unset($myrow);
        }
        return $ret;
    }

    /**
     * Count the number of online users
     *
     * @param object $criteria {@link CriteriaElement}
     * @return bool
     */
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('online');
        if (is_object($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result =& $this->db->query($sql)) {
            $ret = false;
            return $ret;
        }
        list($ret) = $this->db->fetchRow($result);
        return $ret;
    }
}
