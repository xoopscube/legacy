<?php
/**
 * Xoops Comments
 * @package    kernel
 * @subpackage core
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
include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
require_once XOOPS_ROOT_PATH.'/class/xoopsobject.php';

$root =& XCube_Root::getSingleton();
$root->mLanguageManager->loadPageTypeMessageCatalog('comment');


class XoopsComments extends XoopsObject
{
    public $ctable;
    public $db;

    public function __construct($ctable, $id=null)
    {
        $this->ctable = $ctable;
        $this->db =& Database::getInstance();
        parent::__construct();
        $this->initVar('comment_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('item_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('order', XOBJ_DTYPE_INT, null, false);
        $this->initVar('mode', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('subject', XOBJ_DTYPE_TXTBOX, null, false, 191);
        $this->initVar('comment', XOBJ_DTYPE_TXTAREA, null, false, null);
        $this->initVar('ip', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('pid', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('date', XOBJ_DTYPE_INT, null, false);
        $this->initVar('nohtml', XOBJ_DTYPE_INT, 1, false);
        $this->initVar('nosmiley', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('noxcode', XOBJ_DTYPE_INT, 0, false);
        $this->initVar('user_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('icon', XOBJ_DTYPE_OTHER, null, false);
        $this->initVar('prefix', XOBJ_DTYPE_OTHER, null, false);
        if (!empty($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $this->load((int)$id);
            }
        }
    }

    public function load($id)
    {
        $sql = 'SELECT * FROM ' . $this->ctable . ' WHERE comment_id=' . $id . '';
        $arr = $this->db->fetchArray($this->db->query($sql));
        $this->assignVars($arr);
    }

    public function store()
    {
        if (!$this->cleanVars()) {
            return false;
        }
        foreach ($this->cleanVars as $k=>$v) {
            $$k = $v;
        }
        $isnew = false;
        if (empty($comment_id)) {
            $isnew = true;
            $comment_id = $this->db->genId($this->ctable . '_comment_id_seq');
            $sql = sprintf("INSERT INTO %s (comment_id, pid, item_id, date, user_id, ip, subject, comment, nohtml, nosmiley, noxcode, icon) VALUES (%u, %u, %u, %u, %u, '%s', '%s', '%s', %u, %u, %u, '%s')", $this->ctable, $comment_id, $pid, $item_id, time(), $user_id, $ip, $subject, $comment, $nohtml, $nosmiley, $noxcode, $icon);
        } else {
            $sql = sprintf("UPDATE %s SET subject = '%s', comment = '%s', nohtml = %u, nosmiley = %u, noxcode = %u, icon = '%s'  WHERE comment_id = %u", $this->ctable, $subject, $comment, $nohtml, $nosmiley, $noxcode, $icon, $comment_id);
        }
        if (!$result = $this->db->query($sql)) {
            //echo $sql;
            return false;
        }
        if (empty($comment_id)) {
            $comment_id = $this->db->getInsertId();
        }
        if (false !== $isnew) {
            $sql = sprintf('UPDATE %s SET posts = posts+1 WHERE uid = %u', $this->db->prefix('users'), $user_id);
            if (!$result = $this->db->query($sql)) {
                echo 'Could not update user posts.';
            }
        }
        return $comment_id;
    }

    public function delete()
    {
        $sql = sprintf('DELETE FROM %s WHERE comment_id = %u', $this->ctable, $this->getVar('comment_id'));
        if (!$result = $this->db->query($sql)) {
            return false;
        }
        $sql = sprintf('UPDATE %s SET posts = posts-1 WHERE uid = %u', $this->db->prefix('users'), $this->getVar('user_id'));
        if (!$result = $this->db->query($sql)) {
            echo 'Could not update user posts.';
        }
        $mytree = new XoopsTree($this->ctable, 'comment_id', 'pid');
        $arr = $mytree->getAllChild($this->getVar('comment_id'), 'comment_id');
        $size = count($arr);
        if ($size > 0) {
            foreach ($arr as $iValue) {
                $sql = sprintf('DELETE FROM %s WHERE comment_bid = %u', $this->ctable, $iValue['comment_id']);
                if (!$result = $this->db->query($sql)) {
                    echo 'Could not delete comment.';
                }
                $sql = sprintf('UPDATE %s SET posts = posts-1 WHERE uid = %u', $this->db->prefix('users'), $iValue['user_id']);
                if (!$result = $this->db->query($sql)) {
                    echo 'Could not update user posts.';
                }
            }
        }
        return ($size + 1);
    }

    public function &getCommentTree()
    {
        $mytree = new XoopsTree($this->ctable, 'comment_id', 'pid');
        $ret = [];
        $tarray = $mytree->getChildTreeArray($this->getVar('comment_id'), 'comment_id');
        foreach ($tarray as $ele) {
            $ret[] = new XoopsComments($this->ctable, $ele);
        }
        return $ret;
    }

    public function getAllComments($criteria= [], $asobject=true, $orderby= 'comment_id ASC', $limit=0, $start=0)
    {
        $ret = [];
        $where_query = '';
        if (is_array($criteria) && count($criteria) > 0) {
            $where_query = ' WHERE';
            foreach ($criteria as $c) {
                $where_query .= " $c AND";
            }
            $where_query = substr($where_query, 0, -4);
        }
        if (!$asobject) {
            $sql = 'SELECT comment_id FROM ' . $this->ctable . "$where_query ORDER BY $orderby";
            $result = $this->db->query($sql, $limit, $start);
            while ($myrow = $this->db->fetchArray($result)) {
                $ret[] = $myrow['comment_id'];
            }
        } else {
            $sql = 'SELECT * FROM ' . $this->ctable . '' . $where_query . " ORDER BY $orderby";
            $result = $this->db->query($sql, $limit, $start);
            while ($myrow = $this->db->fetchArray($result)) {
                $ret[] = new XoopsComments($this->ctable, $myrow);
            }
        }
        //echo $sql;
        return $ret;
    }

    /* Methods below will be moved to maybe another class? */
    public function printNavBar($item_id, $mode= 'flat', $order=1)
    {
        global $xoopsConfig, $xoopsUser;
        echo "<form method='get' action='".xoops_getenv('PHP_SELF')."'><table width='100%' border='0' cellspacing='1' cellpadding='2'><tr><td class='bg1' align='center'><select name='mode'><option value='nocomments'";
        if ('nocomments' == $mode) {
            echo " selected='selected'";
        }
        echo '>' . _NOCOMMENTS . "</option><option value='flat'";
        if ('flat' == $mode) {
            echo " selected='selected'";
        }
        echo '>' . _FLAT . "</option><option value='thread'";
        if ('thread' == $mode || '' == $mode) {
            echo " selected='selected'";
        }
        echo '>' . _THREADED . "</option></select><select name='order'><option value='0'";
        if (1 !== $order) {
            echo " selected='selected'";
        }
        echo '>' . _OLDESTFIRST . "</option><option value='1'";
        if (1 == $order) {
            echo " selected='selected'";
        }
        echo '>' . _NEWESTFIRST . "</option></select><input type='hidden' name='item_id' value='" . (int)$item_id . "' /><input type='submit' value='" . _CM_REFRESH . "' />";
        if (1 == $xoopsConfig['anonpost'] || $xoopsUser) {
            if ('flat' != $mode || 'nocomments' != $mode || 'thread' != $mode) {
                $mode = 'flat';
            }
            echo "&nbsp;<input type='button' onclick='location=\"newcomment.php?item_id=" . (int)$item_id . '&amp;order=' . (int)$order . '&amp;mode=' . $mode . "\"' value='" . _CM_POSTCOMMENT . "' />";
        }
        echo '</td></tr></table></form>';
    }

    public function showThreadHead()
    {
        openThread();
    }

    public function showThreadPost($order, $mode, $adminview=0, $color_num=1)
    {
        global $xoopsConfig, $xoopsUser;
        $edit_image = '';
        $reply_image = '';
        $delete_image = '';
        $post_date = formatTimestamp($this->getVar('date'), 'm');
        if (0 !== $this->getVar('user_id')) {
            $poster = new XoopsUser($this->getVar('user_id'));
            if (!$poster->isActive()) {
                $poster = 0;
            }
        } else {
            $poster = 0;
        }
        if (null !== $this->getVar('icon') && '' !== $this->getVar('icon')) {
            $subject_image = "<a name='".$this->getVar('comment_id') . "' id='" . $this->getVar('comment_id') . "'></a><img src='" . XOOPS_URL . '/images/subject/' . $this->getVar('icon') . "' alt=''>";
        } else {
            $subject_image =  "<a name='".$this->getVar('comment_id') . "' id='" . $this->getVar('comment_id') . "'></a><img class='svg' src='" . XOOPS_URL . "/images/icons/no_posticon.svg' alt=''>";
        }
        if ($adminview) {
            $ip_image = "<img class='svg' src='".XOOPS_URL."/images/icons/ip-network.svg' alt='".$this->getVar('ip') . "'>";
        } else {
            $ip_image = "<img class='svg' src='".XOOPS_URL."/images/icons/ip-network.svg' alt=''>";
        }
        if ($adminview || ($xoopsUser && $this->getVar('user_id') == $xoopsUser->getVar('uid'))) {
            $edit_image = "<a href='editcomment.php?comment_id=".$this->getVar('comment_id') . '&amp;mode=' . $mode . '&amp;order=' . (int)$order . "'><img class='svg' src='" . XOOPS_URL . "/images/icons/edit.svg' alt='" . _EDIT . "'></a>";
        }
        if ($xoopsConfig['anonpost'] || $xoopsUser) {
            $reply_image = "<a href='replycomment.php?comment_id=".$this->getVar('comment_id') . '&amp;mode=' . $mode . '&amp;order=' . (int)$order . "'><img class='svg' src='" . XOOPS_URL . "/images/icons/reply.svg' alt='" . _REPLY . "'></a>";
        }
        if ($adminview) {
            $delete_image = "<a href='deletecomment.php?comment_id=".$this->getVar('comment_id') . '&amp;mode=' . $mode . '&amp;order=' . (int)$order . "'><img class='svg' src='" . XOOPS_URL . "/images/icons/delete.svg' alt='" . _DELETE . "'></a>";
        }

        if ($poster) {
            $text = $this->getVar('comment');
            if ($poster->getVar('attachsig')) {
                $text .= '<p><br>__________<br>' . $poster->user_sig() . '</p>';
            }
            $reg_date = _CM_JOINED;
            $reg_date .= formatTimestamp($poster->getVar('user_regdate'), 's');
            $posts = _CM_POSTS;
            $posts .= $poster->getVar('posts');
            $user_from = _CM_FROM;
            $user_from .= $poster->getVar('user_from');
            $rank = $poster->rank();
            if ('' !== $rank['image']) {
                $rank['image'] = "<img class='svg' src='".XOOPS_UPLOAD_URL . '/' . $rank['image'] . "' alt=''>";
            }
            $avatar_image = "<img src='".XOOPS_UPLOAD_URL . '/' . $poster->getVar('user_avatar') . "' alt=''>";
            if ($poster->isOnline()) {
                $online_image = "<span class='user-online'>"._ONLINE . '</span>';
            } else {
                $online_image = '';
            }
            $profile_image = "<a href='".XOOPS_URL . '/userinfo.php?uid=' . $poster->getVar('uid') . "'><img class='svg' src='" . XOOPS_URL . "/images/icons/user.svg' alt='" . _PROFILE . "'></a>";
            if ($xoopsUser) {
                $pm_image =  "<a href='javascript:openWithSelfMain(\"".XOOPS_URL . '/pmlite.php?send2=1&amp;to_userid=' . $poster->getVar('uid') . "\",\"pmlite\",450,370);'><img class='svg' src='" . XOOPS_URL . "/images/icons/mail.svg' alt='" . sprintf(_SENDPMTO, $poster->getVar('uname', 'E')) . "'></a>";
            } else {
                $pm_image = '';
            }
            if ($poster->getVar('user_viewemail')) {
                $email_image = "<a href='mailto:".$poster->getVar('email', 'E') . "'><img class='svg' src='" . XOOPS_URL . "/images/icons/mail.svg' alt='" . sprintf(_SENDEMAILTO, $poster->getVar('uname', 'E')) . "'></a>";
            } else {
                $email_image = '';
            }
            $posterurl = $poster->getVar('url');
            if ('' !== $posterurl) {
                $www_image = "<a href='$posterurl' rel='external'><img class='svg' src='".XOOPS_URL."/images/icons/web.svg' alt='"._VISITWEBSITE."'></a>";
            } else {
                $www_image = '';
            }
            if ('' !== $poster->getVar('user_icq')) {
                $icq_image = "<a href='https://wwp.icq.com/scripts/search.dll?to=".$poster->getVar('user_icq', 'E') . "'><img class='svg' src='" . XOOPS_URL . "/images/icons/icq.svg' alt='" . _ADD . "'></a>";
            } else {
                $icq_image = '';
            }
            if ('' !== $poster->getVar('user_aim')) {
                $aim_image = "<a href='aim:goim?screenname=".$poster->getVar('user_aim', 'E') . '&amp;message=Hi+' . $poster->getVar('user_aim') . "+Are+you+there?'><img class='svg' src='" . XOOPS_URL . "/images/icons/aim.svg' alt='aim'></a>";
            } else {
                $aim_image = '';
            }
            if ('' !== $poster->getVar('user_yim')) {
                $yim_image = "<a href='https://edit.yahoo.com/config/send_webmesg?.target=".$poster->getVar('user_yim', 'E') . "&amp;.src=pg'><img class='svg' src='" . XOOPS_URL . "/images/icons/yim.svg' alt='yim'></a>";
            } else {
                $yim_image = '';
            }
            if ('' !== $poster->getVar('user_msnm')) {
                $msnm_image = "<a href='".XOOPS_URL . '/userinfo.php?uid=' . $poster->getVar('uid') . "'><img  class='svg' src='" . XOOPS_URL . "/images/icons/msnm.svg' alt='msnm'></a>";
            } else {
                $msnm_image = '';
            }
            showThread($color_num, $subject_image, $this->getVar('subject'), $text, $post_date, $ip_image, $reply_image, $edit_image, $delete_image, $poster->getVar('uname'), $rank['title'], $rank['image'], $avatar_image, $reg_date, $posts, $user_from, $online_image, $profile_image, $pm_image, $email_image, $www_image, $icq_image, $aim_image, $yim_image, $msnm_image);
        } else {
            showThread($color_num, $subject_image, $this->getVar('subject'), $this->getVar('comment'), $post_date, $ip_image, $reply_image, $edit_image, $delete_image, $xoopsConfig['anonymous']);
        }
    }

    public function showThreadFoot()
    {
        closeThread();
    }

    public function showTreeHead($width= '100%')
    {
        echo "<table class='outer' width='$width'><tr><td colspan='3'>". _CM_REPLIES ."</td></tr><tr class='bg3'><td width='60%' class='fg2'>". _CM_TITLE ."</td><td width='20%' class='fg2'>". _CM_POSTER ."</td><td class='fg2'>". _CM_POSTED . '</td></tr>';
    }

    public function showTreeItem($order, $mode, $color_num)
    {
        if (1 == $color_num) {
            $bg = 'even';
        } else {
            $bg = 'odd';
        }
        $prefix = str_replace('.', '&nbsp;&nbsp;&nbsp;&nbsp;', $this->getVar('prefix'));
        $date = formatTimestamp($this->getVar('date'), 'm');
        if ('' !== $this->getVar('icon')) {
            $icon = 'subject/' . $this->getVar('icon', 'E');
        } else {
            $icon = 'icons/no_posticon.svg';
        }
        echo "<tr class='$bg' align='left'><td>".$prefix."<img src='".XOOPS_URL . '/images/'
             . $icon . "'>&nbsp;<a href='" . xoops_getenv('PHP_SELF') . '?item_id='
             . $this->getVar('item_id') . '&amp;comment_id='
             . $this->getVar('comment_id') . '&amp;mode='
             . $mode . '&amp;order='
             . $order . '#'
             . $this->getVar('comment_id') . "'>" . $this->getVar('subject') . "</a></td><td><a href='" . XOOPS_URL . '/userinfo.php?uid='
             . $this->getVar('user_id') . "'>" . XoopsUser::getUnameFromId($this->getVar('user_id')) . '</a></td><td>'
             . $date . '</td></tr>';
    }

    public function showTreeFoot()
    {
        echo '</table><br>';
    }
}
