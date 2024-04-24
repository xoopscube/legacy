<?php
/**
 * XML RSS parser
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

require_once(XOOPS_ROOT_PATH.'/class/xml/saxparser.php');
require_once(XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php');

class XoopsXmlRss2Parser extends SaxParser
{
    public $_tempArr = [];
    public $_channelData = [];
    public $_imageData = [];
    public $_items = [];

    public function __construct(&$input)
    {
        parent::__construct($input);
        $this->useUtfEncoding();
        $this->addTagHandler(new RssChannelHandler());
        $this->addTagHandler(new RssTitleHandler());
        $this->addTagHandler(new RssLinkHandler());
        $this->addTagHandler(new RssGeneratorHandler());
        $this->addTagHandler(new RssDescriptionHandler());
        $this->addTagHandler(new RssCopyrightHandler());
        $this->addTagHandler(new RssNameHandler());
        $this->addTagHandler(new RssManagingEditorHandler());
        $this->addTagHandler(new RssLanguageHandler());
        $this->addTagHandler(new RssLastBuildDateHandler());
        $this->addTagHandler(new RssWebMasterHandler());
        $this->addTagHandler(new RssImageHandler());
        $this->addTagHandler(new RssUrlHandler());
        $this->addTagHandler(new RssWidthHandler());
        $this->addTagHandler(new RssHeightHandler());
        $this->addTagHandler(new RssItemHandler());
        $this->addTagHandler(new RssCategoryHandler());
        $this->addTagHandler(new RssPubDateHandler());
        $this->addTagHandler(new RssCommentsHandler());
        $this->addTagHandler(new RssSourceHandler());
        $this->addTagHandler(new RssAuthorHandler());
        $this->addTagHandler(new RssGuidHandler());
        $this->addTagHandler(new RssTextInputHandler());
    }

    public function setChannelData($name, &$value)
    {
        if (!isset($this->_channelData[$name])) {
            $this->_channelData[$name] =& $value;
        } else {
            $this->_channelData[$name] .= $value;
        }
    }

    public function &getChannelData($name = null)
    {
        if (isset($name)) {
            if (isset($this->_channelData[$name])) {
                return $this->_channelData[$name];
            }
            $ret = false;
            return $ret;
        }
        return $this->_channelData;
    }

    public function setImageData($name, &$value)
    {
        $this->_imageData[$name] =& $value;
    }

    public function &getImageData($name = null)
    {
        if (isset($name)) {
            if (isset($this->_imageData[$name])) {
                return $this->_imageData[$name];
            }
            $ret = false;
            return $ret;
        }
        return $this->_imageData;
    }

    public function setItems(&$itemarr)
    {
        $this->_items[] =& $itemarr;
    }

    public function &getItems()
    {
        return $this->_items;
    }

    public function setTempArr($name, &$value, $delim = '')
    {
        if (!isset($this->_tempArr[$name])) {
            $this->_tempArr[$name] =& $value;
        } else {
            $this->_tempArr[$name] .= $delim.$value;
        }
    }

    public function getTempArr()
    {
        return $this->_tempArr;
    }

    public function resetTempArr()
    {
        unset($this->_tempArr);
        $this->_tempArr = [];
    }
}

class RssChannelHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'channel';
    }
}

class RssTitleHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'title';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('title', $data);
            break;
        case 'image':
            $parser->setImageData('title', $data);
            break;
        case 'item':
        case 'textInput':
            $parser->setTempArr('title', $data);
            break;
        default:
            break;
        }
    }
}

class RssLinkHandler extends XmlTagHandler
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
        case 'channel':
            $parser->setChannelData('link', $data);
            break;
        case 'image':
            $parser->setImageData('link', $data);
            break;
        case 'item':
        case 'textInput':
            $parser->setTempArr('link', $data);
            break;
        default:
            break;
        }
    }
}

class RssDescriptionHandler extends XmlTagHandler
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
        case 'channel':
            $parser->setChannelData('description', $data);
            break;
        case 'image':
            $parser->setImageData('description', $data);
            break;
        case 'item':
        case 'textInput':
            $parser->setTempArr('description', $data);
            break;
        default:
            break;
        }
    }
}

class RssGeneratorHandler extends XmlTagHandler
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
        case 'channel':
            $parser->setChannelData('generator', $data);
            break;
        default:
            break;
        }
    }
}

class RssCopyrightHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'copyright';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('copyright', $data);
            break;
        default:
            break;
        }
    }
}

class RssNameHandler extends XmlTagHandler
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
        case 'textInput':
            $parser->setTempArr('name', $data);
            break;
        default:
            break;
        }
    }
}

class RssManagingEditorHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'managingEditor';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('editor', $data);
            break;
        default:
            break;
        }
    }
}

class RssLanguageHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'language';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('language', $data);
            break;
        default:
            break;
        }
    }
}

class RssWebMasterHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'webMaster';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('webmaster', $data);
            break;
        default:
            break;
        }
    }
}

class RssDocsHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'docs';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('docs', $data);
            break;
        default:
            break;
        }
    }
}

class RssTtlHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'ttl';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('ttl', $data);
            break;
        default:
            break;
        }
    }
}

class RssTextInputHandler extends XmlTagHandler
{

    public function RssWebMasterHandler()
    {
    }

    public function getName()
    {
        return 'textInput';
    }

    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    public function handleEndElement(&$parser)
    {
        $parser->setChannelData('textinput', $parser->getTempArr());
    }
}

class RssLastBuildDateHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'lastBuildDate';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('lastbuilddate', $data);
            break;
        default:
            break;
        }
    }
}

class RssImageHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'image';
    }
}

class RssUrlHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'url';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ('image' === $parser->getParentTag()) {
            $parser->setImageData('url', $data);
        }
    }
}

class RssWidthHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'width';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ('image' === $parser->getParentTag()) {
            $parser->setImageData('width', $data);
        }
    }
}

class RssHeightHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'height';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ('image' === $parser->getParentTag()) {
            $parser->setImageData('height', $data);
        }
    }
}

class RssItemHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'item';
    }

    public function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    public function handleEndElement(&$parser)
    {
        $parser->setItems($parser->getTempArr());
    }
}

class RssCategoryHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'category';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('category', $data);
            break;
        case 'item':
            $parser->setTempArr('category', $data, ', ');
            break;
        default:
            break;
        }
    }
}

class RssCommentsHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'comments';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ('item' === $parser->getParentTag()) {
            $parser->setTempArr('comments', $data);
        }
    }
}

class RssPubDateHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'pubDate';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        switch ($parser->getParentTag()) {
        case 'channel':
            $parser->setChannelData('pubdate', $data);
            break;
        case 'item':
            $parser->setTempArr('pubdate', $data);
            break;
        default:
            break;
        }
    }
}

class RssGuidHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'guid';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ('item' === $parser->getParentTag()) {
            $parser->setTempArr('guid', $data);
        }
    }
}

class RssAuthorHandler extends XmlTagHandler
{

    public function RssGuidHandler()
    {
    }

    public function getName()
    {
        return 'author';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ('item' === $parser->getParentTag()) {
            $parser->setTempArr('author', $data);
        }
    }
}

class RssSourceHandler extends XmlTagHandler
{

    public function __construct()
    {
    }

    public function getName()
    {
        return 'source';
    }

    public function handleBeginElement(&$parser, &$attributes)
    {
        if ('item' === $parser->getParentTag()) {
            $parser->setTempArr('source_url', $attributes['url']);
        }
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ('item' === $parser->getParentTag()) {
            $parser->setTempArr('source', $data);
        }
    }
}
