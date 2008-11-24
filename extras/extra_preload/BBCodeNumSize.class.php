<?php
/**
 * @brief This preload adds bbcode pattern which ic compatible with X2.
 *        [size=32px]Foo[/size]
 * @version $Id: BBCodeNumSize.class.php,v 1.1 2007/05/15 02:35:01 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

class BBCodeNumSize extends XCube_ActionFilter
{
	function preBlockFilter()
	{
		$this->mRoot->mTextFilter->mMakeXCodeConvertTable->add(array(&$this, 'bbcode'), XCUBE_DELEGATE_PRIORITY_1);
	}
	
	function bbcode(&$patterns, &$replacements)
	{
		$patterns[] = "/\[size=(['\"]?)([a-z0-9-]*)\\1](.*)\[\/size\]/sU";
		$replacements[0][] = $replacements[1][] = '<span style="font-size: \\2;">\\3</span>';
	}
}

?>