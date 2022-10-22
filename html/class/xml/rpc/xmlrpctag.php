<?php
/**
 * XML RPC Tag
 * @package    kernel
 * @subpackage xml
 * @version    XCL 2.3.1
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

class XoopsXmlRpcDocument
{

    public $_tags = [];

    public function __construct()
    {
    }

    public function add(&$tagobj)
    {
        $this->_tags[] =& $tagobj;
    }

    public function render()
    {
    }
}

class XoopsXmlRpcResponse extends XoopsXmlRpcDocument
{
    public function render()
    {
        $payload = '';
        foreach ($this->_tags as $iValue) {
            if (!$iValue->isFault()) {
                $payload .= $iValue->render();
            } else {
                return '<?xml version="1.0"?><methodResponse>'. $iValue->render().'</methodResponse>';
            }
        }
        return '<?xml version="1.0"?><methodResponse><params><param>'.$payload.'</param></params></methodResponse>';
    }
}

class XoopsXmlRpcRequest extends XoopsXmlRpcDocument
{

    public $methodName;

    public function __construct($methodName)
    {
        $this->methodName = trim($methodName);
    }

    public function render()
    {
        $payload = '';
        foreach ($this->_tags as $iValue) {
            $payload .= '<param>'. $iValue->render().'</param>';
        }
        return '<?xml version="1.0"?><methodCall><methodName>'.$this->methodName.'</methodName><params>'.$payload.'</params></methodCall>';
    }
}

class XoopsXmlRpcTag
{

    public $_fault = false;

    public function __construct()
    {
    }

    public function &encode(&$text)
    {
        $text = preg_replace(["/\&([a-z\d\#]+)\;/i", "/\&/", "/\#\|\|([a-z\d\#]+)\|\|\#/i"], ["#||\\1||#", '&amp;', "&\\1;"], str_replace(['<', '>'], ['&lt;', '&gt;'], $text));
        return $text;
    }

    public function setFault($fault = true)
    {
        $this->_fault = (int)$fault > 0;
    }

    public function isFault()
    {
        return $this->_fault;
    }

    public function render()
    {
    }
}

class XoopsXmlRpcFault extends XoopsXmlRpcTag
{

    public $_code;
    public $_extra;

    public function __construct($code, $extra = null)
    {
        $this->setFault(true);
        $this->_code = (int)$code;
        $this->_extra = isset($extra) ? trim($extra) : '';
    }

    public function render()
    {
        switch ($this->_code) {
        case 101:
            $string = 'Invalid server URI';
            break;
        case 102:
            $string = 'Parser parse error';
            break;
        case 103:
            $string = 'Module not found';
            break;
        case 104:
            $string = 'User authentication failed';
            break;
        case 105:
            $string = 'Module API not found';
            break;
        case 106:
            $string = 'Method response error';
            break;
        case 107:
            $string = 'Method not supported';
            break;
        case 108:
            $string = 'Invalid parameter';
            break;
        case 109:
            $string = 'Missing parameters';
            break;
        case 110:
            $string = 'Selected blog application does not exist';
            break;
        case 111:
            $string = 'Method permission denied';
            break;
        default:
            $string = 'Method response error';
            break;
        }
        $string .= "\n".$this->_extra;
        return '<fault><value><struct><member><name>faultCode</name><value>'.$this->_code.'</value></member><member><name>faultString</name><value>'.$this->encode($string).'</value></member></struct></value></fault>';
    }
}

class XoopsXmlRpcInt extends XoopsXmlRpcTag
{

    public $_value;

    public function __construct($value)
    {
        $this->_value = (int)$value;
    }

    public function render()
    {
        return '<value><int>'.$this->_value.'</int></value>';
    }
}

class XoopsXmlRpcDouble extends XoopsXmlRpcTag
{

    public $_value;

    public function __construct($value)
    {
        $this->_value = (float)$value;
    }

    public function render()
    {
        return '<value><double>'.$this->_value.'</double></value>';
    }
}

class XoopsXmlRpcBoolean extends XoopsXmlRpcTag
{

    public $_value;

    public function __construct($value)
    {
        $this->_value = (!empty($value) && false !== $value) ? 1 : 0;
    }

    public function render()
    {
        return '<value><boolean>'.$this->_value.'</boolean></value>';
    }
}

class XoopsXmlRpcString extends XoopsXmlRpcTag
{

    public $_value;

    public function __construct($value)
    {
        $this->_value = (string)$value;
    }

    public function render()
    {
        return '<value><string>'.$this->encode($this->_value).'</string></value>';
    }
}

class XoopsXmlRpcDatetime extends XoopsXmlRpcTag
{

    public $_value;

    public function __construct($value)
    {
        if (!is_numeric($value)) {
            $this->_value = strtotime($value);
        } else {
            $this->_value = (int)$value;
        }
    }

    public function render()
    {
        return '<value><dateTime.iso8601>'.gmstrftime('%Y%m%dT%H:%M:%S', $this->_value) . '</dateTime.iso8601></value>';
    }
}

class XoopsXmlRpcBase64 extends XoopsXmlRpcTag
{

    public $_value;

    public function __construct($value)
    {
        $this->_value = base64_encode($value);
    }

    public function render()
    {
        return '<value><base64>'.$this->_value.'</base64></value>';
    }
}

class XoopsXmlRpcArray extends XoopsXmlRpcTag
{

    public $_tags = [];

    public function __construct()
    {
    }

    public function add(&$tagobj)
    {
        $this->_tags[] =& $tagobj;
    }

    public function render()
    {
        $count = count($this->_tags);
        $ret = '<value><array><data>';
        for ($i = 0; $i < $count; $i++) {
            $ret .= $this->_tags[$i]->render();
        }
        $ret .= '</data></array></value>';
        return $ret;
    }
}

class XoopsXmlRpcStruct extends XoopsXmlRpcTag
{

    public $_tags = [];

    public function __construct()
    {
    }

    public function add($name, &$tagobj)
    {
        $this->_tags[] = ['name' => $name, 'value' => $tagobj];
    }

    public function render()
    {
        $count = count($this->_tags);
        $ret = '<value><struct>';
        for ($i = 0; $i < $count; $i++) {
            $ret .= '<member><name>'.$this->encode($this->_tags[$i]['name']).'</name>'.$this->_tags[$i]['value']->render().'</member>';
        }
        $ret .= '</struct></value>';
        return $ret;
    }
}
