<?php
/**
 * Controller for administration of openid table
 * @version $Rev$
 * @link $URL$
 */
class Openid_Admin_Controller
{
    /**
     * @var string
     */
    var $_url;

    /**
     * @var string
     */
    var $_keyField;

    /**
     * Enter description here...
     *
     * @var Openid_Handler
     */
    var $_handler;

    var $_control;

    var $_defaultOp = 'list';

    /**
     * @var array
     */
    var $_allowed = array('list', 'new', 'edit');
    var $_allowedAction = array('save', 'insert', 'delete', 'deleteok');

    var $_template;

    function Openid_Admin_Controller()
    {
        require_once XOOPS_ROOT_PATH . "/modules/openid/class/handler/{$this->_control}.php";
        $className = 'Openid_Handler_' . ucfirst($this->_control);
        $this->_handler = new $className();
        $this->_keyField = $this->_handler->_keyField;
        $this->_url = xoops_getenv('PHP_SELF') . '?controller=' . $this->_control;
    }

    function execute($adminmenu)
    {
        $op = isset($_REQUEST['op']) ? $_REQUEST['op'] : $this->_defaultOp;
        $method = $op . 'Action';
        if (in_array($op, $this->_allowed)) {
            xoops_cp_header();
            foreach ($adminmenu as $a) {
                echo '| <a href="' . XOOPS_URL . '/modules/openid/' . $a['link'] . '">' . $a['title'] . '</a> ';
            }
            echo '<hr>';
            $this->$method();
            xoops_cp_footer();
        } else if (in_array($op, $this->_allowedAction)) {
            $this->$method();
        } else {
            exit(htmlspecialchars($op, ENT_QUOTES));
        }
    }

    function newAction()
    {
        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $record = new Openid_Context();
        //No initial value
        $this->_showForm($record, 'insert');
    }

    function editAction()
    {
        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $request = new Openid_Context();
        if (!$request->accept($this->_keyField, 'string', 'get')) {
            redirect_header($this->_url, 2, 'Bad operation');
        }
        $record = $this->_handler->get($request->get4Sql($this->_keyField));
        $this->_showForm($record, 'save');
    }

    /**
     * Render edit form
     *
     * @abstract
     * @param Openid_Context $record
     * @param string $op
     */
    function _showForm(&$record, $op)
    {
    }

    function insertAction()
    {
        $this->_checkToken();

        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $post = new Openid_Context();
        $this->_getRequest($post, 'insert');

        if ($this->_handler->insert($post)) {
            $message = 'Record insert Success';
        } else {
            $message  = 'Record insert Fail<br />';
            $message .= $this->_handler->getError();
        }
        redirect_header($this->_url, 2, $message);
    }

    function saveAction()
    {
        $this->_checkToken();

        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $request = new Openid_Context();
        if (!$request->accept($this->_keyField)) {
            exit;
        }
        $post =& $this->_handler->get($request->get($this->_keyField));
        $this->_getRequest($post, 'save');

        if ($this->_handler->update($post)) {
            $message = 'Record Save Success';
        } else {
            $message  = 'Record Save Fail<br />';
            $message .= $this->_handler->getError();
        }
        redirect_header($this->_url, 2, $message);
    }

    function _checkToken()
    {
        require_once XOOPS_ROOT_PATH . '/modules/openid/class/utils.php';
        if (!OpenID_Utils::validateToken()) {
            redirect_header($this->_url, 2, 'Token Error');
        }
    }

    /**
     * Get form vars
     *
     * @abstract
     * @param Openid_Context $post
     * @param string $op
     */
    function _getRequest(&$post, $op)
    {
    }

    function deleteAction()
    {
        if (!empty($_GET[$this->_keyField])) {
            $key = htmlspecialchars($_GET[$this->_keyField], ENT_QUOTES);
            $hiddens = array('op' => 'deleteok', $this->_keyField => $key);
            $message = 'Delete this record.<br />' . $this->_keyField . ' = ' . $key;
            xoops_confirm($hiddens, $this->_url, $message);
        } else {
            redirect_header($this->_url, 2, 'Delete key is not specified');
        }
    }

    function deleteokAction()
    {
        $this->_checkToken();

        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $post = new Openid_Context();
        if ($post->accept($this->_keyField)) {
            if ($this->_handler->delete($post->get($this->_keyField))) {
                $message = 'Record Delete Success';
            } else {
                $message  = 'Record Delete Error<br />';
                $message .= $this->_handler->getError();
            }
        } else {
            $message = 'Delete key is not specified';
        }
        redirect_header($this->_url, 2, $message);
    }

    function listAction()
    {
        require_once XOOPS_ROOT_PATH.'/class/template.php';
        $view = new XoopsTpl();
        $view->assign('url', $this->_url);
        $this->_list($view);
        $view->display($this->_template);
    }

    /**
     * @abstract
     * @param XoopsTpl $view
     */
    function _list($view)
    {
    }

    /**
     * @static
     * @return void
     */
    function route()
    {
        global $xoopsConfig;
        include_once XOOPS_ROOT_PATH . '/modules/openid/language/' . $xoopsConfig['language'] . '/modinfo.php';
        // for X2
        if (!defined('XOOPS_CUBE_LEGACY')) {
            include_once XOOPS_ROOT_PATH . '/modules/openid/language/' . $xoopsConfig['language'] . '/admin.php';
        }
     	include XOOPS_ROOT_PATH . '/modules/openid/include/admin_menu.php';
        $controller = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : $openid_default_controller;
        if (@in_array($controller, $openid_allowed_controller)) {
            require_once XOOPS_ROOT_PATH . "/modules/openid/class/admin/{$controller}.php";
            $className = 'Openid_Admin_' . ucfirst($controller);
            $instance = new $className();
            $instance->execute($adminmenu);
        } else {
            exit(htmlspecialchars($controller, ENT_QUOTES));
        }
    }
}
?>