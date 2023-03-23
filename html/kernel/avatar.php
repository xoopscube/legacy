<?php
/**
 * avatar class object
 * @package    kernel
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

class XoopsAvatar extends XoopsObject
{
    public $_userCount;

    public function __construct()
    {
        parent::__construct();
        $this->initVar('avatar_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('avatar_file', XOBJ_DTYPE_OTHER, null, false, 30);
        $this->initVar('avatar_name', XOBJ_DTYPE_TXTBOX, null, true, 100);
        $this->initVar('avatar_mimetype', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('avatar_created', XOBJ_DTYPE_INT, null, false);
        $this->initVar('avatar_display', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('avatar_weight', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('avatar_type', XOBJ_DTYPE_OTHER, 0, false);
    }

    public function setUserCount($value)
    {
        $this->_userCount = (int)$value;
    }

    public function getUserCount()
    {
        return $this->_userCount;
    }
}


/**
* XOOPS avatar handler class.
* This class is responsible for providing data access mechanisms to the data source
* of XOOPS avatar class objects.
*
*
* @author  Kazumi Ono <onokazu@xoops.org>
*/

class XoopsAvatarHandler extends XoopsObjectHandler
{

    public function &create($isNew = true)
    {
        $avatar =new XoopsAvatar();
        if ($isNew) {
            $avatar->setNew();
        }
        return $avatar;
    }

    public function &get($id)
    {
        $ret = false;
        $id = (int)$id;
        if ($id > 0) {
            $sql = 'SELECT * FROM '.$this->db->prefix('avatar').' WHERE avatar_id='.$id;
            if ($result = $this->db->query($sql)) {
                $numrows = $this->db->getRowsNum($result);
                if (1 == $numrows) {
                    $avatar =new XoopsAvatar();
                    $avatar->assignVars($this->db->fetchArray($result));
                    $ret =& $avatar;
                }
            }
        }
        return $ret;
    }

    public function insert(&$avatar)
    {
        if ('xoopsavatar' != strtolower(get_class($avatar))) {
            return false;
        }
        if (!$avatar->isDirty()) {
            return true;
        }
        if (!$avatar->cleanVars()) {
            return false;
        }
        foreach ($avatar->cleanVars as $k => $v) {
            ${$k} = $v;
        }
        if ($avatar->isNew()) {
            $avatar_id = $this->db->genId('avatar_avatar_id_seq');
            $sql = sprintf('INSERT INTO %s (avatar_id, avatar_file, avatar_name, avatar_created, avatar_mimetype, avatar_display, avatar_weight, avatar_type) VALUES (%u, %s, %s, %u, %s, %u, %u, %s)', $this->db->prefix('avatar'), $avatar_id, $this->db->quoteString($avatar_file), $this->db->quoteString($avatar_name), time(), $this->db->quoteString($avatar_mimetype), $avatar_display, $avatar_weight, $this->db->quoteString($avatar_type));
        } else {
            $sql = sprintf('UPDATE %s SET avatar_file = %s, avatar_name = %s, avatar_created = %u, avatar_mimetype= %s, avatar_display = %u, avatar_weight = %u, avatar_type = %s WHERE avatar_id = %u', $this->db->prefix('avatar'), $this->db->quoteString($avatar_file), $this->db->quoteString($avatar_name), $avatar_created, $this->db->quoteString($avatar_mimetype), $avatar_display, $avatar_weight, $this->db->quoteString($avatar_type), $avatar_id);
        }
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        if (empty($avatar_id)) {
            $avatar_id = $this->db->getInsertId();
        }
        $avatar->assignVar('avatar_id', $avatar_id);
        return true;
    }

    public function delete(&$avatar)
    {
        if ('xoopsavatar' != strtolower(get_class($avatar))) {
            return false;
        }
        $id = $avatar->getVar('avatar_id');
        $sql = sprintf('DELETE FROM %s WHERE avatar_id = %u', $this->db->prefix('avatar'), $id);
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE avatar_id = %u', $this->db->prefix('avatar_user_link'), $id);
        $result = $this->db->query($sql);
        return true;
    }

    public function &getObjects($criteria = null, $id_as_key = false)
    {
        $ret = [];
        $limit = $start = 0;
        $sql = 'SELECT a.*, COUNT(u.user_id) AS count FROM '.$this->db->prefix('avatar').' a LEFT JOIN '.$this->db->prefix('avatar_user_link').' u ON u.avatar_id=a.avatar_id';
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
            $sql .= ' GROUP BY a.avatar_id ORDER BY avatar_weight, avatar_id';
            $limit = $criteria->getLimit();
            $start = $criteria->getStart();
        }
        $result = $this->db->query($sql, $limit, $start);
        if (!$result) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $avatar =new XoopsAvatar();
            $avatar->assignVars($myrow);
            $avatar->setUserCount($myrow['count']);
            if (!$id_as_key) {
                $ret[] =& $avatar;
            } else {
                $ret[$myrow['avatar_id']] =& $avatar;
            }
            unset($avatar);
        }
        return $ret;
    }

    public function getCount($criteria = null)
    {
        $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('avatar');
        if (isset($criteria) && $criteria instanceof \criteriaelement) {
            $sql .= ' '.$criteria->renderWhere();
        }
        if (!$result =& $this->db->query($sql)) {
            return 0;
        }
        [$count] = $this->db->fetchRow($result);
        return $count;
    }

    public function addUser($avatar_id, $user_id)
    {
        $avatar_id = (int)$avatar_id;
        $user_id = (int)$user_id;
        if ($avatar_id < 1 || $user_id < 1) {
            return false;
        }
        $sql = sprintf('DELETE FROM %s WHERE user_id = %u', $this->db->prefix('avatar_user_link'), $user_id);
        $this->db->query($sql);
        $sql = sprintf('INSERT INTO %s (avatar_id, user_id) VALUES (%u, %u)', $this->db->prefix('avatar_user_link'), $avatar_id, $user_id);
        if (!$result =& $this->db->query($sql)) {
            $avatar_id = XOOPS_UPLOAD_URL . "/modules/user/images/no_avatar.gif";
            return $avatar_id ;
        }
        return true;
    }

    public function &getUser(&$avatar)
    {
        $ret = [];
        if ('xoopsavatar' != strtolower(get_class($avatar))) {
            return $ret;
        }
        $sql = 'SELECT user_id FROM '.$this->db->prefix('avatar_user_link').' WHERE avatar_id='.$avatar->getVar('avatar_id');
        if (!$result = $this->db->query($sql)) {
            return $ret;
        }
        while ($myrow = $this->db->fetchArray($result)) {
            $ret[] =& $myrow['user_id'];
        }
        return $ret;
    }

    public function &getList($avatar_type = null, $avatar_display = null)
    {
        $criteria = new CriteriaCompo();
        if (isset($avatar_type)) {
            $avatar_type = ('C' == $avatar_type) ? 'C' : 'S';
            $criteria->add(new Criteria('avatar_type', $avatar_type));
        }
        if (isset($avatar_display)) {
            $criteria->add(new Criteria('avatar_display', (int)$avatar_display));
        }
        $avatars =& $this->getObjects($criteria, true);
        $ret = ['blank.gif' => _NONE];
        foreach (array_keys($avatars) as $i) {
            $ret[$avatars[$i]->getVar('avatar_file')] = $avatars[$i]->getVar('avatar_name');
        }
        return $ret;
    }
}
