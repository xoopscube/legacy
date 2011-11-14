<?php
/**
 * Clean up temporary records
 *
 * @version $Rev$
 * @link $URL$
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/modules/openid/class/admin/controller.php';
class Openid_Admin_Assoc extends Openid_Admin_Controller
{
	/**
     * @var string
     */
    var $_control = 'assoc';

    /**
     * @var array
     */
    var $_allowed = array('list', 'garbage');

    function listAction()
    {
        echo '<h3>' . _AD_OPENID_LANG_ASSOC . '</h3>';
    	echo '<p>' . _AD_OPENID_LANG_ASSOC_DESC . '</p>';
    	echo '<table border=1>';
        echo '<tr><th>' . _AD_OPENID_LANG_PATTERN . '</th><th>' . _AD_OPENID_LANG_ISSUED;
        echo '</th><th colspan="2"></th></tr>';

        $start = isset($_GET['start']) ? intval($_GET['start']) : 0;
        if ($records =& $this->_handler->getObjects(30, $start)) {
            foreach ($records as $r) {
                $server_url = $r->get4Show('server_url');
                $expire = ($r->get('issued') + $r->get('life') < time());
                $issued = formatTimestamp($r->get('issued'), 'm/d H:i');
            	echo '<tr><td>';
                echo $server_url;
                echo '</td><td>';
                echo $expire ? '<font color="#808080">' . $issued . '</font>' : $issued;
                echo '</td><td>';
                echo '<a href="' . XOOPS_URL . '/modules/openid/admin/index.php?controller=filter&op=new&auth=1&pattern=' . $server_url . '">' . _AD_OPENID_LANG_ALLOW . '</a>';
                echo '</td><td>';
                echo '<a href="' . XOOPS_URL . '/modules/openid/admin/index.php?controller=filter&op=new&auth=0&pattern=' . $server_url . '">' . _AD_OPENID_LANG_DENY . '</a>';
                echo '</td></tr>';
            }
            echo '</table>';

            require_once XOOPS_ROOT_PATH.'/class/pagenav.php';
            $pageNav = new XoopsPageNav($this->_handler->getCount(), 30, $start, 'start', 'controller=' . $this->_control);
            echo $pageNav->renderNav();
            echo '<p><a href="' . $this->_url . '&op=garbage">' . _AD_OPENID_LANG_CLEANUP . '</a></p>';
        } else {
            echo '</table>';
            echo '<p>' . $this->_handler->getError() . '</p>';
        }
    }

    function garbageAction()
    {
    	require_once XOOPS_ROOT_PATH . '/modules/openid/class/library.php';
        $library = new Openid_Library();

        echo $library->cleanupStore();
    }
}
?>