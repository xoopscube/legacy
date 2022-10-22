<?php
/**
 * Theme set parser
 * @package    kernel
 * @subpackage xml
 * @version    XCL 2.3.1
 * @author     Other Authors Minahito, 2007/05/15
 * @author     Kazumi Ono (aka onokazu)
 * @copyright  (c) 2000-2003 XOOPS.org
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

include_once XOOPS_ROOT_PATH.'/class/xml/saxparser.php';
include_once XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php';

class XoopsThemeSetParser extends SaxParser
{
    public $tempArr = [];
    public $themeSetData = [];
    public $imagesData = [];
    public $templatesData = [];

    public function __construct(&$input)
    {
        parent::__construct($input);
        $this->addTagHandler(new ThemeSetThemeNameHandler());
        $this->addTagHandler(new ThemeSetDateCreatedHandler());
        $this->addTagHandler(new ThemeSetAuthorHandler());
        $this->addTagHandler(new ThemeSetDescriptionHandler());
        $this->addTagHandler(new ThemeSetGeneratorHandler());
        $this->addTagHandler(new ThemeSetNameHandler());
        $this->addTagHandler(new ThemeSetEmailHandler());
        $this->addTagHandler(new ThemeSetLinkHandler());
        $this->addTagHandler(new ThemeSetTemplateHandler());
        $this->addTagHandler(new ThemeSetImageHandler());
        $this->addTagHandler(new ThemeSetModuleHandler());
        $this->addTagHandler(new ThemeSetFileTypeHandler());
        $this->addTagHandler(new ThemeSetTagHandler());
    }

    public function setThemeSetData($name, &$value)
    {
        $this->themeSetData[$name] =& $value;
    }

    public function &getThemeSetData($name=null)
    {
        if (isset($name)) {
            if (isset($this->themeSetData[$name])) {
                return $this->themeSetData[$name];
            }
            $ret = false;
            return $ret;
        }
        return $this->themeSetData;
    }

    public function setImagesData(&$imagearr)
    {
        $this->imagesData[] =& $imagearr;
    }

    public function &getImagesData()
    {
        return $this->imagesData;
    }

    public function setTemplatesData(&$tplarr)
    {
        $this->templatesData[] =& $tplarr;
    }

    public function &getTemplatesData()
    {
        return $this->templatesData;
    }

    public function setTempArr($name, &$value, $delim='')
    {
        if (!isset($this->tempArr[$name])) {
            $this->tempArr[$name] =& $value;
        } else {
            $this->tempArr[$name] .= $delim.$value;
        }
    }

    public function getTempArr()
    {
        return $this->tempArr;
    }

    public function resetTempArr()
    {
        unset($this->tempArr);
        $this->tempArr = [];
    }
}


class ThemeSetDateCreatedHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'dateCreated';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'themeset':
            $parser->setThemeSetData('date', $data);
            break;
        default:
            break;
        }
    }
}

class ThemeSetAuthorHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'author';
    }

    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    public function handleEndElement(&$parser)
    {
        $parser->setCreditsData($parser->getTempArr());
    }
}

class ThemeSetDescriptionHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'description';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
            case 'image':
            case 'template':
            $parser->setTempArr('description', $data);
            break;
            default:
            break;
        }
    }
}

class ThemeSetGeneratorHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'generator';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'themeset':
            $parser->setThemeSetData('generator', $data);
            break;
        default:
            break;
        }
    }
}

class ThemeSetNameHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'name';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'themeset':
            $parser->setThemeSetData('name', $data);
            break;
        case 'author':
            $parser->setTempArr('name', $data);
            break;
        default:
            break;
        }
    }
}

class ThemeSetEmailHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'email';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'author':
            $parser->setTempArr('email', $data);
            break;
        default:
            break;
        }
    }
}

class ThemeSetLinkHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'link';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'author':
            $parser->setTempArr('link', $data);
            break;
        default:
            break;
        }
    }
}

class ThemeSetTemplateHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'template';
    }

    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
        $parser->setTempArr('name', $attributes['name']);
    }

    public function handleEndElement(&$parser)
    {
        $parser->setTemplatesData($parser->getTempArr());
    }
}

class ThemeSetImageHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'image';
    }

    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
        $parser->setTempArr('name', $attributes[0]);
    }

    public function handleEndElement(&$parser)
    {
        $parser->setImagesData($parser->getTempArr());
    }
}

class ThemeSetModuleHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'module';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'template':
        case 'image':
            $parser->setTempArr('module', $data);
            break;
        default:
            break;
        }
    }
}

class ThemeSetFileTypeHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'fileType';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'template':
            $parser->setTempArr('type', $data);
            break;
        default:
            break;
        }
    }
}

class ThemeSetTagHandler extends XmlTagHandler
{
    public function __construct()
    {
    }

    public function getName()
    {
        return 'tag';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'image':
            $parser->setTempArr('tag', $data);
            break;
        default:
            break;
        }
    }
}
