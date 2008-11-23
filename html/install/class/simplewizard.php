<?php
/**
 *
 * @package Legacy
 * @version $Id: simplewizard.php,v 1.4 2008/09/25 15:12:47 kilica Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <http://xoopscube.sourceforge.net/> 
 * @license http://xoopscube.sourceforge.net/license/GPL_V2.txt GNU GENERAL PUBLIC LICENSE Version 2
 *
 */
class SimpleWizard {
  var $_v;
  var $_op;
  var $_title;
  var $_content;
  var $_next = '';
  var $_back = '';
  var $_reload ='';
  var $_template_path;
  var $_base_template_name;
  var $_custom_seq;

    function setTemplatePath($name) {
        $this->_template_path = $name;
    }

    function setBaseTemplate($name) {
        $this->_base_template_name = $name;
    }

    function assign($name, $value) {
        $this->_v[$name] = $value;
    }
    
    function setContent($value) {
        $this->_content = $value;
    }

    function setOp($value) {
        $this->_op = $value;
    }

    function setTitle($value) {
        $this->_title = $value;
    }

    function setNext($value) {
        $this->_next = $value;
        $this->_custom_seq = true;
    }

    function setBack($value) {
        $this->_back = $value;
        $this->_custom_seq = true;
    }

    function setReload($value) {
        $this->_reload = $value;
        $this->_custom_seq = true;
    }

    function addArray($name, $value) {
        if (!isset($this->_v[$name]) || !is_array($this->_v[$name])) {
            $this->_v[$name] = array();
        }
        $this->_v[$name][] = $value;
    }

    function v($name) {
        if (!empty($this->_v[$name])) {
            return $this->_v[$name];
        } else {
            return false;
        }
    }

    function e() {
        $args = func_get_args();
        if (func_num_args() >0) {
            if (!empty($this->_v[$args[0]])) {
                $value = $this->_v[$args[0]];
                if ((func_num_args() ==2) && is_array($value)) {
                    $value = $value[$args[1]];
                }
            } else {
                $value = '';
            }
            echo $value;
        }
    }
    
    function render($fname='') {
        if ($fname && file_exists($this->_template_path.'/'.$fname)) {
            ob_start();
            include $this->_template_path.'/'.$fname;
            $this->setContent(ob_get_contents());
            ob_end_clean();
        }
        $content = $this->_content;
        if (!empty($this->_title)) {
            $title = $this->_title;
        } else {
            $title = $GLOBALS['wizardSeq']->getTitle($this->_op);
        }
        if (!empty($this->_next)) {
            $b_next = $this->_next;
        } else if (!$this->_custom_seq) {
            $b_next = $GLOBALS['wizardSeq']->getNext($this->_op);
        } else {
            $b_next = '';
        }
        if (!empty($this->_back)) {
            $b_back = $this->_back;
        } else if (!$this->_custom_seq) {
            $b_back = $GLOBALS['wizardSeq']->getBack($this->_op);
        } else {
            $b_back = '';
        }
        if (!empty($this->_reload)) {
            $b_reload = $this->_reload;
        } else if (!$this->_custom_seq) {
            $b_reload = $GLOBALS['wizardSeq']->getReload($this->_op);
        } else {
            $b_reload = '';
        }
        include $this->_base_template_name;
    }
    function error() {
        $content = $this->_content;
        if (!empty($this->_title)) {
            $title = $this->_title;
        } else {
            $title = $GLOBALS['wizardSeq']->getTitle($this->_op);
        }
        if (!empty($this->_next)) {
            $b_next = $this->_next;
        } else {
            $b_next = '';
        }
        if (!empty($this->_back)) {
            $b_back = $this->_back;
        } else {
            $b_back = '';
        }
        if (!empty($this->_reload)) {
            $b_reload = $this->_reload;
        } else {
            $b_reload = '';
        }
        include $this->_base_template_name;
    }
}

class SimpleWizardSequence {
  var $_list;
  
    function add($name, $title='', $next='', $next_btn='', $back='', $back_btn='', $reload='') {
        $this->_list[$name]['title'] = $title;
        $this->_list[$name]['next'] = $next;
        $this->_list[$name]['next_btn'] = $next_btn;
        $this->_list[$name]['back'] = $back;
        $this->_list[$name]['back_btn'] = $back_btn;
        $this->_list[$name]['reload'] = $reload;
    }
    
    function insertAfter($after, $name, $title='', $back='', $back_btn='', $reload='') {
        if (!empty($this->_list[$after])) {
            $this->_list[$name]['title'] = $title;
            $this->_list[$name]['next'] = $this->_list[$after]['next'];
            $this->_list[$name]['next_btn'] = $this->_list[$after]['next_btn'];
            $this->_list[$after]['next'] = $name;
            $this->_list[$after]['next_btn'] = $title;
            $this->_list[$name]['back'] = $back;
            $this->_list[$name]['back_btn'] = $back_btn;
            $this->_list[$name]['reload'] = $reload;
        }
    }

    // Add replaceAfter method from GIJOE's patch.
    function replaceAfter($after, $name, $title='', $next='', $next_btn='', $back='', $back_btn='', $reload='') {
        if (!empty($this->_list[$after])) {
            $this->_list[$name]['title'] = $title;
            $this->_list[$name]['next'] = $next;
            $this->_list[$name]['next_btn'] = $next_btn;
            $this->_list[$after]['next'] = $name;
            $this->_list[$after]['next_btn'] = $title;
            $this->_list[$name]['back'] = $back;
            $this->_list[$name]['back_btn'] = $back_btn;
            $this->_list[$name]['reload'] = $reload;
        }
    }

    function getTitle($name) {
        if (!empty($this->_list[$name]['title'])) {
            return($this->_list[$name]['title']);
        } else {
            return '';
        }
    }

    function getNext($name) {
        if (!empty($this->_list[$name]['next'])||!empty($this->_list[$name]['next_btn'])) {
            return(array($this->_list[$name]['next'],$this->_list[$name]['next_btn']));
        } else {
            return '';
        }
    }

    function getBack($name) {
        if (!empty($this->_list[$name]['back'])||!empty($this->_list[$name]['back_btn'])) {
            return(array($this->_list[$name]['back'],$this->_list[$name]['back_btn']));
        } else {
            return '';
        }
    }

    function getReload($name) {
        if (!empty($this->_list[$name]['reload'])) {
            return($this->_list[$name]['reload']);
        } else {
            return '';
        }
    }
}

