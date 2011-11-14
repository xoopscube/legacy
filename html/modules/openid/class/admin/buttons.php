<?php
/**
 * Controller for one click login buttons
 * @version $Rev$
 * @link $URL$
 */
if (!class_exists('Openid_Admin_Controller')) {
    exit();
}
class Openid_Admin_Buttons extends Openid_Admin_Controller
{
    function Openid_Admin_Buttons()
    {
        $this->_control = 'buttons';
        parent::Openid_Admin_Controller();
        $this->_allowedAction[] = 'generate'; 
    }

    function listAction()
    {
        global $xoopsConfig;
        echo '<h3>' . _AD_OPENID_LANG_BUTTONS . '</h3>';
        echo '<p>' . _AD_OPENID_LANG_BUTTONS_DESC . '</p>';
        echo '<p>[<a href="' . $this->_url . '&op=new">' . _AD_OPENID_LANG_NEW . '</a>]</p>';

        if ($records =& $this->_handler->getObjects()) {
            echo '<table border=1><tr>';
            echo '<th></th>';
            echo '<th>Title</th>';
            echo '<th>OpenID Identifier</th>';
            echo '<th>Type</th>';
            echo '<th colspan="2"></th></tr>';
            foreach ($records as $r) {
                $type = $r->get('type');
                $image = $r->get4show('image');
                $description = $r->get4Show('description');
                echo '<tr><td>';
                echo '<img src="';
                echo (strpos($image, 'http') !== 0) ? XOOPS_URL . '/modules/openid/images/' : '';
                echo $image . '" alt="' . $description . '">';
                echo '</td><td>';
                echo $description;
                echo '</td><td>';
                echo $r->get4Show('identifier');
                echo '</td><td>';
                echo $type ? 'signon' : 'server';
                echo '</td><td>';
                echo '<a href="' . $this->_url . '&op=edit&id=' . $r->get4Show('id') . '">' . _EDIT . '</a>';
                echo '</td><td>';
                echo '<a href="' . $this->_url . '&op=delete&id=' . $r->get4Show('id') . '">' . _DELETE . '</a>';
                echo '</td></tr>';
            }
            echo '</table>';
        } else {
            echo '<p>' . $this->_handler->getError() . '</p>';
        }
        echo '<br />';

        $filterItems = array();
        @include XOOPS_ROOT_PATH . '/modules/openid/language/' . $xoopsConfig['language'] . '/filterItems.php';
        $options = array();
        foreach ($filterItems as $item) {
            if (isset($item['op_identifier']) || isset($item['user_identifier'])) {
                $options[] = $item['description'];
            }
        }
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsSimpleForm('', 'openid_generator', $this->_url, 'post', true);
        $form->addElement(new XoopsFormHidden('controller', $this->_control));
        $form->addElement(new XoopsFormHidden('op', 'generate'));

        $element = new XoopsFormSelect(_ADD, 'offset');
        $element->addOptionArray($options);
        $form->addElement($element);

        $form->addElement(new XoopsFormButton('', 'submit', 'OK', 'submit'));
        echo $form->render();
    }

    /**
     * Show Edit or New-create Form
     *
     * @param Openid_Context $record
     * @param string $op
     */
    function _showForm(&$record, $op)
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
        $form = new XoopsThemeForm(_AD_OPENID_LANG_BUTTONS, 'openid_buttons', $this->_url, 'post', true);

        $form->addElement(new XoopsFormHidden('controller', $this->_control));
        $form->addElement(new XoopsFormHidden('op', $op));

        $form->addElement(new XoopsFormText(_AD_OPENID_LANG_DESCRIPTION, 'description',
                            64, 255, $record->get4Show('description')));
        $form->addElement(new XoopsFormText(_AD_OPENID_LANG_IMAGE, 'image', 64, 255, $record->get4Show('image')));

        if ($op == 'save') {
            $form->addElement(new XoopsFormHidden('id', $record->get4Show('id')));
            $form->addElement(new XoopsFormHidden('type', $record->get4Show('type')));
        } else {
            $element = new XoopsFormRadio(_AD_OPENID_LANG_TYPE, 'type', $record->get4Show('type'));
            $element->addOptionArray(array(_AD_OPENID_LANG_TYPE_SERVER, _AD_OPENID_LANG_TYPE_SINON));
            $form->addElement($element);
        }
        $form->addElement(new XoopsFormText('OpenID Identifier', 'identifier', 64, 255, $record->get4Show('identifier')));
        if ($record->get('type') !== '0') {
        	$form->addElement(new XoopsFormText(_AD_OPENID_LANG_RANGE, 'range', 10, 5, $record->get4Show('range')));
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
        $post->accept('type', 'int');
        $post->accept('identifier');
        $post->accept('image');
        $post->accept('description');
    }

    function generateAction()
    {
        $this->_checkToken();

        global $xoopsConfig;
        $filterItems = array();
        @include XOOPS_ROOT_PATH . '/modules/openid/language/' . $xoopsConfig['language'] . '/filterItems.php';

        if (isset($_POST['offset']) && $item = $filterItems[intval($_POST['offset'])]) {
            require_once XOOPS_ROOT_PATH . '/modules/openid/class/context.php';
            $record = new Openid_Context();
            if (isset($item['op_identifier'])) {
                $record->set('type', 0);
                $record->set('identifier', $item['op_identifier']);
            } else if (isset($item['user_identifier'])) {
                $record->set('type', 1);
                $record->set('identifier', $item['user_identifier']);
                $record->set('range', $item['range']);
            } else {
                redirect_header($this->_url, 2, 'Bad request');
            }
            $record->set('image', $item['image']);
            $record->set('description', $item['description']);
        } else {
            redirect_header($this->_url, 2, 'Bad request');
        }

        if ($this->_handler->insert($record)) {
            redirect_header($this->_url, 2, 'Record insert Success');
        } else {
            $message = 'Record insert Fail<br />' . $this->_handler->getError();
            redirect_header($this->_url, 2, $message);
        }
    }
}
?>