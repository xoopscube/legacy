<?php
// $Id: xmlrpctag.php,v 1.1 2007/05/15 02:34:53 minahito Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //

class XoopsXmlRpcDocument
{

    public $_tags = array();

    public function XoopsXmlRpcDocument()
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
        $count = count($this->_tags);
        $payload = '';
        for ($i = 0; $i < $count; $i++) {
            if (!$this->_tags[$i]->isFault()) {
                $payload .= $this->_tags[$i]->render();
            } else {
                return '<?xml version="1.0"?><methodResponse>'.$this->_tags[$i]->render().'</methodResponse>';
            }
        }
        return '<?xml version="1.0"?><methodResponse><params><param>'.$payload.'</param></params></methodResponse>';
    }
}

class XoopsXmlRpcRequest extends XoopsXmlRpcDocument
{

    public $methodName;

    public function XoopsXmlRpcRequest($methodName)
    {
        $this->methodName = trim($methodName);
    }

    public function render()
    {
        $count = count($this->_tags);
        $payload = '';
        for ($i = 0; $i < $count; $i++) {
            $payload .= '<param>'.$this->_tags[$i]->render().'</param>';
        }
        return '<?xml version="1.0"?><methodCall><methodName>'.$this->methodName.'</methodName><params>'.$payload.'</params></methodCall>';
    }
}

class XoopsXmlRpcTag
{

    public $_fault = false;

    public function XoopsXmlRpcTag()
    {
    }

    public function &encode(&$text)
    {
        $text = preg_replace(array("/\&([a-z\d\#]+)\;/i", "/\&/", "/\#\|\|([a-z\d\#]+)\|\|\#/i"), array("#||\\1||#", "&amp;", "&\\1;"), str_replace(array("<", ">"), array("&lt;", "&gt;"), $text));
        return $text;
    }

    public function setFault($fault = true)
    {
        $this->_fault = (intval($fault) > 0) ? true : false;
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

    public function XoopsXmlRpcFault($code, $extra = null)
    {
        $this->setFault(true);
        $this->_code = intval($code);
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

    public function XoopsXmlRpcInt($value)
    {
        $this->_value = intval($value);
    }

    public function render()
    {
        return '<value><int>'.$this->_value.'</int></value>';
    }
}

class XoopsXmlRpcDouble extends XoopsXmlRpcTag
{

    public $_value;

    public function XoopsXmlRpcDouble($value)
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

    public function XoopsXmlRpcBoolean($value)
    {
        $this->_value = (!empty($value) && $value != false) ? 1 : 0;
    }

    public function render()
    {
        return '<value><boolean>'.$this->_value.'</boolean></value>';
    }
}

class XoopsXmlRpcString extends XoopsXmlRpcTag
{

    public $_value;

    public function XoopsXmlRpcString($value)
    {
        $this->_value = strval($value);
    }

    public function render()
    {
        return '<value><string>'.$this->encode($this->_value).'</string></value>';
    }
}

class XoopsXmlRpcDatetime extends XoopsXmlRpcTag
{

    public $_value;

    public function XoopsXmlRpcDatetime($value)
    {
        if (!is_numeric($value)) {
            $this->_value = strtotime($value);
        } else {
            $this->_value = intval($value);
        }
    }

    public function render()
    {
        return '<value><dateTime.iso8601>'.gmstrftime("%Y%m%dT%H:%M:%S", $this->_value).'</dateTime.iso8601></value>';
    }
}

class XoopsXmlRpcBase64 extends XoopsXmlRpcTag
{

    public $_value;

    public function XoopsXmlRpcBase64($value)
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

    public $_tags = array();

    public function XoopsXmlRpcArray()
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

    public $_tags = array();

    public function XoopsXmlRpcStruct()
    {
    }

    public function add($name, &$tagobj)
    {
        $this->_tags[] = array('name' => $name, 'value' => $tagobj);
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
