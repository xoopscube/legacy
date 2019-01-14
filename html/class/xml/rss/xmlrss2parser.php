<?php
// $Id: xmlrss2parser.php,v 1.1 2007/05/15 02:34:38 minahito Exp $
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

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once(XOOPS_ROOT_PATH.'/class/xml/saxparser.php');
require_once(XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php');

class XoopsXmlRss2Parser extends SaxParser
{
    public $_tempArr = array();
    public $_channelData = array();
    public $_imageData = array();
    public $_items = array();

    public function XoopsXmlRss2Parser(&$input)
    {
        $this->SaxParser($input);
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
        $this->_tempArr = array();
    }
}

class RssChannelHandler extends XmlTagHandler
{

    public function RssChannelHandler()
    {
    }

    public function getName()
    {
        return 'channel';
    }
}

class RssTitleHandler extends XmlTagHandler
{

    public function RssTitleHandler()
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

    public function RssLinkHandler()
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

    public function RssDescriptionHandler()
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

    public function RssGeneratorHandler()
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

    public function RssCopyrightHandler()
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

    public function RssNameHandler()
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

    public function RssManagingEditorHandler()
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

    public function RssLanguageHandler()
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

    public function RssWebMasterHandler()
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

    public function RssDocsHandler()
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

    public function RssTtlHandler()
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

    public function RssLastBuildDateHandler()
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

    public function RssImageHandler()
    {
    }

    public function getName()
    {
        return 'image';
    }
}

class RssUrlHandler extends XmlTagHandler
{

    public function RssUrlHandler()
    {
    }

    public function getName()
    {
        return 'url';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('url', $data);
        }
    }
}

class RssWidthHandler extends XmlTagHandler
{

    public function RssWidthHandler()
    {
    }

    public function getName()
    {
        return 'width';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('width', $data);
        }
    }
}

class RssHeightHandler extends XmlTagHandler
{

    public function RssHeightHandler()
    {
    }

    public function getName()
    {
        return 'height';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('height', $data);
        }
    }
}

class RssItemHandler extends XmlTagHandler
{

    public function RssItemHandler()
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

    public function RssCategoryHandler()
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
        default:
            break;
        }
    }
}

class RssCommentsHandler extends XmlTagHandler
{

    public function RssCommentsHandler()
    {
    }

    public function getName()
    {
        return 'comments';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('comments', $data);
        }
    }
}

class RssPubDateHandler extends XmlTagHandler
{

    public function RssPubDateHandler()
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

    public function RssGuidHandler()
    {
    }

    public function getName()
    {
        return 'guid';
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
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
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('author', $data);
        }
    }
}

class RssSourceHandler extends XmlTagHandler
{

    public function RssSourceHandler()
    {
    }

    public function getName()
    {
        return 'source';
    }

    public function handleBeginElement(&$parser, &$attributes)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('source_url', $attributes['url']);
        }
    }

    public function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('source', $data);
        }
    }
}
