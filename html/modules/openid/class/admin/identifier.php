<?php
/**
 * Controller for administration of openid table
 * @version $Rev$
 * @link $URL$
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/modules/openid/class/admin/controller.php';
class Openid_Admin_Identifier extends Openid_Admin_Controller
{
	var $_control = 'identifier';

    function listAction()
    {
		$start = isset($_GET['start']) ? intval($_GET['start']) : 0;

        require_once XOOPS_ROOT_PATH.'/class/pagenav.php';
        $pageNav = new XoopsPageNav($this->_handler->getCount(), 30, $start);
        $nav_html = $pageNav->renderNav();

        echo '
<h3>' . _AD_OPENID_LANG_IDENTIFIER . '</h3>
<p><a href="' . $this->_url . '&op=new">' . _AD_OPENID_LANG_NEW . '</a></p>
' . $nav_html . '
<table border=1>
 <tr>
  <th><a href="' . $this->_url . '&sort=claimed_id">Claimed ID</a></th>
  <th><a href="' . $this->_url . '&sort=uid">' . _AD_OPENID_LANG_USER . '</a></th>
  <th></th>
  <th><a href="' . $this->_url . '&sort=local_id">OP-Local ID</a></th>
  <th>Display ID</th><th>' . _AD_OPENID_LANG_GROUPS . '</th><th></th>
 </tr>
';

        switch (@$_GET['sort']) {
            case 'claimed_id':
            case 'uid':
            case 'local_id':
                $sort = $_GET['sort'];
                break;
            default:
                $sort = null;
        }
        if (!$identifiers =& $this->_handler->getObjects(30, $start, $sort)) {
        	echo '</table>';
        	echo '<p>' . $this->_handler->getError() . '</p>';
            return;
        }

        $uids = array();
        foreach ($identifiers as $identifier) {
            $uids[] = $identifier->get('uid');
        }

        require_once XOOPS_ROOT_PATH . '/modules/openid/class/member.php';
        $member = new Openid_Member();
        $users =& $member->getUsers($uids);

        $mode = array(_AD_OPENID_LANG_INACTIVE, _AD_OPENID_LANG_PRIVATE,
            _AD_OPENID_LANG_OPEN2MEMBER, _AD_OPENID_LANG_PUBLIC);

        foreach ($identifiers as $identifier) {
   	        $uid = intval($identifier->get('uid'));
            $uname = (is_object($users[$uid]))? '<a href="' . XOOPS_URL . '/userinfo.php?uid=' . $uid . '">' . $users[$uid]->getVar('uname') . '(' . $uid . ')</a>' : 'Deleted user' . '(' . $uid . ')';
            echo '
<tr>
 <td><a href="' . $this->_url . '&op=edit&id=' . $identifier->get4Show('id') . '">' . $identifier->get4Show('claimed_id') . '</a></td>
 <td>' . $uname . '</td>
 <td>' . $mode[$identifier->get('omode')] . '</td>
 <td>' . $identifier->get4Show('local_id') . '</td>
 <td>' . $identifier->get4Show('displayid') . '</td>
 <td>' . $member->getGroups($uid, ', ') . '</td>
 <td><a href="' . $this->_url . '&op=delete&id=' . $identifier->get4Show('id') . '">' . _DELETE . '</a></td>
</tr>';
        }
        echo '</table>';
		echo $nav_html;
    }

    /**
     * Generate edit form
     *
     * @param Openid_Context $record
     * @param string $op
     */
    function _showForm(&$record, $op)
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsThemeForm('Identifier', 'openid_identifier',
            XOOPS_URL . '/modules/openid/admin/index.php', 'post', true);

        $form->addElement(new XoopsFormHidden('controller', 'identifier'));
        $form->addElement(new XoopsFormHidden('op', $op));

        if ($op == 'save') {
            $form->addElement(new XoopsFormHidden('id', $record->get4Show('id')));
        }
        $form->addElement(new XoopsFormText('Claimed ID', 'claimed', 64, 255, $record->get4Show('claimed_id')));
        $form->addElement(new XoopsFormSelectUser(_AD_OPENID_LANG_USER, 'uid', false, $record->get4Show('uid')));

        $element = new XoopsFormSelect(_AD_OPENID_LANG_MODE, 'omode', $record->get4Show('omode'));
        $element->addOptionArray(array(_AD_OPENID_LANG_INACTIVE, _AD_OPENID_LANG_PRIVATE,
            _AD_OPENID_LANG_OPEN2MEMBER, _AD_OPENID_LANG_PUBLIC));
        $form->addElement($element);

        $form->addElement(new XoopsFormText('OP-Local ID', 'local', 64, 255, $record->get4Show('local_id')));
        $form->addElement(new XoopsFormText('Display ID', 'display', 64, 255, $record->get4Show('displayid')));
        $form->addElement(new XoopsFormLabel(_AD_OPENID_LANG_CREATED, $record->get4Show('created')));
        $form->addElement(new XoopsFormLabel(_AD_OPENID_LANG_MODIFIED, $record->get4Show('modified')));

        $form->addElement(new XoopsFormButton('', 'submit', 'OK', 'submit'));
        echo $form->render();
    }

    /**
     * Get form vars
     *
     * @param Openid_Context $post
     * @param string $op
     */
    function _getRequest(&$post, $op)
    {
        if ($op == 'save') {
    	    $post->accept('id', 'int');
        }
    	$post->accept('claimed_id', 'string', 'post', 'claimed');
        $post->accept('local_id', 'string', 'post', 'local');
        $post->accept('uid', 'int');
        $post->accept('omode', 'int');
        $post->accept('displayid', 'string', 'post', 'display');
    }
}
?>