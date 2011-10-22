<?php
// $Id: themesetparser.php,v 1.1 2007/05/15 02:35:35 minahito Exp $
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

include_once XOOPS_ROOT_PATH.'/class/xml/saxparser.php';
include_once XOOPS_ROOT_PATH.'/class/xml/xmltaghandler.php';

class XoopsThemeSetParser extends SaxParser
{
	var $tempArr = array();
	var $themeSetData = array();
	var $imagesData = array();
	var $templatesData = array();

	function XoopsThemeSetParser(&$input)
	{
		$this->SaxParser($input);
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

	function setThemeSetData($name, &$value)
	{
		$this->themeSetData[$name] =& $value;
	}

	function &getThemeSetData($name=null)
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

	function setImagesData(&$imagearr)
	{
		$this->imagesData[] =& $imagearr;
	}

	function &getImagesData()
	{
		return $this->imagesData;
	}

	function setTemplatesData(&$tplarr)
	{
		$this->templatesData[] =& $tplarr;
	}

	function &getTemplatesData()
	{
		return $this->templatesData;
	}

	function setTempArr($name, &$value, $delim='')
	{
		if (!isset($this->tempArr[$name])) {
			$this->tempArr[$name] =& $value;
		} else {
			$this->tempArr[$name] .= $delim.$value;
		}
	}

	function getTempArr()
	{
		return $this->tempArr;
	}

	function resetTempArr()
	{
		unset($this->tempArr);
		$this->tempArr = array();
	}
}


class ThemeSetDateCreatedHandler extends XmlTagHandler
{

	function ThemeSetDateCreatedHandler()
	{

	}

	function getName()
	{
		return 'dateCreated';
	}

	function handleCharacterData(&$parser, &$data)
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
	function ThemeSetAuthorHandler()
	{

	}

	function getName()
	{
		return 'author';
	}

	function handleBeginElement(&$parser, &$attributes)
	{
		$parser->resetTempArr();
	}

	function handleEndElement(&$parser)
	{
		$parser->setCreditsData($parser->getTempArr());
	}
}

class ThemeSetDescriptionHandler extends XmlTagHandler
{
	function ThemeSetDescriptionHandler()
	{

	}

	function getName()
	{
		return 'description';
	}

	function handleCharacterData(&$parser, &$data)
	{
		switch ($parser->getParentTag()) {
		case 'template':
			$parser->setTempArr('description', $data);
			break;
		case 'image':
			$parser->setTempArr('description', $data);
			break;
		default:
			break;
		}
	}
}

class ThemeSetGeneratorHandler extends XmlTagHandler
{
	function ThemeSetGeneratorHandler()
	{

	}

	function getName()
	{
		return 'generator';
	}

	function handleCharacterData(&$parser, &$data)
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
	function ThemeSetNameHandler()
	{

	}

	function getName()
	{
		return 'name';
	}

	function handleCharacterData(&$parser, &$data)
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
	function ThemeSetEmailHandler()
	{

	}

	function getName()
	{
		return 'email';
	}

	function handleCharacterData(&$parser, &$data)
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
	function ThemeSetLinkHandler()
	{

	}

	function getName()
	{
		return 'link';
	}

	function handleCharacterData(&$parser, &$data)
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
	function ThemeSetTemplateHandler()
	{

	}

	function getName()
	{
		return 'template';
	}

	function handleBeginElement(&$parser, &$attributes)
	{
		$parser->resetTempArr();
		$parser->setTempArr('name', $attributes['name']);
	}

	function handleEndElement(&$parser)
	{
		$parser->setTemplatesData($parser->getTempArr());
	}
}

class ThemeSetImageHandler extends XmlTagHandler
{
	function ThemeSetImageHandler()
	{

	}

	function getName()
	{
		return 'image';
	}

	function handleBeginElement(&$parser, &$attributes)
	{
		$parser->resetTempArr();
		$parser->setTempArr('name', $attributes[0]);
	}

	function handleEndElement(&$parser)
	{
		$parser->setImagesData($parser->getTempArr());
	}
}

class ThemeSetModuleHandler extends XmlTagHandler
{
	function ThemeSetModuleHandler()
	{

	}

	function getName()
	{
		return 'module';
	}

	function handleCharacterData(&$parser, &$data)
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
	function ThemeSetFileTypeHandler()
	{

	}

	function getName()
	{
		return 'fileType';
	}

	function handleCharacterData(&$parser, &$data)
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
	function ThemeSetTagHandler()
	{

	}

	function getName()
	{
		return 'tag';
	}

	function handleCharacterData(&$parser, &$data)
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
?>