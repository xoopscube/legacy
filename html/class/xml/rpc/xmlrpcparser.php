<?php
/**
 * Class RSS Parser
 * This class offers methods to parse RSS Files
 * @package    kernel
 * @subpackage xml
 * @version    XCL 2.4.0
 * @author     Other authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH.'/class/xml/saxparser.php';
require_once XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php';


class XoopsXmlRpcParser extends SaxParser
{

    /**
    * @access private
    * @var    array
    */
    public $_param;

    /**
    * @access private
    * @var    string
    */
    public $_methodName;

    /**
    * @access private
    * @var    array
    */
    public $_tempName;

    /**
    * @access private
    * @var    array
    */
    public $_tempValue;

    /**
    * @access private
    * @var    array
    */
    public $_tempMember;

    /**
    * @access private
    * @var    array
    */
    public $_tempStruct;

    /**
    * @access private
    * @var    array
    */
    public $_tempArray;

    /**
    *
    * @access private
    * @var    array
    */
    public $_workingLevel = [];

    /**
     * Constructor of the class
     *
     * @access
     * @param $input
     * @see
     * @author
     */
    public function __construct(&$input)
    {
        parent::__construct($input);
        $this->addTagHandler(new RpcMethodNameHandler());
        $this->addTagHandler(new RpcIntHandler());
        $this->addTagHandler(new RpcDoubleHandler());
        $this->addTagHandler(new RpcBooleanHandler());
        $this->addTagHandler(new RpcStringHandler());
        $this->addTagHandler(new RpcDateTimeHandler());
        $this->addTagHandler(new RpcBase64Handler());
        $this->addTagHandler(new RpcNameHandler());
        $this->addTagHandler(new RpcValueHandler());
        $this->addTagHandler(new RpcMemberHandler());
        $this->addTagHandler(new RpcStructHandler());
        $this->addTagHandler(new RpcArrayHandler());
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function setTempName($name)
    {
        $this->_tempName[$this->getWorkingLevel()] = $name;
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return mixed
     * @author
     * @see
     */
    public function getTempName()
    {
        return $this->_tempName[$this->getWorkingLevel()];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function setTempValue($value)
    {
        if (is_array($value)) {
            $this->_tempValue = (array)$this->_tempValue;
            foreach ($value as $k => $v) {
                $this->_tempValue[$k] = $v;
            }
        } elseif (is_string($value)) {
            if (isset($this->_tempValue)) {
                if (is_string($this->_tempValue)) {
                    $this->_tempValue .= $value;
                }
            } else {
                $this->_tempValue = $value;
            }
        } else {
            $this->_tempValue = $value;
        }
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return array
     * @author
     * @see
     */
    public function getTempValue()
    {
        return $this->_tempValue;
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return void
     * @author
     * @see
     */
    public function resetTempValue()
    {
        unset($this->_tempValue);
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $name
     * @param $value
     * @return void
     * @author
     * @see
     */
    public function setTempMember($name, $value)
    {
        $this->_tempMember[$this->getWorkingLevel()][$name] = $value;
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return mixed
     * @author
     * @see
     */
    public function getTempMember()
    {
        return $this->_tempMember[$this->getWorkingLevel()];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return void
     * @author
     * @see
     */
    public function resetTempMember()
    {
        $this->_tempMember[$this->getCurrentLevel()] = [];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return void
     * @author
     * @see
     */
    public function setWorkingLevel()
    {
        array_push($this->_workingLevel, $this->getCurrentLevel());
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return mixed
     * @author
     * @see
     */
    public function getWorkingLevel()
    {
        return $this->_workingLevel[count($this->_workingLevel) - 1];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return void
     * @author
     * @see
     */
    public function releaseWorkingLevel()
    {
        array_pop($this->_workingLevel);
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function setTempStruct($member)
    {
        $key = key($member);
        $this->_tempStruct[$this->getWorkingLevel()][$key] = $member[$key];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return mixed
     * @author
     * @see
     */
    public function getTempStruct()
    {
        return $this->_tempStruct[$this->getWorkingLevel()];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return void
     * @author
     * @see
     */
    public function resetTempStruct()
    {
        $this->_tempStruct[$this->getCurrentLevel()] = [];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function setTempArray($value)
    {
        $this->_tempArray[$this->getWorkingLevel()][] = $value;
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return mixed
     * @author
     * @see
     */
    public function getTempArray()
    {
        return $this->_tempArray[$this->getWorkingLevel()];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return void
     * @author
     * @see
     */
    public function resetTempArray()
    {
        $this->_tempArray[$this->getCurrentLevel()] = [];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function setMethodName($methodName)
    {
        $this->_methodName = $methodName;
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function setParam($value)
    {
        $this->_param[] = $value;
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return array
     * @author
     * @see
     */
    public function &getParam()
    {
        return $this->_param;
    }
}


class RpcMethodNameHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'methodName';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        $parser->setMethodName($data);
    }
}

class RpcIntHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return array
     * @author
     * @see
     */
    public function getName()
    {
        return ['int', 'i4'];
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        $parser->setTempValue((int)$data);
    }
}

class RpcDoubleHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'double';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        $data = (float)$data;
        $parser->setTempValue($data);
    }
}

class RpcBooleanHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'boolean';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        $data = (boolean)$data;
        $parser->setTempValue($data);
    }
}

class RpcStringHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'string';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        $parser->setTempValue((string)$data);
    }
}

class RpcDateTimeHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'dateTime.iso8601';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        $matches = [];
        if (!preg_match('/^([0-9]{4})([0-9]{2})([0-9]{2})T([0-9]{2}):([0-9]{2}):([0-9]{2})$/', $data, $matches)) {
            $parser->setTempValue(time());
        } else {
            $parser->setTempValue(gmmktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]));
        }
    }
}

class RpcBase64Handler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'base64';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        $parser->setTempValue(base64_decode($data));
    }
}

class RpcNameHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'name';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'member':
            $parser->setTempName($data);
            break;
        default:
            break;
        }
    }
}


class RpcValueHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'value';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $data
     * @return void
     * @author
     * @see
     */
    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'member':
            $parser->setTempValue($data);
            break;
        case 'data':
        case 'array':
            $parser->setTempValue($data);
            break;
        default:
            break;
        }
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $attributes
     * @return void
     * @author
     * @see
     */
    public function handleBeginElement(&$parser, &$attributes)
    {
        //$parser->resetTempValue();
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function handleEndElement(&$parser)
    {
        switch ($parser->getCurrentTag()) {
        case 'member':
            $parser->setTempMember($parser->getTempName(), $parser->getTempValue());
            break;
        case 'array':
        case 'data':
            $parser->setTempArray($parser->getTempValue());
            break;
        default:
            $parser->setParam($parser->getTempValue());
            break;
        }
        $parser->resetTempValue();
    }
}

class RpcMemberHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'member';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $attributes
     * @return void
     * @author
     * @see
     */
    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->setWorkingLevel();
        $parser->resetTempMember();
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function handleEndElement(&$parser)
    {
        $member =& $parser->getTempMember();
        $parser->releaseWorkingLevel();
        $parser->setTempStruct($member);
    }
}

class RpcArrayHandler extends XmlTagHandler
{

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'array';
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param $parser
     * @param $attributes
     * @return void
     * @author
     * @see
     */
    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->setWorkingLevel();
        $parser->resetTempArray();
    }

    /**
     * This Method starts the parsing of the specified RDF File. The File can be a local or a remote File.
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function handleEndElement(&$parser)
    {
        $parser->setTempValue($parser->getTempArray());
        $parser->releaseWorkingLevel();
    }
}

class RpcStructHandler extends XmlTagHandler
{

    /**
     *
     *
     * @access
     * @return string
     * @author
     * @see
     */
    public function getName()
    {
        return 'struct';
    }

    /**
     *
     *
     * @access
     * @param $parser
     * @param $attributes
     * @return void
     * @author
     * @see
     */
    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->setWorkingLevel();
        $parser->resetTempStruct();
    }

    /**
     *
     *
     * @access
     * @param
     * @return void
     * @author
     * @see
     */
    public function handleEndElement(&$parser)
    {
        $parser->setTempValue($parser->getTempStruct());
        $parser->releaseWorkingLevel();
    }
}
