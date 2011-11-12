<?php
/**
 * Controller for extension settings
 *
 * @version $Rev$
 * @link $URL$
 */
if (!class_exists('Openid_Admin_Controller')) {
    exit();
}
class Openid_Admin_Extension extends Openid_Admin_Controller
{
    /**
     * @var string
     */
	var $_control = 'extension';

    /**
     * @var array
     */
    var $_allowed = array('list');
    var $_allowedAction = array('delete', 'deleteok');
    
	function listAction()
    {
        echo '<h3>' . _AD_OPENID_LANG_EXTENSION . '</h3>';
    	echo '<p>' . _AD_OPENID_LANG_EXTENSION_DESC . '</p>';

        $module_handler =& xoops_gethandler('module');
    	if ($records =& $this->_handler->getObjects()) {
            echo '<table border=1>';
            echo '<tr><th>' . _AD_OPENID_LANG_MODNAME;
            echo '</th><th>' . _AD_OPENID_LANG_DIRNAME . '</th></tr>';
        	foreach ($records as $r) {
                $module =& $module_handler->getByDirname($r->get('dirname'));
        		echo '<tr><td>';
                if ($module) {
                    echo $module->getVar('name');
                } else {
                    echo '<a href="' . $this->_url . '&op=delete&dirname=' . $r->get4Show('dirname') . '">' . _DELETE . '</a>';
                }
                echo '</td><td>';
                echo $r->get4Show('dirname');
                echo '</td></tr>';
            }
            echo '</table>';

        } else {
            echo '<p>' . $this->_handler->getError() . '</p>';
        }
    }
}
?>