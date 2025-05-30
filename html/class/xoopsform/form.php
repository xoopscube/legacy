<?php
/**
 * Abstract base class for forms
 * @package    kernel
 * @subpackage form
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

class XoopsForm
{
    /**#@+
     * @access  private
     */
    /**
     * "action" attribute for the html form
     * @var string
     */
    public $_action;

    /**
     * "method" attribute for the form.
     * @var string
     */
    public $_method;

    /**
     * "name" attribute of the form
     * @var string
     */
    public $_name;

    /**
     * title for the form
     * @var string
     */
    public $_title;

    /**
     * array of {@link XoopsFormElement} objects
     * @var  array
     */
    public $_elements = [];

    /**
     * extra information for the <form> tag
     * @var string
     */
    public $_extra;

    /**
     * required elements
     * @var array
     */
    public $_required = [];

    /**#@-*/

    /**
     * constructor
     *
     * @param   string  $title  title of the form
     * @param   string  $name   "name" attribute for the <form> tag
     * @param   string  $action "action" attribute for the <form> tag
     * @param   string  $method "method" attribute for the <form> tag
     * @param   bool    $addtoken whether to add a security token to the form
     */
    public function __construct($title, $name, $action, $method= 'post', $addtoken = false)
    {
        $this->_title = $title;
        $this->_name = $name;
        $this->_action = $action;
        $this->_method = $method;
        if (false != $addtoken) {
            $this->addElement(new XoopsFormHiddenToken());
        }
    }
    public function XoopsForm($title, $name, $action, $method= 'post', $addtoken = false)
    {
        return self::__construct($title, $name, $action, $method, $addtoken);
    }

    /**
     * return the title of the form
     *
     * @return  string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * get the "name" attribute for the <form> tag
     *
     * @return  string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * get the "action" attribute for the <form> tag
     *
     * @return  string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * get the "method" attribute for the <form> tag
     *
     * @return  string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Add an element to the form
     *
     * @param   object  &$formElement    reference to a {@link XoopsFormElement}
     * @param   bool    $required       is this a "required" element?
     */
    public function addElement(&$formElement, $required=false)
    {
        if (is_string($formElement)) {
            $this->_elements[] = $formElement;
        } elseif ($formElement instanceof \xoopsformelement) {
            $this->_elements[] =& $formElement;
            if ($required) {
                if (!$formElement->isContainer()) {
                    $this->_required[] =& $formElement;
                } else {
                    $required_elements =& $formElement->getRequired();
                    $count = count($required_elements);
                    for ($i = 0 ; $i < $count; $i++) {
                        $this->_required[] =& $required_elements[$i];
                    }
                }
            }
        }
    }

    /**
     * get an array of forms elements
     *
     * @param   bool    get elements recursively?
     * @return  array   array of {@link XoopsFormElement}s
     */
    public function &getElements($recurse = false)
    {
        if (!$recurse) {
            return $this->_elements;
        } else {
            $ret = [];
            $count = count($this->_elements);
            for ($i = 0; $i < $count; $i++) {
                if (!is_object($this->_elements[$i])) {
                    $ret[] = $this->_elements[$i];
                }
                if (!$this->_elements[$i]->isContainer()) {
                    $ret[] =& $this->_elements[$i];
                } else {
                    $elements =& $this->_elements[$i]->getElements(true);
                    $count2 = count($elements);
                    for ($j = 0; $j < $count2; $j++) {
                        $ret[] =& $elements[$j];
                    }
                    unset($elements);
                }
            }
            return $ret;
        }
    }

    /**
     * get an array of "name" attributes of form elements
     *
     * @return  array   array of form element names
     */
    public function getElementNames()
    {
        $ret = [];
        $elements =& $this->getElements(true);
        $count = count($elements);
        for ($i = 0; $i < $count; $i++) {
            $ret[] = $elements[$i]->getName();
        }
        return $ret;
    }

    /**
     * get a reference to a {@link XoopsFormElement} object by its "name"
     *
     * @param  string  $name    "name" attribute assigned to a {@link XoopsFormElement}
     * @return object  reference to a {@link XoopsFormElement}, false if not found
     */
    public function &getElementByName($name)
    {
        $elements =& $this->getElements(true);
        $count = count($elements);
        for ($i = 0; $i < $count; $i++) {
            if ($name == $elements[$i]->getName()) {
                return $elements[$i];
            }
        }
        $ret = false;
        return $ret;
    }

    /**
     * Sets the "value" attribute of a form element
     *
     * @param   string $name    the "name" attribute of a form element
     * @param   string $value   the "value" attribute of a form element
     */
    public function setElementValue($name, $value)
    {
        $ele =& $this->getElementByName($name);
        if (is_object($ele) && method_exists($ele, 'setValue')) {
            $ele->setValue($value);
        }
    }

    /**
     * Sets the "value" attribute of form elements in a batch
     *
     * @param   array $values   array of name/value pairs to be assigned to form elements
     */
    public function setElementValues($values)
    {
        if (is_array($values) && !empty($values)) {
            // will not use getElementByName() for performance..
            $elements =& $this->getElements(true);
            $count = count($elements);
            for ($i = 0; $i < $count; $i++) {
                $name = $elements[$i]->getName();
                if ($name && isset($values[$name]) && method_exists($elements[$i], 'setValue')) {
                    $elements[$i]->setValue($values[$name]);
                }
            }
        }
    }

    /**
     * Gets the "value" attribute of a form element
     *
     * @param   string  $name   the "name" attribute of a form element
     * @return  string  the "value" attribute assigned to a form element, null if not set
     */
    public function &getElementValue($name)
    {
        $ele =& $this->getElementByName($name);
        if (is_object($ele) && method_exists($ele, 'getValue')) {
            return $ele->getValue($value);
        }

        $ret = null;
        return $ret;
    }

    /**
     * gets the "value" attribute of all form elements
     *
     * @return  array   array of name/value pairs assigned to form elements
     */
    public function &getElementValues()
    {
        // will not use getElementByName() for performance..
        $elements =& $this->getElements(true);
        $count = count($elements);
        $values = [];
        for ($i = 0; $i < $count; $i++) {
            $name = $elements[$i]->getName();
            if ($name && method_exists($elements[$i], 'getValue')) {
                $values[$name] =& $elements[$i]->getValue();
            }
        }
        return $values;
    }

    /**
     * set the extra attributes for the <form> tag
     *
     * @param   string  $extra  extra attributes for the <form> tag
     */
    public function setExtra($extra)
    {
        $this->_extra = ' ' . $extra;
    }

    /**
     * get the extra attributes for the <form> tag
     *
     * @return  string
     */
    public function &getExtra()
    {
        if (isset($this->_extra)) {
            $ret =& $this->_extra;
        } else {
            $ret = '';
        }
        return $ret;
    }

    /**
     * make an element "required"
     *
     * @param   object  &$formElement    reference to a {@link XoopsFormElement}
     */
    public function setRequired(&$formElement)
    {
        $this->_required[] =& $formElement;
    }

    /**
     * get an array of "required" form elements
     *
     * @return  array   array of {@link XoopsFormElement}s
     */
    public function &getRequired()
    {
        return $this->_required;
    }

    /**
     * insert a break in the form
     *
     * This method is abstract. It must be overwritten in the child classes.
     *
     * @param   string  $extra  extra information for the break
     * @abstract
     */
    public function insertBreak($extra = null)
    {
    }

    /**
     * returns renderered form
     *
     * This method is abstract. It must be overwritten in the child classes.
     *
     * @abstract
     */
    public function render()
    {
    }

    /**
     * displays rendered form
     */
    public function display()
    {
        echo $this->render();
    }

    /**
     * Renders the Javascript function needed for client-side for validation
     *
     * @param bool $withtags Include the < javascript > tags in the returned string
     * @return string|null
     */
    public function renderValidationJS($withtags = true)
    {
        $root =& XCube_Root::getSingleton();
        $renderSystem =& $root->getRenderSystem(XOOPSFORM_DEPENDENCE_RENDER_SYSTEM);

        $renderTarget =& $renderSystem->createRenderTarget();

        $renderTarget->setAttribute('legacy_module', 'legacy');
        $renderTarget->setTemplateName('legacy_xoopsform_opt_validationjs.html');
        $renderTarget->setAttribute('form', $this);
        $renderTarget->setAttribute('withtags', $withtags);

        $required =& $this->getRequired();
        $reqcount = count($required);

        $renderTarget->setAttribute('required', $required);
        $renderTarget->setAttribute('required_count', $reqcount);

        $renderSystem->render($renderTarget);

        return $renderTarget->getResult();


        $js = '';
        if ($withtags) {
            $js .= "\n<!-- Start Form Validation JavaScript //-->\n<script type='text/javascript'>\n<!--//\n";
        }
        $myts =& MyTextSanitizer::sGetInstance();
        $formname = $this->getName();
        $required =& $this->getRequired();
        $reqcount = count($required);
        $js .= "function xoopsFormValidate_{$formname}() {
    myform = window.document.$formname;\n";
        for ($i = 0; $i < $reqcount; $i++) {
            $eltname    = $required[$i]->getName();
            $eltcaption = trim($required[$i]->getCaption());
            $eltmsg = empty($eltcaption) ? sprintf(_FORM_ENTER, $eltname) : sprintf(_FORM_ENTER, $eltcaption);
            $eltmsg = str_replace('"', '\"', stripslashes($eltmsg));
            $js .= "if ( myform.{$eltname}.value == \"\" ) "
                . "{ window.alert(\"{$eltmsg}\"); myform.{$eltname}.focus(); return false; }\n";
        }
        $js .= "return true;\n}\n";
        if ($withtags) {
            $js .= "//--></script>\n<!-- End Form Validation JavaScript //-->\n";
        }
        return $js;
    }
    /**
     * assign to smarty form template instead of displaying directly
     *
     * @param   object  &$tpl    reference to a {@link Smarty} object
     * @see     Smarty
     */
    public function assign(&$tpl)
    {
        $i = 0;
        $elements = [];
        foreach ($this->getElements() as $ele) {
            $n = ('' != $ele->getName()) ? $ele->getName() : $i;
            $elements[$n]['name']     = $ele->getName();
            $elements[$n]['caption']  = $ele->getCaption();
            $elements[$n]['body']     = $ele->render();
            $elements[$n]['hidden']   = $ele->isHidden();
            if ('' != $ele->getDescription()) {
                $elements[$n]['description']  = $ele->getDescription();
            }
            $i++;
        }
        $js = $this->renderValidationJS();
        $tpl->assign($this->getName(), ['title' => $this->getTitle(), 'name' => $this->getName(), 'action' => $this->getAction(), 'method' => $this->getMethod(), 'extra' => 'onsubmit="return xoopsFormValidate_' . $this->getName() . '();"' . $this->getExtra(), 'javascript' => $js, 'elements' => $elements]
        );
    }
}
