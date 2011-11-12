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

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once(XOOPS_ROOT_PATH.'/class/xml/saxparser.php');
require_once(XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php');

class XoopsXmlRss2Parser extends SaxParser
{
    var $_tempArr = array();
    var $_channelData = array();
    var $_imageData = array();
    var $_items = array();

    function XoopsXmlRss2Parser(&$input)
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

	function setChannelData($name, &$value)
	{
		if (!isset($this->_channelData[$name])) {
			$this->_channelData[$name] =& $value;
		} else {
			$this->_channelData[$name] .= $value;
		}
	}

    function &getChannelData($name = null)
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

    function setImageData($name, &$value)
    {
        $this->_imageData[$name] =& $value;
    }

    function &getImageData($name = null)
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

    function setItems(&$itemarr)
    {
        $this->_items[] =& $itemarr;
    }

    function &getItems()
    {
        return $this->_items;
    }

    function setTempArr($name, &$value, $delim = '')
    {
        if (!isset($this->_tempArr[$name])) {
            $this->_tempArr[$name] =& $value;
        } else {
            $this->_tempArr[$name] .= $delim.$value;
        }
    }

    function getTempArr()
    {
        return $this->_tempArr;
    }

    function resetTempArr()
    {
        unset($this->_tempArr);
        $this->_tempArr = array();
    }
}

class RssChannelHandler extends XmlTagHandler
{

    function RssChannelHandler()
    {

    }

    function getName()
    {
        return 'channel';
    }
}

class RssTitleHandler extends XmlTagHandler
{

    function RssTitleHandler()
    {

    }

    function getName()
    {
        return 'title';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssLinkHandler()
    {

    }

    function getName()
    {
        return 'link';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssDescriptionHandler()
    {

    }

    function getName()
    {
        return 'description';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssGeneratorHandler()
    {

    }

    function getName()
    {
        return 'generator';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssCopyrightHandler()
    {

    }

    function getName()
    {
        return 'copyright';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssNameHandler()
    {

    }

    function getName()
    {
        return 'name';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssManagingEditorHandler()
    {

    }

    function getName()
    {
        return 'managingEditor';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssLanguageHandler()
    {

    }

    function getName()
    {
        return 'language';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssWebMasterHandler()
    {

    }

    function getName()
    {
        return 'webMaster';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssDocsHandler()
    {

    }

    function getName()
    {
        return 'docs';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssTtlHandler()
    {

    }

    function getName()
    {
        return 'ttl';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssWebMasterHandler()
    {

    }

    function getName()
    {
        return 'textInput';
    }

    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    function handleEndElement(&$parser)
    {
        $parser->setChannelData('textinput', $parser->getTempArr());
    }
}

class RssLastBuildDateHandler extends XmlTagHandler
{

    function RssLastBuildDateHandler()
    {

    }

    function getName()
    {
        return 'lastBuildDate';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssImageHandler()
    {
    }

    function getName()
    {
        return 'image';
    }
}

class RssUrlHandler extends XmlTagHandler
{

    function RssUrlHandler()
    {

    }

    function getName()
    {
        return 'url';
    }

    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('url', $data);
        }
    }
}

class RssWidthHandler extends XmlTagHandler
{

    function RssWidthHandler()
    {

    }

    function getName()
    {
        return 'width';
    }

    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('width', $data);
        }
    }
}

class RssHeightHandler extends XmlTagHandler
{

    function RssHeightHandler()
    {
    }

    function getName()
    {
        return 'height';
    }

    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'image') {
            $parser->setImageData('height', $data);
        }
    }
}

class RssItemHandler extends XmlTagHandler
{

    function RssItemHandler()
    {

    }

    function getName()
    {
        return 'item';
    }

    function handleBeginElement(&$parser, &$attributes)
    {
        $parser->resetTempArr();
    }

    function handleEndElement(&$parser)
    {
        $parser->setItems($parser->getTempArr());
    }
}

class RssCategoryHandler extends XmlTagHandler
{

    function RssCategoryHandler()
    {

    }

    function getName()
    {
        return 'category';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssCommentsHandler()
    {

    }

    function getName()
    {
        return 'comments';
    }

    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('comments', $data);
        }
    }
}

class RssPubDateHandler extends XmlTagHandler
{

    function RssPubDateHandler()
    {

    }

    function getName()
    {
        return 'pubDate';
    }

    function handleCharacterData(&$parser, &$data)
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

    function RssGuidHandler()
    {

    }

    function getName()
    {
        return 'guid';
    }

    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('guid', $data);
        }
    }
}

class RssAuthorHandler extends XmlTagHandler
{

    function RssGuidHandler()
    {

    }

    function getName()
    {
        return 'author';
    }

    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('author', $data);
        }
    }
}

class RssSourceHandler extends XmlTagHandler
{

    function RssSourceHandler()
    {

    }

    function getName()
    {
        return 'source';
    }

    function handleBeginElement(&$parser, &$attributes)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('source_url', $attributes['url']);
        }
    }

    function handleCharacterData(&$parser, &$data)
    {
        if ($parser->getParentTag() == 'item') {
            $parser->setTempArr('source', $data);
        }
    }
}
?>