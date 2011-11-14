<?php
/**
 * Controller for filter settings
 * @version $Rev$
 * @link $URL$
 */
if (!class_exists('Openid_Admin_Controller')) {
    exit();
}
class Openid_Admin_Filter extends Openid_Admin_Controller
{
    function Openid_Admin_Filter()
    {
        $this->_control = 'filter';
        parent::Openid_Admin_Controller();
        $this->_url .= '&auth=' . intval(@$_REQUEST['auth']);
        $this->_allowedAction[] = 'generate'; 
    }

    function listAction()
    {
        $auth = empty($_GET['auth']) ? 0 : 1;

        echo '<h3>' . constant('_AD_OPENID_LANG_FILTER_' . $auth) . '</h3>';
        echo '<p>' . constant('_AD_OPENID_LANG_FILTERLEVEL_' . $GLOBALS['xoopsModuleConfig']['filter_level']) . '</p>';
        
        echo '<p><a href="' . $this->_url . '&op=new">' . _AD_OPENID_LANG_NEW . '</a></p>';

        if ($filters =& $this->_handler->getByAuth($auth)) {
            $member_handler =& xoops_gethandler('member');
            $groups = $member_handler->getGroupList();
            echo '<table border=1>';
            echo '<tr><th>' . _AD_OPENID_LANG_PATTERN . '</th>';
            if ($auth) {
                echo '<th>' . _AD_OPENID_LANG_GROUPS . '</th><th></th>';
            }
            echo '<th></th></tr>';
            foreach ($filters as $f) {
                if ($groupid = $f->get4show('groupid')) {
                    $groupids = explode('|', $groupid);
                    $value = '';
                    foreach ($groupids as $g) {
                        $value .= $groups[$g] . ',';
                    }
                } else {
                    $value = _AD_OPENID_LANG_FILTER_DEFAULT;
                }
                echo '<tr><td>';
                echo $f->get4Show('pattern');
                echo '</td><td>';
                if ($auth) {
                    echo $value;
                    echo '</td><td>';
                    echo '<a href="' . $this->_url . '&op=edit&id=' . $f->get4Show('id') . '">' . _EDIT . '</a>';
                    echo '</td><td>';
                }
                echo '<a href="' . $this->_url . '&op=delete&id=' . $f->get4Show('id') . '">' . _DELETE . '</a>';
                echo '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<p>' . $this->_handler->getError() . '</p>';
        }

        echo '<br />';
        if ($auth) {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsSimpleForm('', 'openid_generator', $this->_url, 'post', true);

        $form->addElement(new XoopsFormHidden('controller', 'filter'));
        $form->addElement(new XoopsFormHidden('op', 'generate'));
        $form->addElement(new XoopsFormHidden('auth', $auth));
        
        $element = new XoopsFormSelect(_AD_OPENID_LANG_GENERATOR_KEY, 'type');
        
        $filterItems =& $this->getFilterItems();
        $options = array();
        foreach ($filterItems as $item) {
            $options[] = $item['description'];
        }
        $element->addOptionArray($options);
        $form->addElement($element);

        $form->addElement(new XoopsFormButton('', 'submit', 'OK', 'submit'));
        echo $form->render();
        }
    }

    function newAction()
    {
        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $record = new Openid_Context();
        $record->accept('pattern', 'string', 'get');
        $record->accept('auth', 'int', 'get');
        $this->_showForm($record, 'insert');
    }

    /**
     * Enter description here...
     *
     * @param Openid_Context $record
     * @param string $op
     */
    function _showForm(&$record, $op)
    {
        $auth = $record->get('auth');
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $title = constant('_AD_OPENID_LANG_FILTER_' . $auth);
        $form = new XoopsThemeForm($title, 'openid_filter', $this->_url, 'post', true);

        $form->addElement(new XoopsFormHidden('controller', 'filter'));
        $form->addElement(new XoopsFormHidden('op', $op));
        $form->addElement(new XoopsFormHidden('auth', $auth));

        if ($op == 'save') {
            $form->addElement(new XoopsFormHidden('id', $record->get4Show('id')));
            $form->addElement(new XoopsFormLabel(_AD_OPENID_LANG_PATTERN, $record->get4Show('pattern')));
        } else {
            $form->addElement(new XoopsFormText(_AD_OPENID_LANG_PATTERN, 'pattern',
                            100, 255, $record->get4Show('pattern')));
        }
        if ($auth) {
            $member_handler =& xoops_gethandler('member');
            $groups = $member_handler->getGroupList();
            $groups = array(_AD_OPENID_LANG_FILTER_DEFAULT) + $groups;
            if ($groupid = $record->get4show('groupid')) {
                $value = explode('|', $groupid);
            } else {
                $value = array(0);
            }
            $element = new XoopsFormSelect(_AD_OPENID_LANG_GROUP, 'groupid', $value, count($groups), true);  
            $element->addOptionArray($groups);
            $form->addElement($element);
        }
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
        $post->accept('pattern');
        $post->accept('auth', 'int');
        $post->accept('groupid', 'array');
    }

    function deleteAction()
    {
        if (!empty($_GET['id'])) {
            $id = intval($_GET['id']);
            $hiddens = array('op'=>'deleteok', 'id'=>$id);

            $record =& $this->_handler->get($id);
            $message  = 'Delete this record.<br />';
            $message .= $record->get4Show('pattern');

            xoops_confirm($hiddens, $this->_url, $message);
        } else {
            redirect_header($this->_url, 2, 'Delete key is not specified');
        }
    }

    function generateAction()
    {
        $this->_checkToken();

        require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
        $post = new Openid_Context();
        $post->set('groupid', '0');
        $post->set('auth', OPENID_AUTH_ALLOW);

        $filterItems =& $this->getFilterItems();
        $type = isset($_POST['type']) ? intval($_POST['type']) : 0;
        if (array_key_exists($type, $filterItems)) {
            $item = $filterItems[$type];
            $post->set('pattern', $item['pattern']);
        } else {
            redirect_header($this->_url, 2, 'Bad request');
        }

        if ($this->_handler->insert($post)) {
            redirect_header($this->_url, 2, 'Record insert Success');
        } else {
            $message = 'Record insert Fail<br />' . $filter->getError();
            redirect_header($this->_url, 2, $message);
        }
    }

    /**
     * Get Filter Items from language file
     *
     * @return array
     */
    function &getFilterItems()
    {
        global $xoopsConfig;
        $fileName  = XOOPS_ROOT_PATH . '/modules/openid/language/';
        $fileName .= $xoopsConfig['language'] . '/filterItems.php';
        $filterItems = array();
        if (file_exists($fileName)) {
            include $fileName;
        }
        return $filterItems;
    }
}
?>