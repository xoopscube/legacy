<?php
/**
 * private message class object
 * and handler class
 * @package    kernel
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsPrivmessage extends XoopsObject
{

/**
 * constructor
**/
    public function __construct()
    {
        parent::__construct();
        $this->initVar('msg_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('msg_image', XOBJ_DTYPE_OTHER, 'icon1.gif', false, 100);
        $this->initVar('subject', XOBJ_DTYPE_TXTBOX, null, true, 191);
        $this->initVar('from_userid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('to_userid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('msg_time', XOBJ_DTYPE_OTHER, time(), false);
        $this->initVar('msg_text', XOBJ_DTYPE_TXTAREA, null, true);
        $this->initVar('read_msg', XOBJ_DTYPE_INT, 0, false);
    }
    public function XoopsPrivmessage()
    {
        return self::__construct();
    }

    public function &getFromUser()
    {
        $userHandler=xoops_gethandler('user');
        $user=&$userHandler->get($this->getVar('from_userid'));
        return $user;
    }

    public function isRead()
    {
        return 1 == $this->getVar('read_msg') ? true : false;
    }
}

/**
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS private message class objects.
 * @package		kernel
 * @author		Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 xoops.org
 */
class XoopsPrivmessageHandler extends XoopsObjectHandler
{

/**
 * Create a new {@link XoopsPrivmessage} object
 * @param 	bool 	$isNew 	Flag as "new"?
 * @return 	object
 **/
    public function &create($isNew = true)
    {
        $pm =new XoopsPrivmessage();
        if ($isNew) {
            $pm->setNew();
        }
        return $pm;
    }

/**
 * Load a {@link XoopsPrivmessage} object
 * @param 	int 	$id ID of the message
 * @return 	object
 **/
    public function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('priv_msgs').' WHERE msg_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if (1 == $numrows) {
                    $pm =new XoopsPrivmessage();
                    $pm->assignVars($this->db->fetchArray($result));
                    $ret =& $pm;
                }
            }
        }
        return $ret;
    }

    /**
     * Insert a message in the database
     * @param object $pm {@link XoopsPrivmessage} object
     * @param bool   $force
     * @return    bool
     */
    public function insert(&$pm, $force=false)
    {
        $msg_image = null;
        $subject = null;
        $from_userid = null;
        $to_userid = null;
        $msg_text = null;
        $read_msg = null;
        $msg_id = null;
        if ('xoopsprivmessage' != strtolower(get_class($pm))) {
            return false;
        }
        if (!$pm->isDirty()) {
            return true;
        }
        if (!$pm->cleanVars()) {
            return false;
        }
        foreach ($pm->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($pm->isNew()) {
            $msg_id = $this->db->genId('priv_msgs_msg_id_seq');
            $sql = sprintf('INSERT INTO %s (msg_id, msg_image, subject, from_userid, to_userid, msg_time, msg_text, read_msg) VALUES (%u, %s, %s, %u, %u, %u, %s, %u)', $this->db->prefix('priv_msgs'), $msg_id, $this->db->quoteString($msg_image), $this->db->quoteString($subject), $from_userid, $to_userid, time(), $this->db->quoteString($msg_text), 0);
        } else {
            $sql = sprintf('UPDATE %s SET msg_image = %s, subject = %s, from_userid = %u, to_userid = %u, msg_text = %s, read_msg = %u WHERE msg_id = %u', $this->db->prefix('priv_msgs'), $this->db->quoteString($msg_image), $this->db->quoteString($subject), $from_userid, $to_userid, $this->db->quoteString($msg_text), $read_msg, $msg_id);
        }

        $result = $force ? $this->db->queryF($sql) : $this->db->query($sql);

        if (!$result) {
            return false;
        }
        if (empty($msg_id)) {
            $msg_id = $this->db->getInsertId();
        }
        $pm->assignVar('msg_id', $msg_id);

        return true;
    }

/**
 * Delete from the database
 * @param 	object 	$pm 	{@link XoopsPrivmessage} object
 * @return 	bool
 **/
    public function delete(&$pm)
    {
        if ('xoopsprivmessage' != strtolower(get_class($pm))) {
            return false;
        }
        if (!$result = $this->db->query(sprintf('DELETE FROM %s WHERE msg_id = %u', $this->db->prefix('priv_msgs'), $pm->getVar('msg_id')))) {
            return false;
        }
        return true;
    }

/**
 * Load messages from the database
 * @param 	object 	$criteria 	{@link CriteriaElement} object
 * @param 	bool 	$id_as_key 	use ID as key into the array?
 * @return 	array	Array of {@link XoopsPrivmessage} objects
 **/
    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT * FROM '.$this->db->prefix('priv_msgs');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
            $sort = !in_array($criteria->getSort(), ['msg_id', 'msg_time', 'from_userid']) ? 'msg_id' : $criteria->getSort();
            $sql .= ' ORDER BY '.$sort.' '.$criteria->getOrder();
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $pm =new XoopsPrivmessage();
            $pm->assignVars($myrow);
            if (!$id_as_key) {
                $ret[] =& $pm;
            } else {
                $ret[$myrow['msg_id']] =& $pm;
            }
            unset($pm);
        }
        return $ret;
    }

    /**
     * Return the collect of private message objects which appointed user received.
     * @param int    $uid user id
     * @param int    $start
     * @param int    $limit
     * @param string $order
     * @return array of XoopsPrivmessage.
     */
    public function &getObjectsByFromUid($uid, $start=0, $limit=20, $order = 'DESC')
    {
        $criteria=new Criteria('to_userid', $uid);
        $criteria->addSort('msg_time', $order);
        $criteria->setStart($start);
        $criteria->setLimit($limit);
        $ret =& $this->getObjects($criteria);

        return $ret;
    }

    public function getCountByFromUid($uid)
    {
        return $this->getCount(new Criteria('to_userid', $uid));
    }

    public function getCountUnreadByFromUid($uid)
    {
        $criteria = new CriteriaCompo(new Criteria('read_msg', 0));
        $criteria->add(new Criteria('to_userid', $uid));
        return $this->getCount($criteria);
    }

/**
 * Count message
 * @param 	object 	$criteria = null 	{@link CriteriaElement} object
 * @return 	int
 **/
    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('priv_msgs');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result = $this->db->query($sql)) {
            return 0;
        }
        [$count] = $this->db->fetchRow($result);
        return $count;
    }

/**
 * Mark a message as read
 * @param 	object 	$pm 	{@link XoopsPrivmessage} object
 * @return 	bool
 **/
    public function setRead(&$pm)
    {
        if ('xoopsprivmessage' != strtolower(get_class($pm))) {
            return false;
        }
        $sql = sprintf('UPDATE %s SET read_msg = 1 WHERE msg_id = %u', $this->db->prefix('priv_msgs'), $pm->getVar('msg_id'));
        if (!$this->db->queryF($sql)) {
            return false;
        }
        return true;
    }
}
